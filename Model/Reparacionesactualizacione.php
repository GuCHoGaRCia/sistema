<?php

App::uses('AppModel', 'Model');

class Reparacionesactualizacione extends AppModel {

    public $validate = [
        'reparacione_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'user_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'reparacionesestado_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'fecha' => [
            'date' => [
                'rule' => ['date'],
                'message' => 'Debe completar con una fecha correcta',
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
        'descripcion' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Reparacione' => [
            'className' => 'Reparacione',
            'foreignKey' => 'reparacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Reparacionesestado' => [
            'className' => 'Reparacionesestado',
            'foreignKey' => 'reparacionesestado_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Reparacionesactualizacionesadjunto' => [
            'className' => 'Reparacionesactualizacionesadjunto',
            'foreignKey' => 'reparacionesactualizacione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Reparacionesactualizacionesllavesmovimiento' => [
            'className' => 'Reparacionesactualizacionesllavesmovimiento',
            'foreignKey' => 'reparacionesactualizacione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Reparacionesactualizacionesproveedore' => [
            'className' => 'Reparacionesactualizacionesproveedore',
            'foreignKey' => 'reparacionesactualizacione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Reparacionesactualizacionessupervisore' => [
            'className' => 'Reparacionesactualizacionessupervisore',
            'foreignKey' => 'reparacionesactualizacione_id',
            'dependent' => true,
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

    public function guardarActualizacion($data, $id = null, $estaeditando = false) {
        $resul = $this->Reparacione->beforeGuardar($data);
        if ($resul !== "") {
            return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
        }
        $info = isset($data->data['Reparacionesactualizacione']) ? $data->data['Reparacionesactualizacione'] : $data->data['Reparacione'];
        $reparacione_id = isset($info['reparacione_id']) ? $info['reparacione_id'] : $id;
        $fecha = $this->fecha($info['fecha']);
        $idact = []; // es el id de reparacionesactualizacione que esta editando
        $info['observaciones'] = $data->data['obs'];
        if ($estaeditando) {
            $idact = ['id' => $data->data['Reparacionesactualizacione']['reparacioneactualizacione_id']];
        } else {
            $this->create();
        }
        $userid = isset($_SESSION['Auth']['User']['id']) ? $_SESSION['Auth']['User']['id'] : $this->Reparacione->Consorcio->Client->Aviso->_decryptURL($data->data['Reparacionesactualizacione']['supervisor']); // si no esta logueado, es un supervisor (le asigno su id)
        $client_id = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $this->Reparacione->Consorcio->Client->Aviso->_decryptURL($data->data['Reparacionesactualizacione']['c']); // si no esta logueado, es un supervisor (le asigno su clientid)
        $resul = $this->save(['reparacione_id' => $reparacione_id, 'user_id' => $userid, 'reparacionesestado_id' => $info['reparacionesestado_id'],
            'fecha' => $fecha, 'concepto' => $info['concepto'], 'observaciones' => $info['observaciones']] + $idact);
        $actid = ($estaeditando ? $data->data['Reparacionesactualizacione']['reparacioneactualizacione_id'] : $resul['Reparacionesactualizacione']['id']);
        // actualizo el estado general de la reparacion (listado de reparaciones) con el ultimo estado asignado
        $this->Reparacione->id = $reparacione_id;
        $this->Reparacione->saveField('reparacionesestado_id', $info['reparacionesestado_id']);
        $this->Reparacione->saveField('concepto', $info['concepto']);
        $this->Reparacione->saveField('fecha', $fecha);

        if (isset($data->data['Adjunto'])) {
            $this->Reparacionesactualizacionesadjunto->guardarAdjunto($actid, $data, $client_id);
        }

        if (isset($info['reparacionessupervisore_id'])) {
            // actualizo los supervisores
            $this->Reparacionesactualizacionessupervisore->modificar($actid, $info['reparacionessupervisore_id'], isset($info['reparacionessupervisorefinalizar_id']) ? $info['reparacionessupervisorefinalizar_id'] : null, $client_id);
        }

        if (isset($info['proveedor_id'])) {
            // actualizo los proveedores
            $this->Reparacionesactualizacionesproveedore->modificar($actid, $info['proveedor_id'], isset($info['proveedorfinalizar_id']) ? $info['proveedorfinalizar_id'] : null);
        }

        // realizo movimientos de entrega/recepcion de llaves
        if (isset($data->data['Reparacionesllavesmovimiento']) && !empty($data->data['Reparacionesllavesmovimiento'])) {
            $this->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->guardar($actid, $data->data['Reparacionesllavesmovimiento'], $reparacione_id, $client_id);
        }
        return true;
    }

    public function beforeSave($options = []) {
        if (isset($this->data['Reparacionesactualizacione']['observaciones'])) {
            $this->data['Reparacionesactualizacione']['observaciones'] = $this->cleanHTML($this->data['Reparacionesactualizacione']['observaciones']);
        }
        return true;
    }

}
