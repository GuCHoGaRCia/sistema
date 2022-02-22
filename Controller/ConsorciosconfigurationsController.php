<?php

App::uses('AppController', 'Controller');

class ConsorciosconfigurationsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['Client']['id'], 'Consorcio.habilitado' => 1, $this->Consorciosconfiguration->parseCriteria($this->passedArgs), 'LiquidationsType.enabled' => 1];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Consorciosconfiguration.consorcio_id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['LiquidationsType.client_id=Client.id']]],
            'fields' => ['Consorcio.name', 'Consorciosconfiguration.*'],
            'group' => ['Consorciosconfiguration.id'],
            'order' => ['Consorcio.code', 'LiquidationsType.prefijo']];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 1000, 'maxLimit' => 1000];
        }
        $this->set('consorcios', $this->Consorciosconfiguration->Consorcio->getConsorciosList());
        $this->set('lt', $this->Consorciosconfiguration->LiquidationsType->getLiquidationsTypes(null, true));
        $this->set('consorciosconfigurations', $this->paginar($this->Paginator));
    }

}
