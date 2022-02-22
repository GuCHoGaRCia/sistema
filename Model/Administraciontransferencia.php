<?php

App::uses('AppModel', 'Model');

class Administraciontransferencia extends AppModel {

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
        'Administraciontransferenciasdetalle' => [
            'className' => 'Administraciontransferenciasdetalle',
            'foreignKey' => 'administraciontransferencia_id',
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

    public function getPagosTransferenciaAdmPorConsor($consorcio, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        $cuentas = array_keys($this->Bancoscuenta->getCuentasBancarias($consorcio));
        if (!empty($cuentas)) {
            $resul = $this->find('all', ['conditions' => ['Administraciontransferenciasdetalle.bancoscuenta_id' => $cuentas] + (!empty($desde) ? ['Proveedorspago.created >=' => $this->fecha($desde)] : []) + (!empty($hasta) ? ['Proveedorspago.created <=' => $this->fecha($hasta)] : []) +
                (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (empty($recuperados) ? ['Administraciontransferenciasdetalle.recuperado' => 0] : []) + (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []),
                'joins' => [['table' => 'administraciontransferenciasdetalles', 'alias' => 'Administraciontransferenciasdetalle', 'type' => 'left', 'conditions' => ['Administraciontransferencia.id=Administraciontransferenciasdetalle.administraciontransferencia_id']],
                ],
                'contain' => ['Proveedorspago', 'Administraciontransferenciasdetalle.importe', 'Administraciontransferenciasdetalle.bancoscuenta_id'],
                'fields' => ['DISTINCT Administraciontransferencia.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha'],
                'group' => 'Administraciontransferencia.id',
                'order' => 'Proveedorspago.fecha desc'
                    ]
            );
            $tmp = $resul;
            if (!empty($resul)) {//hago este engendro porq no se como hacer para traer en la consulta solamente el detalle del pago del consorcio seleccionado
                foreach ($resul as $k => $v) {
                    foreach ($v['Administraciontransferenciasdetalle'] as $r => $s) {
                        if (!in_array($s['bancoscuenta_id'], $cuentas)) {
                            unset($tmp[$k]['Administraciontransferenciasdetalle'][$r]);
                        }
                    }
                }
            }
            return $tmp;
        }
        return [];
    }

    public function getPagosTransferenciaAdm($cuentaadm, $desde, $hasta, $proveedor = null, $anulados = 0, $recuperados = 0) {
        return $this->find('all', ['conditions' => ['Administraciontransferencia.bancoscuenta_id' => $cuentaadm, 'Administraciontransferenciasdetalle.recuperado' => $recuperados] + (!empty($desde) ? ['date(Proveedorspago.created) >=' => $this->fecha($desde)] : []) + (!empty($hasta) ? ['date(Proveedorspago.created) <=' => $this->fecha($hasta)] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (empty($anulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'administraciontransferenciasdetalles', 'alias' => 'Administraciontransferenciasdetalle', 'type' => 'left', 'conditions' => ['Administraciontransferencia.id=Administraciontransferenciasdetalle.administraciontransferencia_id']],
                    ],
                    'contain' => ['Proveedorspago', 'Administraciontransferenciasdetalle'],
                    'fields' => ['DISTINCT Administraciontransferencia.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha'],
                        ]
        );
    }

}
