<?php

App::uses('AppModel', 'Model');

class Reportsclient extends AppModel {

    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            ),
        ),
        'report_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
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
        'Report' => array(
            'className' => 'Report',
            'foreignKey' => 'report_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getReporteAMostrar($reporte, $client_id) {
        $options = ['conditions' => ['Reportsclient.client_id' => $client_id, 'Report.enabled' => 1, 'Report.funcion like' => $reporte . '%'],
            'fields' => ['Report.funcion'],
            'recursive' => 0
        ];
        $r = $this->find('first', $options);
        return (empty($r) ? $reporte : $r['Report']['funcion']);
    }

}
