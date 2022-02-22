<?php

App::uses('AppController', 'Controller');
App::uses('UserProfilesController', 'Controller');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->User->recursive = 0;
        $this->Paginator->settings = array('conditions' => array('Client.id' => $_SESSION['Auth']['User']['client_id'], $this->User->parseCriteria($this->passedArgs)),
            'fields' => ['User.id', 'User.name', 'User.username', 'User.lastseen', 'User.password', 'User.enabled', 'Client.identificador_cliente']);
        $this->Prg->commonProcess();
        $this->set('users', $this->paginar($this->Paginator));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function panel_index() {
        $this->User->recursive = 0;
        $this->Paginator->settings = array('conditions' => array($this->User->parseCriteria($this->passedArgs)), 'order' => 'Client.name,User.name');
        $this->Prg->commonProcess();
        $this->set('users', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $clients = $this->User->Client->find('list');
        $u = new UserProfilesController();
        $profiles = $u->UserProfile->getList();
        $this->set(compact('clients', 'profiles'));
    }

    public function panel_delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->User->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function login() {
        if ($this->request->is('post')) {
            if (substr($_SERVER["REMOTE_ADDR"], 0, 10) != '192.168.0.' && $_SERVER["REMOTE_ADDR"] != '::1') {
                if (!isset($this->request->data['User']['token']) || $this->request->data['User']['token'] === "") {
                    return false;
                }
                $recaptcha_secret = "6LcAemkUAAAAAPozIOYbC3Ir7wNDPUpauJKyajiL";
                $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . urlencode($this->request->data['User']['token'])), true);
                if ($response["success"] !== true) {
                    return false;
                }
            }

            // para limitar el acceso de un usuario desde una IP
            //if (in_array($this->request->data['User']['username'], ['ekeegan@ceonlinemdp']) && substr($_SERVER["REMOTE_ADDR"], 0, 10) != '192.168.0.' && $_SERVER["REMOTE_ADDR"] != '::1' && $_SERVER["REMOTE_ADDR"] != "181.231.115.147") {
            //    $this->Flash->error(__('No es posible ingresar desde su ubicaci&oacute;n'));
            //    return $this->redirect('/users/login');
            //}
            //debug(AuthComponent::password($this->request->data['User']['password']));die;
            if ($this->Auth->login()) {
                // actualizo fecha y hora del ultimo logueo
                $this->User->save(['id' => $_SESSION['Auth']['User']['id'], 'lastseen' => date("Y-m-d H:i:s"), 'cantidadlogueosincorrectos' => 0], false);
                return $this->_login_controls();
            } else {
                $this->User->logueoIncorrecto($this->request->data['User']['username']);
                $this->Flash->error(__('Usuario o contrase&ntilde;a inv&aacute;lidos o usuario deshabilitado'));
                return $this->redirect('/users/login');
            }
        }
        // si esta logueado y vuelve al login, lo manda a /clients
        if ($this->Auth->loggedIn()) {
            if ($_SESSION['Auth']['User']['is_admin'] == 1) {
                return $this->redirect('/panel/clients/control');
            } else {
                if (!$_SESSION['Auth']['User']['aceptaterminosycondiciones']) {
                    return $this->redirect('/users/tyc');
                } else {
                    return $this->redirect('/noticias');
                }
            }
        }
    }

    public function logout() {
//        unset($_SESSION['filtro']);
//        unset($_SESSION['Auth']);
//        unset($_SESSION['_Token']);
        session_destroy();
        return $this->redirect($this->Auth->logout());
    }

    public function panel_logout() {
        session_destroy();
        return $this->redirect($this->Auth->logout());
    }

    private function _login_controls() {
        if (!$_SESSION['Auth']['User']['Client']['enabled']) {
            // el cliente se encuentra deshabilitado
            $this->Flash->error(__('El cliente se encuentra deshabilitado, cont&aacute;ctese con su administrador'));
            return $this->Auth->logout();
        }
        if (!$_SESSION['Auth']['User']['enabled']) {
            $this->Flash->error(__('Su usuario se encuentra deshabilitado, cont&aacute;ctese con su administrador'));
            return $this->Auth->logout();
        }

        if ($_SESSION['Auth']['User']['is_admin'] == 1) {
            return $this->redirect('/panel/clients/control');
        } else {
            if (!$_SESSION['Auth']['User']['aceptaterminosycondiciones']) {
                return $this->redirect('/users/tyc');
            } else {
                return $this->redirect('/noticias');
            }
        }
    }

    public function tyc() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['User']['acepta']) && $this->request->data['User']['acepta'] === '1') {
                $this->User->id = $_SESSION['Auth']['User']['id'];
                $this->User->saveField('aceptaterminosycondiciones', 1);

                $this->Flash->success(__('T&eacute;rminos y Condiciones aceptados, bienvenid@ !'));
                return $this->redirect('/noticias');
            } else {
                $this->Flash->error(__('Debe aceptar los T&eacute;rminos y Condiciones para utilizar el Sistema'));
                return $this->redirect('/users/tyc');
            }
        }
        $this->layout = 'default';
    }

}
