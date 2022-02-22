<?php
App::uses('AppController', 'Controller');
class ColaimpresionesdetallesController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Colaimpresionesdetalle->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Colaimpresionesdetalle.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Colaimpresionesdetalle->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('colaimpresionesdetalles', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Colaimpresionesdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Colaimpresionesdetalle.' . $this->Colaimpresionesdetalle->primaryKey => $id]];
		$this->set('colaimpresionesdetalle', $this->Colaimpresionesdetalle->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Colaimpresionesdetalle->create();
			if ($this->Colaimpresionesdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$colaimpresiones = $this->Colaimpresionesdetalle->Colaimpresione->find('list');
		$this->set(compact('colaimpresiones'));
	}

	public function edit($id = null) {
		if (!$this->Colaimpresionesdetalle->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Colaimpresionesdetalle->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Colaimpresionesdetalle.' . $this->Colaimpresionesdetalle->primaryKey => $id]];
			$this->request->data = $this->Colaimpresionesdetalle->find('first', $options);
		}
		$colaimpresiones = $this->Colaimpresionesdetalle->Colaimpresione->find('list');
		$this->set(compact('colaimpresiones'));
	}

	public function delete($id = null) {
		$this->Colaimpresionesdetalle->id = $id;
		if (!$this->Colaimpresionesdetalle->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Colaimpresionesdetalle->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
