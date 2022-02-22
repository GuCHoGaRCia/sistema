<?php

App::uses('AppModel', 'Model');

class Bancosextraccione extends AppModel {

    public $virtualFields = ['tipo' => 4];
    public $validate = array(
        'bancoscuenta_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'caja_id' => array(
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
        'Caja' => array(
            'className' => 'Caja',
            'foreignKey' => 'caja_id',
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
        ),
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosextraccione.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Bancosextraccione.user_id']]]]));
    }

    public function beforeSave($options = []) {
        $this->data['Bancosextraccione']['user_id'] = $_SESSION['Auth']['User']['id'];

        // en bancosextracciones, solo elijo la cta_bancaria, y obtengo de ahi su consorcio.. asi no le pifian eligiendo ambos y mezclando
        if (empty($this->data['Bancosextraccione']['consorcio_id'])) {
            $this->data['Bancosextraccione']['consorcio_id'] = $this->Bancoscuenta->getConsorcio($this->data['Bancosextraccione']['bancoscuenta_id']);
        }
        return true;
    }

    /*
     * Creo un débito en el banco (se llama desde bancosdepositoefectivo::undo())
     */

    public function crear($data) {
        $this->create();
        $this->save($data, ['callbacks' => false, 'validate' => false]);

        // actualizo el saldo
        $this->Bancoscuenta->setSaldo($data['bancoscuenta_id'], -$data['importe']);
    }

    /*
     * Creo el ingreso a la caja y actualizo el saldo de la cuenta bancaria
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            // si no es un crédito entonces entra (es extraccion de banco a caja)
            if (!empty($this->data['Bancosextraccione']['caja_id'])) {
                // hago un ingreso de la caja. Se actualiza el saldo ahi mismo
                $r = $this->data['Bancosextraccione'];
                $data = array('bancoscuenta_id' => $r['bancoscuenta_id'], 'movimientoasociado' => $r['id'], 'consorcio_id' => $r['consorcio_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => date('Y-m-d'), 'concepto' => $r['concepto'], 'importe' => $r['importe']);
                $this->Caja->Cajasingreso->crear($data);
            }

            // resto el importe a la cuenta destino
            $this->Bancoscuenta->setSaldo($this->data['Bancosextraccione']['bancoscuenta_id'], -$this->data['Bancosextraccione']['importe']);
        }
    }

    /*
     * Funcion que anula un movimiento realizando el contrario al actual
     * Si es un débito, hago anulado=1 y actualizo el saldo de la cuenta bancaria
     * Si es una extracción, hago anulado=1 y hago un egreso de la caja
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Bancosextraccione.id' => $id, 'Bancosextraccione.anulado' => 0), 'recursive' => -1));
        if (empty($r)) {
            return false; //no existe o ya fue anulado
        }
        $r = $r['Bancosextraccione'];

        if ($r['caja_id'] != 0) { // esta anulando una extraccion
            $this->save(array('id' => $id, 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['concepto']), ['callbacks' => false]);

            // creo el egreso de la caja. Se actualiza el saldo de la caja ahi mismo
            $data2 = array('bancoscuenta_id' => $r['bancoscuenta_id'], 'consorcio_id' => $r['consorcio_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $r['fecha'], 'concepto' => '[ANULADO] ' . $r['concepto'], 'importe' => $r['importe'], 'anulado' => 1);
            $this->Caja->Cajasegreso->crear($data2);

            // creo el deposito (es el contrario a la extraccion)
            $data2 = ['caja_id' => $r['caja_id'], 'bancoscuenta_id' => $r['bancoscuenta_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'cobranza_id' => null, 'fecha' => $r['fecha'], 'concepto' => '[ANULADO] ' . $r['concepto'], 'importe' => $r['importe'], 'es_transferencia' => 0, 'conciliado' => 0, 'anulado' => 1];
            $this->Bancoscuenta->Bancosdepositosefectivo->crear($data2); //actualizo el saldo bancario ahi
            // actualizo el saldo del banco

            $r = $this->Caja->Cajasingreso->find('first', array('conditions' => array('Cajasingreso.movimientoasociado' => $r['id'], 'Cajasingreso.anulado' => 0), 'fields' => ['Cajasingreso.id', 'Cajasingreso.concepto']));
            if (empty($r)) { // si no existe el movimiento puede ser q ya haya sido anulado (movimientoasociado)
                return true;
            }
            $this->Caja->Cajasingreso->save(['id' => $r['Cajasingreso']['id'], 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['Cajasingreso']['concepto']]);
            return true;
        }

        // es una anulacion de un débito bancario
        if ($this->save(array('id' => $id, 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['concepto']), ['callbacks' => false])) { // al guardar el movimiento, va a actualizar los saldos de las cuentas bancarias
            $data2 = ['caja_id' => 0, 'bancoscuenta_id' => $r['bancoscuenta_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'cobranza_id' => null, 'fecha' => $r['fecha'], 'concepto' => '[ANULADO] ' . $r['concepto'], 'importe' => $r['importe'], 'es_transferencia' => 0, 'conciliado' => 0, 'anulado' => 1];
            $this->Bancoscuenta->Bancosdepositosefectivo->crear($data2); //actualizo el saldo bancario ahi
            return true;
        }

        return false;
    }

    public function getDebitos($consorcio, $desde, $hasta, $incluiranulados = 1) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancosextraccione.caja_id' => 0,
                'OR' => ['Bancosextraccione.proveedorspago_id' => null, 'Bancosextraccione.proveedorspago_id' => 0]] + (!empty($desde) ? ['Bancosextraccione.created >= ' => $desde] : []) + (!empty($hasta) ? ['Bancosextraccione.created <= ' => $hasta] : []) + (empty($incluiranulados) ? ['Bancosextraccione.anulado' => 0] : []),
                    'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'right', 'conditions' => ['User.id = Bancosextraccione.user_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'right', 'conditions' => ['Bancosextraccione.bancoscuenta_id = Bancoscuenta.id']]],
                    'fields' => ['Bancosextraccione.id', 'Bancosextraccione.fecha', 'Bancosextraccione.created', 'Bancosextraccione.concepto', 'Bancosextraccione.importe', 'Bancosextraccione.bancoscuenta_id', 'Bancosextraccione.anulado'],
                    'order' => 'Bancosextraccione.created desc'
        ]);
    }

    public function getTotalDebitos($consorcio, $desde, $hasta, $incluiranulados = 1) {
        $resul = $this->getDebitos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancosextraccione']['importe'];
            }
        }
        return $total;
    }
    
    /*
     * Se usa solamente en generacion asientos automaticos
     */

