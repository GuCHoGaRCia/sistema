<?php

App::uses('AppModel', 'Model');

class GastosGenerale extends AppModel {

    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'rubro_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'description' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $belongsTo = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Rubro' => array(
            'className' => 'Rubro',
            'foreignKey' => 'rubro_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = [
        'GastosGeneraleDetalle' => [
            'className' => 'GastosGeneraleDetalle',
            'foreignKey' => 'gastos_generale_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Proveedorsfactura' => [
            'className' => 'Proveedorsfactura',
            'foreignKey' => 'gastos_generale_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
    ];

    public function canEdit($id) {
        return !empty($this->find('first', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosGenerale.id' => $id), 'joins' => [['table' => 'rubros', 'alias' => 'Rubro', 'type' => 'left', 'conditions' => ['Rubro.id=GastosGenerale.rubro_id']], ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Rubro.consorcio_id']]])));
    }

    public function beforeSave($options = []) {
        if (isset($this->data['GastosGenerale']['description'])) {
            $this->data['GastosGenerale']['description'] = $this->cleanHTML($this->data['GastosGenerale']['description']);
        }
        return true;
    }

    public function addGasto($data) {
        // {"l":"50","r":"32","h":"true","d":"<p>df asdf asd f<\/p>\n","c_21":"753.30","c_22":"461.70","liq":"50","ord":"X"}
        //  l: 8299,r: 1531,h: false,d: <p>44</p>,id: 0,c_396: 44,c_397: 0,liq: 8299,ord: 

        $r = $this->_beforeAddGasto($data);
        if (!empty($r)) {
            return [0 => ['description' => $r], []];
        }

        $d = ['liquidation_id' => $data['x'] == "false" ? 0 : $data['l'], 'rubro_id' => $data['r'], 'description' => $data['d'], 'coeficiente_id' => 0,
            'numero_factura' => 0, 'heredable' => $data['h'] == "true" ? 1 : 0, 'orden' => isset($data['ord']) ? $data['ord'] : 0, 'habilitado' => $data['x'] == "true" ? 1 : 0];
        if (isset($data['id']) && $data['id'] != 0) {
            $d += ['id' => $data['id']];
            $data['m'] = true; // si es un edit, agrego el "modificado"
        } else {
            $this->create();
            $d += ['user_id' => $_SESSION['Auth']['User']['id']]; // guardo el usuario que creó el gasto
        }
        $resul = false;
        $rr = $this->save($d);
        if ($rr) {
            $id = isset($data['id']) && $data['id'] == "0" ? $rr['GastosGenerale']['id'] : $data['id']; // si es un edit, tomo el id enviado, sino el q salga de la inserción
            $resul = true;
            $data['id'] = $id;
            $data['p'] = !empty($this->Liquidation->Proveedorsfactura->getFacturasPagas($id));
            $this->GastosGeneraleDetalle->deleteAll(['gastos_generale_id' => $id], false); // borro los detalles q tengan id = $id (si es cero, no borra nada)
            foreach ($data as $k => $v) { // permito montos negativos (algunos cargan créditos como gastos negativos)
                if (!empty($v) && substr($k, 0, 2) == 'c_') {// el !empty($v) es: si es 0 o '', no guardo el detalle de ese coeficiente, al pedo guardar los ceros!
                    // es un coeficiente, obtengo el c_XX
                    $coeficiente_id = substr($k, 2);
                    $data[$k] = $v == '' ? 0 : $v;
                    $d = ['gastos_generale_id' => $id, 'coeficiente_id' => $coeficiente_id, 'amount' => $v];
                    $this->GastosGeneraleDetalle->create();
                    $resul = $this->GastosGeneraleDetalle->save($d);
                }
            }
        }
        return [$this->validationErrors, $data];
    }

    private function _beforeAddGasto($data) {
        if ($data['l'] != 0) {
            if (!$this->Liquidation->canEdit($data['l'])) {
                return __('La Liquidación es inexistente');
            }
            if (!$this->Rubro->canEdit($data['r'])) {
                return __('El Rubro es inexistente');
            }
            $consorcio_id = $this->Liquidation->getConsorcioId($data['l']);
            if ($consorcio_id != $this->Rubro->getConsorcioId($data['r'])) {
                return __('El Gasto no se guardó: el Rubro no pertenece al Consorcio. Recargue la pantalla e intente nuevamente');
            }
            $this->Liquidation->id = $data['l'];
            if ($this->Liquidation->field('bloqueada') == 1) {
                return __("La liquidaci&oacute;n se encuentra bloqueada, no se puede modificar el Gasto.");
            }
        } else {
            $consorcio_id = $this->Rubro->getConsorcioId($data['r']); // carga un gasto deshabilitado, la liq es cero x lo tanto el consorcio es cero. Obtengo el consor del rubro
        }

        foreach ($data as $k => $v) {
            if (!empty($v) && substr($k, 0, 2) == 'c_') {// es un coeficiente, obtengo el c_XX
                $coeficiente_id = substr($k, 2);
                if ($consorcio_id != $this->GastosGeneraleDetalle->Coeficiente->getConsorcioId($coeficiente_id)) {
                    return __('El Gasto no se guardó: el Coeficiente no pertenece al Consorcio. Recargue la pantalla e intente nuevamente');
                }
            }
        }
        return '';
    }

    //{"GastosGenerale":{
    //	"liquidation_id":"50",
    //	"rubro_id":"32",
    //	"description":"<p>3<\/p>\n",
    //	"date":"20150915"},
    //	"GastosGeneraleDetalle":[{
    //		"coeficiente_id":"1",
    //		"amount":"1"},{
    //		"coeficiente_id":"2",
    //		"amount":"2"}]
    //}
    public function delGasto($data) {
        $this->id = $data['id'];
        $this->Liquidation->id = $this->field('liquidation_id');
        // solo puedo borrar gastos de una liquidación no bloqueada
        if ($this->Liquidation->field('bloqueada') == 0) {
            return $this->delete();
        }
        return false;
    }

    /*
     * Obtiene el total de gastos de una liquidacion especifica
     */

    public function calculaTotalesGastos($liquidation_id) {
        $total = 0;
        $gg = $this->Liquidation->Resumene->getLiquidationData($liquidation_id);
        if (empty($gg)) {
            return 0;
        }
        $data = json_decode($gg['Resumene']['data'], true);
        if (isset($data['gastosinfo'])) {
            foreach ($data['gastosinfo'] as $l => $m) {
                if (isset($m['GastosGeneraleDetalle']['amount'])) {
                    $total += $m['GastosGeneraleDetalle']['amount'];
                } else {
                    // forma nueva
                    foreach ($m['GastosGeneraleDetalle'] as $k => $v) {
                        $total += $v['amount'];
                    }
                }
            }
        }

        return $total;
    }

    /*
     * Devuelve un array con la suma de los totales por coeficiente. En caso q la liquidacion tenga presupuesto, se hace
     * la cuenta: presupuesto anterior+gastosgenerales-presupuesto actual
     */

    public function sumarGastosPorCoeficiente($liquidation_id) {
        $options = array('conditions' => array(
                'GastosGenerale.liquidation_id' => $liquidation_id, 'GastosGenerale.habilitado' => 1, 'Rubro.habilitado' => 1),
            'joins' => [['table' => 'rubros', 'alias' => 'Rubro', 'type' => 'left', 'conditions' => ['Rubro.id=GastosGenerale.rubro_id']],
                ['table' => 'gastos_generale_detalles', 'alias' => 'GastosGeneraleDetalle', 'type' => 'left', 'conditions' => ['GastosGenerale.id=GastosGeneraleDetalle.gastos_generale_id']]],
            'fields' => array('GastosGeneraleDetalle.coeficiente_id', 'sum(GastosGeneraleDetalle.amount) as total'),
            'group' => array('GastosGeneraleDetalle.coeficiente_id'));
        $resul = $this->find('all', $options);
        foreach ($resul as $k => $v) { // obtengo los presupuestos de cada coeficiente y hago la cuenta
            $presupActual = $this->Liquidation->Liquidationspresupuesto->getPresupuesto($liquidation_id, $v['GastosGeneraleDetalle']['coeficiente_id']);
            $presupAnterior = $this->Liquidation->Liquidationspresupuesto->getPresupuesto($this->Liquidation->getLastLiquidation($liquidation_id), $v['GastosGeneraleDetalle']['coeficiente_id']);
            $resul[$k][0]['total'] += (-$presupAnterior + $presupActual);
        }
        return $resul;
    }

    /*
     * Si lista los $solohabilitados, 
     * Agrego contain Proveedorsfactura para saber si tiene gasto asociado y poner la carpetita verde o roja en GastosGenerales/add
     */

    public function listarGastosPorCoeficiente($liquidation_id, $solohabilitados = 0) {
        return $this->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] + ($solohabilitados == 1 ? ['GastosGenerale.liquidation_id' => $liquidation_id, 'GastosGenerale.habilitado' => true] : ['OR' => [['GastosGenerale.liquidation_id' => 0, 'GastosGenerale.habilitado' => false], ['GastosGenerale.liquidation_id' => $liquidation_id, 'GastosGenerale.habilitado' => true]]]),
                    'joins' => [['table' => 'rubros', 'alias' => 'Rubro', 'type' => 'left', 'conditions' => ['Rubro.id=GastosGenerale.rubro_id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Rubro.consorcio_id']]],
                    'order' => 'GastosGenerale.orden,GastosGenerale.id', 'contain' => ['GastosGeneraleDetalle', 'Proveedorsfactura']]);
    }

    public function heredar($actual, $anterior) {
        $options = array('conditions' => array('GastosGenerale.liquidation_id' => $anterior, 'GastosGenerale.heredable' => true),
            'fields' => ['GastosGenerale.*', 'GastosGeneraleDetalle.*'],
            'joins' => [['table' => 'gastos_generale_detalles', 'alias' => 'GastosGeneraleDetalle', 'type' => 'left', 'conditions' => ['GastosGenerale.id=GastosGeneraleDetalle.gastos_generale_id']]]);
        $resul = $this->find('all', $options);
        $insertados = [];
        foreach ($resul as $k => $v) {
            // creo los nuevos gastos generales
            $det = $v; // el detalle
            if (!in_array($v['GastosGenerale']['id'], $insertados)) {
                // si el gasto general ya fue insertado antes, no lo vuelvo a crear (solo creo el detalle que falte)
                $tmp = $v; // el gasto general
                $insertados[] = $tmp['GastosGenerale']['id'];
                unset($tmp['GastosGeneraleDetalle']); // al general le borro el detalle
                unset($tmp['GastosGenerale']['id']);  // al general le borro el id sino va a hacer update()
                $tmp['GastosGenerale']['liquidation_id'] = $actual;
                unset($tmp['GastosGenerale']['created']);
                unset($tmp['GastosGenerale']['modified']);
                $this->create();
                $resul = $this->save($tmp, false);
            }
            unset($det['GastosGenerale']);        // al detalle le borro el general
            unset($det['GastosGeneraleDetalle']['id']);  // al detalle le borro el id sino va a hacer update()

            $det['GastosGeneraleDetalle']['gastos_generale_id'] = $resul['GastosGenerale']['id'] ?? 0;
            $this->GastosGeneraleDetalle->create();
            $this->GastosGeneraleDetalle->save($det, false);
        }
    }

    public function habilita($id, $liquidation_id) {
        $this->id = $id;
        if ($this->field('habilitado') == 1) {
            $this->saveField('habilitado', false);
            $this->saveField('liquidation_id', 0);
        } else {
            $this->saveField('habilitado', 1);
            $this->saveField('liquidation_id', $liquidation_id);
        }
        return 1;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Liquidation.periodo LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Rubro.name LIKE' => '%' . $data['buscar'] . '%',
                'GastosGenerale.description LIKE' => '%' . $data['buscar'] . '%',
                'GastosGeneraleDetalle.amount' => $data['buscar'],
        ));
    }

}
