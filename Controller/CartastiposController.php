<?php

App::uses('AppController', 'Controller');

class CartastiposController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->Cartastipo->recursive = 0;
        $this->Paginator->settings = array('conditions' => array($this->Cartastipo->parseCriteria($this->passedArgs)));
        $this->Prg->commonProcess();
        $this->set('cartastipos', $this->paginar($this->Paginator));
    }


    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Cartastipo->create();
            if ($this->Cartastipo->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function panel_delete($id = null) {
        $this->Cartastipo->id = $id;
        if (!$this->Cartastipo->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Cartastipo->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
