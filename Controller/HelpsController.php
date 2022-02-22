<?php

App::uses('AppController', 'Controller');

class HelpsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $this->Paginator->settings = array('conditions' => array($this->Help->parseCriteria($this->passedArgs)), 'order' => 'Help.modified desc');
        $this->Prg->commonProcess();
        $this->set('helps', $this->paginar($this->Paginator));
    }

    public function panel_view($id = null) {
        if (!$this->Help->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Help.' . $this->Help->primaryKey => $id));
        $this->set('help', $this->Help->find('first', $options));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $this->Help->create();
            if ($this->Help->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La ayuda para esa seccion ya existe, modifique la misma o cambie la actual'));
                //return $this->redirect(['action' => 'index']);
            }
        }
    }

    public function panel_edit($id = null) {
        if (!$this->Help->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Help->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Help.' . $this->Help->primaryKey => $id));
            $this->request->data = $this->Help->find('first', $options);
        }
    }

    public function panel_delete($id = null) {
        $this->Help->id = $id;
        if (!$this->Help->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Help->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    /*
     * Obtengo la ayuda para el controller/action actual. Si es administrador, muestro todo, sino, solamente las que no sean para administradores
     */

    public function getHelp($controller, $action) {
        $options = array('fields' => array('Help.id', 'Help.content', 'Help.modified'),
            'conditions' => array('Help.controller' => $controller, 'Help.action' => $action, 'Help.enabled' => 1,
                (isset($_SESSION['Auth']['User']['is_admin']) && $_SESSION['Auth']['User']['is_admin'] == 0 ? ['Help.soloadmin' => 0] : [])));
        return $this->Help->find('first', $options);
    }

}
