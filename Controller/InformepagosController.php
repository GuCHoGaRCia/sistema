<?php

header("Access-Control-Allow-Origin: https://ceonline.com.ar"); // para q desde el panel Propietario puedan ver archivos y consultas, sino no funciona ajax (porq estoy en ceonline.com.ar/p/?)
header('Access-Control-Allow-Methods: POST,GET');
header('Access-Control-Allow-Headers: x-requested-with');
App::uses('AppController', 'Controller');

class InformepagosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('setInformePago', 'getInformePago', 'getArchivos', 'download');
        array_push($this->Security->unlockedActions, 'setInformePago', 'getInformePago', 'getArchivos', 'download');
    }

    /*
     * Muestro solamente los informes de pagos que no hayan sido verificados o rechazados
     */

    public function index() {
        $conditions = ['Informepago.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Informepago->parseCriteria($this->passedArgs)];
        $conditions += isset($this->request->data['Informepago']['consorcio']) && $this->request->data['Informepago']['consorcio'] !== '0' ? ['Propietario.consorcio_id' => $this->request->data['Informepago']['consorcio']] : [];
        $conditions += isset($this->request->data['Informepago']['formasdepago']) && $this->request->data['Informepago']['formasdepago'] !== '0' ? ['Informepago.formasdepago_id' => $this->request->data['Informepago']['formasdepago']] : [];
        $conditions += isset($this->request->data['Informepago']['verificado']) && $this->request->data['Informepago']['verificado'] == '1' ? [] : ['Informepago.verificado' => 0];
        $conditions += isset($this->request->data['Informepago']['rechazado']) && $this->request->data['Informepago']['rechazado'] == '1' ? [] : ['Informepago.rechazado' => 0];
        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'informepagosadjuntos', 'alias' => 'Informepagosadjunto', 'type' => 'left', 'conditions' => ['Informepago.id=Informepagosadjunto.informepago_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']]],
            'fields' => ['distinct (Informepago.id)', 'Consorcio.name', 'Propietario.name', 'Propietario.unidad', 'Propietario.code', 'Informepago.fecha', 'Informepago.created', 'Informepago.importe', 'Informepago.verificado', 'Informepago.rechazado', 'Informepago.motivorechazo', 'Banco.name', 'Informepago.observaciones', 'Informepago.operacion', 'Formasdepago.forma'],
            'order' => 'Informepago.created desc,Consorcio.name,Informepago.propietario_id',
            'recursive' => 1];

        //debug($this->Paginator->settings);die;
        if (!isset($this->request->data['Informepago']) || empty($this->request->data['Informepago'])) {
            $this->Paginator->settings += ['limit' => 10];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('informepagos', $this->paginar($this->Paginator));
        $this->set('formasdepago', $this->Informepago->Formasdepago->find('list', ['conditions' => ['Formasdepago.client_id' => $_SESSION['Auth']['User']['client_id']]]));
        $this->set('consorcios', $this->Informepago->Client->Consorcio->getConsorciosList());
    }

    public function view() {
        if ($this->request->is('post')) {
            $desde = substr($this->request->data['Informepago']['desde'], 6, 4) . "-" . substr($this->request->data['Informepago']['desde'], 3, 2) . "-" . substr($this->request->data['Informepago']['desde'], 0, 2);
            $hasta = substr($this->request->data['Informepago']['hasta'], 6, 4) . "-" . substr($this->request->data['Informepago']['hasta'], 3, 2) . "-" . substr($this->request->data['Informepago']['hasta'], 0, 2);
            $conditions = ['Informepago.client_id' => $_SESSION['Auth']['User']['client_id'], 'date(Informepago.created) >=' => $desde, 'date(Informepago.created) <=' => $hasta];
            $conditions += isset($this->request->data['Informepago']['verificado']) && $this->request->data['Informepago']['verificado'] !== '1' ? ['Informepago.verificado' => 1] : [];
            $this->set('informepagos', $this->Informepago->find('all', ['conditions' => $conditions,
                        'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']]],
                        'recursive' => 1,
                        'fields' => ['distinct (Informepago.id)', 'Consorcio.name', 'Propietario.name', 'Propietario.unidad', 'Informepago.fecha', 'Informepago.verificado', 'Informepago.importe', 'Banco.name', 'Informepago.observaciones', 'Informepago.operacion', 'Formasdepago.forma'],
                        'order' => 'Informepago.created desc,Consorcio.name,Informepago.propietario_id']));
            $this->set('desde', $this->request->data['Informepago']['desde']);
            $this->set('hasta', $this->request->data['Informepago']['hasta']);
        } else {
            $conditions = ['Informepago.client_id' => $_SESSION['Auth']['User']['client_id'], 'date(Informepago.created) >=' => date("Y-m-01"), 'date(Informepago.created) <=' => date("Y-m-d"), 'Informepago.verificado' => 1];
            $this->set('informepagos', $this->Informepago->find('all', ['conditions' => $conditions,
                        'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']]],
                        'recursive' => 1,
                        'fields' => ['distinct (Informepago.id)', 'Consorcio.name', 'Propietario.name', 'Propietario.unidad', 'Informepago.fecha', 'Informepago.verificado', 'Informepago.importe', 'Banco.name', 'Informepago.observaciones', 'Informepago.operacion', 'Formasdepago.forma'],
                        'order' => 'Informepago.created desc,Consorcio.name,Informepago.propietario_id']));
        }
    }

    public function delete($id = null) {
        $this->Informepago->id = $id;
        if (!$this->Informepago->exists() || $this->Informepago->find('count', ['conditions' => ['Informepago.client_id' => $_SESSION['Auth']['User']['client_id'], 'Informepago.id' => $id], 'recursive' => 0]) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Informepago->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function getInformePago() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Informepago->getInformePago($this->request->data['p'], $this->request->data['cl'])));
    }

    public function setInformePago() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Informepago->setInformePago($this->request->data, $this->request->form))); //$this->request->data: fecha, importe, etc.... $this->request->form: los comprobantes adjuntos
    }

    /*
     * En Cobranzas->Informe pagos propietarios, rechazo un pago informado debido a 'm'
     */

    public function rechazar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Informepago->rechazar($this->request->data['i'], $this->request->data['m'])));
    }

    /*
     * En Cobranzas->Informe pagos propietarios, cancelo el rechazo un pago informado
     */

    public function undorechazar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Informepago->undorechazar($this->request->data['i'], $this->request->data['m'])));
    }

    public function delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultasadjunto->delAdjunto($this->request->data['id'], $this->request->data['cli'])));
    }

    /*
     * Permite descargar uno de los comprobantes de pago de los propietarios
     * Si es un usuario de CEONLINE usa: ruta, 'c', client_id
     * Si es un Propietario desde su Panel usa: ruta, propietario_id, link
     */

    public function download($name = null, $pid = null, $link = null) {
        if ($pid !== 'c') {
            $m = $this->Informepago->Client->Consorcio->Propietario->Aviso->_decryptURL($link);
            $cliente = $this->Informepago->Propietario->Consorcio->getConsorcioClientId($this->Informepago->Propietario->getPropietarioConsorcio($pid));
        } else {
            $cliente = $link;
        }
        $name = $this->Informepago->Client->Consorcio->Propietario->Aviso->_decryptURL($name);
        // si $m y el mail es valido entra, O si existe el archivo, tambien
        if ((preg_match('/^([-\.\w]+)$/', $name) > 0 && strpos($name, '../') === false || isset($m) && filter_var($m, FILTER_VALIDATE_EMAIL) !== FALSE) && is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name)) {
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . basename($name), ['download' => true, 'name' => basename($name)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            if ($pid === 'c') {
                // viene desde informepagos/index (es un Administrador viendo los informes)
                return $this->redirect(['action' => 'index']);
            } else {
                // viene del panel propietario, intenta bajar un comprobante adjuntado hace tiempo
                return $this->redirect(['controller' => 'avisos', 'action' => 'view', $link]);
            }
        }
    }

}
