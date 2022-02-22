<?php

App::uses('AppController', 'Controller');

class CartadeudoresController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index($id) {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Cartadeudore->parseCriteria($this->passedArgs)];
        if (!$this->Cartadeudore->Propietario->Consorcio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['controller' => 'Consorcios', 'action' => 'index']);
        }
        $conditions += ['Consorcio.id' => $id];
        $this->passedArgs = []; // para evitar

        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cartadeudore.propietario_id=Propietario.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']]],
            'fields' => ['Consorcio.name', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Cartadeudore.created', 'Cartadeudore.id'],
            'order' => 'Cartadeudore.created desc'
        ];
        $this->Paginator->settings += ['limit' => 20];
        $this->Prg->commonProcess();

        $this->set('cartadeudores', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Cartadeudore->Propietario->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Cartadeudore->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $cartadeudore = $this->Cartadeudore->find('first', ['conditions' => ['Cartadeudore.id' => $id],
            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cartadeudore.propietario_id=Propietario.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']]],
            'fields' => ['Consorcio.name', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Cartadeudore.created', 'Cartadeudore.id', 'Cartadeudore.carta']]);
        $this->set(compact('cartadeudore'));
        $this->layout = '';
    }

}
