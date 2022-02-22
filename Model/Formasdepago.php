<?php

App::uses('AppModel', 'Model');

class Formasdepago extends AppModel {

    public $displayField = 'forma';
    public $validate = [
        'forma' => [
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
    public $hasMany = [
        'Informepago' => [
            'className' => 'Informepago',
            'foreignKey' => 'formasdepago_id',
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
        return !empty($this->find('first', ['conditions' => ['Formasdepago.client_id' => $_SESSION['Auth']['User']['client_id'], 'Formasdepago.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function get($all = false, $client = null) {
        return Hash::combine($this->find('all', ['conditions' => ['Formasdepago.client_id' => empty($client) ? $_SESSION['Auth']['User']['client_id'] ?? 0 : $client] + ($all ? [] : ['Formasdepago.habilitada' => 1]),
                            'fields' => ['id', 'forma', 'destino'], 'order' => 'orden,destino,forma']), '{n}.Formasdepago.id', '{n}.Formasdepago');
    }

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['Formasdepago']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        return true;
    }

    public function guardar($data) {
        $clientes = $this->Client->find('list');
        foreach ($clientes as $k => $v) {
            $data['Formasdepago']['client_id'] = $k;
            $this->create();
            $this->save($data);
        }
        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Formasdepago.forma LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
