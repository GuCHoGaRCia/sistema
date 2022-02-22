<?php

App::uses('AppModel', 'Model');

class Plataformasdepagosconfig extends AppModel {

    public $validate = [
        'plataformasdepago_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'datointerno' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'minimo' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Debe ser un importe mayor o igual a cero',
            ),
        ),
        'comision' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Debe ser un importe mayor o igual a cero',
            ),
        ),
        'codigo' => [
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
        'Plataformasdepago' => [
            'className' => 'Plataformasdepago',
            'foreignKey' => 'plataformasdepago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Plataformasdepagosconfigsdetalle' => [
            'className' => 'Plataformasdepagosconfigsdetalle',
            'foreignKey' => 'plataformasdepagosconfig_id',
            'dependent' => true,
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

    public function save($data = null, $validate = true, $fieldList = []) {
        $resul = parent::save($data);
        if (!empty($resul) && isset($data['Plataformasdepagosconfig']['id'])) {
            $this->Plataformasdepagosconfigsdetalle->deleteAll(['Plataformasdepagosconfigsdetalle.plataformasdepagosconfig_id' => $data['Plataformasdepagosconfig']['id']], false); //borro siempre, asi solo queda el detalle en las q usan roela
            if ($data['Plataformasdepagosconfig']['plataformasdepago_id'] == 3) {//es roela, guardo el detalle x consorcio
                foreach ($data['Plataformasdepagosconfigsdetalle'] as $k => $v) {
                    $this->Plataformasdepagosconfigsdetalle->create();
                    $this->Plataformasdepagosconfigsdetalle->save(['plataformasdepagosconfig_id' => $data['Plataformasdepagosconfig']['id'], 'consorcio_id' => $k, 'valor' => $v]);
                }
            }
            return true;
        }
        return false;
    }

    /*
     * Obtiene las configuraciones de plataformas de todos los clientes
     */

    public function getList() {
        return Hash::combine($this->find('all'), '{n}.Plataformasdepagosconfig.client_id', '{n}.Plataformasdepagosconfig');
    }

    /*
     * Obtiene la Configuracion del Plataformas de un cliente particular.
     */

    public function getConfig($cliente = null) {
        $resul = $this->find('first', ['conditions' => ['client_id' => $cliente], 'contain' => ['Plataformasdepagosconfigsdetalle']]);
        return (!empty($resul) ? $resul : []);
    }

}
