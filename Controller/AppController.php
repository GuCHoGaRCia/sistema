<?php

App::uses('Controller', 'Controller');
App::uses('HelpsController', 'Controller');
App::uses('ConsultasController', 'Controller');
App::uses('UserProfilesController', 'Controller');
App::uses('ConsultaspropietariosController', 'Controller');

class AppController extends Controller {

    public $components = [//'DebugKit.Toolbar',
        'Paginator',
        'Security',
        'Session',
        'Search.Prg',
        'Reportes.Reportes',
        'Flash' => [
            'clear' => false // false stackea los mensajes de flash
        ],
        'Export', // para exportar a CSV
        'Auth' => [
            'unauthorizedRedirect' => ['controller' => 'users', 'action' => 'login', 'panel' => false],
            'loginAction' => ['controller' => 'users', 'action' => 'login', 'panel' => false],
            'loginRedirect' => ['controller' => 'clients', 'action' => 'index', 'panel' => false],
            'logoutRedirect' => ['controller' => 'users', 'action' => 'login', 'panel' => false],
            'authorize' => 'Controller',
            'authError' => 'No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema',
    ]];
    public $helpers = ['Functions', 'Session', 'Time', 'JqueryValidation', 'Minify.Minify', 'Reportes.FPDF'];

    public function beforeFilter() {
        $this->layout = 'default';
        $this->Security->unlockedActions = ['editar', 'invertir']; // permito blackhole x ajax
        $this->Security->blackHoleCallback = 'blackhole';
        $this->Auth->authenticate = [
            AuthComponent::ALL => ['userModel' => 'User',
                'fields' => ['username' => 'username', 'password' => 'password'],
            ],
            'My'
        ];

        if (isset($this->request->params['prefix']) && ($this->request->params['prefix'] == 'panel')) {
            if ($this->Session->check('Auth.User')) {
                if (!empty($_SESSION['Auth']['User']['perfil'])) {
                    // layout para los casos especificos de usuarios q deben ver parte del sistema
                    $this->layout = basename($_SESSION['Auth']['User']['perfil']);
                } else {
                    $this->layout = 'backendadmin';
                }
            }
        } elseif ($this->Session->check('Auth.User')) {
            if (!empty($_SESSION['Auth']['User']['perfil'])) {
                // layout para los casos especificos de usuarios q deben ver parte del sistema
                if (!$this->estaAutorizado($_SESSION['Auth']['User']['perfil'], $this->params['controller'], $this->params['action'])) {
                    if (in_array('X-Requested-With', array_keys(apache_request_headers()))) {
                        echo "<div class='error'>Su usuario no posee permisos para realizar esta acci&oacute;n (" . strtolower($this->params['controller'] . "/" . $this->params['action']) . ")</div>";
                        die;
                    } else {
                        $this->Flash->error(__('Su usuario no posee permisos para realizar esta acci&oacute;n (' . strtolower($this->params['controller'] . "/" . $this->params['action']) . ")"));
                        return $this->redirect($this->referer());
                    }
                }

                // verifico si el layout existe, caso contrario deslogueo (sino hace too_many_redirects) y muestro cartel
                if (file_exists(dirname(__FILE__) . DS . ".." . DS . "View" . DS . "Layouts" . DS . basename($_SESSION['Auth']['User']['perfil']) . '.ctp')) {
                    $this->layout = basename($_SESSION['Auth']['User']['perfil']);
                } else {
                    $this->Flash->error('Falta crear el archivo de perfil "' . h(basename($_SESSION['Auth']['User']['perfil'])) . '"');
                    return $this->redirect($this->Auth->logout());
                }
            } else {
                $this->layout = 'backendcli';
            }
        } else {
            if ($this->request->is('ajax')) {
                if (isset($this->request->data['pk'])) { // solo para editable !!!
                    // ajax deslogueado (tira este error, en vez de Forbidden en el x-editable)
                    die("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema.");
                } else {
                    // permito ajax deslogueado SII despues del beforeFilter de su controller hago $this->Auth->allow y $this->Security->unlockedActions
                    //die("hola");
                }
            }
            if (!$this->Auth->loggedIn()) {
                // cargo la ayuda si /*esta logueado y */existe. Para el caso de los Propietarios, la ayuda debe aparecer igual
                $h = new HelpsController();
                $m = $h->getHelp($this->params['controller'], str_replace('panel_', '', $this->params['action']));
                $this->set('help', $m);
                return $this->Auth->redirect('/');
            }
            $this->Auth->allow();
        }

        //if (isset($_SESSION['Auth']['User']['layout']) && !$this->estaAutorizado($_SESSION['Auth']['User']['layout'], $this->params['controller'], $this->params['action'])) {
        //return $this->redirect($this->Auth->logout());
        //debug($_SESSION['Auth']['User']['layout']);
        //debug($this->params);
        //}
        // cargo la ayuda si /*esta logueado y */existe. Para el caso de los Propietarios, la ayuda debe aparecer igual
        $h = new HelpsController();
        $m = $h->getHelp($this->params['controller'], str_replace('panel_', '', $this->params['action']));
        $this->set('help', $m);

        if (isset($_SESSION['Auth']['User']['client_id'])) {
            // verifico si hay consultas entre cliente/ceonline sin ver
            $this->set('seen', (new ConsultasController())->Consulta->getUnseen());

            // verifico si hay consultas entre cliente/propietarios sin ver
            $this->set('seenP', (new ConsultaspropietariosController())->Consultaspropietario->getUnseen());
        }

        // fuerzo estos id de usuario a q se deslogueen
        //if (!in_array($_SESSION['Auth']['User']['id'], [0])) {
        //    @session_destroy();
        //}
    }

