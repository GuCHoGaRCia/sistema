<?php
App::uses('AppController', 'Controller');
class AdministracionefectivosController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Administracionefectivo->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Administracionefectivo.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Administracionefectivo->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('administracionefectivos', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Administracionefectivo->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Administracionefectivo.' . $this->Administracionefectivo->primaryKey => $id]];
		$this->set('administracionefectivo', $this->Administracionefectivo->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Administracionefectivo->create();
			if ($this->Administracionefectivo->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$proveedorspagos = $this->Administracionefectivo->Proveedorspago->find('list');
		$bancoscuentas = $this->Administracionefectivo->Bancoscuentum->find('list');
		$this->set(compact('proveedorspagos', 'bancoscuentas'));
	}

	public function edit($id = null) {
		if (!$this->Administracionefectivo->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Administracionefectivo->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Administracionefectivo.' . $this->Administracionefectivo->primaryKey => $id]];
			$this->request->data = $this->Administracionefectivo->find('first', $options);
		}
		$proveedorspagos = $this->Administracionefectivo->Proveedorspago->find('list');
		$bancoscuentas = $this->Administracionefectivo->Bancoscuentum->find('list');
		$this->set(compact('proveedorspagos', 'bancoscuentas'));
	}

	public function delete($id = null) {
		$this->Administracionefectivo->id = $id;
		if (!$this->Administracionefectivo->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Administracionefectivo->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
