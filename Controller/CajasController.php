<?php

App::uses('AppController', 'Controller');

class CajasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Caja->recursive = 0;
        $this->Prg->commonProcess();
        $this->set('chequesconsaldo', $this->Caja->Cheque->getSaldoChequesPendientes(true)); // de todas las cajas
        $this->set('cajas', $this->Caja->find('all', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Caja->parseCriteria($this->passedArgs)),
                    'fields' => array('Caja.name', 'Caja.saldo_pesos', 'Caja.saldo_cheques', 'User.id', 'User.username'))));
    }

    public function view($id = null) {
        if (!$this->Caja->exists($id) || $this->Caja->find('count', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $d = $h = null;
        if ($this->request->is('post')) {
            $d = $this->Caja->fecha($this->request->data['Caja']['desde']);
            $h = $this->Caja->fecha($this->request->data['Caja']['hasta']);
            if (!$this->Caja->validateDate($d, 'Y-m-d') || !$this->Caja->validateDate($h, 'Y-m-d') || !$this->Caja->fechaEsMenorIgualQue($d, $h)) {
                $this->Flash->error(__('Las fechas son incorrectas'));
                return $this->redirect($this->referer());
            }
        }
        $this->set('movimientos', $this->Caja->getMovimientos(isset($this->request->data['Caja']['cajas']) ? $this->request->data['Caja']['cajas'] : $id, $d, $h, 1));
        //} else {
        //    $this->set('movimientos', $this->Caja->getMovimientos($id));
        //}

        $options = ['conditions' => ['Caja.id' => isset($this->request->data['Caja']['cajas']) ? $this->request->data['Caja']['cajas'] : $id], 'recursive' => 0, 'fields' => ['Caja.id', 'Caja.name', 'Caja.saldo_pesos', 'Caja.saldo_cheques']];
        $this->set('c', $this->Caja->find('first', $options));
        $this->set('cajas', $this->Caja->find('list', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id']]]));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Caja->create();
            if ($this->Caja->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Ya existe una caja para el usuario seleccionado'));
            }
        }
        $users = $this->Caja->Client->User->find('list', array('conditions' => array('User.client_id' => $_SESSION['Auth']['User']['client_id'])));
        $this->set(compact('users'));
    }

    public function transferencias() {
        if ($this->request->is('post') && isset($this->request->data['Caja']['destinos'])) {
            $resul = $this->Caja->saveTransferencia($this->request->data);
            if ($resul == "") {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(h($resul));
            }
        }

        $cajas = $this->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.user_id' => $_SESSION['Auth']['User']['id']), 'recursive' => 0));
        if (count($cajas) == 0) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar una transferencia'));
            return $this->redirect(['action' => 'index']);
        }

        reset($cajas);
        $cajaid = key($cajas); // para obtener el id de la caja

        $idsCajasUsuariosCeOnline = [];
        if (!in_array($_SESSION['Auth']['User']['username'], ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster'])) {
            $cajasUsuariosCeOnline = $this->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'User.username' => ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster']), 'recursive' => 0));
            $idsCajasUsuariosCeOnline = array_keys($cajasUsuariosCeOnline);
        }

        $destinos = $this->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id !=' => array_merge([$cajaid], $idsCajasUsuariosCeOnline))));
        $cheques = $this->Caja->Cheque->getChequesListosParaEntregar();
        $saldo = $this->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $cajaid), 'fields' => 'Caja.saldo_pesos'));
        $this->set(compact('cajas', 'destinos', 'cheques', 'saldo', 'cajaid'));
    }

    public function delete($id = null) {
        $this->Caja->id = $id;
        if (!$this->Caja->exists() || $this->Caja->find('count', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Caja->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen movimientos o saldos asociados a la caja, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
