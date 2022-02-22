<?php

App::uses('AppController', 'Controller');

class PlataformasdepagosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->Plataformasdepago->recursive = 0;
        $this->Paginator->settings = [];
        $this->Prg->commonProcess();
        $this->set('ppc', $this->Plataformasdepago->Plataformasdepagosconfig->getList());
        $this->set('plataformasdepagos', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Plataformasdepago->create();
            if ($this->Plataformasdepago->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    /* public function panel_delete($id = null) {
      $this->Plataformasdepago->id = $id;
      if (!$this->Plataformasdepago->exists()) {
      $this->Flash->error(__('El dato es inexistente'));
      return $this->redirect(['action' => 'index']);
      }
      $this->request->allowMethod('post', 'delete');
      if ($this->Plataformasdepago->delete()) {
      $this->Flash->success(__('El dato fue eliminado'));
      } else {
      $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
      }
      return $this->redirect($this->referer());
      } */
}
