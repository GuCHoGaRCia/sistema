<?php

App::uses('AppModel', 'Model');

class Cartasprecio extends AppModel {

    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cartastipo_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Ya existe ese tipo de carta para el cliente actual',
                'on' => 'create',
            ),
        ),
        'importe' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Debe ser un importe mayor o igual a cero',
            ),
        ),
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Cartastipo' => array(
            'className' => 'Cartastipo',
            'foreignKey' => 'cartastipo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /*
     * Obtiene el Precio de un tipo de carta específico de un cliente (para pasar Gastos particulares automaticamente al cargar la carta)
     */

    public function getPrecio($client_id, $cartastipo_id) {
        $resul = $this->find('first', ['conditions' => ['client_id' => $client_id, 'cartastipo_id' => $cartastipo_id], 'fields' => ['Cartasprecio.importe']]);
        if (!empty($resul)) {
            return $resul['Cartasprecio']['importe'];
        }
        return 0;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Cartastipo.nombre LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
                'Cartasprecio.importe' => $data['buscar'],
        ));
    }

    public function checkUnique($check) {
        $resul = $this->find('count', array(
            'conditions' => array('Cartasprecio.cartastipo_id' => $check['cartastipo_id'], 'Cartasprecio.client_id' => $this->data['Cartasprecio']['client_id']),
            'recursive' => -1
        ));
        return ($resul == 0);
    }

}
