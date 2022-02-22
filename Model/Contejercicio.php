<?php

App::uses('AppModel', 'Model');

class Contejercicio extends AppModel {

    public $displayField = 'nombre';
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
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'inicio' => [
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
            'verificafecha' => array(
                'rule' => array('checkDates'),
                'message' => 'El inicio debe ser menor o igual al fin',
                'on' => 'update',
            ),
        ],
        'fin' => [
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
            'verificafecha' => array(
                'rule' => array('checkDates'),
                'message' => 'El inicio debe ser menor o igual al fin',
                'on' => 'update',
            ),
        ],
    ];
    public $belongsTo = [
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Contejercicio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contejercicio.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function get() {
        return $this->find('list', ['conditions' => ['Contejercicio.client_id' => $_SESSION['Auth']['User']['client_id'], 'bloqueado' => 0]]);
    }

    public function isBloqueado($id) {
        $this->id = $id;
        return $this->field('bloqueado');
    }

    /*
     * Obtiene el Ejercicio en curso (actual o no) para el consorcio $consorcio. Si no existe un ejercicio en curso, devuelve cero
     */

    public function getEjercicioActual($consorcio) {
        $resul = $this->find('first', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id'], 'consorcio_id' => $consorcio, 'bloqueado' => 0]]);
        if (empty($resul)) {
            return 0;
        }
        return $resul['Contejercicio']['id'];
    }

    /*
     * Obtiene todos los ejercicios del consorcio
     */

    public function getEjercicios($consorcio) {
        return $this->find('list', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id'], 'consorcio_id' => $consorcio], 'order' => 'id desc']);
    }

    public function getMeses($ejercicio) {
        $ej = $this->find('first', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id'], 'id' => $ejercicio]]);
        $translate = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', 12 => 'Diciembre'];
        $meses = [];

        $inicio = $ej['Contejercicio']['inicio'];
        $fin = $ej['Contejercicio']['fin'];
        while (strtotime($inicio) <= strtotime($fin)) {
            $meses[$inicio] = $translate[date("m", strtotime($inicio))] . " " . date("Y", strtotime($inicio));
            $inicio = date("Y-m-d", strtotime($inicio . "+1 month"));
        }
        return $meses;
    }

    /*
     * Obtiene los datos del ejercicio seleccionado
     */

    public function getEjercicioInfo($id) {
        $resul = $this->find('first', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id'], 'id' => $id]]);
        if (empty($resul)) {
            return [];
        }
        return $resul['Contejercicio'];
    }

    /*
     * Verifico que el mes/año seleccionados se encuentren entre inicio y fin del ejercicio (inclusive)
     */

    public function esMesValido($ejercicio, $mesaño) {
        $this->id = $ejercicio;
        if (date('Y-m', strtotime($mesaño)) < date('Y-m', strtotime($this->field('inicio'))) || date('Y-m', strtotime($mesaño)) > date('Y-m', strtotime($this->field('fin')))) {
            return false;
        }
        return true;
    }

    /*
     * Verifico que el inicio sea menor o igual al fin
     */

    public function checkDates($check) {
        if (isset($this->data['Contejercicio']['id'])) {
            if (isset($check['inicio'])) {
                $fecha = $check['inicio'];
                $esinicio = true;
            } else {
                $fecha = $check['fin'];
                $esinicio = false;
            }
            $this->id = $this->data['Contejercicio']['id'];
            $fechaAComparar = $this->field(($esinicio ? 'fin' : 'inicio'));
            if ($esinicio) {
                // inicio <= fin
                return (date('Y-m-d', strtotime($fecha)) <= date('Y-m-d', strtotime($fechaAComparar)));
            } else {
                // fin >= inicio
                return (date('Y-m-d', strtotime($fecha)) >= date('Y-m-d', strtotime($fechaAComparar)));
            }
        }
        return true;
    }

    public function beforeSave($options = array()) {
        $this->data['Contejercicio']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        $this->data['Contejercicio']['inicio'] = $this->fecha($this->data['Contejercicio']['inicio']);
        $this->data['Contejercicio']['fin'] = $this->fecha($this->data['Contejercicio']['fin']);

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
                'Contejercicio.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
