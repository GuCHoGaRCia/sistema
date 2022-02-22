<?php

App::uses('AppModel', 'Model');

class Proveedorspagoscheque extends AppModel {

    public $belongsTo = [
        'Proveedorspago' => [
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedorspago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Cheque' => [
            'className' => 'Cheque',
            'foreignKey' => 'cheque_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Proveedorspagoscheque. LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
