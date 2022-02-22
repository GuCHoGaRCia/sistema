<?php

App::uses('AppController', 'Controller');

class AjustesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'add', 'getAjustesPeriodo');
    }

    public function index() {
        $this->Ajuste->recursive = 0;
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1];
        $b = isset($this->request->data['Ajuste']['buscar']) ? ['OR' => ['Propietario.name like' => '%' . $this->request->data['Ajuste']['buscar'] . '%', 'Ajuste.concepto like' => '%' . $this->request->data['Ajuste']['buscar'] . '%']] : [];
        $d = isset($this->request->data['Ajuste']['desde']) ? $this->request->data['Ajuste']['desde'] : '';
        $h = isset($this->request->data['Ajuste']['hasta']) ? $this->request->data['Ajuste']['hasta'] : '';
        $c = isset($this->request->data['Ajuste']['consorcio']) && $this->request->data['Ajuste']['consorcio'] !== '' ? ['Consorcio.id' => $this->request->data['Ajuste']['consorcio']] : [];
        $conditions += isset($this->request->data['Ajuste']['anulado']) && $this->request->data['Ajuste']['anulado'] == '1' ? [] : ['Ajuste.anulado' => 0];
        $conditions += !empty($d) ? ['date(Ajuste.created) >=' => $this->Ajuste->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Ajuste.created) <=' => $this->Ajuste->fecha($h)] : [];
        $conditions += $c + $b;
        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']],
                ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Ajuste.user_id']]],
            'contain' => ['Propietario', 'Ajustetipoliquidacione'],
            'fields' => ['Propietario.name', 'Propietario.id', 'Propietario.consorcio_id', 'User.id', 'User.name', 'Ajuste.fecha', 'Ajuste.created', 'Ajuste.concepto', 'Ajuste.modified', 'Ajuste.id', 'Ajuste.importe', 'Ajuste.anulado', 'Consorcio.name', 'Consorcio.habilitado'],
            'order' => 'Ajuste.fecha desc'];
        if (!isset($this->request->data['Ajuste']) || empty($this->request->data['Ajuste'])) {
            $this->Paginator->settings += ['limit' => 10];
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('c', $c);
        $this->set('bloqueadas', $this->Ajuste->Propietario->Consorcio->Liquidation->getFechaBloqueadaXTipoLiquidacion());
        $this->set('ajustes', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Ajuste->Propietario->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->Ajuste->exists($id) || $this->Ajuste->find('count', array('conditions' => array('User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Ajuste.id' => $id), 'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Ajuste.user_id']]])) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('ajuste', $this->Ajuste->find('first', ['conditions' => ['Ajuste.id' => $id], 'fields' => ['Ajuste.*', 'Propietario.*', 'Consorcio.*', 'User.*', 'Client.*'],
                    'contain' => ['Ajustetipoliquidacione', 'Ajustetipoliquidacione.LiquidationsType.name'],
                    'joins' => [['table' => 'ajustetipoliquidaciones', 'alias' => 'Ajustetipoliquidacione', 'type' => 'left', 'conditions' => ['Ajuste.id=Ajustetipoliquidacione.ajuste_id']],
                        ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Ajuste.propietario_id=Propietario.id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                        ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']],
                        ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['Ajuste.user_id=User.id']],
                        ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['LiquidationsType.id=Ajustetipoliquidacione.liquidations_type_id']]
                    ]
        ]));
        $this->layout = '';
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Ajuste->create();
            if ($this->Ajuste->procesaAjusteManual($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $this->set('consorcios', $this->Ajuste->Propietario->Consorcio->getConsorciosList());
    }

    /*
     * Son los ajustes para todos los propietarios del consorcio
     */

    public function periodo($id = null) {
        if ($this->request->is('post')) {
            if ($this->Ajuste->procesaAjustePeriodo($this->request->data)) {
                $this->Flash->success(__('El Ajuste fue guardado correctamente'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, verifique los errores'));
            }
        }
        $liquidations = $this->Ajuste->Propietario->Consorcio->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.inicial' => 0), 'recursive' => -1, 'limit' => 1, 'contain' => ['Consorcio']));
        if (count($liquidations) == 0) {
            $this->Flash->error(__('Debe agregar una Liquidaci&oacute;n (men&uacute Liquidaciones) antes de agregar un Ajuste'));
            return $this->redirect(['action' => 'index']);
        }
        $propietarios = $this->Ajuste->Propietario->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0, 'contain' => 'Consorcio', 'limit' => 1));
        if (count($propietarios) == 0) {
            $this->Flash->error(__('Debe agregar Propietarios (men&uacute Datos) antes de agregar un Ajuste'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('consorcios', $this->Ajuste->Propietario->Consorcio->getConsorciosList());
        $this->set('id', $id);
    }

    public function getAjustesPeriodo() {
        if (!$this->request->is('ajax')) {
            die();
        }
        $this->layout = '';
        $this->autoRender = false;
        die(json_encode(['consorcios' => $this->Ajuste->getAjustesPeriodo($this->request->data['c']), 'limite' => $this->Ajuste->Propietario->Consorcio->Liquidation->getLastBloqueadaClosedDate($this->request->data['c'])]));
    }

    public function delete($id = null) {
        if (!$this->Ajuste->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Ajuste->id = $id;
        if ($this->Ajuste->undo($id)) {
            $this->Flash->success(__('El dato fue anulado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser anulado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