    public function blackhole($type) {
        if ($type === 'csrf') {
            $this->Flash->error(__('No est&aacute; permitido reenviar un formulario m&aacute;s de una vez para evitar duplicados'));
            $this->redirect('/');
        }
    }

    public function isAuthorized($user) {
        if (isset($this->request->params['prefix']) && $this->request->params['prefix'] === 'panel' && $user['is_admin'] != 1) {
            echo '<a href="/sistema/users/logout">Salir</a><br />';
            die(__('No puede ingresar a la web solicitada'));
        }

        // Any registered user can access public functions
        if (empty($this->request->params['panel'])) {
            return true;
        }

        // Only admins can access admin functions
        if (isset($this->request->params['panel'])) {
            return (bool) ($user['is_admin'] == 1);
        }

        // Default deny
        return false;
    }

    private function estaAutorizado($layout, $c, $a) {
        $controller = strtolower($c);
        $action = strtolower($a);
        $permisos = (new UserProfilesController())->UserProfile->getPermisos($layout);
        if (empty($permisos)) {
            return false;
        }
        //debug($permisos);
        //debug($this->params['controller']);
        //debug($this->params['action']);
        //die;
        if (in_array(strtolower($this->params['controller'] . "/" . $this->params['action']), $permisos)) {
            return true;
        }
        if ($layout == 'supervisor') {
            $permisosupervisor = ['reparaciones' => [], 'users' => ['login', 'logout'], 'noticias' => []];
            if (in_array($controller, $permisosupervisor)) {
                if (empty($permisosupervisor[$controller])) {
                    return true;
                }
                if (in_array($action, $permisosupervisor[$controller])) {
                    return true;
                }
                return false;
            }
            return false;
        }

        return false;
    }

    /*
     * Funcion que me permite actualizar un registro con Ajax sin tener que recargar la p�gina
     * usa: bootstrap editable 
     */

    public function panel_editar() { // para q funcione en admin routing
        $this->editar();
    }

