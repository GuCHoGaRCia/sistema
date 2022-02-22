<?php

App::uses('AppModel', 'Model');

class Plataformasdepago extends AppModel {

    public $displayField = 'titulo';
    public $validate = [
        'titulo' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'habilitada' => [
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
    public $hasMany = [
        'Plataformasdepagosconfig' => [
            'className' => 'Plataformasdepagosconfig',
            'foreignKey' => 'plataformasdepago_id',
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
        return $this->find('list', ['conditions' => ['Plataformasdepago.habilitada' => 1]]);
    }

    public function get() {
        return Hash::combine($this->find('all', ['conditions' => ['Plataformasdepago.habilitada' => 1]]), '{n}.Plataformasdepago.id', '{n}.Plataformasdepago');
    }

    public function getConfig($cliente) {
        return $this->Plataformasdepagosconfig->getConfig($cliente);
    }

}
