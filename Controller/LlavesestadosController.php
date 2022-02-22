<?php

App::uses('AppController', 'Controller');

class LlavesestadosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        //$this->Llavesestado->recursive = 0;
        $this->Prg->commonProcess();
        $this->set('llavesestados', $this->paginar($this->Paginator));
    }

    /* public function add() {
      if ($this->request->is('post')) {
      $this->Llavesestado->create();
      if ($this->Llavesestado->save($this->request->data)) {
      $this->Flash->success(__('El dato fue guardado'));
      return $this->redirect(['action' => 'index']);
      } else {
      $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
      }
      }
      } */

    /* public function delete($id = null) {
      $this->Llavesestado->id = $id;
      if (!$this->Llavesestado->exists()) {
      $this->Flash->error(__('El dato es inexistente'));
      return $this->redirect(['action' => 'index']);
      }
      $this->request->allowMethod('post', 'delete');
      if ($this->Llavesestado->delete()) {
      $this->Flash->success(__('El dato fue eliminado'));
      } else {
      $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
      }
      return $this->redirect($this->referer());
      } */
}
