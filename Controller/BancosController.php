<?php

App::uses('AppController', 'Controller');

class BancosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Banco->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Banco->parseCriteria($this->passedArgs)), 'order' => 'Banco.name');
        $this->Prg->commonProcess();
        $this->set('bancos', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Banco->create();
            if ($this->Banco->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function delete($id = null) {
        if (!$this->Banco->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Banco->id = $id;
        if ($this->Banco->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen cuentas bancarias asociadas al banco, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
