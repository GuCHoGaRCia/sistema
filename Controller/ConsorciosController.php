<?php

App::uses('AppController', 'Controller');

class ConsorciosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        //array_push($this->Security->unlockedActions, 'cartadeudores'); // permito blackhole x ajax
    }

    public function index() {
        //echo $this->Consorcio->Client->Aviso->_encryptURL("xx") . "<br>";
        //echo $this->Consorcio->Client->Aviso->_decryptURL($this->Consorcio->Client->Aviso->_encryptURL("xx"));
        $this->Paginator->settings = array('conditions' => array('Client.id' => $_SESSION['Auth']['User']['client_id'], $this->Consorcio->parseCriteria($this->passedArgs)),
            'contain' => ['Client'],
            'order' => 'Client.name,Consorcio.code');
        $this->Prg->commonProcess();
        $gp = [];
        //if ($this->Consorcio->Client->cargaGPdeCartas($_SESSION['Auth']['User']['client_id'])) {//solo si carga gastos particulares de cartas
        foreach ($this->Consorcio->getConsorciosList() as $k => $v) {
            $gp[$k] = $this->Consorcio->Cuentasgastosparticulare->getCuentasInfo($k);
        }
        //}
        $this->set('consorcios', $this->paginar($this->Paginator));
        $this->set('gp', $gp);
    }

    public function listar() {
        $lista = $this->Consorcio->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1], 'recursive' => 0, 'order' => 'Client.name,Consorcio.code']);
        $count = [];
        foreach ($lista as $v) {
            $count[$v['Consorcio']['id']] = $this->Consorcio->Propietario->getCount($v['Consorcio']['id']);
        }
        $this->set('consorcios', $lista);
        $this->set('propietarios', $count);
    }

    public function panel_listar() {
        $lista = [];
        if (isset($this->request->data['filter']['clientes'])) {
            $conditions = ['Consorcio.habilitado' => 1, 'Consorcio.client_id' => $this->request->data['filter']['clientes']];
            $lista = $this->Consorcio->find('all', ['conditions' => $conditions, 'contain' => ['Client'], 'order' => 'Client.name,Consorcio.code']);
        }

        // cuento cantidad propietarios por consorcio
        $count = [];
        foreach ($lista as $v) {
            $count[$v['Consorcio']['id']] = $this->Consorcio->Propietario->getCount($v['Consorcio']['id']);
        }
        $this->set('propietarios', $count);
        $this->set('lista', $lista);
        $this->set('clientes', $this->Consorcio->Client->find('list', ['conditions' => ['Client.enabled' => 1], 'order' => 'Client.name']));
    }

    public function panel_index() {
        $this->Paginator->settings = array('conditions' => array($this->Consorcio->parseCriteria($this->passedArgs)),
            'fields' => ['Client.id', 'Client.name', 'Consorcio.id', 'Consorcio.name', 'Consorcio.cuit', 'Consorcio.address', 'Consorcio.city', 'Consorcio.code', 'Consorcio.interes', 'Consorcio.prorrateagastosgenerales', 'Consorcio.imprime_cod_barras', 'Consorcio.imprime_cpe', 'Consorcio.2_cuotas', 'Consorcio.description'],
            'contain' => ['Client'],
            'order' => 'Client.name,Consorcio.code');
        $this->Prg->commonProcess();
        $this->set('consorcios', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Consorcio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            $this->set('propietarios', $this->Consorcio->Propietario->find('all', ['conditions' => ['Propietario.consorcio_id' => isset($this->request->data['Consorcio']['consorcio']) ? $this->request->data['Consorcio']['consorcio'] : $id]]));
        } else {
            $this->set('propietarios', $this->Consorcio->Propietario->find('all', ['conditions' => ['Propietario.consorcio_id' => $id]]));
        }

        $options = ['conditions' => ['Consorcio.' . $this->Consorcio->primaryKey => isset($this->request->data['Consorcio']['consorcio']) ? $this->request->data['Consorcio']['consorcio'] : $id], 'recursive' => 0, 'fields' => ['Consorcio.id', 'Consorcio.name']];
        $this->set('c', $this->Consorcio->find('first', $options));
        $this->set('consorcios', $this->Consorcio->getConsorciosList());
    }

    public function etiquetas($id = null) {
        if (!$this->Consorcio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('propietarios', $this->Consorcio->Propietario->find('all', ['conditions' => ['Propietario.consorcio_id' => $id], 'order' => 'Propietario.orden,Propietario.code']));
        $this->layout = '';
    }

    public function resumen() {
        $vista = 'resumen';
        $ultimo = $this->Consorcio->Client->query('SELECT date(fecha) Fecha FROM saldoscajabancos group by date(fecha) order by date(fecha) desc limit 1');
        $procesando = true;
        if (!empty($ultimo) && strtotime($ultimo[0][0]['Fecha']) == strtotime(date("Y-m-d", strtotime("-1 day")))) {
            $procesando = false;
        }
        $this->set('procesando', $procesando);
        $consorcios = $this->Consorcio->getConsorciosList();
        if ($this->request->is('post')) {
            if ($this->request->data['Consorcio']['consorcio'] != 0 && !$this->Consorcio->canEdit($this->request->data['Consorcio']['consorcio'])) {
                $this->Flash->error(__('El dato es inexistente'));
                return $this->redirect(['action' => 'resumen']);
            }
            $d = $this->Consorcio->fecha($this->request->data['Consorcio']['desde']);
            $h = $this->Consorcio->fecha($this->request->data['Consorcio']['hasta']);
            if (!$this->Consorcio->validateDate($d, 'Y-m-d') || !$this->Consorcio->validateDate($h, 'Y-m-d') || !$this->Consorcio->fechaEsMenorIgualQue($d, $h)) {
                $this->Flash->error(__('Las fechas son incorrectas'));
                return $this->redirect(['action' => 'resumen']);
            }
            if ($this->request->data['Consorcio']['consorcio'] == 0) {
                $vista = 'resumentotalizado';
                $saldos = $movimientosDiaActual = [];

                if ($h == date("Y-m-d")) {      // hasta es el dia actual
                    $desde = $h . " 00:00:00";
                    $hasta = $h . " 23:59:59";
                    foreach ($consorcios as $k => $v) {
                        $saldos[$k]['desde'] = $this->Consorcio->Client->Saldoscajabanco->getSaldos($k, date("Y-m-d", strtotime($this->Consorcio->fecha($d) . " -1 day")));
                        $saldos[$k]['hasta'] = $this->Consorcio->Client->Saldoscajabanco->getSaldos($k, date("Y-m-d", strtotime($this->Consorcio->fecha($h) . " -1 day")));
                        $movimientosDiaActual[$k] = $this->Consorcio->Client->Caja->getTotalesMovimientosResumen($k, $desde, $hasta, 1);
                    }
                    $this->set('movimientosDiaActual', $movimientosDiaActual);
                } else {
                    foreach ($consorcios as $k => $v) {
                        $saldos[$k]['desde'] = $this->Consorcio->Client->Saldoscajabanco->getSaldos($k, date("Y-m-d", strtotime($this->Consorcio->fecha($d) . " -1 day")));
                        $saldos[$k]['hasta'] = $this->Consorcio->Client->Saldoscajabanco->getSaldos($k, date("Y-m-d", strtotime($this->Consorcio->fecha($h))));
                    }
                }
                $this->set('saldos', $saldos);
            } else {
                $this->set('movimientos', $this->Consorcio->Client->Caja->getMovimientosResumen($this->request->data['Consorcio']['consorcio'], $this->request->data['Consorcio']['desde'], $this->request->data['Consorcio']['hasta'])); //incluyo anulados!
                $this->set('saldos', $this->Consorcio->Client->Saldoscajabanco->getSaldos($this->request->data['Consorcio']['consorcio'], date("Y-m-d", strtotime($this->Consorcio->fecha($this->request->data['Consorcio']['desde']) . " -1 day"))));
            }
            $this->set('d', $d);
            $this->set('h', $h);
        }
        $this->set('consorcios', $consorcios);
        $this->set('proveedors', $this->Consorcio->Client->Proveedor->getList($_SESSION['Auth']['User']['client_id']));
        $this->set('cuentas', $this->Consorcio->Client->Caja->Cajasingreso->Bancoscuenta->get());

        $this->render($vista);
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Consorcio->create();
            if ($this->Consorcio->save($this->request->data)) {
                if (isset($_SERVER['HTTP_ORIGIN']) && strpos($_SERVER['HTTP_ORIGIN'], "localhost") === false) {// si se agrega un consorcio nos avisa x email
                    $info = "Cliente: " . h($_SESSION['Auth']['User']['Client']['name']) . " - Consorcio: " . h($this->request->data['Consorcio']['name']);
                    $this->Consorcio->Client->Avisosqueue->addQueue($_SESSION['Auth']['User'], "[NUEVO CONSORCIO] ", $info, $info, "info@ceonline.com.ar");
                }
                $this->Flash->success(__('El Consorcio fue guardado. Por favor configure el mismo'));
                return $this->redirect(['controller' => 'Consorciosconfigurations', 'action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        if ($this->Consorcio->Client->LiquidationsType->find('count', array('conditions' => array('LiquidationsType.client_id' => $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.enabled' => 1))) == 0) {
            $this->Flash->error(__('Debe agregar Tipos de liquidaciones (men&uacute; Liquidaciones) y habilitarlas antes de crear un consorcio'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function cartadeudores($id) {
        $saldos = $cantidad = [];
        $this->Consorcio->recursive = 0;
        if (!$this->Consorcio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $propietarios = $this->Consorcio->Propietario->getPropietarios($id, ['id', 'name2', 'email']);
        if (empty($propietarios)) {
            $this->Flash->error(__('Debe agregar Propietarios antes de enviar cartas deudores'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('propietarios', $propietarios);
        $this->set('consorcio', $this->Consorcio->findById($id));
        if ($this->request->is('post') && isset($this->request->data['Consorcio'])) {
            $this->set('carta', $this->request->data['Consorcio']['cartadeudores']);
            unset($this->request->data['Consorcio']['cartadeudores']);
            foreach ($this->request->data['Consorcio'] as $k => $v) {
                $saldos[$k] = $this->Consorcio->Propietario->SaldosCierre->getSaldosDeudorPropietario($k);
            }
            $this->set('saldos', $saldos);

            $this->layout = '';
            $this->render('cartadeudoresprint');
        }

        foreach ($propietarios as $k => $v) {
            $saldo = $this->Consorcio->Propietario->SaldosCierre->getSaldosDeudorPropietario($k);
            if ($saldo > 0) {
                $saldos[$k] = $saldo;
                $cantidad[$k] = $this->Consorcio->Propietario->SaldosCierre->getCantidadLiquidacionesImpagas($k);
            }
        }
        $this->set('saldos', $saldos);
        $this->set('cantidad', $cantidad);
        $this->set('cartadeudores', $this->Consorcio->Client->getCartaDeudores($_SESSION['Auth']['User']['client_id']));
    }

    public function cartadeudoresemail() {
        $saldos = $p = [];
        if ($this->request->is('post') && isset($this->request->data['Consorcio'])) {
            $this->set('carta', $this->request->data['Consorcio']['cartadeudores']);
            unset($this->request->data['Consorcio']['cartadeudores']);
            unset($this->request->data['Consorcio']['Imprimir']);
            unset($this->request->data['Consorcio']['Email']);
            foreach ($this->request->data['Consorcio'] as $k => $v) {
                if ($v == 0) {
                    continue;
                }
                if (!$this->Consorcio->Propietario->canEdit($k)) {
                    die("El dato es inexistente");
                }
                $consorcio = $this->Consorcio->Propietario->getPropietarioConsorcio($k);
                $p[$k] = $this->Consorcio->Propietario->getPropietarios($consorcio, ['id', 'name2', 'email'], $k)[$k];
                $saldos[$k] = $this->Consorcio->Propietario->SaldosCierre->getSaldosDeudorPropietario($k);
            }
            $this->set('saldos', $saldos);

            $this->layout = '';
            $this->autoRender = false;
            $this->Consorcio->recursive = 0;
            $consor = $this->Consorcio->findById($consorcio);
            foreach ($p as $k => $v) {
                if (!empty($v['email'])) {
                    $e = explode(',', $v['email']);
                    $view = new View($this, false);
                    $view->layout = '';
                    $view->set('consorcio', $consor);
                    $view->set('p', [$k => $v]);
                    $html = preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'), array('>', '<', '\\1'), $view->render('cartadeudoresemail'));
                    foreach ($e as $m) {
                        $encolar = $this->Consorcio->Client->Avisosqueue->addQueue($_SESSION['Auth']['User'], 'Informe deuda', $html, $html, $m);
                    }
                    $this->Consorcio->Propietario->Cartadeudore->add($k, $html);
                }
            }
        }
        return "La Carta deudor fue enviada por email correctamente";
    }

    public function recordatoriopago($id) {
        $saldos = $cantidad = [];
        $this->Consorcio->recursive = 0;
        if (!$this->Consorcio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $propietarios = $this->Consorcio->Propietario->getPropietarios($id, ['id', 'name2', 'email']);
        if (empty($propietarios)) {
            $this->Flash->error(__('Debe agregar Propietarios antes de enviar recordatorio de pagos'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('propietarios', $propietarios);
        $this->set('consorcio', $this->Consorcio->findById($id));

        foreach ($propietarios as $k => $v) {
            $saldo = $this->Consorcio->Propietario->SaldosCierre->getSaldosDeudorPropietario($k);
            if ($saldo > 0) {
                $saldos[$k] = $saldo;
                $cantidad[$k] = $this->Consorcio->Propietario->SaldosCierre->getCantidadLiquidacionesImpagas($k);
            }
        }
        $this->set('saldos', $saldos);
        $this->set('cantidad', $cantidad);
        $this->set('recordatoriopago', $this->Consorcio->Client->getRecordatorioPago($_SESSION['Auth']['User']['client_id']));
    }

    public function recordatoriopagoemail() {
        $saldos = $p = [];
        if ($this->request->is('post') && isset($this->request->data['Consorcio'])) {
            $this->set('recordatoriopago', $this->request->data['Consorcio']['recordatoriopago']);
            unset($this->request->data['Consorcio']['recordatoriopago']);
            unset($this->request->data['Consorcio']['Email']);
            foreach ($this->request->data['Consorcio'] as $k => $v) {
                if ($v == 0) {
                    continue;
                }
                if (!$this->Consorcio->Propietario->canEdit($k)) {
                    die("El dato es inexistente");
                }
                $consorcio = $this->Consorcio->Propietario->getPropietarioConsorcio($k);
                $p[$k] = $this->Consorcio->Propietario->getPropietarios($consorcio, ['id', 'name2', 'email'], $k)[$k];
                $saldos[$k] = $this->Consorcio->Propietario->SaldosCierre->getSaldosDeudorPropietario($k);
            }
            $this->set('saldos', $saldos);

            $this->layout = '';
            $this->autoRender = false;
            $this->Consorcio->recursive = 0;
            $consor = $this->Consorcio->findById($consorcio);
            foreach ($p as $k => $v) {
                if (!empty($v['email'])) {
                    $e = explode(',', $v['email']);
                    $view = new View($this, false);
                    $view->layout = '';
                    $view->set('consorcio', $consor);
                    $view->set('p', [$k => $v]);
                    $html = preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'), array('>', '<', '\\1'), $view->render('recordatoriopagoemail'));
                    foreach ($e as $m) {
                        $encolar = $this->Consorcio->Client->Avisosqueue->addQueue($_SESSION['Auth']['User'], 'Recordatorio Pago', $html, $html, $m);
                    }
                    //$this->Consorcio->Propietario->Cartadeudore->add($k, $html);
                }
            }
        }
        return "El Recordatorio de Pago fue enviado por email correctamente";
    }

    /*
     * Listado Propietarios expensas > $2000 y superficie > 100 requerido x AFIP. Se llama desde Consorcios/index (icono reportes)
     */

    public function rg3369afip() {
        if (!isset($this->request->data['Consorcio']['consorcio']) || !$this->Consorcio->canEdit($this->request->data['Consorcio']['consorcio'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if (!isset($this->request->data['Consorcio']['liquidacion']) || !$this->Consorcio->Liquidation->canEdit($this->request->data['Consorcio']['liquidacion'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if (!isset($this->request->data['Consorcio']['superficie']) || !isset($this->request->data['Consorcio']['monto']) || !is_numeric($this->request->data['Consorcio']['superficie']) || !is_numeric($this->request->data['Consorcio']['monto'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post')) {
            $this->set('data', $this->Consorcio->rg3369afip($this->request->data['Consorcio']));
        }

        $this->layout = '';
    }

    public function edit($id = null) {
        if (!$this->Consorcio->exists($id) || $this->Consorcio->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Consorcio->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Consorcio.id' => $id), 'recursive' => 0);
            $this->request->data = $this->Consorcio->find('first', $options);
        }
    }

    public function panel_delete($id = null) {
        $this->Consorcio->id = $id;
        if (!$this->Consorcio->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        ini_set('max_execution_time', '10000');
        if ($this->Consorcio->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    /*
     * Si se agregan consorcios automaticamente, usar esta funcion para crear las liquidaciones iniciales, los presupuestos y los saldos iniciales de los Propietarios
     */

    public function panel_creaLiquidacionesIniciales() {
        ini_set('max_execution_time', '10000');
        $consorcios = $this->Consorcio->find('all', array('conditions' => ['Client.enabled' => 1], 'fields' => ['Consorcio.id', 'Consorcio.name', 'Consorcio.client_id'], 'recursive' => 0));
        foreach ($consorcios as $v) {
            $this->Consorcio->creaLiquidacionesIniciales($v);
        }
        $this->Flash->success(__('Se crearon las liquidaciones iniciales, presupuestos y saldos iniciales del cliente actual'));
        return $this->redirect(['controller' => 'Clients', 'action' => 'procesos']);
    }

}
