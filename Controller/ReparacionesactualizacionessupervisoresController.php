<?php

App::uses('AppController', 'Controller');

class ReparacionesactualizacionessupervisoresController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Reparacionesactualizacionessupervisore->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Reparacionesactualizacionessupervisore.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Reparacionesactualizacionessupervisore->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('reparacionesactualizacionessupervisores', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Reparacionesactualizacionessupervisore->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Reparacionesactualizacionessupervisore.' . $this->Reparacionesactualizacionessupervisore->primaryKey => $id]];
        $this->set('reparacionesactualizacionessupervisore', $this->Reparacionesactualizacionessupervisore->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Reparacionesactualizacionessupervisore->create();
            if ($this->Reparacionesactualizacionessupervisore->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $reparacionesactualizaciones = $this->Reparacionesactualizacionessupervisore->Reparacionesactualizacione->find('list');
        $reparacionessupervisores = $this->Reparacionesactualizacionessupervisore->Reparacionessupervisore->find('list');
        $this->set(compact('reparacionesactualizaciones', 'reparacionessupervisores'));
    }

    public function edit($id = null) {
        if (!$this->Reparacionesactualizacionessupervisore->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Reparacionesactualizacionessupervisore->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Reparacionesactualizacionessupervisore.id' => $id]];
            $this->request->data = $this->Reparacionesactualizacionessupervisore->find('first', $options);
        }
        $reparacionesactualizaciones = $this->Reparacionesactualizacionessupervisore->Reparacionesactualizacione->find('list');
        $reparacionessupervisores = $this->Reparacionesactualizacionessupervisore->Reparacionessupervisore->find('list');
        $this->set(compact('reparacionesactualizaciones', 'reparacionessupervisores'));
    }

    public function delete($id = null) {
        $this->Reparacionesactualizacionessupervisore->id = $id;
        if (!$this->Reparacionesactualizacionessupervisore->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Reparacionesactualizacionessupervisore->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
