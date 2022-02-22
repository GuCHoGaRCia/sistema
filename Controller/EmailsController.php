<?php

App::uses('AppController', 'Controller');

class EmailsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'add', 'getPropietarios'); // permito blackhole x ajax
    }

    public function index() {
        $this->Email->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Email.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Email->parseCriteria($this->passedArgs)],
            'order' => 'Email.id desc'];
        $this->Prg->commonProcess();
        $this->set('emails', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Email->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Email.' . $this->Email->primaryKey => $id]];
        $this->set('email', $this->Email->find('first', $options));
    }

    public function panel_index() {
        $this->Email->recursive = 0;
        $this->Paginator->settings = ['conditions' => [$this->Email->parseCriteria($this->passedArgs)], 'order' => 'Email.id desc'];
        $this->Prg->commonProcess();
        $this->set('emails', $this->paginar($this->Paginator));
    }

    public function panel_view($id = null) {
        if (!$this->Email->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Email.' . $this->Email->primaryKey => $id]];
        $this->set('email', $this->Email->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Email->create();
            if ($this->Email->save($this->request->data)) {
                $this->Flash->success(__('El email fue enviado correctamente'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El email no pudo ser enviado, intente nuevamente'));
            }
        }
        $this->set('consorcios', $this->Email->Client->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']]]));
    }

    public function getPropietarios() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Email->Client->Aviso->getPropietarios($this->data['con'])));
    }

    public function edit($id = null) {
        if (!$this->Email->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Email->save($this->request->data)) {
                $this->Flash->success(__('El email fue enviado correctamente'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El email no pudo ser enviado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Email.' . $this->Email->primaryKey => $id]];
            $this->request->data = $this->Email->find('first', $options);
        }
        /* $clients = $this->Email->Client->find('list');
          $this->set(compact('clients')); */
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Email->create();
            if ($this->Email->save($this->request->data)) {
                $this->Flash->success(__('El email fue enviado correctamente'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El email no pudo ser enviado, intente nuevamente'));
            }
        }
        $clients = $this->Email->Client->find('list');
        $this->set(compact('clients'));
    }

    public function panel_edit($id = null) {
        if (!$this->Email->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Email->save($this->request->data)) {
                $this->Flash->success(__('El email fue enviado correctamente'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El email no pudo ser enviado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Email.' . $this->Email->primaryKey => $id]];
            $this->request->data = $this->Email->find('first', $options);
        }
        $clients = $this->Email->Client->find('list');
        $this->set(compact('clients'));
    }

    public function delete($id = null) {
        $this->Email->id = $id;
        if (!$this->Email->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Email->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function panel_delete($id = null) {
        $this->Email->id = $id;
        if (!$this->Email->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Email->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
