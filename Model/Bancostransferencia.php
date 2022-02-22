<?php

App::uses('AppModel', 'Model');

class Bancostransferencia extends AppModel {

    public $virtualFields = ['tipo' => 6];
    public $validate = array(
        'bancoscuenta_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'destino_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'user_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'fecha' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Debe completar con una fecha correcta',
            ),
        ),
        'concepto' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'importe' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Debe ser un importe mayor a cero',
            ),
        ),
    );

    public $belongsTo = array(
        'Bancoscuenta' => array(
            'className' => 'Bancoscuenta',
            'foreignKey' => 'bancoscuenta_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Destino' => array(
            'className' => 'Bancoscuenta',
            'foreignKey' => 'destino_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancostransferencia.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Bancostransferencia.user_id']]]]));
    }

    public function getIngresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancostransferencia.destino_id !=' => null,
                'Bancoscuenta.consorcio_id' => $consorcio] + (!empty($desde) ? ['Bancostransferencia.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancostransferencia.created <=' => $hasta] : []) + (empty($incluiranulados) ? ['Bancostransferencia.anulado' => 0] : []),
                    'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'right', 'conditions' => ['User.id=Bancostransferencia.user_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'right', 'conditions' => ['Bancostransferencia.destino_id=Bancoscuenta.id']]],
                    'fields' => ['Bancostransferencia.id', 'Bancostransferencia.fecha', 'Bancostransferencia.created', 'Bancostransferencia.concepto', 'Bancostransferencia.importe', 'Bancostransferencia.bancoscuenta_id', 'Bancostransferencia.destino_id', 'Bancostransferencia.anulado']
        ]);
    }

    public function getEgresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancostransferencia.destino_id !=' => null,
                'Bancoscuenta.consorcio_id' => $consorcio] + (!empty($desde) ? ['Bancostransferencia.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancostransferencia.created <=' => $hasta] : []) + (empty($incluiranulados) ? ['Bancostransferencia.anulado' => 0] : []),
                    'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'right', 'conditions' => ['User.id=Bancostransferencia.user_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'right', 'conditions' => ['Bancostransferencia.bancoscuenta_id=Bancoscuenta.id']]],
                    'fields' => ['Bancostransferencia.id', 'Bancostransferencia.fecha', 'Bancostransferencia.created', 'Bancostransferencia.concepto', 'Bancostransferencia.importe', 'Bancostransferencia.bancoscuenta_id', 'Bancostransferencia.destino_id', 'Bancostransferencia.anulado']
        ]);
    }

    public function getTotalIngresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getIngresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancostransferencia']['importe'];
            }
        }
        return $total;
    }

    public function getTotalEgresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getEgresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancostransferencia']['importe'];
            }
        }
        return $total;
    }

    /*
     * Si no es admin le establezco el client_id por el del cliente
     * Si es admin elije el cliente, por eso no se necesitaríaa
     */

    public function beforeSave($options = []) {
        // chequeo que este seteado bancoscuenta_id porq al editar en el index tira error sino
        if (isset($this->data['Bancostransferencia']['bancoscuenta_id'])) {
            // está creando uno nuevo
            if ($this->data['Bancostransferencia']['bancoscuenta_id'] == $this->data['Bancostransferencia']['destino_id']) {
                //SessionComponent::setFlash(__('Las cuentas or&iacute;gen y destino no pueden ser las mismas'), 'error', [], 'otro');
                return false;
            }

            // chequeo que la cuenta bancaria origen tenga saldo y sea mayor al importe a transferir
            //if (!$this->Bancoscuenta->hasSaldo($this->data['Bancostransferencia']['bancoscuenta_id'], $this->data['Bancostransferencia']['importe'])) {
            //    //SessionComponent::setFlash(__('La cuenta bancaria or&iacute;gen no posee saldo suficiente'), 'error', [], 'otro');
            //    return false;
            //}
        }

        $this->data['Bancostransferencia']['user_id'] = $_SESSION['Auth']['User']['id'];

        return true;
    }

    /*
     * Actualizo el saldo de las cuentas bancarias origen y destino
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            // resto el importe de la cuenta origen
            $this->Bancoscuenta->setSaldo($this->data['Bancostransferencia']['bancoscuenta_id'], -$this->data['Bancostransferencia']['importe']);

            // sumo el importe a la cuenta destino
            $this->Bancoscuenta->setSaldo($this->data['Bancostransferencia']['destino_id'], $this->data['Bancostransferencia']['importe']);
        }
    }

    /*
     * Funcion que anula un movimiento realizando el contrario al actual
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Bancostransferencia.id' => $id, 'Bancostransferencia.anulado' => 0), 'recursive' => -1));
        if (empty($r)) {
            return false; //no existe o ya fue anulado
        }
        $r = $r['Bancostransferencia'];

        // chequeo que la cuenta bancaria origen tenga saldo y sea mayor al importe a transferir
        //if (!$this->Bancoscuenta->hasSaldo($r['destino_id'], $r['importe'])) {
        //SessionComponent::setFlash(__('La cuenta bancaria orígen no posee saldo suficiente'), 'error', [], 'otro');
        //    return false;
        //}

        $this->create();
        $data = array('bancoscuenta_id' => $r['destino_id'], 'destino_id' => $r['bancoscuenta_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $r['fecha'], 'concepto' => '[ANULADO] ' . $r['concepto'], 'importe' => $r['importe'], 'anulado' => 1);
        if ($this->save($data, ['callbacks' => false])) { // al guardar la transferencia, va a actualizar los saldos de las cuentas bancarias
            $this->id = $id;
            $this->savefield('anulado', 1, ['callbacks' => false]); // evito q actualice saldos ahora, ya lo hizo antes
            // sumo el importe de la cuenta origen
            $this->Bancoscuenta->setSaldo($r['bancoscuenta_id'], $r['importe']);

            // resto el importe a la cuenta destino
            $this->Bancoscuenta->setSaldo($r['destino_id'], -$r['importe']);
            return true;
        }
        return false;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Bancostransferencia.concepto LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
