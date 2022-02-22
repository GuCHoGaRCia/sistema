<?php

App::uses('AppController', 'Controller');

class ContfuncionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->Paginator->settings = ['conditions' => [$this->Contfuncione->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('contfunciones', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Contfuncione->create();
            if ($this->Contfuncione->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function panel_delete($id = null) {
        $this->Contfuncione->id = $id;
        if (!$this->Contfuncione->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Contfuncione->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
