<?php

App::uses('AppController', 'Controller');

class LiquidationsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getLiquidaciones', 'multiprorrateo'); // permito blackhole x ajax
        if (isset($this->request->data['filter']['consorcio'])) {
            $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']] = $this->request->data['filter']['consorcio'];
        }
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.inicial' => 0, 'Liquidation.bloqueada' => 0, $this->Liquidation->parseCriteria($this->passedArgs)];
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]) && $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']] === "") {
            unset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]);
        }
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']])) {
            $conditions += ['Consorcio.id' => $_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']]];
            unset($conditions['Liquidation.bloqueada']);
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions,
            'fields' => array('Liquidation.*', 'Consorcio.id', 'Consorcio.name', 'LiquidationsType.id', 'LiquidationsType.name', 'LiquidationsType.prefijo'), 'contain' => ['LiquidationsType', 'Consorcio'],
            'order' => 'Liquidation.cerrada desc,Liquidation.bloqueada,Liquidation.closed desc,Liquidation.id desc,Liquidation.modified desc'];
        @$this->Prg->commonProcess();
        $this->Paginator->settings['order'] = 'Liquidation.created desc';
        if (isset($_SESSION['filtro'][$this->request->params['controller'] . $this->request->params['action']])) {
            $this->Paginator->settings += ['limit' => 400];
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('liquidations', $this->paginar($this->Paginator));
        $consorcios = $this->Liquidation->Consorcio->getConsorciosList();
        $this->set('activas', $this->Liquidation->getLiquidationsActivasIds());
        $this->set('consorcios', $consorcios);
        $this->set('client_id', $_SESSION['Auth']['User']['client_id']);

        // para exportar
        $_SESSION['exportar'] = $this->Paginator->settings + ['page' => isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 0];
        $_SESSION['exportar']['fields'] = ['Consorcio.name as Consorcio', 'LiquidationsType.name as Tipo', 'Liquidation.periodo as Periodo', 'Liquidation.vencimiento as Vencimiento', 'Liquidation.limite as Limite'];
    }

    public function panel_index() {
        $conditions = [$this->Liquidation->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Consorcio.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = array('conditions' => $conditions,
            'fields' => array('Liquidation.*', 'Consorcio.id', 'Consorcio.name', 'LiquidationsType.id', 'LiquidationsType.name', 'Client.name', 'Client.id'),
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]],
            'order' => 'Client.name,Liquidation.id desc',
            'contain' => ['LiquidationsType', 'Consorcio'],
            'recursive' => 0);
        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('cliente', $this->Liquidation->Consorcio->Client->find('list'));
        $this->set('liquidations', $this->paginar($this->Paginator));
    }

    public function verPagos($id) {
        if (!$this->Liquidation->exists($id) || $this->Liquidation->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.id' => $id), 'recursive' => 0)) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->layout = '';
        $this->set('periodo', $this->Liquidation->getPeriodo($id));
        $this->set('consorcio', $this->Liquidation->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $this->Liquidation->getConsorcioId($id)], 'recursive' => 0]));
        $this->set('pagos', $this->Liquidation->Consorcio->Client->Proveedor->Proveedorspago->getPagosReporteVerPagos($id));
    }

    public function add() {
        if ($this->request->is('post')) {
            if (!$this->Liquidation->Consorcio->exists($this->request->data['Liquidation']['consorcio_id'])) {
                $this->Flash->error(__('Liquidación inválida'));
                return $this->redirect(['action' => 'index']);
            }
            if (!$this->Liquidation->LiquidationsType->exists($this->request->data['Liquidation']['liquidations_type_id'])) {
                $this->Flash->error(__('Tipo de liquidación inválida'));
                return $this->redirect(['action' => 'index']);
            }
            $this->add2();
        }
        if ($this->Liquidation->Consorcio->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']))) == 0) {
            $this->Flash->error(__('Debe agregar un consorcio (men&uacute; Datos) antes de crear una Liquidaci&oacute;n'));
            return $this->redirect(['action' => 'index']);
        }
        $consorcios = $this->Liquidation->Consorcio->getConsorciosList();
        $liquidationsTypes = $this->Liquidation->LiquidationsType->getLiquidationsTypes();
        $this->set(compact('consorcios', 'liquidationsTypes'));
    }

    public function add2() {
        if ($this->request->is('post') && isset($this->request->data['Liquidation']['liquidation_id'])) { // last liquidation id
            $this->Liquidation->create();
            if ($this->Liquidation->saveAll($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        if ($this->Liquidation->Consorcio->Coeficiente->find('count', array('conditions' => array('Coeficiente.consorcio_id' => $this->request->data['Liquidation']['consorcio_id'], 'Coeficiente.enabled' => 1))) == 0) {
            $this->Flash->error(__('Debe agregar Coeficientes (men&uacute; Datos) antes de crear una Liquidaci&oacute;n'));
            return $this->redirect(['action' => 'index']);
        }
        $consorcios = $this->Liquidation->Consorcio->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $this->request->data['Liquidation']['consorcio_id'])));
        $liquidationsTypes = $this->Liquidation->LiquidationsType->find('list', array('conditions' => array('LiquidationsType.client_id' => $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.id' => $this->request->data['Liquidation']['liquidations_type_id'])));
        $liquidations = $this->Liquidation->getLiquidations($this->request->data['Liquidation']['consorcio_id'], $this->request->data['Liquidation']['liquidations_type_id']);
        $period = $this->Liquidation->getPeriodos($this->request->data['Liquidation']['consorcio_id'], $this->request->data['Liquidation']['liquidations_type_id']);
        $coeficientes = $this->Liquidation->Consorcio->Coeficiente->find('list', array('conditions' => array('Coeficiente.consorcio_id' => $this->request->data['Liquidation']['consorcio_id'], 'Coeficiente.enabled' => 1)));
        $presupuestos = [];
        foreach ($liquidations as $k => $v) {
            $presupuestos[$k] = [];
            foreach ($coeficientes as $k1 => $v1) {
                $presupuestos[$k][$k1] = $this->Liquidation->Liquidationspresupuesto->getPresupuesto($k, $k1);
            }
        }
        $this->set(compact('consorcios', 'liquidationsTypes', 'liquidations', 'coeficientes', 'period', 'presupuestos'));
        $this->render('add2');
    }

    public function controlesCierres($id = null) {
        if (!$this->Liquidation->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('get');
        $resul = $this->Liquidation->doControls($id);

        if ($resul != "") {
            $this->Flash->error($resul);
            return $this->redirect(['action' => 'index']);
        }

        $chequesconsaldo = $this->Liquidation->Consorcio->Client->Caja->Cheque->getSaldoChequesPendientes(true);
        if ($chequesconsaldo !== []) {
            $this->Flash->error(__("Existen cheques en uso y con saldo pendiente de utilizar. Verifique los mismos por favor e intente nuevamente"));
            return $this->redirect(['controller' => 'cajas', 'action' => 'index']);
        }
        $this->Flash->success(__('Los controles se realizaron correctamente'));
        $this->Flash->success(__('Se termin&oacute; de prorratear la liquidaci&oacute;n'));
        $this->Liquidation->prorrateo($id);
        $this->redirect($this->referer());
    }

    public function multiprorrateo() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $error = false;
        $chequesconsaldo = $this->Liquidation->Consorcio->Client->Caja->Cheque->getSaldoChequesPendientes(true);
        if ($chequesconsaldo !== []) {
            $this->Flash->error(__("Existen cheques en uso y con saldo pendiente de utilizar. Verifique los mismos por favor e intente nuevamente"));
            $error = true;
        }
        $ids = $this->request->data['ids'];
        $ids = array_reverse($ids); // al prorratear varios, los deja casi en el mismo orden que estaban al seleccionarlos, sino los da vuelta a todos
        foreach ($ids as $v) {
            // si ocurre algun error corto el proceso porque es pesado
            if (!$this->Liquidation->canEdit($v)) {
                $this->Flash->error(__('El dato es inexistente'));
                $error = true;
                break;
            }
            $resul = $this->Liquidation->doControls($v);
            if ($resul != "") {
                $this->Flash->error($resul);
                $error = true;
                break;
            }
        }

        if (!$error) {
            // todo salió bien, prorrateo todas
            foreach ($ids as $v) {
                $this->Liquidation->prorrateo($v);
            }
        }

        if (!$error) {
            $this->Flash->success(__('Se prorratearon exitosamente <span title="' . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 2) . '">' . count($ids) . '</span> Liquidaciones'));
        }

        $this->layout = '';
        $this->autoRender = false;
    }

    public function nuevaLiquidacion($liquidation_id, $consor, $type) {
        $consorcios = $this->Liquidation->Consorcio->find('list', array('conditions' => array('Consorcio.id' => $consor)));
        $liquidationsTypes = $this->Liquidation->LiquidationsType->find('list', array('conditions' => array('LiquidationsType.id' => $type)));
        $liquidations = $this->Liquidation->find('list', array('conditions' => array('Liquidation.id' => $liquidation_id)));
        $coeficientes = $this->Liquidation->Consorcio->Coeficiente->find('list', array('conditions' => array('Coeficiente.consorcio_id' => $consor, 'Coeficiente.enabled' => 1)));
        $period = $this->Liquidation->getPeriodos($consor, $type);
        $this->set(compact('consorcios', 'liquidationsTypes', 'liquidations', 'coeficientes', 'period'));
        $this->render('guardarnueva');
    }

    public function guardarnueva() {
        if ($this->request->is('post') && isset($this->request->data['Liquidation']['liquidation_id'])) { // last liquidation id
            $this->Liquidation->create();
            if ($this->Liquidation->saveAll($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Liquidation->Consorcio->find('list', array('conditions' => array('Consorcio.id' => $this->request->data['Liquidation']['consorcio_id'])));
        $liquidationsTypes = $this->Liquidation->LiquidationsType->find('list', array('conditions' => array('LiquidationsType.id' => $this->request->data['Liquidation']['liquidations_type_id'])));
        $liquidations = $this->Liquidation->getLiquidations($this->request->data['Liquidation']['consorcio_id'], $this->request->data['Liquidation']['liquidations_type_id']);
        $this->set(compact('consorcios', 'liquidationsTypes', 'liquidations'));
        $this->render('guardarNueva');
    }

    public function panel_guardarnueva() {
        $this->guardarnueva();
    }

    /*
     * Devuelve la lista de las Liquidaciones para el Analitico de Gastos o Estado Disponibilidad General que estan en consorcios/index
     * O el informe de deuda en propietarios/index
     */

    public function getLiquidaciones() {
        if (!$this->request->is('ajax') || !isset($this->data['c'])) {
            die();
        }
        if (!$this->Liquidation->Consorcio->canEdit($this->data['c'])) {
            die();
        }
        if (isset($this->data['origenllamada'])) {
            $origen = $this->data['origenllamada'];
            if ($origen == 1) {                 // viene desde el informe de deuda en propietarios/index
                if (!$this->Liquidation->Consorcio->Propietario->canEdit($this->data['propid'])) {
                    die();
                }
                die(json_encode($this->Liquidation->SaldosCierre->getSaldosYLiqsPropietarioDeuda($this->data['c'], $this->data['propid'])['periodos']));
            }
            if ($origen == 2) {             // viene desde el Analitico de Gastos en consorcios/index
                die(json_encode($this->Liquidation->getLiquidations($this->data['c'], $this->Liquidation->LiquidationsType->getLiquidationsTypeIdFromPrefijo(0), 0)));
            }
            if ($origen == 3) {             // viene desde el estado de disponiblidad general en consorcios/index
                die(json_encode($this->Liquidation->getLiquidationsEdc($this->data['c'])));
            }
            if ($origen == 4) {                 // viene desde el informe de deuda extraordinaria en propietarios/index
                if (!$this->Liquidation->Consorcio->Propietario->canEdit($this->data['propid'])) {
                    die();
                }
                die(json_encode($this->Liquidation->SaldosCierre->getSaldosYLiqsPropietarioDeuda($this->data['c'], $this->data['propid'], 5)['periodos']));
            }
            if ($origen == 5) {                 // viene desde el informe de deuda fondo en propietarios/index
                if (!$this->Liquidation->Consorcio->Propietario->canEdit($this->data['propid'])) {
                    die();
                }
                die(json_encode($this->Liquidation->SaldosCierre->getSaldosYLiqsPropietarioDeuda($this->data['c'], $this->data['propid'], 9)['periodos']));
            }
        }
        die();
    }

    public function delete($id = null) {
        $this->Liquidation->id = $id;
        if (!$this->Liquidation->exists() || $this->Liquidation->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.id' => $id), 'recursive' => 0)) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Liquidation->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function panel_delete($id = null) {
        $this->Liquidation->id = $id;
        if (!$this->Liquidation->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Liquidation->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
