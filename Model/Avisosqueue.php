<?php

App::uses('AppModel', 'Model');

class Avisosqueue extends AppModel {

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
        'emailfrom' => [
            'email' => [
                'rule' => ['email'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'razonsocial' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'asunto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'altbody' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'codigohtml' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'mailto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
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
     * Agrega un mail a la cola de envio (no me importa si ya está repetido, solo chequeo si no está en la lista negra)
     */

    public function addQueue($datoscliente, $asunto, $html, $text, $email, $from = 'no-responder-este-mail@ceonline.com.ar') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (!$this->Client->Avisosblacklist->isBlacklisted($email)) {
            $this->create();
            $this->save(['client_id' => empty($datoscliente) ? $_SESSION['Auth']['User']['client_id'] : $datoscliente['Client']['id'],
                'emailfrom' => explode(',', $from)[0], 'razonsocial' => !empty($datoscliente) ? $datoscliente['Client']['name'] : 'CEONLINE',
                'asunto' => $asunto, 'altbody' => $text, 'codigohtml' => $html, 'mailto' => strtolower($email)], false);
            return true;
        }
        return false;
    }

}
