<?php

App::uses('AppController', 'Controller');

class BancosextraccionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Bancosextraccione->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosextraccione.caja_id !=' => 0, 'Consorcio.habilitado' => 1, $this->Bancosextraccione->parseCriteria($this->passedArgs)),
            'joins' => array(array('table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => array('Banco.id=Bancoscuenta.banco_id'))),
            'fields' => ['Bancoscuenta.name', 'Caja.name', 'Caja.id', 'Bancosextraccione.id', 'Bancosextraccione.user_id', 'Bancosextraccione.fecha', 'Bancosextraccione.created', 'Bancosextraccione.concepto', 'Bancosextraccione.importe', 'Bancosextraccione.proveedorspago_id', 'Bancosextraccione.anulado'],
            'order' => 'Bancosextraccione.fecha desc');
        $this->Prg->commonProcess();
        $this->set('bancosextracciones', $this->paginar($this->Paginator));
    }

    public function index2() {
        $conditions = ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosextraccione.caja_id' => 0, 'Bancosextraccione.proveedorspago_id' => 0, 'Consorcio.habilitado' => 1, $this->Bancosextraccione->parseCriteria($this->passedArgs)];
        $conditions += isset($this->request->data['Bancosextraccione']['cuenta']) && $this->request->data['Bancosextraccione']['cuenta'] !== '0' ? ['Bancosextraccione.bancoscuenta_id' => $this->request->data['Bancosextraccione']['cuenta']] : [];
        $conditions += isset($this->request->data['Bancosextraccione']['anulado']) && $this->request->data['Bancosextraccione']['anulado'] == '1' ? [] : ['Bancosextraccione.anulado' => 0];
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => array(array('table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => array('Banco.id=Bancoscuenta.banco_id'))),
            'fields' => ['Bancoscuenta.name', 'Caja.name', 'Caja.id', 'Bancosextraccione.id', 'Bancosextraccione.fecha', 'Bancosextraccione.conciliado', 'Bancosextraccione.created', 'Bancosextraccione.concepto', 'Bancosextraccione.importe', 'Bancosextraccione.user_id', 'Bancosextraccione.proveedorspago_id', 'Bancosextraccione.anulado'],
            'recursive' => 0,
            'order' => 'Bancosextraccione.fecha desc');
        if (!isset($this->request->data['Bancosextraccione']) || empty($this->request->data['Bancosextraccione'])) {
            $this->Paginator->settings += ['limit' => 10];
            //$this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('bancosextracciones', $this->paginar($this->Paginator));
        $this->set('cuentas', $this->Bancosextraccione->Bancoscuenta->get());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Bancosextraccione->create();
            if ($this->Bancosextraccione->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                //debug($this->Bancosextraccione->validationErrors);
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $bancoscuentas = $this->Bancosextraccione->Bancoscuenta->get();
        if (count($bancoscuentas) == 0) {
            $this->Flash->error(__('Debe crear una Cuenta bancaria (men&uacute Bancos) antes de agregar una extracci&oacute;n'));
            return $this->redirect(['action' => 'index']);
        }
        $cajas = $this->Bancosextraccione->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $this->Bancosextraccione->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']))));
        if (count($cajas) == 0) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar una extracci&oacute;n'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('consorcios', $this->Bancosextraccione->Consorcio->getConsorciosList());
        $this->set(compact('bancoscuentas', 'cajas'));
    }

    public function add2() {
        if ($this->request->is('post')) {
            $this->Bancosextraccione->create();
            if ($this->Bancosextraccione->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index2']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $bancoscuentas = $this->Bancosextraccione->Bancoscuenta->get();
        if (count($bancoscuentas) == 0) {
            $this->Flash->error(__('Debe crear una Cuenta bancaria (men&uacute Bancos) antes de agregar un dep&oacute;sito'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('bancoscuentas'));
    }

    public function delete($id = null) {
        $this->Bancosextraccione->id = $id;
        if (!$this->Bancosextraccione->exists() || $this->Bancosextraccione->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosextraccione.id' => $id), 'recursive' => 0, 'joins' => array(array('table' => 'bancoscuentas', 'alias' => 'c1', 'type' => 'left', 'conditions' => array('c1.id=Bancosextraccione.bancoscuenta_id')), array('table' => 'bancos', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Bancoscuenta.banco_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Bancosextraccione->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El movimiento fue anulado'));
        } else {
            $this->Flash->error(__('El movimiento no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
