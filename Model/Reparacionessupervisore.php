<?php

App::uses('AppModel', 'Model');

class Reparacionessupervisore extends AppModel {

    public $displayField = 'nombre';
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'direccion' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'allowEmpty' => true,
            ],
        ],
        'telefono' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'allowEmpty' => true,
            ],
        ],
        'email' => array(
            'maildir' => array(
                'rule' => ['checkEmails'],
                'message' => 'El formato del email es incorrecto. Ej: juan@gmail.com. Si desea agregar mas de un email, separelos con coma y sin espacios. Ej: juan@gmail.com,pepe@hotmail.com',
                'allowEmpty' => true,
            ),
        ),
        'habilitado' => [
            'boolean' => [
                'rule' => ['boolean'],
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
    public $hasMany = [
        'Reparacionesactualizacionessupervisore' => [
            'className' => 'Reparacionesactualizacionessupervisore',
            'foreignKey' => 'reparacionessupervisore_id',
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

    /*
     * Obtiene TODOS los supervisores del Cliente
     */

    public function get($client_id = null) {
        return $this->find('list', ['conditions' => ['Reparacionessupervisore.client_id' => empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id], 'order' => 'Reparacionessupervisore.nombre']);
    }

    /*
     * Obtengo los clientes asociados al Supervisor (puede tener mas de uno, como los Proveedores)
     */

    public function getClientId($email) {
        return $this->find('list', ['conditions' => ['Reparacionessupervisore.email like' => '%' . $email . '%'], 'fields' => ['Reparacionessupervisore.id', 'Reparacionessupervisore.client_id']]);
    }

    /*
     * Obtiene los supervisores HABILTIADOS del Cliente
     */

    public function getList($client_id = null) {
        return $this->find('list', ['conditions' => ['Reparacionessupervisore.client_id' => empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id, 'Reparacionessupervisore.habilitado' => 1], 'order' => 'Reparacionessupervisore.nombre']);
    }

    public function beforeSave($options = []) {
        if (isset($_SESSION['Auth']['User']['client_id'])) {
            $this->data['Reparacionessupervisore']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }
        return true;
    }

    /*
     * Si el supervisor tiene reparaciones asignadas pendientes, no se puede deshabilitar/eliminar
     */

    public function tieneReparacionesPendientes($id) {
        $resul = $this->Reparacionesactualizacionessupervisore->find("count", array("conditions" => array("reparacionessupervisore_id" => $id, 'finalizado' => 0)));
        return (bool) ($resul != 0);
    }

    public function deshabilitar($id) {
        if ($this->tieneReparacionesPendientes($id)) {
            return 'El Supervisor posee Reparaciones pendientes, no se puede deshabilitar';
        } else {
            $this->id = $id;
            $this->saveField('habilitado', 0);
            return 1;
        }
    }

    public function getEmail($id) {
        $this->id = $id;
        return $this->field('email');
    }

    public function getSupervisorId($email) {
        return $this->find('first', ['conditions' => ['email like' => '%' . $email . '%']]);
    }

    /*
     * Solo pueden borrarse los Supervisores sin reparaciones asignadas
     */

    /* public function beforeDelete($cascade = true) {
      if (0 == $this->Reparacionesactualizacionessupervisore->find("count", array("conditions" => array("reparacionessupervisore_id" => $this->id)))) {
      return true;
      } else {
      return false;
      }
      } */

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Reparacionessupervisore.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
