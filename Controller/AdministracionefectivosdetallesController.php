<?php
App::uses('AppController', 'Controller');
class AdministracionefectivosdetallesController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Administracionefectivosdetalle->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Administracionefectivosdetalle.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Administracionefectivosdetalle->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('administracionefectivosdetalles', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Administracionefectivosdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Administracionefectivosdetalle.' . $this->Administracionefectivosdetalle->primaryKey => $id]];
		$this->set('administracionefectivosdetalle', $this->Administracionefectivosdetalle->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Administracionefectivosdetalle->create();
			if ($this->Administracionefectivosdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$administracionefectivos = $this->Administracionefectivosdetalle->Administracionefectivo->find('list');
		$consorcios = $this->Administracionefectivosdetalle->Consorcio->find('list');
		$this->set(compact('administracionefectivos', 'consorcios'));
	}

	public function edit($id = null) {
		if (!$this->Administracionefectivosdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Administracionefectivosdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Administracionefectivosdetalle.' . $this->Administracionefectivosdetalle->primaryKey => $id]];
			$this->request->data = $this->Administracionefectivosdetalle->find('first', $options);
		}
		$administracionefectivos = $this->Administracionefectivosdetalle->Administracionefectivo->find('list');
		$consorcios = $this->Administracionefectivosdetalle->Consorcio->find('list');
		$this->set(compact('administracionefectivos', 'consorcios'));
	}

	public function delete($id = null) {
		$this->Administracionefectivosdetalle->id = $id;
		if (!$this->Administracionefectivosdetalle->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Administracionefectivosdetalle->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
