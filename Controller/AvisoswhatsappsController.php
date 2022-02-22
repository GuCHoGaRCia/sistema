<?php

App::uses('AppController', 'Controller');

class AvisoswhatsappsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Avisoswhatsapp->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Avisoswhatsapp.client_id' => $_SESSION['Auth']['User']['Client']['id'],
                $this->Avisoswhatsapp->parseCriteria($this->passedArgs)], 'order' => 'Avisoswhatsapp.id desc'];
        $this->Prg->commonProcess();
        $this->set('avisoswhatsapps', $this->paginar($this->Paginator));
    }

}
