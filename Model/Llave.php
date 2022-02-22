<?php

App::uses('AppModel', 'Model');

class Llave extends AppModel {

    public $displayField = 'descripcion';
    public $virtualFields = ['name2' => 'concat("#",numero," - ",descripcion)'];
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'user_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'llavesestado_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'numero' => [
            'numeric' => [
                'rule' => ['numeric'],
                'allowEmpty' => true,
                'required' => false,
            ],
        ],
        'fecha' => [
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
                'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'descripcion' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'observaciones' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'habilitada' => [
            'boolean' => [
                'rule' => ['boolean'],
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
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
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
        ],
        'Llavesestado' => [
            'className' => 'Llavesestado',
            'foreignKey' => 'llavesestado_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Llavesmovimiento' => [
            'className' => 'Llavesmovimiento',
            'foreignKey' => 'llave_id',
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

    public function getList($client_id = null) {
        return $this->find('list', ['conditions' => ['Llave.client_id' => empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id, 'Llave.habilitada' => 1, 'Consorcio.habilitado' => 1],
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Llave.consorcio_id']]]]);
    }

    public function getDisponibles($client_id = null) {
        return $this->find('list', ['conditions' => ['Llave.client_id' => empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id, 'Llave.llavesestado_id !=' => 2, 'Llave.habilitada' => 1, 'Consorcio.habilitado' => 1],
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Llave.consorcio_id']]]]);
    }

    public function getEntregadas($reparacione_id, $client_id = null) {// estado entregada = 2
        return $this->find('list', ['conditions' => ['Llave.llavesestado_id !=' => 1, 'Llave.reparacione_id' => $reparacione_id, 'Llave.client_id' => empty($client_id) ? $_SESSION['Auth']['User']['client_id'] : $client_id,
                        'Llave.habilitada' => 1, 'Consorcio.habilitado' => 1], 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Llave.consorcio_id']]]]);
    }

    public function habilitarDeshabilitar($llave_id) {
        $this->id = $llave_id;
        if ($this->field('habilitada')) {// esta habilitada
            if ($this->field('llavesestado_id') == 1) { // intenta deshabilitar
                $this->saveField('habilitada', 0);
                return ['e' => 0, 'd' => __("La Llave fue deshabilitada correctamente")];
            } else {
                return ['e' => 1, 'd' => __("La Llave se encuentra Entregada, no se puede deshabilitar")];
            }
        } else {
            $this->saveField('habilitada', 1);
            return ['e' => 0, 'd' => __("La Llave fue habilitada correctamente")];
        }
    }

    public function beforeSave($options = []) {
        if (isset($this->data['Llave']['consorcio_id'])) {
            $max = $this->find('first', ['conditions' => ['Llave.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['max(Llave.numero) as numero']]);
            $this->data['Llave']['numero'] = $max[0]['numero'] + 1;
            $this->data['Llave']['user_id'] = $_SESSION['Auth']['User']['id'];
            $this->data['Llave']['llavesestado_id'] = 1; // estado "recibida"
            $this->data['Llave']['client_id'] = $_SESSION['Auth']['User']['client_id'];
            $this->data['Llave']['fecha'] = $this->fecha($this->data['Llave']['fecha']);
        }
        return true;
    }

    public function beforeDelete($cascade = true) {
        if ($this->field('habilitada')) {
            return false;
        }
        $count = $this->Llavesmovimiento->find('count', ['conditions' => ['llave_id' => $this->id]]);
        if ($count == 0) {
            return true;
        }
        return false;
    }

// funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Llave.descripcion LIKE' => '%' . $data['buscar'] . '%',
                'Llave.numero' => $data['buscar'],
        ]];
    }

}
