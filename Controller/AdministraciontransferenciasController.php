<?php
App::uses('AppController', 'Controller');
class AdministraciontransferenciasController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Administraciontransferencia->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Administraciontransferencia.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Administraciontransferencia->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('administraciontransferencias', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Administraciontransferencia->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Administraciontransferencia.' . $this->Administraciontransferencia->primaryKey => $id]];
		$this->set('administraciontransferencia', $this->Administraciontransferencia->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Administraciontransferencia->create();
			if ($this->Administraciontransferencia->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->Administraciontransferencia->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Administraciontransferencia->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Administraciontransferencia.' . $this->Administraciontransferencia->primaryKey => $id]];
			$this->request->data = $this->Administraciontransferencia->find('first', $options);
		}
	}

	public function delete($id = null) {
		$this->Administraciontransferencia->id = $id;
		if (!$this->Administraciontransferencia->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Administraciontransferencia->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
