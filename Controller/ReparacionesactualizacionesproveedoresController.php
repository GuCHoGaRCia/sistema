<?php
App::uses('AppController', 'Controller');
class ReparacionesactualizacionesproveedoresController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Reparacionesactualizacionesproveedore->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Reparacionesactualizacionesproveedore.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Reparacionesactualizacionesproveedore->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('reparacionesactualizacionesproveedores', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Reparacionesactualizacionesproveedore->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Reparacionesactualizacionesproveedore.' . $this->Reparacionesactualizacionesproveedore->primaryKey => $id]];
		$this->set('reparacionesactualizacionesproveedore', $this->Reparacionesactualizacionesproveedore->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Reparacionesactualizacionesproveedore->create();
			if ($this->Reparacionesactualizacionesproveedore->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$reparacionesactualizaciones = $this->Reparacionesactualizacionesproveedore->Reparacionesactualizacione->find('list');
		$proveedors = $this->Reparacionesactualizacionesproveedore->Proveedor->find('list');
		$this->set(compact('reparacionesactualizaciones', 'proveedors'));
	}

	public function edit($id = null) {
		if (!$this->Reparacionesactualizacionesproveedore->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Reparacionesactualizacionesproveedore->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Reparacionesactualizacionesproveedore.' . $this->Reparacionesactualizacionesproveedore->primaryKey => $id]];
			$this->request->data = $this->Reparacionesactualizacionesproveedore->find('first', $options);
		}
		$reparacionesactualizaciones = $this->Reparacionesactualizacionesproveedore->Reparacionesactualizacione->find('list');
		$proveedors = $this->Reparacionesactualizacionesproveedore->Proveedor->find('list');
		$this->set(compact('reparacionesactualizaciones', 'proveedors'));
	}

	public function delete($id = null) {
		$this->Reparacionesactualizacionesproveedore->id = $id;
		if (!$this->Reparacionesactualizacionesproveedore->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Reparacionesactualizacionesproveedore->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
