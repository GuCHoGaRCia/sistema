<?php

App::uses('AppController', 'Controller');

class BancoscuentasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Prg->commonProcess();
        $this->set('bancoscuentas', $this->Bancoscuenta->find('all', array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'], ['OR' => ['Consorcio.habilitado' => 1, 'Bancoscuenta.consorcio_id' => 0]],
                        $this->Bancoscuenta->parseCriteria($this->passedArgs)),
                    'contain' => ['Banco', 'Consorcio'], 'fields' => ['Banco.id', 'Banco.name', 'Consorcio.id', 'Consorcio.name', 'Bancoscuenta.cbu',
                        'Bancoscuenta.cuenta', 'Bancoscuenta.name', 'Bancoscuenta.saldo', 'Bancoscuenta.consorcio_id', 'Bancoscuenta.cgp_comision', 'Bancoscuenta.comision_fija_interdeposito',
                        'Bancoscuenta.comision_variable', 'Bancoscuenta.id', 'Bancoscuenta.defectocobranzaautomatica', 'Bancoscuenta.habilitada'],
                    'order' => 'Consorcio.code')));
        $gp = [];
        foreach ($this->Bancoscuenta->Banco->Client->Consorcio->getConsorciosList() as $k => $v) {
            $gp[$k] = $this->Bancoscuenta->Banco->Client->Consorcio->Cuentasgastosparticulare->getCuentasInfo($k);
        }
        $this->set('gp', $gp);
    }

    public function view($id = null) {
        if (!$this->Bancoscuenta->exists($id) || $this->Bancoscuenta->find('count', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $id], 'recursive' => 0]) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            //$d = $this->Bancoscuenta->fecha($this->request->data['Bancoscuenta']['desde']);
            $d = $this->request->data['Bancoscuenta']['desde'];
            $h = $this->request->data['Bancoscuenta']['hasta'];

            if (!$this->Bancoscuenta->validateDate($this->Bancoscuenta->fecha($d), 'Y-m-d') || !$this->Bancoscuenta->validateDate($this->Bancoscuenta->fecha($h), 'Y-m-d') || !$this->Bancoscuenta->fechaEsMenorIgualQue($this->Bancoscuenta->fecha($d), $this->Bancoscuenta->fecha($h))) {
                $this->Flash->error(__('Las fechas son incorrectas'));
                return $this->redirect(['action' => 'index']);
            }
            $this->set('movimientos', $this->Bancoscuenta->getMovimientos(isset($this->request->data['Bancoscuenta']['cuentas']) ? $this->request->data['Bancoscuenta']['cuentas'] : $id, $d, $h, $this->request->data['Bancoscuenta']['incluye_anulados']));
            //$this->set('saldoanterior', $this->Bancoscuenta->Banco->Client->Saldoscajabanco->getSaldos($k, date("Y-m-d", strtotime($this->Bancoscuenta->fecha($d) . " -1 day"))));
        }

        $options = ['conditions' => ['Bancoscuenta.id' => isset($this->request->data['Bancoscuenta']['cuentas']) ? $this->request->data['Bancoscuenta']['cuentas'] : $id], 'recursive' => 0, 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.name', 'Bancoscuenta.cuenta', 'Bancoscuenta.saldo']];
        $this->set('c', $this->Bancoscuenta->find('first', $options));
        $this->set('formasdepago', $this->Bancoscuenta->Banco->Client->Formasdepago->get());
        $this->set('cuentas', $this->Bancoscuenta->get());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Bancoscuenta->create();
            if ($this->Bancoscuenta->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = [0 => '00 - Administración'] + $this->Bancoscuenta->Consorcio->getConsorciosList();
        if (count($consorcios) == 0) {
            $this->Flash->error(__('Debe crear un Consorcio (men&uacute Datos) antes de agregar una cuenta'));
            return $this->redirect(['action' => 'index']);
        }
        $bancos = $this->Bancoscuenta->Banco->find('list', array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'])));
        if (count($bancos) == 0) {
            $this->Flash->error(__('Cree aqu&iacute; un Banco antes de agregar una Cuenta Bancaria'));
            return $this->redirect(['controller' => 'bancos', 'action' => 'add']);
        }

        $this->set(compact('bancos', 'consorcios'));
    }

    public function recuperos() {
        $conditions = ['Banco.client_id' => $_SESSION['Auth']['User']['client_id']];
        $b = isset($this->request->data['Bancoscuenta']['bancoscuenta_id']) ? $this->request->data['Bancoscuenta']['bancoscuenta_id'] : '';
        $d = isset($this->request->data['Bancoscuenta']['desde']) ? $this->request->data['Bancoscuenta']['desde'] : date("01/m/Y");
        $h = isset($this->request->data['Bancoscuenta']['hasta']) ? $this->request->data['Bancoscuenta']['hasta'] : date("d/m/Y");
        $recuperados = isset($this->request->data['Bancoscuenta']['incluirrecuperados']) ? $this->request->data['Bancoscuenta']['incluirrecuperados'] : 0;

        $conditions += !empty($b) ? ['Proveedor.id' => $b] : [];
        if ($this->request->is('post')) {
            $this->set('movimientos', $this->Bancoscuenta->getMovimientosAdministracion($b, $d, $h, $recuperados));
        }
        $this->set('b', $b);
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('r', $recuperados);
        $this->set('consor', $this->Bancoscuenta->Consorcio->getConsorciosList());
        $this->set('bancoscuentas', $this->Bancoscuenta->get());
        $this->set('ctaconsor', $this->Bancoscuenta->getCtaConsor());
        $this->set('bancoscuentasadms', $this->Bancoscuenta->getCuentasAdm());
    }

    public function cambiaCADefecto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Bancoscuenta->cambiaCADefecto($this->request->params['pass'])));
    }

    public function delete($id = null) {
        $this->Bancoscuenta->id = $id;
        if (!$this->Bancoscuenta->exists() || $this->Bancoscuenta->find('count', array('conditions' => array('Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $id), 'recursive' => 0)) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Bancoscuenta->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen movimientos asociados a la cuenta bancaria, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
