<?php

App::uses('AppModel', 'Model');

class Reparacionesactualizacionesproveedore extends AppModel {

    public $validate = [
        'reparacionesactualizacione_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'proveedor_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'finalizado' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Reparacionesactualizacione' => [
            'className' => 'Reparacionesactualizacione',
            'foreignKey' => 'reparacionesactualizacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Proveedor' => [
            'className' => 'Proveedor',
            'foreignKey' => 'proveedor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * modifica los proveedores
     */

    function modificar($reparacionesactualizacione_id, $asignar, $finalizar) {
        if (!empty($asignar)) {// si no elige nada viene ''
            foreach ($asignar as $v) {
                $this->create();
                $this->save(['reparacionesactualizacione_id' => $reparacionesactualizacione_id, 'proveedor_id' => $v, 'finalizado' => 0]);
            }
        }
        if (!empty($finalizar)) {
            foreach ($finalizar as $v) {
                // si esta editando, y finaliza un proveedor asignado en la mimsa actualizacion, deberia borrarlo, para que no quede en la misma actualizacion "asignado" y "finalizado"
                //$resul = $this->find('list', ['conditions' => ['reparacionesactualizacione_id' => $reparacionesactualizacione_id, 'proveedor_id', $v], 'fields' => ['id', 'id']]);
                //if (empty($resul)) {
                $this->create();
                $this->save(['reparacionesactualizacione_id' => $reparacionesactualizacione_id, 'proveedor_id' => $v, 'finalizado' => 1]);
                //} else {
                //    $this->deleteAll(['id' => array_values($resul)], false);
                //}
            }
        }
    }

}
