<?php

App::uses('AppModel', 'Model');

class Cartastipo extends AppModel {

    public $displayField = 'nombre';
    public $validate = array(
        'nombre' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'abreviacion' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $hasMany = array(
        'Cartasprecio' => array(
            'className' => 'Cartasprecio',
            'foreignKey' => 'cartastipo_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Carta' => array(
            'className' => 'Carta',
            'foreignKey' => 'cartastipo_id',
            'dependent' => false,
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

    /*
     * Funcion que devuelve el id del tipo de carta (CU, CR, S, ETC)
     */

    public function getCartastipoId($tipo) {
        if (strlen($tipo) == 1) {
            // es una carta simple, el tipo es 'S'
            $tipo = 'S';
        }
        $r = $this->find('first', array('conditions' => array('Cartastipo.abreviacion' => $tipo), 'recursive' => -1, 'fields' => array('Cartastipo.id')));
        return ($r === array() ? 0 : $r['Cartastipo']['id']);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                $this->alias . '.nombre LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.abreviacion LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
