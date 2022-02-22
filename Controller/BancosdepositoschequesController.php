<?php

App::uses('AppController', 'Controller');

class BancosdepositoschequesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = [array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Bancosdepositoscheque->parseCriteria($this->passedArgs))];
        $this->Bancosdepositoscheque->recursive = 0;
        if (isset($this->request->data['filter']['cuenta']) && $this->request->data['filter']['cuenta'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['cuenta'])) {
            $conditions += ['Bancosdepositoscheque.bancoscuenta_id' => $this->request->data['filter']['cuenta']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => array(array('table' => 'bancos', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Bancoscuenta.banco_id')),
                array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Bancoscuenta.consorcio_id'))),
            'fields' => array('Cheque.concepto', 'Cheque.id', 'Cheque.importe', 'Bancoscuenta.name', 'Bancoscuenta.id', 'Bancosdepositoscheque.id',
                'Bancosdepositoscheque.user_id', 'Bancosdepositoscheque.concepto', 'Bancosdepositoscheque.fecha', 'Bancosdepositoscheque.anulado'),
            'order' => 'Bancosdepositoscheque.fecha desc,Bancosdepositoscheque.created desc');

        if (!isset($this->request->data['filter']['cuenta'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }

        $cuentasBancarias = $this->Bancosdepositoscheque->Bancoscuenta->get();
        $this->set(compact('cuentasBancarias'));
        $this->set('bancosdepositoscheques', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Bancosdepositoscheque->create();
            $resul = $this->Bancosdepositoscheque->guardar($this->request->data);
            if ($resul === "") {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error($resul);
            }
        }

        $cheques = $this->Bancosdepositoscheque->Cheque->getChequesListosParaEntregar();
        if (empty($cheques)) {
            $this->Flash->error(__('No existen cheques para depositar'));
            return $this->redirect(['action' => 'index']);
        }
        $bancoscuentas = $this->Bancosdepositoscheque->Bancoscuenta->get();
        $this->set(compact('cheques', 'bancoscuentas'));
    }

    public function delete($id = null) {
        $this->Bancosdepositoscheque->id = $id;
        if (!$this->Bancosdepositoscheque->exists() || $this->Bancosdepositoscheque->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosdepositoscheque.id' => $id), 'recursive' => 0, 'joins' => array(array('table' => 'bancos', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Bancoscuenta.banco_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Bancosdepositoscheque->undo($id)) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
