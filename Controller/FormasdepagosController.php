<?php

App::uses('AppController', 'Controller');

class FormasdepagosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Formasdepago->recursive = 0;
        $this->Paginator->settings = ['conditions' => [$this->Formasdepago->parseCriteria($this->passedArgs), 'Formasdepago.client_id' => $_SESSION['Auth']['User']['client_id']], 'order' => 'habilitada desc,orden'];
        $this->Prg->commonProcess();
        $this->set('formasdepagos', $this->paginar($this->Paginator));
    }

    public function panel_index() {
        if (isset($this->request->data['filter']['cliente']) && $this->request->data['filter']['cliente'] === "") {
            unset($this->request->data['filter']);
        }
        $conditions = [$this->Formasdepago->parseCriteria($this->passedArgs)];

        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Formasdepago.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0];

        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 1000, 'maxLimit' => 1000];
        }

        $this->set('formasdepagos', $this->paginar($this->Paginator));
        $this->set('client_id', $this->Formasdepago->Client->find('list'));
    }

    /*public function add() {
        if ($this->request->is('post')) {
            $this->Formasdepago->create();
            if ($this->Formasdepago->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }*/

    public function panel_add() {
        if ($this->request->is('post')) {
            if ($this->Formasdepago->guardar($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

}
