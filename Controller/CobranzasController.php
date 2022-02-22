<?php

App::uses('AppController', 'Controller');

class CobranzasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'add', 'add2', 'periodo', 'getCobranzasPeriodo', 'borrarMultiple');
        $this->Auth->allow('ver');
    }

    public function index() {
        $this->Cobranza->recursive = 1;
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']];
        $b = [];
        if (isset($this->request->data['Cobranza']['buscar']) && !empty($this->request->data['Cobranza']['buscar'])) {
            if (in_array(strtolower($this->request->data['Cobranza']['buscar']), ['cm', 'ca', 'cp'])) {
                $b = ['Cobranza.recibimosde like' => $this->request->data['Cobranza']['buscar'] . ' %'];
            } else {
                $b = ['OR' => ['Propietario.name like' => '%' . $this->request->data['Cobranza']['buscar'] . '%',
                        'Cobranza.concepto like' => '%' . $this->request->data['Cobranza']['buscar'] . '%', 'Cobranza.recibimosde like' => '%' . $this->request->data['Cobranza']['buscar'] . '%', 'Cobranza.numero' => $this->request->data['Cobranza']['buscar']]];
            }
        }

        $d = isset($this->request->data['Cobranza']['desde']) ? $this->request->data['Cobranza']['desde'] : '';
        $h = isset($this->request->data['Cobranza']['hasta']) ? $this->request->data['Cobranza']['hasta'] : '';
        $c = isset($this->request->data['Cobranza']['consorcio']) && $this->request->data['Cobranza']['consorcio'] !== '' ? ['Consorcio.id' => $this->request->data['Cobranza']['consorcio']] : [];
        $conditions += isset($this->request->data['Cobranza']['anulada']) && $this->request->data['Cobranza']['anulada'] == '1' ? [] : ['Cobranza.anulada' => 0];
        $conditions += !empty($d) ? ['date(Cobranza.fecha) >=' => $this->Cobranza->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Cobranza.fecha) <=' => $this->Cobranza->fecha($h)] : [];
        $conditions += $c + $b;
        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']],
                ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Cobranza.user_id']]],
            'contain' => ['Propietario', 'Cobranzatipoliquidacione'],
            'fields' => ['Propietario.name', 'Propietario.consorcio_id', 'Propietario.id', 'Propietario.unidad', 'Propietario.code', 'Cobranza.user_id', 'Cobranza.numero', 'User.id', 'User.name', 'Cobranza.fecha', 'Cobranza.created', 'Cobranza.concepto', 'Cobranza.modified', 'Cobranza.id', 'Cobranza.amount', 'Cobranza.anulada', 'Cobranza.recibimosde','Consorcio.habilitado', 'Consorcio.name'],
            'order' => 'Cobranza.numero desc,Cobranza.created desc,Cobranza.fecha desc,Cobranza.id'];
        if (!isset($this->request->data['Cobranza']) || empty($this->request->data['Cobranza'])) {
            $this->Paginator->settings += ['limit' => 10];
        } else {
            $this->Paginator->settings += ['limit' => 10000, 'maxLimit' => 10000];
        }
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('b', isset($this->request->data['Cobranza']['buscar']) ? $this->request->data['Cobranza']['buscar'] : '');
        $this->set('c', $c);

        $this->set('bloqueadas', $this->Cobranza->Propietario->Consorcio->Liquidation->getFechaBloqueadaXTipoLiquidacion());
        $this->set('cobranzas', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Cobranza->Propietario->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Cobranza->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $detalle = $this->Cobranza->getDetalleCobranza($id);
        $periodos = [];
        $tipos = $this->Cobranza->Cobranzatipoliquidacione->find('all', ['conditions' => ['Cobranzatipoliquidacione.cobranza_id' => $id], 'recursive' => 0, 'fields' => ['LiquidationsType.name', 'LiquidationsType.id', 'Cobranzatipoliquidacione.amount', 'Cobranzatipoliquidacione.solocapital']]);
        foreach ($tipos as $v) {
            $periodos[$v['LiquidationsType']['id']] = $this->Cobranza->Propietario->Consorcio->Liquidation->getPeriodo($this->Cobranza->Propietario->Consorcio->Liquidation->getUltimaLiquidacion($detalle['Consorcio']['id'], $v['LiquidationsType']['id'], $detalle['Cobranza']['created']));
        }
        $this->set('cobranza', $detalle);
        $this->set('periodos', $periodos);
        $this->set('cheque', $this->Cobranza->Cobranzacheque->getMovimientosCobranza($id));
        $this->set('tipos', $tipos);
        $this->set('users', $this->Cobranza->User->getList());
        $this->set('formasdepago', $this->Cobranza->Propietario->Consorcio->Client->Formasdepago->get());
        $this->layout = '';
    }

    public function ver($link = null) {
        if (empty($link)) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        $d = $this->Cobranza->Propietario->Aviso->_decryptURL($link);
        if (empty($d)) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        $d = explode("#", $d);
        if (empty($d) || count($d) != 4) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        // $liquidation_id#$propid#$link#$cobranza_id
        $datos = $this->Cobranza->Propietario->Aviso->getDatos($d[2]);
        if (empty($datos) || $datos === []) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }

        $check = $this->Cobranza->Propietario->Aviso->Client->Reportsclient->Report->checkClient3($d[0], $d[1], $d[2]);
        if (!$check) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        $id = $d[3];
        $detalle = $this->Cobranza->getDetalleCobranza($id);
        if (empty($detalle)) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        $periodos = [];
        $tipos = $this->Cobranza->Cobranzatipoliquidacione->find('all', ['conditions' => ['Cobranzatipoliquidacione.cobranza_id' => $id], 'recursive' => 0, 'fields' => ['LiquidationsType.name', 'LiquidationsType.id', 'Cobranzatipoliquidacione.amount', 'Cobranzatipoliquidacione.solocapital']]);
        foreach ($tipos as $v) {
            $periodos[$v['LiquidationsType']['id']] = $this->Cobranza->Propietario->Consorcio->Liquidation->getPeriodo($this->Cobranza->Propietario->Consorcio->Liquidation->getUltimaLiquidacion($detalle['Consorcio']['id'], $v['LiquidationsType']['id'], $detalle['Cobranza']['created']));
        }
        $this->set('cobranza', $detalle);
        $this->set('periodos', $periodos);
        $this->set('cheque', $this->Cobranza->Cobranzacheque->getMovimientosCobranza($id));
        $this->set('tipos', $tipos);
        $this->set('formasdepago', $this->Cobranza->Propietario->Consorcio->Client->Formasdepago->get());
        $this->layout = '';
        $this->set('mostrarsolounrecibo', 1);
        $this->render('view');
    }

    /*
     * Se utiliza para mostrar el listado de las ultimas cobranzas del propietario seleccionado en liq sin prorratear o sin bloquear
     * Al hacer click en el iconito "i" de las cuentas corrientes de los Propietarios (de las liq "En Proceso")
     */

    public function listar($liquidation_id, $propietario_id) {
        $this->set('cobranzas', $this->Cobranza->getCobranzas($liquidation_id, $propietario_id));
        $this->layout = '';
    }

    /*
     * Son las cobranzas automáticas
     */

    public function add() {
        if ($this->request->is('post')) {
            $resul = $this->Cobranza->guardar($this->request->data);
            if (isset($resul['c']) && $resul['c'] != 0) {
                $this->Flash->success(__('Se guardaron ' . $resul['c'] . ' Cobranzas Autom&aacute;ticas correctamente'));
            }
            if (!empty($resul['d'])) {
                $this->Flash->error($resul['d']);
            }
        } else {
            $this->Flash->info(__('Se mostrar&aacute;n solamente las cobranzas recibidas a traves de PLAPSA de los Consorcios que tengan una Cuenta Bancaria asociada.'));
            //$this->Flash->warning(__('Si el Consorcio no posee Cuenta Bancaria asociada, se mostrar&aacute; este &iacute;cono.'));
        }
        $this->set('cliente', $this->Cobranza->Propietario->Aviso->_encryptURL($_SESSION['Auth']['User']['Client']['code']));
        $formadepago = $this->Cobranza->Propietario->Consorcio->Client->Formasdepago->findByFormaAndClientId('Cobranza Automática', $_SESSION['Auth']['User']['client_id']);
        $this->set('formadepago', $formadepago['Formasdepago']['id'] ?? 0);
    }

    /*
     * Son las cobranzas manuales
     */

    public function add2($consorcio_id = null) {
        if ($this->request->is('post')) {
            $resul = $this->Cobranza->procesaCobranzaManual($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado') . ". " . "<a target='_blank' href='/sistema/cobranzas/view/" . $this->Cobranza->getInsertId() . "'>" . __('Imprimir comprobante') . "</a>");
            }
            die(json_encode($resul));
        }
        $consorcios = $this->Cobranza->Propietario->Consorcio->getConsorciosList();
        if (!empty($consorcio_id) && !in_array($consorcio_id, array_keys($consorcios))) {
            $this->Flash->error(__('El Consorcio es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $liquidations = $this->Cobranza->Propietario->Consorcio->Liquidation->find('list', array('conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.inicial' => 0] + (!empty($consorcio_id) ? ['Consorcio.id' => $consorcio_id] : []), 'recursive' => -1, 'limit' => 1, 'contain' => ['Consorcio']));
        if (count($liquidations) == 0) {
            $this->Flash->error(__('Debe agregar una Liquidaci&oacute;n (men&uacute Liquidaciones) antes de agregar una cobranza'));
            return $this->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $propietarios = $this->Cobranza->Propietario->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0, 'contain' => 'Consorcio', 'limit' => 1));
        if (count($propietarios) == 0) {
            $this->Flash->error(__('Debe agregar Propietarios (men&uacute Datos) antes de agregar una cobranza'));
            return $this->redirect(['controller' => 'propietarios', 'action' => 'add']);
        }
        $caja = $this->Cobranza->Propietario->Consorcio->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if ($caja === -1) {
            $this->Flash->error(__('El usuario no tiene una Caja asociada, no se pueden cargar cobranzas'));
            return $this->redirect(['controller' => 'cajas', 'action' => 'add']);
        }
        $this->set('consorcio_id', $consorcio_id);
        $this->set('consorcios', $consorcios);
    }

    /*
     * Son las cobranzas para todos los propietarios del consorcio (como las viejas "por periodo")
     */

    public function periodo($id = null) {
        if ($this->request->is('post')) {
            $resul = $this->Cobranza->procesaCobranzaPeriodo($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado'));
            }
            die(json_encode($resul));
        }
        $liquidations = $this->Cobranza->Propietario->Consorcio->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.inicial' => 0), 'recursive' => -1, 'limit' => 1, 'contain' => ['Consorcio']));
        if (count($liquidations) == 0) {
            $this->Flash->error(__('Debe agregar una Liquidaci&oacute;n (men&uacute Liquidaciones) antes de agregar una cobranza'));
            return $this->redirect(['action' => 'index']);
        }
        $propietarios = $this->Cobranza->Propietario->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0, 'contain' => 'Consorcio', 'limit' => 1));
        if (count($propietarios) == 0) {
            $this->Flash->error(__('Debe agregar Propietarios (men&uacute Datos) antes de agregar una cobranza'));
            return $this->redirect(['action' => 'index']);
        }
        $caja = $this->Cobranza->Propietario->Consorcio->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if ($caja === -1) {
            $this->Flash->error(__('El usuario no tiene una Caja asociada, no se pueden cargar cobranzas'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('consorcios', $this->Cobranza->Propietario->Consorcio->getConsorciosList());
        $this->set('id', $id);
    }

    public function multas() {
        if ($this->request->is('post')) {
            $datamultas = $this->Cobranza->obtienepropdeudores($this->request->data['Cobranza'], true);

            if (isset($this->request->data['Cobranza']) && count($this->request->data['Cobranza']) > 2) {
                $resul = $this->Cobranza->guardaMultas($this->request->data['Cobranza'], $datamultas);

                if ($resul === "") {
                    $this->Flash->success(__('El dato fue guardado'));
                    return $this->redirect(['action' => 'multas']);
                } else {
                    $this->Flash->error(__("El dato no pudo ser guardado<br>") . $resul);
                }
            }
            $cuentaMulta = $this->Cobranza->Propietario->Consorcio->getCGPDefectoMulta($this->request->data['Cobranza']['consorcio_id']);
            if ($cuentaMulta == 0) {
                $this->Flash->error(__('Debe seleccionar una cuenta de Gastos Particulares para las Multas'));
                return $this->redirect(['action' => 'index', 'controller' => 'consorcios']);
            }

            $propietarios = $datamultas['propietarios'];
            $liquidacionActivaId = $this->Cobranza->Propietario->Consorcio->Liquidation->getLiquidationActivaId($this->request->data['Cobranza']['consorcio_id'], $this->request->data['Cobranza']['tipos']);

            $multascargadas = $this->Cobranza->Propietario->GastosParticularesMulta->listarCargadas($propietarios, $liquidacionActivaId);

            $cantmultascargadas = count($multascargadas);
            $cantpropietarios = count($propietarios);

            if ($cantmultascargadas == $cantpropietarios && ($cantmultascargadas != 0 && $cantpropietarios != 0)) {  // si las multas cargadas de los propietarios en la liq activa es igual a los propietarios deudores de la liq activa
                $this->set('todosmultados', true);                 // entonces se los multo a todos y tengo que tirar el cartel de info en la vista de multas
            } else {
                $this->set('propietarios', $propietarios);
                $this->set('saldosactualespropietarios', $datamultas['saldosactualespropietarios']);
                $this->set('saldosultimaexpensapropietarios', $datamultas['saldosultimaexpensapropietarios']);
                $this->set('nombreConsorcio', $this->Cobranza->Propietario->Consorcio->getConsorcioName($this->request->data['Cobranza']['consorcio_id']));
                $this->set('interesMultaConsorcio', $this->Cobranza->Propietario->Consorcio->getInteresMulta($this->request->data['Cobranza']['consorcio_id']));
                $this->set('consorcio_id', $this->request->data['Cobranza']['consorcio_id']);
                $this->set('idTipoLiquidacion', $this->request->data['Cobranza']['tipos']);
                $this->set('multascargadas', $this->Cobranza->Propietario->GastosParticularesMulta->listarCargadas($propietarios, $liquidacionActivaId));
            }
        }

        $this->set('tipos', $this->Cobranza->User->Client->LiquidationsType->getLiquidationsTypes($_SESSION['Auth']['User']['client_id']));
        $this->set('consorcios', $this->Cobranza->Propietario->Consorcio->getConsorciosList());
    }

    public function multassobrecapital() {
        if ($this->request->is('post')) {
            $data = $this->request->data['Cobranza'];
            $datamultas = $this->Cobranza->obtienepropdeudoresSobreCapital($data);

            if (isset($data) && count($data) > 2) {
                $resul = $this->Cobranza->guardaMultasSobreCapital($data, $datamultas);
                if ($resul === "") {
                    $this->Flash->success(__('El dato fue guardado'));
                    return $this->redirect(['action' => 'multassobrecapital']);
                } else {
                    $this->Flash->error(__("El dato no pudo ser guardado<br>") . $resul);
                }
            }

            if ($datamultas === 'soloLiqInicial') {
                $this->Flash->warning(__('Debe tener creada al menos una Liquidacion del tipo elegido'));
                return $this->redirect(['action' => 'multassobrecapital']);
            }
            if ($datamultas === 'todasBloqueadas') {
                $this->Flash->warning(__('Debe tener abierta al menos una Liquidacion del tipo elegido en el consorcio seleccionado'));
                return $this->redirect(['action' => 'multassobrecapital']);
            }

            $consorcio_id = $data['consorcio_id'];
            $idTipoLiquidacion = $data['tipos'];
            $cuentaMultaCapital = $this->Cobranza->Propietario->Consorcio->getCGPDefectoMultaCapital($consorcio_id);
            if ($cuentaMultaCapital == 0) {
                $this->Flash->error(__('Debe seleccionar una cuenta de Gastos Particulares para las Multas sobre Capital'));
                return $this->redirect(['action' => 'index', 'controller' => 'consorcios']);
            }

            $propietarios = $datamultas['propietarios'];
            $liquidacionActivaId = $this->Cobranza->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $idTipoLiquidacion);

            $multascargadas = $this->Cobranza->Propietario->GastosParticularesMulta->listarCargadas($propietarios, $liquidacionActivaId, 1);
            $cantmultascargadas = count($multascargadas);
            $cantpropietarios = count($propietarios);

            if ($cantmultascargadas == $cantpropietarios && ($cantmultascargadas != 0 && $cantpropietarios != 0)) {  // si las multas cargadas de los propietarios en la liq activa es igual a los propietarios deudores de la liq activa
                $this->set('todosmultados', true);                 // entonces se los multo a todos y tengo que tirar el cartel de info en la vista de multas
            } else {
                $this->set('propietarios', $propietarios);
                $this->set('saldoscapitalactual', $datamultas['saldoscapitalactual']);
                $this->set('cantidadperiodosdeuda', $datamultas['cantidadperiodosdeuda']);
                $this->set('nombreConsorcio', $this->Cobranza->Propietario->Consorcio->getConsorcioName($consorcio_id));
                $this->set('interesMultaCapitalConsorcio', $this->Cobranza->Propietario->Consorcio->getInteresMultaCapital($consorcio_id));
                $this->set('consorcio_id', $consorcio_id);
                $this->set('idTipoLiquidacion', $idTipoLiquidacion);
                $this->set('multascargadas', $multascargadas);
            }
        }

        $this->set('tipos', $this->Cobranza->User->Client->LiquidationsType->getLiquidationsTypes($_SESSION['Auth']['User']['client_id']));
        $this->set('consorcios', $this->Cobranza->Propietario->Consorcio->getConsorciosList());
    }

    /*
     * Genera el interes por Pago Fuera de Termino
     */

    public function pft() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['Cobranza']) && count($this->request->data['Cobranza']) > 2) {
                $resul = $this->Cobranza->guardaPFT($this->request->data);
                if ($resul === "") {
                    $this->Flash->success(__('El dato fue guardado'));
                    return $this->redirect(['action' => 'pft']);
                } else {
                    $this->Flash->error(__("El dato no pudo ser guardado<br>") . $resul);
                }
            }

            $cuentaPFT = $this->Cobranza->Propietario->Consorcio->getCGPDefectoPFT($this->request->data['Cobranza']['consorcio_id']);
            if ($cuentaPFT == 0) {
                $this->Flash->error(__('Debe seleccionar una cuenta de Gastos Particulares para los Pagos fuera de T&eacute;rmino'));
                return $this->redirect(['action' => 'index', 'controller' => 'consorcios']);
            }

            $cobranzas = $this->Cobranza->getCobranzasPeriodo($this->request->data['Cobranza']['consorcio_id'])[$this->request->data['Cobranza']['tipos']];

            $this->set('interes', $this->Cobranza->Propietario->Consorcio->getInteres($this->request->data['Cobranza']['consorcio_id']));
            $this->set('data', ['cobranzas' => $cobranzas,
                'vencimiento' => $this->Cobranza->Propietario->Consorcio->Liquidation->getLastBloqueadaVencimiento($this->request->data['Cobranza']['consorcio_id'], $this->request->data['Cobranza']['tipos']),
                'pftcargados' => $this->Cobranza->GastosParticularesPft->listarCargados($cobranzas)]);
        }
        $this->set('tipos', $this->Cobranza->User->Client->LiquidationsType->getLiquidationsTypes($_SESSION['Auth']['User']['client_id']));
        $this->set('consorcios', $this->Cobranza->Propietario->Consorcio->getConsorciosList());
    }

    public function getCobranzasPeriodo() {
        if (!$this->request->is('ajax')) {
            die();
        }
        $this->layout = '';
        $this->autoRender = false;
        die(json_encode(['consorcios' => $this->Cobranza->getCobranzasPeriodo($this->request->data['c']), 'limite' => $this->Cobranza->Propietario->Consorcio->Liquidation->getLastBloqueadaClosedDate($this->request->data['c'])]));
    }

    /*
     * Permite descargar el archivo enviado por PLAPSA con los pagos de un dia específico
     */

    public function download($ruta = null, $c = null) {
        $cliente = $this->Cobranza->Propietario->Aviso->_decryptURL($c);
        if (empty($cliente)) {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['action' => 'index']);
        }
        $dir = APP . WEBROOT_DIR . DS . 'plapsa' . DS . basename($cliente) . DS;
        if (isset($cliente) && $cliente === $_SESSION['Auth']['User']['Client']['code'] && preg_match('/^([-\.\w]+)$/', $ruta) > 0 && !empty($ruta) && is_file($dir . $ruta)) {
            $this->response->file($dir . $ruta, ['download' => true, 'name' => basename($ruta)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null) {
        if (!$this->Cobranza->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $resul = $this->Cobranza->undo($id);
        if (empty($resul)) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser anulado. ') . $resul);
        }
        return $this->redirect($this->referer());
    }

    public function borrarMultiple() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $ids = $this->request->data['ids'];
        $cont = 0;
        foreach ($ids as $v) {
            if (!$this->Cobranza->canEdit($v)) {
                $cont++;
                continue;
            }
            $this->Cobranza->id = $v;
            $this->request->allowMethod('post', 'delete');
            $resul = $this->Cobranza->undo($v);
            if (!empty($resul)) {
                $cont++;
            }
        }
        $cantelementos = count($ids);
        $anuladas = $cantelementos - $cont;
        if ($anuladas > 0) {
            $this->Flash->success(__('Se anularon correctamente ' . $anuladas . ' Cobranzas'));
        }
        if ($cont > 0) {
            $this->Flash->error(__('No se anularon ' . $cont . ' Cobranzas'));
        }
        $this->layout = '';
        $this->autoRender = false;
    }

}
