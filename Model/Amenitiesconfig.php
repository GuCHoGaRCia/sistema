<?php

App::uses('AppModel', 'Model');

class Amenitiesconfig extends AppModel {

    public $validate = [
        'amenitie_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'maxreservasporpropietario' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe ingresar un numero mayor o igual a cero. Cero indica sin limite',
            ],
            'range' => [
                'rule' => ['range', -1, 750],
                'message' => 'Debe ingresar un numero mayor o igual a cero. Cero indica sin limite',
            ],
        ],
        'diashabilitadosreserva' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe ingresar un numero mayor o igual a cero. Cero indica sin limite',
            ],
            'range' => [
                'rule' => ['range', -1, 750],
                'message' => 'Debe ingresar un numero mayor o igual a cero. Cero indica sin limite',
            ],
        ],
        'reservacondicional' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'propietariorealizalimpieza' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    public $belongsTo = [
        'Amenitie' => [
            'className' => 'Amenitie',
            'foreignKey' => 'amenitie_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Amenity.client_id' => $_SESSION['Auth']['User']['client_id'], 'Amenitiesconfig.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenity.id=Amenitiesconfig.amenitie_id']]]]));
    }

    /*
     * Obtiene la configuracion de una Amenitie
     */

    public function get($id) {
        return $this->find('first', ['conditions' => ['Amenitiesconfig.id' => $id]]);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Amenitiesconfig.amenitie_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
