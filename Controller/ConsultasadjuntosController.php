<?php

App::uses('AppController', 'Controller');

class ConsultasadjuntosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'delAdjunto', 'setArchivo'); // permito blackhole x ajax
    }

    public function index() {
        $this->Consultasadjunto->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Consultasadjunto.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Consultasadjunto->parseCriteria($this->passedArgs)));
        $this->Prg->commonProcess();
        $this->set('consultasadjuntos', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Consultasadjunto->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Consultasadjunto.' . $this->Consultasadjunto->primaryKey => $id));
        $this->set('consultasadjunto', $this->Consultasadjunto->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Consultasadjunto->create();
            if ($this->Consultasadjunto->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function edit($id = null) {
        if (!$this->Consultasadjunto->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Consultasadjunto->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Consultasadjunto.' . $this->Consultasadjunto->primaryKey => $id));
            $this->request->data = $this->Consultasadjunto->find('first', $options);
        }
    }

    public function delete($id = null) {
        $this->Consultasadjunto->id = $id;
        if (!$this->Consultasadjunto->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Consultasadjunto->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function setArchivo($data) {
        $this->Consultasadjunto->setArchivo($data);
        return $this->Consultasadjunto->find('all', array('conditions' => array('Consultasadjunto.client_id' => $_SESSION['Auth']['User']['client_id']), 'limit' => 15, 'fields' => array('Consultasadjunto.ruta as r', "DATE_FORMAT(Consultasadjunto.created,'%d/%m/%Y %T') as f"), 'order' => 'created asc'));
    }

    public function delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultasadjunto->delAdjunto($this->request->data['id'], $this->request->data['cli'])));
    }

}