    public function editar() {
        if (!$this->request->is('ajax')) {
            return $this->redirect($this->Auth->logout());
        }
        if (!isset($this->request->data['pk']) || !is_numeric($this->request->data['pk'])) {
            return $this->redirect($this->Auth->logout());
        }
        if (!isset($this->request->data['name']) || empty($this->request->data['name']) || !isset($this->request->data['value'])) {
            return $this->redirect($this->Auth->logout());
        }

        $field = trim(filter_var($this->request->data['name'], FILTER_SANITIZE_STRING));
        $model = $this->modelClass;
        if (in_array($field, ['id', 'created', 'modified']) || !$this->$model->hasField($field)) {//no permito modificar id, created o modified
            return $this->redirect($this->Auth->logout());
        }
        $value = trim($this->request->data['value']);
        $pk = $this->request->data['pk'];

        // valido que el pk que se intenta modificar pertenece al cliente logueado! si es admin, puede hacer todo
        if (!$_SESSION['Auth']['User']['is_admin'] && !$this->$model->canEdit($pk)) {
            die("El dato no se puede modificar o es inexistente");
        }

        $this->$model->id = trim($pk);
        if (!($this->$model->exists() && $this->$model->saveField($field, $value, true))) {
            echo __(utf8_encode($this->$model->validationErrors[$field][0])); // por los acentos de mierda en validation[message]
            die;
        }
        //$model = $this->modelClass;debug($this->$model->getDataSource()->getLog(false, false));die;
        $this->autoRender = false;
    }

    /*
     * Funcion que me permite actualizar un registro con Ajax sin tener que recargar la p�gina
     */

    public function panel_invertir($field = null, $id = null) { // para q funcione en admin routing
        return $this->invertir($field, $id);
    }

    public function invertir($field = null, $id = null) {
        if (!$this->request->is('ajax')) {
            return $this->redirect($this->Auth->logout());
        }
        $model = $this->modelClass;
        if (empty($field) || empty($id) || in_array($field, ['id', 'created', 'modified']) || !$this->$model->hasField($field)) {
            return $this->redirect($this->Auth->logout());
        }

        // valido que el pk que se intenta modificar pertenece al cliente logueado! si es admin, puede hacer todo
        if (!$_SESSION['Auth']['User']['is_admin'] && !$this->$model->canEdit($id)) {
            die("El dato no se puede modificar o es inexistente");
        }

        $this->autoRender = false;
        if ($this->$model && $field && $id) {
            $field = $this->$model->escapeField($field);
            return $this->$model->updateAll([$field => '1 -' . $field], [$this->$model->escapeField() => $id]);
        }
        die("El dato no se puede modificar o es inexistente");
    }

    /*
     * Funcion que cuando se busca algo en una pagina inexistente, no tira NotFoundException, sino que redirecciona a la pagina 1 de la busqueda
     */

    public function paginar($paginator) {
        try {
            return $paginator->paginate();
        } catch (NotFoundException $e) {
            $this->redirect(array('controller' => $this->request->params['controller'],
                'action' => $this->request->params['action'], 'buscar' => isset($this->request->params['named']['buscar']) ? $this->request->params['named']['buscar'] : '', 'page' => (str_replace('panel_', '', $this->params['action']) == 'delete' ? $this->request->params['named']['page'] - 1 : 1)));
        }
    }

    /*
     * Permite exportar a csv un listado.
     */

    public function exportar() {
        if (!isset($_SESSION['exportar']) || empty($_SESSION['exportar'])) {
            $this->Flash->error(__('No se ecuentran datos para exportar'));
            $this->redirect($this->referer());
        }
        $conditions = $_SESSION['exportar'];
        if (isset($conditions['page']) && $conditions['page'] != 0) {
            $conditions['offset'] = $conditions['page'] * $conditions['limit'] - $conditions['limit'];
        }
        $resul = $this->{$this->modelClass}->find('all', $conditions);
        if (!empty($resul)) {
            $this->Export->exportCsv($resul, $this->modelClass . '-' . date("YmdHis") . '.csv', null, ';');
        } else {
            $this->Flash->error(__('No se ecuentran datos para exportar'));
            $this->redirect($this->referer());
        }
    }

}
