<?php

App::uses('AppController', 'Controller');

class CartaspreciosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Cartasprecio->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Cartasprecio.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Cartasprecio->parseCriteria($this->passedArgs)),
            'order' => 'Cartastipo.nombre');
        $this->Prg->commonProcess();
        $this->set('cartasprecios', $this->paginar($this->Paginator));
    }

    public function panel_index() {
        $conditions = [$this->Cartasprecio->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Cartasprecio.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }
        $this->Cartasprecio->recursive = 0;
        $this->Paginator->settings = array('conditions' => $conditions, 'order' => 'Client.name,Cartastipo.nombre');

        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 10];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('cartasprecios', $this->paginar($this->Paginator));
        $this->set('clients', $this->Cartasprecio->Client->find('list'));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Cartasprecio->create();
            if ($this->Cartasprecio->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->Cartasprecio->Client->find('list');
        $cartastipos = $this->Cartasprecio->Cartastipo->find('list');
        $this->set(compact('clients', 'cartastipos'));
    }

    public function panel_generar() {
        $clients = $this->Cartasprecio->Client->find('list');
        $cartastipos = $this->Cartasprecio->Cartastipo->find('list');
        foreach ($clients as $k => $v) {
            foreach ($cartastipos as $r => $s) {
                if (empty($this->Cartasprecio->find('first', ['conditions' => ['Cartasprecio.client_id' => $k, 'Cartasprecio.cartastipo_id' => $r]]))) {
                    $this->Cartasprecio->create();
                    $this->Cartasprecio->save(['client_id' => $k, 'cartastipo_id' => $r, 'importe' => 0]);
                }
            }
        }
        $this->Flash->success(__('Se generaron los Precios de los Tipos de Carta para todos los Clientes'));
        $this->autoRender = false;
        return $this->redirect(['action' => 'index']);
    }

    public function panel_delete($id = null) {
        $this->Cartasprecio->id = $id;
        if (!$this->Cartasprecio->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Cartasprecio->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
