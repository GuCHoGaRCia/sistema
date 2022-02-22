<?php

App::uses('AppController', 'Controller');

class ReportsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $check = false;
        //if (!isset($_SESSION['Auth']['User']['client_id']) && (!isset($this->request->params['pass']) || count($this->request->params['pass']) < 3) || ($this->request->params['action'] === 'resumencuenta' && !isset($this->request->params['pass'][3]))) {
        //    die("El dato es inexistente");
        //}
        //* VERIFICAR EL EMAIL CIFRADO!!!!!!! q pertenezca a un email de un Prop del cliente
        //* RC=verificar el email param(2) con el propiet_id param(1) q coincidan y sean del cliente param(3)
        if (!in_array($this->request->params['action'], ['cuentacorrientepropietario', 'analiticogastos', 'reimpresioncupon', 'recibosliquidacion'])) {
            if (isset($_SESSION['Auth']['User']['client_id']) && isset($this->request->params['pass'][0])) {
                if ($this->request->params['action'] == 'edconsorcio') {// el 1º parametro es el consorcio. Reportes desde menu consorcios
                    $check = $this->Report->checkClient2($this->request->params['pass'][0], $_SESSION['Auth']['User']['client_id']);
                } else {// el primer parametro es la liqudiacion. Reportes desde menu liquidacion
                    $check = $this->Report->checkClient($this->request->params['pass'][0], $_SESSION['Auth']['User']['client_id']);
                }
            }
            if (!isset($this->request->params['pass'][0]) || !isset($_SESSION['Auth']['User']['is_admin']) || $_SESSION['Auth']['User']['is_admin'] || !$check) {
                // puede q sea un cliente accediendo al link
                if (isset($this->request->params['pass'][3])) {//desde el panel, abre rc y quito el ultimo parametro, sale x error 2
                    $check = $this->Report->checkClient($this->request->params['pass'][0], $this->request->params['pass'][3]);
                    if (!$check) {
                        die("El dato es inexistente<!--1-->");
                    }
                }
            }
            // chequeo q sea un propietario accediendo al link y q vea el resumencuenta,compos y resgastos suyo (o de su consorcio)
            if (!isset($_SESSION['Auth']['User']['client_id']) && in_array($this->request->params['action'], ['resumencuenta', 'resumengastos', 'composicionsaldos'])) {
                $pid = isset($this->request->params['pass'][1]) ? $this->request->params['pass'][1] : null;
                $link = isset($this->request->params['pass'][2]) ? $this->request->params['pass'][2] : null;
                $client_id = isset($this->request->params['pass'][3]) ? $this->request->params['pass'][3] : null;
                if (empty($link) || empty($client_id) || empty($pid)) {
                    die("El dato es inexistente<!--2-->"); //es propietario en el Panel abriendo rescta,resgastos o compos, sin parametro pid, link o client_id
                }
                $check = $this->Report->checkClient($this->request->params['pass'][0], $client_id);
                if (!$check) {
                    die("El dato es inexistente<!--3-->");
                }
                $check = $this->Report->checkClient3($this->request->params['pass'][0], $pid, $link);
                if (!$check) {
                    die("El dato es inexistente<!--4-->");
                }
            }
        }

        // si esta logueado (y no es admin (cola impresion)) y toquetea los numeros de estos reportes
        if (isset($_SESSION['Auth']['User']['client_id']) && !$_SESSION['Auth']['User']['is_admin']) {
            $rep = ['composicionsaldos', 'resumencuenta', 'resumengastos', 'gastosparticularesporcuenta', 'planillapagos', 'cuentacorrienteliquidacion', 'edliquidacion', 'resumenperiodo', 'cobranzasrecibidas', 'propietariosdeudores', 'propietariosacreedores', 'recibosliquidacion'];
            if (isset($this->request->params['action']) && in_array($this->request->params['action'], $rep) && isset($this->request->params['pass'][0])) {// esta logueado, verifico q la composicion sea del cliente
                $liq = ClassRegistry::init('Liquidation');
                $client_id = $liq->Consorcio->getConsorcioClientId($liq->getConsorcioId($this->request->params['pass'][0]));
                if (empty($client_id) || $_SESSION['Auth']['User']['client_id'] != $client_id) {
                    die("El dato es inexistente<!--5-->");
                }
            }
        }

        $this->Auth->allow('resumencuenta', 'resumengastos', 'composicionsaldos');
    }

    public function panel_index() {
        $this->Report->recursive = 0;
        $this->Paginator->settings = array('conditions' => array($this->Report->parseCriteria($this->passedArgs)), 'order' => 'Report.name');
        $this->Prg->commonProcess();
        $this->set('reports', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Report->create();
            if ($this->Report->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function panel_delete($id = null) {
        $this->Report->id = $id;
        if (!$this->Report->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Report->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /*
     * Genera los resumenes de cuenta
     */

    public function resumenesdecuentas($liquidation_id, $client_id) {
        $this->Reportes->resumenesdecuentas($liquidation_id, $client_id);
    }

    /*
     * Genera los resumenes de cuenta en PDF
     */

    public function resumenesdecuentaspdf($liquidation_id, $client_id) {
        $this->Reportes->resumenesdecuentaspdf($liquidation_id, $client_id);
    }

    /*
     * Genera los resumenes de cuenta de 1 propietario solo
     */

    public function resumencuenta($liquidation_id, $propietario_id, $link = null, $client_id = null) {
        $this->Reportes->resumencuenta($liquidation_id, $propietario_id, $link, $client_id);
    }

    public function reimpresioncupon() {
        $this->Reportes->reimpresioncupon($this->request->data['Propietario'], $_SESSION['Auth']['User']['client_id']);
    }

    /*
     * Genera el resumen de gastos
     */

    public function resumengastos($liquidation_id, $client_id = null) {
        $this->Reportes->resumengastos($liquidation_id, $client_id);
    }

    /*
     * Genera la composicion de saldos
     */

    public function composicionsaldos($consorcio_id, $client_id = null) {
        $this->Reportes->composicionsaldos($consorcio_id, $client_id);
    }

    /*
     * Genera el estado de disponibilidad del consorcio

      Ejemplo de $this->request->data['Consorcio']:
      array(
      'edc' => '1',
      'cid' => '302',
      'propid' => '',
      'l1' => '28-id:12781',
      'l2' => '30-id:13648'
      )
     */

    public function edconsorcio() {
        $controlOrdenL1 = intval(substr($this->request->data['Consorcio']['l1'], 0, strpos($this->request->data['Consorcio']['l1'], '-')));
        $controlOrdenL2 = intval(substr($this->request->data['Consorcio']['l2'], 0, strpos($this->request->data['Consorcio']['l2'], '-')));
        if (!isset($controlOrdenL1) || !isset($controlOrdenL2)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect($this->referer());
        }
        if ($controlOrdenL1 == 0 || $controlOrdenL2 == 0) {
            $this->Flash->error(__('Debe seleccionar Liquidaci&oacute;n inicial y final'));
            return $this->redirect($this->referer());
        }
        if ($controlOrdenL2 < $controlOrdenL1) {
            $this->Flash->error(__('La Liquidaci&oacute;n de inicio debe ser menor o igual a la de fin'));
            return $this->redirect($this->referer());
        }
        $idL1 = intval(substr($this->request->data['Consorcio']['l1'], strpos($this->request->data['Consorcio']['l1'], ':') + 1));
        $idL2 = intval(substr($this->request->data['Consorcio']['l2'], strpos($this->request->data['Consorcio']['l2'], ':') + 1));
        if (!$this->Report->Colaimpresione->Client->Consorcio->Liquidation->canEdit($idL1) || !$this->Report->Colaimpresione->Client->Consorcio->Liquidation->canEdit($idL2)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect($this->referer());
        }
        $this->Reportes->edconsorcio($this->request->data['Consorcio']['cid'], $idL1, $idL2, $_SESSION['Auth']['User']['client_id']);
    }

    /*
     * Genera el estado de disponibilidad de la liquidacion
     */

    public function edliquidacion($liquidation_id, $client_id = null) {
        $this->Reportes->edliquidacion($liquidation_id, $client_id);
    }

    /*
     * Genera el Resumen del Periodo de la liquidacion
     */

    public function resumenperiodo($liquidation_id, $client_id = null) {
        $this->Reportes->resumenperiodo($liquidation_id, $client_id);
    }

    /*
     * Genera la Planilla de Pagos de la liquidacion actual
     */

    public function planillapagos($liquidation_id, $client_id = null) {
        $this->Reportes->planillapagos($liquidation_id, $client_id);
    }

    /*
     * Genera el Listado de Propietarios y sus datos
     */

    public function propietariosdatos($consorcio_id) {
        $this->Reportes->propietariosdatos($consorcio_id);
    }

    /*
     * Genera la Planilla de Firmas del Consorcio actual
     */

    public function planillafirmas($consorcio_id) {
        $this->Reportes->planillafirmas($consorcio_id);
    }

    /*
     * Genera la Planilla de Gastos Particulares para completar del Consorcio actual
     */

    public function planillaparticulares($consorcio_id) {
        $this->Reportes->planillaparticulares($consorcio_id);
    }

    /*
     * Genera el listado de propietarios con sus coeficientes
     */

    public function coeficientespropietarios($consorcio_id) {
        $this->Reportes->coeficientespropietarios($consorcio_id);
    }

    /*
     * Genera el listado de propietarios deudores
     */

    public function propietariosdeudores($liquidation_id, $consorcio_id, $client_id = null) {
        $this->Reportes->propietariosdeudores($liquidation_id, $consorcio_id, $client_id);
    }

    /*
     * Genera el listado de propietarios acreedores (ver 3º parametro)
     */

    public function propietariosacreedores($liquidation_id, $consorcio_id, $client_id = null) {
        $this->Reportes->propietariosdeudores($liquidation_id, $consorcio_id, $client_id, 'propietariosacreedores');
    }

    /*
     * Genero los recibos para q los administradores entreguen a los propietarios (con el importe de la liquidacion actual).
     * Si esta seteado $triple, se genera el recibo triple (en 1 hoja, 3 recibos para un mismo propietario
     */

    public function recibosliquidacion($liquidation_id, $client_id = null, $triple = null) {
        $this->Reportes->recibosliquidacion($liquidation_id, $client_id, $triple);
    }

    /*
     * Genera la Cuenta corriente del propietario actual.
     * Al abrir cobranza manual muestro la cuenta corriente de los ultimos 3 meses x defecto ($this->request->data['f1']...)
     */

    public function cuentacorrientepropietario($model = 'Propietario') {
        $f1 = explode('/', isset($this->request->data[$model]['f1']) ? $this->request->data[$model]['f1'] : (isset($this->request->data['f1']) ? $this->request->data['f1'] : die("No se encuentra prorrateada la liquidaci&oacute;n")));
        $f2 = explode('/', isset($this->request->data[$model]['f2']) ? $this->request->data[$model]['f2'] : (isset($this->request->data['f2']) ? $this->request->data['f2'] : die("No se encuentra prorrateada la liquidaci&oacute;n")));
        if (!checkdate($f1[1], $f1[0], $f1[2]) || !checkdate($f2[1], $f2[0], $f2[2]) || strtotime($f1[2] . "-" . $f1[1] . "-" . $f1[0]) > strtotime($f2[2] . "-" . $f2[1] . "-" . $f2[0])) {
            $this->Flash->error(__('La fecha de inicio debe ser menor o igual a la de fin'));
            return $this->redirect($this->referer());
        }       
        $origen = $this->request->data['origen'] ?? '0';  // origen se setea con el valor 1 si se entra a cobranzas manuales o al agregar un ajuste desde (cobranzas->ajustes), esto es cuando se muestra la cuenta corriente x defecto
        $pid = isset($this->request->data[$model]['pid']) ? $this->request->data[$model]['pid'] : $this->request->data['pid'];
        $this->Reportes->cuentacorrientepropietario($pid, $f1[2] . "-" . $f1[1] . "-" . $f1[0], $f2[2] . "-" . $f2[1] . "-" . $f2[0], $origen);
    }

    public function analiticogastos() {
        if ($this->request->data['Consorcio']['l1'] == 0 || $this->request->data['Consorcio']['l2'] == 0) {
            $this->Flash->error(__('Debe seleccionar Liquidaci&oacute;n inicial y final'));
            return $this->redirect($this->referer());
        }
        if ($this->request->data['Consorcio']['l2'] < $this->request->data['Consorcio']['l1']) {
            $this->Flash->error(__('La Liquidaci&oacute;n de inicio debe ser menor o igual a la de fin'));
            return $this->redirect($this->referer());
        }
        if (!$this->Report->Colaimpresione->Client->Consorcio->Liquidation->canEdit($this->request->data['Consorcio']['l1']) || !$this->Report->Colaimpresione->Client->Consorcio->Liquidation->canEdit($this->request->data['Consorcio']['l2'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect($this->referer());
        }
        $this->Reportes->analiticogastos($this->request->data['Consorcio']['l1'], $this->request->data['Consorcio']['l2']);
    }

    /*
     * Genera la Cuenta corriente de liquidacion actual. Parecido al resumen de cuenta pero solamente con el detalle y sin codigos de barras y demas
     */

    public function cuentacorrienteliquidacion($liquidation_id) {
        $this->Reportes->cuentacorrienteliquidacion($liquidation_id);
    }

    /*
     * Genera la Cuenta corriente del proveedor actual.
     * Al abrir pago proveedor muestro la cuenta corriente de los ultimos 3 meses x defecto ($this->request->data['f1']...)
     */

    public function cuentacorrienteproveedor() {
        $f1 = explode('/', isset($this->request->data['Proveedor']['f1']) ? $this->request->data['Proveedor']['f1'] : $this->request->data['f1']);
        $f2 = explode('/', isset($this->request->data['Proveedor']['f2']) ? $this->request->data['Proveedor']['f2'] : $this->request->data['f2']);
        if (!checkdate($f1[1], $f1[0], $f1[2]) || !checkdate($f2[1], $f2[0], $f2[2]) || strtotime($f1[2] . "-" . $f1[1] . "-" . $f1[0]) > strtotime($f2[2] . "-" . $f2[1] . "-" . $f2[0])) {
            $this->Flash->error(__('La fecha de inicio debe ser menor o igual a la de fin'));
            return $this->redirect($this->referer());
        }
        $pid = isset($this->request->data['Proveedor']['pid']) ? $this->request->data['Proveedor']['pid'] : $this->request->data['pid'];
        $this->Reportes->cuentacorrienteproveedor($pid, $f1[2] . "-" . $f1[1] . "-" . $f1[0], $f2[2] . "-" . $f2[1] . "-" . $f2[0]);
    }

    /*
     * Genero el reporte Cobranzas liquidacion. Son las cobranzas recibidas en esta liquidacion (desde la fecha de cierre de la ultima hasta la fecha actual)
     */

    public function cobranzasrecibidas($liquidation_id) {
        $this->Reportes->cobranzasrecibidas($liquidation_id);
    }

    /*
     * Muestra los gastos particulares de liquidacion actual agrupados por cuenta
     */

    public function gastosparticularesporcuenta($liquidation_id) {
        $this->Reportes->gastosparticularesporcuenta($liquidation_id);
    }

}
