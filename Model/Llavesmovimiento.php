<?php

App::uses('AppModel', 'Model');

class Llavesmovimiento extends AppModel {

    public $validate = [
        'llave_id' => [
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
        'llavesestado_id' => [
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
        'proveedor_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'reparacionessupervisore_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Llave' => [
            'className' => 'Llave',
            'foreignKey' => 'llave_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Llavesestado' => [
            'className' => 'Llavesestado',
            'foreignKey' => 'llavesestado_id',
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
        ],
        'Reparacionessupervisore' => [
            'className' => 'Reparacionessupervisore',
            'foreignKey' => 'reparacionessupervisore_id',
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
        ],
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Reparacionesactualizacionesllavesmovimiento' => [
            'className' => 'Reparacionesactualizacionesllavesmovimiento',
            'foreignKey' => 'llavesmovimiento_id',
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

    public function beforeSave($options = []) {
        if (isset($_SESSION['Auth']['User']['id'])) {
            $this->data['Llavesmovimiento']['user_id'] = $_SESSION['Auth']['User']['id'];
        }

        return true;
    }

    /*
     * Verifico q la llave este habilitada, exista el supervisor, proveedor o propietario (y que sean del cliente actual)
     */

    public function beforeGuardar($data, $client_id) {
        $resul = "";
        if (isset($data['r'])) {// verifico si esta lista para recepcionar
            foreach ($data['r'] as $k => $v) {
                $llave = $this->Llave->find('first', array('conditions' => array('Llave.client_id' => $client_id, 'Llave.id' => $k, 'Llave.habilitada' => 0), 'fields' => ['Llave.name2']));
                if (!empty($llave)) {
                    $resul .= "La llave " . $llave['Llave']['name2'] . " es inexistente o se encuentra deshabilitada\n";
                }
            }
            unset($data['r']);
        }
        foreach ($data as $k => $v) {
            $llave = $this->Llave->find('first', array('conditions' => array('Llave.client_id' => $client_id, 'Llave.id' => $k, 'Llave.habilitada' => 0), 'fields' => ['Llave.name2']));
            if (!empty($llave)) {
                $resul .= "La llave " . $llave['Llave']['name2'] . " es inexistente o se encuentra deshabilitada\n";
            }

            $a = explode("#", $v);
            if (count($a) == 2) {
                if ($a[0] == 0) {// proveedor
                    if ($this->Proveedor->find('count', array('conditions' => array('Proveedor.client_id' => $client_id, 'Proveedor.id' => $a[1]))) == 0) {
                        $resul .= "El Proveedor ingresado es inexistente\n";
                    }
                } else if ($a[0] == 1) {//supervisor
                    if ($this->Reparacionessupervisore->find('count', array('conditions' => array('Reparacionessupervisore.client_id' => $client_id, 'Reparacionessupervisore.id' => $a[1], 'Reparacionessupervisore.habilitado' => 1))) == 0) {
                        $resul .= "El Supervisor ingresado es inexistente o se encuentra deshabilitado\n";
                    }
                } else {//propietario
                    if ($this->Propietario->find('count', array('conditions' => array('Consorcio.client_id' => $client_id, 'Propietario.id' => $a[1]), 'recursive' => 0)) == 0) {
                        $resul .= "El Propietario ingresado es inexistente\n";
                    }
                }
            }
        }
        return $resul;
    }

    // $reparacione_id lo uso para cambiar el estado y la reparacion q tiene la llave en la tabla llaves
    // desnormalicé a propósito porq sino era un quilombo 
    public function guardar($actid, $data, $reparacione_id, $client_id = null) {
        $resul = $this->beforeGuardar($data, $client_id);
        if ($resul !== "") {
            return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
        }
        if (isset($data['r'])) {// recepcion de llave
            foreach ($data['r'] as $k => $v) {
                $llave = $this->Llave->find('first', array('conditions' => array('Llave.id' => $k, 'Llave.habilitada' => 1), 'fields' => ['Llave.propietario_id']));
                $info = ['llave_id' => $k, 'fecha' => date("Y-m-d"), 'titulo' => 'Recepción llave', 'llavesestado_id' => 1, 'proveedor_id' => 0, 'reparacionessupervisore_id' => 0, 'propietario_id' => $llave['Llave']['propietario_id']];
                $this->create(); //llavesestado_id=entregada
                $resul = $this->save($info);
                $this->Reparacionesactualizacionesllavesmovimiento->create();
                $this->Reparacionesactualizacionesllavesmovimiento->save(['llavesmovimiento_id' => $resul['Llavesmovimiento']['id'], 'reparacionesactualizacione_id' => $actid]);

                //actualizo la llave
                $this->Llave->id = $k;
                $this->Llave->save(['llavesestado_id' => 1, 'reparacione_id' => 0], ['callbacks' => false]);
            }
            unset($data['r']);
        }
        foreach ($data as $k => $v) {// entrega de llave
            $info = ['llave_id' => $k, 'fecha' => date("Y-m-d"), 'titulo' => 'Entrega llave', 'llavesestado_id' => 2, 'proveedor_id' => 0, 'reparacionessupervisore_id' => 0, 'propietario_id' => 0];
            $a = explode("#", $v);
            if ($a[0] == 0) {// proveedor
                $info['proveedor_id'] = $a[1];
            } else if ($a[0] == 1) {//supervisor
                $info['reparacionessupervisore_id'] = $a[1];
            } else {//propietario
                $info['propietario_id'] = $a[1];
            }
            $this->create(); //llavesestado_id=entregada
            $resul = $this->save($info);
            $this->Reparacionesactualizacionesllavesmovimiento->create();
            $this->Reparacionesactualizacionesllavesmovimiento->save(['llavesmovimiento_id' => $resul['Llavesmovimiento']['id'], 'reparacionesactualizacione_id' => $actid]);

            //actualizo la llave (entregada)
            $this->Llave->id = $k;
            $this->Llave->save(['llavesestado_id' => 2, 'reparacione_id' => $reparacione_id], ['callbacks' => false]);
        }
    }

    public function mover($data) {
        if (isset($data['Llavesmovimiento']['e'])) {
            $resul = $this->beforeGuardar($data['Llavesmovimiento']['e'], $_SESSION['Auth']['User']['client_id']);
            if ($resul !== "") {
                return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
            }
            foreach ($data['Llavesmovimiento']['e'] as $k => $v) {// entrega de llave
                $info = ['llave_id' => $k, 'fecha' => date("Y-m-d"), 'titulo' => $data['Llavesmovimiento']['titulo'], 'llavesestado_id' => 2, 'proveedor_id' => 0, 'reparacionessupervisore_id' => 0, 'propietario_id' => 0];
                $a = explode("#", $v);
                if ($a[0] == 0) {// proveedor
                    $info['proveedor_id'] = $a[1];
                } else if ($a[0] == 1) {//supervisor
                    $info['reparacionessupervisore_id'] = $a[1];
                } else {//propietario
                    $info['propietario_id'] = $a[1];
                }
                $this->create(); //llavesestado_id=entregada
                $this->save($info);

                //actualizo la llave (entregada)
                $this->Llave->id = $k;
                $this->Llave->save(['llavesestado_id' => 2], ['callbacks' => false]);
            }
        }
        if (isset($data['Llavesmovimiento']['r'])) {// recepcion de llave
            foreach ($data['Llavesmovimiento']['r'] as $k => $v) {
                $llave = $this->Llave->find('first', array('conditions' => array('Llave.id' => $data['Llavesmovimiento']['llave_id'], 'Llave.habilitada' => 1), 'fields' => ['Llave.propietario_id']));
                $info = ['llave_id' => $k, 'fecha' => date("Y-m-d"), 'titulo' => $data['Llavesmovimiento']['titulo'], 'llavesestado_id' => 1, 'proveedor_id' => 0, 'reparacionessupervisore_id' => 0, 'propietario_id' => $llave['Llave']['propietario_id']];
                $this->create(); //llavesestado_id=entregada
                $this->save($info);
                //actualizo la llave
                $this->Llave->id = $k;
                $this->Llave->save(['llavesestado_id' => 1], ['callbacks' => false]);
            }
        }
        if (!isset($data['Llavesmovimiento']['e']) && !isset($data['Llavesmovimiento']['r'])) {// es solo cambio de estado (rotura, robada, etc)
            $info = ['llave_id' => $data['Llavesmovimiento']['llave_id'], 'fecha' => $this->fecha($data['Llavesmovimiento']['fecha']), 'titulo' => $data['Llavesmovimiento']['titulo'], 'llavesestado_id' => $data['Llavesmovimiento']['llavesestado_id'], 'proveedor_id' => 0, 'reparacionessupervisore_id' => 0, 'propietario_id' => 0];
            $this->create(); //llavesestado_id=entregada
            $this->save($info);
            $this->Llave->id = $data['Llavesmovimiento']['llave_id'];
            $this->Llave->save(['llavesestado_id' => $data['Llavesmovimiento']['llavesestado_id']], ['callbacks' => false]);
        }
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
                'Llavesmovimiento.llave_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
