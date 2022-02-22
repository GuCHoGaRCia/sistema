<?php

App::uses('AppController', 'Controller');

class BancostransferenciasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Bancostransferencia->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Bancostransferencia->parseCriteria($this->passedArgs)),
            'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Bancoscuenta.consorcio_id')),
                array('table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => array('Banco.id=Bancoscuenta.banco_id'))),
            'order' => 'Bancostransferencia.fecha desc');
        $this->Prg->commonProcess();
        $this->set('consorcios', $this->Bancostransferencia->Bancoscuenta->Consorcio->getConsorciosList()); // para q no muestre los deshabilitados
        $this->set('bancostransferencias', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            if (!$this->Bancostransferencia->Bancoscuenta->canEdit($this->request->data['Bancostransferencia']['bancoscuenta_id']) || !$this->Bancostransferencia->Bancoscuenta->canEdit($this->request->data['Bancostransferencia']['destino_id'])) {
                $this->Flash->error(__('El dato es inexistente'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Bancostransferencia->create();
            if ($this->Bancostransferencia->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Las cuentas or&iacute;gen y destino no pueden ser las mismas, y la cuenta or&iacute;gen debe tener saldo disponible'));
            }
        }
        $bancoscuentas = $this->Bancostransferencia->Bancoscuenta->get();
        if (count($bancoscuentas) == 0) {
            $this->Flash->error(__('Debe crear un Banco (men&uacute Bancos) antes de agregar una transferencia'));
            return $this->redirect(['action' => 'index']);
        }
        $destinos = $this->Bancostransferencia->Bancoscuenta->get();
        $this->set(compact('bancoscuentas', 'destinos'));
    }

    public function delete($id = null) {
        if (!$this->Bancostransferencia->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Bancostransferencia->id = $id;
        if ($this->Bancostransferencia->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El movimiento fue anulado'));
        } else {
            $this->Flash->error(__('El movimiento no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
