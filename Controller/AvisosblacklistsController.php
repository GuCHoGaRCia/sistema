<?php

App::uses('AppController', 'Controller');

class AvisosblacklistsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = [$this->Avisosblacklist->parseCriteria($this->passedArgs), 'Avisosblacklist.client_id' => $_SESSION['Auth']['User']['client_id']];
        $this->Paginator->settings = ['conditions' => $conditions];
        $this->Paginator->settings += ['limit' => 50];
        $this->Prg->commonProcess();
        $this->set('avisosblacklists', $this->paginar($this->Paginator));
    }

    public function panel_index() {
        $conditions = [$this->Avisosblacklist->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente']) && $this->request->data['filter']['cliente'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['or' => ['Avisosblacklist.client_id' => $this->request->data['filter']['cliente']]];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions];

        //debug($this->Paginator->settings);die;
        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 10];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('clients', $this->Avisosblacklist->Client->find('list', ['fields' => ['Client.idglobal', 'Client.name'], 'order' => 'Client.name'])); // los clientes con idglobal=>name (idglobal del ceo viejo)
        $this->set('clients2', $this->Avisosblacklist->Client->find('list'), ['order' => 'Client.name']); // los clientes con id=>name
        $this->set('avisosblacklists', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Avisosblacklist->create();
            if ($this->Avisosblacklist->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->Avisosblacklist->Client->find('list', ['order' => 'Client.name']);
        $this->set(compact('clients'));
    }

    public function panel_delete($id = null) {
        $this->Avisosblacklist->id = $id;
        if (!$this->Avisosblacklist->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Avisosblacklist->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function delete($id = null) {
        $this->Avisosblacklist->id = $id;
        if (!$this->Avisosblacklist->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Avisosblacklist->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
