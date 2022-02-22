<?php

App::uses('AppController', 'Controller');

class ReparacionessupervisoresController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'tieneReparacionesPendientes'); // permito blackhole x ajax
        $this->Auth->allow('view', 'view2', 'd');
        if (($this->request->is('get') || $this->request->is('post')) && in_array($this->request['action'], ['view', 'view2', 'd']) && isset($this->request->pass[0])) {
            // acceso por link de supervisor
            $this->email = $this->Reparacionessupervisore->Client->Consorcio->Propietario->Aviso->_decryptURL($this->request->pass[0]);
            if (empty($this->email) || filter_var($this->email, FILTER_VALIDATE_EMAIL) === FALSE) {
                die(__('El dato es inexistente'));
            }
            $this->client_id = $this->Reparacionessupervisore->getClientId($this->email);
        }
    }

    public function index() {
        $this->Reparacionessupervisore->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Reparacionessupervisore.client_id' => $_SESSION['Auth']['User']['client_id']], 'order' => 'Reparacionessupervisore.nombre'];
        //$this->Prg->commonProcess();
        $this->set('reparacionessupervisores', $this->paginar($this->Paginator));
    }

    public function view() {
        if (empty($this->email)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }
        $supervisor = $this->Reparacionessupervisore->getSupervisorId($this->email);
        if (empty($supervisor)) {
            header("HTTP/1.0 404 Not Found");
            die;
        }
        $this->layout = 'supervisor';
        if (isset($this->request->data['Reparacionessupervisore']['estado']) && $this->request->data['Reparacionessupervisore']['estado'] === "") {
            unset($this->request->data['Reparacionessupervisore']);
        }
        $estado = 1;
        if (isset($this->request->data['Reparacionessupervisore']['estado'])) {
            $estado = $this->request->data['Reparacionessupervisore']['estado'];
        }
        $this->set('link', $this->request->pass[0]);
        $this->set('estado', $estado);
        $this->set('estados', $this->Reparacionessupervisore->Client->Consorcio->Reparacione->Reparacionesestado->get());
        $this->set('reparaciones', $this->Reparacionessupervisore->Client->Consorcio->Reparacione->getReparacionesSupervisor($supervisor['Reparacionessupervisore']['id'], $estado));
        if (!isset($_SESSION['Auth']['User']['client_id'])) {// actualizo solo cuando no esta logueado al sistema (el Supervisor ingresó por su panel desde el mail)
            $this->Reparacionessupervisore->id = $supervisor['Reparacionessupervisore']['id']; //actualizo ultimo acceso
            $this->Reparacionessupervisore->saveField('ultimoacceso', date("Y-m-d H:i:s"));
        }
    }

    public function view2($link, $reparacione_id = null) {
        $options = array('conditions' => array('Reparacione.id' => $reparacione_id), 'contain' => ['Reparacionesestado.nombre',
                'Reparacionesactualizacione', 'Reparacionesactualizacione.Reparacionesestado.nombre', 'Reparacionesactualizacione.Reparacionesactualizacionesadjunto',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Llave.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Proveedor.name',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Reparacionessupervisore.nombre',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Propietario.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionessupervisore', 'Consorcio.name', 'Consorcio.client_id', 'Propietario.name2'], 'recursive' => 0);
        $this->set('reparaciones', $this->Reparacionessupervisore->Client->Consorcio->Reparacione->find('first', $options));
        $proveedors = $reparacionessupervisores = $users = [];
        foreach ($this->client_id as $v) {
            $proveedors += $this->Reparacionessupervisore->Client->Proveedor->getList($v);
            $reparacionessupervisores += $this->Reparacionessupervisore->Client->Reparacionessupervisore->getList($v);
            $users += $this->Reparacionessupervisore->Client->User->getList($v);
        }

        $this->set(compact('proveedors', 'reparacionessupervisores', 'link', 'users'));
        $this->layout = '';
    }

    function d($link, $name = null) {
        if (empty($name) || empty($link)) {
            // si entra es q quiso descargar un adjunto con un link invalido
            die;
        }
        $name = $this->Reparacionessupervisore->Client->Consorcio->Reparacione->Consorcio->Propietario->Aviso->_decryptURL($name);
        $ruta = APP . WEBROOT_DIR . DS . 'files' . DS . $this->client_id . DS . 'rep' . DS;
        if (preg_match('/^([-\.\w]+)$/', $name) > 0 && is_file($ruta . basename($name))) {
            // nuevo en cake 2.3 http://book.cakephp.org/2.0/en/controllers/request-response.html#cake-response-file
            $this->response->file($ruta . basename($name), ['download' => true, 'name' => basename($name)]);
            return $this->response;
        } else {
            die(__('El archivo no pudo ser descargado<script>setTimeout(function(){window.close();},3000);</script>'));
        }
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Reparacionessupervisore->create();
            if ($this->Reparacionessupervisore->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->Reparacionessupervisore->Client->find('list');
        $this->set(compact('clients'));
    }

    /* public function edit($id = null) {
      if (!$this->Reparacionessupervisore->exists($id) || $this->Reparacionessupervisore->find('count', array('conditions' => array('Reparacionessupervisore.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacionessupervisore.id' => $id))) == 0) {
      $this->Flash->error(__('El dato es inexistente'));
      return $this->redirect(['action' => 'index']);
      }
      if ($this->request->is(['post', 'put'])) {
      if ($this->Reparacionessupervisore->save($this->request->data)) {
      $this->Flash->success(__('El dato fue guardado'));
      return $this->redirect(['action' => 'index']);
      } else {
      $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
      }
      } else {
      $options = ['conditions' => ['Reparacionessupervisore.' . $this->Reparacionessupervisore->primaryKey => $id]];
      $this->request->data = $this->Reparacionessupervisore->find('first', $options);
      }
      $clients = $this->Reparacionessupervisore->Client->find('list');
      $this->set(compact('clients'));
      } */

    public function link($id = null) {
        if (!$this->Reparacionessupervisore->exists($id) || $this->Reparacionessupervisore->find('count', array('conditions' => array('Reparacionessupervisore.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacionessupervisore.id' => $id))) == 0) {
            die(__('El dato es inexistente'));
        }
        $email = explode(',', $this->Reparacionessupervisore->getEmail($id));
        $this->redirect(array('controller' => 'Reparacionessupervisores', 'action' => 'view', $this->Reparacionessupervisore->Client->Consorcio->Propietario->Aviso->_encryptURL($email[0])));
        //$this->redirect("http://ceonline.com.ar/sup/?" . $this->Reparacionessupervisore->Client->Aviso->_encryptURL($email[0]));
    }

    public function deshabilitar() {
        if (!$this->request->is('ajax')) {
            die;
        }
        $id = $this->request->data['id'];
        if (!$this->Reparacionessupervisore->exists($id) || $this->Reparacionessupervisore->find('count', array('conditions' => array('Reparacionessupervisore.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacionessupervisore.id' => $id))) == 0) {
            die;
        }
        die(json_encode($this->Reparacionessupervisore->deshabilitar($id)));
    }

}
