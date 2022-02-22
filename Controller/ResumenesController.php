<?php

App::uses('AppController', 'Controller');

class ResumenesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Resumene->recursive = 0;
        $this->set('resumenes', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Resumene->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Resumene.' . $this->Resumene->primaryKey => $id));
        $this->set('resumene', $this->Resumene->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Resumene->create();
            if ($this->Resumene->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $liquidations = $this->Resumene->Liquidation->find('list');
        $this->set(compact('liquidations'));
    }

    public function edit($id = null) {
        if (!$this->Resumene->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Resumene->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Resumene.' . $this->Resumene->primaryKey => $id));
            $this->request->data = $this->Resumene->find('first', $options);
        }
        $liquidations = $this->Resumene->Liquidation->find('list');
        $this->set(compact('liquidations'));
    }

    public function delete($id = null) {
        $this->Resumene->id = $id;
        if (!$this->Resumene->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Resumene->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
