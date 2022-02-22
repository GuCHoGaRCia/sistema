<?php

App::uses('AppController', 'Controller');

class GastosParticularesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'borrarMultiple');
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], /* 'Liquidation.bloqueada' => 0 */];
        if (isset($this->request->data['GastosParticulare']['consorcio']) && $this->request->data['GastosParticulare']['consorcio'] === "") {
            unset($this->request->data['GastosParticulare']['consorcio']);
        }
        if (isset($this->request->data['GastosParticulare']['consorcio'])) {
            $conditions += ['Liquidation.consorcio_id' => $this->request->data['GastosParticulare']['consorcio']];
            $this->set('c', $this->request->data['GastosParticulare']['consorcio']);
        }
        $b = [];
        $buscar = '';
        if (isset($this->request->data['GastosParticulare']['buscar']) && !empty($this->request->data['GastosParticulare']['buscar'])) {
            $buscar = $this->request->data['GastosParticulare']['buscar'];
            $b = [
                'OR' => [
                    'Propietario.name LIKE' => '%' . $buscar . '%',
                    'Propietario.unidad LIKE' => '%' . $buscar . '%',
                    'Cuentasgastosparticulare.name LIKE' => '%' . $buscar . '%',
                    'GastosParticulare.description LIKE' => '%' . $buscar . '%',
                    'GastosParticulare.amount' => $buscar
            ]];
        }
        $this->set('buscar', $buscar);

        $d = isset($this->request->data['GastosParticulare']['desde']) ? $this->request->data['GastosParticulare']['desde'] : '';
        $h = isset($this->request->data['GastosParticulare']['hasta']) ? $this->request->data['GastosParticulare']['hasta'] : '';
        $conditions += !empty($d) ? ['GastosParticulare.date >=' => $this->GastosParticulare->fecha($d)] : [];
        $conditions += !empty($h) ? ['GastosParticulare.date <=' => $this->GastosParticulare->fecha($h)] : [];

        $this->GastosParticulare->recursive = 0;
        $this->Paginator->settings = ['conditions' => $conditions + $b, 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]],
            'fields' => ['GastosParticulare.id', 'GastosParticulare.date', 'GastosParticulare.description', 'GastosParticulare.amount', 'GastosParticulare.heredable', 'Liquidation.id', 'Liquidation.bloqueada', 'Liquidation.periodo', 'Cuentasgastosparticulare.id', 'Cuentasgastosparticulare.name', 'Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Consorcio.name', 'Consorcio.id', 'Coeficiente.name', 'Coeficiente.id'],
            'order' => 'GastosParticulare.id desc,Liquidation.id desc,Propietario.unidad desc'];

        if (!isset($this->request->data['GastosParticulare']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
        } else {
            $this->Paginator->settings += ['limit' => 5000, 'maxLimit' => 5000];
        }
        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('gastosParticulares', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->GastosParticulare->Liquidation->Consorcio->getConsorciosList());
    }

    public function view($id = null) {
        if (!$this->GastosParticulare->exists($id) || $this->GastosParticulare->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosParticulare.id' => $id), 'recursive' => 1, 'joins' => array(array('table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Liquidation.consorcio_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('GastosParticulare.' . $this->GastosParticulare->primaryKey => $id));
        $this->set('gastosParticulare', $this->GastosParticulare->find('first', $options));
    }

    public function add($id = null) {
        if ($this->request->is('post') && isset($this->request->data['GastosParticulare']['cuentasgastosparticulare_id'])) {
            $this->GastosParticulare->create();
            if ($this->GastosParticulare->guardar($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        if ($this->request->is('post') && isset($this->request->data['GastosParticulare']['liquidation_id'])) {
            $consorcio_id = $this->GastosParticulare->Liquidation->getConsorcioId($this->request->data['GastosParticulare']['liquidation_id']);
            $liquidations = $this->GastosParticulare->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.id' => $this->request->data['GastosParticulare']['liquidation_id']), 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']));
            $cuentasgastosparticulares = $this->GastosParticulare->Cuentasgastosparticulare->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $consorcio_id), 'recursive' => 0));
            if (count($cuentasgastosparticulares) == 0) {
                $this->Flash->error(__('Debe crear Cuentas (men&uacute Gastos) antes de agregar un gasto'));
                return $this->redirect(['action' => 'index']);
            }
            $propietarios = $this->GastosParticulare->Propietario->getList($consorcio_id);
            if (count($propietarios) == 0) {
                $this->Flash->error(__('Debe agregar Propietarios (men&uacute Datos) antes de agregar un gasto'));
                return $this->redirect(['action' => 'index']);
            }
            $coeficientes = $this->GastosParticulare->Coeficiente->getList($consorcio_id);
            if (count($coeficientes) == 0) {
                $this->Flash->error(__('Debe Crear Coeficientes (men&uacute Datos) antes de agregar un gasto'));
                return $this->redirect(['action' => 'index']);
            }
            $this->set(compact('liquidations', 'cuentasgastosparticulares', 'propietarios', 'coeficientes'));
            $this->render('add2');
        }
        $liquidations = $this->GastosParticulare->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Liquidation.inicial' => 0, 'Liquidation.bloqueada' => 0),
            'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2'], 'order' => 'Consorcio.code'));
        if (count($liquidations) == 0) {
            $this->Flash->error(__('Debe crear una liquidaci&oacute;n (men&uacute Liquidaciones) antes de agregar un gasto'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('liquidations'));
    }

    public function delete($id = null) {
        $this->GastosParticulare->id = $id;
        if (!$this->GastosParticulare->exists() || $this->GastosParticulare->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosParticulare.id' => $id, 'Liquidation.bloqueada' => 0), 'recursive' => 1, 'joins' => array(array('table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Liquidation.consorcio_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->GastosParticulare->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function borrarMultiple() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            die;
        }
        $ids = $this->request->data['ids'];
        $cont = 0;
        foreach ($ids as $v) {
            $this->GastosParticulare->id = $v;
            if (!$this->GastosParticulare->exists() || $this->GastosParticulare->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'GastosParticulare.id' => $v, 'Liquidation.bloqueada' => 0), 'recursive' => 1, 'joins' => array(array('table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Liquidation.consorcio_id'))))) == 0) {
                $cont++;
                continue;
            }
            $this->request->allowMethod('post', 'delete');
            if (!$this->GastosParticulare->delete()) {
                $cont++;
            }
        }
        $cantelementos = count($ids);
        $borrados = $cantelementos - $cont;
        if ($borrados > 0) {
            $this->Flash->success(__('Se borraron exitosamente ' . $borrados . ' Gastos Particulares'));
        }
        if ($cont > 0) {
            $this->Flash->error(__('No se borraron ' . $cont . ' Gastos Particulares'));
        }
        $this->layout = '';
        $this->autoRender = false;
    }

}
