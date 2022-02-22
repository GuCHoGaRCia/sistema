<?php

App::uses('AppModel', 'Model');

class GastosParticularesComision extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'gastos_particulares_comision';
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

}
