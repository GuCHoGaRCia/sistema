<?php

App::uses('AppModel', 'Model');

class Conttitulo extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(code," - ", titulo)'];
    public $displayField = 'titulo';
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'conttitulo_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
                'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'code' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'titulo' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'orden' => [
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
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Conttitulo2' => [
            'className' => 'Conttitulo',
            'foreignKey' => 'conttitulo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Contcuenta' => [
            'className' => 'Contcuenta',
            'foreignKey' => 'conttitulo_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Conttitulo' => [
            'className' => 'Conttitulo',
            'foreignKey' => 'conttitulo_id',
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

    public function get() {
        return $this->find('list', ['conditions' => ['Conttitulo.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['id', 'name2'], 'order' => 'code,orden']);
    }

    /*
     * Para mostrar los Titulos indentados, obtiene la cantidad de espacios a izquierda a poner en el titulo de cada uno (para que se muestren como un arbol)
     */

    public function getArbol() {
        $titulos = Hash::combine($this->find('all', ['conditions' => ['Conttitulo.client_id' => $_SESSION['Auth']['User']['client_id']]]), '{n}.Conttitulo.id', '{n}.Conttitulo');
        $arbol = [];
        $cant = 5;
        foreach ($titulos as $k => $v) {
            if ($v['conttitulo_id'] == 0) {
                $arbol[$k] = $cant; // es padre general (activo, pasivo, patrimonio neto)
            } else {
                $arbol[$k] = $cant;
                $j = $v['conttitulo_id'];
                while ($j != 0) {//sumo el valor de todos los padres
                    $arbol[$k] += $cant;
                    $j = $titulos[$j]['conttitulo_id'];
                }
            }
        }
        return $arbol;
    }

    /*
     * Obtengo las Hojas del arbol para agregar las cuentas en ellas
     */

    public function getHojas() {
        $titulos = Hash::combine($this->find('all', ['conditions' => ['Conttitulo.client_id' => $_SESSION['Auth']['User']['client_id']]]), '{n}.Conttitulo.id', '{n}.Conttitulo');
        $hojas = $this->get(); //todos los titulos
        foreach ($titulos as $k => $v) {
            if ($v['conttitulo_id'] != 0 && isset($hojas[$v['conttitulo_id']])) {
                unset($hojas[$v['conttitulo_id']]); //si tiene padre, borro el padre. Al borrar todos los padres, me quedan solo las hojas
            }
        }
        return $hojas;
    }

    public function beforeSave($options = array()) {
        $this->data['Conttitulo']['client_id'] = $_SESSION['Auth']['User']['client_id'];

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
                'Conttitulo.client_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
