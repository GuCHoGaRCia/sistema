<?php

App::uses('AppController', 'Controller');

class CoeficientesPropietariosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if ($this->request->is('post') && isset($this->request->data['filter']['consorcio'])) {
            $conditions = ['conditions' => ['Coeficiente.consorcio_id' => $this->request->data['filter']['consorcio'], 'Consorcio.habilitado' => '1', 'Client.id' => $_SESSION['Auth']['User']['client_id']], 'order' => 'Coeficiente.id', 'recursive' => 0,
                'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]]];
        } else {
            $id = $this->CoeficientesPropietario->Propietario->Consorcio->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0]);
            if (empty($id)) {
                $this->Flash->error(__('Debe agregar Consorcios antes de modificar los coeficientes de cada propietario'));
                $this->redirect(['controller' => 'consorcios', 'action' => 'add']);
            }
            $conditions = ['conditions' => ['Consorcio.habilitado' => '1', 'Coeficiente.consorcio_id' => $id['Consorcio']['id']], 'order' => 'Coeficiente.id',
                'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Coeficiente.consorcio_id']],
                    ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]]];
        }
        $coefs = $this->CoeficientesPropietario->Coeficiente->find('all', $conditions);
        $propietarios = $this->CoeficientesPropietario->find('all', ['conditions' => ['Propietario.consorcio_id' => isset($id['Consorcio']['id']) ? $id['Consorcio']['id'] : $this->request->data['filter']['consorcio']],
            'order' => 'Propietario.orden,Coeficiente.id',
            'recursive' => 0,
            'fields' => ['CoeficientesPropietario.id', 'CoeficientesPropietario.coeficiente_id', 'CoeficientesPropietario.propietario_id', 'CoeficientesPropietario.value', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']
        ]);
        if (empty($propietarios)) {// no existen los coeficientes de los propietarios, los creo
            $p = $this->CoeficientesPropietario->Propietario->getPropietariosId(isset($id['Consorcio']['id']) ? $id['Consorcio']['id'] : $this->request->data['filter']['consorcio']);
            foreach ($p as $r) {
                foreach ($coefs as $k => $v) {
                    $this->CoeficientesPropietario->create();
                    $this->CoeficientesPropietario->save(['coeficiente_id' => $v['Coeficiente']['id'], 'propietario_id' => $r['Propietario']['id'], 'value' => 0], ['callbacks' => false, 'validate' => false]);
                }
            }
            $propietarios = $this->CoeficientesPropietario->find('all', ['conditions' => ['Propietario.consorcio_id' => isset($id['Consorcio']['id']) ? $id['Consorcio']['id'] : $this->request->data['filter']['consorcio']],
                'order' => 'Propietario.orden,Coeficiente.id',
                'recursive' => 0,
                'fields' => ['CoeficientesPropietario.id', 'CoeficientesPropietario.coeficiente_id', 'CoeficientesPropietario.propietario_id', 'CoeficientesPropietario.value', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']
            ]);
        }
        $prop = [];
        foreach ($propietarios as $k => $v) { // para cada coeficiente, los junto x propietario
            $prop[$v['CoeficientesPropietario']['propietario_id']]['prop'] = $v['Propietario'];
            $prop[$v['CoeficientesPropietario']['propietario_id']][$v['CoeficientesPropietario']['coeficiente_id']] = ['key' => $v['CoeficientesPropietario']['id'], 'value' => $v['CoeficientesPropietario']['value']];
        }

        $this->set('consorcios', $this->CoeficientesPropietario->Propietario->Consorcio->getConsorciosList());
        $this->set('coeficientes', $coefs);
        $this->set('prop', $prop);
    }

}
