<?php

App::uses('AppModel', 'Model');

class Consultaspropietario extends AppModel {

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
        'mensaje' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'es_respuesta' => [
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
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
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
     * Obtiene las consultas del propietario actual (puede tener consultas en varios clientes)
     */

    public function getConsultasPropietario($p, $cl = null) {
        $options = array('conditions' => array('Consultaspropietario.client_id' => empty($cl) ? $_SESSION['Auth']['User']['client_id'] : $cl, 'Consultaspropietario.propietario_id' => $p),
            'fields' => array('Consultaspropietario.mensaje as m', 'Consultaspropietario.es_respuesta as r', "DATE_FORMAT(Consultaspropietario.created,'%d/%m/%Y %T') as f"), 'order' => 'created desc');
        return $this->find('all', $options);
    }

    public function setConsultasPropietario($c, $cl = null, $p = null, $l = 0) {// desde el panel del administrador, seteo $l para saber q es respuesta
        $this->create();
        $this->save(['client_id' => $cl, 'propietario_id' => $p, 'mensaje' => filter_var($c, FILTER_SANITIZE_STRING), 'es_respuesta' => $l, 'seen' => $l]);
        return $this->find('all', array('conditions' => array('Consultaspropietario.client_id' => $cl, 'Consultaspropietario.propietario_id' => $p), 'fields' => array('Consultaspropietario.mensaje as m', 'Consultaspropietario.es_respuesta as r', "DATE_FORMAT(Consultaspropietario.created,'%d/%m/%Y %T') as f"), 'order' => 'created desc'));
    }

    public function getUnseen() {
        $n = $this->query('select seen from consultaspropietarios where client_id=' . $_SESSION['Auth']['User']['client_id'] . ' group by propietario_id order by id desc limit 1');
        return $n;
    }

    public function setUnseen($id) {
        $num = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        if (!is_int($num) || !isset($_SESSION['Auth']['User']['client_id'])) {
            return false;
        }
        $n = $this->query('update consultaspropietarios set seen=1 where propietario_id=' . $num . ' and client_id=' . $_SESSION['Auth']['User']['client_id'] . ' order by id desc');
        return $n;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        $cad = explode(" ", $data['buscar']);
        $res = "";
        foreach ($cad as $v) {
            $res .= $v . "|";
        }
        return ['OR' => ['Propietario.name REGEXP' => substr($res, 0, -1), 'Consultaspropietario.mensaje LIKE' => '%' . $data['buscar'] . '%']];
    }

}
