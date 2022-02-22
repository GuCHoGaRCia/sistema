<?php

App::uses('AppController', 'Controller');

class ReparacionesestadosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Reparacionesestado->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Reparacionesestado.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Reparacionesestado->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('reparacionesestados', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Reparacionesestado->create();
            if ($this->Reparacionesestado->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function edit($id = null) {
        if (!$this->Reparacionesestado->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Reparacionesestado->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Reparacionesestado.' . $this->Reparacionesestado->primaryKey => $id]];
            $this->request->data = $this->Reparacionesestado->find('first', $options);
        }
    }

}
