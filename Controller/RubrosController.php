<?php

App::uses('AppController', 'Controller');

class RubrosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Rubro->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = array('conditions' => $conditions, 'contain' => ['Consorcio'], 'fields' => ['Consorcio.id', 'Consorcio.name', 'Rubro.name', 'Rubro.orden', 'Rubro.habilitado', 'Rubro.id']);

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400/* , 'order' => 'Rubro.name' */];
        }
        $this->set('rubros', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Rubro->Consorcio->getConsorciosList());
    }

    public function add() {
        if ($this->request->is('post')) {
            if ($this->Rubro->guardar($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Rubro->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        $this->Rubro->id = $id;
        if (!$this->Rubro->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Rubro->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen gastos asociados al Rubro, no se puede eliminar'));
        }
        return $this->redirect($this->referer());
    }

}
