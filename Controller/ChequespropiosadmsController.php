<?php

App::uses('AppController', 'Controller');

class ChequespropiosadmsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Chequespropiosadm.client_id' => $_SESSION['Auth']['User']['client_id']/* , $this->Chequespropiosadm->parseCriteria($this->passedArgs) */];
        $d = isset($this->request->data['Chequespropiosadm']['desde']) ? $this->request->data['Chequespropiosadm']['desde'] : date("01/m/Y");
        $h = isset($this->request->data['Chequespropiosadm']['hasta']) ? $this->request->data['Chequespropiosadm']['hasta'] : date("d/m/Y");
        $conditions += isset($this->request->data['Chequespropiosadm']['consorcio_id']) && $this->request->data['Chequespropiosadm']['consorcio_id'] !== '' ? ['Bancoscuenta.consorcio_id' => $this->request->data['Chequespropiosadm']['consorcio_id']] : [];
        $conditions += isset($this->request->data['Chequespropiosadm']['anulado']) && $this->request->data['Chequespropiosadm']['anulado'] == 1 ? [] : ['Chequespropiosadm.anulado' => 0];
        $conditions += !empty($d) ? ['date(Chequespropiosadm.created) >=' => $this->Chequespropiosadm->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Chequespropiosadm.created) <=' => $this->Chequespropiosadm->fecha($h)] : [];
        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0, 'order' => 'Chequespropiosadm.id desc', 'contain' => ['Chequespropiosadmsdetalle', 'User'],
            'fields' => ['DISTINCT Chequespropiosadm.id', 'Chequespropiosadm.fecha_emision', 'Chequespropiosadm.fecha_vencimiento', 'Chequespropiosadm.concepto', 'Chequespropiosadm.numero', 'Chequespropiosadm.anulado', 'Chequespropiosadm.importe', 'User.name'],
            'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Chequespropiosadmsdetalle.bancoscuenta_id']]],
        ];
        if (!isset($this->request->data['Chequespropiosadm']) || empty($this->request->data['Chequespropiosadm'])) {
            $this->Paginator->settings += ['limit' => 10];
            //$this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }

        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('chequespropios', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Chequespropiosadm->Client->Consorcio->getConsorciosList());
        $this->set('cuentas', $this->Chequespropiosadm->Client->Banco->Bancoscuenta->get());
    }

    /* public function getInfo($id) {
      $this->layout = '';
      debug($this->Chequespropiosadm->getInfo($id));
      die;
      } */

    public function view($id = null) {
        if (!$this->Chequespropiosadm->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Chequespropiosadm.' . $this->Chequespropiosadm->primaryKey => $id]];
        $this->set('chequespropiosadm', $this->Chequespropiosadm->find('first', $options));
    }

    public function delChequepropio() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropiosadm->undo($this->request->data['id'])));
    }

    public function agregar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropiosadm->agregar($this->request->data)));
    }

    public function getChequesPendientes() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropiosadm->getChequesPendientes()));
    }

    public function delete($id = null) {
        $this->Chequespropiosadm->id = $id;
        if (!$this->Chequespropiosadm->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Chequespropiosadm->undo($id)) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
