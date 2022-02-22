<?php

App::uses('AppController', 'Controller');

class ProveedorsfacturasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']];
        $p = isset($this->request->data['Proveedorsfactura']['proveedor_id']) ? $this->request->data['Proveedorsfactura']['proveedor_id'] : '';
        $c = isset($this->request->data['Proveedorsfactura']['consorcio_id']) ? $this->request->data['Proveedorsfactura']['consorcio_id'] : '';
        $b = isset($this->request->data['Proveedorsfactura']['buscar']) ? $this->request->data['Proveedorsfactura']['buscar'] : '';
        if ($this->request->is('post') && (!empty($p) || !empty($c) || !empty($b))) {
            $this->set('proveedorsfacturas', $this->Proveedorsfactura->getFacturas($p, $c, 0, $b));
        } else {
            $conditions = ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1];
            $this->Paginator->settings = array('conditions' => $conditions, 'contain' => ['Proveedorsfacturasadjunto'],
                'joins' => [['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                    ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                    ['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorsfactura.proveedor_id']]],
                'fields' => ['Proveedor.name', 'Proveedorsfactura.concepto', 'Proveedorsfactura.gastos_generale_id', 'Proveedorsfactura.fecha', 'Proveedorsfactura.created', 'Proveedorsfactura.saldo', 'Proveedorsfactura.numero', 'Proveedorsfactura.importe', 'Consorcio.name', 'Liquidation.periodo', 'Liquidation.bloqueada'],
                'order' => 'Proveedorsfactura.created desc'); //ordeno por proveedor y created? antes estaba fecha
            $this->set('proveedorsfacturas', $this->paginar($this->Paginator));
        }

        $this->set('p', $p);
        $this->set('c', $c);
        $this->set('b', $b);
        $this->set('proveedores', $this->Proveedorsfactura->Proveedor->getList());
        $this->set('consorcios', $this->Proveedorsfactura->Liquidation->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Proveedorsfactura->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Proveedorsfactura.' . $this->Proveedorsfactura->primaryKey => $id), 'contain' => ['Proveedorsfacturasadjunto']);
        $resul = $this->Proveedorsfactura->find('first', $options);
        $this->set('proveedorsfactura', $resul);
        $this->set('gastosGenerale', $this->Proveedorsfactura->Liquidation->GastosGenerale->find('first', ['conditions' => ['GastosGenerale.id' => $resul['Proveedorsfactura']['gastos_generale_id']],
                    'contain' => ['Liquidation'], 'recursive' => -1,
                    'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Liquidation.consorcio_id')),
                        array('table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => array('LiquidationsType.id=Liquidation.liquidations_type_id')))]));
        $this->set('pagos', $this->Proveedorsfactura->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorsfactura_id' => $id, 'Proveedorspago.anulado' => 0], 'recursive' => 0, 'contain' => ['Proveedorspago', 'Proveedorsfactura'], 'fields' => ['Proveedorsfactura.fecha', 'Proveedorspago.fecha', 'Proveedorsfactura.concepto', 'Proveedorspagosfactura.importe']]));
        $this->layout = '';
    }

    public function view2($id = null) {
        if (!$this->Proveedorsfactura->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Proveedorsfactura.' . $this->Proveedorsfactura->primaryKey => $id), 'fields' => ['Proveedorsfactura.*', 'Liquidation.periodo', 'Consorcio.name'],
            'joins' => [['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]]);
        $resul = $this->Proveedorsfactura->find('first', $options);
        $this->set('proveedorsfactura', $resul);
        /* $this->set('gastosGenerale', $this->Proveedorsfactura->Liquidation->GastosGenerale->find('first', ['conditions' => ['GastosGenerale.id' => $resul['Proveedorsfactura']['gastos_generale_id']],
          'contain' => ['Liquidation'], 'recursive' => -1,
          'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Liquidation.consorcio_id')),
          array('table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => array('LiquidationsType.id=Liquidation.liquidations_type_id')))]));
          $this->set('pagos', $this->Proveedorsfactura->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorsfactura_id' => $id, 'Proveedorspago.anulado' => 0], 'recursive' => 0, 'contain' => ['Proveedorspago', 'Proveedorsfactura'], 'fields' => ['Proveedorsfactura.fecha', 'Proveedorspago.fecha', 'Proveedorsfactura.concepto', 'Proveedorspagosfactura.importe']]));
         */
        $this->layout = '';
    }

    public function add() {
        if ($this->request->is('post') && ($this->request->data['Proveedorsfactura']['guardagasto'] == 0 || isset($this->request->data['Proveedorsfactura']['rubro_id']))) {
            if ($this->Proveedorsfactura->guardar($this->request)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }

        if ($this->request->is('post') && $this->request->data['Proveedorsfactura']['guardagasto'] == 1) {
            $consorcio_id = $this->Proveedorsfactura->Liquidation->getConsorcioId($this->request->data['Proveedorsfactura']['liquidation_id']);
            $rubros = $this->Proveedorsfactura->Liquidation->Consorcio->Rubro->getRubrosInfo($consorcio_id);
            if (count($rubros) == 0) {
                $this->Flash->error(__('Debe crear Rubros (men&uacute Gastos) antes de agregar una factura'));
                return $this->redirect(['action' => 'index']);
            }
            if ($this->Proveedorsfactura->Liquidation->Consorcio->Propietario->find('count', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Propietario.consorcio_id' => $consorcio_id], 'recursive' => 0, 'contain' => 'Consorcio']) == 0) {
                $this->Flash->error(__('Debe crear Propietarios (men&uacute Datos) antes de agregar una factura'));
                return $this->redirect(['action' => 'index']);
            }
            $coeficientes = $this->Proveedorsfactura->Liquidation->GastosGenerale->GastosGeneraleDetalle->Coeficiente->getList($consorcio_id);
            if (count($coeficientes) == 0) {
                $this->Flash->error(__('Debe crear Coeficientes (men&uacute Datos) antes de agregar una factura'));
                return $this->redirect(['action' => 'index']);
            }
            $liquidations = $this->Proveedorsfactura->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Liquidation.id' => $this->request->data['Proveedorsfactura']['liquidation_id']),
                'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']));
            $distribuciones = $this->Proveedorsfactura->Liquidation->Consorcio->GastosDistribucione->find('list', ['conditions' => ['GastosDistribucione.consorcio_id' => $consorcio_id]]);
            $distribucionesDetalle = $this->Proveedorsfactura->Liquidation->Consorcio->GastosDistribucione->find('all', ['conditions' => ['GastosDistribucione.consorcio_id' => $consorcio_id], 'contain' => 'GastosDistribucionesDetalle']);
            $this->set(compact('rubros', 'coeficientes', 'distribuciones', 'distribucionesDetalle'));
        } else {
            $liquidations = $this->Proveedorsfactura->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Liquidation.inicial' => 0, 'Liquidation.bloqueada' => 0),
                'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2'], 'order' => 'Consorcio.code'));
        }
        $proveedors = $this->Proveedorsfactura->Proveedor->getList();
        if (count($proveedors) == 0) {
            $this->Flash->error(__('Debe crear un Proveedor (men&uacute Proveedores) antes de agregar una factura'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('proveedors', 'liquidations'));
    }

    public function add2($liquidation_id, $gastosgenerale_id) {
        $this->layout = '';
        $this->autoRender = false;
        if (!$this->Proveedorsfactura->Liquidation->canEdit($liquidation_id)) {
            return json_encode(['e' => 1, 'd' => 'La Liquidación es inexistente' . $liquidation_id]);
        }
        if (!$this->Proveedorsfactura->Liquidation->GastosGenerale->canEdit($gastosgenerale_id)) {
            return json_encode(['e' => 1, 'd' => 'El Gasto General es inexistente']);
        }
        if ($this->request->is('post') && isset($this->request->data['Proveedorsfactura']['proveedor_id'])) {
            if ($this->Proveedorsfactura->guardar($this->request)) {
                return json_encode(['e' => 0, 'd' => 'El dato fue guardado correctamente']);
            } else {
                $d = '';
                foreach ($this->Proveedorsfactura->validationErrors as $x) {
                    $d .= $x[0] . ". ";
                }
                return json_encode(['e' => 1, 'd' => $d]);
            }
        }
        $proveedors = $this->Proveedorsfactura->Proveedor->getList();
        $this->set(compact('proveedors'));
        $liquidations = $this->Proveedorsfactura->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.id' => $liquidation_id),
            'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2'], 'order' => 'Consorcio.code'));
        $gg = $this->Proveedorsfactura->find('all', ['conditions' => ['Proveedorsfactura.gastos_generale_id' => $gastosgenerale_id], 'contain' => ['Proveedorsfacturasadjunto'],
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorsfactura.proveedor_id']]],
            'fields' => ['Proveedorsfactura.concepto', 'Proveedorsfactura.importe', 'Proveedorsfactura.numero', 'Proveedorsfactura.fecha', 'Proveedorsfactura.id', 'Proveedor.name']]);
        $this->set(compact('liquidations', 'gg', 'gastosgenerale_id'));
        $this->layout = '';
        $this->render();
    }

    public function addfd($proveedorsfactura_id) {
        $this->layout = '';
        $this->autoRender = false;
        if (!$this->Proveedorsfactura->canEdit($proveedorsfactura_id)) {
            return json_encode(['e' => 1, 'd' => 'La Factura proveedor es inexistente']);
        }
        if ($this->request->is('post') && isset($this->request->params['form'])) {
            $this->request->data['Proveedorsfactura']['id'] = $proveedorsfactura_id;
            if ($this->Proveedorsfactura->Proveedorsfacturasadjunto->guardar($this->request)) {
                return json_encode(['e' => 0, 'd' => 'El dato fue guardado correctamente']);
            } else {
                return json_encode(['e' => 1, 'd' => 'El dato no pudo ser guardado, intente nuevamente']);
            }
        }
        $adj = $this->Proveedorsfactura->Proveedorsfacturasadjunto->find('all', ['conditions' => ['Proveedorsfacturasadjunto.proveedorsfactura_id' => $proveedorsfactura_id]]);
        $this->set(compact('adj'));
        $this->set('id', $proveedorsfactura_id);
        $this->render();
    }

    public function pagarEfectivo($proveedorsfactura_id) {
        $this->layout = '';
        $this->autoRender = false;
        if (!$this->Proveedorsfactura->canEdit($proveedorsfactura_id)) {
            return json_encode(['e' => 1, 'd' => 'La Factura proveedor es inexistente']);
        }
        $resul = $this->Proveedorsfactura->pagarEfectivo($proveedorsfactura_id);
        if ($resul['e'] == 0) {
            $this->Flash->success(__('La Factura fue abonada en efectivo'));
        } else {
            $this->Flash->error($resul['d']);
        }
        $this->redirect($this->referer());
    }

    /*
     * Obtiene las facturas PENDIENTES de pago de un Proveedor y Consorcio específico. Se utiliza en Pago proveedores
     */

    function getFacturas() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Proveedorsfactura->getFacturas($this->request->data['p'], $this->request->data['c'], false))); // true es para las facturas pagas
    }

    public function download($n = null, $pid = null, $link = null, $client_id = null) {
        $this->layout = '';
        $this->autoRender = false;
        $cli = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $this->Proveedorsfactura->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($client_id);
        if (empty($cli) || empty($n)) {
            echo __('El archivo no pudo ser descargado');
            return false;
        }
        $email = !empty($link) ? $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($link) : null;
        if (!empty($link) && filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE || empty($pid)) {//!empty($link) es para q no este descargando desde Adjuntos/index logueado
            echo __('El archivo no pudo ser descargado');
            return false;
        }
        $adjunto = $this->Proveedorsfactura->Proveedorsfacturasadjunto->find('first', ['conditions' => ['url' => $n], 'fields' => 'ruta']);
        if (empty($adjunto)) {
            echo __('El archivo no pudo ser descargado');
            return false;
        }
        $name = basename($adjunto['Proveedorsfacturasadjunto']['ruta']);
        if (preg_match('/^([-\.\w]+)$/', $name) > 0 && is_file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cli) . DS . $name)) {
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cli) . DS . $name, ['download' => true, 'name' => $name]);
            return $this->response;
        } else {
            echo __('El archivo no pudo ser descargado');
            return false;
        }
    }

    public function delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Proveedorsfactura->Proveedorsfacturasadjunto->delAdjunto($this->request->data['url'])));
    }

    public function delete($id = null) {
        if (!$this->Proveedorsfactura->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Proveedorsfactura->id = $id;
        if ($this->Proveedorsfactura->eliminar()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen pagos asociados a la factura, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
