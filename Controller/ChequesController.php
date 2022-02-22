<?php

App::uses('AppController', 'Controller');

class ChequesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getChequesPendientes', 'agregar'); // permito blackhole x ajax
    }

    public function index() {
        $this->Cheque->recursive = 0;
        $conditions = ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.depositado' => 0, 'Cheque.proveedorspago_id' => 0, $this->Cheque->parseCriteria($this->passedArgs)];
        $conditions += isset($this->request->data['Cheque']['consorcio']) && !empty($this->request->data['Cheque']['consorcio']) ? ['Propietario.consorcio_id' => $this->request->data['Cheque']['consorcio']] : [];
        $conditions += isset($this->request->data['Cheque']['anulado']) && $this->request->data['Cheque']['anulado'] == '1' ? [] : ['Cheque.anulado' => 0];
        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0, 'order' => 'Cheque.fecha_vencimiento desc,Cheque.created desc',
            'fields' => ['DISTINCT Cheque.id', 'Cheque.fecha_emision', 'Cheque.fecha_vencimiento', 'Cheque.concepto', 'Cheque.importe', 'Cheque.banconumero', 'Cheque.depositado', 'Cheque.anulado', 'Cheque.saldo', 'Cheque.fisico', 'Caja.user_id', 'Caja.name', 'Propietario.consorcio_id'],
            'joins' => [['table' => 'cobranzacheques', 'alias' => 'Cobranzacheque', 'type' => 'left', 'conditions' => ['Cobranzacheque.cheque_id=Cheque.id']],
                ['table' => 'cobranzas', 'alias' => 'Cobranza', 'type' => 'left', 'conditions' => ['Cobranzacheque.cobranza_id=Cobranza.id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cobranza.propietario_id=Propietario.id']]]
        ];
        if (!isset($this->request->data['Cheque']) || empty($this->request->data['Cheque'])) {
            $this->Paginator->settings += ['limit' => 15];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $cheques = $this->paginar($this->Paginator);
        $this->set('cheques', $cheques);
        $this->set('consorcios', $this->Cheque->Client->Consorcio->getConsorciosList($_SESSION['Auth']['User']['client_id']));
        $sepuedeanular = [];
        foreach (Set::extract('/Cheque/id', $cheques) as $k => $v) {
            $sepuedeanular[$v] = $this->Cheque->sePuedeAnular($v);
        }
        $this->set('sepuedeanular', $sepuedeanular);
    }

    public function view($id = null, $sinlayout = 0) {
        if (!$this->Cheque->exists($id) || $this->Cheque->find('count', array('conditions' => array('Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('cheque', $this->Cheque->find('first', ['conditions' => ['Cheque.id' => $id]]));
        $this->set('users', $this->Cheque->Client->Caja->User->find('list', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id']]]));
        $this->set('movimientos', $this->Cheque->getMovimientosCheque($id));
        $this->set('sinlayout', $sinlayout);
        $cajas = $this->Cheque->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'])));
        $this->set(compact('cajas'));
        if ($sinlayout == 1) {
            $this->layout = '';
        }
    }

    /*
     * Son los Cheques depositados en una cuenta bancaria
     */

    public function depositados() {
        $conditions = ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.depositado !=' => 0, 'Consorcio.habilitado' => 1, $this->Cheque->parseCriteria($this->passedArgs)];
        $this->Cheque->recursive = 0;

        if (isset($this->request->data['filter']['cuenta']) && $this->request->data['filter']['cuenta'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['cuenta'])) {
            $conditions += ['Bancosdepositoscheque.bancoscuenta_id' => $this->request->data['filter']['cuenta']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => [['table' => 'bancosdepositoscheques', 'alias' => 'Bancosdepositoscheque', 'type' => 'left', 'conditions' => ['Bancosdepositoscheque.cheque_id=Cheque.id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Bancosdepositoscheque.bancoscuenta_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Bancoscuenta.consorcio_id']]],
            'fields' => ['Cheque.*', 'Bancosdepositoscheque.concepto', 'Bancosdepositoscheque.fecha', 'Caja.name', 'Bancosdepositoscheque.anulado', 'Bancoscuenta.name', 'Consorcio.name'],
            'order' => 'Bancosdepositoscheque.fecha desc,Cheque.fecha_vencimiento desc,Cheque.created desc');

        if (!isset($this->request->data['filter']['cuenta'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $cuentasBancarias = $this->Cheque->Bancosdepositoscheque->Bancoscuenta->get();
        $this->set(compact('cuentasBancarias'));
        $this->set('cheques', $this->paginar($this->Paginator));
    }

    /*
     * Son los cheques que fueron utilizados en pago proveedores (entregados al proveedor)
     */

    public function entregados() {
        $d = isset($this->request->data['Cheque']['desde']) ? $this->request->data['Cheque']['desde'] : date("01/m/Y", strtotime("-3 months"));
        $h = isset($this->request->data['Cheque']['hasta']) ? $this->request->data['Cheque']['hasta'] : date("d/m/Y");
        $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.depositado' => 0, 'Cheque.proveedorspago_id !=' => 0];
        $conditions += isset($this->request->data['Cheque']['proveedor_id']) && $this->request->data['Cheque']['proveedor_id'] !== '' ? ['Proveedor.id' => $this->request->data['Cheque']['proveedor_id']] : [];
        $conditions += isset($this->request->data['Cheque']['anulado']) && $this->request->data['Cheque']['anulado'] == 1 ? [] : ['Cheque.anulado' => 0];
        $conditions += !empty($d) ? ['date(Cheque.fecha_vencimiento) >=' => $this->Cheque->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Cheque.fecha_vencimiento) <=' => $this->Cheque->fecha($h)] : [];
        $this->Paginator->settings = array('conditions' => $conditions,
            'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Cheque.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorspago.proveedor_id=Proveedor.id']]],
            'fields' => ['Cheque.id', 'Cheque.proveedorspago_id', 'Cheque.fecha_emision', 'Cheque.fecha_vencimiento', 'Cheque.concepto', 'Cheque.importe', 'Cheque.banconumero', 'Cheque.anulado', 'Proveedor.name'],
            'order' => 'Cheque.fecha_vencimiento desc,Cheque.created desc');
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('proveedors', $this->Cheque->Client->Proveedor->getList());
        $this->set('cheques', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Cheque->create();
            if ($this->Cheque->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $cajas = $this->Cheque->Caja->find('list', array('conditions' => array('Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $this->Cheque->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']))));
        if (count($cajas) == 0) {
            $this->Flash->error(__('Debe crear una Caja (men&uacute Cajas) antes de agregar un cheque'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('cajas'));
    }

    public function delete($id = null) {
        $this->Cheque->id = $id;
        if (!$this->Cheque->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if (!$this->Cheque->sePuedeAnular($id)) {
            // fue usado el cheque, no puedo anularlo
            $this->Flash->error(__('El cheque se encuentra en uso (el saldo no es igual al importe), no se puede anular'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Cheque->undo($this->request->params['pass'][0])) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function agregar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Cheque->agregar($this->request->data)));
    }

}
