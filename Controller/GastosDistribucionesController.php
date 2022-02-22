<?php

App::uses('AppController', 'Controller');

class GastosDistribucionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = [$this->GastosDistribucione->parseCriteria($this->passedArgs), 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0, 'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']]]];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('gastosDistribuciones', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->GastosDistribucione->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->GastosDistribucione->exists($id) || $this->GastosDistribucione->find('count', array('joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=GastosDistribucione.consorcio_id']]], 'fields' => ['Consorcio.id'], 'conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosDistribucione.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('GastosDistribucione.' . $this->GastosDistribucione->primaryKey => $id), 'recursive' => 1,
            'fields' => ['Consorcio.name', 'GastosDistribucione.id', 'GastosDistribucione.consorcio_id', 'GastosDistribucione.nombre', 'GastosDistribucionesDetalle.*'],
            'joins' => [['table' => 'gastos_distribuciones_detalles', 'alias' => 'GastosDistribucionesDetalle', 'type' => 'left', 'conditions' => ['GastosDistribucionesDetalle.gastos_distribucione_id=GastosDistribucione.id']]]);
        $gastosDistribuciones = $this->GastosDistribucione->find('first', $options);
        $this->set('gastosDistribuciones', $gastosDistribuciones);
        $coeficientes = $this->GastosDistribucione->GastosDistribucionesDetalle->Coeficiente->find('list', ['conditions' => ['Consorcio.id' => $gastosDistribuciones['GastosDistribucione']['consorcio_id']], 'recursive' => 0]);
        $this->set('coeficientes', $coeficientes);
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->add2();
        }
        $consorcios = $this->GastosDistribucione->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function add2() {
        if ($this->request->is('post') && isset($this->request->data['GastosDistribucionesDetalle'][0]['porcentaje'])) {
            if ($this->GastosDistribucione->guardar($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente. Verifique que la suma de los coeficientes sea 100%'));
            }
        }
        if (isset($this->request->data['GastosDistribucione']['nombre'])) {
            $this->set('nombre', $this->request->data['GastosDistribucione']['nombre']);
        }
        $consorcios = $this->GastosDistribucione->Consorcio->find('list', ['conditions' => ['Consorcio.id' => $this->request->data['GastosDistribucione']['consorcio_id']]]);
        $coeficientes = $this->GastosDistribucione->Consorcio->Coeficiente->find('list', array('conditions' => array('Coeficiente.consorcio_id' => $this->request->data['GastosDistribucione']['consorcio_id'], 'Coeficiente.enabled' => 1)));
        $this->set(compact('coeficientes', 'consorcios'));
        $this->render('add2');
    }

    public function delete($id = null) {
        $this->GastosDistribucione->id = $id;
        if (!$this->GastosDistribucione->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->GastosDistribucione->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
