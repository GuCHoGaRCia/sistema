<?php

App::uses('AppModel', 'Model');

class Audit extends AppModel {////

    public $actsAs = array('Search.Searchable', 'Containable');
    public $filterArgs = array(
        array('name' => 'buscar', 'type' => 'query', 'method' => 'filterName')
    );
    public $hasMany = array(
        'AuditDelta' => array(
            'className' => 'AuditDelta',
            'foreignKey' => 'audit_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Audit.event' => trim($data['buscar']),
                'Audit.model' => trim($data['buscar']),
                'Audit.json_object like' => '%' . trim($data['buscar']) . '%',
                'Audit.description' => trim($data['buscar']),
        ));
    }

}
