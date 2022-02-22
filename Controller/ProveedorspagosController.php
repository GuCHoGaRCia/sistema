<?php

App::uses('AppController', 'Controller');

class ProveedorspagosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'add');
    }

    public function index() {
        $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']];
        $px = isset($this->request->data['Proveedorspago']['proveedor_id']) ? $this->request->data['Proveedorspago']['proveedor_id'] : '';
        $c = isset($this->request->data['Proveedorspago']['consorcio_id']) ? $this->request->data['Proveedorspago']['consorcio_id'] : '';
        $anulados = isset($this->request->data['Proveedorspago']['incluiranulados']) ? $this->request->data['Proveedorspago']['incluiranulados'] : 0;
        $d = isset($this->request->data['Proveedorspago']['desde']) ? $this->request->data['Proveedorspago']['desde'] : date("01/m/Y");
        $h = isset($this->request->data['Proveedorspago']['hasta']) ? $this->request->data['Proveedorspago']['hasta'] : date("d/m/Y");

        $conditions += !empty($px) ? ['Proveedor.id' => $px] : [];
        $conditions += !empty($c) ? ['Liquidation.consorcio_id' => $c] : [];
        if ($this->request->is('post')) {
            $this->set('proveedorspagos', $this->Proveedorspago->getPagos($px, $c, $d, $h, $anulados));
        } else {
            $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.anulado' => 0,
                    //'date(Proveedorspago.created) >=' => $this->Proveedorspago->fecha($d), 'date(Proveedorspago.created) <=' => $this->Proveedorspago->fecha($h)            
            ];
            $this->Paginator->settings = ['conditions' => $conditions,
                'contain' => ['Proveedor', 'Proveedorspagosacuenta', 'Proveedorspagosfactura.Proveedorsfactura.Liquidation.consorcio_id', 'Cajasegreso.consorcio_id', 'Bancosextraccione.consorcio_id', 'Chequespropio.bancoscuenta_id'],
                'fields' => ['DISTINCT Proveedorspago.id', 'Proveedorspago.numero', 'Proveedor.name', 'Proveedor.id', 'Proveedorspago.concepto', 'Proveedorspago.user_id', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspago.anulado', 'Proveedorspago.modified',
                    'Proveedorspago.importe', 'Proveedorspago.tipo', 'Proveedorspago.user_id', /* 'Liquidation.consorcio_id', */ /* 'Cajasegreso.consorcio_id', 'Bancosextraccione.consorcio_id', */ /* 'Chequespropio.bancoscuenta_id' */],
                'joins' => [//['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                //['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                //['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                // para pago a cuenta
                //['table' => 'cajasegresos', 'alias' => 'Cajasegreso', 'type' => 'left', 'conditions' => ['Cajasegreso.proveedorspago_id=Proveedorspago.id']],
                //['table' => 'bancosextracciones', 'alias' => 'Bancosextraccione', 'type' => 'left', 'conditions' => ['Bancosextraccione.proveedorspago_id=Proveedorspago.id']],
                //['table' => 'chequespropios', 'alias' => 'Chequespropio', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']],
                //['table' => 'proveedorspagosacuentas', 'alias' => 'Proveedorspagosacuenta', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspago_id']],
                ],
                //'contain' =>[],
                //'group' => 'Proveedorspago.id',
                'order' => 'Proveedorspago.fecha desc,Proveedorspago.id desc',
                'limit' => 15];
            $this->set('proveedorspagos', $this->paginar($this->Paginator));
        }
        $this->set('pac', $this->Proveedorspago->Proveedorspagosacuenta->getPagosAplicados());
        $this->set('px', $px);
        $this->set('c', $c);
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('a', $anulados);
        $this->set('users', $this->Proveedorspago->User->getList());
        $this->set('proveedores', $this->Proveedorspago->Proveedor->getList());
        $this->set('bancoscuentas', $this->Proveedorspago->Proveedor->Client->Banco->Bancoscuenta->get());
        $this->set('consorcios', $this->Proveedorspago->Proveedorspagosfactura->Proveedorsfactura->Liquidation->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Proveedorspago->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $resul = $this->Proveedorspago->getDetalleFormaPago($id);
        $this->set(compact('resul'));
        $this->set('users', $this->Proveedorspago->Proveedor->Client->User->getList());
        $this->set('consorcios', $this->Proveedorspago->Proveedor->Client->Consorcio->getConsorciosList());
        $this->layout = '';
    }

    public function view2($id = null) {
        if (!$this->Proveedorspago->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Proveedorspago.id' => $id], 'recursive' => 1, 'contain' => ['Proveedor', 'User', 'Chequespropio', 'Proveedorspagosacuenta', 'Proveedorspagoscheque'],
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Proveedor.client_id']]]
        ];
        $proveedorspago = $this->Proveedorspago->find('first', $options);
        $ids = $this->Proveedorspago->find('all', ['conditions' => ['Proveedorspago.numero' => $proveedorspago['Proveedorspago']['numero'], 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'fields' => ['Proveedorspago.id']]);
        $facturas = $pagosacuenta = $cheques = $chequespropios = $chequespropiosadm = $efectivo = $efectivoadm = $transferencia = $transferenciaadm = $pagosacuentaaplicados = $notasdecreditoaplicadas = [];
        foreach ($ids as $v) {
            $id = $v['Proveedorspago']['id'];
            $facturas[] = $this->Proveedorspago->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorspago_id' => $id], 'fields' => ['Proveedorsfactura.*', 'Consorcio.name', 'Liquidation.periodo', 'Proveedorspagosfactura.importe'],
                'joins' => [['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                    ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                    ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Liquidation.consorcio_id=Consorcio.id']]]]);
            $pc = $this->Proveedorspago->Proveedorspagosacuenta->find('first', ['conditions' => ['Proveedorspagosacuenta.proveedorspago_id' => $id]]);
            if (!empty($pc)) {
                $pagosacuenta[] = $pc;
            }
            $cheques += Hash::combine($this->Proveedorspago->Proveedorspagoscheque->find('all', ['conditions' => ['Proveedorspagoscheque.proveedorspago_id' => $id], 'recursive' => -1, 'contain' => ['Cheque']]), '{n}.Cheque.id', '{n}.Cheque');

            $chequespropios[] = Hash::combine($this->Proveedorspago->Proveedor->Client->Chequespropio->find('all', ['conditions' => ['Chequespropio.proveedorspago_id' => $id], 'recursive' => -1]), '{n}.Chequespropio.id', '{n}.Chequespropio');
            $chequespropiosadm[] = $this->Proveedorspago->Proveedor->Client->Chequespropiosadm->Chequespropiosadmsdetalle->find('all', ['conditions' => ['Chequespropiosadmsdetalle.proveedorspago_id' => $id], 'recursive' => 0, 'contain' => ['Chequespropiosadm']]);
            $efectivo[] = $this->Proveedorspago->Proveedor->Client->Caja->Cajasegreso->find('first', ['conditions' => ['Cajasegreso.proveedorspago_id' => $id], 'recursive' => 0, 'fields' => ['Cajasegreso.importe', 'Cajasegreso.concepto', 'Cajasegreso.consorcio_id']]);
            $x = $this->Proveedorspago->Administracionefectivo->find('all', ['conditions' => ['Administracionefectivo.proveedorspago_id' => $id], 'contain' => ['Administracionefectivosdetalle'],
                'group' => ['Administracionefectivo.bancoscuenta_id'],
                'joins' => [['table' => 'administracionefectivosdetalles', 'alias' => 'Administracionefectivosdetalle', 'type' => 'left', 'conditions' => ['Administracionefectivo.id=Administracionefectivosdetalle.administracionefectivo_id']]]]);
            if (!empty($x)) {
                $efectivoadm[] = $x;
            }

            $transferencia[] = $this->Proveedorspago->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->find('all', ['conditions' => ['Bancosextraccione.proveedorspago_id' => $id], 'recursive' => 0,
                //'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'right', 'conditions' => ['Bancosextraccione.proveedorspago_id=Chequespropiosadmsdetalle.proveedorspago_id']]],
                'fields' => ['Bancoscuenta.name', 'Bancoscuenta.id', 'Bancosextraccione.importe', 'Bancosextraccione.consorcio_id'/* ,'Chequespropiosadmsdetalle.*' */]]);
            $x = $this->Proveedorspago->Administraciontransferencia->find('all', ['conditions' => ['Administraciontransferencia.proveedorspago_id' => $id], 'contain' => ['Administraciontransferenciasdetalle'],
                'group' => ['Administraciontransferencia.bancoscuenta_id'],
                'joins' => [['table' => 'administraciontransferenciasdetalles', 'alias' => 'Administraciontransferenciasdetalle', 'type' => 'left', 'conditions' => ['Administraciontransferencia.id=Administraciontransferenciasdetalle.administraciontransferencia_id']]]]);
            if (!empty($x)) {
                $transferenciaadm[] = $x;
            }
            $pagosacuentaaplicados[] = $this->Proveedorspago->Proveedorspagosacuenta->find('all', ['conditions' => ['Proveedorspagosacuenta.proveedorspagoaplicado_id' => $id]]);
            $notasdecreditoaplicadas[] = $this->Proveedorspago->Proveedorspagosnc->find('all', ['conditions' => ['Proveedorspagosnc.proveedorspago_id' => $id]]);
        }
        $consorcios = $this->Proveedorspago->Proveedor->Client->Consorcio->getConsorciosList();
        $bancoscuentas = $this->Proveedorspago->Proveedor->Client->Banco->Bancoscuenta->get();
        $this->set(compact('proveedorspago', 'facturas', 'pagosacuenta', 'efectivo', 'efectivoadm', 'transferencia', 'transferenciaadm', 'cheques', 'chequespropios', 'chequespropiosadm', 'bancoscuentas', 'pagosacuentaaplicados', 'notasdecreditoaplicadas', 'consorcios'));
        $this->set('users', $this->Proveedorspago->Proveedor->Client->User->getList());
        $this->layout = '';
    }

    public function add($id = null) {
        if ($this->request->is('post')) {
            if (!$this->request->is('ajax')) {
                die();
            }
            $resul = $this->Proveedorspago->guardar($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado correctamente'));
            }
            die(json_encode($resul));
        }
        $proveedors = $this->Proveedorspago->Proveedor->getList();
        if (count($proveedors) == 0) {
            $this->Flash->error(__('Debe crear un Proveedor (men&uacute Proveedores) antes de agregar una factura'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($id) && !in_array($id, array_keys($proveedors))) {
            $this->Flash->error(__('El proveedor es inexistente'));
            return $this->redirect(['action' => 'add']);
        }
        $caja = $this->Proveedorspago->Proveedor->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if ($caja === -1) {
            $this->Flash->error(__('El usuario no tiene una Caja asociada, no se pueden cargar pagos'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('proveedors', 'caja', 'id'));
    }

    public function delete($id = null) {
        $this->Proveedorspago->id = $id;
        if (!$this->Proveedorspago->exists() || $this->Proveedorspago->find('count', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.id' => $id], 'recursive' => 0]) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $resul = $this->Proveedorspago->undo($id);
        if (empty($resul)) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error($resul);
        }
        return $this->redirect(['action' => 'index']);
    }

}
