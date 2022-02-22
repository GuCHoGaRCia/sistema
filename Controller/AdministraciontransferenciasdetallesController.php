<?php
App::uses('AppController', 'Controller');
class AdministraciontransferenciasdetallesController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Administraciontransferenciasdetalle->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Administraciontransferenciasdetalle.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Administraciontransferenciasdetalle->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('administraciontransferenciasdetalles', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Administraciontransferenciasdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Administraciontransferenciasdetalle.' . $this->Administraciontransferenciasdetalle->primaryKey => $id]];
		$this->set('administraciontransferenciasdetalle', $this->Administraciontransferenciasdetalle->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Administraciontransferenciasdetalle->create();
			if ($this->Administraciontransferenciasdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$administraciontransferencias = $this->Administraciontransferenciasdetalle->Administraciontransferencium->find('list');
		$bancoscuentas = $this->Administraciontransferenciasdetalle->Bancoscuentum->find('list');
		$this->set(compact('administraciontransferencias', 'bancoscuentas'));
	}

	public function edit($id = null) {
		if (!$this->Administraciontransferenciasdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Administraciontransferenciasdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Administraciontransferenciasdetalle.' . $this->Administraciontransferenciasdetalle->primaryKey => $id]];
			$this->request->data = $this->Administraciontransferenciasdetalle->find('first', $options);
		}
		$administraciontransferencias = $this->Administraciontransferenciasdetalle->Administraciontransferencium->find('list');
		$bancoscuentas = $this->Administraciontransferenciasdetalle->Bancoscuentum->find('list');
		$this->set(compact('administraciontransferencias', 'bancoscuentas'));
	}

	public function delete($id = null) {
		$this->Administraciontransferenciasdetalle->id = $id;
		if (!$this->Administraciontransferenciasdetalle->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Administraciontransferenciasdetalle->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
