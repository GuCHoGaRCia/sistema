<?php

App::uses('AppController', 'Controller');

class ConsultaspropietariosadjuntosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'delAdjunto'); // permito blackhole x ajax
    }

    public function index() {
        $this->Consultaspropietariosadjunto->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Consultaspropietariosadjunto.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Consultaspropietariosadjunto->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('consultaspropietariosadjuntos', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Consultaspropietariosadjunto->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Consultaspropietariosadjunto.' . $this->Consultaspropietariosadjunto->primaryKey => $id]];
        $this->set('consultaspropietariosadjunto', $this->Consultaspropietariosadjunto->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Consultaspropietariosadjunto->create();
            if ($this->Consultaspropietariosadjunto->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->Consultaspropietariosadjunto->Client->find('list');
        $propietarios = $this->Consultaspropietariosadjunto->Propietario->find('list');
        $this->set(compact('clients', 'propietarios'));
    }

    public function edit($id = null) {
        if (!$this->Consultaspropietariosadjunto->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Consultaspropietariosadjunto->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Consultaspropietariosadjunto.' . $this->Consultaspropietariosadjunto->primaryKey => $id]];
            $this->request->data = $this->Consultaspropietariosadjunto->find('first', $options);
        }
        $clients = $this->Consultaspropietariosadjunto->Client->find('list');
        $propietarios = $this->Consultaspropietariosadjunto->Propietario->find('list');
        $this->set(compact('clients', 'propietarios'));
    }

    public function delete($id = null) {
        $this->Consultaspropietariosadjunto->id = $id;
        if (!$this->Consultaspropietariosadjunto->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Consultaspropietariosadjunto->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultaspropietariosadjunto->delAdjunto($this->request->data['id'], $this->request->data['cli'])));
    }

}
