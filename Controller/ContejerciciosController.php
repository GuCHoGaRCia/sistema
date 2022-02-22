<?php

App::uses('AppController', 'Controller');

class ContejerciciosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getMeses', 'getEjercicios'); // permito blackhole x ajax
    }

    public function index() {
        $this->Contejercicio->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Contejercicio.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Contejercicio->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('contejercicios', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Contejercicio->exists($id) || $this->Contejercicio->find('count', array('conditions' => array('Contejercicio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contejercicio.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Contejercicio.' . $this->Contejercicio->primaryKey => $id]];
        $this->set('contejercicio', $this->Contejercicio->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Contejercicio->create();
            if ($this->Contejercicio->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Contejercicio->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function edit($id = null) {
        if (!$this->Contejercicio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Contejercicio->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Contejercicio.' . $this->Contejercicio->primaryKey => $id]];
            $this->request->data = $this->Contejercicio->find('first', $options);
        }
        $consorcios = $this->Contejercicio->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    /*
     * Obtiene los ejercicios asociados a un Consorcio
     */

    public function getEjercicios() {
        if (!$this->Contejercicio->Consorcio->canEdit($this->data['id'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if (!$this->request->is('ajax')) {
            die;
        }
        die(json_encode($this->Contejercicio->getEjercicios($this->data['id'])));
    }

    /*
     * Obtiene los meses asociados a un Ejercicio
     */

    public function getMeses() {
        if (!$this->Contejercicio->canEdit($this->data['id'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if (!$this->request->is('ajax')) {
            die;
        }
        die(json_encode($this->Contejercicio->getMeses($this->data['id'])));
    }

    public function delete($id = null) {
        $this->Contejercicio->id = $id;
        if (!$this->Contejercicio->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Contejercicio->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
