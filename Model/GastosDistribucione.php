<?php

App::uses('AppModel', 'Model');

class GastosDistribucione extends AppModel {

    public $displayField = 'nombre';
    public $validate = [
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            ],
        ],
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            ],
        ],
    ];
    public $belongsTo = [
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = array(
        'GastosDistribucionesDetalle' => array(
            'className' => 'GastosDistribucionesDetalle',
            'foreignKey' => 'gastos_distribucione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
    ));

    //array(
    //    'GastosDistribucione' => array(
    //        'consorcio_id' => '7',
    //        'nombre' => 'aaaa'
    //    ),
    //    'GastosDistribucionesDetalle' => array(
    //        (int) 0 => array(
    //                'porcentaje' => '33',
    //                'coeficiente_id' => '21'
    //        ),
    //        (int) 1 => array(
    //                'porcentaje' => '44',
    //                'coeficiente_id' => '22'
    //        )
    //    )
    //)
    public function guardar($data) {
        $dist = [];
        $dist['GastosDistribucione'] = $data['GastosDistribucione'];
        unset($data['GastosDistribucione']);
        $suma = 0;
        foreach ($data['GastosDistribucionesDetalle'] as $v) {
            $suma += $v['porcentaje'];
        }
        if ($suma != 100) {
            return false;
        } else {
            $this->create();
            $resul = $this->save($dist);
            $id = $resul['GastosDistribucione']['id'];

            foreach ($data['GastosDistribucionesDetalle'] as $v) {
                $v['gastos_distribucione_id'] = $id;
                $this->GastosDistribucionesDetalle->create();
                $this->GastosDistribucionesDetalle->save($v);
            }

            return true;
        }
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'GastosDistribucione.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
