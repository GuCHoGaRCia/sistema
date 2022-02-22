<?php

App::uses('AppController', 'Controller');

class PropietariosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getPropietarios', 'edit');
        if (isset($this->request->data['filter']['consorcio'])) {
            $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']] = $this->request->data['filter']['consorcio'];
        }
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Propietario->parseCriteria($this->passedArgs)];
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]) && $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']] === "") {
            unset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]);
        }
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']])) {
            $conditions += ['Consorcio.id' => $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'fields' => ['Propietario.id', 'Propietario.code', 'Propietario.orden', 'Propietario.name', 'Propietario.email', 'Propietario.address', 'Propietario.postal_address', 'Propietario.telephone', 'Propietario.whatsapp',
                'Propietario.city', 'Propietario.postal_city', 'Propietario.unidad', 'Propietario.imprime_resumen_cuenta', 'Propietario.sistema_online', 'Propietario.exceptua_interes', 'Propietario.miembrodelconsejo', 'Consorcio.name', 'Consorcio.id'],
            'contain' => ['Consorcio'],
            'order' => 'Consorcio.code,Propietario.consorcio_id,Propietario.orden,Propietario.code'
        );
        @$this->Prg->commonProcess();
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']])) {
            $this->Paginator->settings += ['limit' => 1000];
        } else {
            $this->Paginator->settings += ['limit' => 20, 'maxLimit' => 20];
        }
        // para exportar
        $_SESSION['exportar'] = $this->Paginator->settings + ['page' => isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 0];
        $_SESSION['exportar']['fields'] = ['Consorcio.name as Consorcio', 'Consorcio.code as Codigo', 'Propietario.code as Codigo', 'Propietario.orden as Orden', 'Propietario.name as Nombre', 'Propietario.email as Email', 'Propietario.postal_address as Direccion', 'Propietario.telephone as Telefono', 'Propietario.whatsapp as WhatsApp'
            , 'Propietario.postal_city as Ciudad', 'Propietario.unidad as Unidad'];

        $this->set('propietarios', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Propietario->Consorcio->getConsorciosList());
    }

    // Utilizada para el reporte del informe deuda de propietario, en datos->propietarios

    public function informedeudapropietario() {
        if (!isset($this->request->data['Propietario']['propid']) || !$this->Propietario->canEdit($this->request->data['Propietario']['propid'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $propietario_id = $this->request->data['Propietario']['propid'];

        $l1 = $this->request->data['Propietario']['l1'] ?? die("El dato es inexistente");
        $l2 = $this->request->data['Propietario']['l2'] ?? die("El dato es inexistente");

        if (!$this->Propietario->Consorcio->Liquidation->canEdit($l1) || !$this->Propietario->Consorcio->Liquidation->canEdit($l2)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($l1 == 0 || $l2 == 0) {
            $this->Flash->error(__('Debe seleccionar Liquidaci&oacute;n inicial y final'));
            return $this->redirect($this->referer());
        }
        if ($l2 < $l1) {
            $this->Flash->error(__('La Liquidaci&oacute;n inicial debe ser menor o igual a la final'));
            return $this->redirect($this->referer());
        }

        $consorcio_id = $this->Propietario->getPropietarioConsorcio($propietario_id);
        $liquidaciones = $this->Propietario->Consorcio->Liquidation->getLiquidationsSegunPrefijo($consorcio_id, 0);

        $idliq = key($liquidaciones);
        $consorcio = $this->Propietario->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'fields' => ['name', 'address', 'city', 'cuit']]);

        $saldospropietario = $liqselegidas = [];
        $liqselegidas[$l2] = $liquidaciones[$l2];
        $saldospropietario[$l2] = $this->Propietario->SaldosCierre->getSaldo($l2, $propietario_id, true);
        $liq_id = $l2;

        while ($liq_id != $l1) {
            $liq_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($liq_id);
            $liqselegidas[$liq_id] = $liquidaciones[$liq_id];
            $saldospropietario[$liq_id] = $this->Propietario->SaldosCierre->getSaldo($liq_id, $propietario_id, true);
        }

        if ($liquidaciones[$l1]['inicial']) {
            $saldospropietario[$l1] = $saldospropietario[$l1][$propietario_id];
        } else {
            $liqAnterior_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($l1);
            $liqAnterior = $liquidaciones[$liqAnterior_id];
            $saldosPropietarioLiqAnterior = $this->Propietario->SaldosCierre->getSaldo($liqAnterior_id, $propietario_id, true);
        }

        $client = $this->Propietario->Cobranza->User->Client->getClientInfo($idliq);
        $this->set('cliente', $client);
        $this->set('datosPropietario', $this->Propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.code', 'Propietario.name', 'Propietario.address', 'Propietario.city', 'Propietario.unidad', 'Propietario.superficie', 'Propietario.poligono']], $propietario_id));
        $this->set('liquidaciones', $liquidaciones);
        $this->set('saldospropietario', $saldospropietario);
        $this->set('liqselegidas', array_reverse($liqselegidas, true));  // son las liquidaciones del rango seleccionado para el informe de deuda
        $this->set('liqAnterior', $liqAnterior ?? '');
        $this->set('saldosPropietarioLiqAnterior', $saldosPropietarioLiqAnterior[$propietario_id] ?? '');
        $this->set('propietario_id', $propietario_id);
        $this->set('consorcio', $consorcio);

        $this->layout = '';
    }

// Utilizada para el reporte del informe deuda de propietario extraordinaria, en datos->propietarios

    public function informedeudapropietarioext() {
        if (!isset($this->request->data['Propietario']['propid']) || !$this->Propietario->canEdit($this->request->data['Propietario']['propid'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $propietario_id = $this->request->data['Propietario']['propid'];

        $l1 = $this->request->data['Propietario']['l1'] ?? die("El dato es inexistente");
        $l2 = $this->request->data['Propietario']['l2'] ?? die("El dato es inexistente");

        if (!$this->Propietario->Consorcio->Liquidation->canEdit($l1) || !$this->Propietario->Consorcio->Liquidation->canEdit($l2)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($l1 == 0 || $l2 == 0) {
            $this->Flash->error(__('Debe seleccionar Liquidaci&oacute;n inicial y final'));
            return $this->redirect($this->referer());
        }
        if ($l2 < $l1) {
            $this->Flash->error(__('La Liquidaci&oacute;n inicial debe ser menor o igual a la final'));
            return $this->redirect($this->referer());
        }

        $consorcio_id = $this->Propietario->getPropietarioConsorcio($propietario_id);
        $liquidaciones = $this->Propietario->Consorcio->Liquidation->getLiquidationsSegunPrefijo($consorcio_id, 5);

        $idliq = key($liquidaciones);
        $consorcio = $this->Propietario->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'fields' => ['name', 'address', 'city', 'cuit']]);

        $saldospropietario = $liqselegidas = [];
        $liqselegidas[$l2] = $liquidaciones[$l2];
        $saldospropietario[$l2] = $this->Propietario->SaldosCierre->getSaldo($l2, $propietario_id, true);
        $liq_id = $l2;

        while ($liq_id != $l1) {
            $liq_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($liq_id);
            $liqselegidas[$liq_id] = $liquidaciones[$liq_id];
            $saldospropietario[$liq_id] = $this->Propietario->SaldosCierre->getSaldo($liq_id, $propietario_id, true);
        }

        if ($liquidaciones[$l1]['inicial']) {
            $saldospropietario[$l1] = $saldospropietario[$l1][$propietario_id];
        } else {
            $liqAnterior_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($l1);
            $liqAnterior = $liquidaciones[$liqAnterior_id];
            $saldosPropietarioLiqAnterior = $this->Propietario->SaldosCierre->getSaldo($liqAnterior_id, $propietario_id, true);
        }

        $client = $this->Propietario->Cobranza->User->Client->getClientInfo($idliq);
        $this->set('cliente', $client);
        $this->set('datosPropietario', $this->Propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.code', 'Propietario.name', 'Propietario.address', 'Propietario.city', 'Propietario.unidad', 'Propietario.superficie', 'Propietario.poligono']], $propietario_id));
        $this->set('liquidaciones', $liquidaciones);
        $this->set('saldospropietario', $saldospropietario);
        $this->set('liqselegidas', array_reverse($liqselegidas, true));  // son las liquidaciones del rango seleccionado para el informe de deuda
        $this->set('liqAnterior', $liqAnterior ?? '');
        $this->set('saldosPropietarioLiqAnterior', $saldosPropietarioLiqAnterior[$propietario_id] ?? '');
        $this->set('propietario_id', $propietario_id);
        $this->set('consorcio', $consorcio);

        $this->layout = '';
    }

    // Utilizada para el reporte del informe deuda de propietario fondo, en datos->propietarios

    public function informedeudapropietariofondo() {
        if (!isset($this->request->data['Propietario']['propid']) || !$this->Propietario->canEdit($this->request->data['Propietario']['propid'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $propietario_id = $this->request->data['Propietario']['propid'];

        $l1 = $this->request->data['Propietario']['l1'] ?? die("El dato es inexistente");
        $l2 = $this->request->data['Propietario']['l2'] ?? die("El dato es inexistente");

        if (!$this->Propietario->Consorcio->Liquidation->canEdit($l1) || !$this->Propietario->Consorcio->Liquidation->canEdit($l2)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($l1 == 0 || $l2 == 0) {
            $this->Flash->error(__('Debe seleccionar Liquidaci&oacute;n inicial y final'));
            return $this->redirect($this->referer());
        }
        if ($l2 < $l1) {
            $this->Flash->error(__('La Liquidaci&oacute;n inicial debe ser menor o igual a la final'));
            return $this->redirect($this->referer());
        }

        $consorcio_id = $this->Propietario->getPropietarioConsorcio($propietario_id);
        $liquidaciones = $this->Propietario->Consorcio->Liquidation->getLiquidationsSegunPrefijo($consorcio_id, 9);

        $idliq = key($liquidaciones);
        $consorcio = $this->Propietario->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'fields' => ['name', 'address', 'city', 'cuit']]);

        $saldospropietario = $liqselegidas = [];
        $liqselegidas[$l2] = $liquidaciones[$l2];
        $saldospropietario[$l2] = $this->Propietario->SaldosCierre->getSaldo($l2, $propietario_id, true);
        $liq_id = $l2;

        while ($liq_id != $l1) {
            $liq_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($liq_id);
            $liqselegidas[$liq_id] = $liquidaciones[$liq_id];
            $saldospropietario[$liq_id] = $this->Propietario->SaldosCierre->getSaldo($liq_id, $propietario_id, true);
        }

        if ($liquidaciones[$l1]['inicial']) {
            $saldospropietario[$l1] = $saldospropietario[$l1][$propietario_id];
        } else {
            $liqAnterior_id = $this->Propietario->Consorcio->Liquidation->getLastLiquidation($l1);
            $liqAnterior = $liquidaciones[$liqAnterior_id];
            $saldosPropietarioLiqAnterior = $this->Propietario->SaldosCierre->getSaldo($liqAnterior_id, $propietario_id, true);
        }

        $client = $this->Propietario->Cobranza->User->Client->getClientInfo($idliq);
        $this->set('cliente', $client);
        $this->set('datosPropietario', $this->Propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.code', 'Propietario.name', 'Propietario.address', 'Propietario.city', 'Propietario.unidad', 'Propietario.superficie', 'Propietario.poligono']], $propietario_id));
        $this->set('liquidaciones', $liquidaciones);
        $this->set('saldospropietario', $saldospropietario);
        $this->set('liqselegidas', array_reverse($liqselegidas, true));  // son las liquidaciones del rango seleccionado para el informe de deuda
        $this->set('liqAnterior', $liqAnterior ?? '');
        $this->set('saldosPropietarioLiqAnterior', $saldosPropietarioLiqAnterior[$propietario_id] ?? '');
        $this->set('propietario_id', $propietario_id);
        $this->set('consorcio', $consorcio);

        $this->layout = '';
    }

    // Utilizada para el reporte de propietario deudor, en datos->propietarios

    public function reportemultas($propietario_id) {
        if (!$this->Propietario->canEdit($propietario_id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $consorcio_id = $this->Propietario->getPropietarioConsorcio($propietario_id);
        $liquidaciones = $this->Propietario->Consorcio->Liquidation->getLiquidationsSegunPrefijo($consorcio_id, 0);
        $consorcio = $this->Propietario->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'fields' => ['name', 'address', 'city', 'cuit']]);
        $propCapitalInteres = $multa = [];

        foreach ($liquidaciones as $k => $v) {
            $liquidationTypeId = $this->Propietario->Consorcio->Liquidation->getLiquidationsTypeId($k);
            $propCapitalInteres[$k] = $this->Propietario->Cobranza->obtienepropdeudoresCapitalInteres($consorcio_id, $liquidationTypeId, $k, $propietario_id);
            $multa[$k] = $this->Propietario->GastosParticularesMulta->getMultaLiquidacion($propietario_id, $k);
        }

        $this->set('liquidationTypeName', $this->Propietario->Cobranza->User->Client->LiquidationsType->getLiquidationsTypesName($liquidationTypeId)['LiquidationsType']['name']);
        $this->set('liquidaciones', $liquidaciones);
        $this->set('propCapitalInteres', $propCapitalInteres);
        $this->set('multa', $multa);
        $this->set('propietario_id', $propietario_id);
        $this->set('consorcio', $consorcio);

        $this->layout = '';
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Propietario->create();
            if ($this->Propietario->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado. Continuar la carga de Propietarios del consorcio'));
                return $this->redirect(array('action' => 'add', $this->request->data['Propietario']['consorcio_id']));
            } else {
                $this->Flash->error(__('No se han cargado los Tipos de liquidaciones. Agregue Tipos de liquidaci&oacute;n e intente nuevamente.'));
            }
        }

        if ($this->Propietario->Consorcio->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']))) == 0) {
            $this->Flash->error(__('Debe agregar un consorcio (men&uacute; Datos) antes de crear un Propietario'));
            return $this->redirect(['controller' => 'consorcios', 'action' => 'add']);
        }

        // en caso q haya guardado un propietario, redirijo al "add" y selecciono el ultimo consorcio utilizado para q se haga m�s facil la carga
        $consorcios = $this->Propietario->Consorcio->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], (isset($this->request->params['pass'][0]) ? ['Consorcio.id' => $this->request->params['pass'][0]] : []))));
        $this->set(compact('consorcios'));
    }

    public function edit($id = null) {
        if (!$this->Propietario->canEdit($id)) {
            die('El dato es inexistente');
        }
        $this->layout = '';
        if (!empty($this->request->data)) {
            if ($this->Propietario->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado.'));
                die(json_encode(['e' => 0]));
            } else {
                die(json_encode(['e' => 1, 'd' => $this->Propietario->validationErrors]));
            }
        } else {
            $options = array('conditions' => array('Propietario.' . $this->Propietario->primaryKey => $id));
            $this->request->data = $this->Propietario->find('first', $options);
        }
        $consorcios = $this->Propietario->Consorcio->find('list', array('conditions' => array('Consorcio.id' => $this->request->data['Propietario']['consorcio_id'])));
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        if (!$this->Propietario->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Propietario->id = $id;
        if ($this->Propietario->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Redirecciona desde "Propietarios" a una url de acceso al panel del propietario
     */
    public function link($id = null) {
        if (!$this->Propietario->canEdit($id)) {
            die(__('El dato es inexistente'));
        }
        $email = $this->Propietario->getPropietarioEmail($id);
        $this->redirect("https://ceonline.com.ar/link/?" . $this->Propietario->Aviso->_encryptURL($email));
    }

    /*
     * Utilizada para obtener los propietarios en cobranzas
     */

    public function getPropietarios() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Propietario->getList($this->request->data['q'])));
    }

    public function buscarPropietario() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Propietario->get($this->request->query['q'])));
    }

}
