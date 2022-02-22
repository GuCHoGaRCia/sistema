<?php

App::uses('AppController', 'Controller');

class ReparacionesactualizacionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('agregar', 'edit', 'delAdjunto');
        array_push($this->Security->unlockedActions, 'agregar', 'delAdjunto'); // permito blackhole x ajax
        if (!isset($_SESSION['Auth']['User']['Client']['id']) && in_array($this->request['action'], ['agregar', 'edit']) && isset($this->request->pass[0])) {
            // acceso por link de supervisor
            $this->email = $this->Reparacionesactualizacione->User->Client->Consorcio->Propietario->Aviso->_decryptURL($this->request->pass[0]);
            if (empty($this->email) || filter_var($this->email, FILTER_VALIDATE_EMAIL) === FALSE) {
                die(__('El dato es inexistente'));
            }
            $this->client_id = $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getClientId($this->email);
        }
    }

    public function agregar($id = null) {
        if ($this->request->is(['post', 'put'])) {
            if (!$this->request->is('ajax')) {
                die;
            }
            die(json_encode($this->Reparacionesactualizacione->guardarActualizacion($this->request)));
        }
        if (isset($this->client_id)) {
            $id = $this->request->pass[1];
            $this->set('supervisor', $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getClientId($this->email));
        } else {
            $this->client_id = $_SESSION['Auth']['User']['Client']['id'];
        }
        $c = $this->client_id;
        $options = array('conditions' => array('Reparacione.id' => $id), 'contain' => ['Reparacionesestado.nombre',
                'Reparacionesactualizacione', 'Reparacionesactualizacione.Reparacionesestado.nombre', 'Reparacionesactualizacione.Reparacionesactualizacionesadjunto',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Llave.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Proveedor.name',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Reparacionessupervisore.nombre',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Propietario.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionessupervisore', 'Consorcio.name', 'Propietario.name2'], 'recursive' => 0);
        $reparaciones = $this->Reparacionesactualizacione->Reparacione->find('first', $options);
        if (empty($reparaciones)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['controller' => 'reparaciones', 'action' => 'index']);
        }
        $users = $this->Reparacionesactualizacione->User->getList($this->client_id);
        $proveedors = $this->Reparacionesactualizacione->User->Client->Proveedor->getList($this->client_id);
        $reparacionessupervisores = $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getList($this->client_id);
        $reparacionessupervisorestodos = $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->get($this->client_id);
        $reparacionesestados = $this->Reparacionesactualizacione->Reparacionesestado->get();
        $llaves = $this->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->Llave->getDisponibles($this->client_id);
        $llavesentregadas = $this->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->Llave->getEntregadas($id, $this->client_id);
        $consorcios = $this->Reparacionesactualizacione->Reparacione->Consorcio->getConsorciosList($this->client_id);
        $this->set(compact('consorcios', 'reparaciones', 'users', 'reparacionesestados', 'proveedors', 'reparacionessupervisores', 'reparacionessupervisorestodos', 'llaves', 'llavesentregadas', 'c'));
        $this->layout = '';
    }

    /*
     * Edita las reparaciones de la version nueva
     */

    public function edit($id = null) {
        if ($this->request->is(['post', 'put'])) {
            if (!$this->request->is('ajax')) {
                die;
            }
            die(json_encode($this->Reparacionesactualizacione->guardarActualizacion($this->request, null, true)));
        }
        if (isset($this->client_id)) {
            $id = $this->request->pass[1];
            $this->set('supervisor', $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getClientId($this->email));
        } else {
            $this->client_id = $_SESSION['Auth']['User']['Client']['id'];
        }
        $options = array('conditions' => array('Reparacione.id' => $id), 'contain' => ['Reparacionesestado.nombre',
                'Reparacionesactualizacione.Reparacionesestado.nombre', 'Reparacionesactualizacione.Reparacionesactualizacionesadjunto',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Llave.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Proveedor.name',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Reparacionessupervisore.nombre',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Propietario.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionessupervisore', 'Consorcio.name', 'Consorcio.client_id', 'Propietario.name2'],
            'joins' => [['table' => 'reparacionesactualizaciones', 'alias' => 'Reparacionesactualizacione', 'type' => 'right', 'conditions' => ['Reparacione.id=Reparacionesactualizacione.reparacione_id']]],
            'recursive' => 0);
        $reparaciones = $this->Reparacionesactualizacione->Reparacione->find('first', $options);
        if (empty($reparaciones)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['controller' => 'reparaciones', 'action' => 'index']);
        }
        $users = $this->Reparacionesactualizacione->User->getList($this->client_id);
        $proveedors = $this->Reparacionesactualizacione->User->Client->Proveedor->getList($this->client_id);
        $reparacionessupervisores = $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getList($this->client_id);
        $reparacionessupervisorestodos = $this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->get($this->client_id);
        $reparacionesestados = $this->Reparacionesactualizacione->Reparacionesestado->get();
        $llaves = $this->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->Llave->getDisponibles($this->client_id);
        $llavesentregadas = $this->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->Llave->getEntregadas($id, $this->client_id);
        $consorcios = $this->Reparacionesactualizacione->Reparacione->Consorcio->getConsorciosList($this->client_id);
        $this->set(compact('consorcios', 'reparaciones', 'users', 'reparacionesestados', 'proveedors', 'reparacionessupervisores', 'reparacionessupervisorestodos', 'llaves', 'llavesentregadas'));
        $this->layout = '';
    }

    public function delAdjunto() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Reparacionesactualizacione->Reparacionesactualizacionesadjunto->delAdjunto($this->request->data['id'], $this->request->data['c'])));
    }

    public function delete($id = null) {
        $this->Reparacionesactualizacione->id = $id;
        if (!$this->Reparacionesactualizacione->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Reparacionesactualizacione->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
