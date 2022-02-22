<?php

App::uses('AppModel', 'Model');

class Colaimpresione extends AppModel {

    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'bloqueado' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'linkenviado' => array(
            'boolean' => array(
                'rule' => array('date'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = array(
        'Colaimpresionesdetalle' => array(
            'className' => 'Colaimpresionesdetalle',
            'foreignKey' => 'colaimpresione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function getLiquidationId($id) {
        $this->id = $id;
        return $this->field('liquidation_id');
    }

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Colaimpresione.client_id' => $_SESSION['Auth']['User']['client_id'], 'Colaimpresione.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    /*
     * Verifico si existen elementos sin imprimir en la cola de impresion
     */

    public function verificar() {
        $n = $this->query('select id as c from colaimpresionesdetalles where imprimir=1 and impreso=0 limit 1');
        return $n;
    }

    public function addCola($liquidation_id, $model, $client_id = null) {
        if (!in_array($model, App::objects('Model'))) {
            return ['e' => 1, 'd' => "Los datos ingresados son incorrectos"];
        }
        // cheque q la fecha vencimiento no haya pasado
        $this->Liquidation->id = $liquidation_id;
        if ($this->fechaEsMenorIgualQue($this->Liquidation->field('vencimiento'), date("Y-m-d"), true)) {
            return ['e' => 1, 'd' => "<img src='/sistema/img/warning.png' /> La fecha de Vencimiento ya pasó, no se puede finalizar!"];
        }
        // D  = dia 0 = sumar 3 dias (miercoles)
        // L  = dia 1 = sumar 3 dias (jueves)
        // M  = dia 2 = sumar 3 dias (viernes)
        // Mi = dia 3 = sumar 5 dias (lunes)
        // J  = dia 4 = sumar 5 dias (martes)
        // V  = dia 5 = sumar 5 dias (miercoles)
        // S  = dia 6 = sumar 4 dias (miercoles)
        $cant = [3, 3, 3, 5, 5, 5, 4];
        $f = date("Y-m-d", strtotime("+" . $cant[date("w")] . " days"));
        if ($this->fechaEsMenorIgualQue($this->Liquidation->field('limite'), $f, true)) {
            return ['e' => 1, 'd' => "El Vencimiento debe ser posterior a las 48hs hábiles siguientes al dia actual"];
        }

        $num = $this->checkUnique($liquidation_id, $client_id);
        if (!empty($num)) {
            return ['e' => 1, 'd' => "La Liquidación ya se encuentra finalizada"];
        }

        if ($this->isBloqueado($num)) {
            return ['e' => 1, 'd' => "La Liquidación no pudo ser finalizada porque se encuentra bloqueada"];
        }

        $this->create();
        $resul = $this->save(['liquidation_id' => $liquidation_id, 'client_id' => (empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id)], false);
        if (isset($resul['Colaimpresione']['id'])) {
            $this->Liquidation->id = $liquidation_id;
            $this->Liquidation->saveField('bloqueada', 1);
        }
        $lt = $this->Liquidation->getLiquidationsTypeId($liquidation_id);
        $conf = $this->Client->Consorcio->Consorciosconfiguration->getConfiguracion($this->Liquidation->field('consorcio_id'), $lt);
        if (empty($conf)) {
            return ['e' => 1, 'd' => "El Consorcio no posee configuración"];
        }
        $reportes = ['imprimerc' => 'resumenesdecuentas', 'imprimerg' => 'resumengastos', 'imprimecs' => 'composicionsaldos'];
        $online = ['resumenesdecuentas' => 'onlinerc', 'resumengastos' => 'onlinerg', 'composicionsaldos' => 'onlinecs'];
        $imprime = false;
        foreach ($reportes as $r => $s) { // 'imprimerc' => true, 'imprimerg' => false, etc
            // al poner en la cola, si está configurado como online, ya lo pone online. Le doy la opcion al admin de quitar el online manualmente en liq finalizadas
            $this->Colaimpresionesdetalle->create();
            $this->Colaimpresionesdetalle->save(['colaimpresione_id' => $resul['Colaimpresione']['id'], 'reporte' => $s,
                'imprimir' => $conf[$r], 'poneronline' => $conf[$online[$s]], 'impreso' => 0, 'online' => $conf[$online[$s]]]);
            if ($conf[$r]) {
                $imprime = true;
            }
        }
        if (!$imprime) {// si no tiene nada para imprimir, se autobloquea
            $this->id = $resul['Colaimpresione']['id'];
            $this->saveField('bloqueado', 1);
        }
        return ['e' => 0];
    }

    /*
     * Devuelvo los reportes que se encuentran "Impreso / Online" en la cola de impresion del consorcio solicitado para que se muestre
     * en el Panel del Propietario solamente aquellos que están "Impreso / Online"
     */

    public function getReportesenCola($ids) {
        $reportes = [];
        if (!empty($ids)) {
            // hago array_unique porq $ids tiene de cada propietario a q consor corresponde, y si un mismo propietario de un consorcio tiene varios deptos,
            // entonces va a traer muchas veces el id del consorcio
            $ids = array_unique($ids); //$k=propietario, $v=consorcio
            foreach ($ids as $k => $v) {
                // obtengo las 5 ultimas liquidaciones del consorcio $v
                $liquidaciones = $this->Liquidation->find('list', ['conditions' => ['Liquidation.consorcio_id' => $v, 'Liquidation.cerrada' => 1, 'Liquidation.inicial' => 0],
                    'order' => 'Liquidation.closed desc', 'limit' => 7]); // ver liquidation->getLastLiquidationsFromConsorcio
                //	(int) 7 => 'Expensa Sep2014',
                //	(int) 50 => 'Liquidacion fondo',
                //	(int) 101 => 'Expensa Oct2014'
                $reportes[$v] = [];
                foreach ($liquidaciones as $r => $s) {
                    // obtengo los reportes en Cola de Impresion de la liquidacion $r que se encuentren "Listo" e "Impreso / Online"
                    $rep = $this->find('all', ['conditions' => ['Colaimpresione.liquidation_id' => $r, /* 'Colaimpresione.bloqueado' => true, */ 'Colaimpresionesdetalle.online' => true],
                        'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']]],
                        'fields' => ['Colaimpresione.liquidation_id', 'Colaimpresionesdetalle.reporte']]);
                    foreach ($rep as $p) {
                        $reportes[$v][$p['Colaimpresionesdetalle']['reporte']][$p['Colaimpresione']['liquidation_id']] = '';
                    }
                }
            }
        }

        //	(int) 7 => array(
        //		'resumenesdecuentas' => array(
        //			(int) 7 => '',
        //			(int) 50 => '',
        //			(int) 101 => ''
        //		),
        //		'resumengastos' => array(
        //			(int) 50 => ''
        //		)
        //	),
        //	(int) 18 => array()
        //debug($reportes);
        //die;
        return $reportes;
    }

    public function checkUnique($liquidation_id, $client_id) {
        $resul = $this->find('first', array(
            'conditions' => array('Colaimpresione.liquidation_id' => $liquidation_id, 'Colaimpresione.client_id' => (empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id)/* , 'reporte' => $reporte */),
            'fields' => ['Colaimpresione.id']));
        if (empty($resul)) {
            return 0;
        }
        return $resul['Colaimpresione']['id'];
    }

    public function view($id, $reporte) {
        $client_id = (($_SESSION['Auth']['User']['is_admin'] == 0) ? $_SESSION['Auth']['User']['client_id'] : $this->find('first', ['conditions' => ['Colaimpresione.id' => $id], 'fields' => 'Colaimpresione.client_id'])['Colaimpresione']['client_id']);
        $resul = $this->find('first', array('conditions' => array('Colaimpresione.id' => $id, 'Colaimpresionesdetalle.reporte' => $reporte, $client_id),
            'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']],
                ['table' => 'resumenes', 'alias' => 'Resumene', 'type' => 'left', 'conditions' => ['Colaimpresione.liquidation_id=Resumene.liquidation_id']]],
            'fields' => ['Colaimpresionesdetalle.reporte', 'Resumene.data']));
        if (empty($resul)) {
            return null;
        }
        return ['reporte' => $resul['Colaimpresionesdetalle']['reporte'], 'data' => $resul['Resumene']['data']];
    }

    /*
     * Verifica si el reporte se encuentra bloqueado
     */

    public function isBloqueado($check = null) {
        if (isset($this->data['Colaimpresione']['id'])) {
            $this->id = $this->data['Colaimpresione']['id']; // cuando edita desde el index
            return !($this->field('bloqueado'));
        }
        $this->id = $check; // cuando agrega a la cola
        return (bool) ($this->field('bloqueado') == 1);
    }

    public function beforeDelete($cascade = true) {
        // verifico si tiene alguna liquidación siguiente con cobranzas, en ese caso no permito eliminar


        if ($this->isBloqueado($this->id)) {
            return false;
        } else {
            // se puede borrar, tengo q sacar el bloqueada de liquidaciones
            $this->Liquidation->id = $this->field('liquidation_id');
            $this->Liquidation->saveField('bloqueada', 0);
            return true;
        }
    }

    /*
     * Funcion q bloquea o desbloquea un reporte de la cola de impresion y hace lo correspondiente con la liquidacion asociada
     */

    public function bloquear($id = null) {
        $this->id = $id;
        $this->saveField('impreso', !$this->field('impreso'));
        $this->saveField('online', !$this->field('online'));
        $this->saveField('bloqueado', 1); // si intenta poner "Impreso / online" el reporte sin antes haberlo dejado "Listo"
        // ahora bloqueo la liquidación correspondiente al reporte en cola (si est� bloqueando)
        if ($this->field('impreso') == 1) {
            $this->Liquidation->id = $this->field('liquidation_id');
            $this->Liquidation->saveField('bloqueada', 1);
            //$this->Client->Aviso->enviarLink($this->field('liquidation_id')); // envio los links a los propietarios del consorcio asociado (esta bloqueando la liquidacion)
        } else {
            // desbloque el reporte, desbloqueo los demas reportes de la liquidacion y desbloqueo la liquidación
            $this->updateAll(['Colaimpresione.impreso' => 0, 'Colaimpresione.bloqueado' => 0], ['Colaimpresione.liquidation_id' => $this->field('liquidation_id')]);
            $this->Liquidation->id = $this->field('liquidation_id');
            $this->Liquidation->saveField('bloqueada', 0);
        }
    }

    /*
     * Funcion q bloquea o desbloquea un reporte de la cola de impresion y hace lo correspondiente con la liquidacion asociada
     */

    public function enviarLink($id = null) {
        $this->id = $id;
        $this->Client->Aviso->enviarLink($this->field('liquidation_id'), $this->field('client_id')); // envio los links a los propietarios del consorcio asociado (esta bloqueando la liquidacion)
        $this->saveField('linkenviado', date('Y-m-d H:i:s'));
    }

    /*
     * Funcion q genera y envia el archivo de deuda a la plataforma correspondiente del cliente
     */

    public function enviarsaldo($id = null) {
        $this->id = $id;
        $consorcio = $this->Liquidation->getConsorcioId($this->field('liquidation_id'));
        $consorcio_code = $this->Client->Consorcio->getConsorcioCode($consorcio);
        $d = $this->Liquidation->Resumene->getLiquidationData($this->field('liquidation_id'));
        if (empty($d)) {
            return ['e' => 1];
        }
        $data = json_decode($d['Resumene']['data'], true);
        $ltprefijo = $this->Liquidation->LiquidationsType->getPrefijo($this->field('client_id'), $this->Liquidation->getLiquidationsTypeId($this->field('liquidation_id')));
        $liquidacion = $this->Liquidation->find('first', ['conditions' => ['Liquidation.id' => $this->field('liquidation_id')], 'recursive' => 0, 'fields' => 'Liquidation.*'])['Liquidation'];
        $usa2cuotas = $this->Client->Consorcio->is2Cuotas($consorcio);
        $x = ClassRegistry::init('Plataformasdepago');
        $plataforma = $x->getConfig($this->field('client_id'));
        $plataformas = $x->get();
        $p = $plataformas[$plataforma['Plataformasdepagosconfig']['plataformasdepago_id']]['modelo'];
        $client_code = isset($plataforma['Plataformasdepagosconfig']['codigo']) && !empty($plataforma['Plataformasdepagosconfig']['codigo']) ? $plataforma['Plataformasdepagosconfig']['codigo'] : $this->Client->getClientCode($this->field('client_id'));
        $roela = "";
        //busco el codigo de convenio de roela (si usa roela)
        if ($plataforma['Plataformasdepagosconfig']['plataformasdepago_id'] == 3) {
            $key = $this->buscaLista2($plataforma['Plataformasdepagosconfigsdetalle'], ['consorcio_id' => $consorcio]);
            if ($key !== []) {
                $roela = $plataforma['Plataformasdepagosconfigsdetalle'][$key]['valor'];
            }
        }
        $file = $p::generarArchivoInformeDeuda($client_code, $consorcio_code, $liquidacion, $data, $ltprefijo, $usa2cuotas, $plataforma['Plataformasdepagosconfig']['comision'], $plataforma['Plataformasdepagosconfig']['minimo'], $roela); //datointerno roela
        $resul = $p::enviarSaldosPlataformaPagos($client_code, $file);
        $fecha = date('Y-m-d H:i:s');
        if (($resul && $roela !== "") || ($resul === true || (is_array($resul) && $resul['e'] == 0))) {
            if ($roela !== "") {//solo para roela
                if (is_numeric($roela)) {
                    $file = $resul; //numero transaccion recibida
                } else {// roela tiró error, salgo y aviso
                    return ['e' => 1, 'd' => "Error recibido de Roela: " . h($resul)];
                }
            }

            $this->id = $id;
            $this->saveField('saldoenviado', $fecha);
            $this->saveField('archivo', $file . "#" . $this->field('archivo')); //guardo el archivo generado y enviado, para poder descargarlo del ftp si es necesario
            return ['e' => 0, 'f' => $fecha];
        }
        //return is_array($resul) ? $resul + ['f' => $fecha] : false;
        if (is_array($resul)) {
            return ['e' => 0, 'f' => $fecha];
        } else {
            return ['e' => 1];
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
                'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
                'concat(Client.name," - ",Consorcio.name," - ",Liquidation.name) LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
