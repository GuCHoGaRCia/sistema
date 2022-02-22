<?php

App::uses('AppModel', 'Model');

class Proveedorspagosnc extends AppModel {

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
        'proveedorsfactura_id' => [
            'numeric' => [
                'rule' => ['numeric'],
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
    ];
    public $belongsTo = [
        'Proveedorspago' => [
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedorspago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Proveedorsfactura' => [
            'className' => 'Proveedorsfactura',
            'foreignKey' => 'proveedorsfactura_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function getNCParaAplicar($proveedor_id = null) {
        return $this->Proveedorsfactura->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorsfactura.importe <' => 0, 'Proveedorsfactura.saldo >' => 0, 'Proveedor.id' => $proveedor_id],
                    'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']],
                        ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Proveedorsfactura.liquidation_id=Liquidation.id']]
                    ],
                    'fields' => ['Proveedorsfactura.id', 'Proveedorsfactura.importe', 'Proveedorsfactura.numero', 'Proveedorsfactura.fecha', 'Proveedorsfactura.saldo', 'Liquidation.consorcio_id']]);
    }

    /* public function beforeDelete($cascade = true) {
      $count1 = $this->Proveedorspagosfactura->find('count', array('conditions' => array('proveedorsfactura_id' => $this->id, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0,
      'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']]]));
      $count2 = $this->Proveedorspagosnc->find('count', array('conditions' => array('proveedorsfactura_id' => $this->id, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0,
      'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']]]));
      if ($count1 + $count2 === 0) {
      return true;
      }
      return false;
      } */

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Proveedorspagosnc.proveedorspago_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
