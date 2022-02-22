<?php

App::uses('AppController', 'Controller');

class NotasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Nota->recursive = 0;
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Nota->parseCriteria($this->passedArgs), 'Liquidation.bloqueada' => 0];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Liquidation.consorcio_id'))),
            'fields' => array('Liquidation.periodo', 'Liquidation.id', 'Consorcio.name', 'Consorcio.id', 'Nota.id')
        );

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('notas', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Nota->Liquidation->Consorcio->getConsorciosList());
    }

    public function edit($id = null) {
        if (!$this->Nota->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Nota->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Nota.' . $this->Nota->primaryKey => $id));
            $this->request->data = $this->Nota->find('first', $options);
        }
        $liquidations = $this->Nota->Liquidation->find('list', array('conditions' => array('Liquidation.id' => $this->request->data['Nota']['liquidation_id']), 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']));
        $this->set(compact('liquidations'));
    }

}
