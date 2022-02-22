<?php

App::uses('AppController', 'Controller');

class CajasingresosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Cajasingreso->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['OR' => ['Bancoscuenta.consorcio_id' => $this->request->data['filter']['consorcio'], 'Propietario.consorcio_id' => $this->request->data['filter']['consorcio'], 'Cajasingreso.consorcio_id' => $this->request->data['filter']['consorcio']]];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cobranza.propietario_id=Propietario.id']],
                //['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']],
            ],
            'recursive' => 0,
            'order' => 'Cajasingreso.fecha desc,Cajasingreso.created desc',
            'group' => 'Cajasingreso.id');

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('cajasingresos', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Cajasingreso->Cobranza->Propietario->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'order' => 'Client.name,Consorcio.code']));
    }

    public function view($id = null) {
        if (!$this->Cajasingreso->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Cajasingreso.' . $this->Cajasingreso->primaryKey => $id));
        $this->set('cajasingreso', $this->Cajasingreso->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Cajasingreso->create();
            if ($this->Cajasingreso->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $cajaid = $this->Cajasingreso->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        $cajas = $this->Cajasingreso->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $cajaid)));
        if (count($cajas) == 0) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar un egreso'));
            return $this->redirect(['action' => 'index']);
        }
        $saldo = $this->Cajasingreso->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $cajaid), 'fields' => 'Caja.saldo_pesos'));
        $this->set('consorcios', $this->Cajasingreso->Cobranza->Propietario->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'order' => 'Client.name,Consorcio.code']));
        $this->set(compact('cajas', 'saldo'));
    }

    public function add2() {
        if ($this->request->is('post')) {
            $this->Cajasingreso->create();
            if ($this->Cajasingreso->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $cajas = $this->Cajasingreso->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'])));
        $this->set(compact('cajas'));
    }

    public function delete($id = null) {
        $this->Cajasingreso->id = $id;
        if (!$this->Cajasingreso->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Cajasingreso->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El movimiento fue anulado'));
        } else {
            $this->Flash->error(__('El movimiento no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
