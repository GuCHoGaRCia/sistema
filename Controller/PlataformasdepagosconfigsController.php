<?php

App::uses('AppController', 'Controller');

class PlataformasdepagosconfigsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_edit($id = null) {
        $this->layout = '';
        if ($this->request->is(['post', 'put'])) {
            if ($this->Plataformasdepagosconfig->save($this->request->data)) {
                die(json_encode(['e' => 0]));
            } else {
                die(json_encode(['e' => 1, 'd' => $this->Plataformasdepagosconfig->validationErrors]));
            }
        } else {
            $options = ['conditions' => ['Plataformasdepagosconfig.client_id' => $id], 'recursive' => 1];
            $busca = $this->Plataformasdepagosconfig->find('first', $options);
            if (empty($busca)) {
                $this->Plataformasdepagosconfig->create();
                $this->Plataformasdepagosconfig->save(['client_id' => $id, 'plataformasdepago_id' => 0, 'datointerno' => 0, 'minimo' => 0, 'comision' => 0, 'codigo' => 0]);
                $busca = $this->Plataformasdepagosconfig->find('first', ['conditions' => ['Plataformasdepagosconfig.client_id' => $id]]);
            }
            $this->request->data = $busca;
        }
        $x = ClassRegistry::init('Client');
        $this->set('name', $x->getClientName($id));
        $c = ClassRegistry::init('Consorcio');
        $consorcios = $c->getConsorciosList($id);
        $plataformasdepagos = $this->Plataformasdepagosconfig->Plataformasdepago->getList();
        $this->set(compact('plataformasdepagos', 'consorcios'));
    }

}
