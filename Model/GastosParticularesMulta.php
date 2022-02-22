<?php

App::uses('AppModel', 'Model');

class GastosParticularesMulta extends AppModel {

    public $useTable = 'gastos_particulares_multa';
    public $validate = [
        'liquidation_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'gastos_particulare_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
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
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function listarCargadas($propietarios, $liquidacionId, $multacapital = null) {
        $resul = [];
        foreach ($propietarios as $k => $v) {
            $d = $this->find('first', ['conditions' => ['GastosParticularesMulta.propietario_id' => $v['id'], 'GastosParticularesMulta.liquidation_id' => $liquidacionId, 'multasobrecapital' => empty($multacapital) ? 0 : 1]]);
            if (!empty($d)) {
                $resul[] = $v['id'];
            }
        }
        return $resul;
    }

    // Obtiene la multa de la liquidacion de un propietario

    public function getMultaLiquidacion($propietario_id, $liquidacionId) {
        $condiciones = array('GastosParticularesMulta.propietario_id' => $propietario_id, 'GastosParticularesMulta.liquidation_id' => $liquidacionId);
        $options = array('conditions' => $condiciones,
            'fields' => ['Gastos_particulares.amount'],
            'joins' => [['table' => 'gastos_particulares', 'alias' => 'Gastos_particulares', 'type' => 'left', 'conditions' => ['GastosParticularesMulta.gastos_particulare_id=Gastos_particulares.id']]]
        );
        return $this->find('first', $options);
    }

}
