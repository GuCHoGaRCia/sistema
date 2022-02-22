<?php

App::uses('AppModel', 'Model');

class Cobranzacheque extends AppModel {

    public $validate = [
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
        'cheque_id' => [
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
        'Cobranza' => [
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Cheque' => [
            'className' => 'Cheque',
            'foreignKey' => 'cheque_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Obtiene los cheques que hayan sido utilizados en la cobranza especificada
     */

    public function getMovimientosCobranza($cobranza_id) {
        return $this->find('all', ['conditions' => ['Cobranzacheque.cobranza_id' => $cobranza_id], 'recursive' => 0, 'fields' => ['Cheque.*', 'Cobranzacheque.*']]);
    }

    /*
     * Obtiene los movimientos que hayan sido realizados con el cheque especificado
     */

    public function getMovimientosCheque($cheque_id) {
        return $this->find('all', ['conditions' => ['Cobranzacheque.cheque_id' => $cheque_id, 'Cobranza.anulada' => 0], 'recursive' => 0/*, 'fields' => ['Cheque.*', 'Cobranzacheque.*', 'Cobranza.fecha2']*/]);
    }

}
