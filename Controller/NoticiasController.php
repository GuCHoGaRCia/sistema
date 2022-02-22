<?php

App::uses('AppController', 'Controller');

class NoticiasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Paginator->settings = array('conditions' => array($this->Noticia->parseCriteria($this->passedArgs)), 'order' => 'created desc', 'limit' => 5);
        $this->Prg->commonProcess();
        $this->set('noticias', $this->paginar($this->Paginator));
    }

    public function panel_index() {
        $this->Paginator->settings = array('conditions' => array($this->Noticia->parseCriteria($this->passedArgs)), 'order' => 'created desc', 'limit' => 5);
        $this->Prg->commonProcess();
        $this->set('noticias', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Noticia->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Noticia.' . $this->Noticia->primaryKey => $id));
        $this->set('noticia', $this->Noticia->find('first', $options));
    }

    public function panel_view($id = null) {
        if (!$this->Noticia->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Noticia.' . $this->Noticia->primaryKey => $id));
        $this->set('noticia', $this->Noticia->find('first', $options));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Noticia->create();
            if ($this->Noticia->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function panel_edit($id = null) {
        if (!$this->Noticia->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Noticia->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Noticia.' . $this->Noticia->primaryKey => $id));
            $this->request->data = $this->Noticia->find('first', $options);
        }
    }

    public function panel_delete($id = null) {
        $this->Noticia->id = $id;
        if (!$this->Noticia->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Noticia->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
