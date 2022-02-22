<?php

App::uses('AppModel', 'Model');

class Cartadeudore extends AppModel {

    public $validate = [
        'propietario_id' => [
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
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cartadeudore.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cartadeudore.propietario_id=Propietario.id']],
                                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']]]]));
    }

    public function add($propietario_id, $carta) {
        $this->create();
        $this->save(['propietario_id' => $propietario_id, 'carta' => $carta]);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
