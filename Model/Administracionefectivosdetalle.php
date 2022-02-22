<?php

App::uses('AppModel', 'Model');

class Administracionefectivosdetalle extends AppModel {

    public $validate = [
        'administracionefectivo_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'consorcio_id' => [
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
        'Administracionefectivo' => [
            'className' => 'Administracionefectivo',
            'foreignKey' => 'administracionefectivo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
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
                'Administracionefectivosdetalle.administracionefectivo_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
