<?php
App::uses('AppModel', 'Model');

class GastosDistribucionesDetalle extends AppModel {

	public $validate = [
	'gastos_distribucione_id' => [
	'numeric' => [
	'rule' => ['numeric'],
		//'message' => 'Your custom message here',
		//'allowEmpty' => false,
		//'required' => false,
		//'last' => false, // Stop validation after this rule
		//'on' => 'create', // Limit validation to 'create' or 'update' operations
	],
	],
	'coeficiente_id' => [
	'numeric' => [
	'rule' => ['numeric'],
		//'message' => 'Your custom message here',
		//'allowEmpty' => false,
		//'required' => false,
		//'last' => false, // Stop validation after this rule
		//'on' => 'create', // Limit validation to 'create' or 'update' operations
	],
	],
	'porcentaje' => [
	'decimal' => [
	'rule' => ['decimal'],
		//'message' => 'Your custom message here',
		//'allowEmpty' => false,
		//'required' => false,
		//'last' => false, // Stop validation after this rule
		//'on' => 'create', // Limit validation to 'create' or 'update' operations
	],
	],
];

	public $belongsTo = [
		'GastosDistribucione' => [
			'className' => 'GastosDistribucione',
			'foreignKey' => 'gastos_distribucione_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Coeficiente' => [
			'className' => 'Coeficiente',
			'foreignKey' => 'coeficiente_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		]
	];
}
