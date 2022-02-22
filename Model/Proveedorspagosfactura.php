<?php
App::uses('AppModel', 'Model');

class Proveedorspagosfactura extends AppModel {


	public $belongsTo = [
		'Proveedorspago' => [
			'className' => 'Proveedorspago',
			'foreignKey' => 'proveedorspago_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Proveedorsfactura' => [
			'className' => 'Proveedorsfactura',
			'foreignKey' => 'proveedorsfactura_id',
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
                'Proveedorspagosfactura. LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }}
