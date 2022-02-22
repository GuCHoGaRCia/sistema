<?php

App::uses('AppModel', 'Model');

class Bancosdepositoscheque extends AppModel {

    public $virtualFields = ['tipo' => 5, 'importe' => 'Cheque.importe', 'fecha2' => 'concat(Bancosdepositoscheque.fecha," ",DATE_FORMAT(Bancosdepositoscheque.created, "%H:%i:%s"))'];
    public $validate = array(
        'cheque_id' => array(
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
    );
    public $belongsTo = array(
        'Cheque' => array(
            'className' => 'Cheque',
            'foreignKey' => 'cheque_id',
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
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancosdepositoscheque.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'cheques', 'alias' => 'Cheque', 'type' => 'left', 'conditions' => ['Cheque.id=Bancosdepositoscheque.cheque_id']]]]));
    }

    /*
     * Obtiene el total depositos cheque x cuenta bancaria en un rango de fechas
     * se utiliza en la generacion de asientos automaticos
     */

    public function getTotalDepositosCheque($consorcio, $desde, $hasta) {
        $cuentas = $this->Bancoscuenta->getCuentasBancarias($consorcio);
        $total = [];
        foreach ($cuentas as $k => $v) {
            $resul = $this->find('all', ['conditions' => ['Bancosdepositoscheque.bancoscuenta_id' => $k, 'Bancosdepositoscheque.fecha >=' => $this->fecha($desde), 'Bancosdepositoscheque.fecha <=' => $this->fecha($hasta)],
                'joins' => [['table' => 'cheques', 'alias' => 'Cheque', 'type' => 'left', 'conditions' => ['Cheque.id=Bancosdepositoscheque.cheque_id']]],
                'fields' => 'sum(Cheque.importe) as total']);
            $total[$k] = $resul[0][0]['total'] ?? 0;
        }

        return $total;
    }

    /*
     * Obtiene los movimientos que hayan sido realizados con el cheque especificado
     */

    public function getMovimientosCheque($cheque_id) {
        return $this->find('all', ['conditions' => ['Bancosdepositoscheque.cheque_id' => $cheque_id], 'recursive' => 0, 'fields' => ['Bancosdepositoscheque.*'], 'order' => 'Bancosdepositoscheque.fecha2 desc']);
    }

    public function guardar($data) {
        $resul = $this->beforeGuardar($data);
        if ($resul !== "") {
            return $resul; // no guarda nada, sale x error
        }

        // lo hago de nuevo a esto (ya lo hice en el beforeGuardar)
        $tmp = $data['Cheque'];
        $r = [];
        foreach ($tmp as $k => $v) {
            if ($v['cheque_id'] != 0) {
                $r[$k] = $v;
            }
        }

        // deposito los cheques en el banco
        $fecha = date('Y-m-d');
        $caja_id = $this->Bancoscuenta->Banco->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        foreach ($r as $k => $v) {
            $consorcio_id = $this->Cheque->getConsorcioId($v['cheque_id']);
            $bancoscuenta_id = $data['Bancosdepositoscheque']['bancoscuenta_id'];
            $this->Cheque->depositar($v['cheque_id'], $bancoscuenta_id);
            $this->create();
            $resul = $this->save(['cheque_id' => $v['cheque_id'], 'bancoscuenta_id' => $bancoscuenta_id,
                'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $fecha, 'concepto' => 'ChD ' . $data['Bancosdepositoscheque']['concepto'], 'anulado' => 0]);

            //creo el egreso de caja para cada cheque depositado
            $importe = $this->Cheque->getImporte($v['cheque_id']);
            $this->Bancoscuenta->Banco->Client->Caja->Cajasegreso->create();
            $this->Bancoscuenta->Banco->Client->Caja->Cajasegreso->save(['concepto' => 'ChD ' . $data['Bancosdepositoscheque']['concepto'], 'movimientoasociado' => $resul['Bancosdepositoscheque']['id'],
                'importe' => 0, 'cheque' => $importe, 'anulado' => 0, 'fecha' => $fecha, 'user_id' => $_SESSION['Auth']['User']['id'], 'proveedorspago_id' => 0,
                'consorcio_id' => $consorcio_id, 'bancoscuenta_id' => $bancoscuenta_id, 'caja_id' => $caja_id], ['callbacks' => false]);

            $this->Bancoscuenta->Banco->Client->Caja->setSaldo($caja_id, -$importe, 'saldo_cheques');
        }
        return "";
    }

    /*
     * Realizo los chequeos previos al pago de las facturas proveedor
     */

    public function beforeGuardar($data) {
        $errores = "";
        if (!$this->Bancoscuenta->canEdit($data['Bancosdepositoscheque']['bancoscuenta_id'])) {
            $errores .= __("La cuenta bancaria es inexistente") . "<br>";
        }
        // chequeo q los cheques efectivamente no están en uso
        $tmp = $data['Cheque'];
        $r = [];
        // si no selecciona ningun cheque no puede seguir
        foreach ($tmp as $k => $v) {
            if ($v['cheque_id'] != 0) {
                $r[$k] = $v;
            }
        }
        if (count($r) == 0) {
            $errores .= __("Debe seleccionar al menos un cheque para depositar") . "<br>";
        }

        // verifico que los cheques no están en uso. Si estan en uso, no puedo depositarlo
        $montototal = 0;
        foreach ($r as $k => $v) {
            if ($this->Cheque->isInUse($v['cheque_id'])) {
                $errores .= 'El cheque #' . $v['cheque_id'] . ' se encuentra en uso' . "<br>";
                $montototal += $this->Cheque->getImporte($v['cheque_id']);
            }
        }

        return $errores;
    }

    /*
     * Es un delete, actualizo el cheque (depositado=0) y los saldos de banco y caja
     */

    public function undo($id) {
        $this->id = $id;
        if ($this->field('anulado') == 1) {
            return true;
        }
        $consorcio_id = $this->Bancoscuenta->getConsorcio($this->field('bancoscuenta_id'));
        $this->Cheque->undoDepositar($this->field('cheque_id'), $consorcio_id, $this->field('fecha'));

        // anulo el egreso de caja asociado (el inicial del deposito bancario)
        $resul = $this->Bancoscuenta->Banco->Client->Caja->Cajasegreso->find('list', ['conditions' => ['movimientoasociado' => $this->field('id'), 'bancoscuenta_id' => $this->field('bancoscuenta_id')], 'fields' => ['id', 'concepto']]);
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $this->Bancoscuenta->Banco->Client->Caja->Cajasegreso->save(['id' => $k, 'anulado' => 1, 'concepto' => "[ANULADO] " . $v], ['callbacks' => false]);
            }
        }

        $this->save(['concepto' => '[ANULADO] ' . $this->field('concepto'), 'anulado' => 1]);
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
                'Bancosdepositoscheque.concepto LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
