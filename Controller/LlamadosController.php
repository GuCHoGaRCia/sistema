<?php

App::uses('AppController', 'Controller');

class LlamadosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $clients = $this->Llamado->Client->find('list', ['order' => 'Client.name']);
        $this->set('clients', $clients);
    }

    public function panel_getLlamados() {
        if (!$this->request->is('ajax')) {
            die();
        }

        die(json_encode($this->Llamado->getLlamados(@$this->request->data['cli'])));
    }

    public function panel_setLlamado() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Llamado->setLlamado($this->request->data['c'], @$this->request->data['cli'])));
    }

    public function panel_getArchivos() {
        die(json_encode($this->Llamado->Client->Llamadosadjunto->getArchivos(@$this->request->data['cli'])));
    }

    public function panel_setArchivo() {
        die(json_encode($this->Llamado->Client->Llamadosadjunto->setArchivo($this->request->params['form'], @$this->request->data['cli'])));
    }

    public function panel_download($name = null, $cli = null) {
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Llamado->Client->getClientIdFromMultipleEmails($this->Llamado->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
        $name2 = $this->Llamado->Client->Consorcio->Propietario->Aviso->_decryptURL($name);
        if (is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2)) {
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas' . DS . $name2, ['download' => true, 'name' => basename($name2)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['controller' => 'consultas', 'action' => 'index', 'panel' => true]);
        }
    }

}
