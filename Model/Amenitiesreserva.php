<?php

App::uses('AppModel', 'Model');

class Amenitiesreserva extends AppModel {

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
        'fecha' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => 'Debe completar el dato',
            ],
        ],
        'amenitiesturno_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
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
        'cancelado' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
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
        ],
        'Amenitiesturno' => [
            'className' => 'Amenitiesturno',
            'foreignKey' => 'amenitiesturno_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Obtiene la cantidad der reservas de una amenitie, para un propietario y año dado
     */

    public function getCantidadReservasPorPropietario($amenitie_id, $propietario_id, $año) {
        return $this->find('count', ['conditions' => ['Amenity.id' => $amenitie_id, 'Amenity.habilitado' => 1, 'Amenitiesreserva.propietario_id' => $propietario_id, 'year(Amenitiesreserva.fecha)' => $año, 'Amenitiesreserva.cancelado' => 0],
                    'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenity.id=Amenitiesreserva.amenitie_id']]]]);
    }

    /*
     * Obtiene (canceladas y no canceladas) las reservas realizadas en una amenitie para un turno y fecha específico
     */

    public function getReservas($amenitie_id, $amenitiesturno_id, $fecha) {
        return $this->find('all', ['conditions' => ['Amenitiesreserva.amenitie_id' => $amenitie_id, 'Amenitiesreserva.amenitiesturno_id' => $amenitiesturno_id,
                        'Amenity.habilitado' => 1, 'Amenitiesreserva.fecha' => $fecha],
                    'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenity.id=Amenitiesreserva.amenitie_id']]],
                    'order' => 'Amenitiesreserva.created desc']);
    }

    /*
     * obtiene las reservas NO canceladas (ordenadas viejas a nuevas)
     */

    public function getReservasVigentes($amenitie_id, $amenitiesturno_id, $fecha) {
        return $this->find('all', ['conditions' => ['Amenitiesreserva.amenitie_id' => $amenitie_id, 'Amenitiesreserva.amenitiesturno_id' => $amenitiesturno_id,
                        'Amenity.habilitado' => 1, 'Amenitiesreserva.fecha' => $fecha, 'Amenitiesreserva.cancelado' => 0],
                    'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenity.id=Amenitiesreserva.amenitie_id']]],
                    'order' => 'Amenitiesreserva.created']);
    }

    public function getReservaPropietario($amenitie_id, $amenitiesturno_id, $propietario_id, $fecha) {
        return $this->find('first', ['conditions' => ['Amenitiesreserva.amenitie_id' => $amenitie_id, 'Amenitiesreserva.amenitiesturno_id' => $amenitiesturno_id, 'Amenitiesreserva.propietario_id' => $propietario_id, 'Amenitiesreserva.cancelado' => 0,
                        'Amenity.habilitado' => 1, 'Amenitiesreserva.fecha' => $fecha],
                    'joins' => [['table' => 'amenities', 'alias' => 'Amenity', 'type' => 'left', 'conditions' => ['Amenity.id=Amenitiesreserva.amenitie_id']]],
                    'order' => 'created desc']);
    }

    /*
     * Crea una reserva
     */

    public function crear($amenitie_id, $amenitiesturno_id, $propietario_id, $fecha, $limpieza) {
        $this->create();
        if ($this->save(['amenitie_id' => $amenitie_id, 'amenitiesturno_id' => $amenitiesturno_id, 'propietario_id' => $propietario_id, 'fecha' => $this->fecha($fecha), 'seleccionarquienrealizalimpieza' => $limpieza])) {
            return ['e' => 0, 'd' => __('El Turno fue reservado correctamente')];
        } else {
            return ['e' => 1, 'd' => __('El Turno no pudo ser reservado, intente nuevamente')];
        }
    }

    /*
     * Cancela una reserva y guarda la fecha de cancelacion (es la misma de modified, pero x las dudas)
     */

    public function cancelar($amenitiesreserva_id, $multa = 0) {
        $this->id = $amenitiesreserva_id;
        $this->saveField('cancelado', 1);
        $this->saveField('multado', $multa);
        $this->saveField('fechacancelacion', date("Y-m-d H:i:s"));
    }

    /*
     * Multa una reserva
     */

    public function multar($amenitiesreserva_id) {
        $this->save(['id' => $amenitiesreserva_id, 'multa' => 1], ['callbacks' => false]);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Amenitiesreserva.amenitie_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
