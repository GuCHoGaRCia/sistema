<?php

App::uses('AppModel', 'Model');

class Cobranza extends AppModel {
    /*
     * Tipos movimientos:
     *  1 - Cajasingreso
     *  2 - Cajasegreso
     *  3 - Bancosdepositosefectivo
     *  4 - Bancosextraccione
     *  5 - Bancosdepositoscheque
     *  6 - Bancostransferencia
     *  7 - Chequespropio
     *  8 - Proveedorspago
     *  9 - Cobranza
     * 10 - Proveedorsfactura
     * 11 - ChequespropiosADM
     */

    public $virtualFields = ['tipo' => 9, 'fecha2' => 'concat(Cobranza.fecha," ",DATE_FORMAT(Cobranza.created, "%H:%i:%s"))'];
    public $validate = array(
        'propietario_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'amount' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
            'range' => array(
                'rule' => array('range', 0, 999999),
                'message' => 'Debe ser un numero decimal mayor a cero',
            ),
        ),
        'amount' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
            'range' => array(
                'rule' => array('range', 0, 999999),
                'message' => 'Debe ser un numero decimal mayor a cero',
            ),
        ),
    );
    public $belongsTo = array(
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
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
    public $hasMany = [
        'Cobranzatipoliquidacione' => [
            'className' => 'Cobranzatipoliquidacione',
            'foreignKey' => 'cobranza_id',
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
        'Cobranzacheque' => [
            'className' => 'Cobranzacheque',
            'foreignKey' => 'cobranza_id',
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
        'Cajasingreso' => [
            'className' => 'Cajasingreso',
            'foreignKey' => 'cobranza_id',
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
        'Bancosdepositosefectivo' => [
            'className' => 'Bancosdepositosefectivo',
            'foreignKey' => 'cobranza_id',
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
        'GastosParticularesPft' => [
            'className' => 'GastosParticularesPft',
            'foreignKey' => 'cobranza_id',
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
    public $hasOne = [
        'Pagoselectronico' => [
            'className' => 'Pagoselectronico',
            'foreignKey' => 'cobranza_id',
            'dependent' => false,
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
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cobranza.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    /*
     * Funcion que devuelve la cobranza de propietarios de una liquidacion anterior a la actual segun su tipo.
     * Si no existe una liquidacion anterior, devuelve [], sino devuelve propietario_id, monto
     * IMPORTANTE: Devuelve las cobranzas con fecha de creacion entre el cierre de la liquidacion anterior y now(). 
     * Si esta bloqueada, devuelve las cobranzas entre el cierre de la liquidacion anterior y el cierre de la actual
     */

    public function getCobranzas($liquidation_id, $propietario_id = null) {
        $liquidation_type = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationsTypeId($liquidation_id);
        $consorcio_id = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getConsorcioId($liquidation_id);
        // obtengo las cobranzas de la liquidacion anterior (puede ser q sean cobranzas de la liquidacion inicial)
        $anterior = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLastLiquidation($liquidation_id);
        $closed = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationClosedDate($anterior);

        $created = ['Cobranza.created <' => date('Y-m-d H:i:s')];
        $liq = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->find('first', ['conditions' => ['Liquidation.id' => $liquidation_id, 'Liquidation.bloqueada' => 1], 'fields' => 'Liquidation.closed']);
        if (!empty($liq) && !is_null($liq['Liquidation']['closed']) && $liq['Liquidation']['closed'] !== "0000-00-00 00:00:00") {
            $created = ['Cobranza.created <' => $liq['Liquidation']['closed']];
        }

        $condiciones = ['Cobranzatipoliquidacione.liquidations_type_id' => $liquidation_type, 'Propietario.consorcio_id' => $consorcio_id, /* 'Liquidation.bloqueada' => 1, */ 'Cobranza.created >=' => $closed, $created, 'Cobranza.anulada' => 0];
        if (!is_null($propietario_id)) {
            $condiciones['Cobranza.propietario_id'] = $propietario_id;
        }
        $options = ['conditions' => $condiciones, 'recursive' => -1/* , 'order' => array('Liquidation.created DESC') */,
            'fields' => ['Cobranza.propietario_id', 'Cobranzatipoliquidacione.amount', 'Cobranzatipoliquidacione.liquidations_type_id', 'Cobranzatipoliquidacione.solocapital', 'Cobranza.fecha', 'Cobranza.numero', 'Cobranza.id'],
            'joins' => [['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'right', 'conditions' => ['Cobranzatipoliquidacione.cobranza_id=Cobranza.id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'right', 'conditions' => ['Cobranza.propietario_id=Propietario.id']]]
        ];
        $resul = $this->find('all', $options);
        if (count($resul) == 0) {
            // no encontró ninguna liquidacion anterior, busco las cobranzas de la liquidacion INICIAL
            $condiciones['Liquidation.liquidations_type_id'] = $liquidation_type;
            $condiciones['Liquidation.bloqueada'] = 1;
            return $this->getCobranzasIniciales($condiciones);
        }
        // no puedo hacer Hash::combine porq puede haber varias cobranzas de un mismo propietario_id, entonces me deja un solo key
        return $resul;
    }

    /*
     * Obtiene las cobranzas en un rango de fechas. Utilizado para generar asientos automáticos (contabilidad).
     * Tambien, desde cola de impresiones, verifico al desbloquear que no tenga cobranzas realizadas luego de cerrar la liquidacion
     */

    public function getCobranzasFecha($consorcio_id, $desde, $hasta, $lt = null) {
        $options = ['conditions' => ['Propietario.consorcio_id' => $consorcio_id, 'Cobranza.created >=' => $desde . " 00:00:00", 'Cobranza.created <=' => $hasta . " 23:59:59", 'Cobranza.anulada' => 0] + (!empty($lt) ? ['Cobranzatipoliquidacione.liquidations_type_id' => $lt] : []),
            'fields' => ['sum(Cobranzatipoliquidacione.amount) as total'],
            'joins' => [['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'right', 'conditions' => ['Cobranzatipoliquidacione.cobranza_id=Cobranza.id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'right', 'conditions' => ['Cobranza.propietario_id=Propietario.id']]]
        ];
        $resul = $this->find('all', $options);
        return (!empty($resul[0][0]['total']) ? $resul[0][0]['total'] : 0);
    }

    /*
     * Funcion que devuelve el total de las cobranzas por tipo de liquidación
     */

    public function getTotalCobranzasPorTipodeLiquidacion($liquidation_id, $propietario_id = null) {
        $total = [];
        $liquidation_types = $this->Cobranzatipoliquidacione->LiquidationsType->getLiquidationsTypes();
        foreach ($liquidation_types as $k => $v) {
            $total[$k] = 0;
        }
        $cobranzas = $this->getCobranzas($liquidation_id, $propietario_id);
        foreach ($cobranzas as $k => $v) {
            $total[$v['Cobranzatipoliquidacione']['liquidations_type_id']] += $v['Cobranzatipoliquidacione']['amount'];
        }
        return $total;
    }

    /*
     * Devuelve la suma total de cobranzas realizadas en una liquidacion (tomando en cuenta su tipo de liquidacion)
     */

    public function totalCobranzas($liquidation_id, $propietario_id = null) {
        $total = 0;
        $cobranzas = $this->getCobranzas($liquidation_id, isset($propietario_id) ? $propietario_id : null);
        if (!empty($cobranzas)) {
            foreach ($cobranzas as $k => $v) {
                $total += $v['Cobranzatipoliquidacione']['amount'];
            }
        }
        return $total;
    }

    /*
     * Obtengo el detalle de una cobranza (ANULADA O NO) para mostrar en cobranzas/view/x
     */

    public function getDetalleCobranza($cobranza_id) {
        $options = array('conditions' => array('Cobranza.id' => $cobranza_id), 'fields' => ['Consorcio.*', 'Propietario.*', 'Client.*', 'Cobranza.*', 'Cajasingreso.*', 'Bancosdepositosefectivo.*', 'Bancoscuenta.name', 'Caja.name'],
            'contain' => ['Cobranzatipoliquidacione'],
            'joins' => [
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=Cobranza.propietario_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']],
                ['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'left', 'conditions' => ['Cobranza.id=Cobranzatipoliquidacione.cobranza_id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']],
                //['table' => 'cobranzacheques', 'alias' => 'Cobranzacheque', 'type' => 'right', 'conditions' => ['Cobranza.id=Cobranzacheque.cobranza_id']],
                ['table' => 'cajasingresos', 'alias' => 'Cajasingreso', 'type' => 'left', 'conditions' => ['Cobranza.id=Cajasingreso.cobranza_id']],
                ['table' => 'cajas', 'alias' => 'Caja', 'type' => 'left', 'conditions' => ['Caja.id=Cajasingreso.caja_id']],
                ['table' => 'bancosdepositosefectivos', 'alias' => 'Bancosdepositosefectivo', 'type' => 'left', 'conditions' => ['Cobranza.id=Bancosdepositosefectivo.cobranza_id']],
                ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.id=Bancosdepositosefectivo.bancoscuenta_id']]]);
        return $this->find('first', $options);
    }

    /*
     * Permite obtener el total de cobranzas por forma de pago (efectivo, transferencia, cobranza automática, interdeposito, cheque)
     */

    public function getTotalPorFormadePago($liquidation_id) {
        $formasdepago = $this->User->Client->Formasdepago->get(true);
        $tipos = ['Efectivo' => 0, 'Cheque de Terceros' => 0]; // dejarlo, porq si el consorcio no tiene cuenta bancaria da error mas abajo (no está inicializado efectivo/cheque)
        $consorcio = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getConsorcioId($liquidation_id);
        $cliente = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->Consorcio->getConsorcioClientId($consorcio);
        $cuentas = $this->Propietario->Consorcio->Bancoscuenta->getCuentasBancarias($consorcio, $cliente);
        foreach ($cuentas as $c => $c1) {
            foreach ($formasdepago as $k => $v) {
                if ($v['destino'] == 2) {//es banco
                    $tipos[$c][$v['forma']] = 0;
                } else {// caja
                    $tipos[$v['forma']] = 0;
                }
            }
        }
        $lt = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationsTypeId($liquidation_id);
        $cobranzas = $this->getCobranzas($liquidation_id);
        //$tipos = ['Efectivo' => 0, 'Transferencia' => 0, 'Cobranza Automática' => 0, 'Interdepósito' => 0, 'Cheque' => 0];
        foreach ($cobranzas as $v) {
            $detalle = $this->getDetalleCobranza($v['Cobranza']['id']);
            $porc = [];
            foreach ($detalle['Cobranzatipoliquidacione'] as $d1) {
                //cobranzas liquidacion con detalle forma de pago proporcional al total de ordinaria y extraordinaria
                $porc[$d1['liquidations_type_id']] = 100 * $d1['amount'] / $detalle['Cobranza']['amount'];
            }
            //$t = $i = $e = $c = 0;
            if (!empty($detalle['Cajasingreso']['importe'])) {
                $tipos['Efectivo'] += $detalle['Cajasingreso']['importe'] * $porc[$lt] / 100;
            }
            if (!empty($detalle['Cajasingreso']['cheque'])) {
                $tipos['Cheque de Terceros'] += $detalle['Cajasingreso']['cheque'] * $porc[$lt] / 100;
            }
            if (substr($detalle['Cobranza']['recibimosde'], 0, 3) === 'CA ' && !empty($detalle['Bancosdepositosefectivo']['bancoscuenta_id'])) {      // va CA con el espacio para saber que es Cobranza Automática, y no otra palabra como por ej. CASA
                $tipos[$detalle['Bancosdepositosefectivo']['bancoscuenta_id']]['Cobranza Automática'] += $detalle['Cobranza']['amount'];
            } else {
                $index = '';
                if ($detalle['Bancosdepositosefectivo']['formasdepago_id'] != 0) {
                    $index = $formasdepago[$detalle['Bancosdepositosefectivo']['formasdepago_id']]['forma'];
                } else {
                    $index = ($detalle['Bancosdepositosefectivo']['es_transferencia'] == 1 ? 'Transferencia' : 'Interdepósito');
                }

                foreach ($detalle['Cobranzatipoliquidacione'] as $d) {
                    if ($d['liquidations_type_id'] == $lt && !empty($detalle['Bancosdepositosefectivo']['bancoscuenta_id'])) {
                        $tipos[$detalle['Bancosdepositosefectivo']['bancoscuenta_id']][$index] += $detalle['Bancosdepositosefectivo']['importe'] * $porc[$lt] / 100;
                    }
                }
            }
            /*
              if (!empty($detalle['Bancosdepositosefectivo']['importe'])) {
              foreach ($detalle['Cobranzatipoliquidacione'] as $d) {
              $valor = $detalle['Bancosdepositosefectivo']['importe'] * $porc[$lt] / 100;
              if ($d['liquidations_type_id'] == $lt) {
              //if (substr($detalle['Bancosdepositosefectivo']['concepto'], 0, 3) === 'CA ') {      // va CA con el espacio para saber que es Cobranza Automática, y no otra palabra como por ej. CASA
              //    $tipos['Cobranza Automática'] += $valor;
              //} else {
              $tipos['Transferencia'] += $valor;
              //}
              }
              }
              }
              }
              if (!empty($detalle['Bancosdepositosefectivo']['importe']) && $detalle['Bancosdepositosefectivo']['es_transferencia'] == 0) {
              foreach ($detalle['Cobranzatipoliquidacione'] as $d) {
              if ($d['liquidations_type_id'] == $lt) {
              $tipos['Interdepósito'] += $detalle['Bancosdepositosefectivo']['importe'] * $porc[$lt] / 100;
              }
              }
              } */
            //$tipos['Efectivo'] += $e;
            //$tipos['Cheque'] += $c;
            //$tipos['Transferencia'] += $t;
            //$tipos['Interdepósito'] += $i;
        }
        return $tipos;
    }

    /*
     * En la cobranza manual, se utiliza para mostrar la cuenta corriente propietario (se llama desde saldosCierres::getSaldosPropietario())
     */

    public function getCobranzasPropietario($propietario_id = null) {
        $options = array('conditions' => ['Cobranza.propietario_id' => $propietario_id, 'Cobranza.anulada' => 0],
            'fields' => array('Cobranza.id', 'Cobranza.fecha', 'Cobranza.created', 'Cobranza.concepto', 'Cobranzatipoliquidacione.liquidations_type_id', 'Cobranzatipoliquidacione.amount'),
            'order' => 'Cobranza.fecha',
            'joins' => [['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'right', 'conditions' => ['Cobranza.id=Cobranzatipoliquidacione.cobranza_id']]]);
        return $this->find('all', $options);
    }

    /*
     * Obtengo las cobranzas del consorcio para todos los tipos de liquidaciones para liquidaciones q no hayan sido cerradas
     */

    public function getCobranzasPeriodo($consorcio_id) {
        $liquidation_types = $this->Cobranzatipoliquidacione->LiquidationsType->getLiquidationsTypes();
        // obtengo las cobranzas de la liquidacion anterior (puede ser q sean cobranzas de la liquidacion inicial)
        $resultado = [];
        foreach ($liquidation_types as $k => $v) {
            $anterior = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getUltimaLiquidacion($consorcio_id, $k);
            $closed = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationClosedDate($anterior);
            $condiciones = ['Cobranzatipoliquidacione.liquidations_type_id' => $k, /* 'Liquidation.bloqueada' => 1, */ 'Liquidation.consorcio_id' => $consorcio_id, 'Propietario.consorcio_id' => $consorcio_id, 'Liquidation.inicial' => 0, 'Cobranza.created >=' => $closed, 'Cobranza.created <' => date('Y-m-d H:i:s'), 'Cobranza.anulada' => 0];
            $options = ['conditions' => $condiciones, 'recursive' => -1/* , 'order' => array('Liquidation.created DESC') */,
                'fields' => ['Cobranza.propietario_id', 'Cobranza.id', 'Cobranza.numero', 'Cobranzatipoliquidacione.amount', 'Cobranzatipoliquidacione.solocapital', 'Cobranza.fecha', 'Propietario.name', 'Propietario.unidad', 'Propietario.code'],
                'joins' => [['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'left', 'conditions' => ['Cobranzatipoliquidacione.cobranza_id=Cobranza.id']],
                    ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=Cobranzatipoliquidacione.liquidations_type_id']],
                    ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Cobranza.propietario_id=Propietario.id']]],
                'group' => 'Cobranza.id', // si no pongo esto, me cuatriplica las cobranzas (??)
                'order' => 'Propietario.orden'
            ];
            $resul = $this->find('all', $options);
            if (count($resul) == 0 && !$this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->hasLiquidationsBloqueadas($consorcio_id, $k)) {
                // no encontró ninguna liquidacion anterior, busco las cobranzas de la liquidacion INICIAL
                $condiciones['Liquidation.liquidations_type_id'] = $k;
                $resultado[$k] = $this->getCobranzasIniciales($condiciones);
            } else {
                $resultado[$k] = $resul;
            }
        }

        // no puedo hacer Hash::combine porq puede haber varias cobranzas de un mismo propietario_id, entonces me deja un solo key
        return $resultado;
    }

    // COBRANZAS X TRANSFERENCIA, por CONSORCIO Y DESDE HASTA!
    // CON EL CONSOR OBTENGO BANCOSCUENTA_ID
    /*
     * Obtengo el numero de recibo para la cobranza (único por cliente)
     */

    public function beforeSave($options = []) {
        $max = $this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['max(Cobranza.numero) as numero'],
            'joins' => [['table' => 'users', 'alias' => 'User', 'type' => 'right', 'conditions' => ['Cobranza.user_id=User.id']]]]);
        $this->data['Cobranza']['numero'] = $max[0]['numero'] + 1;

        return true;
    }

    public function get($propietario_id = null) {
        return $this->LiquidationsType->Liquidation->SaldosCierre->getSaldosPropietario($propietario_id);
    }

    /*
     * Devuelve las cobranzas de la liquidacion inicial
     */

    public function getCobranzasIniciales($condiciones) {
        unset($condiciones['Cobranza.liquidation_id <']); // saco la condicion q sea el id<
        unset($condiciones['Propietario.consorcio_id']); // saco la condicion q sea el id<
        $condiciones['Liquidation.inicial'] = 1;
        $options = array('conditions' => $condiciones,
            'joins' => [['table' => 'cobranzatipoliquidaciones', 'alias' => 'Cobranzatipoliquidacione', 'type' => 'right', 'conditions' => ['Cobranza.id=Cobranzatipoliquidacione.cobranza_id']],
                ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'right', 'conditions' => ['Liquidation.liquidations_type_id=Cobranzatipoliquidacione.liquidations_type_id']]],
            'fields' => array('Cobranza.propietario_id', 'Cobranzatipoliquidacione.amount', 'Cobranza.fecha'));
        return $this->find('all', $options);
    }

    /*
     * Guardo las cobranzas automáticas
     * cid#pid#bid#importe  
     * ConsorcioCode#PropietarioCode#BancoscuentaId#pagoelectronicoid#saldo (Los id son los internos)
     *   $pago[0]#       $pago[1]       $pago[2]         $pago[3]     $pago[4]
     */

    //array(
    //	'Cobranza' => array(
    //		(int) 0 => array(
    //			'data' => '11#20#20#214#981'
    //		),
    //		(int) 1 => array(
    //              	'gp' => 'on',
    //			'data' => '25#3#16#756#0'
    //		),
    //		(int) 2 => array(
    //			'data' => '8#42#18#213#617'
    //		),.....
    public function guardar($data) {
        $r = ['e' => 0, 'd' => '', 'c' => 0]; //c cantidad cobranzas guardadas correctamente
        $fdp = $data['Cobranza']['formadepago'];
        unset($data['Cobranza']['formadepago']);
        foreach ($data['Cobranza'] as $v) {
            $pago = explode('#', $v['data']);
            if (count($pago) == 5) {
                $datosdelpago = $this->Propietario->Pagoselectronico->find('first', ['conditions' => ['Pagoselectronico.id' => $pago[3], 'Pagoselectronico.client_code' => $_SESSION['Auth']['User']['Client']['code']],
                    'fields' => ['Pagoselectronico.prefijo', 'Pagoselectronico.id', 'Pagoselectronico.fecha_proc', 'Pagoselectronico.medio', 'Pagoselectronico.importe', 'Pagoselectronico.comision', 'Pagoselectronico.cobranza_id']]);
                if (empty($datosdelpago)) {
                    $r = ['e' => 1, 'd' => 'El Pago Electronico es inexistente'];
                    break;
                }
                if ($datosdelpago['Pagoselectronico']['cobranza_id'] != 0) {// si en 2 pestañas abren cobranzas/add (cobranzas automaticas) y en las 2 guardan, ahora chequea q cobranza_id sea ==0 (entonces la carga, sino tira error)
                    $r = ['e' => 1, 'd' => 'El Pago ya fue guardado anteriormente. Por favor, actualice la p&aacute;gina'];
                    break;
                }
                $datosdelpago['Pagoselectronico']['importe'] = (isset($v['gp'])) ? ($datosdelpago['Pagoselectronico']['importe']) : $pago[4];
                $x = ClassRegistry::init('Plataformasdepago');
                $plataforma = $x->getConfig($_SESSION['Auth']['User']['client_id'])['Plataformasdepagosconfig'];

                // 27/05/2021 si tilda Guarda pago y la adm está configurada con comision, se la resto, porq sino guarda doble comision
                // al haber modificado el view, cuando tiene comision cero se la sumo porq sino si debe 1000, y pagó 900, va a quedar como deudor y en realidad pagó 900+comision
                // entonces pagó todo lo q debía
                if (isset($v['gp']) && $plataforma['plataformasdepago_id'] != 0 && $plataforma['comision'] > 0) {
                    $datosdelpago['Pagoselectronico']['importe'] -= $datosdelpago['Pagoselectronico']['comision'];
                }

                if ($datosdelpago['Pagoselectronico']['importe'] == 0) {
                    return ['e' => 1, 'd' => 'El saldo de uno o m&aacute;s Propietarios es cero y no se tild&oacute; "Guarda Pago". Por favor, intente nuevamente'];
                }
                $datosdelpago['Pagoselectronico']['formadepago'] = $fdp;
                $resul = $this->procesaCobranzaAutomatica($datosdelpago['Pagoselectronico'], $pago);
                if (!empty($resul)) {
                    $r['e'] = 1;
                    $r['d'] .= $resul;
                } else {
                    $r['c'] += 1;
                }
            }
        }
        return $r;
    }

    /*
     * Guardo la cobranza y su respectivo bancosdepositosefectivos (caja=0, user_id=actual, bancoscuenta_id=ladelconsorcioactual)
     * $otrosdatos = ConsorcioId#PropietarioId#BancoscuentaId#pagoelectronicoid (Los id son los internos)
     */

    public function procesaCobranzaAutomatica($datos, $otrosdatos) {
        if (!empty($datos)) {
            // si el Consorcio no tiene liquidacion en curso (del tipo ordinaria, extra o la asociada al pago recibido), entonces no guardo el pago y lo dejo pendiente
            $consor = $this->Propietario->Consorcio->getConsorcioId($_SESSION['Auth']['User']['client_id'], $otrosdatos[0]);
            if (!$this->Propietario->Consorcio->canEdit($consor)) {
                return "El Consorcio es inexistente<br>";
            }
            if (!$this->Propietario->Consorcio->Client->Banco->Bancoscuenta->canEdit($otrosdatos[2])) {
                return "La Cuenta bancaria es inexistente<br>";
            }

            $lt = $this->Propietario->Consorcio->Liquidation->LiquidationsType->getLiquidationsTypeIdFromPrefijo($datos['prefijo']);
            $liq = $this->Propietario->Consorcio->Liquidation->getLiquidacionesAbiertas($consor, $lt);
            $this->Propietario->Consorcio->id = $consor;
            if (empty($liq)) {
                return "El Consorcio " . h($this->Propietario->Consorcio->field('name')) . " no posee una Liquidaci&oacute;n abierta, cree una y vuelva a Guardar las Cobranzas Autom&aacute;ticas<br>";
            }
            $cuentaGP = $this->Propietario->Consorcio->field('cuentagpcomisionplataforma');
            $prop = $this->Propietario->getPropietarioId($consor, $otrosdatos[1]);
            if (!$this->Propietario->canEdit($prop)) {
                return "El Propietario es inexistente<br>";
            }
            $this->Propietario->id = $prop;
            $concepto = 'CA ' . $this->Propietario->Consorcio->field('name') . " - " . $this->Propietario->field('name') . " (" . $this->Propietario->field('unidad') . ")";

            $importe = $datos['importe'];

            //chequeo q los campos sean correctos
            $this->set(['propietario_id' => $prop, 'fecha' => $datos['fecha_proc'], 'amount' => $importe, 'concepto' => '', 'recibimosde' => $concepto, 'anulada' => 0,
                'user_id' => $_SESSION['Auth']['User']['id']]);
            if (!$this->validates()) {
                return 'No se pudo guardar la Cobranza, por favor intente nuevamente<br>';
            }
            // guardo la cobranza
            $this->create();
            $this->save(['propietario_id' => $prop, 'fecha' => $datos['fecha_proc'], 'amount' => $importe, 'concepto' => '', 'recibimosde' => $concepto, 'anulada' => 0,
                'user_id' => $_SESSION['Auth']['User']['id']]);
            $cobranzaid = $this->id;

            // guardo el detalle de la cobranza segun el prefijo (tipo de liq)
            $this->Cobranzatipoliquidacione->create();
            $this->Cobranzatipoliquidacione->save(['cobranza_id' => $cobranzaid, 'liquidations_type_id' => $lt, 'amount' => $importe, 'solocapital' => 0]);

            // pongo como "cargado" al pago electronico
            $this->Propietario->Pagoselectronico->id = $datos['id'];
            $this->Propietario->Pagoselectronico->saveField('cobranza_id', $cobranzaid);

            // 2017-04-26 cambio la fecha de deposito a 3 dias hábiles posteriores a la fecha de proceso. Para 3 dias habiles anteriores, el array $cant va al reves (4555333)
            // D  = dia 0 = sumar 3 dias (miercoles)
            // L  = dia 1 = sumar 3 dias (jueves)
            // M  = dia 2 = sumar 3 dias (viernes)
            // Mi = dia 3 = sumar 5 dias (lunes)
            // J  = dia 4 = sumar 5 dias (martes)
            // V  = dia 5 = sumar 5 dias (miercoles)
            // S  = dia 6 = sumar 4 dias (miercoles)
            $cant = [3, 3, 3, 5, 5, 5, 4];
            $datos['fecha_proc'] = date("Y-m-d", strtotime($datos['fecha_proc'] . " +" . $cant[date("w", strtotime($datos['fecha_proc']))] . " days"));

            // si la fecha ya pasó, creo el deposito en el banco (porq ya está acreditada). Ej: los que cargan las cobranzas todas juntas a fin de mes (degano, etc)
            if (strtotime(date("Y-m-d")) >= strtotime($datos['fecha_proc'])) {
                /* Este choclo evitaba q se guarde la comision de PLAPSA en el banco (para los q cobran comision x GP o GG)
                 * Al final no se hace este cambio, en chavez y no se quien guardan en el banco la cobranza completa y despues hacen un movimiento descontando la comision de PLAPSA
                  $x = ClassRegistry::init('Plataformasdepago');
                  $plataforma = $x->getConfig($_SESSION['Auth']['User']['client_id'])['Plataformasdepagosconfig'];
                  $comision = -1;
                  if ($plataforma['plataformasdepago_id'] != 0 && $plataforma['comision'] == 0) {
                  $comision = 0;
                  } */

                $d = ['cobranza_id' => $cobranzaid, 'bancoscuenta_id' => $otrosdatos[2], 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $datos['fecha_proc'], 'es_transferencia' => 1,
                    'concepto' => $concepto, 'importe' => $importe /* - ($comision == 0 ? $datos['comision'] : 0) */, 'formasdepago_id' => $datos['formadepago']];
                // agrego a la cuenta bancaria el importe de transferencia > 0
                $this->Propietario->Consorcio->Client->Caja->Bancosdepositosefectivo->crear($d);
            }

            // creo el Gasto particular con el importe de comision cobrado x la plataforma

            if (!empty($cuentaGP)) {
                reset($liq);
                $first_key = key($liq);
                $x = ['liquidation_id' => $first_key, 'propietario_id' => $this->Propietario->id, 'cuentasgastosparticulare_id' => $cuentaGP,
                    'date' => $datos['fecha_proc'], 'amount' => $datos['comision'], 'description' => __('Comisión Plataforma ') . " " . $concepto, 'heredable' => 0];
                $this->Propietario->GastosParticulare->crear($x, $cobranzaid);
            }
        }
        return '';
    }

    //array(
    //    'Cobranza' => array(
    //        'recibimosde' => 'CM Consorcio Uno - Maria Juana Perez 4 - 2ºA (2)',
    //        'propietario_id' => '943',
    //        'concepto' => '',
    //        'fecha' => array(
    //            'day' => '04',
    //            'month' => '11',
    //            'year' => '2020'
    //        ),
    //        'lt_44' => '0',
    //        'chk_44' => '0',
    //        'lt_45' => '55',
    //        'chk_45' => '0',
    //        'lt_46' => '0',
    //        'chk_46' => '0',
    //        'amount' => '55.00',
    //        'efectivo' => '44',
    //        'cheque' => '0.00',
    //        'transferencia' => '11.00',
    //        'formadepago' => '954',
    //        'bancoscuenta' => '778'
    //    )
    //)

    public function procesaCobranzaManual($data) {
        //debug($data);die;
        $resul = $this->_controlarCobranza($data);
        if ($resul !== "") {
            return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
        }
        $consor = $this->Propietario->getPropietarioConsorcio($data['Cobranza']['propietario_id']);
        $this->create();
        $resul = $this->save(['consorcio_id' => $consor, 'propietario_id' => $data['Cobranza']['propietario_id'], 'fecha' => $data['Cobranza']['fecha'], 'concepto' => $data['Cobranza']['concepto'],
            'recibimosde' => $data['Cobranza']['recibimosde'], 'amount' => $data['Cobranza']['amount'], 'user_id' => $_SESSION['Auth']['User']['id']]);
        $id = $resul['Cobranza']['id'];
        $total = 0;

        $concepto = empty($data['Cobranza']['concepto']) ? $data['Cobranza']['recibimosde'] : $data['Cobranza']['recibimosde'] . " " . $data['Cobranza']['concepto'];

        // para cada tipo de liq guardo cuanto se cobra
        foreach ($data['Cobranza'] as $k => $v) {
            if (substr($k, 0, 3) == 'lt_' && $v > 0) {
                $ltid = (int) substr($k, 3);
                $check = (int) $data['Cobranza']['chk_' . $ltid];
                if ($ltid > 0) {
                    // el tipo de liquidacion actual tiene id > 0 y el importe $v > 0
                    $total += $v;
                    $this->Cobranzatipoliquidacione->create();
                    $this->Cobranzatipoliquidacione->save(['cobranza_id' => $id, 'liquidations_type_id' => $ltid, 'amount' => $v, 'solocapital' => $check]);

                    $fdp = $this->User->Client->Formasdepago->get(true);
                    $formadepago = isset($data['Cobranza']['formadepago']) ? ($fdp[$data['Cobranza']['formadepago']]['forma'] ?? '') : '';

                    $importe = $v - ($data['Cobranza']['efectivo'] ?? 0) - ($data['Cobranza']['cheque'] ?? 0);
                    if ($formadepago === 'Transferencia' && $importe > 0) {
                        // creo el gasto particular en caso q tenga configurado comision_variable en la cuenta bancaria
                        $com = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->getComisionVariable($data['Cobranza']['bancoscuenta_id']);
                        $cuenta = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->getCGPComisionCobranza($data['Cobranza']['bancoscuenta_id']);
                        $liq = $this->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consor, $ltid);
                        if ($com > 0 && $cuenta != 0 && !empty($liq)) {
                            $x = ['liquidation_id' => $liq, 'propietario_id' => $data['Cobranza']['propietario_id'], 'cuentasgastosparticulare_id' => $cuenta,
                                'date' => $data['Cobranza']['fecha'], 'amount' => $importe * ($com / 100), 'description' => __('Comisión') . " Transferencia " . $concepto, 'heredable' => 0];
                            $this->Propietario->GastosParticulare->crear($x, $id);
                        }
                    }
                    // en periodo o manual
                    if ($formadepago === 'Interdepósito' && $importe > 0) {
                        // creo el gasto particular en caso q tenga configurado comision_variable en la cuenta bancaria
                        $com = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->getComisionVariable($data['Cobranza']['bancoscuenta_id']);
                        $com2 = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->getComisionFijaInterdeposito($data['Cobranza']['bancoscuenta_id']);
                        $cuenta = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->getCGPComisionCobranza($data['Cobranza']['bancoscuenta_id']);
                        $liq = $this->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consor, $ltid);
                        if ($com2 > 0 && $cuenta != 0 && !empty($liq)) {
                            $x = ['liquidation_id' => $liq, 'propietario_id' => $data['Cobranza']['propietario_id'], 'cuentasgastosparticulare_id' => $cuenta, 'date' => $data['Cobranza']['fecha'],
                                'amount' => $importe * ($com / 100) + $com2, 'description' => __('Comisión Interdepósito') . " " . $concepto, 'heredable' => 0];
                            $this->Propietario->GastosParticulare->crear($x, $id);
                        }
                    }
                }
            }
        }
        $caja_id = $this->Propietario->Consorcio->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if (!empty($data['Cobranza']['bancoscuenta_id']) && isset($data['Cobranza']['transferencia']) && $data['Cobranza']['transferencia'] > 0) {
            // en cobranzas manuales x transferencia entra acá! agrego a la cuenta bancaria el importe de transferencia > 0
            $d = ['cobranza_id' => $id, 'bancoscuenta_id' => $data['Cobranza']['bancoscuenta_id'], 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'],
                'fecha' => $data['Cobranza']['fecha'], 'concepto' => $concepto, 'importe' => $data['Cobranza']['transferencia'], 'es_transferencia' => 1, 'formasdepago_id' => $data['Cobranza']['formadepago']];
            $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->crear($d);
        }
        if (!empty($data['Cobranza']['bancoscuenta_id']) && isset($data['Cobranza']['interdeposito']) && $data['Cobranza']['interdeposito'] > 0) {
            // agrego a la cuenta bancaria el importe de interdeposito > 0
            $d = ['cobranza_id' => $id, 'bancoscuenta_id' => $data['Cobranza']['bancoscuenta_id'], 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'],
                'fecha' => $data['Cobranza']['fecha'], 'concepto' => $concepto, 'importe' => $data['Cobranza']['interdeposito'], 'es_transferencia' => 0, 'formasdepago_id' => 0];
            $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->crear($d);
        }
        $totalcheque = 0; // en el importe del ingreso se suma efectivo y cheque
        if (isset($data['Cobranza']['cheque']) && (float) $data['Cobranza']['cheque'] > 0) {
            // guardo cada cheque utilizado en la cobranza
            foreach ($data['Cobranza'] as $k => $v) {
                if (substr($k, 0, 5) == 'lcht_') {
                    $chid = (int) substr($k, 5);
                    $this->Cobranzacheque->create();
                    $this->Cobranzacheque->save(['cobranza_id' => $id, 'cheque_id' => $chid, 'amount' => $v]);
                    $this->Propietario->Consorcio->Client->Cheque->setSaldo($chid, -$v); // actualizo el saldo del cheque
                    $totalcheque += $v;
                }
            }
        }
        if ($data['Cobranza']['efectivo'] > 0 || $totalcheque > 0) {
            // agrego a la caja del usuario actual el total de la cobranza que haya sido en efectivo > 0
            $this->Propietario->Consorcio->Client->Caja->Cajasingreso->crear(['consorcio_id' => $consor, 'caja_id' => $caja_id, 'fecha' => $data['Cobranza']['fecha'], 'concepto' => $concepto, 'importe' => $data['Cobranza']['efectivo'], 'cheque' => $totalcheque, 'user_id' => $_SESSION['Auth']['User']['id'], 'cobranza_id' => $id]);
        }
        return ['e' => 0];
    }

    /*
     *  Procesa la cobranza por Periodo (cada una se procesa como cobranza manual). No se reciben las cobranzas vacias o cero
      //array(
      //	'Cobranza' => array(
      //		'f_2004_122' => '13/09/2016',
      //		'c_2004_122' => '5', // este Propietario pagó 16$ en 2 tipos de liquidaciones (122 y 123)
      //                en el Tipo 122 puso fecha (13/09/2016), en el Tipo 123 no hay (por defecto fecha actual)
      //		'c_2004_123' => '11', // no tiene fecha asociada (por defecto pongo el dia de hoy)
      //		'consorcio_id' => '53',
      //		'bancoscuenta_id' => '13', // cuenta bancaria
      //		'fdp' => 'T' //forma de pago (Efectivo, Transferencia)
      //	)
      //	'Cobranza' => array(
      //		'c_909_17' => '1490',
      //		'c_912_17' => '200',// este Propietario pagó 500$ en 2 tipos de liquidaciones (17 y 18)
      //		'c_912_18' => '300',
      //		'c_931_17' => '825',
      //		'c_932_17' => '940',
      //		'bancoscuenta_id' => '16', // cuenta bancaria
      //		'fdp' => 'E' // forma de pago (Efectivo, Transferencia)
      //	)
      //)
     */

    public function procesaCobranzaPeriodo($data) {
        $bancoscuenta_id = $data['Cobranza']['bancoscuenta_id'] ?? 0; // si es cero, el consorcio no tiene cuenta bancaria
        if ($bancoscuenta_id != 0 && !$this->Propietario->Consorcio->Bancoscuenta->canEdit($bancoscuenta_id)) {
            return ['e' => 1, 'd' => 'La Cuenta bancaria es inexistente'];
        }
        $consorcio_id = $data['Cobranza']['consorcio_id'] ?? 0;
        if (!$this->Propietario->Consorcio->canEdit($consorcio_id)) {
            return ['e' => 1, 'd' => 'El Consorcio es inexistente'];
        }
        foreach ($data['Cobranza'] as $k => $v) {
            $d = explode("_", $k);
            if ($d[0] !== "c") {
                continue;
            }
            if (!$this->Propietario->canEdit($d[1])) {
                return ['e' => 1, 'd' => 'El Propietario es inexistente'];
            }
        }

        if (isset($data['Cobranza']['fdp']) && !$this->Propietario->Consorcio->Client->Formasdepago->canEdit($data['Cobranza']['fdp'])) {
            return ['e' => 1, 'd' => 'La forma de pago es inexistente'];
        }

        $fdp = $this->User->Client->Formasdepago->get(true);
        $formadepago = $fdp[$data['Cobranza']['fdp']]['forma'];
        unset($data['Cobranza']['bancoscuenta_id']); // saco estos 3 para q no rompan las bolas en el prox foreach
        unset($data['Cobranza']['consorcio_id']);
        $this->Propietario->Consorcio->id = $consorcio_id;
        $consorcio = $this->Propietario->Consorcio->field('name');
        $datos = []; // voy guardando el detalle de cada cobranza de cada propietario

        foreach ($data['Cobranza'] as $k => $v) {// para cada cobranza, ejecuto procesaCobranzaManual()
            $d = explode("_", $k);
            if ($d[0] !== "c") {
                continue; // sigo cuando son fechas
            }
            $this->Propietario->id = $d[1];
            $concepto = 'CP ' . $consorcio . " - " . $this->Propietario->field('name') . " - " . $this->Propietario->field('unidad') . " (" . $this->Propietario->field('code') . ")";
            if (!isset($datos[$d[1]])) {
                $datos[$d[1]] = [];
            }
            $f = [];
            if (isset($data['Cobranza']["f_" . $d[1] . "_" . $d[2]])) {
                $f = explode('/', $data['Cobranza']["f_" . $d[1] . "_" . $d[2]]);
            }
            $fecha = !empty($f) && count($f) === 3 && checkdate($f[1], $f[0], $f[2]) ? $f[2] . "-" . $f[1] . "-" . $f[0] : date("Y-m-d");
            $datos[$d[1]] += ['Cobranza' => ['propietario_id' => $d[1], 'fecha' => $fecha, 'concepto' => '', 'recibimosde' => $concepto, 'amount' => 0, 'user_id' => $_SESSION['Auth']['User']['id']] + (!empty($bancoscuenta_id) ? ['bancoscuenta_id' => $bancoscuenta_id] : [])];
            // tengo q ver dentro de todas las cobranzas enviadas cuales son del mismo propietario, y crear los  'lt_XX' => '574' para cada uno
            foreach ($data['Cobranza'] as $l => $m) {
                if ($l === 'c_' . $d[1] . '_' . $d[2] && $m > 0) {// es una cobranza del propietario actual, agrego los detalles abonados en cada liquidation_type
                    $datos[$d[1]]['Cobranza'] += ['chk_' . $d[2] => 0]; // el check de solocapital = 0
                    $datos[$d[1]]['Cobranza'] += ['lt_' . $d[2] => $m]; // el monto en el tipo de liquidacion $d[2]
                    $datos[$d[1]]['Cobranza']['amount'] += $m; // sumo la cobranza del mismo propiet en cada liquidation type
                }
            }
            $datos[$d[1]]['Cobranza']['formadepago'] = $data['Cobranza']['fdp'];
            $datos[$d[1]]['Cobranza']['efectivo'] = $formadepago === 'Efectivo' ? $datos[$d[1]]['Cobranza']['amount'] : 0;
            $datos[$d[1]]['Cobranza']['transferencia'] = $formadepago === 'Transferencia' ? $datos[$d[1]]['Cobranza']['amount'] : 0;
            $datos[$d[1]]['Cobranza']['interdeposito'] = $formadepago === 'Interdepósito' ? $datos[$d[1]]['Cobranza']['amount'] : 0;
            $datos[$d[1]]['Caja']['user_id'] = $_SESSION['Auth']['User']['id'];
        }
        // proceso todas las cobranzas generadas
        foreach ($datos as $k => $v) {
            $resul = $this->procesaCobranzaManual($v);
            if ($resul['e'] == 1) {
                return $resul;
            }
        }
        return ['e' => 0];
    }

    /*
     * Realiza controles sobre la cobranza manual
     */

    private function _controlarCobranza($data) {
        $errores = "";
        if (!isset($data['Cobranza']['amount']) || $data['Cobranza']['amount'] <= 0) {
            $errores .= " # El importe total debe ser mayor a cero<br>";
        }
        if (isset($data['Cobranza']['cheque']) && (float) $data['Cobranza']['cheque'] > 0) {
            // guardo cada cheque utilizado en la cobranza
            foreach ($data['Cobranza'] as $k => $v) {
                if (substr($k, 0, 5) == 'lcht_') {
                    $chid = (int) substr($k, 5);
                    if ($this->Propietario->Consorcio->Client->Cheque->find('count', array('conditions' => array('Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.id' => $chid))) == 0) {
                        $errores .= " # El Cheque utilizado es inexistente<br>";
                        break;
                    }
                    if ($this->Propietario->Consorcio->Client->Cheque->isAnulado($chid)) {
                        $errores .= " # El <a href='" . $this->webroot . "Cheques/view/$chid' target='_blank'>Cheque</a> utilizado se encuentra anulado<br>";
                    }
                    if ($this->Propietario->Consorcio->Client->Cheque->isDepositado($chid)) {
                        $errores .= " # El <a href='" . $this->webroot . "Cheques/view/$chid' target='_blank'>Cheque</a> ya fue depositado<br>";
                    }
                    if (!$this->Propietario->Consorcio->Client->Cheque->hasSaldo($chid, $v)) {
                        $errores .= " # El <a href='" . $this->webroot . "Cheques/view/$chid' target='_blank'>Cheque</a> utilizado no posee saldo disponible<br>";
                    }
                }
            }
        }
        if (!isset($data['Cobranza']['propietario_id']) || !$this->Propietario->canEdit($data['Cobranza']['propietario_id'])) {
            return " # El dato es inexistente";
        }

        // el Consorcio del Propietario y el Consorcio de la Cuenta bancaria deben coincidir (20180719). Agregue isset y !empty x el caso q no tengan cuenta bancaria creada
        if (isset($data['Cobranza']['bancoscuenta_id'])) {
            if (!$this->Propietario->Consorcio->Bancoscuenta->canEdit($data['Cobranza']['bancoscuenta_id'])) {
                return " # El dato es inexistente";
            }
            if ($this->Propietario->Consorcio->Bancoscuenta->getConsorcio($data['Cobranza']['bancoscuenta_id']) != $this->Propietario->getPropietarioConsorcio($data['Cobranza']['propietario_id'])) {
                $errores .= " # El Consorcio asociado al Propietario no coincide con el Consorcio asociado a la Cuenta Bancaria<br>";
            }
        }

        if (isset($data['Cobranza']['formadepago']) && !empty($data['Cobranza']['formadepago']) && !$this->Propietario->Consorcio->Client->Formasdepago->canEdit($data['Cobranza']['formadepago'])) {
            // verifico q la forma de pago seleccionada sea del cliente actual
            $errores .= " # La Forma de pago seleccionada es inexistente<br>";
        }
        // sirve para cobranza manual (cheque) y periodo (interdeposito)
        if (round($data['Cobranza']['amount'] - $data['Cobranza']['efectivo'] - ($data['Cobranza']['transferencia'] ?? 0) - ($data['Cobranza']['cheque'] ?? $data['Cobranza']['interdeposito'] ?? 0), 2) != 0) {
            $errores .= " # Los importes ingresados son incorrectos";
        }
        return $errores;
    }

    // Busca solo los propietarios deudores y retorna a los mismos juntos con sus saldos actuales y los de la ultima expensa

    public function obtienepropdeudores($data, $no_exceptua_interes = null) {
        if (!empty($no_exceptua_interes)) {
            $propietarios = $this->Propietario->getPropietariosNoExceptuanInteres($data['consorcio_id'], ['fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']]);
        } else {
            $propietarios = $this->Propietario->getPropietarios($data['consorcio_id'], ['fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']]);
        }
        $saldosactualespropietarios = [];
        $saldosultimaexpensapropietarios = [];

        $propietariosaux = $propietarios;
        foreach ($propietariosaux as $k => $v) {
            $SaldoActualPorTipoDeLiquidacionPropietario = $this->Propietario->getSaldoActualPorTipoDeLiquidacion($k);

            if ($SaldoActualPorTipoDeLiquidacionPropietario[$data['tipos']] > 0) {      // si el saldo es mayor a cero quiere decir que ese propietario debe
                $saldosactualespropietarios[$k] = $SaldoActualPorTipoDeLiquidacionPropietario;
                $saldosultimaexpensapropietarios[$k] = $this->Propietario->getSaldoUltimaExpensa($k);
            } else {
                unset($propietarios[$k]);
            }
        }
        return ['propietarios' => $propietarios, 'saldosactualespropietarios' => $saldosactualespropietarios, 'saldosultimaexpensapropietarios' => $saldosultimaexpensapropietarios];
    }

    // Busca solo los propietarios deudores . Se utiliza para multasSobreCapital en CobranzasController

    public function obtienepropdeudoresSobreCapital($data) {
        $consorcio_id = $data['consorcio_id'];

        $liquidations_type_id = $data['tipos'];           // $data['tipos'] es el $liquidations_type_id del tipo de liq elegida para multas sobre capital

        $liquidacionActivaId = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationActivaId($consorcio_id, $liquidations_type_id);

        if ($liquidacionActivaId == '0') {      // Si $liquidacionActivaId es cero, es porque no hay liqs abiertas en el consorcio o es porque es la liq inicial     
            $ultimaLiquidacionBloqueadaId = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLastBloqueadaId($consorcio_id, $liquidations_type_id);
            // con la ultima Liquidacion Bloqueada veo si es la liq inicial
            $esInicial = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLiquidationInicial($ultimaLiquidacionBloqueadaId);
            if ($esInicial == 1) {
                return 'soloLiqInicial';
            }
            if ($esInicial == 0) {
                return 'todasBloqueadas';
            }
        }

        $cantPeriodosDeDeuda = [];
        $saldosCapitalActual = [];

        $propietarios = $this->Propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']]);
        $propietariosaux = $propietarios;

        $idLiqAnterior = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->getLastLiquidation($liquidacionActivaId);

        $client = $this->Propietario->Consorcio->getConsorcioClientId($consorcio_id);
        $prefijo = $this->Cobranzatipoliquidacione->LiquidationsType->getPrefijo($client, $liquidations_type_id);

        foreach ($propietariosaux as $k => $v) {
            $SaldoActualLiquidacionPropietario = $this->Propietario->getSaldoActualLiquidacion($k, $liquidations_type_id);

            if ($SaldoActualLiquidacionPropietario > 0) {      // si el saldo es mayor a cero
                $saldosyLiqsDeuda = $this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->SaldosCierre->getSaldosYLiqsPropietarioDeuda($consorcio_id, $k, $prefijo);

                if (!empty($saldosyLiqsDeuda['periodos'])) {  // hay liq/s con deuda debe mas de un periodo es propietario deudor
                    $SaldoLiqCapInt = $this->Propietario->getSaldoDeLiquidacionCapitalInteres($k, $liquidations_type_id, $liquidacionActivaId);

                    $cobranzasYajustes = 0;
                    if (isset($SaldoLiqCapInt['ajustes']) && isset($SaldoLiqCapInt['cobranzas'])) {
                        $sumaAjustes = 0;
                        foreach ($SaldoLiqCapInt['ajustes'] as $ka => $ve) {
                            $sumaAjustes = $sumaAjustes + $ve['Ajustetipoliquidacione']['amount'];
                        }
                        $cobranzasYajustes = $sumaAjustes + $SaldoLiqCapInt['cobranzas'];
                    }

                    $interesLiqAnteriorALaActual = $saldosyLiqsDeuda['saldos'][$idLiqAnterior][$k]['interes'];
                    $capitalLiqAnteriorALaActual = $saldosyLiqsDeuda['saldos'][$idLiqAnterior][$k]['capital'];

                    $resta = $interesLiqAnteriorALaActual - $cobranzasYajustes;

                    if ($resta <= 0) {     // si la resta en menor o igual a cero saco todo el interes entonces el remanente tiene valor solo del capital                       
                        $saldosCapitalActual[$k] = round($capitalLiqAnteriorALaActual + $interesLiqAnteriorALaActual - $cobranzasYajustes, 2);
                    } else {
                        $saldosCapitalActual[$k] = $capitalLiqAnteriorALaActual;
                    }
                    $cantPeriodosDeDeuda[$k] = count($saldosyLiqsDeuda['periodos']);
                } else {
                    unset($propietarios[$k]);
                }
            } else {
                unset($propietarios[$k]);
            }
        }
        return ['propietarios' => $propietarios, 'saldoscapitalactual' => $saldosCapitalActual, 'cantidadperiodosdeuda' => $cantPeriodosDeDeuda];
    }

    // Busca solo los propietarios deudores (si se le pasa el propietarioId devuelse solo ese propietario) y retorna a los mismos difereciando capital 
    // de interes Y si es la liquidacion activa tambien devuelve ajustes y cobranzas

    public function obtienepropdeudoresCapitalInteres($consorcio_id, $liquidationTypeId, $liquidation_id, $propietario_id = null) {
        $propietarios = $this->Propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']], $propietario_id);
        $saldospropietarios = [];
        $propietariosaux = $propietarios;
        foreach ($propietariosaux as $k => $v) {
            $SaldoDeLiquidacionPropietario = $this->Propietario->getSaldoDeLiquidacionCapitalInteres($k, $liquidationTypeId, $liquidation_id);
            if ($SaldoDeLiquidacionPropietario['capital'] > 0) {      // si el saldo de capital es mayor a cero quiere decir que ese propietario debe
                $saldospropietarios[$k] = $SaldoDeLiquidacionPropietario;
            } else {
                unset($propietarios[$k]);
            }
        }
        return ['propietarios' => $propietarios, 'saldospropietarios' => $saldospropietarios/* , 'saldosultimaexpensapropietarios' => $saldosultimaexpensapropietarios */];
    }

    /*
     * Guarda las Multas

      $data:

      array(
      'consorcio_id' => '297',
      'tipos' => '110',
      (int) 18294 => '1',
      (int) 18296 => '1'
      )

     */

    public function guardaMultas($data, $multas) {
        $errores = $this->_controlarMulta($data);
        if ($errores !== "") {
            return $errores;
        }
        $consorcio_id = $data['consorcio_id'];
        $tipoliquidacion = $data['tipos'];

        unset($data['consorcio_id']);
        unset($data['tipos']);

        $interesMultaConsorcio = $this->Propietario->Consorcio->getInteresMulta($consorcio_id);
        $cuentaMulta = $this->Propietario->Consorcio->getCGPDefectoMulta($consorcio_id);
        $nombreCuentaMulta = $this->Propietario->Consorcio->Cuentasgastosparticulare->getNombreCGP($cuentaMulta);
        $liquidation_id = $this->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $tipoliquidacion);

        foreach ($data as $k => $v) {       //utilizo $data porque el $k son los propietarios id seleccionados para multar
            $multa = 0;
            if ($multas['saldosactualespropietarios'][$k][$tipoliquidacion] < $multas['saldosultimaexpensapropietarios'][$k][$tipoliquidacion]) {
                $multa = $multas['saldosactualespropietarios'][$k][$tipoliquidacion] * ($interesMultaConsorcio / 100);
            } else {
                $multa = $multas['saldosultimaexpensapropietarios'][$k][$tipoliquidacion] * ($interesMultaConsorcio / 100);
            }
            $arregloGP = ['liquidation_id' => $liquidation_id, 'propietario_id' => $k, 'coeficiente_id' => null, 'cuentasgastosparticulare_id' => $cuentaMulta, 'date' => date("Y-m-d"), 'amount' => $multa, 'description' => $nombreCuentaMulta, 'heredable' => 0];
            $this->Propietario->GastosParticulare->crear($arregloGP, null, null, $k);        // Crea el gasto particular, funcion crear definida en GastosParticulare
        }
        return '';
    }

    public function guardaMultasSobreCapital($data, $multas) {
        $errores = $this->_controlarMulta($data);
        if ($errores !== "") {
            return $errores;
        }
        $consorcio_id = $data['consorcio_id'];
        $tipoliquidacion = $data['tipos'];

        unset($data['consorcio_id']);
        unset($data['tipos']);

        $interesMultaCapitalConsorcio = $this->Propietario->Consorcio->getInteresMultaCapital($consorcio_id);
        $cuentaMultaCapital = $this->Propietario->Consorcio->getCGPDefectoMultaCapital($consorcio_id);
        $liquidation_id = $this->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $tipoliquidacion);

        foreach ($data as $k => $v) {       //utilizo $data porque el $k son los propietarios id seleccionados para multar
            $multa = $multas['saldoscapitalactual'][$k] * ($interesMultaCapitalConsorcio / 100);

            $arregloGP = ['liquidation_id' => $liquidation_id, 'propietario_id' => $k, 'coeficiente_id' => null, 'cuentasgastosparticulare_id' => $cuentaMultaCapital, 'date' => date("Y-m-d"), 'amount' => $multa, 'description' => 'INTERES PUNITORIO POR FALTA DE PAGO ' . $interesMultaCapitalConsorcio . '%', 'heredable' => 0];
            $this->Propietario->GastosParticulare->crear($arregloGP, null, null, $k, 1);        // Crea el gasto particular, funcion crear definida en GastosParticulare
        }
        return '';
    }

    private function _controlarMulta($data) {
        $consorcio_id = $data['consorcio_id'];
        if ($this->Propietario->Consorcio->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $consorcio_id))) == 0) {
            return 'El Consorcio es inexistente';
        }
        $liquidation_types = $this->Cobranzatipoliquidacione->LiquidationsType->getLiquidationsTypes();
        if (!in_array($data['tipos'], array_keys($liquidation_types))) {
            return 'El Tipo de Liquidación es inexistente';
        }
        $propietarios = array_keys($data);

        unset($propietarios[0]);
        unset($propietarios[1]);

        if ($this->Propietario->find('count', array('conditions' => array('Consorcio.id' => $consorcio_id, 'Propietario.id' => $propietarios), 'recursive' => 0)) != count($propietarios)) {
            return 'El Propietario es inexistente';
        }
        return '';
    }

    /*
     * Guarda los Pagos Fuera de Término
     */

    public function guardaPFT($data) {
        $errores = $this->_controlarPFT($data);
        if ($errores !== "") {
            return $errores;
        }

        $interes = $this->Propietario->Consorcio->getInteres($data['Cobranza']['consorcio_id']);
        $cobranza = $this->getCobranzasPeriodo($data['Cobranza']['consorcio_id']);
        $vencimiento = $this->Propietario->Consorcio->Liquidation->getLastBloqueadaVencimiento($data['Cobranza']['consorcio_id'], $data['Cobranza']['tipos']);
        $tipo = $data['Cobranza']['tipos'];
        $consorcio_id = $data['Cobranza']['consorcio_id'];

        unset($data['Cobranza']['tipos']);
        unset($data['Cobranza']['consorcio_id']);

        $cuentaPFT = $this->Propietario->Consorcio->getCGPDefectoPFT($consorcio_id);
        foreach ($data['Cobranza'] as $ke => $va) {
            $aux = substr($ke, 2);
            $propietario_id = strtok($aux, "_");
            $cobranza_id = strtok("_");                 // id de la cobranza que fue seleccionada en el form

            $keyscobranza = $this->buscaLista($cobranza[$tipo], ['propietario_id' => $propietario_id], true);

            foreach ($keyscobranza as $c) {
                $fechaPago = $cobranza[$tipo][$c]['Cobranza']['fecha'];
                if (strtotime($fechaPago) > strtotime($vencimiento) && $cobranza[$tipo][$c]['Cobranza']['id'] == $cobranza_id) {
                    // pagó fuera de termino para esa cobranza
                    $diasretraso = abs(strtotime($vencimiento) - strtotime($fechaPago)) / (60 * 60 * 24);
                    $total = round($cobranza[$tipo][$c]['Cobranzatipoliquidacione']['amount'] * $diasretraso * round($interes / 30, 4) / 100, 2);
                    $numRecibo = $cobranza[$tipo][$c]['Cobranza']['numero'];
                    $liquidation_id = $this->Propietario->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $tipo);
                    $arregloGP = ['liquidation_id' => $liquidation_id, 'propietario_id' => $propietario_id, 'coeficiente_id' => null, 'cuentasgastosparticulare_id' => $cuentaPFT, 'date' => date("Y-m-d"), 'amount' => $total, 'description' => 'Interés Pago Fuera de Término - Recibo N° ' . $numRecibo, 'heredable' => 0];
                    $this->Propietario->GastosParticulare->crear($arregloGP, null, $cobranza_id);        // Crea el gasto particular, funcion crear definida en GastosParticulare
                }
            }
        }
        return '';
    }

    // Puede venir mas de una cobranza de un propietario y hay que hacer un solo gasto particular
    private function _controlarPFT($data) {
        $consorcio_id = $data['Cobranza']['consorcio_id'];
        if ($this->Propietario->Consorcio->find('count', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $consorcio_id))) == 0) {
            return 'El Consorcio es inexistente';
        }

        $liquidation_types = $this->Cobranzatipoliquidacione->LiquidationsType->getLiquidationsTypes();
        if (!in_array($data['Cobranza']['tipos'], array_keys($liquidation_types))) {
            return 'El Tipo de Liquidación es inexistente';
        }

        unset($data['Cobranza']['tipos']);
        unset($data['Cobranza']['consorcio_id']);
        foreach ($data['Cobranza'] as $k => $v) {
            if ($this->Propietario->find('count', array('conditions' => array('Consorcio.id' => $consorcio_id, 'Propietario.id' => substr($k, 2)), 'recursive' => 0)) == 0) {
                return 'El Propietario es inexistente';
            }
        }
        return '';
    }

    /*
     * Funcion que anula una cobranza
     * Antes de anular, se verifica: 
     *      que la caja tenga saldo para hacer el egreso
     *      que la cuenta bancaria tenga saldo para hacer la extraccion
     *      que los cheques no se hayan depositado
     * Para ello se anula la cobranza (field anulada) 
     * se anula el ingreso a la caja (si corresponde) (se anulan ingreso y transf para poder ver el recibo detallado una vez anulada la cobranza, sino me faltan datos)
     * se anula la transferencia (si corresponde)
     * se resta del saldo del cheque el importe sin anular el mismo (si corresponde)
     */

    public function undo($id) {
        $resul = $this->beforeUndo($id);
        if (!empty($resul)) {
            return $resul;
        }
        //$totalcheque = 0;
        $cheques = $this->Cobranzacheque->find('all', ['conditions' => ['Cobranzacheque.cobranza_id' => $id], 'fields' => ['Cobranzacheque.id', 'Cobranzacheque.amount', 'Cobranzacheque.cheque_id']]);
        //$caja_id = $this->Propietario->Consorcio->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        foreach ($cheques as $k => $v) {
            $this->Propietario->Consorcio->Client->Cheque->setSaldo($v['Cobranzacheque']['cheque_id'], $v['Cobranzacheque']['amount']); // actualizo el saldo del cheque
            //$totalcheque += $v['Cobranzacheque']['amount'];
            //$this->Propietario->Consorcio->Client->Caja->setSaldo($caja_id, -$v['Cobranzacheque']['amount'], 'saldo_cheques'); // actualizo el saldo de la caja (NO! Porq el cheque sigue estando). Actualizo cuando borro el cheque desde Cheques de terceros
            //$this->Cobranzacheque->delete($v['Cobranzacheque']['id']); // no lo borro asi queda el detalle de las cobranzas anuladas
        }

        // anulo el ingreso a la caja
        $cajasingresoid = $this->Propietario->Consorcio->Client->Caja->Cajasingreso->find('first', ['conditions' => ['Cajasingreso.cobranza_id' => $id], 'fields' => ['Cajasingreso.id', 'Cajasingreso.caja_id', 'Cajasingreso.importe']]);
        if (!empty($cajasingresoid)) {
            //$this->Propietario->Consorcio->Client->Caja->setSaldo($cajasingresoid['Cajasingreso']['caja_id'], -$cajasingresoid['Cajasingreso']['importe']);
            //$this->Propietario->Consorcio->Client->Caja->Cajasingreso->id = $cajasingresoid['Cajasingreso']['id'];
            //$this->Propietario->Consorcio->Client->Caja->Cajasingreso->saveField('anulado', 1);
            //$data2 = array('bancoscuenta_id' => 0, 'consorcio_id' => $cajasingresoid['Cajasingreso']['consorcio_id'], 'caja_id' => $cajasingresoid['Cajasingreso']['caja_id'], 'user_id' => $_SESSION['Auth']['User']['id'],
            //    'fecha' => date('Y-m-d'), 'concepto' => '[ANULADO] ' . $this->Propietario->Consorcio->Client->Caja->Cajasingreso->field('concepto'), 'importe' => $cajasingresoid['Cajasingreso']['importe'], 'anulado' => 0);
            //$this->Propietario->Consorcio->Client->Caja->Cajasegreso->crear($data2);
            $this->Propietario->Consorcio->Client->Caja->Cajasingreso->undo($cajasingresoid['Cajasingreso']['id']);
        }

        // anulo la transferencia
        $transferenciaid = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->find('first', ['conditions' => ['Bancosdepositosefectivo.cobranza_id' => $id], 'fields' => ['Bancosdepositosefectivo.id', 'Bancosdepositosefectivo.bancoscuenta_id', 'Bancosdepositosefectivo.importe']]);
        if (!empty($transferenciaid)) {
            //$this->Propietario->Consorcio->Client->Banco->Bancoscuenta->setSaldo($transferenciaid['Bancosdepositosefectivo']['bancoscuenta_id'], -$transferenciaid['Bancosdepositosefectivo']['importe']);
            //$this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->id = $transferenciaid['Bancosdepositosefectivo']['id'];
            //$this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->saveField('anulado', 1);
            $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->undo($transferenciaid['Bancosdepositosefectivo']['id']);
            $this->Propietario->GastosParticulare->borrar($id); // borro el gasto particular asociado en caso q haya creado comisiones x cobranza
        }

        // si es una cobranza automatica, pongo cobranza_id en Pagoselectronico en cero (para q no lo muestre como cargado)
        //Model::updateAll(array $fields, mixed $conditions)
        $this->Pagoselectronico->updateAll(['Pagoselectronico.cobranza_id' => 0], ['Pagoselectronico.cobranza_id' => $id]);

        // anulo la cobranza
        $this->id = $id;
        $this->saveField('concepto', '[ANULADO] ' . $this->field('concepto'));
        $this->saveField('anulada', 1);
        return '';
    }

    public function beforeUndo($id) {
        $this->id = $id;

        // Si el usuario tiene tildado "eliminacobranzas", puede eliminar cualquier cobranza de cualquier usuario. Si esta tildado, no me importa q usuario la hizo, sigo
        // en villarino, paso q se borro ela caja y la cobranza quedó con una caja vieja
        if (isset($_SESSION['Auth']['User']['eliminacobranzas']) && !$_SESSION['Auth']['User']['eliminacobranzas'] && $this->field('user_id') !== $_SESSION['Auth']['User']['id']) {
            return __('La Caja asociada al usuario no es la misma en la cual se guardó la Cobranza');
        }
        if ($this->field('anulada') == 1) {
            return __('La Cobranza ya se encuentra anulada');
        }

        // verifico (si hubo transferencia) que la cuenta bancaria tenga saldo para realizar la ELIMINACIÓN del movimiento
        //$montotransferencia = $this->Propietario->Consorcio->Client->Banco->Bancoscuenta->Bancosdepositosefectivo->find('first', ['conditions' => ['Bancosdepositosefectivo.cobranza_id' => $id], 'fields' => ['Bancosdepositosefectivo.importe', 'Bancosdepositosefectivo.bancoscuenta_id']]);
        //if (!empty($montotransferencia) && !$this->Propietario->Consorcio->Client->Banco->Bancoscuenta->hasSaldo($montotransferencia['Bancosdepositosefectivo']['bancoscuenta_id'], $montotransferencia['Bancosdepositosefectivo']['importe'])) {
        //    return __('La cuenta bancaria asociada no tiene saldo suficiente para realizar la extracci&oacute;n');
        //}
        // verifico (si hubo cheque) que la caja tenga saldocheque para realizar la ELIMINACIÓN del movimiento
        /* $montocheque = $this->Cobranzacheque->find('all', ['conditions' => ['Cobranzacheque.cobranza_id' => $id], 'fields' => ['sum(amount) as total']]);
          if (!empty($montocheque) && !$this->Propietario->Consorcio->Client->Caja->hasSaldo($this->Propietario->Consorcio->Client->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']), $montocheque[0][0]['total'], 'saldo_cheques')) {
          return __('La Caja asociada al usuario no tiene saldo de cheques suficiente para realizar el egreso');
          } */
        // verifico (si hubo cheque) que el mismo no haya sido utilizado para pagar a un proveedor u otra cobranza
        $cheques = $this->Cobranzacheque->find('list', ['conditions' => ['Cobranzacheque.cobranza_id' => $id], 'fields' => ['cobranza_id', 'cheque_id']]);
        if (!empty($cheques)) {
            $ch = $this->Propietario->Consorcio->Client->Proveedor->Proveedorspago->Proveedorspagoscheque->find('first', ['conditions' => ['Proveedorspagoscheque.cheque_id' => array_values($cheques), 'Proveedorspago.anulado' => 0], 'recursive' => 0, 'fields' => 'Proveedorspago.numero']);
            if (!empty($ch)) {
                return __('El cheque utilizado en esta cobranza fue utilizado en el Pago a Proveedor # ' . $ch['Proveedorspago']['numero']);
            }
            $algunofuedepositado = false;
            foreach ($cheques as $k => $v) {
                if ($this->Cobranzacheque->Cheque->isDepositado($v)) {
                    $algunofuedepositado = true;
                    break;
                }
            }
            if ($algunofuedepositado) {
                return __('Uno o m&aacute;s cheques utilizados en la misma fueron depositados');
            }
        }

        return '';
    }

}
