<?php

App::uses('AppController', 'Controller');

class ConttitulosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Paginator->settings = ['conditions' => ['Conttitulo.client_id' => $_SESSION['Auth']['User']['Client']['id']], 'order' => 'code,orden', 'limit' => 100];
        //$this->Prg->commonProcess();
        $this->set('conttitulos', $this->paginar($this->Paginator));
        $this->set('titulos', $this->Conttitulo->get());
        $this->set('arbol', $this->Conttitulo->getArbol());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Conttitulo->create();
            if ($this->Conttitulo->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $conttitulos = $this->Conttitulo->getArbol();
        $tit = $this->Conttitulo->get();
        $this->set(compact('conttitulos', 'tit'));
    }

    public function edit($id = null) {
        if (!$this->Conttitulo->exists($id) || $this->Conttitulo->find('count', array('conditions' => array('Conttitulo.client_id' => $_SESSION['Auth']['User']['client_id'], 'Conttitulo.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Conttitulo->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Conttitulo.' . $this->Conttitulo->primaryKey => $id]];
            $this->request->data = $this->Conttitulo->find('first', $options);
        }
        $conttitulos = $this->Conttitulo->getArbol();
        $tit = $this->Conttitulo->get();
        $this->set(compact('conttitulos', 'tit'));
    }

    public function delete($id = null) {
        $this->Conttitulo->id = $id;
        if (!$this->Conttitulo->exists() || $this->Conttitulo->find('count', array('conditions' => array('Conttitulo.client_id' => $_SESSION['Auth']['User']['client_id'], 'Conttitulo.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Conttitulo->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
