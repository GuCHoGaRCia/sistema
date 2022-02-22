<?php
App::uses('AppController', 'Controller');
class ReparacionesactualizacionesllavesmovimientosController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Reparacionesactualizacionesllavesmovimiento->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Reparacionesactualizacionesllavesmovimiento.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Reparacionesactualizacionesllavesmovimiento->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('reparacionesactualizacionesllavesmovimientos', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Reparacionesactualizacionesllavesmovimiento->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Reparacionesactualizacionesllavesmovimiento.' . $this->Reparacionesactualizacionesllavesmovimiento->primaryKey => $id]];
		$this->set('reparacionesactualizacionesllavesmovimiento', $this->Reparacionesactualizacionesllavesmovimiento->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Reparacionesactualizacionesllavesmovimiento->create();
			if ($this->Reparacionesactualizacionesllavesmovimiento->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$reparacionesactualizaciones = $this->Reparacionesactualizacionesllavesmovimiento->Reparacionesactualizacione->find('list');
		$llavesmovimientos = $this->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->find('list');
		$this->set(compact('reparacionesactualizaciones', 'llavesmovimientos'));
	}

	public function edit($id = null) {
		if (!$this->Reparacionesactualizacionesllavesmovimiento->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Reparacionesactualizacionesllavesmovimiento->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Reparacionesactualizacionesllavesmovimiento.' . $this->Reparacionesactualizacionesllavesmovimiento->primaryKey => $id]];
			$this->request->data = $this->Reparacionesactualizacionesllavesmovimiento->find('first', $options);
		}
		$reparacionesactualizaciones = $this->Reparacionesactualizacionesllavesmovimiento->Reparacionesactualizacione->find('list');
		$llavesmovimientos = $this->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->find('list');
		$this->set(compact('reparacionesactualizaciones', 'llavesmovimientos'));
	}

	public function delete($id = null) {
		$this->Reparacionesactualizacionesllavesmovimiento->id = $id;
		if (!$this->Reparacionesactualizacionesllavesmovimiento->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Reparacionesactualizacionesllavesmovimiento->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
