<?php

App::uses('AppController', 'Controller');

class LlavesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'habilitarDeshabilitar');
    }

    public function index() {
        $this->Llave->recursive = 0;
        $conditions = ['Llave.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Llave->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions + ['Consorcio.habilitado' => 1],
            'fields' => ['Llave.*', 'Consorcio.name', 'Propietario.name'], 'order' => 'Llave.habilitada desc,Consorcio.code,Llave.numero'];

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }

        $consorcios = $this->Llave->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
        $this->set('llaves', $this->paginar($this->Paginator));
        $this->set('llavesestados', $this->Llave->Llavesestado->getList());
    }

    public function view($id = null) {
        if ($this->Llave->find('count', array('conditions' => array('Llave.client_id' => $_SESSION['Auth']['User']['Client']['id'], 'Llave.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Llavesmovimiento.llave_id' => $id], 'contain' => ['Llave', 'Llavesestado', 'Proveedor', 'Propietario', 'Propietario.Consorcio.name', 'Reparacionessupervisore'],
            'fields' => ['Llave.numero', 'Llave.descripcion', 'Llave.user_id', 'Llavesestado.nombre', 'Proveedor.name', 'Propietario.name', 'Reparacionessupervisore.nombre',
                'Llavesmovimiento.id', 'Llavesmovimiento.titulo', 'Llavesmovimiento.fecha'],
            'order' => 'Llavesmovimiento.id desc'];
        $this->set('llave', $this->Llave->Llavesmovimiento->find('all', $options));
        $this->set('users', $this->Llave->User->find('list', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id']]]));
        $this->set('llavesestados', $this->Llave->Llavesestado->getList());
        $this->layout = '';
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Llave->create();
            if ($this->Llave->save($this->request->data)) {
                $this->Flash->success(__('La llave fue creada correctamente, verifique su n&uacute;mero'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Llave->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    function habilitarDeshabilitar() {
        $resul = $this->Llave->habilitarDeshabilitar($this->request->params['pass'][0]);
        if ($resul['e'] == 0) {
            $this->Flash->success(__($resul['d']));
        } else {
            $this->Flash->error(__($resul['d']));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function delete($id = null) {
        $this->Llave->id = $id;
        if (!$this->Llave->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Llave->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Solo pueden eliminarse Llaves deshabilitadas y sin movimientos relacionados'));
        }
        return $this->redirect($this->referer());
    }

}
