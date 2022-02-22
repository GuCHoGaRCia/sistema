<?php

header("Access-Control-Allow-Origin: https://ceonline.com.ar"); // para q desde el panel Propietario puedan ver archivos y consultas, sino no funciona ajax (porq estoy en ceonline.com.ar/p/?)
header('Access-Control-Allow-Methods: POST,GET');
header('Access-Control-Allow-Headers: x-requested-with');
App::uses('AppController', 'Controller');

class ConsultaspropietariosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('view', 'getConsultas', 'setConsultas', 'setArchivo', 'getArchivos', 'download', 'getConsultasPropietario', 'setConsultasPropietario', 'setUnseen');
        array_push($this->Security->unlockedActions, 'getConsultasPropietario', 'getConsultas', 'setConsultas', 'setArchivo', 'getArchivos', 'setConsultasPropietario', 'setUnseen');
        /* debug($this->request->data['link']);
          debug($this->request->params['form']['link']); */
        // si es un Propietario en su panel (no existe $_SESSION y la accion es alguna de estas, chequeo q el link esté correcto
        if (!isset($_SESSION['Auth']['User']['client_id']) && in_array($this->request['action'], ['getConsultas', 'setConsultas', 'setArchivo', 'getArchivos', 'download', 'getConsultasPropietario', 'setConsultasPropietario'])) {
            // acceso por link de propietario
            if (isset($this->request->data['link']) || isset($this->request->params['form']['link']) || isset($this->request->params['pass'][2])) {
                $link = isset($this->request->data['link']) ? $this->request->data['link'] : (isset($this->request->params['pass'][2]) ? $this->request->params['pass'][2] : $this->request->params['form']['link']);
                // es un propietario entrando con el link
                $email = $this->Consultaspropietario->Client->Consorcio->Propietario->Aviso->_decryptURL($link); // email del propiet
                $emails = explode(',', $email);
                if (empty($emails)) {
                    die("2");
                }
                foreach ($emails as $e) {
                    if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                        die("2");
                    }
                }
                $pids = $this->Consultaspropietario->Client->Consorcio->Propietario->getPropietarioIdFromEmail($email, 'all'); // pueden ser varios propietario_id (varios de distintos clientes). keys=propiet_id, values=consorcio_id
                if (empty($pids)) {
                    die("2");
                }
            } else {
                die("3"); //no envio el link, quien es?? 
            }
        }
    }

    public function index() {
        $this->Paginator->settings = ['conditions' => ['Consultaspropietario.client_id' => $_SESSION['Auth']['User']['client_id'], 'c2.propietario_id IS NULL', $this->Consultaspropietario->parseCriteria($this->passedArgs)],
            'joins' => [['table' => 'consultaspropietarios', 'alias' => 'c2', 'type' => 'left', 'conditions' => ['Consultaspropietario.propietario_id=c2.propietario_id', 'Consultaspropietario.id<c2.id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'right', 'conditions' => ['Consultaspropietario.propietario_id=Propietario.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'right', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]],
            'order' => 'Consultaspropietario.id desc', // dejar orden x id, porq se agrupan por propietario_id, y me muestra primero la ultima
            'group' => 'Consultaspropietario.propietario_id',
            'fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code', 'Consorcio.name', 'Consultaspropietario.id', 'Consultaspropietario.mensaje', 'Consultaspropietario.created', 'Consultaspropietario.seen']];
        $this->Prg->commonProcess();
        $this->set('consultaspropietarios', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Consultaspropietario->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $client_id = $email = null;
        if (isset($this->request->data['p'])) {
            // es un administrador utilizando el sistema
            $client_id = $_SESSION['Auth']['User']['client_id'];
        }
        if (isset($this->request->data['link'])) {
            // es un propietario entrando con el link
            $email = $this->Consultaspropietario->Client->Consorcio->Propietario->Aviso->_decryptURL($this->request->data['link']); // email del propiet
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("El dato es inexistente");
            }
            $pids = $this->Consulta->Client->Consorcio->Propietario->getPropietarioIdFromEmail($email, 'all'); // pueden ser varios propietario_id (varios de distintos clientes). keys=propiet_id, values=consorcio_id
            if (empty($pids)) {
                die("El dato es inexistente");
            }
        }
        $this->Consultaspropietario->id = $id;
        $propietario_id = $this->Consultaspropietario->field('propietario_id');
        $consorcio_id = $this->Consultaspropietario->Propietario->getPropietarioConsorcio($propietario_id);
        //$prop = [$propietario_id => $consorcio_id];
        $cli = empty($client_id) ? $this->Consultaspropietario->Propietario->Consorcio->getConsorcioClientId($consorcio_id) : $client_id;

        $this->set('consultas', json_encode($this->Consultaspropietario->getConsultasPropietario($propietario_id, $cli)));
        $consultasadjuntos = $this->Consultaspropietario->Client->Consultaspropietariosadjunto->getArchivos($propietario_id, $cli);
        $this->set('consultasadjuntos', json_encode($consultasadjuntos));
        $this->set('formasdepago', $this->Consultaspropietario->Client->Formasdepago->find('list'));
        $this->set('datospropietario', $this->Consultaspropietario->Propietario->find('first', ['conditions' => ['Propietario.id' => $propietario_id], 'fields' => ['Propietario.name', 'Propietario.unidad', 'Consorcio.name', 'Consorcio.id'], 'recursive' => 0]));
        $this->set('pid', $propietario_id);
        $this->set('cl', $cli);
        if (!empty($email)) {
            $this->set('link', utf8_encode($this->Consulta->Client->Consorcio->Propietario->Aviso->_encryptURL($email)));
        } else {
            $this->set('link', '');
        }

        //die($this->render("/consultaspropietarios/view"));
    }

    public function add() {
        
    }

    /*
     * Devuelve las consultas del propietario
     * $this->request->data['p'] -> busca por el id del propietario
     */

    public function getConsultasPropietario() {
        if (!$this->request->is('ajax')) {
            die();
        }
        $client_id = $email = null;
        if (isset($this->request->data['p'])) {
            // es un administrador utilizando el sistema
            $client_id = $_SESSION['Auth']['User']['client_id'];
        }
        if (isset($this->request->data['link'])) {
            // es un propietario entrando con el link
            $email = $this->Consultaspropietario->Client->Consorcio->Propietario->Aviso->_decryptURL($this->request->data['link']); // email del propiet
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("El dato es inexistente");
            }
            $pids = $this->Consulta->Client->Consorcio->Propietario->getPropietarioIdFromEmail($email, 'all'); // pueden ser varios propietario_id (varios de distintos clientes). keys=propiet_id, values=consorcio_id
            if (empty($pids)) {
                die("El dato es inexistente");
            }
        }

        $prop = empty($pids) ? $this->request->data['p'] : $pids;
        $cli = empty($client_id) ? array_values($pids) : $client_id;

        $this->set('consultas', json_encode($this->Consultaspropietario->getConsultasPropietario($prop, $cli)));
        $consultasadjuntos = $this->Consultaspropietario->Client->Consultaspropietariosadjunto->getArchivos($prop, $cli);
        $this->set('consultasadjuntos', json_encode($consultasadjuntos));
        $this->set('datospropietario', $this->Consultaspropietario->Propietario->find('first', ['conditions' => ['Propietario.id' => $prop], 'fields' => ['Propietario.name', 'Propietario.unidad', 'Consorcio.name'], 'recursive' => 0]));
        $this->set('pid', $prop);
        $this->set('cl', $cli);
        if (!empty($email)) {
            $this->set('link', utf8_encode($this->Consulta->Client->Consorcio->Propietario->Aviso->_encryptURL($email)));
        } else {
            $this->set('link', '');
        }

        $this->layout = '';
        die($this->render("/Consultaspropietarios/view"));
    }

    public function getConsultas() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultaspropietario->getConsultasPropietario($this->request->data['p'], $this->request->data['cl'])));
    }

    public function getArchivos() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultaspropietario->Client->Consultaspropietariosadjunto->getArchivos($this->request->data['p'], @$this->request->data['cl'])));
    }

    public function setConsultasPropietario() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultaspropietario->setConsultasPropietario($this->request->data['c'], @$this->request->data['cl'], @$this->request->data['p'], @$this->request->data['l'])));
    }

    public function setArchivo() {
        die(json_encode($this->Consultaspropietario->Client->Consultaspropietariosadjunto->setArchivo($this->request->params['form'], @$this->request->data['cli'], $this->request->data['p'])));
    }

    public function download($name = null, $cli = null) {
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Consultaspropietario->Client->getClientIdFromMultipleEmails($this->Consultaspropietario->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
        $name = $this->Consultaspropietario->Client->Consorcio->Propietario->Aviso->_decryptURL($name);
        if (preg_match('/^([-\.\w]+)$/', $name) > 0 && strpos($name, '../') === false && is_file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cliente) . DS . 'consultas' . DS . $name)) {
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cliente) . DS . 'consultas' . DS . basename($name), ['download' => true, 'name' => basename($name)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            return $this->redirect(['controller' => 'consultas', 'action' => 'view', $cli]);
        }
    }

    public function setUnseen() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Consultaspropietario->setUnseen($this->request->data['pid'])));
    }

    public function delete($id = null) {
        $this->Consultaspropietario->id = $id;
        if (!$this->Consultaspropietario->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Consultaspropietario->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
