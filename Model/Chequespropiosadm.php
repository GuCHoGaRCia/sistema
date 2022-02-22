<?php

App::uses('AppModel', 'Model');

class Chequespropiosadm extends AppModel {

    public $virtualFields = ['tipo' => 11, 'fecha' => 'fecha_vencimiento'];
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'user_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'fecha_emision' => [
            'date' => [
                'rule' => ['date'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'fecha_vencimiento' => [
            'date' => [
                'rule' => ['date'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'concepto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'numero' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'importe' => [
            'decimal' => [
                'rule' => ['decimal'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'conciliado' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'anulado' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Chequespropiosadmsdetalle' => [
            'className' => 'Chequespropiosadmsdetalle',
            'foreignKey' => 'chequespropiosadm_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ]
    ];

    /*
     * Agrega un Cheque Propio de ADMINISTRACION desde Pago a Proveedores
     */

    //	'fe' => '2018-02-19',
    //	'fv' => '2018-02-19',
    //	'c' => 'ChP PP ABETE Y CIA',
    //	'n' => '1',
    //	'i' => '5.00',
    //	'b' => '29',
    //	'badm' => '29',
    //	'd' => array(
    //          (int) 174 (bancoscuenta_id) => '2',   $2
    //          (int) 179 (bancoscuenta_id) => '3'    $3
    //	)

    public function agregar($data) {
        if (!isset($data['fe']) || !isset($data['fv']) || $data['fv'] < $data['fe']) {
            return ['r' => 0, 'e' => 'La fecha de vencimiento debe ser mayor o igual a la de emisión'];
        }
        if (!isset($data['i']) || $data['i'] <= 0) {
            return ['r' => 0, 'e' => 'El importe debe ser mayor a cero'];
        }
        if (!isset($data['c']) || $data['c'] === '' || empty($data['c'])) {
            return ['r' => 0, 'e' => 'Debe ingresar un concepto'];
        }

        $sum = 0;
        if (!empty($data['d'])) {
            foreach ($data['d'] as $k => $v) {// es un CHP de administracion, guardo el importe del ChP de cada consorcio
                $sum += $v;
            }
        }
        $a = (float) $data['i'];
        $b = (float) $sum;
        if ("$a" != "$b") {// dejar la comparacion con STRING, es una chotada sino 904.53 != 904.53 ???!?!?!
            return ['r' => 0, 'e' => 'El importe total es distinto a la suma por Consorcio'];
        }
        $badm = $this->Chequespropiosadmsdetalle->Bancoscuenta->find('list', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => 0], 'recursive' => 0, 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.id']]);
        if (in_array($data['b'], $badm)) {// si es cheque de adm, chequeo q i = sumadeimportesdecadaconsor
            $sum = 0;
            if (!empty($data['d'])) {
                foreach ($data['d'] as $k => $v) {
                    $sum += $v;
                }
            }
            $a = (float) $data['i'];
            $b = (float) $sum;
            if ("$a" != "$b") {// dejar la comparacion con STRING, es una chotada sino 904.53 != 904.53 ???!?!?!
                return ['r' => 0, 'e' => 'El importe total es distinto a la suma por Consorcio'];
            }
        }
        // chequeo todas las cuentas bancarias a ver si existen
        foreach ($data['d'] as $k => $v) {
            $r = $this->Chequespropiosadmsdetalle->Bancoscuenta->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $data['b']], 'recursive' => 0, 'fields' => 'Bancoscuenta.id']);
            if (!isset($data['b']) || empty($r)) {
                return ['r' => 0, 'e' => 'La cuenta bancaria es inexistente'];
            }
        }
        $resul = $this->save(['fecha_emision' => $data['fe'], 'fecha_vencimiento' => $data['fv'], 'concepto' => $data['c'], 'importe' => $data['i'], 'bancoscuenta_id' => $data['b'], 'numero' => $data['n']]);
        if ($resul) {
            // es un CHP de administracion, guardo el importe del ChP de cada consorcio
            $chpid = $resul['Chequespropiosadm']['id'];
            foreach ($data['d'] as $k => $v) {
                if ($v > 0) {// guardo los q tengan importe >0
                    $this->Chequespropiosadmsdetalle->create(); // el consorcio_id es redundante, pero me simplifica en el view
                    $this->Chequespropiosadmsdetalle->save(['chequespropiosadm_id' => $chpid, 'bancoscuenta_id' => $k, 'consorcio_id' => $this->Chequespropiosadmsdetalle->Bancoscuenta->getConsorcio($k), 'proveedorspago_id' => 0, 'importe' => $v]);
                    // NO HAGO NADA HASTA Q NO GUARDE EL PAGO PROVEEDOR!                    
                    // Si la fecha de vencimiento es menor o igual a la fecha actual, modifico el saldo de la cuenta bancaria (se hace al guardar el detalle)
                    //if (strtotime($data['fv']) <= strtotime(date("Y-m-d"))) {
                    //    $this->Chequespropiosadmsdetalle->Bancoscuenta->setSaldo($k, -$v);
                    //}
                }
            }
            return ['r' => 1, 'e' => $this->find('first', array('conditions' => array('Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropiosadm.id' => $chpid), 'recursive' => 0,
                    'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']]],
                    'fields' => array('DISTINCT Chequespropiosadm.id', 'Chequespropiosadm.numero', 'Chequespropiosadm.concepto', 'Chequespropiosadm.importe', 'Chequespropiosadmsdetalle.bancoscuenta_id'),
                    'contain' => ['Chequespropiosadmsdetalle', 'Chequespropiosadmsdetalle.Bancoscuenta.consorcio_id'],
                    'group' => 'Chequespropiosadm.id'// dejar el group sino me duplica, aunque tenga el DISTINCT!
                        )
            )];
        } else {
            return ['r' => 0, 'e' => 'El cheque propio no pudo guardarse'];
        }
    }

