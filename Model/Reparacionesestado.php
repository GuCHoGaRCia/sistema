<?php

App::uses('AppModel', 'Model');

class Reparacionesestado extends AppModel {

    public $displayField = 'nombre';
    public $validate = [
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
    ];
    public $hasMany = [
        'Reparacione' => [
            'className' => 'Reparacione',
            'foreignKey' => 'reparacionesestado_id',
            'dependent' => false,
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
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Reparacionesestado.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacionesestado.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function get() {
        return $this->find('list', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id']]]);
    }

    public function getAll() {
        return Hash::combine($this->find('all', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id']]]), '{n}.Reparacionesestado.id', '{n}.Reparacionesestado');
    }

    public function beforeSave($options = array()) {
        if (!$_SESSION['Auth']['User']['is_admin']) {
            $this->data['Reparacionesestado']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        return true;
    }

}
