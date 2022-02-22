<?php

App::uses('AppModel', 'Model');

class Avisosblacklist extends AppModel {

    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'email' => [
            'email' => [
                'rule' => ['email'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'cantidad' => [
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
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Verifica si un mail se encuentra en la lista negra. 
     * Los emails q se encuentran en la lista negra son los reportados por Sendgrid q fueron catalogados como spam, incorrectos o rebotaron por algun motivo
     */

    public function isBlacklisted($email) {
        $resul = $this->find('first', ['conditions' => ['Avisosblacklist.email' => $email]]);
        if (empty($resul)) {
            return false;
        }
        // como esta en la lista negra, puede que no tenga asignado client_id, se lo asigno
        $this->id = $resul['Avisosblacklist']['id'];
        $this->saveField('client_id', $_SESSION['Auth']['User']['client_id']);

        return true;
    }

    // funcion de busqueda
    public function filterName($data, $field = null) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Avisosblacklist.email like' => '%' . $data['buscar'] . '%',
            ]
        ];
    }

}
