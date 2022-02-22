<?php

App::uses('AppModel', 'Model');

/**
 * Resumene Model
 *
 * @property Liquidation $Liquidation
 */
class Resumene extends AppModel {

    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'data' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $belongsTo = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function guardaProrrateo($data = []) {
        //0 $totales, 1 $cobranzas, 2 $saldosanteriores, 3 $cobranzas, 4 $prop, 5 $remanentes, 6 $descripcioncoeficientes
        $options = array('conditions' => array('Resumene.liquidation_id' => $data['liquidation_id']), 'recursive' => -1, 'fields' => ['Resumene.id']);
        $resul = $this->find('first', $options);
        if (isset($resul['Resumene']['id'])) {
            // seteo la pk para que haga update y no insert
            $this->id = $resul['Resumene']['id'];
        } else {
            $this->create(); // si no creo, no me genera uno nuevo y sigue usando el id anterior
        }
        $this->save(['liquidation_id' => $data['liquidation_id'], 'data' => json_encode($data)]);

        /* $a = $this->find('first', array('conditions' => array('Resumene.id' => 1)));
          debug(json_decode($a['Resumene']['data'], true));
          die;
         */
    }

    public function getLiquidationData($liquidation_id) {
        $options = array('conditions' => array('Resumene.liquidation_id' => $liquidation_id), 'fields' => array('Resumene.data'));
        return $this->find('first', $options);
    }

}
