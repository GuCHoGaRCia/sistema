<?php

App::uses('AppModel', 'Model');

class Administracionefectivo extends AppModel {

    public $validate = [
        'proveedorspago_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'bancoscuenta_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ]
    ];
    public $belongsTo = [
        'Proveedorspago' => [
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedorspago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Bancoscuenta' => [
            'className' => 'Bancoscuenta',
            'foreignKey' => 'bancoscuenta_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Administracionefectivosdetalle' => [
            'className' => 'Administracionefectivosdetalle',
            'foreignKey' => 'administracionefectivo_id',
            'dependent' => false,
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

    public function getPagosEfectivoAdmPorConsor($consorcio, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        return $this->find('all', ['conditions' => ['Administracionefectivosdetalle.consorcio_id' => $consorcio] + (!empty($desde) ? ['date(Proveedorspago.created) >=' => $desde] : []) + (!empty($hasta) ? ['date(Proveedorspago.created) <=' => $hasta] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (empty($recuperados) ? ['Administracionefectivosdetalle.recuperado' => 0] : []) + (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'administracionefectivosdetalles', 'alias' => 'Administracionefectivosdetalle', 'type' => 'left', 'conditions' => ['Administracionefectivo.id=Administracionefectivosdetalle.administracionefectivo_id']],
                    ], //DEJAR EL DISTINCT Porq sino duplica algunos egresos de caja no se porq 
                    'contain' => ['Proveedorspago', 'Administracionefectivosdetalle'],
                    'fields' => ['DISTINCT Administracionefectivo.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha'],
                        ]
        );
    }

    public function getPagosEfectivoAdm($cuentaadm, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        return $this->find('all', ['conditions' => ['Administracionefectivo.bancoscuenta_id' => $cuentaadm, 'Administracionefectivosdetalle.recuperado' => $recuperados] + (!empty($desde) ? ['date(Proveedorspago.created) >=' => $this->fecha($desde)] : []) + (!empty($hasta) ? ['date(Proveedorspago.created) <=' => $this->fecha($hasta)] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'administracionefectivosdetalles', 'alias' => 'Administracionefectivosdetalle', 'type' => 'left', 'conditions' => ['Administracionefectivo.id=Administracionefectivosdetalle.administracionefectivo_id']],
                        ['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Administracionefectivo.proveedorspago_id']],
                    ], //DEJAR EL DISTINCT Porq sino duplica algunos egresos de caja no se porq 
                    'contain' => ['Administracionefectivosdetalle'],
                    'fields' => ['DISTINCT Administracionefectivo.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha'],
                        ]
        );
    }

}
