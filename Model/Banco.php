<?php

App::uses('AppModel', 'Model');

class Banco extends AppModel {

    //public $actsAs = ['AuditLog.Auditable'];

    public $virtualFields = ['name2' => 'CONCAT(Client.name, " - ", Banco.name)'];
    public $displayField = 'name';
    public $validate = array(
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'address' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'city' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $hasMany = array(
        'Bancoscuenta' => array(
            'className' => 'Bancoscuenta',
            'foreignKey' => 'banco_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Informepago' => array(
            'className' => 'Informepago',
            'foreignKey' => 'banco_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
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
        return !empty($this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Banco.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    /*
     * Si no es admin le establezco el client_id por el del cliente
     * Si es admin elije el cliente, por eso no se necesita
     */

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['Banco']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        return true;
    }

    /*
     * Verifico q no cuentas bancarias asociadas al banco
     */

    public function beforeDelete($cascade = true) {
        $count = $this->Bancoscuenta->find('count', array(
            'conditions' => array('banco_id' => $this->id)
        ));
        if ($count == 0) {
            return true;
        }
        return false;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                $this->alias . '.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
