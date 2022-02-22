<?php

App::uses('AppController', 'Controller');

class LiquidationsTypesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Paginator->settings = array('conditions' => array('LiquidationsType.client_id' => $_SESSION['Auth']['User']['client_id'], $this->LiquidationsType->parseCriteria($this->passedArgs)));
        $this->Prg->commonProcess();
        $this->set('liquidationsTypes', $this->paginar($this->Paginator));
    }

    public function panel_index() {
        $this->Paginator->settings = array('conditions' => array($this->LiquidationsType->parseCriteria($this->passedArgs)),
            'fields' => ['LiquidationsType.name', 'LiquidationsType.prefijo', 'LiquidationsType.enabled', 'LiquidationsType.id', 'Client.name'],
            'recursive' => 0,
            'order' => 'Client.name,LiquidationsType.name');
        $this->Prg->commonProcess();
        $this->set('liquidationsTypes', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->LiquidationsType->create();
            if ($this->LiquidationsType->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->LiquidationsType->Client->find('list', array('conditions' => array('Client.id' => $_SESSION['Auth']['User']['client_id'])));
        $this->set(compact('clients'));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->LiquidationsType->create();
            if ($this->LiquidationsType->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->LiquidationsType->Client->find('list', ['order' => 'Client.name']);
        $this->set(compact('clients'));
    }

    public function delete($id = null) {
        $this->LiquidationsType->id = $id;
        if (!$this->LiquidationsType->exists() || $this->LiquidationsType->find('count', array('conditions' => array('Client.id' => $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.id' => $id), 'recursive' => 0)) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->LiquidationsType->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function panel_delete($id = null) {
        $this->LiquidationsType->id = $id;
        if (!$this->LiquidationsType->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->LiquidationsType->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
