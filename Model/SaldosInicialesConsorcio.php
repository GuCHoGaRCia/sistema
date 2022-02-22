<?php

App::uses('AppModel', 'Model');

class SaldosInicialesConsorcio extends AppModel {

    public $validate = [
        'liquidations_type_id' => [
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
        'saldo' => [
            'decimal' => [
                'rule' => ['decimal'],
                'message' => 'Debe ser un numero decimal',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'LiquidationsType' => [
            'className' => 'LiquidationsType',
            'foreignKey' => 'liquidations_type_id',
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

    /*
     * Devuelve el saldo inicial de un consorcio y su tipo de liquidacion. El order se lo agregué porq hay duplicados con saldo cero, y toma el primero sino..
     */

    public function getSaldo($consorcio, $liquidation_type) {
        $resul = $this->find('first', ['conditions' => ['SaldosInicialesConsorcio.consorcio_id' => $consorcio, 'SaldosInicialesConsorcio.liquidations_type_id' => $liquidation_type], 'fields' => 'SaldosInicialesConsorcio.saldo', 'order' => 'created desc']);
        if (empty($resul)) {
            return 0;
        } else {
            return $resul['SaldosInicialesConsorcio']['saldo'];
        }
    }

}
