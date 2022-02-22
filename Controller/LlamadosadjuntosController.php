<?php

App::uses('AppController', 'Controller');

class LlamadosadjuntosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

//    public function setArchivo($data) {
//        $this->Llamadosadjunto->setArchivo($data);
//        return $this->Llamadosadjunto->find('all', array('conditions' => array('Llamadosadjunto.client_id' => $_SESSION['Auth']['User']['client_id']), 'limit' => 15, 'fields' => array('Llamadosadjunto.ruta as r', "DATE_FORMAT(Llamadosadjunto.created,'%d/%m/%Y %T') as f"), 'order' => 'created asc'));
//    }

    public function panel_delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Llamadosadjunto->delAdjunto($this->request->data['id'], $this->request->data['cli'])));
    }

}
