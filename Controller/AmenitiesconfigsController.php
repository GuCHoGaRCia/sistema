<?php
App::uses('AppController', 'Controller');
class AmenitiesconfigsController extends AppController {

public function beforeFilter() {
        parent::beforeFilter();
    }

	public function index() {
		$this->Amenitiesconfig->recursive = 0;
        $this->Paginator->settings = ['conditions' => ['Amenitiesconfig.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Amenitiesconfig->parseCriteria($this->passedArgs)]];
        $this->Prg->commonProcess();		
		$this->set('amenitiesconfigs', $this->paginar($this->Paginator));
	}

	public function view($id = null) {
		if (!$this->Amenitiesconfig->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$options = ['conditions' => ['Amenitiesconfig.' . $this->Amenitiesconfig->primaryKey => $id]];
		$this->set('amenitiesconfig', $this->Amenitiesconfig->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Amenitiesconfig->create();
			if ($this->Amenitiesconfig->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		}
		$amenities = $this->Amenitiesconfig->Amenitie->find('list');
		$this->set(compact('amenities'));
	}

	public function edit($id = null) {
		if (!$this->Amenitiesconfig->exists($id)) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->Amenitiesconfig->save($this->request->data)) {
				$this->Flash->success(__('El dato fue guardado'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
			}
		} else {
			$options = ['conditions' => ['Amenitiesconfig.' . $this->Amenitiesconfig->primaryKey => $id]];
			$this->request->data = $this->Amenitiesconfig->find('first', $options);
		}
		$amenities = $this->Amenitiesconfig->Amenitie->find('list');
		$this->set(compact('amenities'));
	}

	public function delete($id = null) {
		$this->Amenitiesconfig->id = $id;
		if (!$this->Amenitiesconfig->exists()) {
			$this->Flash->error(__('El dato es inexistente'));
			return $this->redirect(['action' => 'index']);
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Amenitiesconfig->delete()) {
			$this->Flash->success(__('El dato fue eliminado'));
		} else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
		}
		return $this->redirect($this->referer());
	}
}