    /*
     * Devuelve los cheques pendientes (no anulados, sin depositar y que tengan saldo pendiente)
     */

    public function getChequesPendientes() {
        return $this->find('all', array('conditions' => array('Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropiosadm.anulado' => 0, 'Chequespropiosadmsdetalle.proveedorspago_id' => 0), //'recursive' => 0,
                    'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Chequespropiosadmsdetalle.bancoscuenta_id']]],
                    'fields' => array('DISTINCT Chequespropiosadm.id', 'Chequespropiosadm.numero', 'Chequespropiosadm.concepto', 'Chequespropiosadm.importe', /* 'Bancoscuenta.name', 'Bancoscuenta.consorcio_id' */),
                    'contain' => ['Chequespropiosadmsdetalle', 'Chequespropiosadmsdetalle.Bancoscuenta.consorcio_id'],
                    'group' => 'Chequespropiosadm.id'// dejar el group sino me duplica, aunque tenga el DISTINCT!
                        )
        );
    }

    public function getInfo($id) {
        return $this->find('first', array('conditions' => array('Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropiosadm.id' => $id), 'fields' => ['Chequespropiosadm.fecha_vencimiento', 'Chequespropiosadm.importe', 'Chequespropiosadm.bancoscuenta_id', 'Chequespropiosadm.concepto', 'Chequespropiosadm.numero', 'Chequespropiosadm.anulado']));
    }

    /*
     * Funcion que obtiene el importe de un cheque propio
     */

    public function getImporte($id) {
        $r = $this->find('first', array('conditions' => array('Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropiosadm.id' => $id), 'fields' => array('importe')));
        return empty($r) ? 0 : $r['Chequespropiosadm']['importe'];
    }

    public function getPagosChequespropiosadmPorConsor($consorcio, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        $cuentas = array_keys($this->Chequespropiosadmsdetalle->Bancoscuenta->getCuentasBancarias($consorcio));
        if (!empty($cuentas)) {
            $resul = $this->find('all', ['conditions' => ['Chequespropiosadmsdetalle.bancoscuenta_id' => $cuentas] + (!empty($desde) ? ['Proveedorspago.created >=' => $desde] : []) + (!empty($hasta) ? ['Proveedorspago.created <=' => $hasta] : []) +
                (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []) + (empty($recuperados) ? ['Chequespropiosadmsdetalle.recuperado' => 0] : []),
                'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']],
                    ['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Chequespropiosadmsdetalle.proveedorspago_id']],
                ], //DEJAR EL DISTINCT Porq sino duplica algunos egresos de caja no se porq 
                'contain' => ['Chequespropiosadmsdetalle.importe', 'Chequespropiosadmsdetalle.bancoscuenta_id'],
                'fields' => ['DISTINCT Chequespropiosadm.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha'],
                'group' => 'Chequespropiosadm.id',
                'order' => 'Proveedorspago.fecha desc'
                    ]
            );
            $tmp = $resul;
            if (!empty($resul)) {//hago este engendro porq no se como hacer para traer en la consulta solamente el detalle del pago del consorcio seleccionado
                foreach ($resul as $k => $v) {
                    foreach ($v['Chequespropiosadmsdetalle'] as $r => $s) {
                        if (!in_array($s['bancoscuenta_id'], $cuentas)) {
                            unset($tmp[$k]['Chequespropiosadmsdetalle'][$r]);
                        }
                    }
                }
            }
            return $tmp;
        }
        return [];
    }

    public function getPagosChequespropiosadm($cuentaadm, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        return $this->find('all', ['conditions' => ['Chequespropiosadm.bancoscuenta_id' => $cuentaadm, 'Chequespropiosadmsdetalle.recuperado' => $recuperados] + (!empty($desde) ? ['date(Proveedorspago.created) >=' => $this->fecha($desde)] : []) + (!empty($hasta) ? ['date(Proveedorspago.created) <=' => $this->fecha($hasta)] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) /* + (!empty($consorcio) ? ['Cajasegreso.consorcio_id' => $consorcio, 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] : []) */ +
                    (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'right', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']],
                        ['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Chequespropiosadmsdetalle.proveedorspago_id']],
                    ], //DEJAR EL DISTINCT Porq sino duplica algunos egresos de caja no se porq         
                    'contain' => ['Chequespropiosadmsdetalle', 'Chequespropiosadmsdetalle.Proveedorspago'], // dejar esto sino me duplica todo
                    'fields' => ['DISTINCT Chequespropiosadm.id', 'Chequespropiosadm.fecha', 'Chequespropiosadm.id', 'Chequespropiosadm.concepto', 'Chequespropiosadm.numero'],
                        ]
        );
    }

    /*
     * Los cheques propios pertenecen a un cliente y usuario
     */

    public function beforeSave($options = array()) {
        $this->data['Chequespropiosadm']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        $this->data['Chequespropiosadm']['user_id'] = $_SESSION['Auth']['User']['id'];
        /* if (isset($this->data['Chequespropiosadm']['importe']) && !isset($this->data['Chequespropiosadm']['id'])) {
          //esta creando uno nuevo desde /add
          } */
        return true;
    }

    /*
     * Funcion que anula un cheque propio de adm
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Chequespropiosadm.id' => $id, 'Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropiosadm.anulado' => 0),
            'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']]],
            'fields' => ['Chequespropiosadm.id', 'Chequespropiosadm.concepto', 'Chequespropiosadm.importe', 'Chequespropiosadm.bancoscuenta_id', 'Chequespropiosadm.fecha_vencimiento']));
        if (!empty($r)) {
            // anulo el cheque propio (no pongo return $this->save(...) porq no devuelve true or false! No se q onda..
            $this->save(['id' => $r['Chequespropiosadm']['id'], 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['Chequespropiosadm']['concepto']], ['callbacks' => false]);
            if (strtotime($r['Chequespropiosadm']['fecha_vencimiento']) <= strtotime(date("Y-m-d"))) {
                //$this->Chequespropiosadmsdetalle->Bancoscuenta->setSaldo($r['Chequespropiosadmsdetalle']['bancoscuenta_id'], $r['Chequespropiosadm']['importe']);
                $data2 = ['caja_id' => 0, 'bancoscuenta_id' => $r['Chequespropiosadm']['bancoscuenta_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'cobranza_id' => null, 'fecha' => $r['Chequespropiosadm']['fecha_vencimiento'], 'concepto' => '[ANULADO] ' . $r['Chequespropiosadm']['concepto'], 'importe' => $r['Chequespropiosadm']['importe'], 'es_transferencia' => 0, 'conciliado' => 0, 'anulado' => 1];
                $this->Chequespropiosadmsdetalle->Bancoscuenta->Bancosdepositosefectivo->crear($data2);
            }
            // IMPORTANTE!!!!!!!!!
            // los ChPADM cuyo vencimiento es a futuro, crea los créditos en el cron_movimientosbancarios.php !!!!!!!!!
            return true;
        } else {
            return false;
        }
    }

    /*
     * Verifico que la fecha de emision sea menor o igual a la de vencimiento
     */

    public function checkDates($check) {
        return (date('Y-m-d', strtotime($this->data['Chequespropiosadm']['fecha_emision'])) <= date('Y-m-d', strtotime($this->data['Chequespropiosadm']['fecha_vencimiento'])));
    }

// funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Chequespropiosadm.client_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
