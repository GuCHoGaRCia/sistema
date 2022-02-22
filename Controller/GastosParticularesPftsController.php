<?php
App::uses('AppController', 'Controller');
class GastosParticularesPftsController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->GastosParticularesPft->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['GastosParticularesPft.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->GastosParticularesPft->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('gastosParticularesPfts', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->GastosParticularesPft->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['GastosParticularesPft.' . $this->GastosParticularesPft->primaryKey => $id]];
		$this->set('gastosParticularesPft', $this->GastosParticularesPft->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->GastosParticularesPft->create();
			if ($this->GastosParticularesPft->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$gastosParticulares = $this->GastosParticularesPft->GastosParticulare->find('list');
		$cobranzas = $this->GastosParticularesPft->Cobranza->find('list');
		$this->set(compact('gastosParticulares', 'cobranzas'));
	}

	public function edit($id = null) {
		if (!$this->GastosParticularesPft->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->GastosParticularesPft->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['GastosParticularesPft.' . $this->GastosParticularesPft->primaryKey => $id]];
			$this->request->data = $this->GastosParticularesPft->find('first', $options);
		}
		$gastosParticulares = $this->GastosParticularesPft->GastosParticulare->find('list');
		$cobranzas = $this->GastosParticularesPft->Cobranza->find('list');
		$this->set(compact('gastosParticulares', 'cobranzas'));
	}

	public function delete($id = null) {
		$this->GastosParticularesPft->id = $id;
		if (!$this->GastosParticularesPft->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->GastosParticularesPft->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
