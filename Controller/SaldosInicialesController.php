<?php

App::uses('AppController', 'Controller');

class SaldosInicialesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        if ($this->request->is('post') && isset($this->request->data['SaldosIniciale']['consorcio_id'])) {
            $this->SaldosIniciale->verificaSaldos($this->request->data['SaldosIniciale']['consorcio_id']);
            $resul = $this->SaldosIniciale->find('all', [
                'conditions' => ['SaldosIniciale.liquidations_type_id' => $this->request->data['SaldosIniciale']['liquidations_type_id'],
                    'Propietario.consorcio_id' => $this->request->data['SaldosIniciale']['consorcio_id'], 'Consorcio.habilitado' => 1],
                'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]],
                'recursive' => 0,
                'fields' => ['Consorcio.name', 'Propietario.id', 'Propietario.name', 'Propietario.unidad', 'SaldosIniciale.id', 'SaldosIniciale.capital', 'SaldosIniciale.interes'],
                'order' => 'Propietario.orden,Propietario.unidad']
            );
            $this->set('saldos', $resul);
        }

        $consorcios = $this->SaldosIniciale->Propietario->Consorcio->getConsorciosList();
        $liquidationsTypes = $this->SaldosIniciale->LiquidationsType->getLiquidationsTypes();
        $this->set(compact('consorcios', 'liquidationsTypes'));
    }

}
