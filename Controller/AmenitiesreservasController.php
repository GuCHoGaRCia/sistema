<?php

App::uses('AppController', 'Controller');

class AmenitiesreservasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Amenitiesreserva->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Amenitiesreserva.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Amenitiesreserva->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('amenitiesreservas', $this->paginar($this->Paginator));
    }

    public function view($id = null) {
        if (!$this->Amenitiesreserva->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Amenitiesreserva.' . $this->Amenitiesreserva->primaryKey => $id]];
        $this->set('amenitiesreserva', $this->Amenitiesreserva->find('first', $options));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Amenitiesreserva->create();
            if ($this->Amenitiesreserva->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $amenities = $this->Amenitiesreserva->Amenitie->find('list');
        $amenitiesturnos = $this->Amenitiesreserva->Amenitiesturno->find('list');
        $propietarios = $this->Amenitiesreserva->Propietario->find('list');
        $this->set(compact('amenities', 'amenitiesturnos', 'propietarios'));
    }

    public function edit($id = null) {
        if (!$this->Amenitiesreserva->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Amenitiesreserva->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Amenitiesreserva.' . $this->Amenitiesreserva->primaryKey => $id]];
            $this->request->data = $this->Amenitiesreserva->find('first', $options);
        }
        $amenities = $this->Amenitiesreserva->Amenitie->find('list');
        $amenitiesturnos = $this->Amenitiesreserva->Amenitiesturno->find('list');
        $propietarios = $this->Amenitiesreserva->Propietario->find('list');
        $this->set(compact('amenities', 'amenitiesturnos', 'propietarios'));
    }

    public function delete($id = null) {
        $this->Amenitiesreserva->id = $id;
        if (!$this->Amenitiesreserva->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Amenitiesreserva->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
