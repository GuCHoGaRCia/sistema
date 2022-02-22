<?php

App::uses('AppController', 'Controller');

class AvisosqueuesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->Avisosqueue->recursive = 0;
        $this->Paginator->settings = ['conditions' => [$this->Avisosqueue->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('avisosqueues', $this->paginar($this->Paginator));
    }

}
