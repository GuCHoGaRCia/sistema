<?php

App::uses('AppModel', 'Model');

class Proveedorspago extends AppModel {

    public $virtualFields = ['tipo' => 8, 'fecha2' => 'concat(Proveedorspago.fecha," ",DATE_FORMAT(Proveedorspago.created, "%H:%i:%s"))'];
    public $validate = array(
        'proveedor_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'fecha' => array(
            'date' => [
                'rule' => ['date'],
                'message' => 'Debe completar con una fecha correcta',
            ],
        ),
        'importe' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>=', 0),
                'message' => 'Debe ser un importe mayor o igual a cero',
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
        'Proveedor' => array(
            'className' => 'Proveedor',
            'foreignKey' => 'proveedor_id',
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
    );
    public $hasMany = array(
        'Proveedorspagosfactura' => array(
            'className' => 'Proveedorspagosfactura',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Proveedorspagosacuenta' => array(
            'className' => 'Proveedorspagosacuenta',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Proveedorspagoscheque' => array(
            'className' => 'Proveedorspagoscheque',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Cajasegreso' => array(
            'className' => 'Cajasegreso',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Bancosextraccione' => array(
            'className' => 'Bancosextraccione',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Proveedorspagosnc' => array(
            'className' => 'Proveedorspagosnc',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Chequespropio' => [
            'className' => 'Chequespropio',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Chequespropiosadmsdetalle' => [
            'className' => 'Chequespropiosadmsdetalle',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Administracionefectivo' => [
            'className' => 'Administracionefectivo',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Administraciontransferencia' => [
            'className' => 'Administraciontransferencia',
            'foreignKey' => 'proveedorspago_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']]]]));
    }

    /*
     * Obtiene los Pagos a proveedor por Consorcio
     * $prefijoLT se usa para obtener los pagos de un tipo de liquidacion especifico (0,5,9,etc)
     */

    public function getPagos($proveedor_id, $consorcio_id, $desde = null, $hasta = null, $incluiranulados = 0, $prefijoLT = -1) {
        $cuentas = [];
        if (!empty($consorcio_id)) {
            $cuentas = array_keys($this->Proveedor->Client->Consorcio->Bancoscuenta->getCuentasBancarias($consorcio_id));
        }
        // se utiliza en el index de proveedorspagos y en el listado de pagos a proveedor (liquidaciones->liquidaciones), es la misma funcion, pero una recibe fecha con hora y la otra fecha sola
        $fecha = [];
        if (!empty($desde) && strlen($desde) == 10) {
            $fecha += ['date(Proveedorspago.created) >=' => $this->fecha($desde)];
        } else {
            $fecha += ['Proveedorspago.created >=' => $this->fecha($desde)];
        }
        if (!empty($hasta) && strlen($hasta) == 10) {
            $fecha += ['date(Proveedorspago.created) <=' => $this->fecha($hasta)];
        } else {
            $fecha += ['Proveedorspago.created <=' => $this->fecha($hasta)];
        }

        $condicionconsorcio = [];
        if (!empty($consorcio_id)) {
            $condicionconsorcio = ['OR' => ['Liquidation.consorcio_id' => $consorcio_id,
                    ['Liquidation.consorcio_id is null',
                        'OR' => [
                            ['AND' => ['Cajasegreso.consorcio_id' => $consorcio_id, 'Bancosextraccione.consorcio_id' => null/* , 'Chequespropio.bancoscuenta_id' => null */]], // comentado porq sino no muestra los pagos a cuenta efectivo chequepropio
                            ['AND' => ['Cajasegreso.consorcio_id' => null, 'Bancosextraccione.consorcio_id' => $consorcio_id, 'Chequespropio.bancoscuenta_id' => null]],
                            ['AND' => ['Cajasegreso.consorcio_id' => null, 'Bancosextraccione.consorcio_id' => null, 'Chequespropio.bancoscuenta_id' => $cuentas]]
                        ]
                    ]]
            ];
        }

        $resul = $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']] + (!empty($proveedor_id) ? ['Proveedor.id' => $proveedor_id] : []) +
            $condicionconsorcio +
            (empty($incluiranulados) ? ['Proveedorspago.anulado' => 0] : []) + ($prefijoLT == -1 ? [] : ($prefijoLT == 0 ? ["(LiquidationsType.prefijo is null or LiquidationsType.prefijo=$prefijoLT)"] : ["(LiquidationsType.prefijo=$prefijoLT)"])) + $fecha,
            'contain' => ['Proveedor'],
            //!empty($consorcio_id) es porq cuando filtra x consorcio, necesita saber la liq. Sino, no es necesario (ej, cuando entra a index)
            'joins' => [
                ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=LiquidationsType.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Liquidation.consorcio_id=Consorcio.id']],
                // para pago a cuenta
                ['table' => 'proveedorspagosacuentas', 'alias' => 'Proveedorspagosacuenta', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspagoaplicado_id']],
                ['table' => 'cajasegresos', 'alias' => 'Cajasegreso', 'type' => 'left', 'conditions' => ['Cajasegreso.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'bancosextracciones', 'alias' => 'Bancosextraccione', 'type' => 'left', 'conditions' => ['Bancosextraccione.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'chequespropios', 'alias' => 'Chequespropio', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Chequespropio.bancoscuenta_id=Bancoscuenta.id']],
            ],
            'fields' => ['DISTINCT Proveedorspago.numero', 'Proveedor.name', 'Proveedor.id', 'Proveedorspago.concepto', 'Proveedorspago.created', 'Proveedorspago.user_id', 'Proveedorspago.fecha', 'Proveedorspago.anulado', 'Proveedorspago.modified',
                'Proveedorspago.id', 'Proveedorspago.importe', 'Proveedorspago.tipo', 'Liquidation.consorcio_id', 'Liquidation.periodo', 'Cajasegreso.consorcio_id', 'Bancosextraccione.consorcio_id', 'Chequespropio.bancoscuenta_id', 'Bancoscuenta.consorcio_id',
                'Proveedorspagosacuenta.proveedorspagoaplicado_id', 'LiquidationsType.prefijo', 'coalesce(Liquidation.consorcio_id,Cajasegreso.consorcio_id,Bancosextraccione.consorcio_id,Bancoscuenta.consorcio_id) as consorcio'
            ],
            'order' => (!empty($proveedor_id) ? 'consorcio,Proveedorspago.fecha desc,Proveedorsfactura.proveedor_id,Proveedorsfactura.fecha' : 'Proveedor.name,Consorcio.code,Proveedorspago.fecha desc,Proveedorsfactura.proveedor_id,Liquidation.consorcio_id,Proveedorsfactura.fecha'),
            'group' => 'Proveedorspago.id'
                ]
        );
        return $resul;
    }

    /*
     * Para el estado de disponibilidad de las extraordinarias o fondos (gastos pagos en esos tipos de liquidaciones
     */

    public function getTotalPagosPorLiquidacion($consorcio_id, $desde = null, $hasta = null, $prefijoLT = -1) {
        $pagos = $this->getPagos(0, $consorcio_id, $desde, $hasta, 0, $prefijoLT);
        $total = 0;
        foreach ($pagos as $k => $v) {
            $total += $v['Proveedorspago']['importe'];
        }
        return $total;
    }

    /*
     * Obtiene los pagos a proveedor del consorcio generados en un rango de fechas
     * Se utiliza en la generacion de asientos automaticos
     */

    public function getTotalPagosPorFecha($consorcio_id, $desde, $hasta) {
        $cuentas = [];
        if (!empty($consorcio_id)) {
            $cuentas = array_keys($this->Proveedor->Client->Consorcio->Bancoscuenta->getCuentasBancarias($consorcio_id));
        }

        $condicionconsorcio = ['OR' => ['Liquidation.consorcio_id' => $consorcio_id,
                ['Liquidation.consorcio_id is null',
                    'OR' => [
                        ['AND' => ['Cajasegreso.consorcio_id' => $consorcio_id, 'Bancosextraccione.consorcio_id' => null]],
                        ['AND' => ['Cajasegreso.consorcio_id' => null, 'Bancosextraccione.consorcio_id' => $consorcio_id, 'Chequespropio.bancoscuenta_id' => null]],
                        ['AND' => ['Cajasegreso.consorcio_id' => null, 'Bancosextraccione.consorcio_id' => null, 'Chequespropio.bancoscuenta_id' => $cuentas]]
                    ]
                ]]
        ];

        $resul = $this->find('list', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'],
        'Proveedorspago.fecha >=' => $this->fecha($desde), 'Proveedorspago.fecha <=' => $this->fecha($hasta)] + $condicionconsorcio,
            'contain' => ['Proveedor'],
            'joins' => [
                ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=LiquidationsType.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Liquidation.consorcio_id=Consorcio.id']],
                // para pago a cuenta
                ['table' => 'proveedorspagosacuentas', 'alias' => 'Proveedorspagosacuenta', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspagoaplicado_id']],
                ['table' => 'cajasegresos', 'alias' => 'Cajasegreso', 'type' => 'left', 'conditions' => ['Cajasegreso.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'bancosextracciones', 'alias' => 'Bancosextraccione', 'type' => 'left', 'conditions' => ['Bancosextraccione.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'chequespropios', 'alias' => 'Chequespropio', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Chequespropio.bancoscuenta_id=Bancoscuenta.id']],
            ],
            'fields' => ['Proveedorspago.id', 'Proveedorspago.importe'
            ],
            'group' => 'Proveedorspago.id'
                ]
        );
        if (empty($resul)) {
            return 0;
        }
        $total = 0;
        foreach ($resul as $v) {
            $total += $v;
        }
        return $total;
    }

    /*
     * Se llama desde Liquidations/index, el reporte Listar pagos proveedor
     */

    public function getPagosPorLiquidacion($liquidation_id) {
        $last = $this->Proveedor->Client->Consorcio->Liquidation->getLastLiquidation($liquidation_id);
        if ($last == 0) {
            $desde = $this->Proveedor->Client->Consorcio->Liquidation->getLiquidationCreatedDate($liquidation_id);
        } else {
            $desde = $this->Proveedor->Client->Consorcio->Liquidation->getLiquidationClosedDate($last);
        }
        $hasta = $this->Proveedor->Client->Consorcio->Liquidation->getLiquidationClosedDate($liquidation_id);
        $LiquidationTypeId = $this->Proveedor->Client->Consorcio->Liquidation->getLiquidationsTypeId($liquidation_id);
        $prefijo = $this->Proveedor->Client->LiquidationsType->getPrefijo($_SESSION['Auth']['User']['client_id'], $LiquidationTypeId);

        return $this->getPagos(null, $this->Proveedor->Client->Consorcio->Liquidation->getConsorcioId($liquidation_id), $desde, $hasta, 0, $prefijo);
    }

    // Obtiene los pagos de una liquidacion a mostrar en el reporte ver pagos que esta en liquidaciones

    public function getPagosReporteVerPagos($liquidation_id) {
        $todosLosPagos = $this->getPagosPorLiquidacion($liquidation_id);
        $pac = $this->Proveedorspagosacuenta->getPagosAplicados();
        $busc = $todosLosPagos;

        foreach ($todosLosPagos as $k => $v) {
            if (in_array($v['Proveedorspago']['id'], $pac)) {   // si el pago a proveedor tiene pago a cuenta aplicado y ninguna otra forma de pago se hace el unset, 
                // sino hay que mostrar el pago con el valor de lo que hizo la otra forma de pago
                if (is_null($v['Cajasegreso']['consorcio_id']) && is_null($v['Bancosextraccione']['consorcio_id']) && is_null($v['Chequespropio']['bancoscuenta_id'])) {
                    unset($busc[$k]);
                }
            }
        }

        $pagos = [];
        foreach ($busc as $k => $v) {
            $idPagoProveedor = $v['Proveedorspago']['id'];
            $pagos[$idPagoProveedor] = $this->getDetalleFormaPago($idPagoProveedor);
        }
        return $pagos;
    }

    //Obtiene el detalle de la forma de pago para el pago a proveedor.
    //$id es el id del pago a proveedor.

    public function getDetalleFormaPago($id) {
        $options = ['conditions' => ['Proveedorspago.id' => $id], 'recursive' => 2, 'contain' => ['Proveedor', 'User', 'Proveedorspagosacuenta', 'Proveedorspagoscheque'],
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Proveedor.client_id']]]];
        $proveedorspago = $this->find('first', $options);
        $ids = $this->find('list', ['conditions' => ['Proveedorspago.numero' => $proveedorspago['Proveedorspago']['numero'], 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'fields' => ['Proveedorspago.id']]);
        $facturas = $this->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorspago_id' => $id],
            'fields' => ['Proveedorsfactura.*', 'Consorcio.name', 'Consorcio.id', 'Liquidation.periodo', 'Proveedorspagosfactura.importe', 'Bancoscuenta.id'],
            'group' => ['Proveedorsfactura.id'],
            'joins' => [['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Liquidation.consorcio_id=Consorcio.id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.consorcio_id=Liquidation.consorcio_id']]]]);
        $cheques = Hash::combine($this->Proveedorspagoscheque->find('all', ['conditions' => ['Proveedorspagoscheque.proveedorspago_id' => $id], 'recursive' => -1, 'contain' => ['Cheque']]), '{n}.Cheque.id', '{n}.Cheque');
        $chequespropios = Hash::combine($this->Proveedor->Client->Chequespropio->find('all', ['conditions' => ['Chequespropio.proveedorspago_id' => $id], 'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']]]]), '{n}.Chequespropio.id', '{n}.Chequespropio');
        $chequespropiosadm = $this->Proveedor->Client->Chequespropiosadm->Chequespropiosadmsdetalle->find('all', ['conditions' => ['Chequespropiosadmsdetalle.proveedorspago_id' => $id], 'recursive' => 0, 'contain' => ['Chequespropiosadm']]);
        $efectivo = $this->Proveedor->Client->Caja->Cajasegreso->find('first', ['conditions' => ['Cajasegreso.proveedorspago_id' => $id], 'recursive' => 0, 'fields' => ['Cajasegreso.importe', 'Caja.name']]);
        $efectivoadm = $this->Administracionefectivo->find('all', ['conditions' => ['Administracionefectivo.proveedorspago_id' => $ids], 'contain' => ['Administracionefectivosdetalle'],
            'group' => ['Administracionefectivo.bancoscuenta_id'],
            'joins' => [['table' => 'administracionefectivosdetalles', 'alias' => 'Administracionefectivosdetalle', 'type' => 'left', 'conditions' => ['Administracionefectivo.id=Administracionefectivosdetalle.administracionefectivo_id']]]]);
        $transferencia = $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->find('all', ['conditions' => ['Bancosextraccione.proveedorspago_id' => $id], 'recursive' => 0, 'fields' => ['Bancoscuenta.name', 'Bancoscuenta.id', 'Bancosextraccione.importe', 'Bancosextraccione.consorcio_id']]);
        $transferenciaadm = $this->Administraciontransferencia->find('all', ['conditions' => ['Administraciontransferencia.proveedorspago_id' => $ids], 'contain' => ['Administraciontransferenciasdetalle'],
            'group' => ['Administraciontransferencia.bancoscuenta_id'],
            'joins' => [['table' => 'administraciontransferenciasdetalles', 'alias' => 'Administraciontransferenciasdetalle', 'type' => 'left', 'conditions' => ['Administraciontransferencia.id=Administraciontransferenciasdetalle.administraciontransferencia_id']]]]);
        $pagosacuentaaplicados = $this->Proveedorspagosacuenta->find('all', ['conditions' => ['Proveedorspagosacuenta.proveedorspagoaplicado_id' => $id]]);
        $notasdecreditoaplicadas = $this->Proveedorspagosnc->find('all', ['conditions' => ['Proveedorspagosnc.proveedorspago_id' => $id]]);
        $bancoscuentas = $this->Proveedor->Client->Banco->Bancoscuenta->get();
        $bancoscuentasporconsorcio = $this->Proveedor->Client->Banco->Bancoscuenta->getCuentasBancariasPorConsorcio();

        return ['proveedorspago' => $proveedorspago, 'facturas' => $facturas, 'efectivo' => $efectivo, 'efectivoadm' => $efectivoadm, 'transferencia' => $transferencia, 'transferenciaadm' => $transferenciaadm, 'cheques' => $cheques, 'chequespropios' => $chequespropios, 'chequespropiosadm' => $chequespropiosadm, 'bancoscuentas' => $bancoscuentas, 'pagosacuentaaplicados' => $pagosacuentaaplicados, 'notasdecreditoaplicadas' => $notasdecreditoaplicadas, 'bancoscuentasporconsorcio' => $bancoscuentasporconsorcio];
    }

    /*
     * Obtengo el total de pagos a proveedor y su detalle
     */

    public function getTotalPagosPorConsorcio($consorcio, $desde, $hasta, $proveedor = null, $incluiranulados = 0) {
        $d = $this->fecha($desde);
        $h = $this->fecha($hasta);
        return ['efectivocheque' => $this->getPagosEfectivoChequePorConsorcio($consorcio, $d, $h, $proveedor, $incluiranulados),
            'chequepropio' => $this->getPagosChequePropioPorConsorcio($consorcio, $d, $h, $proveedor, $incluiranulados),
            'transferencia' => $this->getPagosTransferenciaPorConsorcio($consorcio, $d, $h, $proveedor, $incluiranulados),
            'acuenta' => $this->getPagosACuenta($consorcio, $d, $h, $proveedor, $incluiranulados),
            'efectivoadm' => $this->Administracionefectivo->getPagosEfectivoAdmPorConsor($consorcio, $d, $h, $proveedor, $incluiranulados, 1),
            'chequepropioadm' => $this->Chequespropiosadmsdetalle->Chequespropiosadm->getPagosChequespropiosadmPorConsor($consorcio, $d, $h, $proveedor, $incluiranulados, 1),
            'transferenciaadm' => $this->Administraciontransferencia->getPagosTransferenciaAdmPorConsor($consorcio, $d, $h, $proveedor, $incluiranulados, 1),
        ];
    }

    public function getPagosEfectivoChequePorConsorcio($consorcio, $desde, $hasta, $proveedor = null, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['Cajasegreso.importe !=' => null, 'Cajasegreso.cheque !=' => null] + (!empty($desde) ? ['Proveedorspago.created >=' => $desde] : []) + (!empty($hasta) ? ['Proveedorspago.created <=' => $hasta] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (!empty($consorcio) ? ['Cajasegreso.consorcio_id' => $consorcio, 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] : []) +
                    (empty($incluiranulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'cajasegresos', 'alias' => 'Cajasegreso', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Cajasegreso.proveedorspago_id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Cajasegreso.consorcio_id=Consorcio.id']],
                    ], //DEJAR EL DISTINCT Porq sino duplica algunos egresos de caja
                    'fields' => ['DISTINCT Cajasegreso.id', 'Proveedorspago.id', 'Proveedorspago.proveedor_id', 'Proveedorspago.concepto', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspago.anulado', 'Cajasegreso.importe', 'Cajasegreso.cheque'],
                    'order' => 'Proveedorspago.created desc'
        ]);
    }

    /*
     * Utilizo Chequespropio.fecha_vencimiento porq no me importa en q fecha haya hecho el Pago. Si hace un PP con 3 cheques, vence hoy, mañana y pasado, y busco los pagos de mañana, NO me va a traer el PP, porq fue hecho hoy.
     * Y necesito q me traiga (estamos en cheques propios) los Cheques propios q vencen mañana, asi q me traería el cheque de mañana
     * Si el PP.created entra en desde y hasta, tomo todos los cheques anteriores
     * $usafechavencimiento se utiliza en false en asientos automaticos (busca cheques por fecha emision, no vencimiento)
     */

    public function getPagosChequePropioPorConsorcio($consorcio, $desde, $hasta, $proveedor = null, $incluiranulados = 0, $usafechavencimiento = true) {
        return $this->Chequespropio->find('all', ['conditions' => ['Chequespropio.proveedorspago_id !=' => 0] + (empty($incluiranulados) ? ['Proveedorspago.anulado' => 0] : []) +
                    //(!empty($desde) ? ['Chequespropio.fecha_vencimiento >=' => $desde] : []) + (!empty($hasta) ? ['Chequespropio.fecha_vencimiento <=' => $hasta] : []) +
                    (!empty($desde) && !empty($hasta) ? [//el pp entra en el rango y los vencimientos son menores a su creacion, O, el vencimiento entra en el rango y el 
                'OR' => [
                    ['Proveedorspago.created >=' => $desde, 'Proveedorspago.created <=' => $hasta, 'Chequespropio.fecha_vencimiento <= date(Proveedorspago.created)'],
                    ['Proveedorspago.created <=' => $hasta] + ($usafechavencimiento === true ? ['Chequespropio.fecha_vencimiento >=' => $desde, 'Chequespropio.fecha_vencimiento <=' => $hasta] : ['Chequespropio.fecha_emision >=' => $desde, 'Chequespropio.fecha_emision <=' => $hasta])
                ]
                    ] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (!empty($consorcio) ? ['Bancoscuenta.consorcio_id' => $consorcio, 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] : []),
                    'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']],
                        ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Chequespropio.bancoscuenta_id=Bancoscuenta.id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Bancoscuenta.consorcio_id=Consorcio.id']]
                    ],
                    //'contain' => ['Chequespropio.importe', 'Chequespropio.bancoscuenta_id', 'Chequespropio.concepto', 'Chequespropio.numero', 'Chequespropio.fecha_vencimiento'],
                    'fields' => ['DISTINCT Proveedorspago.id', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspago.concepto', 'Proveedorspago.importe', 'Proveedorspago.anulado', 'Chequespropio.*'],
                    'order' => 'Proveedorspago.created desc'
        ]);
    }

    public function getPagosTransferenciaPorConsorcio($consorcio, $desde, $hasta, $proveedor = null, $incluiranulados = 0) {
        return $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], /* 'Proveedorspagosacuenta.id is null', */ 'Bancosextraccione.proveedorspago_id !=' => 0, 'Bancosextraccione.proveedorspago_id is not null'] +
                    (!empty($desde) ? ['Proveedorspago.created >=' => $desde] : []) + (!empty($hasta) ? ['Proveedorspago.created <=' => $hasta] : []) + (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) +
                    (!empty($consorcio) ? ['Bancosextraccione.consorcio_id' => $consorcio] : []) + (empty($incluiranulados) ? ['Proveedorspago.anulado' => 0] : []),
                    'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']],
                        ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
                        ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                        ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                        ['table' => 'bancosextracciones', 'alias' => 'Bancosextraccione', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Bancosextraccione.proveedorspago_id']],
                    //['table' => 'proveedorspagosacuentas', 'alias' => 'Proveedorspagosacuenta', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspago_id']],
                    ], //va distinct sino me multiplica las transferencias, 1 x factura abonada. 2018/07/03
                    'fields' => ['DISTINCT Bancosextraccione.proveedorspago_id', 'Bancosextraccione.bancoscuenta_id', 'Bancosextraccione.fecha', 'Bancosextraccione.created', 'Bancosextraccione.concepto', 'Bancosextraccione.importe', 'Bancosextraccione.anulado'],
                    'order' => 'Proveedorspago.fecha desc'
        ]);
    }

    /*
     * Obtengo los pagos a cuenta, son pagosproveedor q pueden no tener factura asociada.
     * No muestro los q ya aparecen en getPagosEfectivoChequePorConsorcio.
     * Ej: si paga un pago a proveedor de 1000 con cheque de 1100, quedan 100 a cuenta, en la funcion anterior lo muestra como PP cheque 1100, y
     * lo mostraría tambien aca x 100 pesos, pero hay 100 pesos q no van
     */

    public function getPagosACuenta($consorcio, $desde, $hasta, $proveedor = null, $incluiranulados = 0) {// no me importa si fue aplicado o no, lo muestro igual (ya se pagó igual)
        //$ids = Hash::extract($this->getPagosEfectivoChequePorConsorcio($consorcio, $desde, $hasta, $proveedor), '{n}.Proveedorspago.id');   //H:i:s
        return $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cajasegreso.importe is not null', /* 'Proveedorspagosacuenta.proveedorspagoaplicado_id' => 0, */
                    /* 'Proveedorspago.id !=' => $ids */                    ] + (empty($incluiranulados) ? ['Proveedorspago.anulado' => 0] : []) +
                    (!empty($desde) ? ['Proveedorspago.created >=' => $desde] : []) + (!empty($hasta) ? ['Proveedorspago.created <=' => $hasta] : []) +
                    (!empty($proveedor) ? ['Proveedorspago.proveedor_id' => $proveedor] : []) + (!empty($consorcio) ? ['Proveedorspagosacuenta.consorcio_id' => $consorcio] : []),
                    'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']],
                        ['table' => 'proveedorspagoscheques', 'alias' => 'Proveedorspagoscheque', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagoscheque.proveedorspago_id']],
                        ['table' => 'cheques', 'alias' => 'Cheque', 'type' => 'left', 'conditions' => ['Proveedorspagoscheque.cheque_id=Cheque.id']],
                        ['table' => 'proveedorspagosacuentas', 'alias' => 'Proveedorspagosacuenta', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosacuenta.proveedorspago_id']],
                        ['table' => 'cajasegresos', 'alias' => 'Cajasegreso', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Cajasegreso.proveedorspago_id']], // pagos a cuenta en efectivo
                        ['table' => 'bancosextracciones', 'alias' => 'Bancosextraccione', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Bancosextraccione.proveedorspago_id']], //pagos a cuenta x transferencia
                    ],
                    'fields' => ['Proveedorspago.concepto', 'Proveedorspago.fecha', 'Proveedorspago.created', 'Proveedorspagosacuenta.importe'/* para getTotalPagosACuenta() */, 'Proveedorspago.id', 'Proveedorspago.anulado', 'Cajasegreso.importe', 'Cajasegreso.cheque', 'Bancosextraccione.importe', 'Bancosextraccione.bancoscuenta_id', 'Cheque.id', 'Cheque.importe'],
                    'order' => 'Proveedorspago.fecha desc'
        ]);
    }

    public function getTotalPagosACuenta($consorcio, $desde, $hasta, $incluiranulados = 0) {
        $resul = $this->getPagosACuenta($consorcio, $desde, $hasta, null, $incluiranulados);
        $total = ['e' => 0, 't' => 0, 'c' => 0];
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                if (isset($v['Cajasegreso']['importe']) && !empty($v['Cajasegreso']['importe'])) {
                    $total['e'] += $v['Cajasegreso']['importe'];
                }
                if (isset($v['Bancosextraccione']['importe']) && !empty($v['Bancosextraccione']['importe'])) {
                    $total['t'] += $v['Bancosextraccione']['importe'];
                }
                if (isset($v['Cheque']['importe']) && !empty($v['Cheque']['importe'])) {
                    $total['c'] += $v['Cheque']['importe'];
                }
            }
        }
        return $total;
    }

    /*
     * Se utiliza para el cron_saldoscajabanco.php, que guarda los saldos de caja y banco y otros en la tabla saldoscajabancos
     */

    public function getTotalPagosProveedor($consorcio, $desde, $hasta) {
        $resul = $this->getTotalPagosPorConsorcio($consorcio, $desde, $hasta, null, 1);
        $total = ['e' => 0, 'ch' => 0, 'chp' => 0, 't' => 0, 'eadm' => 0, 'chpadm' => 0, 'tadm' => 0];
        if (!empty($resul)) {
            foreach ($resul as $k => $v) {
                if ($k == 'efectivocheque' && !empty($v)) {
                    foreach ($v as $r) {
                        //$total += $r['Cajasegreso']['importe'] + $r['Cajasegreso']['cheque'];
                        $total['e'] += $r['Cajasegreso']['importe'];
                        $total['ch'] += $r['Cajasegreso']['cheque'];
                    }
                }
                if ($k == 'chequepropio' && !empty($v)) {
                    foreach ($v as $r) {
                        //foreach ($r['Chequespropio'] as $s) {
                        // sumo solamente los cheques propios que hayan VENCIDO en el rango de fecha especificado
                        // NO LO HAGO MAS, porq ya la funcion Proveedorspago::getPagosChequePropioPorConsorcio() obtiene los cheques vencidos en el rango de fecha, sin importar la fecha del PP
                        //if (strtotime($s['fecha_vencimiento']) >= strtotime($desde) && strtotime($s['fecha_vencimiento']) <= strtotime($hasta)) {
                        $total['chp'] += $r['Chequespropio']['importe'];
                        //}
                        //}
                    }
                }
                if ($k == 'transferencia' && !empty($v)) {
                    foreach ($v as $r) {
                        $total['t'] += $r['Bancosextraccione']['importe'];
                    }
                }
                if ($k == 'efectivoadm' && !empty($v)) {
                    foreach ($v as $vv) {
                        if (isset($vv['Administracionefectivosdetalle']) && !empty($vv['Administracionefectivosdetalle'])) {
                            foreach ($vv['Administracionefectivosdetalle'] as $r) {
                                $total['eadm'] += $r['importe'];
                            }
                        }
                    }
                }
                if ($k == 'chequepropioadm' && !empty($v)) {
                    foreach ($v as $vv) {
                        if (isset($vv['Chequespropiosadmsdetalle']) && !empty($vv['Chequespropiosadmsdetalle'])) {
                            foreach ($vv['Chequespropiosadmsdetalle'] as $r) {
                                $total['chpadm'] += $r['importe'];
                            }
                        }
                    }
                }
                if ($k == 'transferenciaadm' && !empty($v)) {
                    foreach ($v as $vv) {
                        if (isset($vv['Administraciontransferenciasdetalle']) && !empty($vv['Administraciontransferenciasdetalle'])) {
                            foreach ($vv['Administraciontransferenciasdetalle'] as $r) {
                                //$total += $r['importe'];
                                $total['tadm'] += $r['importe'];
                            }
                        }
                    }
                }
            }
        }
        return $total;
    }

    /* public function getTotalPagosChequePorConsorcio($caja_id, $desde, $hasta) {
      $cheque = Hash::combine($this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.consorcio_id >' => 0, 'Proveedorspago.anulado' => 0, 'Cheque.caja_id' => $caja_id,
      'Cheque.proveedorspago_id !=' => 0] + (!empty($desde) ? ['Proveedorspago.fecha >=' => $desde] : []) + (!empty($hasta) ? ['Proveedorspago.fecha <=' => $hasta] : []),
      'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']],
      ['table' => 'proveedorspagosfacturas', 'alias' => 'Proveedorspagosfactura', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Proveedorspagosfactura.proveedorspago_id']],
      ['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
      ['table' => 'cheques', 'alias' => 'Cheque', 'type' => 'left', 'conditions' => ['Proveedorspago.id=Cheque.proveedorspago_id']],
      ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']]],
      'fields' => ['Liquidation.consorcio_id', 'ifnull(sum(Cheque.importe),0) as Proveedorcheque'],
      'group' => 'Liquidation.consorcio_id']), '{n}.Liquidation.consorcio_id', '{n}.0');
      return $cheque;
      } */

    /*
     * Guarda uno o mas pagos a facturas de proveedores
     * Si no selecciona una factura es un pago a cuenta (cero)
     */

    //'Proveedorspago' => array(
    //        'proveedor_id' => '476',
    //        'concepto' => 'PP ABETE Y CIA',
    //        'fecha' => array(
    //                'day' => '02',
    //                'month' => '02',
    //                'year' => '2018'
    //        )
    //),
    //(int) 305 => array(
    //        'fac' => array(
    //                (int) 3222 => '44.92'
    //        ),
    //        'chpadm' => array(
    //                (int) 3900 => '44.00',
    //                (int) 3902 => '0.00'
    //        ),
    //        'chequepropio' => array(
    //                (int) 3901 => '0.92'
    //        )
    //),
    //(int) 310 => array(
    //        'fac' => array(
    //                (int) 5699 => '904.53'
    //        ),
    //        'chpadm' => array(
    //                (int) 3900 => '0.00',
    //                (int) 3902 => '900.00'
    //        ),
    //        'transferencia' => array(
    //                (int) 179 => '4.53'
    //        )
    //)

    public function guardar($data) {
        $resul = $this->beforeGuardar($data);
        if ($resul !== "") {
            return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
        }
        $sumatotal = 0; //para actualizar el saldo del Proveedor
        $caja_id = $this->Proveedor->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        $max = $this->find('first', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['max(Proveedorspago.numero) as numero'],
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'right', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']]]]);
        $numerorecibo = $max[0]['numero'] + 1;
        $chpadm = $tadm = $eadm = [];
        foreach ($data as $k => $v) {
            if ($k === 'Proveedorspago') {
                continue;
            }
            $totalapagar = abs($this->_sumaTotalAPagar($v)); //si hace un pago a cuenta solamente, trae negativo el importe
            $sumatotal += $totalapagar;
            $fecha = $this->fecha($data['Proveedorspago']['fecha']);
            $datos = ['proveedor_id' => $data['Proveedorspago']['proveedor_id'], 'fecha' => $fecha, 'importe' => $totalapagar,
                'concepto' => $data['Proveedorspago']['concepto'], 'anulado' => 0, 'user_id' => $_SESSION['Auth']['User']['id'], 'numero' => $numerorecibo];
            $this->create();
            $resul = $this->save($datos);
            $proveedorspago_id = $resul['Proveedorspago']['id'];
            if (empty($proveedorspago_id)) {
                return ['e' => 1, 'd' => implode(" ", reset($this->validationErrors))]; // Ej: {"importe":["Debe ser un importe mayor a cero"]}, al hacer reset e implode, devuelve "Debe ser un importe mayor a cero"
            }
            $totalefectivo = $totalcht = 0;
            foreach ($v as $r => $s) {
                if ($r === 'fac') {
                    foreach ($s as $a => $b) {// actualizo el saldo de la factura abonada (sin importar la forma de pago)
                        $this->Proveedor->Proveedorsfactura->setSaldo($a, -$b);
                        $this->Proveedorspagosfactura->create();
                        $this->Proveedorspagosfactura->save(['proveedorspago_id' => $proveedorspago_id, 'proveedorsfactura_id' => $a, 'importe' => $b]);
                    }
                    continue;
                }
                if ($r === 'transferencia') {
                    foreach ($s as $a => $b) {
                        $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->create();
                        $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->save(['bancoscuenta_id' => $a, 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'],
                            'proveedorspago_id' => $proveedorspago_id, 'consorcio_id' => $k, 'fecha' => $fecha, 'concepto' => $data['Proveedorspago']['concepto'], 'importe' => $b, 'anulado' => 0]);
                    }
                    continue;
                }

                //(int) 65 => array(
                //    'pac' => '1000.00',
                //    'efectivoadm' => array(
                //        (int) 29 => '400.00'
                //    ),
                //    'transferenciaadm' => array(
                //        (int) 554 => array(
                //            (int) 27 => '600.00'
                //        )
                //    )
                //),
                //(int) 71 => array(
                //    'pac' => '1000.00',
                //    'efectivoadm' => array(
                //        (int) 29 => '400.00'
                //    ),
                //    'transferenciaadm' => array(
                //        (int) 554 => array(
                //                (int) 32 => '600.00'
                //        )
                //    )
                //)

                if ($r === 'transferenciaadm') {// los almaceno y proceso despues
                    foreach ($s as $a => $b) {// $k consorcio_id, $a bancoscuentaADM_id
                        if (!isset($tadm[$a])) {
                            $tadm[$a] = [];
                        }
                        foreach ($b as $b1 => $b2) {//$b1 bancoscuenta_id, $b2 importe
                            $tadm[$a][$k]['b'] = $b1;
                            $tadm[$a][$k]['i'] = $b2;
                        }
                    }
                    continue;
                }

                if ($r === 'efectivo') {// pago en efectivo con plata de la caja del usuario y consorcio $k
                    $totalefectivo = $s;
                    continue;
                }
                if ($r === 'efectivoadm') {// los almaceno y proceso despues
                    foreach ($s as $a => $b) {// $k consorcio_id, $a bancoscuentaADM_id
                        if (!isset($eadm[$a])) {
                            $eadm[$a] = [];
                        }
                        $eadm[$a][$k]['i'] = $b;
                    }
                    continue;
                }
                if ($r === 'chequeterceros') {// pago con chequeterceros
                    foreach ($s as $a => $b) {
                        $importe = $this->Proveedor->Client->Cheque->getImporte($a);
                        //$this->Proveedor->Client->Cheque->setSaldo($chequeid, -$saldo); // actualizo el saldo del cheque (dejo el saldo en CERO (uso todo el cheque))
                        $this->Proveedor->Client->Cheque->id = $a;
                        $this->Proveedor->Client->Cheque->save(['proveedorspago_id' => $proveedorspago_id]); // lo asocio al pago proveedor
                        //$this->Proveedor->Client->Caja->setSaldo($caja_id, -$importe, 'saldo_cheques'); // actualizo el saldo cheque de la caja (entregué el cheque de terceros)
                        $this->Proveedorspagoscheque->create(); // guardo acá para q me quede historial en caso q elimine el pago proveedor (se q cheques utilizó)
                        $this->Proveedorspagoscheque->save(['proveedorspago_id' => $proveedorspago_id, 'cheque_id' => $a]);
                        $totalcht += $importe;
                    }
                    continue;
                }

                if ($r === 'chequepropio') {// pago con chequepropio
                    foreach ($s as $a => $b) {
                        //$this->Proveedor->Client->Chequespropio->save(['id' => $a, 'proveedorspago_id' => $proveedorspago_id], ['callbacks' => false]); 
                        $this->Proveedor->Client->Chequespropio->id = $a;
                        $this->Proveedor->Client->Chequespropio->saveField('proveedorspago_id', $proveedorspago_id, ['callbacks' => false]); // lo asocio al pago proveedor (QUEDA EN USO)
                        if (strtotime($this->Proveedor->Client->Chequespropio->field('fecha_vencimiento')) <= strtotime(date("Y-m-d"))) {
                            $this->Proveedor->Client->Banco->Bancoscuenta->setSaldo($this->Proveedor->Client->Chequespropio->field('bancoscuenta_id'), -$this->Proveedor->Client->Chequespropio->field('importe'));
                        }
                    }
                    continue;
                }
                if ($r === 'chpadm') {// pago con chequepropio de administracion
                    //debug("asd");
                    //debug($s);
                    foreach ($s as $a => $b) {// $k consorcio, $a chequeadm_id
                        if (!isset($chpadm[$a])) {
                            $chpadm[$a] = [];
                        }
                        foreach ($b as $b1 => $b2) {
                            $chpadm[$a][$k]['i'] = $b2;
                            $chpadm[$a][$k]['pp'] = $proveedorspago_id;
                            $chpadm[$a][$k]['bc'] = $b1;
                        }
                    }
                    continue;
                }

                // hizo un pago a cuenta
                if ($r === 'pac') {
                    $this->Proveedorspagosacuenta->create();
                    $this->Proveedorspagosacuenta->save(['proveedorspago_id' => $proveedorspago_id, 'proveedorspagoaplicado_id' => 0, 'importe' => $s, 'consorcio_id' => $k]);
                }

                if ($r === 'paca') {
                    foreach ($s as $a => $b) {
                        $this->Proveedorspagosacuenta->id = $a;
                        $this->Proveedorspagosacuenta->saveField('proveedorspagoaplicado_id', $proveedorspago_id); // lo asocio al pago proveedor
                    }
                }
                // pagó parte aplicando NOTAS DE CREDITO
                if ($r === 'anc') {
                    foreach ($s as $a => $b) {
                        $this->Proveedorspagosnc->create();
                        $this->Proveedorspagosnc->save(['proveedorspago_id' => $proveedorspago_id, 'proveedorsfactura_id' => $a, 'importe' => $b]);
                        $this->Proveedor->Proveedorsfactura->setSaldo($a, -$b); // actualizo el saldo de la nota de crédito
                    }
                }
            }

            // al salir de recorrer el consorcio, creo el egreso de caja (si pago en efectivo o cheque terceros)
            if ($totalefectivo > 0 || $totalcht > 0) {
                $this->Proveedor->Client->Caja->Cajasegreso->create();
                $this->Proveedor->Client->Caja->Cajasegreso->save(['proveedorspago_id' => $proveedorspago_id, 'consorcio_id' => $k, 'caja_id' => $caja_id, 'user_id' => $_SESSION['Auth']['User']['id'],
                    'fecha' => $fecha, 'concepto' => $data['Proveedorspago']['concepto'], 'importe' => $totalefectivo, 'cheque' => $totalcht], ['callbacks' => false, 'validate' => false]);
                $this->Proveedor->Client->Caja->setSaldo($caja_id, -$totalefectivo); //actualizo el saldo efectivo
                $this->Proveedor->Client->Caja->setSaldo($caja_id, -$totalcht, 'saldo_cheques'); //actualizo el saldo cheque de la caja
            }
        }

        // al finalizar, veo si hay cheques propios de administracion y los asocio al PagoProveedor
        foreach ($chpadm as $k => $v) {// $k chequeadmid, $v detalle consorcio_id=>importe y pp_id
            if (!empty($v)) {
                foreach ($v as $m => $n) {//$n[pp] proveedorspago_id, $m consorcio_id
                    $this->Proveedor->Client->Chequespropiosadm->Chequespropiosadmsdetalle->updateAll(['Chequespropiosadmsdetalle.proveedorspago_id' => $n['pp']], ['Chequespropiosadmsdetalle.chequespropiosadm_id' => $k, 'Chequespropiosadmsdetalle.bancoscuenta_id' => $n['bc']]); // lo asocio al pago proveedor
                    // NO Modifico el saldo de cada cuenta bancaria, es CHP de ADM, se modifica solo la Cuenta Bancaria de la ADM
                    /* foreach ($chequeadm['Chequespropiosadmsdetalle'] as $d) {
                      if ($d['bancoscuenta_id'] == $cuentabanco) {
                      if (strtotime($chequeadm['Chequespropiosadm']['fecha_vencimiento']) <= strtotime(date("Y-m-d"))) {
                      $this->Proveedor->Client->Banco->Bancoscuenta->setSaldo($cuentabanco, -$d['importe']);
                      }
                      break;
                      }
                      } */
                    //$this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->create();
                    //$this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->save(['bancoscuenta_id' => $cuentabanco, 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'],
                    //    'proveedorspago_id' => 0/* Asi diferencio CHPAdm de Extraccion comun */, 'consorcio_id' => $m, 'fecha' => $chequeadm['Chequespropiosadm']['fecha_vencimiento'], 'concepto' => $data['Proveedorspago']['concepto'] . " ChPAdm " . $chequeadm['Chequespropiosadm']['concepto'] . " #" . $chequeadm['Chequespropiosadm']['numero'] . " Importe: " . $chpadmimporte, 'importe' => $chpadmimporte, 'anulado' => 0]);
                }
                $chequeadm = $this->Proveedor->Client->Chequespropiosadm->getInfo($k);
                $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->create();
                $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->save(['bancoscuenta_id' => $chequeadm['Chequespropiosadm']['bancoscuenta_id']/* $n['bc'] */, 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'],
                    'proveedorspago_id' => 0/* Asi diferencio CHPAdm de Extraccion comun */, 'consorcio_id' => $m, 'fecha' => $chequeadm['Chequespropiosadm']['fecha_vencimiento'], 'concepto' => $data['Proveedorspago']['concepto'] . " ChPAdm " . $chequeadm['Chequespropiosadm']['concepto'] . " #" . $chequeadm['Chequespropiosadm']['numero'] . " Importe: " . $chequeadm['Chequespropiosadm']['importe'], 'importe' => $chequeadm['Chequespropiosadm']['importe'], 'anulado' => 0]);
                $this->Proveedor->Client->Banco->Bancoscuenta->setSaldo($chequeadm['Chequespropiosadm']['bancoscuenta_id'], -$chequeadm['Chequespropiosadm']['importe']);
            }
        }
        //proceso las transferencias ADM
        if (!empty($tadm)) {
            foreach ($tadm as $k => $v) {//$k bancoscuentaADM_id, $a consorcio_id, b bancoscuenta_id, i importe
                $this->Proveedor->Client->Banco->Bancoscuenta->Administraciontransferencia->create();
                $resul = $this->Proveedor->Client->Banco->Bancoscuenta->Administraciontransferencia->save(['proveedorspago_id' => $proveedorspago_id, 'bancoscuenta_id' => $k]);
                $tid = $resul['Administraciontransferencia']['id'];
                foreach ($v as $r => $s) {
                    $this->Proveedor->Client->Banco->Bancoscuenta->Administraciontransferencia->Administraciontransferenciasdetalle->create();
                    $this->Proveedor->Client->Banco->Bancoscuenta->Administraciontransferencia->Administraciontransferenciasdetalle->save(['administraciontransferencia_id' => $tid, 'bancoscuenta_id' => $s['b'], 'importe' => $s['i']]);
                }
            }
        }

        //proceso efectivo ADM
        if (!empty($eadm)) {
            $totaleadm = 0;
            foreach ($eadm as $k => $v) {//$k bancoscuentaADM_id, $r consorcio_id, b bancoscuenta_id, i importe
                $this->Proveedor->Client->Banco->Bancoscuenta->Administracionefectivo->create();
                $resul = $this->Proveedor->Client->Banco->Bancoscuenta->Administracionefectivo->save(['proveedorspago_id' => $proveedorspago_id, 'bancoscuenta_id' => $k]);
                $tid = $resul['Administracionefectivo']['id'];
                foreach ($v as $r => $s) {
                    $this->Proveedor->Client->Banco->Bancoscuenta->Administracionefectivo->Administracionefectivosdetalle->create();
                    $this->Proveedor->Client->Banco->Bancoscuenta->Administracionefectivo->Administracionefectivosdetalle->save(['administracionefectivo_id' => $tid, 'consorcio_id' => $r, 'importe' => $s['i']]);
                    $totaleadm += $s['i'];
                }
                $cajaadm = $this->Proveedor->Client->Caja->getCajaAdm(0);
                if (!empty($cajaadm) && $totaleadm > 0) {
                    $this->Proveedor->Client->Caja->Cajasegreso->crear(['user_id' => 0, 'caja_id' => $cajaadm, 'importe' => $totaleadm, 'cheque' => 0, 'estransferencia' => 0, 'anulado' => 0, 'fecha' => $fecha,
                        'concepto' => "EADM " . $data['Proveedorspago']['concepto']]);
                }
            }
        }
        // actualizo el saldo del Proveedor (en amount tengo el monto abonado, no me importa si fue transferencia, caja o cheques)
        $this->Proveedor->setSaldo($data['Proveedorspago']['proveedor_id'], -$sumatotal);
        return ['e' => 0];
    }

    /*
     * Realizo los chequeos previos al pago de las facturas proveedor
     */

    public function beforeGuardar($data) {
        $errores = "";

        //chequeo q el proveedor sea del cliente actual y exista
        if (isset($data['Proveedorspago']['proveedor_id']) && !($this->Proveedor->getProveedorClientId($data['Proveedorspago']['proveedor_id']) == $_SESSION['Auth']['User']['client_id'])) {
            $errores .= "El Proveedor es inexistente\n";
        }

        foreach ($data as $k => $v) {
            if ($k === 'Proveedorspago' || (!isset($v['fac']) && !isset($v['pac']))) {// si es Proveedorspago o no tiene facturas a pagar ni pago a cuenta (si se llega a enviar algo sin facturas)
                continue;
            }

            //chequeo q los campos sean correctos
            $this->set(['proveedor_id' => $data['Proveedorspago']['proveedor_id'], 'fecha' => $this->fecha($data['Proveedorspago']['fecha']), 'importe' => 1, 'concepto' => $data['Proveedorspago']['concepto'],
                'anulado' => 0, 'user_id' => $_SESSION['Auth']['User']['id']]);
            if (!$this->validates()) {
                $errores .= implode(". ", $this->validationErrors);
            }
            if (!$this->_verificaTotalAbonado($v)) {
                $errores .= "El total de Facturas es distinto al total Abonado para el Consorcio " . $this->Proveedor->Client->Consorcio->getConsorcioName($k) . "\n";
            }

            // verifico que las Facturas seleccionadas tengan pendiente de abonar el importe seleccionado
            if (isset($v['fac'])) {// si vienen PagosACuenta, 'fac' no esta definido
                foreach ($v['fac'] as $r => $s) {
                    if (!$this->Proveedor->Proveedorsfactura->hasSaldo($r, (float) $s)) {
                        $errores .= "El importe a pagar de la Factura #" . $this->Proveedor->Proveedorsfactura->getNumeroFactura($r) . " es mayor a su saldo pendiente\n";
                    }
                }
            }

            // verifico (si hay efectivo para usar) NADA, no se chequea
            // verifico (si hay CHP para usar) NADA
            // verifico (si hay Transferencias para usar) que la cuenta bancaria sea del consorcio actual
            if (isset($v['transferencia'])) {
                foreach ($v['transferencia'] as $r => $s) {
                    $cuentas = array_keys($this->Proveedor->Client->Consorcio->Bancoscuenta->getCuentasBancarias($k));
                    if (!in_array($r, $cuentas)) {
                        $errores .= "La Cuenta Bancaria '" . $this->Proveedor->Client->Consorcio->Bancoscuenta->getBancoNombre($k) . "' destino no pertenece al Consorcio '" . $this->Proveedor->Client->Consorcio->getConsorcioName($k) . "'\n";
                    }
                }
            }
            if (isset($v['transferenciaadm'])) {
                foreach ($v['transferenciaadm'] as $r => $s) {
                    foreach ($s as $t => $u) {
                        if ($this->Proveedor->Client->Consorcio->Bancoscuenta->getConsorcio($t) != $k) {
                            $errores .= "La Cuenta Bancaria '" . $this->Proveedor->Client->Consorcio->Bancoscuenta->getBancoNombre($t) . "' destino no pertenece al Consorcio '" . $this->Proveedor->Client->Consorcio->getConsorcioName($k) . "'\n";
                        }
                    }
                }
            }

            // verifico (si hay Chequeterceros para usar) que esten listos para entregar
            if (isset($v['chequeterceros'])) {
                foreach ($v['chequeterceros'] as $r => $s) {
                    if ($this->Proveedor->Client->Cheque->isAnulado($r)) {
                        $errores .= "El Cheque de terceros #" . $this->Proveedor->Client->Cheque->getNumero($r) . " del Consorcio '" . $this->Proveedor->Client->Consorcio->getConsorcioName($k) . "' se encuentra anulado\n";
                    } else {
                        if (!$this->Proveedor->Client->Cheque->isListoParaEntregar($r)) {
                            $errores .= "El Cheque de terceros #" . $this->Proveedor->Client->Cheque->getNumero($r) . " del Consorcio '" . $this->Proveedor->Client->Consorcio->getConsorcioName($k) . "' ya fue utilizado en Pago a Proveedor\n";
                        }
                    }
                }
            }
            // verifico (si hay Chequespropios para usar) que esten listos para utilizar
            if (isset($v['chequepropio'])) {
                foreach ($v['chequepropio'] as $r => $s) {
                    if ($this->Proveedor->Client->Chequespropio->isInUse($r)) {
                        $errores .= "El Cheque propio #" . $this->Proveedor->Client->Chequespropio->getNumero($r) . " ya fue utilizado o se encuentra anulado\n";
                    }
                }
            }
            if (isset($v['chpadm'])) {
                foreach ($v['chpadm'] as $r => $s) {
                    $chpinfo = $this->Proveedor->Client->Chequespropiosadm->getInfo($r);
                    if (empty($chpinfo) || $chpinfo['Chequespropiosadm']['anulado']) {
                        $errores .= "El Cheque propio de Administracion de $$s se encuentra anulado o es inexistente\n";
                    }
                }
            }
            if (isset($v['pac'])) {
                $pac = (float) $v['pac'];
                if ($pac > 0 && isset($v['paca']) && !empty($v['paca'])) {
                    $errores .= "No se puede realizar un Pago a Cuenta utilizando como forma de pago otro Pago a Cuenta anterior\n";
                }
            }
            if (isset($v['anc'])) {
                foreach ($v['anc'] as $r => $s) {
                    if (!$this->Proveedor->Proveedorsfactura->hasSaldo($r, (float) $s)) {
                        $errores .= "El importe a utilizar de la Nota de Crédito #" . $this->Proveedor->Proveedorsfactura->getNumeroFactura($r) . " es mayor a su saldo\n";
                    }
                }
            }
        }

        return $errores;
    }

    /*
     * Devuelve verdadero si la suma del total de todas las formas de pago es igual al total de facturas enviadas
     * del consorcio seleccionado (un consorcio a la vez)
     */

    private function _verificaTotalAbonado($v) {
        //debug($v);
        $totalabonado = $totalfacturas = 0;
        foreach ($v as $r => $s) {
            if ($r === 'fac') {// sumo las facturas
                foreach ($s as $a => $b) {
                    $totalfacturas += (float) $b;
                }
            }
            if ($r === 'pac') {// sumo los pagos a cuenta
                $totalfacturas += (float) $s;
            }
            if ($r === 'chequepropio') { // sumo los cheques propios
                foreach ($s as $a => $b) {
                    $totalabonado += (float) $this->Proveedor->Client->Chequespropio->getImporte($a);
                }
            }
            if ($r === 'chpadm') { // sumo los cheques propios de administracion
                foreach ($s as $a => $b) {
                    foreach ($b as $b1) {
                        $totalabonado += (float) $b1;
                    }
                }
            }
            if ($r === 'efectivo') { // sumo el efectivo
                $totalabonado += (float) $s;
            }
            if ($r === 'efectivoadm') { // sumo el efectivoadm
                foreach ($s as $a => $b) {
                    $totalabonado += (float) $b;
                }
            }
            if ($r === 'transferencia') { // sumo las transferencias
                $totalabonado += array_sum($s);
            }
            if ($r === 'transferenciaadm') { // sumo las transferenciasadm
                foreach ($s as $a => $b) {
                    foreach ($b as $b1) {
                        $totalabonado += (float) $b1;
                    }
                }
            }
            if ($r === 'chequeterceros') { // sumo los cheques terceros
                foreach ($s as $a => $b) {
                    $totalabonado += $this->Proveedor->Client->Cheque->getImporte($a); // $b es cero (cheque terceros solamente tilda el cheque)
                }
            }
            if ($r === 'paca') { // sumo los pagos a cuenta aplicados
                foreach ($s as $a => $b) {
                    $totalabonado += $this->Proveedorspagosacuenta->getImporte($a);
                }
            }
            if ($r === 'anc') { // sumo las notas de credito
                foreach ($s as $a => $b) {
                    $totalabonado += (float) $b;
                }
            }
        }
//                if ($k == 'efectivoadm' && !empty($v)) {
//                    if (isset($v['Administracionefectivosdetalle']) && !empty($v['Administracionefectivosdetalle'])) {
//                        foreach ($v['Administracionefectivosdetalle'] as $r) {
//                            $total += $r['importe'];
//                        }
//                    }
//                }
//                if ($k == 'chequepropioadm' && !empty($v)) {
//                    if (isset($v['Chequespropiosadmsdetalle']) && !empty($v['Chequespropiosadmsdetalle'])) {
//                        foreach ($v['Chequespropiosadmsdetalle'] as $r) {
//                            $total += $r['importe'];
//                        }
//                    }
//                }
//                if ($k == 'transferenciaadm' && !empty($v)) {
//                    if (isset($v['Administraciontransferenciasdetalle']) && !empty($v['Administraciontransferenciasdetalle'])) {
//                        foreach ($v['Administraciontransferenciasdetalle'] as $r) {
//                            $total += $r['importe'];
//                        }
//                    }
//                }
        //debug($totalabonado);
        //debug($totalfacturas);
        return (bool) ("$totalabonado" === "$totalfacturas"); // dejar como string, sino 126.32 no es igual a 126.32!! JAJA, la puta q lo pario. La resta me da a veces 1.xxe-14
    }

    /*
     * Obtiene el total a pagar (facturas y pagos a cuenta) del Consorcio actual, resta PACA y ANC
     */

    private function _sumaTotalAPagar($v) {
        $totalabonado = 0;
        //foreach ($data as $k => $v) {
        foreach ($v as $r => $s) {
            if ($r === 'fac') {// sumo las facturas
                foreach ($s as $a => $b) {
                    $totalabonado += (float) $b;
                }
            }
            if ($r === 'pac') {// SUMO los pagos a cuenta
                $totalabonado += (float) $s;
            }
            if ($r === 'paca') {// RESTO los pagos a cuenta aplicados
                foreach ($s as $a => $b) {
                    $totalabonado -= (float) $this->Proveedorspagosacuenta->getImporte($a);
                }
            }
            if ($r === 'anc') { // RESTA las notas de credito aplicadas
                foreach ($s as $a => $b) {
                    $totalabonado -= (float) $b;
                }
            }
        }
        //}
        return $totalabonado;
    }

    /*
     * Obtengo el numero de recibo para el pago proveedor (único por cliente)
     */

    //public function beforeSave($options = []) {
    /* $max = $this->find('first', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['max(Proveedorspago.numero) as numero'],
      'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'right', 'conditions' => ['Proveedor.id=Proveedorspago.proveedor_id']]]]);
      $this->data['Proveedorspago']['numero'] = $max[0]['numero'] + 1; */

    //    return true;
    //}

    /*
     * Funcion que anula un pago a Proveedor
     * Antes de anular, se verifica: 
     *      que la caja tenga saldo para hacer el egreso
     *      que la cuenta bancaria tenga saldo para hacer la extraccion
     *      que los cheques no se hayan depositado
     * Para ello se anula la cobranza (field anulada)
     * se elimina el ingreso a la caja (si corresponde)
     * se elimina la transferencia (si corresponde)
     * se resta del saldo del cheque el importe sin anular el mismo (si corresponde)
     */

    public function undo($id) {
        $this->id = $id;
        $ids = $this->find('list', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorspago.numero' => $this->field('numero')], 'recursive' => 0]);
        $resul = $this->beforeUndo($ids);
        if (!empty($resul)) {
            return $resul;
        }
        foreach ($ids as $xk => $xv) {
            // elimino el egreso de la caja (si existe) y actualizo el saldo
            $cajasegresoid = $this->Proveedor->Client->Caja->Cajasegreso->find('first', ['conditions' => ['Cajasegreso.proveedorspago_id' => $xk], 'fields' => ['Cajasegreso.id', 'Cajasegreso.importe', 'Cajasegreso.caja_id']]);
            if (!empty($cajasegresoid)) {
                //$this->Proveedor->Client->Caja->setSaldo($cajasegresoid['Cajasegreso']['caja_id'], $cajasegresoid['Cajasegreso']['importe']);
                $this->Proveedor->Client->Caja->Cajasegreso->undo($cajasegresoid['Cajasegreso']['id']);
            }

            // elimino la transferencia (si existe) y actualizo el saldo
            $extraccion = $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->find('all', ['conditions' => ['Bancosextraccione.proveedorspago_id' => $xk], 'fields' => ['Bancosextraccione.id', 'Bancosextraccione.bancoscuenta_id', 'Bancosextraccione.importe']]);
            if (!empty($extraccion)) {
                foreach ($extraccion as $k => $v) {
                    $this->Proveedor->Client->Banco->Bancoscuenta->Bancosextraccione->undo($v['Bancosextraccione']['id']);
                }
            }

            // actualizo el saldo de cada factura
            $facturas = $this->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorspago_id' => $xk], 'fields' => ['Proveedorspagosfactura.proveedorsfactura_id', 'Proveedorspagosfactura.importe']]);
            if (!empty($facturas)) {
                foreach ($facturas as $v) {
                    $this->Proveedor->Proveedorsfactura->setSaldo($v['Proveedorspagosfactura']['proveedorsfactura_id'], $v['Proveedorspagosfactura']['importe']);
                }
            }

            //20180618 NO se anulan los cheques propios porq ahora se Seleccionan los q se van a utilizar en el pago, como los de terceros
            // anulo los cheques propios
            $chequespropios = $this->Proveedor->Client->Chequespropio->find('list', ['conditions' => ['Chequespropio.proveedorspago_id' => $xv, 'Chequespropio.anulado' => 0]]);
            if (!empty($chequespropios)) {
                foreach ($chequespropios as $k => $v) {
                    $this->Proveedor->Client->Chequespropio->undo($k);
                }
            }
            // anulo los cheques propios de administracion
            $chequespropiosadm = $this->Proveedor->Client->Chequespropiosadm->Chequespropiosadmsdetalle->find('list', ['conditions' => ['Chequespropiosadmsdetalle.proveedorspago_id' => $xv], 'fields' => ['id', 'chequespropiosadm_id']]);
            if (!empty($chequespropiosadm)) {
                foreach ($chequespropiosadm as $k => $v) {
                    $this->Proveedor->Client->Chequespropiosadm->undo($v);
                }
            }

            // anulo los pagos a cuenta (si hubiesen) (ya alcanza con q esté anulado el PP)
            //$this->Proveedorspagosacuenta->save(['proveedorspago_id' => $id, 'proveedorspagoaplicado_id' => 0, 'importe' => abs($total)]);
            // quito el proveedorspago_id de los cheques de terceros (y pongo el saldo = importe). Actualizo el saldo del banco
            $cheque = $this->Proveedor->Client->Cheque->find('all', ['conditions' => ['Cheque.proveedorspago_id' => $xk]]);
            if (!empty($cheque)) {
                foreach ($cheque as $k => $v) {
                    $this->Proveedor->Client->Cheque->id = $v['Cheque']['id'];
                    $this->Proveedor->Client->Cheque->saveField('proveedorspago_id', 0);
                }
            }

            // anulo los pagos a cuenta APLICADOS (si hubiesen)
            $this->Proveedorspagosacuenta->updateAll(['proveedorspagoaplicado_id' => 0], ['proveedorspagoaplicado_id' => $xk]);

            // pagó parte aplicando notas de creditos, le sumo otra vez el saldo a las NC
            $nc = $this->Proveedorspagosnc->find('all', ['conditions' => ['Proveedorspagosnc.proveedorspago_id' => $xk], 'fields' => ['Proveedorspagosnc.proveedorsfactura_id', 'Proveedorspagosnc.importe']]);
            if (!empty($nc)) {
                foreach ($nc as $k => $v) {
                    $this->Proveedor->Proveedorsfactura->setSaldo($v['Proveedorspagosnc']['proveedorsfactura_id'], $v['Proveedorspagosnc']['importe']);
                }
            }

            // anulo el pago
            $this->id = $xk;
            $this->saveField('concepto', '[ANULADO] ' . $this->field('concepto'));
            $this->saveField('anulado', 1);
            // actualizo el saldo del Proveedor (en amount tengo el monto abonado, no me importa si fue transferencia, caja o cheques)
            $this->Proveedor->setSaldo($this->field('proveedor_id'), $this->field('importe'));
        }

        return '';
    }

    /*
     * Para cada pago proveedor (con el mismo numero de pago), verifico si no fue aplicado un pago a cuenta con ese id
     */

    public function beforeUndo($ids) {
        /* $this->id = $id;

          // no necesito verificar si hubo pago proveedor en efectivo porq sería directamente actualizar el saldo de la caja y eliminar el egreso
          $montoefectivo = $this->Proveedor->Client->Caja->Cajasingreso->find('first', ['conditions' => ['Cajasengreso.proveedorspago_id' => $id], 'fields' => ['Cajasengreso.importe']]);
          //debug($montoefectivo);die;
          if (!empty($montoefectivo) && !$this->Proveedor->Client->Caja->hasSaldo($this->Proveedor->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']), $montoefectivo['Cajasingreso']['importe'])) {
          return __('La Caja asociada al usuario no tiene saldo suficiente para realizar el egreso');
          } */

        /* no necesito verificar si hubo pago proveedor x transferencia porq sería directamente actualizar el saldo del banco y eliminar la transferencia
         * // verifico (si hubo transferencia) que la cuenta bancaria tenga saldo para realizar la ELIMINACIÓN del movimiento
          $montotransferencia = $this->Proveedor->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->find('first', ['conditions' => ['Bancosdepositosefectivo.cobranza_id' => $id], 'fields' => ['Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.bancoscuenta_id']]);
          if (!empty($montotransferencia) && !$this->Proveedor->Client->Banco->Bancoscuenta->hasSaldo($montotransferencia['Bancosdepositosefectivo']['bancoscuenta_id'], $montotransferencia['Bancosdepositosefectivo']['importe'])) {
          return __('La cuenta bancaria asociada no tiene saldo suficiente para realizar la extracci&oacute;n');
          } */
        // verifico si hubo pago a cuenta, q no haya sido aplicado en otro pago proveedor posterior
        $cad = "";

        foreach ($ids as $k => $v) {
            $this->id = $k;
            if ($this->field('anulado') == 1) {
                $cad .= __('El Pago a Proveedor ya se encuentra anulado');
                return $cad;
            }
            $res = $this->Proveedorspagosacuenta->find('first', ['conditions' => ['proveedorspago_id' => $k, 'proveedorspagoaplicado_id !=' => 0], 'fields' => ['Proveedorspagosacuenta.id', 'Proveedorspagosacuenta.proveedorspagoaplicado_id']]);
            if (!empty($res)) {
                $cad .= __('Existe un pago a cuenta aplicado en el Pago Proveedor #') . "<a target='_blank' href='" . $this->webroot . "proveedorspagos/view/" . $res['Proveedorspagosacuenta']['proveedorspagoaplicado_id'] . "'>" . $res['Proveedorspagosacuenta']['proveedorspagoaplicado_id'] . "</a>. No se puede eliminar<br>";
            }
        }
        return $cad;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Proveedorspago.numero' => $data['buscar'],
                'Proveedorspago.concepto like' => '%' . $data['buscar'] . '%',
            ]
        ];
    }

}
