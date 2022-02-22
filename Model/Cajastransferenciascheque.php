<?php

App::uses('AppModel', 'Model');
/*
 * Se utiliza en las transferencias entre cajas, para poder mantener un detalle de los cheques utilizados en cada transferencia entre cajas
 */

class Cajastransferenciascheque extends AppModel {

    public $belongsTo = [
        'Cajasingreso' => [
            'className' => 'Cajasingreso',
            'foreignKey' => 'cajasingreso_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Cajasegreso' => [
            'className' => 'Cajasegreso',
            'foreignKey' => 'cajasegreso_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Cheque' => [
            'className' => 'Cheque',
            'foreignKey' => 'cheque_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Muestra el detalle de los cheques involucrados en una Transferencia entre cajas ($f es el cajasingreso_id o cajasegreso_id)
     */

    public function listar($f, $id) {
        $cheques = $this->find('all', ['conditions' => ["Cajastransferenciascheque.$f" => $id], 'fields' => 'Cajastransferenciascheque.cheque_id']);
        $resul = [];
        foreach ($cheques as $k => $v) {
            $ch = $this->Cheque->find('first', ['conditions' => ['Cheque.id' => $v['Cajastransferenciascheque']['cheque_id']]]);
            if (!empty($ch)) {
                $resul[] = $ch['Cheque'];
            }
        }
        return $resul;
    }

}
