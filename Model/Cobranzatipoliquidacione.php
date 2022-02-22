<?php

App::uses('AppModel', 'Model');

class Cobranzatipoliquidacione extends AppModel {

    public $belongsTo = [
        'Cobranza' => [
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'LiquidationsType' => [
            'className' => 'LiquidationsType',
            'foreignKey' => 'liquidations_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

}
