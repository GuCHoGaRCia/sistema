<?php

App::uses('AppController', 'Controller');

class LlavesmovimientosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function view($id = null) {
        if (!$this->Llavesmovimiento->exists($id) || $this->Llavesmovimiento->find('count', array('conditions' => array('Llave.client_id' => $_SESSION['Auth']['User']['Client']['id'], 'Llave.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Llavesmovimiento.id' => $id], 'contain' => ['Llave', 'Llavesestado', 'Proveedor', 'Propietario', 'Reparacionessupervisore'],
            'fields' => ['Llave.numero', 'Llave.descripcion', 'Llavesestado.nombre', 'Proveedor.name', 'Propietario.name',
                'Llavesmovimiento.id', 'Llavesmovimiento.titulo', 'Llavesmovimiento.fecha']];
        $this->set('llavesmovimiento', $this->Llavesmovimiento->find('first', $options));
    }

    public function add() {
        if ($this->request->is(['post', 'put'])) {
            if (!$this->request->is('ajax')) {
                die;
            }
            $resul = $this->Llavesmovimiento->mover($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado correctamente'));
            }
            die(json_encode($resul));
        }
        $llaves = $this->Llavesmovimiento->Llave->findById($this->request->params['pass'][0]);
        $llavesestados = $this->Llavesmovimiento->Llavesestado->getList();
        $proveedors = $this->Llavesmovimiento->Proveedor->getList($_SESSION['Auth']['User']['client_id']);
        $consorcios = $this->Llavesmovimiento->Propietario->Consorcio->getConsorciosList();
        $reparacionessupervisores = $this->Llavesmovimiento->Propietario->Consorcio->Client->Reparacionessupervisore->getList($_SESSION['Auth']['User']['client_id']);
        $this->set(compact('llaves', 'llavesestados', 'proveedors', 'consorcios', 'reparacionessupervisores'));
        $this->layout = '';
    }

    public function delete($id = null) {
        $this->Llavesmovimiento->id = $id;
        if (!$this->Llavesmovimiento->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Llavesmovimiento->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
