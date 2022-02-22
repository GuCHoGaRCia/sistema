<?php

App::uses('AppController', 'Controller');

class ChequespropiosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1];
        $d = isset($this->request->data['Chequespropio']['desde']) ? $this->request->data['Chequespropio']['desde'] : '';
        $h = isset($this->request->data['Chequespropio']['hasta']) ? $this->request->data['Chequespropio']['hasta'] : '';
        $b = isset($this->request->data['Chequespropio']['buscar']) ? $this->request->data['Chequespropio']['buscar'] : '';
        $conditions += !empty($b) ? ['OR' => ['Chequespropio.numero' => $b, 'Chequespropio.numero like' => '%' . $b . '%']] : [];
        $conditions += isset($this->request->data['Chequespropio']['cuenta']) && $this->request->data['Chequespropio']['cuenta'] !== '0' ? ['Bancoscuenta.id' => $this->request->data['Chequespropio']['cuenta']] : [];
        $conditions += isset($this->request->data['Chequespropio']['anulado']) && $this->request->data['Chequespropio']['anulado'] == '1' ? [] : ['Chequespropio.anulado' => 0];
        $conditions += !empty($d) ? ['date(Chequespropio.created) >=' => $this->Chequespropio->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Chequespropio.created) <=' => $this->Chequespropio->fecha($h)] : [];
        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0, 'order' => 'Chequespropio.id desc',
            'fields' => ['Chequespropio.id', 'Chequespropio.fecha_emision', 'Chequespropio.fecha_vencimiento', 'Chequespropio.concepto', 'Chequespropio.importe', 'Chequespropio.numero', 'Chequespropio.proveedorspago_id', 'Chequespropio.anulado', 'Bancoscuenta.name', 'User.name'],
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Bancoscuenta.consorcio_id']],
                ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Chequespropio.user_id']]],
            'contain' => ['Bancoscuenta']
        ];
        if (!isset($this->request->data['Chequespropio']) || empty($this->request->data['Chequespropio'])) {
            $this->Paginator->settings += ['limit' => 10];
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('b', $b);
        $this->set('chequespropios', $this->paginar($this->Paginator));
        $this->set('consorcio_id', $this->Chequespropio->Client->Consorcio->getConsorciosList());
        $this->set('cuentas', $this->Chequespropio->Client->Banco->Bancoscuenta->get());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Chequespropio->create();
            if ($this->Chequespropio->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $this->set('bancoscuentas', $this->Chequespropio->Client->Banco->Bancoscuenta->get());
    }

    public function delete($id = null) {
        $this->Chequespropio->id = $id;
        if (!$this->Chequespropio->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Chequespropio->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function delChequepropio() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropio->undo($this->request->data['id'])));
    }

    public function agregar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropio->agregar($this->request->data)));
    }

    public function getChequesPendientes() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Chequespropio->getChequesPendientes()));
    }

}
