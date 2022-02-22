<?php

App::uses('AppModel', 'Model');

class Amenitiesturno extends AppModel {

    public $validate = [
        'amenitie_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'diasemana' => [
            'boolean' => [
                'rule' => ['range', 0, 8],
                'message' => 'El dia de la semana es incorrecto',
            ],
        ],
        'inicio' => [
            'time' => [
                'rule' => ['time'],
                'message' => 'El formato debe ser hh:mm (ej: 13:55)',
            ],
            'checkHoras' => [
                'rule' => ['checkHoras'],
                'message' => 'La hora de Inicio debe ser menor a la de Fin',
            ],
        ],
        'fin' => [
            'time' => [
                'rule' => ['time'],
                'message' => 'El formato debe ser hh:mm (ej: 13:55)',
            ],
            'checkHoras' => [
                'rule' => ['checkHoras'],
                'message' => 'La hora de Inicio debe ser menor a la de Fin',
            ],
        ],
        'habilitado' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    public $belongsTo = [
        'Amenity' => [
            'className' => 'Amenity',
            'foreignKey' => 'amenitie_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Amenitiesreserva' => [
            'className' => 'Amenitiesreserva',
            'foreignKey' => 'amenitiesturno_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Amenity.client_id' => $_SESSION['Auth']['User']['client_id'], 'Amenitiesturno.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenitiesturno.amenitie_id=Amenity.id']]]]));
    }

    public function get($id) {
        $resul = $this->find('first', ['conditions' => ['Amenitiesturno.id' => $id]]);
        if (!empty($resul)) {
            return $resul['Amenitiesturno'];
        } else {
            return [];
        }
    }

    /*
     * Verifica q el turno seleccionado sea de la amenitie (Panel propietario, Amenity::reserva(), al reservar o cancelar
     */

    public function elTurnoEsDeLaAmenity($turno, $amenitie) {
        return !empty($this->find('first', ['conditions' => ['Amenity.id' => $amenitie, 'Amenitiesturno.id' => $turno], 'fields' => ['Amenity.id'],
                            'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenitiesturno.amenitie_id=Amenity.id']]]]));
    }

    public function guardar($data) {
        $this->create();
        if (!$this->save($data['Amenitiesturno'])) {
            return "El dato no pudo ser guardado";
        }
        return "";
    }

    public function beforeDelete($cascade = true) {
        $hayreservas = $this->find('first', ['conditions' => ['Amenity.client_id' => $_SESSION['Auth']['User']['client_id'], 'Amenitiesturno.id' => $this->id], 'fields' => [$this->alias . '.id'],
            'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'right', 'conditions' => ['Amenitiesturno.amenitie_id=Amenity.id']],
                ['table' => 'amenitiesreservas', 'alias' => 'Amenitiesreserva', 'type' => 'right', 'conditions' => ['Amenitiesreserva.amenitiesturno_id=Amenitiesturno.id']]]]);
        return empty($hayreservas);
    }

    public function checkHoras($data) {
        if (isset($this->data['Amenitiesturno']['id'])) {// esta editando
            $this->id = $this->data['Amenitiesturno']['id'];
            $inicio = isset($this->data['Amenitiesturno']['inicio']) ? $this->data['Amenitiesturno']['inicio'] : $this->field('inicio');
            $fin = isset($this->data['Amenitiesturno']['fin']) ? $this->data['Amenitiesturno']['fin'] : $this->field('fin');
        } else {// esta creando nuevo
            $inicio = $this->data['Amenitiesturno']['inicio'];
            $fin = $this->data['Amenitiesturno']['fin'];
        }
        if (!preg_match("/(2[0-4]|[01][1-9]|10|00):([0-5][0-9])/", $inicio) || !preg_match("/(2[0-4]|[01][1-9]|10|00):([0-5][0-9])/", $fin)) {
            die(json_encode(['e' => 1, 'd' => 'El formato debe ser hh:mm (ej: 13:55)']));
        }
        if (strtotime($inicio) > strtotime($fin)) {
            //die(json_encode(['e' => 1, 'd' => 'La hora de Inicio debe ser menor a la de Fin']));
        }
        return true;
    }

}
