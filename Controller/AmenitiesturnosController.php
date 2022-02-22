<?php

App::uses('AppController', 'Controller');

class AmenitiesturnosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function view($id = null) {
        $resul = ['e' => 0, 'd' => ''];
        $this->layout = '';
        $this->autoRender = false;
        if (!$this->Amenitiesturno->Amenity->canEdit($id)) {
            $this->Flash->error('El dato es inexistente');
            return json_encode(['e' => 1, 'd' => __('El dato es inexistente')]);
        }
        if ($this->request->is('post')) {
            $resul = $this->Amenitiesturno->guardar($this->request->data);
            if ($resul == "") {
                $this->Flash->success(__('El dato fue guardado'));
                return json_encode(['e' => 0, 'd' => __('El dato fue guardado correctamente')]);
            } else {
                $this->Flash->error(h($resul));
                return json_encode(['e' => 1, 'd' => h($resul)]);
            }
        }

        $this->set('amenity', $this->Amenitiesturno->Amenity->get($id));
        $this->set('amenitiesturnos', $this->Amenitiesturno->find('all', ['conditions' => ['Amenitiesturno.amenitie_id' => $id]]));
        $this->render();
    }

    public function delete($id = null) {
        $this->layout = '';
        $this->autoRender = false;
        if (!$this->Amenitiesturno->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return json_encode(['e' => 1, 'd' => __('El dato es inexistente')]);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Amenitiesturno->id = $id;
        if ($this->Amenitiesturno->delete()) {
            $this->Flash->success(__('El dato fue eliminado correctamente'));
            return true;
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, verifique si existen reservas en ese turno'));
            return false;
        }
    }

}
