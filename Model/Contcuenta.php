<?php

App::uses('AppModel', 'Model');

class Contcuenta extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(code," - ", titulo)'];
    public $displayField = 'titulo';
    public $useTable = 'contcuentas';
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            ],
        ],
        'conttitulo_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            ],
        ],
        'titulo' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            ],
        ],
        'code' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            ],
        ],
        'debehaber' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
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
        'Conttitulo' => [
            'className' => 'Conttitulo',
            'foreignKey' => 'conttitulo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = array(
        'Contasiento' => array(
            'className' => 'Contasiento',
            'foreignKey' => 'contcuenta_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Contcuenta.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contcuenta.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function get() {
        return $this->find('list', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['id', 'name2'], 'order' => 'code']);
    }

    public function getInfo() {
        return Hash::combine($this->find('all', ['conditions' => ['client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['id', 'name2', 'conttitulo_id'], 'order' => 'code,orden']), '{n}.Contcuenta.id', '{n}.Contcuenta');
    }

    /*
     * Obtiene el Mayor de una cuenta entre dos fechas (sino, por defecto desde el 1º del mes actual hasta el dia actual)
     */

    public function getMayor($id, $consorcio, $d = null, $h = null) {
        if (empty($d)) {
            $d = date("Y-m-01");
        }
        if (empty($h)) {
            $h = date("Y-m-d");
        }
        return $this->find('all', ['conditions' => ['Contcuenta.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contcuenta.id' => $id, 'Contasiento.consorcio_id' => $consorcio, 'Contasiento.fecha >=' => $this->fecha($d), 'Contasiento.fecha <=' => $this->fecha($h)],
                    'joins' => [['table' => 'contasientos', 'alias' => 'Contasiento', 'type' => 'right', 'conditions' => ['Contasiento.contcuenta_id=Contcuenta.id']]],
                    'fields' => ['Contasiento.*'], 'order' => 'Contasiento.numero,Contasiento.fecha']);
    }

    public function beforeSave($options = []) {
        $this->data['Contcuenta']['client_id'] = $_SESSION['Auth']['User']['client_id'];

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
                'Contcuenta.client_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
