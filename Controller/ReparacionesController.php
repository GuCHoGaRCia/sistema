<?php

App::uses('AppController', 'Controller');

class ReparacionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'delImagen', 'view', 'add'); // permito blackhole x ajax
    }

    public function index() {
        $conditions = isset($this->request->data['Reparacione']['consorcio_id']) && !empty($this->request->data['Reparacione']['consorcio_id']) ? ['Reparacione.consorcio_id' => $this->request->data['Reparacione']['consorcio_id']] : [];
        $conditions += isset($this->request->data['Reparacione']['propietario_id']) && !empty($this->request->data['Reparacione']['propietario_id']) ? ['Reparacione.propietario_id' => $this->request->data['Reparacione']['propietario_id']] : [];
        $conditions += isset($this->request->data['Reparacione']['proveedores']) && !empty($this->request->data['Reparacione']['proveedores']) ? ['Reparacionesactualizacionesproveedore.proveedor_id' => $this->request->data['Reparacione']['proveedores']] : [];
        $conditions += isset($this->request->data['Reparacione']['reparacionesestado_id']) && !empty($this->request->data['Reparacione']['reparacionesestado_id']) ? ['Reparacionesestado.id' => $this->request->data['Reparacione']['reparacionesestado_id']] : [];
        $conditions += ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacione.anulada' => 0];
        if (isset($this->request->data['Reparacione']['buscar']) && !empty($this->request->data['Reparacione']['buscar'])) {
            $conditions += ['OR' => ['Reparacione.concepto LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%',
                    'Reparacione.observaciones LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%',
                    'Consorcio.code' => $this->request->data['Reparacione']['buscar'],
                    'Propietario.name LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%']];
        }
        $this->Paginator->settings = ['conditions' => $conditions + ['Consorcio.habilitado' => 1],
            'joins' => [['table' => 'reparacionesactualizaciones', 'alias' => 'Reparacionesactualizacione', 'type' => 'left', 'conditions' => ['Reparacione.id=Reparacionesactualizacione.reparacione_id']],
                ['table' => 'reparacionesactualizacionesproveedores', 'alias' => 'Reparacionesactualizacionesproveedore', 'type' => 'left', 'conditions' => ['Reparacionesactualizacionesproveedore.reparacionesactualizacione_id=Reparacionesactualizacione.id']]],
            'contain' => ['Propietario', 'Consorcio', 'Reparacionesestado', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore'],
            'fields' => ['Consorcio.id', 'Consorcio.name', 'Propietario.id', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Reparacione.id', 'Reparacione.concepto', 'Reparacione.fecha', 'Reparacione.recordatorio', 'Reparacionesestado.nombre', 'Reparacionesestado.color', 'Reparacione.modified', 'Reparacione.created'],
            'order' => 'Reparacione.modified desc',
            'group' => 'Reparacione.id',
            'limit' => 100, 'maxLimit' => 100
        ];
        if (isset($this->request->data['Reparacione']['consorcio_id'])) {
            $this->Paginator->settings['limit'] = 10000;
            $this->Paginator->settings['maxLimit'] = 10000;
        }

        $this->set('reparaciones', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Reparacione->Consorcio->getConsorciosList());
        $this->set('reparacionesestados', $this->Reparacione->Reparacionesestado->get());
        $this->set('proveedores', $this->Reparacione->Reparacionesactualizacione->Reparacionesactualizacionesproveedore->Proveedor->getList());
    }

    public function anuladas() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacione.anulada' => 1, $this->Reparacione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions + ['Consorcio.habilitado' => 1],
            'contain' => ['Propietario', 'Consorcio', 'Reparacionesestado'],
            'fields' => ['Consorcio.id', 'Consorcio.name', 'Propietario.id', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Reparacione.id', 'Reparacione.concepto', 'Reparacione.fecha', 'Reparacione.recordatorio', 'Reparacionesestado.nombre', 'Reparacione.modified', 'Reparacione.created'],
            'order' => 'Reparacione.modified desc'
        ];

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 10000, 'maxLimit' => 10000];
        }
        $this->set('reparaciones', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Reparacione->Consorcio->getConsorciosList());
    }

    /* public function finalizadas() {// reparacionesestado_id=5 finalizada
      $conditions = isset($this->request->data['Reparacione']['consorcio_id']) && !empty($this->request->data['Reparacione']['consorcio_id']) ? ['Reparacione.consorcio_id' => $this->request->data['Reparacione']['consorcio_id']] : [];
      $conditions += isset($this->request->data['Reparacione']['propietario_id']) && !empty($this->request->data['Reparacione']['propietario_id']) ? ['Reparacione.propietario_id' => $this->request->data['Reparacione']['propietario_id']] : [];

      $conditions += ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacione.anulada' => 0, 'Reparacione.reparacionesestado_id' => 5];
      if (isset($this->request->data['Reparacione']['buscar']) && !empty($this->request->data['Reparacione']['buscar'])) {
      $conditions += ['OR' => ['Reparacione.concepto LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%',
      'Reparacione.observaciones LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%',
      'Consorcio.code' => $this->request->data['Reparacione']['buscar'],
      'Propietario.name LIKE' => '%' . $this->request->data['Reparacione']['buscar'] . '%']];
      }
      $this->Paginator->settings = ['conditions' => $conditions + ['Consorcio.habilitado' => 1],
      'contain' => ['Propietario', 'Consorcio', 'Reparacionesestado'],
      'fields' => ['Consorcio.id', 'Consorcio.name', 'Propietario.id', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Reparacione.id', 'Reparacione.concepto', 'Reparacione.fecha', 'Reparacione.recordatorio', 'Reparacionesestado.nombre', 'Reparacione.modified', 'Reparacione.created'],
      'order' => 'Reparacione.modified desc',
      'limit' => 10000, 'maxLimit' => 10000
      ];

      $this->set('reparaciones', $this->paginar($this->Paginator));
      $this->set('consorcios', $this->Reparacione->Consorcio->getConsorciosList());
      } */

    public function supervisor() {
        $this->Paginator->settings = ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, $this->Reparacione->parseCriteria($this->passedArgs),
                'Reparacionesactualizacionessupervisores.reparacionessupervisore_id' => $_SESSION['Auth']['User']['id']],
            'contain' => ['Propietario', 'Consorcio', 'Reparacionesestado'],
            'fields' => ['Consorcio.id', 'Consorcio.name', 'Propietario.id', 'Propietario.name', 'Propietario.code', 'Propietario.unidad', 'Reparacione.id', 'Reparacione.concepto', 'Reparacione.fecha', 'Reparacione.recordatorio', 'Reparacionesestado.nombre', 'Reparacione.modified', 'Reparacione.created'],
            'order' => 'Reparacionesestado.id,Reparacione.modified desc'];
        $this->Prg->commonProcess();
        $this->set('reparaciones', $this->paginar($this->Paginator));
    }

    /*
     * Reparaciones NUEVAS
     */

    public function view($id = null) {
        if (!$this->Reparacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
        }
        $options = array('conditions' => array('Reparacione.id' => $id), 'contain' => ['Reparacionesestado',
                'Reparacionesactualizacione', 'Reparacionesactualizacione.Reparacionesestado', 'Reparacionesactualizacione.Reparacionesactualizacionesadjunto',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Llave.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Proveedor.name',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Reparacionessupervisore.nombre',
                'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Propietario.name2',
                'Reparacionesactualizacione.Reparacionesactualizacionessupervisore', 'Consorcio.name', 'Propietario.name2'], 'recursive' => 0);
        $this->set('reparaciones', $this->Reparacione->find('first', $options));
        $proveedors = $this->Reparacione->Consorcio->Client->Proveedor->getList();
        $estados = $this->Reparacione->Reparacionesestado->getAll();
        $reparacionessupervisores = $this->Reparacione->Consorcio->Client->Reparacionessupervisore->getList();
        $this->set('users', $this->Reparacione->User->getList());
        $this->set(compact('proveedors', 'reparacionessupervisores', 'estados'));
        $this->layout = '';
    }

    public function historial($propietario_id = null) {
        $resul = [];
        if ($this->Reparacione->Consorcio->Propietario->canEdit($propietario_id)) {
            $options = array('conditions' => array('Reparacione.propietario_id' => $propietario_id), 'contain' => ['Reparacionesestado.nombre',
                    'Reparacionesactualizacione', 'Reparacionesactualizacione.Reparacionesestado.nombre', 'Reparacionesactualizacione.Reparacionesactualizacionesadjunto',
                    'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento', 'Reparacionesactualizacione.Reparacionesactualizacionesproveedore',
                    'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Llave.name2',
                    'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Proveedor.name',
                    'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Reparacionessupervisore.nombre',
                    'Reparacionesactualizacione.Reparacionesactualizacionesllavesmovimiento.Llavesmovimiento.Propietario.name2',
                    'Reparacionesactualizacione.Reparacionesactualizacionessupervisore', 'Consorcio.name', 'Propietario.name2'],
                'recursive' => 0, 'order' => 'Reparacione.fecha desc');
            $resul = $this->Reparacione->find('all', $options);
            $proveedors = $this->Reparacione->Consorcio->Client->Proveedor->getList();
            $reparacionessupervisores = $this->Reparacione->Consorcio->Client->Reparacionessupervisore->getList();
            $this->set('users', $this->Reparacione->User->getList());
            $this->set(compact('proveedors', 'reparacionessupervisores'));
        }
        $this->set('reparaciones', $resul);
        $this->layout = '';
    }

    /*
     * Reparaciones VIEJAS
     */

    public function view2($id = null) {
        if (!$this->Reparacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Reparacione.' . $this->Reparacione->primaryKey => $id), 'contain' => ['Reparacionesestado', 'Reparacionesadjunto']);
        $this->set('reparacione', $this->Reparacione->find('first', $options));
        $this->layout = '';
    }

    public function add() {
        if ($this->request->is('post')) {
            if (!$this->request->is('ajax')) {
                die();
            }
            $resul = $this->Reparacione->guardar($this->request);
            if (isset($resul['e']) && $resul['e'] == 0) {
                $this->Flash->success(__('El dato fue guardado correctamente'));
            }
            die(json_encode($resul));
        }
        $consorcios = $this->Reparacione->Consorcio->getConsorciosList();
        if (count($consorcios) == 0) {
            $this->Flash->error(__('Debe crear un Consorcio (men&uacute Datos) antes de agregar una reparaci&oacute;n'));
            return $this->redirect(['action' => 'index']);
        }
        $users = $this->Reparacione->User->getList();
        $proveedors = $this->Reparacione->User->Client->Proveedor->getList();
        $reparacionessupervisores = $this->Reparacione->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->getList();
        $reparacionesestados = $this->Reparacione->Reparacionesestado->get();
        $llaves = $this->Reparacione->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->Llave->getDisponibles();
        $this->set(compact('consorcios', 'reparacionesestados', 'users', 'proveedors', 'reparacionessupervisores', 'llaves'));
    }

    /*
     * Edita las reparaciones de la version vieja
     */

    public function edit($id = null) {
        if (!$this->Reparacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Reparacione->guardarOLD($this->request)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Reparacione.' . $this->Reparacione->primaryKey => $id), 'contain' => ['Reparacionesadjunto']);
            $this->request->data = $this->Reparacione->find('first', $options);
        }
        $consorcios = $this->Reparacione->Consorcio->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $this->request->data['Reparacione']['consorcio_id'])));
        $propietarios = $this->Reparacione->Propietario->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $this->request->data['Reparacione']['consorcio_id']), 'recursive' => 0));
        $reparacionesestados = $this->Reparacione->Reparacionesestado->get();
        $this->set(compact('consorcios', 'propietarios', 'reparacionesestados'));
    }

    public function delImagen() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Reparacione->Reparacionesadjunto->delImagen($this->request->data['id'])));
    }

    public function delete($id = null) {
        if (!$this->Reparacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        $this->Reparacione->id = $id;
        if ($this->Reparacione->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('La reparaci&oacute;n posee llaves entregadas, no se puede eliminar'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function undo($id = null) {
        if (!$this->Reparacione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Reparacione->id = $id;
        $anulada = $this->Reparacione->field('anulada');
        $this->Reparacione->saveField('anulada', !$anulada);
        if ($anulada) {
            $this->Flash->success(__('La Reparaci&oacute;n fue restaurada correctamente'));
            return $this->redirect(['action' => 'anuladas']);
        } else {
            $this->Flash->success(__('La Reparaci&oacute;n fue anulada correctamente'));
            return $this->redirect(['action' => 'index']);
        }
    }

}
