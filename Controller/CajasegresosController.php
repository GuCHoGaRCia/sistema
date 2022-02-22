<?php

App::uses('AppController', 'Controller');

class CajasegresosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Cajasegreso->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['OR' => ['Bancoscuenta.consorcio_id' => $this->request->data['filter']['consorcio'], 'Cajasegreso.consorcio_id' => $this->request->data['filter']['consorcio']]];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => [['table' => 'cajas', 'alias' => 'Caja', 'type' => 'left', 'conditions' => ['Cajasegreso.caja_id=Caja.id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Cajasegreso.bancoscuenta_id=Bancoscuenta.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Cajasegreso.consorcio_id']],
            //['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']]
            ],
            //'contain' => [/*'Caja',*/ 'Bancoscuenta', /*'Consorcio'*/],
            'order' => 'Cajasegreso.fecha desc,Cajasegreso.created desc',
            'fields' => ['Cajasegreso.*', 'Consorcio.name', 'Bancoscuenta.name', 'Caja.name'],
            'group' => 'Cajasegreso.id');

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('cajasegresos', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Cajasegreso->Bancoscuenta->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'order' => 'Client.name,Consorcio.code']));
    }

    public function view($id = null) {
        if (!$this->Cajasegreso->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Cajasegreso.' . $this->Cajasegreso->primaryKey => $id));
        $this->set('cajasegreso', $this->Cajasegreso->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            if ($this->Cajasegreso->crear($this->request->data['Cajasegreso'])) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $cajaid = $this->Cajasegreso->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        $cajas = $this->Cajasegreso->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $cajaid)));
        if (count($cajas) == 0) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar un egreso'));
            return $this->redirect(['action' => 'index']);
        }
        $saldo = $this->Cajasegreso->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $cajaid), 'fields' => 'Caja.saldo_pesos'));
        $this->set(compact('cajas', 'saldo'));
        $this->set('consorcios', $this->Cajasegreso->Bancoscuenta->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'order' => 'Client.name,Consorcio.code']));
    }

    public function delete($id = null) {
        $this->Cajasegreso->id = $id;
        if (!$this->Cajasegreso->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Cajasegreso->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El movimiento fue anulado'));
        } else {
            $this->Flash->error(__('El movimiento no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
