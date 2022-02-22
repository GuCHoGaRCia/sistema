<?php

App::uses('AppController', 'Controller');

class AuditsController extends AppController {

    public $helpers = ['AuditLog'];

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'panel_add');
    }

    public function panel_index() {
        $conditions = [];
        $d = isset($this->request->data['Audit']['desde']) ? $this->request->data['Audit']['desde'] : '';
        $h = isset($this->request->data['Audit']['hasta']) ? $this->request->data['Audit']['hasta'] : '';
        $c = isset($this->request->data['Audit']['client_id']) ? $this->request->data['Audit']['client_id'] : '';
        $b = isset($this->request->data['Audit']['buscar']) ? $this->Audit->filterName($this->request->data['Audit']) : [];
        $conditions += !empty($d) ? ['date(Audit.created) >=' => $this->Audit->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Audit.created) <=' => $this->Audit->fecha($h)] : [];
        $conditions += $b;
        $x = isset($this->request->params['named']['buscar']) ? ['Audit.description' => trim($this->request->params['named']['buscar'])] : [];
        $conditions += $x;
        if (!empty($c)) {
            $conditions += ['Audit.client_id' => $c];
        }
        $this->Paginator->settings = array('conditions' => $conditions, 'order' => 'Audit.created desc', 'contain' => ['AuditDelta']);

        if ((!isset($this->request->data['filter']['client_id']) && !isset($this->request->data['Audit']['buscar'])) || !empty($x)) {
            $this->Paginator->settings += ['limit' => 50];
        } else {
            $this->Paginator->settings += ['limit' => 1500, 'maxLimit' => 1500];
        }
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('c', $c);

        $this->set('audits', $this->paginar($this->Paginator));
        $this->set('clientes', $this->Audit->Client->find('list', ['order' => 'Client.name']));
    }

    public function panel_view($id = null) {
        if (!$this->Audit->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Audit.id' => $id], 'contain' => ['AuditDelta']];
        $this->set('audits', $this->Audit->find('first', $options));
        $this->set('clientes', $this->Audit->Client->find('list', ['order' => 'Client.name']));
    }

//    public function panel_delete($id = null) {
//        $this->Audit->id = $id;
//        if (!$this->Audit->exists()) {
//            $this->Flash->error(__('El dato es inexistente'));
//            return $this->redirect(['action' => 'index']);
//        }
//        $this->request->allowMethod('post', 'delete');
//        if ($this->Audit->undo($id)) {
//            $this->Flash->success(__('El dato fue anulado'));
//        } else {
//            $this->Flash->error(__('El dato no pudo ser anulado, intente nuevamente'));
//        }
//        return $this->redirect($this->referer());
//    }
}
