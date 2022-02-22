<?php

App::uses('AppController', 'Controller');

class CuentasgastosparticularesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Cuentasgastosparticulare->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions, 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Cuentasgastosparticulare.consorcio_id']], ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]],
            'fields' => ['Consorcio.name', 'Consorcio.id', 'Client.name', 'Cuentasgastosparticulare.id', 'Cuentasgastosparticulare.name']];

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('cuentasgastosparticulares', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Cuentasgastosparticulare->Consorcio->getConsorciosList());
    }

    public function add() {
        if ($this->request->is('post')) {
            if ($this->Cuentasgastosparticulare->guardar($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Cuentasgastosparticulare->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        if (!$this->Cuentasgastosparticulare->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Cuentasgastosparticulare->id = $id;
        if ($this->Cuentasgastosparticulare->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen gastos particulares asociados a la Cuenta, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
