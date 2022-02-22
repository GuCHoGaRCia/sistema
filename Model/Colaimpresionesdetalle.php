<?php

App::uses('AppModel', 'Model');

class Colaimpresionesdetalle extends AppModel {

    public $validate = [
        'colaimpresione_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'reporte' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'imprimir' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'online' => [
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
        'Colaimpresione' => [
            'className' => 'Colaimpresione',
            'foreignKey' => 'colaimpresione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function yaFueImpreso($colaimpresione_id) {
        $resul = $this->find('all', ['conditions' => ['Colaimpresionesdetalle.colaimpresione_id' => $colaimpresione_id, 'Colaimpresionesdetalle.impreso' => 1]]);
        return !empty($resul); // si esta vacio, no hay nada impreso (todavia?). Si tiene datos, es q ya fue impreso (devuelve true)
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Colaimpresionesdetalle.colaimpresione_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
