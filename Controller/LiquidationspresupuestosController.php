<?php

App::uses('AppController', 'Controller');

class LiquidationspresupuestosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $conditions = ['c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'c2.habilitado' => 1, $this->Liquidationspresupuesto->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['c2.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions, 'recursive' => 0, 'joins' => [['table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => ['c2.id=Liquidation.consorcio_id']]],
            'order' => 'c2.code desc,Liquidation.id desc,Liquidation.name', 'fields' => ['Liquidation.id', 'Liquidation.periodo', 'Coeficiente.id', 'Coeficiente.name', 'c2.id', 'c2.name', 'Liquidationspresupuesto.id', 'Liquidationspresupuesto.total']];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('liquidationspresupuestos', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Liquidationspresupuesto->Liquidation->Consorcio->getConsorciosList());
    }

}
