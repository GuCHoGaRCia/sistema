<?php

App::uses('AppModel', 'Model');

class Proveedorspagosacuenta extends AppModel {

    public $validate = [
        'proveedorspago_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'proveedorspagoaplicado_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'importe' => [
            'decimal' => [
                'rule' => ['decimal'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Proveedorspago' => [
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedorspago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
    ];

    /*
     * Obtengo los pagos a cuenta guardados q esten disponibles para aplicar a un Pago proveedor nuevo
     */

    public function getPagosParaAplicar($proveedor_id = '', $consorcio_id = '', $incluiraplicados = '', $desde = '', $hasta = '') {
        return $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.anulado' => 0] + (!empty($proveedor_id) ? ['Proveedor.id' => $proveedor_id] : []) +
                    ($incluiraplicados == 0 ? ['Proveedorspagosacuenta.proveedorspagoaplicado_id' => 0] : []) + (!empty($consorcio_id) ? ['Proveedorspagosacuenta.consorcio_id' => $consorcio_id] : []) +
                    (!empty($desde) ? ['date(Proveedorspago.fecha) >=' => $this->fecha($desde)] : []) + (!empty($hasta) ? ['date(Proveedorspago.fecha) <=' => $this->fecha($hasta)] : []),
                    'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspago_id']],
                        ['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorspago.proveedor_id=Proveedor.id']],
                        ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                        ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                        ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]],
                    'fields' => ['DISTINCT Proveedorspagosacuenta.id', 'Proveedorspago.id', 'Proveedor.name', 'Proveedorspago.concepto', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspago.numero', 'Proveedorspagosacuenta.importe', 'Proveedorspagosacuenta.consorcio_id', 'Proveedorspagosacuenta.proveedorspagoaplicado_id'],
                    'order' => 'Proveedorspago.fecha desc,Consorcio.code']); //ordeno los pagos a cuenta x fecha y consorcio
    }

    /*
     * Obtiene los Pagos a cuenta que fueron aplicados a algun Pago a proveedor, para quitarlos del listado de Pago a proveedor si es que
     *   no tienen otra forma de pago como por ejemplo cheque propio.
     */

    public function getPagosAplicados() {
        return array_unique(array_values($this->find('list', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.anulado' => 0, 'Proveedorspagosacuenta.proveedorspagoaplicado_id !=' => 0],
                            'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspago_id']],
                                ['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorspago.proveedor_id=Proveedor.id']]],
                            'fields' => ['Proveedorspagosacuenta.id', 'Proveedorspagosacuenta.proveedorspagoaplicado_id']])));
    }

    /*
     * Funcion que obtiene el importe actual de un pago a cuenta para aplicar
     */

    public function getImporte($id) {
        $r = $this->find('first', array('conditions' => array('Proveedorspagosacuenta.id' => $id), 'fields' => array('importe')));
        return (empty($r) ? 0 : $r['Proveedorspagosacuenta']['importe']);
    }

}
