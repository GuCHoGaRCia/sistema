<?php

App::uses('AppModel', 'Model');

class Cajasingreso extends AppModel {

    // fecha 2 se utiliza para combinar egresos con ingresos y ordenarlos por esa columna.
    // Se toma la fecha del movimiento y se subordena por la hora de creación del mismo, asi los movimientos del mismo dia quedan ordenados segun el orden en q fueron creados
    public $virtualFields = ['tipo' => 1, 'fecha2' => 'concat(Cajasingreso.fecha," ",DATE_FORMAT(Cajasingreso.created, "%H:%i:%s"))'];
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
        /*            'total' => array(
          'rule' => array('comparison', '>', 0),
          'message' => 'Debe ser un importe mayor a cero',
          ), */
        ),
        'anulado' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
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
        'Cobranza' => array(
            'className' => 'Cobranza',
            'foreignKey' => 'cobranza_id',
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
            'foreignKey' => 'cajasingreso_id',
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
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasingreso.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Cajasingreso.user_id']]]]));
    }

    /*
     * Creo el ingreso de un banco a la caja
     */

    public function crear($data, $field = 'saldo_pesos') {
        $this->create();
        $resul = $this->save($data, ['callbacks' => false, 'validate' => false]);
        $insertid = $resul['Cajasingreso']['id'] ?? 0;

        // actualizo el saldo
        $this->Caja->setSaldo($data['caja_id'], ($field == 'saldo_pesos' ? $data['importe'] : $data['cheque']), $field);

        return $insertid;
    }

    /*
     * Obtiene el total de ingresos en efectivo y cheques de una caja especifica entre dos fechas, agrupado por Consorcio.
     */

    public function getTotalesEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return ['ingresos' => Hash::merge(['cobranzas' => $this->getIngresosEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados)], /*['transferencias' => $this->getTransferencias($consorcio, $desde, $hasta, $incluiranulados)],*/ ['extracciones' => $this->getExtracciones($consorcio, $desde, $hasta, $incluiranulados)], ['otros' => $this->getIngresosManuales($consorcio, $desde, $hasta, $incluiranulados)])];
    }

    public function getIngresosEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return Hash::combine($this->find('all', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'],
                        'Propietario.consorcio_id' => $consorcio, 'Cajasingreso.cobranza_id !=' => 0, 'Cajasingreso.cobranza_id !=' => null] + (empty($incluiranulados) ? ['Cajasingreso.anulado' => 0] : []) +
                            (!empty($desde) ? ['Cajasingreso.created >=' => $desde] : []) + (!empty($hasta) ? ['Cajasingreso.created <=' => $hasta] : []),
                            'order' => 'Cajasingreso.fecha desc,Propietario.orden,Propietario.code',
                            'fields' => ['Propietario.consorcio_id', 'Cobranza.id', 'Cajasingreso.fecha', 'Cajasingreso.created', 'Cajasingreso.concepto', 'Cajasingreso.importe', 'Cajasingreso.cheque', 'Cajasingreso.anulado'],
                            'joins' => [['table' => 'cobranzas', 'alias' => 'Cobranza', 'type' => 'left', 'conditions' => ['Cobranza.id=Cajasingreso.cobranza_id']],
                                ['table' => 'users', 'alias' => 'User', 'type' => 'left', 'conditions' => ['User.id=Cajasingreso.user_id']],
                                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=Cobranza.propietario_id']]],
                        ]), '{n}.Cobranza.id', '{n}.Cajasingreso'); // agrupa por {n}.Propietario.consorcio_id el resultado
    }

    public function getTotalIngresosEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getIngresosEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados);
        $total = ['e' => 0, 'c' => 0];
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total['e'] += $v['importe'];
                $total['c'] += $v['cheque'];
            }
        }
        return $total;
    }

    /*
     * Obtiene el total de ingresos efectivo, cheques, manuales en un rango de fechas
     * se utiliza en la generacion de asientos automaticos
     */

    public function getTotalIngresosFecha($consorcio, $desde, $hasta) {
        $resul = $this->find('all', ['conditions' => ['Cajasingreso.consorcio_id' => $consorcio, 'Cajasingreso.fecha >=' => $this->fecha($desde), 'Cajasingreso.fecha <=' => $this->fecha($hasta)],
            'fields' => 'sum(importe+cheque) as total']);
        return $resul[0][0]['total'] ?? 0;
    }

    /*
     * si dejo 'Cajasingreso.bancoscuenta_id !=' => null, me agrega un debito como extraccion ?? WTF
     * agrego caja_id!=0, sino es debito!
     */

    public function getExtracciones($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasingreso.consorcio_id' => $consorcio, 'Cajasingreso.bancoscuenta_id !=' => 0, 'Cajasingreso.caja_id !=' => 0] +
                    (!empty($desde) ? ['Cajasingreso.created >=' => $desde] : []) + (!empty($hasta) ? ['Cajasingreso.created <=' => $hasta] : []) + (empty($incluiranulados) ? ['Cajasingreso.anulado' => 0] : []),
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Cajasingreso.consorcio_id']]],
                    'fields' => ['Cajasingreso.importe', 'Cajasingreso.fecha', 'Cajasingreso.created', 'Cajasingreso.bancoscuenta_id', 'Cajasingreso.concepto', 'Cajasingreso.anulado'],
                    'order' => 'Cajasingreso.fecha desc,Consorcio.code'
        ]);
    }

    public function getTotalExtracciones($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getExtracciones($consorcio, $desde, $hasta, $incluiranulados);
        $total = 0;
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total += $v['Cajasingreso']['importe'];
            }
        }
        return $total;
    }

    /*
     * Los ingresos manuales son en efectivo
     */

    public function getIngresosManuales($consorcio, $desde, $hasta, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasingreso.consorcio_id' => $consorcio,
                'Cajasingreso.movimientoasociado' => 0, 'Cajasingreso.bancoscuenta_id' => 0, 'Cajasingreso.cobranza_id' => null] + (empty($incluiranulados) ? ['Cajasingreso.anulado' => 0] : []) +
                    (!empty($desde) ? ['Cajasingreso.created >=' => $desde] : []) + (!empty($hasta) ? ['Cajasingreso.created <=' => $hasta] : []),
                    'fields' => ['Cajasingreso.fecha', 'Cajasingreso.created', 'Cajasingreso.concepto', 'Cajasingreso.importe', 'Cajasingreso.cheque', 'Cajasingreso.anulado'],
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Cajasingreso.consorcio_id']]],
                    'order' => 'Cajasingreso.created desc'
        ]); //ingresos a caja manuales
    }

    public function getTotalIngresosManuales($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getIngresosManuales($consorcio, $desde, $hasta, $incluiranulados);
        $total = ['e' => 0, 'c' => 0];
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                $total['e'] += $v['Cajasingreso']['importe'];
                $total['c'] += $v['Cajasingreso']['cheque'];
            }
        }
        return $total;
    }

    public function beforeSave($options = array()) {
        $this->data['Cajasingreso']['user_id'] = $_SESSION['Auth']['User']['id'];
        return true;
    }

    /*
     * Actualizo el saldo de la caja
     */

    public function afterSave($created, $options = array()) {
        if ($created) {
            if (!isset($this->data['Cajasingreso']['bancoscuenta_id'])) {
                // sumo el importe a la caja (es un ingreso)
                $this->Caja->setSaldo($this->data['Cajasingreso']['caja_id'], $this->data['Cajasingreso']['importe']);
            }
        }
    }

    /*
     * Funcion que anula un movimiento realizando el contrario al actual
     * Si es un débito hago anulado=1, actualizo el saldo de la cuenta bancaria y hago un egreso de la caja
     * Si es una extracción hago anulado=1, actualizo el saldo de la caja y banco y hago un egreso de la caja
     * Nuevo: hago siempre contramovimiento
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Cajasingreso.id' => $id, 'Cajasingreso.anulado' => 0), 'recursive' => -1));
        if (empty($r)) { // si no existe el movimiento puede ser q ya haya sido anulado (movimientoasociado)
            return false;
        }

        // chequeo que la caja tenga saldo (no se hace mas, permito saldos negativos)
        $r = $r['Cajasingreso'];

        // creo el egreso de la caja. Se actualiza el saldo de la caja ahi mismo
        $data2 = array('bancoscuenta_id' => $r['bancoscuenta_id'], 'consorcio_id' => $r['consorcio_id'], 'caja_id' => $r['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $r['fecha'], 'concepto' => "[ANULADO] " . $r['concepto'], 'importe' => $r['importe'], 'cheque' => $r['cheque'], 'anulado' => 1);
        $this->Caja->Cajasegreso->crear($data2);

        $this->save(['id' => $id, 'anulado' => 1, 'concepto' => "[ANULADO] " . $r['concepto']], ['callbacks' => false]);

        // esta anulando una extraccion, actualizo el saldo del banco
        if ($r['bancoscuenta_id'] != 0) {
            $this->Bancoscuenta->setSaldo($r['bancoscuenta_id'], $r['importe']);
        }

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
                'Cajasingreso.concepto LIKE' => '%' . $data['buscar'] . '%',
                'Caja.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
