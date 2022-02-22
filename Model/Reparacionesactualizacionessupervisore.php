<?php

App::uses('AppModel', 'Model');

class Reparacionesactualizacionessupervisore extends AppModel {

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
        'reparacionessupervisore_id' => [
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
            //'message' => 'Your custom message here',
            //'allowEmpty' => true,
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
        'Reparacionessupervisore' => [
            'className' => 'Reparacionessupervisore',
            'foreignKey' => 'reparacionessupervisore_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * modifica los supervisores
     */

    public function modificar($reparacionesactualizacione_id, $asignar, $finalizar, $client_id = null) {
        if (!empty($asignar)) {// si no elige nada viene ''
            foreach ($asignar as $v) {
                $this->create();
                $this->save(['reparacionesactualizacione_id' => $reparacionesactualizacione_id, 'reparacionessupervisore_id' => $v, 'finalizado' => 0]);
                $this->Reparacionessupervisore->id = $v;
                $emails = $this->Reparacionessupervisore->field('email');
                if (!empty($emails) && !empty($client_id)) {
                    $em = array_unique(explode(',', $emails));
                    foreach ($em as $v) {
                        if (!empty($v) && filter_var($v, FILTER_VALIDATE_EMAIL) && !$this->Reparacionesactualizacione->User->Client->Avisosblacklist->isBlacklisted($v)) {
                            $link = $this->Reparacionesactualizacione->User->Client->Aviso->_encryptURL($v);
                            $url = "ceonline.com.ar/sup/?";
                            $datoscliente = $this->Reparacionesactualizacione->User->Client->find('first', ['conditions' => ['Client.id' => $client_id]]);
                            $html = utf8_decode("Estimado Supervisor:<br><br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Usted tiene una nueva Reparacion asignada. Ingrese <b>"
                                            . "<a href='https://$url" . "Reparacionessupervisores/view/$link'>AQUI</a></b> para visualizar la misma<br><br>") . (!empty($datoscliente) ? ($datoscliente['Client']['name']) : 'Su Administrador de Consorcios');
                            $text = utf8_decode("Estimado Supervisor: Usted tiene una nueva Reparacion asignada. Ingrese (o copie y pegue el link en el navegador) aqui"
                                            . "https://$url" . "Reparacionessupervisores/view/$link para visualizar la misma. Atte. ") . (!empty($datoscliente) ? ($datoscliente['Client']['name']) : 'Su Administrador de Consorcios');
                            $emails = explode(",", $datoscliente['Client']['email']);
                            $emailfrom = $emails[0];
                            $this->Reparacionesactualizacione->User->Client->Avisosqueue->create();
                            $this->Reparacionesactualizacione->User->Client->Avisosqueue->save(['client_id' => empty($datoscliente) ? $_SESSION['Auth']['User']['client_id'] : $datoscliente['Client']['id'],
                                'emailfrom' => $emailfrom, 'razonsocial' => !empty($datoscliente) ? h($datoscliente['Client']['name']) : 'CEONLINE',
                                'asunto' => 'Nueva Reparacion Asignada', 'altbody' => $text, 'codigohtml' => $html, 'mailto' => strtolower($v)], false);
                        }
                    }
                }
            }
        }
        if (!empty($finalizar)) {
            foreach ($finalizar as $v) {
                $this->create();
                $this->save(['reparacionesactualizacione_id' => $reparacionesactualizacione_id, 'reparacionessupervisore_id' => $v, 'finalizado' => 1]);
            }
        }
    }

}
