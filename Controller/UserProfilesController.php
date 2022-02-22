<?php

App::uses('AppController', 'Controller');

class UserProfilesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->UserProfile->recursive = 0;
        $this->Paginator->settings = ['conditions' => [$this->UserProfile->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();
        $this->set('userProfiles', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $resul = $this->UserProfile->guardar($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error($resul['d']);
            }
        }
        // quito estas del combo, porque x defecto se agregan asi no hay q seleccionarlas cada vez
        $this->set('routes', array_diff($this->UserProfile->getAllControllerActions(), ["noticias/index", "users/tyc", "users/login", "users/logout"]));
    }

    public function panel_edit($id = null) {
        if (!$this->UserProfile->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            $resul = $this->UserProfile->guardar($this->request->data);
            if ($resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error($resul['d']);
            }
        } else {
            $options = ['conditions' => ['UserProfile.' . $this->UserProfile->primaryKey => $id]];
            $this->request->data = $this->UserProfile->find('first', $options);
            $this->set('routes', $this->UserProfile->getAllControllerActions());
        }
    }

    public function panel_delete($id = null) {
        $this->UserProfile->id = $id;
        if (!$this->UserProfile->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->UserProfile->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
