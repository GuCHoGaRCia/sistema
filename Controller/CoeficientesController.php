<?php

App::uses('AppController', 'Controller');

class CoeficientesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => '1', $this->Coeficiente->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.id=Coeficiente.consorcio_id'))),
            'fields' => ['Coeficiente.id', 'Coeficiente.consorcio_id', 'Coeficiente.name', 'Coeficiente.enabled', 'Consorcio.name', 'Consorcio.id'],
            'order' => 'Consorcio.code'];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('coeficientes', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Coeficiente->Consorcio->getConsorciosList());
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Coeficiente->create();
            if ($this->Coeficiente->saveAssociated($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('No se han cargado propietarios en el consorcio. Agregue Propietarios (men&uacute; Datos) e intente nuevamente.'));
            }
        }
        $consorcios = $this->Coeficiente->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        if (!$this->Coeficiente->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Coeficiente->id = $id;
        if ($this->Coeficiente->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('Existen Gastos Generales o Particulares asociados al coeficiente. Eliminelos e intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
