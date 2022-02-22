<?php

App::uses('AppModel', 'Model');

class GastosParticularesPft extends AppModel {

    public $useTable = 'gastos_particulares_pft';
    public $validate = [
        'gastos_particulare_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'cobranza_id' => [
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
        'GastosParticulare' => [
            'className' => 'GastosParticulare',
            'foreignKey' => 'gastos_particulare_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Cobranza' => [
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function listarCargados($cobranzas) {
        $resul = [];
        foreach ($cobranzas as $k => $v) {
            $d = $this->find('first', ['conditions' => ['GastosParticularesPft.cobranza_id' => $v['Cobranza']['id']]]);
            if (!empty($d)) {
                $resul[] = $v['Cobranza']['id'];
            }
        }
        return $resul;
    }

}
