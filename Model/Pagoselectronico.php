<?php

App::uses('AppModel', 'Model');

class Pagoselectronico extends AppModel {

    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_code',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_code',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_code',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Cobranza' => array(
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /*
     * Obtiene las cobranzas del cliente actual para la fecha indicada (si fue seleccionada)
     * Si no selecciona fecha, devuelve los pagos sin cargar.
     * Si es admin, obtengo todas las cobranzas de todos los clientes.
     * Verifico que Propietario.name y Consorcio.name sean != null, quiere decir entonces q el consorcio y propietario están cargados (cobranza_id=0), sino
     * dejo el pago pendiente (cuando se carguen esos Consorcios con su cuenta bancaria asociada y propietarios, van a aparecer los pagos pendientes de cargar.
     * Tambine devuelvo el saldo de cada propietario segun el prefijo de la cobranza recibida (para poder seleccionar "guarda pago" de ser necesario)
     */

    public function getCobranzas($data) {
        $client = (($_SESSION['Auth']['User']['is_admin'] == 0) ? ['Pagoselectronico.client_code' => $_SESSION['Auth']['User']['Client']['code'], 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] : []);
        if (!empty($data['f'])) {
            $options = ['conditions' => [$client, 'Pagoselectronico.cobranza_id' => 0, 'Pagoselectronico.fecha_proc' => date('Y-m-d', strtotime(str_replace("/", '-', $data['f'])))]];
        } else {
            $options = ['conditions' => [$client, 'Pagoselectronico.cobranza_id' => 0]];
        }
        $options += ['fields' => ['Pagoselectronico.consorcio_code as cc', 'Pagoselectronico.propietario_code as pc', 'Pagoselectronico.fecha_proc as f', 'Pagoselectronico.medio as m', 'Pagoselectronico.importe as i', 'Pagoselectronico.comision as co', 'Pagoselectronico.id', 'Pagoselectronico.prefijo as pr']];
        $options += ['recursive' => -1, 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.code=Pagoselectronico.consorcio_code']]]];
        $options += ['order' => 'Pagoselectronico.fecha,Pagoselectronico.consorcio_code desc,Pagoselectronico.propietario_code desc'];
        $resul = $this->find('all', $options);
        $consorcios = $propietarios = $consorid = $bancoscuenta = $saldos = $pe = $pe2 = $tl = [];

        // si el cliente usa plataforma y sin comision (cobra la comision x GG o GP, entonces tengo q sumar el importe de la comision
        // al total abonado por el cliente, porq sino en cobranzas automaticas sale saldo != pago, y todos los pagos con la cruz roja.
        // de esta forma el administrador sabe bien quien pagó el total y quien pagó parcial
        $x = ClassRegistry::init('Plataformasdepago');
        $plataforma = $x->getConfig($_SESSION['Auth']['User']['client_id'])['Plataformasdepagosconfig'];
        $sumacomision = false;
        if ($plataforma['plataformasdepago_id'] != 0 && $plataforma['comision'] == 0) {
            $sumacomision = true;
        }

        foreach ($resul as $k => $v) {
            $cid = $this->Consorcio->getConsorcioId($_SESSION['Auth']['User']['client_id'], $v['Pagoselectronico']['cc']);
            if (!$this->Consorcio->isHabilitado($cid)) {// no muestro cobranzas automaticas de consorcios deshabilitados
                continue;
            }
            $bc = $this->Consorcio->Client->Banco->Bancoscuenta->getDefaultCA($cid);
            $pid = $this->Consorcio->Propietario->getPropietarioId($cid, $v['Pagoselectronico']['pc']);
            if (empty($pid) || empty($bc)) {
                continue;
            }
            if ($sumacomision) {
                $v['Pagoselectronico']['i'] += $v['Pagoselectronico']['co'];
            }

            $bancoscuenta[$v['Pagoselectronico']['cc']] = $bc;
            $consorcios[] = $v['Pagoselectronico']['cc'];
            $consorid[] = $cid;
            $liquidations_type_id = $this->Consorcio->Liquidation->LiquidationsType->getLiquidationsTypeIdFromPrefijo($v['Pagoselectronico']['pr']);
            // si tiene cuenta bancaria asociada
            if (!empty($bc)) {
                $liquidation_id = $this->Consorcio->Liquidation->getUltimaLiquidacion($cid, $liquidations_type_id);
                $s = $this->Consorcio->Liquidation->SaldosCierre->getSaldo($liquidation_id, $this->Consorcio->Propietario->getPropietarioId($cid, $v['Pagoselectronico']['pc']), true);
                if (count($s) > 1) { // son saldos iniciales, obtengo solo el q me interesa ahora
                    $saldos[$liquidations_type_id][$v['Pagoselectronico']['cc']][$v['Pagoselectronico']['pc']] = $s[$pid]['capital'] + $s[$pid]['interes'];
                } else {
                    $saldos[$liquidations_type_id][$v['Pagoselectronico']['cc']][$v['Pagoselectronico']['pc']] = !empty($s) ? ($s[$pid]['capital'] < 0 ? $s[$pid]['capital'] : intval($s[$pid]['capital'] + $s[$pid]['interes'])) : 0;
                }

                // en el listado de pagos electronicos no muestra el saldo actualizado, para q lo haga, tengo q sumar las cobranzas y ajustes q existan
                $cobranzasperiodo = $this->Cobranza->getCobranzasPeriodo($cid);
                $cob = $this->buscaLista($cobranzasperiodo[$liquidations_type_id], ['propietario_id' => $pid], true);
                foreach ($cob as $j => $h) {
                    $saldos[$liquidations_type_id][$v['Pagoselectronico']['cc']][$v['Pagoselectronico']['pc']] -= $cobranzasperiodo[$liquidations_type_id][$h]['Cobranzatipoliquidacione']['amount'];
                }
                $ajustesperiodo = $this->Consorcio->Propietario->Ajuste->getAjustesPeriodo($cid);
                $aj = $this->buscaLista($ajustesperiodo[$liquidations_type_id], ['propietario_id' => $pid], true);
                foreach ($aj as $j => $h) {
                    $saldos[$liquidations_type_id][$v['Pagoselectronico']['cc']][$v['Pagoselectronico']['pc']] -= $ajustesperiodo[$liquidations_type_id][$h]['Ajustetipoliquidacione']['amount'];
                }
            }
            $tl[$liquidations_type_id] = $v['Pagoselectronico']['pr'];
            $prop = $this->Propietario->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.code' => $v['Pagoselectronico']['cc'], 'Propietario.code' => $v['Pagoselectronico']['pc']], 'fields' => ['Propietario.code', 'Propietario.name2'], 'recursive' => 0]);
            $propietarios[$v['Pagoselectronico']['cc']][current(array_keys($prop))] = current(array_values($prop));

            //if (empty($bc)) { // si no tiene cuenta bancaria, se hacia esto
            //    $pe2[] = ['p' => $v['Pagoselectronico'], 'lt' => $liquidations_type_id, 'bc' => $bancoscuenta[$v['Pagoselectronico']['cc']]];
            //} else {
            $pe[] = ['p' => $v['Pagoselectronico'], 'lt' => $liquidations_type_id, 'bc' => $bancoscuenta[$v['Pagoselectronico']['cc']]];
            //}

            /* if($v['Pagoselectronico']['pc']=='61'){
              debug($pid);debug($saldos);debug($v);
              } */
        }
        //$pe += $pe2; // los q tienen cuenta bancaria asociada aparecen primero!
        $consor = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.code' => $consorcios], 'fields' => ['Consorcio.code', 'Consorcio.name']]);
        return ['pe' => $pe, 'c' => $consor, 'p' => $propietarios, 'b' => $bancoscuenta, 's' => $saldos, 'tl' => $tl];
    }

    public function getComisionesPLAPSA($client_code = null, $desde = null, $hasta = null) {
        $resul = $this->find('all', ['conditions' => ['Pagoselectronico.plataforma' => 1, 'Plataformasdepagosconfig.plataformasdepago_id' => 1] + [!empty($client_code) ? ['client_code' => $client_code] : [],
        !empty($desde) ? ['Pagoselectronico.fecha_proc >=' => $desde] : [], !empty($hasta) ? ['fecha_proc <=' => $hasta] : []],
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.code=Pagoselectronico.client_code']],
                ['table' => 'plataformasdepagosconfigs', 'alias' => 'Plataformasdepagosconfig', 'type' => 'left', 'conditions' => ['Client.id=Plataformasdepagosconfig.client_id']]],
            'fields' => ['Pagoselectronico.client_code', 'Pagoselectronico.fecha_proc', 'count(Pagoselectronico.id) as cantidad', 'sum(Pagoselectronico.importe) as importe', 'sum(Pagoselectronico.comision) as comision'],
            'group' => 'Client.id',
            'order' => 'Client.code']);
        return $resul;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                //'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                //'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
                //'Pagoselectronico.importe' => $data['buscar'],
                'Pagoselectronico.propietario_code' => $data['buscar'],
        ));
    }

}
