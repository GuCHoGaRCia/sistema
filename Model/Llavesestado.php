<?php

App::uses('AppModel', 'Model');

class Llavesestado extends AppModel {

    public $displayField = 'nombre';
    public $validate = [
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $hasMany = [
        'Llavesmovimiento' => [
            'className' => 'Llavesmovimiento',
            'foreignKey' => 'llavesestado_id',
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
        'Llave' => [
            'className' => 'Llave',
            'foreignKey' => 'llavesestado_id',
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

    public function getList() {
        return $this->find('list', ['order' => 'nombre']);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Llavesestado.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
