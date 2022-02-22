<?php

App::uses('AppController', 'Controller');

class ProveedorspagosacuentasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.anulado' => 0];
        $p = isset($this->request->data['Proveedorspagosacuenta']['proveedor_id']) ? $this->request->data['Proveedorspagosacuenta']['proveedor_id'] : '';
        $d = isset($this->request->data['Proveedorspagosacuenta']['desde']) ? $this->request->data['Proveedorspagosacuenta']['desde'] : '';
        $h = isset($this->request->data['Proveedorspagosacuenta']['hasta']) ? $this->request->data['Proveedorspagosacuenta']['hasta'] : '';
        $c = isset($this->request->data['Proveedorspagosacuenta']['consorcio_id']) ? $this->request->data['Proveedorspagosacuenta']['consorcio_id'] : '';
        $s = isset($this->request->data['Proveedorspagosacuenta']['incluiraplicados']) ? $this->request->data['Proveedorspagosacuenta']['incluiraplicados'] : 0;
        $n = isset($this->request->data['Proveedorspagosacuenta']['buscar']) ? $this->request->data['Proveedorspagosacuenta']['buscar'] : null;
        if ($this->request->is('post') && (!empty($p) || !empty($c) || !empty($s)) && empty($this->request->data['Proveedorspagosacuenta']['buscar'])) {
            $this->set('proveedorspagosacuentas', $this->Proveedorspagosacuenta->getPagosParaAplicar($p, $c, $s, $d, $h));  //$this->Proveedorspagosacuenta->fecha($d),$this->Proveedorspagosacuenta->fecha($h)
        } else {
            $conditions += !empty($n) ? ['Proveedorspago.numero' => $n] : [];
            $conditions += ($s == '0' ? ['Proveedorspagosacuenta.proveedorspagoaplicado_id' => 0] : []);
//            $conditions += !empty($d) ? ['date(Proveedorspago.fecha) >=' => $this->Proveedorspagosacuenta->fecha($d)] : [];
//            $conditions += !empty($h) ? ['date(Proveedorspago.fecha) <=' => $this->Proveedorspagosacuenta->fecha($h)] : [];
            $this->Paginator->settings = ['conditions' => $conditions,
                'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspagosacuenta.proveedorspago_id=Proveedorspago.id']],
                    ['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']]],
                /* lo saco porq si no es POST no se necesita (no se muestran estos datos) ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                  ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                  ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']]], */
                'fields' => ['Proveedor.name', 'Proveedorspagosacuenta.id', 'Proveedorspagosacuenta.importe', 'Proveedorspagosacuenta.proveedorspagoaplicado_id', 'Proveedorspagosacuenta.consorcio_id', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspago.numero', 'Proveedorspago.id'],
                'order' => 'Proveedorspago.fecha desc'
            ];
            $this->set('proveedorspagosacuentas', $this->paginar($this->Paginator));
        }

        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('p', $p);
        $this->set('c', $c);
        $this->set('proveedores', $this->Proveedorspagosacuenta->Proveedorspago->Proveedor->getList());
        $this->set('consorcios', $this->Proveedorspagosacuenta->Proveedorspago->Proveedor->Client->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Proveedorspagosacuenta->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Proveedorspagosacuenta.' . $this->Proveedorspagosacuenta->primaryKey => $id]];
        $this->set('proveedorspagosacuenta', $this->Proveedorspagosacuenta->find('first', $options));
    }

    /* public function add() {
      if ($this->request->is('post')) {
      $this->Proveedorspagosacuenta->create();
      if ($this->Proveedorspagosacuenta->save($this->request->data)) {
      $this->Flash->success(__('El dato fue guardado'));
      return $this->redirect(['action' => 'index']);
      } else {
      $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
      }
      }
      }

      public function edit($id = null) {
      if (!$this->Proveedorspagosacuenta->exists($id)) {
      $this->Flash->error(__('El dato es inexistente'));
      return $this->redirect(['action' => 'index']);
      }
      if ($this->request->is(['post', 'put'])) {
      if ($this->Proveedorspagosacuenta->save($this->request->data)) {
      $this->Flash->success(__('El dato fue guardado'));
      return $this->redirect(['action' => 'index']);
      } else {
      $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
      }
      } else {
      $options = ['conditions' => ['Proveedorspagosacuenta.' . $this->Proveedorspagosacuenta->primaryKey => $id]];
      $this->request->data = $this->Proveedorspagosacuenta->find('first', $options);
      }
      }

      public function delete($id = null) {
      $this->Proveedorspagosacuenta->id = $id;
      if (!$this->Proveedorspagosacuenta->exists()) {
      $this->Flash->error(__('El dato es inexistente'));
      return $this->redirect(['action' => 'index']);
      }
      $this->request->allowMethod('post', 'delete');
      if ($this->Proveedorspagosacuenta->delete()) {
      $this->Flash->success(__('El dato fue eliminado'));
      } else {
      $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
      }
      return $this->redirect($this->referer());
      } */
}
