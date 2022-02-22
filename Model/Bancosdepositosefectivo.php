<?php

App::uses('AppModel', 'Model');

class Bancosdepositosefectivo extends AppModel {

    public $virtualFields = ['tipo' => 3];
    public $validate = array(
        'caja_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true,
                'required' => false
            ),
        ),
        'bancoscuenta_id' => array(
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
        'Caja' => array(
            'className' => 'Caja',
            'foreignKey' => 'caja_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Bancoscuenta' => array(
            'className' => 'Bancoscuenta',
            'foreignKey' => 'bancoscuenta_id',
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
        'Cobranza' => array(
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosdepositosefectivo.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Bancosdepositosefectivo.user_id']]]]));
    }

    public function beforeSave($options = []) {
        if (!isset($this->data['Bancosdepositosefectivo']['caja_id'])) {// en bancosdepositosefectivos/add2, creditos, caja_id viene en cero, asi q esta seteada (no entra aca). Los creditos tienen caja_id 0
            $this->data['Bancosdepositosefectivo']['caja_id'] = $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']); // en bancosdepositosefectivos/add, quite la seleccion de caja (lo hago aca y listo, porq siempre es la caja del usuario actual)    
        }
        $this->data['Bancosdepositosefectivo']['user_id'] = $_SESSION['Auth']['User']['id'];
        return true;
    }

    /*
     * Obtiene el total depositos efectivo x cuenta bancaria en un rango de fechas
     * se utiliza en la generacion de asientos automaticos
     */

    public function getTotalDepositosEfectivo($consorcio, $desde, $hasta) {
        $cuentas = $this->Bancoscuenta->getCuentasBancarias($consorcio);
        $total = [];
        foreach ($cuentas as $k => $v) {
            $resul = $this->find('all', ['conditions' => ['Bancosdepositosefectivo.bancoscuenta_id' => $k, 'Bancosdepositosefectivo.caja_id !=' => 0, 'Bancosdepositosefectivo.fecha >=' => date("Y-m-d", strtotime($desde)), 'Bancosdepositosefectivo.fecha <=' => date("Y-m-d", strtotime($hasta)),
                    'es_transferencia' => 0
                ],
                'fields' => 'sum(Bancosdepositosefectivo.importe) as total']);
            $total[$k] = $resul[0][0]['total'] ?? 0;
        }

        return $total;
    }

    public function getTransferencias($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosdepositosefectivo.cobranza_id !=' => 0, //q traiga solo las cobranzas, sino trae transferencias comunes
                'Bancoscuenta.consorcio_id' => $consorcio] + (empty($incluiranulados) ? ['Bancosdepositosefectivo.anulado' => 0] : []) +
                    (!empty($desde) ? ['Bancosdepositosefectivo.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancosdepositosefectivo.created <=' => $hasta] : []),
                    'joins' => [['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Bancosdepositosefectivo.bancoscuenta_id']],
                        ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Bancosdepositosefectivo.user_id']]],
                    'fields' => ['Bancosdepositosefectivo.cobranza_id', 'Bancosdepositosefectivo.concepto', 'Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.fecha', 'Bancosdepositosefectivo.created', 'Bancosdepositosefectivo.bancoscuenta_id', 'Bancosdepositosefectivo.anulado'],
                    'order' => 'Bancosdepositosefectivo.created desc'
        ]);
    }

    public function getTotalTransferencias($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getTransferencias($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancosdepositosefectivo']['importe'];
            }
        }
        return $total;
    }

    /*
     * Creo un credito en el banco (se llama desde cobranza manual)
     */

    public function crear($data) {
        $this->create();
        $this->save($data, ['callbacks' => false, 'validate' => false]);

        // actualizo el saldo
        $this->Bancoscuenta->setSaldo($data['bancoscuenta_id'], $data['importe']);
    }

    /*
     * Actualizo el saldo de las cuentas bancarias origen y destino. Aca entra al agregar deposito efectivo (hace ->save(data) en el controller/add
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            // si no es un crédito entonces entra (es deposito de caja a banco)
            if (isset($this->data['Bancosdepositosefectivo']['caja_id'])) {
                // hago un egreso de la caja. Se actualiza el saldo ahi mismo
                $r = $this->data['Bancosdepositosefectivo'];
                $data = ['consorcio_id' => $this->Bancoscuenta->getConsorcio($r['bancoscuenta_id']), 'bancoscuenta_id' => $r['bancoscuenta_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'movimientoasociado' => $this->data['Bancosdepositosefectivo']['id'], 'fecha' => date('Y-m-d'), 'concepto' => $r['concepto'], 'importe' => $r['importe']];
                $this->Caja->Cajasegreso->crear($data);
            }

            // sumo el importe a la cuenta destino
            $this->Bancoscuenta->setSaldo($this->data['Bancosdepositosefectivo']['bancoscuenta_id'], $this->data['Bancosdepositosefectivo']['importe']);
        }
    }

    /*
     * Funcion que anula un movimiento realizando el contrario al actual
     * Si es un credito, hago anulado=1 y actualizo el saldo de la cuenta bancaria
     * Si es un deposito, hago anulado=1 y hago un ingreso a la caja
     */

    public function undo($id) {
        $r = $this->find('first', ['conditions' => ['Bancosdepositosefectivo.id' => $id], 'recursive' => -1]);
        $r = $r['Bancosdepositosefectivo'];

        //Cuando anulo un deposito bancario, tengo q hacer los dos movimientos: el ingreso a la caja Y la extraccion, no uno o el otro como antes

        $data2 = ['consorcio_id' => $this->Bancoscuenta->getConsorcio($r['bancoscuenta_id']), 'bancoscuenta_id' => $r['bancoscuenta_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $r['fecha'], 'concepto' => '[ANULADO] ' . $r['concepto'], 'importe' => $r['importe'], 'anulado' => 1];
        //if ($r['caja_id'] != 0) { // es un depósito, sino es crédito
        //creo el ingreso en caja. Se actualiza el saldo de la caja ahi mismo
        //$data2 += ['cheque' => 0, 'anulado' => 1];
        $this->Caja->Cajasingreso->crear($data2 + ['cheque' => 0]);
        $cual = $this->Caja->Cajasegreso->find('first', ['conditions' => ['movimientoasociado' => $id], 'fields' => ['id']]);
        if (!empty($cual)) {
            $this->Caja->Cajasegreso->save(['id' => $cual['Cajasegreso']['id'], 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['concepto']], ['callbacks' => false]);
        }
        //} else {
        //$data2 += ['proveedorspago_id' => 0, 'conciliado' => 0, 'anulado' => 1];
        $this->Bancoscuenta->Bancosextraccione->crear($data2 + ['proveedorspago_id' => 0, 'conciliado' => 0]); //actualizo el saldo bancario ahi
        //}

        $this->save(['id' => $id, 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['concepto']], ['callbacks' => false]);
        return true;
    }

    public function getCreditos($consorcio, $desde, $hasta, $incluiranulados = 1) {
        return $this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancosdepositosefectivo.cobranza_id' => null, 'Bancosdepositosefectivo.es_transferencia' => false,
                'Bancosdepositosefectivo.caja_id' => 0] + (empty($incluiranulados) ? ['Bancosdepositosefectivo.anulado' => 0] : []) +
                    (!empty($desde) ? ['Bancosdepositosefectivo.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancosdepositosefectivo.created <=' => $hasta] : []),
                    'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Bancosdepositosefectivo.user_id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancosdepositosefectivo.bancoscuenta_id=Bancoscuenta.id']]],
                    'fields' => ['Bancosdepositosefectivo.id', 'Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.concepto', 'Bancosdepositosefectivo.fecha', 'Bancosdepositosefectivo.created', 'Bancosdepositosefectivo.bancoscuenta_id', 'Bancosdepositosefectivo.anulado'],
                    'order' => 'Bancosdepositosefectivo.created desc'
        ]);
    }

    public function getTotalCreditos($consorcio, $desde, $hasta, $incluiranulados = 1) {
        $resul = $this->getCreditos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancosdepositosefectivo']['importe'];
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
                $this->alias . '.concepto LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
