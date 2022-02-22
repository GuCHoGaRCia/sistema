<?php

App::uses('AppModel', 'Model');

class Cajasegreso extends AppModel {

    // fecha 2 se utiliza para combinar egresos con ingresos y ordenarlos por esa columna.
    // Se toma la fecha del movimiento y se subordena por la hora de creación del mismo, asi los movimientos del mismo dia quedan ordenados segun el orden en q fueron creados
    public $virtualFields = ['tipo' => 2, 'fecha2' => 'concat(Cajasegreso.fecha," ",DATE_FORMAT(Cajasegreso.created, "%H:%i:%s"))'];
    public $validate = array(
        'caja_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
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
        ),
        'anulado' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
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
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = [
        'Cajastransferenciascheque' => [
            'className' => 'Cajastransferenciascheque',
            'foreignKey' => 'cajasegreso_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasegreso.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Cajasegreso.user_id']]]]));
    }

    /*
     * Creo el egreso de la caja hacia un banco. Chequeo si tiene saldo
     */

    public function crear($data, $field = 'saldo_pesos') {
        if (!isset($data['user_id'])) {
            $data['user_id'] = $_SESSION['Auth']['User']['id'];
        }
        $this->create();
        $resul = $this->save($data, ['callbacks' => false, 'validate' => false]);
        $insertid = $resul['Cajasegreso']['id'] ?? 0;
        // actualizo el saldo
        $this->Caja->setSaldo($data['caja_id'], - ($field == 'saldo_pesos' ? $data['importe'] : $data['cheque']), $field);

        return $insertid;
    }

    /*
     * Obtiene el total de egresos en efectivo, cheques, cheques propios o transferencias de un Consorcio entre dos fechas
     * Los egresos van al banco (deposito o transferencia) o pago a un proveedor
     */

    public function getTotalesEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $pagosproveedor = $this->User->Proveedorspago->getTotalPagosPorConsorcio($consorcio, $desde, $hasta, null, $incluiranulados);
        return ['egresos' => Hash::merge(['depositos' => ['efectivo' => $this->getEgresosBancosDepositos($consorcio, $desde, $hasta, $incluiranulados), 'cheque' => $this->getBancosChequesDepositos($consorcio, $desde, $hasta, $incluiranulados)]], ['pagosproveedor' => $pagosproveedor], ['otros' => $this->getEgresosManuales($consorcio, $desde, $hasta, $incluiranulados)])];
    }

    /*
     * Obtiene el total de egresos efectivo, cheques, manuales en un rango de fechas
     * se utiliza en la generacion de asientos automaticos
     */

    public function getTotalEgresosPagoProveedorFecha($consorcio, $desde, $hasta) {
        $resul = $this->find('all', ['conditions' => ['Cajasegreso.consorcio_id' => $consorcio, 'Cajasegreso.fecha >=' => $this->fecha($desde), 'Cajasegreso.fecha <=' => $this->fecha($hasta), 'Cajasegreso.proveedorspago_id !=' => 0],
            'fields' => 'sum(importe+cheque) as total']);
        return $resul[0][0]['total'] ?? 0;
    }

    /*
     * Obtiene el total de depositos efectivo / cheques en un rango de fechas
     * se utiliza en la generacion de asientos automaticos
     */

    public function getTotalEgresosDepositosFecha($consorcio, $desde, $hasta) {
        $resul = $this->find('all', ['conditions' => ['Cajasegreso.consorcio_id' => $consorcio, 'Cajasegreso.created >=' => $this->fecha($desde), 'Cajasegreso.created <=' => $this->fecha($hasta), 'Cajasegreso.proveedorspago_id' => 0],
            'fields' => 'sum(importe) as efectivo,sum(cheque) as cheque']);
        return ['e' => $resul[0][0]['efectivo'] ?? 0, 'c' => $resul[0][0]['cheque'] ?? 0];
    }

    public function getEgresosBancosDepositos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->Bancoscuenta->find('all', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancosdepositosefectivo.caja_id !=' => 0] +
                    (!empty($desde) ? ['Bancosdepositosefectivo.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancosdepositosefectivo.created <=' => $hasta] : []) + (empty($incluiranulados) ? ['Bancosdepositosefectivo.anulado' => 0] : []),
                    'recursive' => 0,
                    'fields' => ['Bancosdepositosefectivo.*'],
                    'joins' => [['table' => 'bancosdepositosefectivos', 'alias' => 'Bancosdepositosefectivo', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Bancosdepositosefectivo.bancoscuenta_id']]],
                    'order' => 'Bancosdepositosefectivo.created desc'
        ]);
    }

    public function getTotalEgresosBancosDepositos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getEgresosBancosDepositos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Bancosdepositosefectivo']['importe'];
            }
        }
        return $total;
    }

    public function getBancosChequesDepositos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->Bancoscuenta->Bancosdepositoscheque->find('all', ['conditions' => ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, /* 'Cheque.depositado !=' => 0,  'Cheque.anulado' => 0 */] + //quito estas dos, porq si se anula el deposito y el cheque, igual el deposito existió, pero esta anulado (y lo debo incluir en el resumen caja banco y demas)
                    (!empty($desde) ? ['Bancosdepositoscheque.created >=' => $desde] : []) + (!empty($hasta) ? ['Bancosdepositoscheque.created <=' => $hasta] : []) + (empty($incluiranulados) ? ['Bancosdepositoscheque.anulado' => 0] : []),
                    'contain' => ['Cheque.importe'],
                    'fields' => ['Bancosdepositoscheque.id', 'Bancosdepositoscheque.importe', 'Bancosdepositoscheque.bancoscuenta_id', 'Bancosdepositoscheque.concepto', 'Bancosdepositoscheque.fecha', 'Bancosdepositoscheque.created', 'Bancosdepositoscheque.anulado', 'Cheque.id'],
                    'joins' => [['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Bancosdepositoscheque.bancoscuenta_id']]],
                    'order' => 'Bancosdepositoscheque.created desc'
        ]);
    }

    public function getTotalBancosChequesDepositos($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getBancosChequesDepositos($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Cheque']['importe'];
            }
        }
        return $total;
    }

    /*
     * Los Egresos manuales son en efectivo
     */

    public function getEgresosManuales($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasegreso.consorcio_id' => $consorcio, 'Cajasegreso.movimientoasociado' => 0,
                'Cajasegreso.bancoscuenta_id' => 0, 'Cajasegreso.proveedorspago_id' => 0, 'Cajasegreso.estransferencia' => 0] + (empty($incluiranulados) ? ['Cajasegreso.anulado' => 0] : []) +
                    (!empty($desde) ? ['Cajasegreso.created >=' => $desde] : []) + (!empty($hasta) ? ['Cajasegreso.created <=' => $hasta] : []),
                    'fields' => ['Cajasegreso.importe', 'Cajasegreso.cheque', 'Cajasegreso.fecha', 'Cajasegreso.created', 'Cajasegreso.concepto', 'Cajasegreso.anulado'],
                    'recursive' => 0,
                    'order' => 'Cajasegreso.created desc']); // egresos de caja manuales
    }

    public function getTotalEgresosManuales($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getEgresosManuales($consorcio, $desde, $hasta, $incluiranulados);
        $total = ['e' => 0, 'c' => 0];
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total['e'] += $v['Cajasegreso']['importe'];
                $total['c'] += $v['Cajasegreso']['cheque'];
            }
        }
        return $total;
    }

    public function beforeSave($options = []) {
        $this->data['Cajasegreso']['user_id'] = $_SESSION['Auth']['User']['id'];

        return true;
    }

    /*
     * 
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            // si no es un crédito entonces entra (es deposito de caja a banco)
            if (isset($this->data['Cajasegreso']['bancoscuenta_id'])) {
                // hago un egreso de la caja. Se actualiza el saldo ahi mismo
                $r = $this->data['Cajasegreso'];
                $data = ['consorcio_id' => $r['consorcio_id'], 'bancoscuenta_id' => $r['bancoscuenta_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => date('Y-m-d'), 'concepto' => $r['concepto'], 'importe' => $r['importe'], 'importe' => $r['cheque']];
                $this->crear($data);
            }

            // resto el importe a la caja
            //$this->Caja->setSaldo($this->data['Cajasegreso']['caja_id'], -$this->data['Cajasegreso']['importe']);
            //$this->Caja->setSaldo($this->data['Cajasegreso']['caja_id'], -$this->data['Cajasegreso']['cheque'], 'saldo_cheques');
        }
    }

    /*
     * Funcion que anula un movimiento realizando el contrario al actual
     * Si es un crédito hago anulado=1, actualizo el saldo de la cuenta bancaria
     * Si es un depósito hago anulado=1, hago un ingreso a la caja y 
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Cajasegreso.id' => $id, 'Cajasegreso.anulado' => 0), 'recursive' => -1));
        if (empty($r)) {// si no existe el movimiento puede ser q ya haya sido anulado (movimientoasociado)
            return false;
        }

        $r = $r['Cajasegreso'];
        if ($r['bancoscuenta_id'] != 0) {
            // chequeo que la cuenta bancaria origen tenga saldo y sea mayor al importe (si es deposito o crédito)
            /* if (!($this->Bancoscuenta->getSaldo($r['bancoscuenta_id']) - $r['importe'] >= 0)) {
              //SessionComponent::setFlash(__('La cuenta bancaria orígen no posee saldo suficiente'), 'error', [], 'otro');
              return false;
              } */
            // creo el ingreso en caja. Se actualiza el saldo de la caja ahi mismo
            //$data2 = array('bancoscuenta_id' => $r['bancoscuenta_id'], 'consorcio_id' => $r['consorcio_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => date('Y-m-d'), 'concepto' => __('[ANULADO]') . " " . $r['concepto'], 'importe' => $r['importe'], 'cheque' => $r['cheque'], 'anulado' => 1);
            //$this->Caja->Cajasingreso->crear($data2);
            // lo anulo y actualizo el saldo del banco
            //$this->save(array('id' => $id, 'anulado' => 1), ['callbacks' => false]);
            $this->Bancoscuenta->setSaldo($r['bancoscuenta_id'], -$r['importe']);
        }

        // es una anulacion de egreso, no de depósito. Creo el ingreso en caja. Se actualiza el saldo de la caja ahi mismo
        $data2 = array('bancoscuenta_id' => $r['bancoscuenta_id'], 'consorcio_id' => $r['consorcio_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $r['fecha'], 'concepto' => "[ANULADO] " . $r['concepto'], 'importe' => $r['importe'], 'cheque' => $r['cheque'], 'anulado' => 1);
        $this->Caja->Cajasingreso->crear($data2);

        $this->save(['id' => $id, 'anulado' => 1, 'concepto' => "[ANULADO] " . $r['concepto']], ['callbacks' => false]); // al guardar el movimiento, va a actualizar los saldos de las cuentas bancarias;
        return true;
    }

// funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Cajasegreso.concepto LIKE' => '%' . $data['buscar'] . '%',
                'Bancoscuenta.name LIKE' => '%' . $data['buscar'] . '%',
                'Caja.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
