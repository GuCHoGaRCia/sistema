<?php

App::uses('AppModel', 'Model');

class Informepagosadjunto extends AppModel {

    public $validate = [
        'informepago_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'ruta' => [
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
        'Informepago' => [
            'className' => 'Informepago',
            'foreignKey' => 'informepago_id',
            'conditions' => '',
            'fields' => '',
            'order' => 'created desc'
        ]
    ];

    public function beforeDelete($cascade = true) {
        //borro el adjunto
        if (file_exists(APP . DS . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'consultas' . DS . $this->field('ruta'))) {
            $file = new File(APP . DS . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'consultas' . DS . $this->field('ruta'));
            $file->delete();
        }
        return true;
    }

}
