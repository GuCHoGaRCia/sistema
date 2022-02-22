<?php

App::uses('AppModel', 'Model');

class Ajustetipoliquidacione extends AppModel {

    public $belongsTo = [
        'Ajuste' => [
            'className' => 'Ajuste',
            'foreignKey' => 'ajuste_id',
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
