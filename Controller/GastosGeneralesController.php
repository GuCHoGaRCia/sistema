<?php

App::uses('AppController', 'Controller');

class GastosGeneralesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'addGasto', 'delGasto'); // permito blackhole x ajax
    }

    public function index() {
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        $c = "";
        if (isset($this->request->data['filter']['consorcio'])) {
            $c = isset($this->request->data['filter']['consorcio']) ? $this->request->data['filter']['consorcio'] : '';
        }
        $this->Paginator->settings = array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1,
        $this->GastosGenerale->parseCriteria($this->passedArgs)) + (!empty($c) ? ['Consorcio.id' => $c] : []),
            'joins' => [
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'gastos_generale_detalles', 'alias' => 'GastosGeneraleDetalle', 'type' => 'left', 'conditions' => ['GastosGenerale.id=GastosGeneraleDetalle.gastos_generale_id']],
                ['table' => 'coeficientes', 'alias' => 'Coeficiente', 'type' => 'left', 'conditions' => ['GastosGeneraleDetalle.coeficiente_id=Coeficiente.id']]
            ],
            'contain' => ['User', 'Liquidation', 'Rubro'],
            'fields' => ['GastosGenerale.*', 'Consorcio.name', 'Consorcio.id', 'Liquidation.periodo', 'User.name', 'Rubro.name', 'Coeficiente.name', 'GastosGeneraleDetalle.amount'],
            'order' => 'GastosGenerale.id desc,Rubro.id',
        );
        if (isset($this->request->data['filter']['consorcio'])) {
            $this->passedArgs = []; // para evitar
            $this->Paginator->settings += ['limit' => 500, 'maxLimit' => 500];
        } else {
            $this->Prg->commonProcess();
            $this->Paginator->settings += ['limit' => 10, 'maxLimit' => 10];
        }

        $this->set('gastosGenerales', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->GastosGenerale->Liquidation->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->GastosGenerale->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosGenerale.id' => $id),
            'joins' => [['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['GastosGenerale.liquidation_id=Liquidation.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'rubros', 'alias' => 'Rubro', 'type' => 'left', 'conditions' => ['Rubro.id=GastosGenerale.rubro_id']],
                ['table' => 'gastos_generale_detalles', 'alias' => 'GastosGeneraleDetalle', 'type' => 'right', 'conditions' => ['GastosGenerale.id=GastosGeneraleDetalle.gastos_generale_id']],
                ['table' => 'coeficientes', 'alias' => 'Coeficiente', 'type' => 'left', 'conditions' => ['GastosGeneraleDetalle.coeficiente_id=Coeficiente.id']]],
            'fields' => ['GastosGenerale.*', 'Consorcio.name', 'Consorcio.id', 'Liquidation.periodo', 'Rubro.name', 'Coeficiente.name', 'GastosGeneraleDetalle.amount']
        );
        $this->set('gastosGenerale', $this->GastosGenerale->find('first', $options));
    }

    public function add($id = null) {
        if ($this->request->is('post') && isset($this->request->data['GastosGenerale']['rubro_id'])) {
            $this->GastosGenerale->create();
            if ($this->GastosGenerale->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'add']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        if ($this->request->is('post') && isset($this->request->data['GastosGenerale']['liquidation_id'])) {
            if (!$this->GastosGenerale->Liquidation->canEdit($this->request->data['GastosGenerale']['liquidation_id'])) {
                $this->Flash->error(__('La Liquidacion es inexistente'));
                return $this->redirect(['controller' => 'rubros', 'action' => 'add']);
            }
            $consorcio_id = $this->GastosGenerale->Liquidation->getConsorcioId($this->request->data['GastosGenerale']['liquidation_id']);
            $rubros = $this->GastosGenerale->Rubro->getRubrosInfo($consorcio_id);
            if (count($rubros) == 0) {
                $this->Flash->error(__('Debe crear Rubros (men&uacute Gastos) antes de agregar un gasto'));
                return $this->redirect(['controller' => 'rubros', 'action' => 'add']);
            }
            if ($this->GastosGenerale->Liquidation->Consorcio->Propietario->find('count', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Propietario.consorcio_id' => $consorcio_id], 'recursive' => 0, 'contain' => 'Consorcio']) == 0) {
                $this->Flash->error(__('Debe crear Propietarios (men&uacute Datos) antes de agregar un gasto'));
                return $this->redirect(['controller' => 'propietarios', 'action' => 'add']);
            }
            $coeficientes = $this->GastosGenerale->GastosGeneraleDetalle->Coeficiente->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $consorcio_id, 'Coeficiente.enabled' => 1], 'recursive' => 0]);
            if (count($coeficientes) == 0) {
                $this->Flash->error(__('Debe crear Coeficientes (men&uacute Datos) antes de agregar un gasto'));
                return $this->redirect(['controller' => 'coeficientes', 'action' => 'add']);
            }
            $gastos = $this->GastosGenerale->listarGastosPorCoeficiente($this->request->data['GastosGenerale']['liquidation_id']);
            $distribuciones = $this->GastosGenerale->Liquidation->Consorcio->GastosDistribucione->find('list', ['conditions' => ['GastosDistribucione.consorcio_id' => $consorcio_id]]);
            $distribucionesDetalle = $this->GastosGenerale->Liquidation->Consorcio->GastosDistribucione->find('all', ['conditions' => ['GastosDistribucione.consorcio_id' => $consorcio_id], 'contain' => 'GastosDistribucionesDetalle']);
            $facturas = $this->GastosGenerale->Liquidation->Proveedorsfactura->find('list', ['conditions' => ['Proveedorsfactura.liquidation_id' => $this->request->data['GastosGenerale']['liquidation_id']],
                'fields' => ['Proveedorsfactura.gastos_generale_id', 'Proveedorsfactura.id']]);
            $facturaspagas = $this->GastosGenerale->Liquidation->Proveedorsfactura->getFacturasPagas(Hash::extract($gastos, '{n}.GastosGenerale.id'));
            $this->set(compact('rubros', 'coeficientes', 'gastos', 'distribuciones', 'distribucionesDetalle', 'facturas', 'facturaspagas'));
        }
        $liquidations = $this->GastosGenerale->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Liquidation.inicial' => 0, 'Liquidation.bloqueada' => 0),
            'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2'], 'order' => 'Consorcio.code'));
        if (count($liquidations) == 0) {
            $this->Flash->error(__('Debe crear una liquidaci&oacute;n (men&uacute Liquidaciones) antes de agregar un gasto'));
            return $this->redirect(['controller' => 'liquidations', 'action' => 'add']);
        }
        // para saber si hay o no proveedores cargados, y entonces mostrar el boton "Guardar" del #fp (factura proveedor)
        $proveedors = $this->GastosGenerale->Liquidation->Proveedorsfactura->Proveedor->find('list', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']], 'limit' => 1]);
        $this->set(compact('liquidations', 'proveedors'));
        $this->render('add');
    }

    public function habilita($id, $liquidation_id) {
        if (!$this->request->is('ajax')) {
            die();
        }
        if (!$this->GastosGenerale->canEdit($id)) {
            die();
        }
        if (!$this->GastosGenerale->Liquidation->canEdit($liquidation_id)) {
            die();
        }
        die(json_encode($this->GastosGenerale->habilita($id, $liquidation_id)));
    }

    public function addGasto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->GastosGenerale->addGasto($this->request->data)));
    }

    public function delGasto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->GastosGenerale->delGasto($this->request->data)));
    }

}
