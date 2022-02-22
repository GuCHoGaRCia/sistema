<?php

App::uses('AppModel', 'Model');

class Chequespropiosadmsdetalle extends AppModel {

    public $validate = [
        'chequespropiosadm_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
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
        'Chequespropiosadm' => [
            'className' => 'Chequespropiosadm',
            'foreignKey' => 'chequespropiosadm_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
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

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Chequespropiosadmsdetalle.chequespropiosadm_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
