<?php

App::uses('AppController', 'Controller');

class ComunicacionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'add', 'multienvio'); // permito blackhole x ajax
    }

    public function index() {
        $conditions = ['Comunicacione.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => '1', $this->Comunicacione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Comunicacionesdetalle.consorcio_id' => $this->request->data['filter']['consorcio']];
        }
        $this->passedArgs = []; // para evitar
        $this->Paginator->settings = ['conditions' => [$conditions],
            'order' => 'Comunicacione.created desc',
            'recursive' => 0,
            'fields' => ['DISTINCT Comunicacione.id', 'Comunicacione.enviada', 'Comunicacione.asunto', 'Comunicacione.created', 'Consorcio.name', 'Consorcio.id'],
            'joins' => [['table' => 'comunicacionesdetalles', 'alias' => 'Comunicacionesdetalle', 'type' => 'left', 'conditions' => ['Comunicacionesdetalle.comunicacione_id=Comunicacione.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Comunicacionesdetalle.consorcio_id=Consorcio.id']]],
            'contain' => ['Comunicacionesdetalle']];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('comunicaciones', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Comunicacione->Comunicacionesdetalle->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Comunicacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = ['conditions' => ['Comunicacione.' . $this->Comunicacione->primaryKey => $id], 'recursive' => 0, 'contain' => ['Comunicacionesdetalle.Consorcio.name', 'Comunicacionesdetalle.Propietario.email', 'Comunicacionesadjunto']];
        $this->set('data', $this->Comunicacione->find('first', $options));
        $this->layout = '';
        $this->render('vistapreviacomunicacion');
    }

    public function add() {
        if ($this->request->is('post')) {
            $resul = $this->Comunicacione->guardar($this->request);
            if (empty($resul)) {
                $this->Flash->success(__('El dato fue guardado'));
                die(json_encode(['e' => 0]));
            } else {
                die(json_encode(['e' => 1, 'd' => h($resul)]));
            }
        }
        // los consorcios habilitados (estado=2). Estado=3 es deshabilitado
        $consorcios = $this->Comunicacione->Client->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function enviar($id = null) {
        if (!$this->Comunicacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->layout = '';
        $this->autoRender = false;
        $view = new View($this, false);
        $view->layout = '';
        $view->set('client', $_SESSION['Auth']['User']['Client']);
        $view->set('data', $this->Comunicacione->find('first', ['conditions' => ['Comunicacione.id' => $id], 'recursive' => 1, 'joins' => [['table' => 'comunicacionesdetalles', 'alias' => 'Comunicacionesdetalle', 'type' => 'left', 'conditions' => ['Comunicacionesdetalle.comunicacione_id=Comunicacione.id']]]]));

        $html = preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'), array('>', '<', '\\1'), $view->render('envioxmail'));
        if ($this->Comunicacione->encolar($id, $html)) {
            $this->Comunicacione->id = $id;
            $this->Comunicacione->saveField('enviada', 1);
            $this->Flash->success(__('La comunicaci&oacute;n fue enviada correctamente')); //mentira, todavia no. La encolé
        } else {
            $this->Flash->error(__('La comunicaci&oacute;n no pudo ser enviada, intente nuevamente'));
        }
        $this->redirect(['action' => 'index']);
    }

    public function multienvio() {
        $this->layout = '';
        $this->autoRender = false;
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            return json_encode(['e' => 1, 'd' => '1El dato es inexistente']);
        }
        $ids = $this->request->data['ids'];
        foreach ($ids as $v) {
            if (!$this->Comunicacione->canEdit($v)) {
                return json_encode(['e' => 1, 'd' => '2El dato es inexistente']);
            }
        }

        // todo salió bien, prorrateo todas
        foreach ($ids as $v) {
            $this->layout = '';
            $this->autoRender = false;
            $view = new View($this, false);
            $view->layout = '';
            $view->set('client', $_SESSION['Auth']['User']['Client']);
            $view->set('data', $this->Comunicacione->find('first', ['conditions' => ['Comunicacione.id' => $v], 'recursive' => 1, 'joins' => [['table' => 'comunicacionesdetalles', 'alias' => 'Comunicacionesdetalle', 'type' => 'left', 'conditions' => ['Comunicacionesdetalle.comunicacione_id=Comunicacione.id']]]]));

            $html = preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'), array('>', '<', '\\1'), $view->render('envioxmail'));
            if ($this->Comunicacione->encolar($v, $html)) {
                $this->Comunicacione->id = $v;
                $this->Comunicacione->saveField('enviada', 1);
            }
        }

        $this->Flash->success(__('Se enviaron exitosamente <span title="' . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 2) . '">' . count($ids) . '</span> Comunicaciones'));
        return json_encode(['e' => 0]);
    }

    public function getPropietarios() {
        if (!$this->request->is('ajax')) {
            die;
        }
        die(json_encode($this->Comunicacione->Client->Aviso->getPropietarios($this->data['con'])));
    }

    public function edit($id = null) {
        if (!$this->Comunicacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Comunicacione->save($this->request->data, ['validate' => false])) {// faltan campos, asi lo guarda sin problema
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Comunicacione.' . $this->Comunicacione->primaryKey => $id));
            $this->request->data = $this->Comunicacione->find('first', $options);
        }
    }

    public function delete($comunicacione_id = null) {
        $this->request->allowMethod('post', 'delete');
        if (!$this->Comunicacione->canEdit($comunicacione_id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Comunicacione->delete($comunicacione_id)) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
