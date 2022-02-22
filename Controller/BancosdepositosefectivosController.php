<?php

App::uses('AppController', 'Controller');

class BancosdepositosefectivosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Bancosdepositosefectivo->recursive = 0;
        $conditions = ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], ['OR' => ['Consorcio.habilitado' => 1, 'Bancoscuenta.consorcio_id' => 0]], 'Bancosdepositosefectivo.caja_id !=' => 0, $this->Bancosdepositosefectivo->parseCriteria($this->passedArgs)];
        $conditions += isset($this->request->data['Bancosdepositosefectivo']['bancoscuenta']) && $this->request->data['Bancosdepositosefectivo']['bancoscuenta'] !== '0' ? ['Bancoscuenta.id' => $this->request->data['Bancosdepositosefectivo']['bancoscuenta']] : [];
        $conditions += isset($this->request->data['Bancosdepositosefectivo']['incluye_anulados']) && $this->request->data['Bancosdepositosefectivo']['incluye_anulados'] == '1' ? [] : ['Bancosdepositosefectivo.anulado' => 0];
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => array(array('table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => array('Banco.id=Bancoscuenta.banco_id')), array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Bancoscuenta.consorcio_id=Consorcio.id'))),
            'fields' => array('Caja.name', 'Caja.id', 'Bancosdepositosefectivo.cobranza_id', 'Bancosdepositosefectivo.conciliado', 'Bancoscuenta.name', 'Bancoscuenta.id',
                'Bancosdepositosefectivo.id', 'Bancosdepositosefectivo.concepto', 'Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.anulado', 'Bancosdepositosefectivo.fecha', 'Bancosdepositosefectivo.created', 'Bancosdepositosefectivo.user_id'),
            'order' => 'Bancosdepositosefectivo.fecha desc,Bancosdepositosefectivo.created desc');

        if (!isset($this->request->data['Bancosdepositosefectivo']) || empty($this->request->data['Bancosdepositosefectivo'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('bancosdepositosefectivos', $this->paginar($this->Paginator));
        $this->set('bancoscuenta', $this->Bancosdepositosefectivo->Bancoscuenta->get());
    }

    public function index2() {
        $this->Bancosdepositosefectivo->recursive = 0;
        $conditions = ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], ['OR' => ['Consorcio.habilitado' => 1, 'Bancoscuenta.consorcio_id' => 0]], 'Bancosdepositosefectivo.caja_id' => 0, 'Bancosdepositosefectivo.es_transferencia' => false, 'Bancosdepositosefectivo.cobranza_id' => null,
            $this->Bancosdepositosefectivo->parseCriteria($this->passedArgs)];
        $conditions += isset($this->request->data['Bancosdepositosefectivo']['bancoscuenta']) && $this->request->data['Bancosdepositosefectivo']['bancoscuenta'] !== '0' ? ['Bancoscuenta.id' => $this->request->data['Bancosdepositosefectivo']['bancoscuenta']] : [];
        $conditions += isset($this->request->data['Bancosdepositosefectivo']['incluye_anulados']) && $this->request->data['Bancosdepositosefectivo']['incluye_anulados'] == '1' ? [] : ['Bancosdepositosefectivo.anulado' => 0];
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => array(array('table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => array('Banco.id=Bancoscuenta.banco_id')), array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Bancoscuenta.consorcio_id'))),
            'fields' => array('Caja.name', 'Caja.id', 'Bancosdepositosefectivo.cobranza_id', 'Bancosdepositosefectivo.conciliado', 'Bancoscuenta.name', 'Bancoscuenta.id',
                'Bancosdepositosefectivo.id', 'Bancosdepositosefectivo.concepto', 'Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.anulado', 'Bancosdepositosefectivo.fecha', 'Bancosdepositosefectivo.created', 'Bancosdepositosefectivo.user_id'),
            'order' => 'Bancosdepositosefectivo.fecha desc,Bancosdepositosefectivo.created desc');

        if (!isset($this->request->data['Bancosdepositosefectivo']) || empty($this->request->data['Bancosdepositosefectivo'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('bancosdepositosefectivos', $this->paginar($this->Paginator));
        $this->set('bancoscuenta', $this->Bancosdepositosefectivo->Bancoscuenta->get());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Bancosdepositosefectivo->create();
            if ($this->Bancosdepositosefectivo->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La caja no posee saldo suficiente, no se puede guardar el dato'));
            }
        }
        $caja = $this->Bancosdepositosefectivo->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if ($caja == -1) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar un dep&oacute;sito'));
            return $this->redirect(['action' => 'index']);
        }
        $bancoscuentas = $this->Bancosdepositosefectivo->Bancoscuenta->get();
        if (count($bancoscuentas) == 0) {
            $this->Flash->error(__('Debe crear una Cuenta bancaria (men&uacute Bancos) antes de agregar un dep&oacute;sito'));
            return $this->redirect(['action' => 'index']);
        }
        $saldo = $this->Bancosdepositosefectivo->Caja->find('first', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $caja), 'fields' => 'Caja.saldo_pesos'));
        $this->set(compact('bancoscuentas', 'saldo'));
    }

    public function add2() {
        if ($this->request->is('post')) {
            $this->Bancosdepositosefectivo->create();
            if ($this->Bancosdepositosefectivo->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index2']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $bancoscuentas = $this->Bancosdepositosefectivo->Bancoscuenta->get();
        if (count($bancoscuentas) == 0) {
            $this->Flash->error(__('Debe crear una Cuenta bancaria (men&uacute Bancos) antes de agregar un dep&oacute;sito'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('bancoscuentas'));
    }

    public function delete($id = null) {
        $this->Bancosdepositosefectivo->id = $id;
        if (!$this->Bancosdepositosefectivo->exists() || $this->Bancosdepositosefectivo->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosdepositosefectivo.id' => $id), 'recursive' => 0, 'joins' => array(array('table' => 'bancos', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Bancoscuenta.banco_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Bancosdepositosefectivo->undo($id)) {
            $this->Flash->success(__('El movimiento fue anulado'));
        } else {
            $this->Flash->error(__('El movimiento no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
