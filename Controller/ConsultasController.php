<?php

header("Access-Control-Allow-Origin: http://ceonline.com.ar"); // para q desde el panel Propietario puedan ver archivos y consultas, sino no funciona ajax (porq estoy en ceonline.com.ar/p/?)
header('Access-Control-Allow-Methods: POST,GET');
header('Access-Control-Allow-Headers: x-requested-with');
App::uses('AppController', 'Controller');

class ConsultasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('view', 'getConsultas', 'setConsulta', 'setArchivo', 'getArchivos', 'download', 'setUnseen');
        array_push($this->Security->unlockedActions, 'getConsultas', 'setConsulta', 'setArchivo', 'getArchivos', 'panel_getConsultas', 'panel_setConsulta', 'panel_setArchivo', 'panel_getArchivos', 'panel_download', 'verificar', 'setUnseen'); // permito blackhole x ajax
    }

    public function index() {
        
    }

    public function panel_index() {
        $clients = $this->Consulta->Client->find('list', ['conditions' => ['Client.enabled' => 1], 'order' => 'Client.name']);
        $this->set('clients', $clients);
    }

    public function view($link) {
        $email = $this->Consulta->Client->Consorcio->Propietario->Aviso->_decryptURL($link);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("El dato es inexistente");
        }
        $client_id = $this->Consulta->Client->getClientIdFromMultipleEmails($email);
        if (empty($client_id)) {
            die("El dato es inexistente");
        }

        $this->layout = 'backendotrosclientes';
        $this->set('name', $this->Consulta->Client->getClientName($client_id));
        $this->set('link', $this->Consulta->Client->Consorcio->Propietario->Aviso->_encryptURL($email));
    }

    public function getConsultas() {
        if (!$this->request->is('ajax')) {
            die();
        }

        die(json_encode($this->Consulta->getConsultas(@$this->request->data['cli'])));
    }

    public function setConsulta() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consulta->setConsulta($this->request->data['c'], @$this->request->data['cli'])));
    }

    public function verificar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consulta->verificar()));
    }

    public function setUnseen() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consulta->setUnseen(isset($this->request->data['cli']) ? $this->request->data['cli'] : null)));
    }

    public function download($name = null, $cli = null) {
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Consulta->Client->getClientIdFromMultipleEmails($this->Consulta->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
        $name2 = $this->Consulta->Client->Consorcio->Propietario->Aviso->_decryptURL($name);
        if (is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2)) {
            //$this->Consulta->Client->Consultasadjunto->updateAll(['Consultasadjunto.modified' => "'" . date("Y-m-d H:i:s") . "'"], ['Consultasadjunto.url' => $name]); // para q en auditoria quede en auditoría q se descargó el archivo
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2, ['download' => true, 'name' => basename($name2)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['controller' => 'consultas', 'action' => 'view', $cli]);
        }
    }

    public function setArchivo() {
        die(json_encode($this->Consulta->Client->Consultasadjunto->setArchivo($this->request->params['form'], @$this->request->data['cli'])));
    }

    public function getArchivos() {
        die(json_encode($this->Consulta->Client->Consultasadjunto->getArchivos(@$this->request->data['cli'])));
    }

    public function panel_getConsultas() {
        $this->getConsultas();
    }

    public function panel_setConsulta() {
        $this->setConsulta();
    }

    public function panel_getArchivos() {
        $this->getArchivos();
    }

    public function panel_setArchivo() {
        $this->setArchivo();
    }

    public function panel_download($name = null, $cli = null) {
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Consulta->Client->getClientIdFromMultipleEmails($this->Consulta->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
        $name2 = $this->Consulta->Client->Consorcio->Propietario->Aviso->_decryptURL($name);
        if (is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2)) {
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2, ['download' => true, 'name' => basename($name2)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['controller' => 'consultas', 'action' => 'index', 'panel' => true]);
        }
    }

}