    public function getTransferencias($consorcio, $desde, $hasta, $incluiranulados = 1) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancosextraccione.caja_id' => 0,
                    /* 'OR' => ['Bancosextraccione.proveedorspago_id' => null/* , 'Bancosextraccione.proveedorspago_id' => 0 ] */
                    ] + (!empty($desde) ? ['Bancosextraccione.created >= ' => $desde] : []) +
                    (!empty($hasta) ? ['Bancosextraccione.created <= ' => $hasta] : []) + (empty($incluiranulados) ? ['Bancosextraccione.anulado' => 0] : []),
                    'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'right', 'conditions' => ['User.id = Bancosextraccione.user_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'right', 'conditions' => ['Bancosextraccione.bancoscuenta_id = Bancoscuenta.id']]],
                    'fields' => ['Bancosextraccione.id', 'Bancosextraccione.fecha', 'Bancosextraccione.created', 'Bancosextraccione.concepto', 'Bancosextraccione.importe', 'Bancosextraccione.bancoscuenta_id', 'Bancosextraccione.anulado'],
                    'order' => 'Bancosextraccione.created desc'
        ]);
    }

    /*
     * Se usa solamente en generacion asientos automaticos
     */

    public function getTotalTransferencias($consorcio, $desde, $hasta, $incluiranulados = 1) {
        $resul = $this->getTransferencias($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancosextraccione']['importe'];
            }
        }
        return $total;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Bancosextraccione.concepto LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
