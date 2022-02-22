<?php

App::uses('AppController', 'Controller');

class SaldosInicialesConsorciosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->SaldosInicialesConsorcio->recursive = 0;
        $resul = $this->SaldosInicialesConsorcio->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1],
            'fields' => ['SaldosInicialesConsorcio.liquidations_type_id', 'SaldosInicialesConsorcio.consorcio_id', 'SaldosInicialesConsorcio.id', 'SaldosInicialesConsorcio.saldo']]);
        $saldos = [];
        foreach ($resul as $k => $v) { // para cada tipo de liq, los junto por consorcio
            $saldos[$v['SaldosInicialesConsorcio']['consorcio_id']]['cons'] = $v['SaldosInicialesConsorcio'];
            $saldos[$v['SaldosInicialesConsorcio']['consorcio_id']][$v['SaldosInicialesConsorcio']['liquidations_type_id']] = ['key' => $v['SaldosInicialesConsorcio']['id'], 'value' => $v['SaldosInicialesConsorcio']['saldo']];
        }
        $this->set('consorcios', $this->SaldosInicialesConsorcio->Consorcio->getConsorciosList());
        $this->set('lt', $this->SaldosInicialesConsorcio->LiquidationsType->getLiquidationsTypes());
        $this->set('saldos', $saldos);
    }

    /*
     * Genero los saldos iniciales de los tipos de liquidación para cada consorcio (aquellos q no hayan sido creados todavia)
     */

    public function panel_generar() {
        $liquidationsTypes = $this->SaldosInicialesConsorcio->LiquidationsType->find('all', ['fields' => ['client_id', 'id']]);
        foreach ($liquidationsTypes as $l => $w) {
            $consorcios = $this->SaldosInicialesConsorcio->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $w['LiquidationsType']['client_id']]]);
            foreach ($consorcios as $k => $v) {
                $resul = $this->SaldosInicialesConsorcio->find('first', ['conditions' => ['SaldosInicialesConsorcio.consorcio_id' => $k, 'SaldosInicialesConsorcio.liquidations_type_id' => $w['LiquidationsType']['id']]]);
                if (empty($resul)) {
                    $this->SaldosInicialesConsorcio->create();
                    $this->SaldosInicialesConsorcio->save(['consorcio_id' => $k, 'liquidations_type_id' => $w['LiquidationsType']['id'], 'saldo' => 0]);
                }
            }
        }
        $this->Flash->success(__('Los saldos fueron generados'));
        $this->redirect(['controller' => 'clients', 'action' => 'procesos']);
    }

}
