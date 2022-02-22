<?php

class AuditDelta extends AppModel {

    public $belongsTo = array(//
        'Audit' => array(
            'className' => 'Audit',
            'foreignKey' => 'audit_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
