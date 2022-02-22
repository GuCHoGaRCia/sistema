<?php

App::uses('AppModel', 'Model');

class Reparacionesactualizacionesllavesmovimiento extends AppModel {

    public $validate = [
        'reparacionesactualizacione_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'llavesmovimiento_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Reparacionesactualizacione' => [
            'className' => 'Reparacionesactualizacione',
            'foreignKey' => 'reparacionesactualizacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Llavesmovimiento' => [
            'className' => 'Llavesmovimiento',
            'foreignKey' => 'llavesmovimiento_id',
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
                'Reparacionesactualizacionesllavesmovimiento.reparacionesactualizacione_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
