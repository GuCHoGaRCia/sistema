<?php

App::uses('AppModel', 'Model');

class SaldosCierre extends AppModel {

    public $belongsTo = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'SaldosCierre.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=SaldosIniciale.propietario_id']],
                                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]]]));
    }

    /*
     * Funcion que devuelve el saldo de propietarios de una liquidacion anterior a la actual seg�n su tipo.
     * Si no existe el saldo, devuelve array(), sino devuelve propietario_id, capital, interes, redondeo
     * Si $actual=false, es el saldo despues de cerrada la liquidacion actual, sino es el saldo de la liquidacion anterior
     */

    public function getSaldo($liquidation_id, $propietario_id = null, $actual = false) {
        $condiciones = array('SaldosCierre.liquidation_id' => (!$actual ? $this->Liquidation->getLastLiquidation($liquidation_id) : $liquidation_id));
        $tipo = "all";
        if (!empty($propietario_id)) {
            $condiciones['SaldosCierre.propietario_id'] = $propietario_id;
            $tipo = "first";
        }
        $options = array('conditions' => $condiciones,
            'fields' => array('SaldosCierre.propietario_id', 'SaldosCierre.capital', 'SaldosCierre.interes', 'SaldosCierre.redondeo', 'SaldosCierre.cobranzas', 'SaldosCierre.ajustes', 'SaldosCierre.gastosgenerales', 'SaldosCierre.gastosparticulares',
                'SaldosCierre.capant', 'SaldosCierre.intant', 'SaldosCierre.redant', 'SaldosCierre.interesactual'));
        $resul = $this->find($tipo, $options);

        if (count($resul) == 0) {
            return $this->Liquidation->LiquidationsType->SaldosIniciale->getSaldo($liquidation_id);
        } else {
            if (!empty($propietario_id)) {// en Pagoselectronico busco el saldo de cada uno x separado, entonces tiene q ser un array con el 1º elemento indexado en cero
                $resul = [$resul];
            }
            $resul = Hash::combine($resul, '{n}.SaldosCierre.propietario_id', '{n}.SaldosCierre');

            // si se agregaron propietarios nuevos (WTF), entonces los saldos anteriores no existen, los creo en cero (sino no se ve el Propietario en la composición de saldos y tira error en RC)
            if (empty($propietario_id)) {
                $props = $this->Liquidation->Consorcio->Propietario->getList($this->Liquidation->getConsorcioId($liquidation_id));
                foreach ($props as $k => $v) {
                    if (!isset($resul[$k])) {
                        $resul[$k] = ['propietario_id' => "$k", 'capital' => 0, 'interes' => 0, 'redondeo' => 0, 'cobranzas' => 0, 'ajustes' => 0, 'gastosgenerales' => 0,
                            'gastosparticulares' => 0, 'capant' => 0, 'intant' => 0, 'redant' => 0, 'interesactual' => 0];
                    }
                }
            }
            return $resul;
        }

        //'propietario_id' => '30592',
        //'capital' => (int) 0,
        //'interes' => (int) 0,
        //'redondeo' => (int) 0,
        //'cobranzas' => (int) 0,
        //'ajustes' => (int) 0,
        //'gastosgenerales' => (int) 0,
        //'gastosparticulares' => (int) 0,
        //'capant' => (int) 0,
        //'intant' => (int) 0,
        //'redant' => (int) 0,
        //'interesactual' => (int) 0
    }

    /*
     * Obtiene los saldos del propietario de la liq siguiente a la primer liq en la que el propietario tiene saldo remanente cero o a favor (llendo desde adelante hacia atras en el tiempo)
     * Para esto busca a partir de la ultima liquidacion cerrada la primera ocurrencia de que el saldo remanente del propietario sea cero o menor a cero (propietario acreedor).
     * $prefijo permite elegir el tipo de liquidaciones a utilizar
     */

    public function getSaldosYLiqsPropietarioDeuda($consorcio_id, $propietario_id, $prefijo = 0) {
        $periodoliqsdepropcondeuda = $saldosliqsdepropcondeuda = [];
        $liquidaciones = $this->Liquidation->getLiquidations($consorcio_id, $this->Liquidation->LiquidationsType->getLiquidationsTypeIdFromPrefijo($prefijo));

        foreach ($liquidaciones as $k => $v) {
            $saldo = $this->Liquidation->SaldosCierre->getSaldo($k, $propietario_id, true);
            if (isset($saldo[$propietario_id])) {
                if (!isset($saldo[$propietario_id]['liquidations_type_id'])) {
                    $remanente = $saldo[$propietario_id]['capant'] + $saldo[$propietario_id]['intant'] - $saldo[$propietario_id]['redant'] - $saldo[$propietario_id]['cobranzas'] - $saldo[$propietario_id]['ajustes'];
                } else {
                    // si esta seteado liquidations_type_id entonces es la liq inicial porque si es inicial dentro de getSaldo en SaldosCierre se llama a getSaldo de SaldosIniciale que devuelve liquidations_type_id
                    $remanente = $saldo[$propietario_id]['capital'] + $saldo[$propietario_id]['interes'];
                }
                if ($remanente > 0) {
                    $periodoliqsdepropcondeuda[$k] = $v;
                    $saldosliqsdepropcondeuda[$k] = $saldo;
                } else {
                    break;
                }
            } else {
                break;
            }
        }
        return ['periodos' => $periodoliqsdepropcondeuda, 'saldos' => $saldosliqsdepropcondeuda];
    }

    /*
     * Obtiene los saldos al cierre del propietario para mostrar la cta cte en la cobranza manual
     */

    public function getSaldosPropietario($propietario_id = null, $f1 = null, $f2 = null, $todas = false) {
        $resul = null;
        $cond = [];
        if (!is_null($f1) && !is_null($f2)) {
            $cond = ['date(Liquidation.closed) >=' => $f1, 'date(Liquidation.closed) <=' => $f2];
        }
        if (!is_null($propietario_id)) {
            // busco las liquidaciones del consorcio del propietario q se encuentren en estado impreso/online
            $options = ['conditions' => ['SaldosCierre.propietario_id' => $propietario_id, 'Liquidation.bloqueada' => 1] + $cond, 'recursive' => 0, 'order' => ['Liquidation.liquidations_type_id,Liquidation.closed asc'],
                'fields' => ['SaldosCierre.*', 'Liquidation.*', 'Liquidation.closed as fecha']]; // dejar fecha porq sirve para hacer sort x ese campo
            // busco los saldos iniciales
            $saldos = $this->find('all', $options);
            $s = [];
            if ($todas) {
                // obtengo los movimientos de las liquidaciones q no estén cerradas (puede q no esten prorrateadas entonces en SaldosCierre no hay nada!)
                $s['abiertas'] = $this->getSaldosLiquidacionesAbiertas($propietario_id);
            }
            $resul = ['saldos' => $saldos + $s, 'iniciales' => $this->Liquidation->LiquidationsType->SaldosIniciale->getSaldo(null, $propietario_id, 'all')];
        }
        return $resul;
    }

    /*
     * Para la carta deudor, obtengo la deuda total (sumando todos los tipos de liquidacion)
     */

    public function getSaldosDeudorPropietario($propietario_id) {
        $resul = 0;
        $consorcio_id = $this->Propietario->getPropietarioConsorcio($propietario_id);
        $lt = $this->Liquidation->LiquidationsType->getLiquidationsTypes();
        $cobranzas = $this->Liquidation->Consorcio->Propietario->Cobranza->getCobranzasPeriodo($consorcio_id);
        foreach ($lt as $k => $v) {
            $saldo = $this->getSaldo($this->Liquidation->getUltimaLiquidacion($consorcio_id, $k), $propietario_id, true); //actual
            if (!empty($saldo) && isset($saldo[$propietario_id])) {
                $resul += intval($saldo[$propietario_id]['capital'] + $saldo[$propietario_id]['interes']);
                if (!isset($cobranzas[$k])) {
                    continue;
                }
                $keys = $this->buscaLista($cobranzas[$k], ['propietario_id' => $propietario_id], true);
                if (!empty($keys)) {
                    foreach ($keys as $key) {
                        $resul -= $cobranzas[$k][$key]['Cobranzatipoliquidacione']['amount'];
                    }
                }
            }
        }
        return $resul;
    }

    /*
     * Obtiene la cantidad de últimas liquidaciones sin cobranza
     */

    public function getCantidadLiquidacionesImpagas($propietario_id) {
        $listasaldos = $this->find('all', ['conditions' => ['SaldosCierre.propietario_id' => $propietario_id, 'Liquidation.bloqueada' => 1], 'joins' => [['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=SaldosCierre.liquidation_id']]],
            'order' => 'SaldosCierre.liquidation_id desc', 'fields' => 'SaldosCierre.cobranzas']);
        $cant = 0;
        foreach ($listasaldos as $v) {
            if ($v['SaldosCierre']['cobranzas'] == 0) {
                $cant++;
            } else {
                break;
            }
        }
        return $cant;
    }

    public function getSaldosLiquidacionesAbiertas($propietario_id, $lt = null) {
        $liquidations = $this->Liquidation->getLiquidacionesAbiertas($this->Propietario->getPropietarioConsorcio($propietario_id));
        $saldos = [];
        foreach ($liquidations as $m => $x) {
            if (!empty($lt) && $this->Liquidation->getLiquidationsTypeId($m) != $lt) {
                continue;
            }
            $totales = $this->Liquidation->totalesProrrateoPropietario($m);
            $cobranzas = $this->Liquidation->LiquidationsType->Cobranza->getCobranzas($m);
            $saldosanteriores = $this->getSaldo($m); // si no existe, busca el saldo inicial
            $ajustes = $this->Propietario->Ajuste->getAjustes($m);
            $remanentes = $this->Liquidation->getSaldosRemanentes($saldosanteriores, $cobranzas, $ajustes);
            // en $saldosanteriores tengo los redondeos del cierre anterior
            $saldocierre = Hash::combine($this->calculaSaldoCierre($totales, $m, $remanentes, $saldosanteriores, $cobranzas, $ajustes), '{n}.SaldosCierre.propietario_id', '{n}.SaldosCierre');
            if (empty($saldocierre)) {
                continue;
            }

            if (!isset($remanentes[$propietario_id])) {
                continue; // aic san angelo 2 tiene problemas en agosto wtf
            }
//		(int) 2 => array(
//			'SaldosCierre' => array(
//				'capital' => '5.99',
//				'interes' => '0.00',
//				'redondeo' => '0.14',
//				'cobranzas' => '3493.00',
//				'ajustes' => '0.00',
//				'gastosgenerales' => '1432.55',
//				'gastosparticulares' => '0.00',
//				'interesactual' => '0.00'
//			),
//			'Liquidation' => array(
//				'id' => '147',
//				'fecha' => '2016-07-22 09:19:04',
//				'name' => 'JUNIO 2016',
//				'liquidations_type_id' => '99'
//			)
//		),
            // busco los saldos iniciales
            $l = $this->Liquidation->find('first', ['conditions' => ['Liquidation.id' => $m], 'fields' => ['Liquidation.id', 'Liquidation.closed', 'Liquidation.name', 'Liquidation.periodo', 'Liquidation.liquidations_type_id']]);
            $saldos[$this->Liquidation->getLiquidationsTypeId($m)][] = ['SaldosCierre' => $saldocierre[$propietario_id]] + $l;
        }
        // si no hay nada, devuelvo cero
        return $saldos;
    }

    /*
     * Obtiene los saldos al cierre de los propietarios del consorcio de las ultimas liquidaciones de cada tipo de liquidacion
      //     (int) 17 => array(// liquidations_type_id
      //		(int) 497 => array(  // propietario_id
      //			'id' => '2139',
      //			'liquidation_id' => '188',
      //			'propietario_id' => '497',
      //			'capital' => '681.06',
      //			'interes' => '17.66',
      //			'redondeo' => '0.71',
      //			'created' => '2016-04-21 12:19:44',
      //			'modified' => '2016-04-21 12:19:44'
     */

    public function getSaldosTipoLiquidacionPropietarios($consorcio_id) {
        // busco las ultimas liquidaciones de cada tipo de liquidacion
        $liquidation_types = $this->Liquidation->LiquidationsType->getLiquidationsTypes();
        $ultimas_liquidaciones = [];
        foreach ($liquidation_types as $k => $v) {// las ultimas liquidaciones por tipo de liquidacion (si no hay ninguna, es cero)
            $ultimas_liquidaciones[$k] = $this->Liquidation->getUltimaLiquidacion($consorcio_id, $k); // liquidation_type_id,liquidation_id
        }
        $saldos = [];
        foreach ($ultimas_liquidaciones as $k => $v) {// con el id de cada liquidacion, obtengo los saldos directo de la tabla sin importar los id de los propietarios (son todos)
            $saldocierre = $this->find('all', ['conditions' => ['SaldosCierre.liquidation_id' => $v]]);
            if (!empty($saldocierre)) {
                $saldos[$k] = Hash::combine($saldocierre, '{n}.SaldosCierre.propietario_id', '{n}.SaldosCierre');
            } else {
                $saldos[$k] = $this->Liquidation->LiquidationsType->SaldosIniciale->getSaldo($v); // ya esta hecho el Hash::combine
            }
        }
        return $saldos;
    }

    public function calculaSaldoCierre($data = [], $id = null, $remanente = [], $saldosanteriores = []) {
        $d = [];
        $prop = $this->Liquidation->Consorcio->Propietario->getPropietariosId($this->Liquidation->getConsorcioId($id));
        foreach ($prop as $p) {
            $gg = 0;
            $gp = 0;
            $k = $p['Propietario']['id'];
            $tot = 0;
            if (isset($data[$k])) {
                $v = $data[$k];
                //sumo los gastos generales y particulares del propietario. Si no tiene GP, no esta seteado $v['tot']
                $gg = $this->_sumcoefs($v);
                $gp = (isset($v['tot']) ? $v['tot'] : 0);
                $tot = $gg + $gp;
            }
            // calculo el interes (si no exceptua interes el propietario)
            $int = 0.00;
            if (isset($remanente[$k]['capital']) && $remanente[$k]['capital'] > 0 && !$this->Liquidation->Consorcio->Propietario->exceptuaInteres($k)) {
                // liquidacion incial usa capital (por los decimales), las siguientes liquidaciones usa intval(capital) 
                // tomo solo el capital sin el redondeo, porq sino me genera interes por los centavos del redondeo
                // ej: capital 200.25, interes 200*0.03=6, total=206
                // si uso redondeo: capital 200.25, interes 200.25*0.03=6,0075, total 206.0075. 
                // la cobranza es de 206... en el 1º caso interes acumulado=0, en el segundo es 0.0075
                //$remanente[$k]['capital'] = $remanente[$k]['capital'] < 0 ? $remanente[$k]['capital'] : intval($remanente[$k]['capital']);//nunca llega aca porq el if entra si cap>0

                $int = intval($remanente[$k]['capital']) * $this->Liquidation->Consorcio->getInteres($this->Liquidation->getConsorcioId($id)) / 100;
                if ($int < 0.01) {
                    $int = 0; // a veces queda int = 0.0051213 y al hacer round, pone int=0.01
                }
            }

            $capred = $this->calculaRedondeos((isset($saldosanteriores[$k]['redondeo']) ? $saldosanteriores[$k]['redondeo'] : 0), $tot + (isset($remanente[$k]['capital']) ? $remanente[$k]['capital'] : 0), $int + isset($remanente[$k]['interes']) ? $remanente[$k]['interes'] : 0);
            $intfinal = round($int + (isset($remanente[$k]['interes']) ? $remanente[$k]['interes'] : 0), 2);
            $capant = isset($saldosanteriores[$k]['capital']) ? $saldosanteriores[$k]['capital'] : 0;
            $intant = isset($saldosanteriores[$k]['interes']) ? $saldosanteriores[$k]['interes'] : 0;
            $d[]['SaldosCierre'] = ['propietario_id' => $k, 'liquidation_id' => $id, 'capital' => round($capred['capital'], 2), 'interesactual' => $int, 'interes' => $intfinal, 'redondeo' => round($capred['redondeo'], 2),
                'gastosgenerales' => $gg, 'gastosparticulares' => $gp, 'cobranzas' => (isset($remanente[$k]['cobranzas']) ? $remanente[$k]['cobranzas'] : 0), 'ajustes' => (isset($remanente[$k]['ajustes']) ? $remanente[$k]['ajustes'] : 0), 'capant' => $capant,
                'intant' => $intant, 'redant' => $capant + $intant - intval($capant + $intant)];
            /* if ($k == 1294) {
              debug($saldosanteriores[$k]);
              debug($remanente[$k]);
              debug($tot);
              debug(['propietario_id' => $k, 'liquidation_id' => $id, 'capital' => round($capred['capital'], 2), 'interes' => round($int, 2) + round($remanente[$k]['interes'], 2), 'redondeo' => round($capred['redondeo'], 2)]);
              die;
              } */
        }
        return $d;
    }

    /*
     * Calcula redondeos en base al redondeo anterior y el redondeo del capital de la liquidacion ACTUAL
     * Verifico cual es el decimal del capital 
     */

    public function calculaRedondeos($redondeoanterior, $capital, $interes) {
        $redondeoactual = $capital + $interes - intval($capital + $interes) + $redondeoanterior;
        //debug($redondeoactual);
        if ($redondeoactual > 1) {
            // el redondeo es mayor a 1, entonces se lo sumo al capital y lo q queda es el redondeo actual
            //$capital += 1.00;
            $redondeoactual = $redondeoactual - intval($redondeoactual);
        }
        // al capital le saco los decimales, ya q los decimales restantes no sirven (estan incluidos en el redondeo actual)
        return ['capital' => $capital < 0 ? $capital : ($capital), 'redondeo' => $redondeoactual];
    }

    private function _sumcoefs($data) {
        $total = 0.00;
        if (isset($data['coefgen'])) {
            foreach ($data['coefgen'] as $v) {
                $total += isset($v['tot']) ? $v['tot'] : 0;
            }
        }
        if (isset($data['coefpar'])) {
            foreach ($data['coefpar'] as $v) {
                $total += isset($v['tot']) ? $v['tot'] : 0;
            }
        }
        return $total;
    }

    /*
     * Verifico que no exista ya el registro. Si existe, lo actualizo, sino inserto
     */

    public function guardarTodos($data = []) {
        foreach ($data as $v) {
            $conditions = array('SaldosCierre.propietario_id' => $v['SaldosCierre']['propietario_id'], 'SaldosCierre.liquidation_id' => $v['SaldosCierre']['liquidation_id']);
            $options = array('conditions' => $conditions, 'recursive' => -1, 'fields' => array('SaldosCierre.id'));
            $resul = $this->find('first', $options);
            if (isset($resul['SaldosCierre']['id'])) {
                // seteo la pk para que haga update y no insert
                $this->id = $resul['SaldosCierre']['id'];
            } else {
                $this->create(); // si no creo, no me genera uno nuevo y sigue usando el id anterior
            }
            $this->save($v);
        }
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
