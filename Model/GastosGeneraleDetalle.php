<?php

App::uses('AppModel', 'Model');

class GastosGeneraleDetalle extends AppModel {

    public $validate = [
        'gastos_generale_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'coeficiente_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'amount' => [
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
        'GastosGenerale' => [
            'className' => 'GastosGenerale',
            'foreignKey' => 'gastos_generale_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Coeficiente' => [
            'className' => 'Coeficiente',
            'foreignKey' => 'coeficiente_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

}
