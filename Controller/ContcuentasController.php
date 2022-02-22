<?php

App::uses('AppController', 'Controller');

class ContcuentasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Paginator->settings = ['conditions' => ['Contcuenta.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Contcuenta->parseCriteria($this->passedArgs)], 'order' => 'Contcuenta.conttitulo_id,Contcuenta.code'];
        $this->Prg->commonProcess();
        $this->set('contcuentas', $this->paginar($this->Paginator));
        $this->set('hojas', $this->Contcuenta->Conttitulo->getHojas());
    }

    public function mayor($id = null, $consorcio = null, $ejercicio = null, $mes = null) {
        $id = $this->request->data['Contcuenta']['id'] ?? $id;
        $consorcio = $this->request->data['Contcuenta']['consorcio'] ?? $consorcio;
        if (empty($id) || !$this->Contcuenta->canEdit($id)) {
            $this->Flash->error("La Cuenta es inexistente");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }
        if (empty($consorcio) || !$this->Contcuenta->Client->Consorcio->canEdit($consorcio)) {
            $this->Flash->error("El Consorcio es inexistente");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }
        if (empty($ejercicio) || !$this->Contcuenta->Contasiento->Contejercicio->canEdit($ejercicio)) {
            $this->Flash->error("El Ejercicio es inexistente");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }

        $ejercicioinfo = $this->Contcuenta->Contasiento->Contejercicio->getEjercicioInfo($ejercicio);
        if (empty($ejercicioinfo)) {
            $this->Flash->error("El Consorcio no posee un ejercicio en curso");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }

        $desde = $this->request->data['Contcuenta']['desde'] ?? strtotime($ejercicioinfo['inicio']);
        $hasta = $this->request->data['Contcuenta']['hasta'] ?? strtotime($ejercicioinfo['fin']);

        if (!empty($desde) && !empty($hasta) && strtotime($this->Contcuenta->fecha($desde)) > strtotime($this->Contcuenta->fecha($hasta))) {
            $this->Flash->error("La fecha Desde debe ser menor o igual a Hasta");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }

        // verifico q el mes seleccionado se encuentre en el rango de fechas del ejercicio
        if (!(strtotime($mes) >= strtotime($ejercicioinfo['inicio']) && strtotime($mes) <= strtotime($ejercicioinfo['fin']))) {
            $this->Flash->error("El Mes seleccionado no pertenece al Ejercicio");
            return $this->redirect(['controller' => 'contasientos', 'action' => 'balance']);
        }
        $d = date("01/m/Y", $desde);
        $h = date("t/m/Y", strtotime($mes));
        $this->set('d', $d);
        $this->set('h', $h);

        $this->set('resul', $this->Contcuenta->getMayor($id, $consorcio, $d, $h));
        $this->set('id', $id);
        $this->set('consorcio', $consorcio);
        $consorcios = $this->Contcuenta->Contasiento->Consorcio->getConsorciosList();
        $cuentas = $this->Contcuenta->get();
        $this->set(compact('consorcios', 'cuentas', 'ejercicioinfo'));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Contcuenta->create();
            if ($this->Contcuenta->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $this->set('hojas', $this->Contcuenta->Conttitulo->getHojas());
    }

    public function edit($id = null) {
        if (!$this->Contcuenta->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Contcuenta->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Contcuenta.' . $this->Contcuenta->primaryKey => $id]];
            $this->request->data = $this->Contcuenta->find('first', $options);
        }
        $this->set('hojas', $this->Contcuenta->Conttitulo->getHojas());
    }

    public function delete($id = null) {
        if (!$this->Contcuenta->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Contcuenta->id = $id;
        if ($this->Contcuenta->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
