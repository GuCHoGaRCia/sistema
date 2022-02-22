<?php

App::uses('AppController', 'Controller');

class ProveedorsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getSaldosProveedor'); // permito blackhole x ajax
    }

    public function index() {
        $this->Proveedor->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Proveedor->parseCriteria($this->passedArgs)),
            'fields' => ['Proveedor.id', 'Proveedor.name', 'Proveedor.nombrefantasia', 'Proveedor.address', 'Proveedor.city', 'Proveedor.email', 'Proveedor.matricula', 'Proveedor.telephone', 'Proveedor.cuit', 'Proveedor.saldo'],
            'order' => 'Proveedor.name');
        $this->Prg->commonProcess();
        $this->set('proveedors', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Proveedor->create();
            if ($this->Proveedor->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function view($id = null) {
        if (!$this->Proveedor->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            $this->set('movimientos', $this->Proveedor->getMovimientosProveedor(isset($this->request->data['Proveedor']['proveedores']) ? $this->request->data['Proveedor']['proveedores'] : $id, $this->request->data['Proveedor']['desde'], $this->request->data['Proveedor']['hasta'], $this->request->data['Proveedor']['incluye_pagas'], isset($this->request->data['Proveedor']['consorcios']) ? $this->request->data['Proveedor']['consorcios'] : null));
        } else {
            $this->set('movimientos', $this->Proveedor->getMovimientosProveedor($id));
        }
        $options = ['conditions' => ['Proveedor.id' => isset($this->request->data['Proveedor']['proveedores']) ? $this->request->data['Proveedor']['proveedores'] : $id], 'recursive' => 0];
        $this->set('p', $this->Proveedor->find('first', $options));
        $this->set('cuentasbancarias', $this->Proveedor->Client->Banco->Bancoscuenta->get()); //obtengo todas las cuentas bancarias del cliente (porq si no hay facturas, puedo hacer un pago a cuenta)
        $this->set('proveedores', $this->Proveedor->getList());
        $this->set('consorcios', $this->Proveedor->Client->Consorcio->getConsorciosList());
    }

    public function delete($id = null) {
        $this->Proveedor->id = $id;
        if (!$this->Proveedor->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Proveedor->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen Facturas o Pagos asociados al Proveedor, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /*
     * Utilizada para obtener los proveedores q coincidan con el texto ingresado en pago proveedores (nombre, cuit)
     */

    function get() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Proveedor->get($this->request->query['q'])));
    }

    /*
     * Devuelve el saldo del Proveedor para el pago Proveedores
     * $this->request->data['p'] -> busca por el id del Proveedor
     */

    public function getSaldosProveedor() {
        if (!$this->request->is('ajax') || empty($this->request->data['p'])) {
            die();
        }
        if (!$this->Proveedor->canEdit($this->request->data['p'])) {
            die();
        }

        $this->set('proveedor', $this->Proveedor->find('first', ['conditions' => ['Proveedor.id' => $this->request->data['p']], 'fields' => ['Proveedor.name', 'Proveedor.id', 'Proveedor.saldo']]));
        $this->set('consorcio_id', $this->Proveedor->Client->Consorcio->getConsorciosList());
        $this->set('chequespropios', $this->Proveedor->Client->Chequespropio->getChequesPendientes());
        $this->set('chequespropiosadm', $this->Proveedor->Client->Chequespropiosadm->getChequesPendientes());
        $this->set('chequesterceros', $this->Proveedor->Client->Cheque->getChequesListosParaEntregar());
        $this->set('caja', $this->Proveedor->Client->Caja->find('first', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.user_id' => $_SESSION['Auth']['User']['id']], 'fields' => ['Caja.name', 'Caja.saldo_pesos']]));
        $this->set('bancoscuentas', $this->Proveedor->Client->Banco->Bancoscuenta->find('all', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'OR' => ['Consorcio.habilitado' => 1, 'Bancoscuenta.consorcio_id' => 0]], 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.consorcio_id', 'Bancoscuenta.name2', 'Bancoscuenta.saldo'], 'recursive' => 0, 'order' => 'Bancoscuenta.name2']));
        $this->set('bancoscuenta_id', $this->Proveedor->Client->Banco->Bancoscuenta->get()); //obtengo todas las cuentas bancarias del cliente (porq si no hay facturas, puedo hacer un pago a cuenta)
        $this->set('pagosacuentaparaaplicar', $this->Proveedor->Proveedorspago->Proveedorspagosacuenta->getPagosParaAplicar($this->request->data['p'], null, 0)); // null->consor, 0->no incluir aplicados
        $this->set('notasdecreditoaaplicar', $this->Proveedor->Proveedorspago->Proveedorspagosnc->getNCParaAplicar($this->request->data['p']));
        $this->layout = '';
        $seccion = 'Proveedors';
        if (isset($this->request->data['f'])) {
            $seccion = $this->request->data['f'];
        }
        $this->set('seccion', $seccion);
        $this->render("/$seccion/agregarcobranza");
    }

    public function panel_actualizaSaldosProveedor() {
        $this->Proveedor->actualizaSaldosProveedor();
        $this->Flash->success(__('Se actualizaron los Saldos de TODOS los Proveedores del Sistema'));
        return $this->redirect(['controller' => 'clients', 'action' => 'procesos']);
    }

}
