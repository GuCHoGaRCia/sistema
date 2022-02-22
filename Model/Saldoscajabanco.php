<?php

App::uses('AppModel', 'Model');

class Saldoscajabanco extends AppModel {

    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    /*
     * Obtengo el ultimo saldo guardado del consorcio en la fecha $fecha.
     * Es independiente de la fecha que se haya hecho el guardado de saldos
     */

    public function getSaldos($consorcio, $fecha) {
        $f = date("Y-m-d 23:59:59", strtotime($this->fecha($fecha))); //el dia anterior
        $resul = $this->find('first', ['conditions' => ['consorcio_id' => $consorcio, 'date(fecha) <=' => $f], 'order' => 'id desc']); // el order me selecciona el registro con la fecha mas cercana
        if (empty($resul)) {
            return ['saldocajaefectivo' => 0, 'saldocajacheque' => 0, 'saldobancoefectivo' => 0, 'saldobancocheque' => 0, 'ingresosefectivo' => 0, 'ingresoscheque' => 0, 'ingresostransferencias' => 0, 'ingresosextracciones' => 0, 'ingresosmanuales' => 0, 'ingresostransferenciasinterbancos' => 0, 'ingresoscreditos' => 0,
                'bancosdepositosefectivo' => 0, 'bancosdepositoscheques' => 0, 'egresosmanuales' => 0, 'egresospagosproveedorefectivo' => 0, 'egresospagosproveedorcheque' => 0, 'egresospagosproveedorchequepropio' => 0,
                'egresospagosproveedortransferencia' => 0, 'egresospagosproveedorefectivoadm' => 0, 'egresospagosproveedorchequepropioadm' => 0, 'egresospagosproveedortransferenciaadm' => 0,
                'egresostransferenciasinterbancos' => 0, 'egresospagosacuenta' => 0, 'egresosdebitos' => 0];
        } else {
            return $resul['Saldoscajabanco'];
        }
    }

}
