<?php

App::uses('AppModel', 'Model');

class Proveedorsfactura extends AppModel {

    public $virtualFields = ['tipo' => 10];
    public $displayField = 'concepto';
    public $validate = array(
        'proveedor_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'concepto' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'fecha' => array(
            'fecha' => array(
                'rule' => array('date'),
                'message' => 'Debe completar con una fecha correcta',
            ),
        ),
        'numero' => array(
            'numero' => array(
                'rule' => ['checkNumero'],
                'message' => 'El numero de factura ya fue cargado'
            ),
        ),
        'importe' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
        /* 'total' => array(
          'rule' => array('comparison', '>', 0),
          'message' => 'Debe ser un importe mayor a cero',
          ), */
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
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    public $hasMany = array(
        'Proveedorspagosfactura' => array(
            'className' => 'Proveedorspagosfactura',
            'foreignKey' => 'proveedorsfactura_id',
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
            'foreignKey' => 'proveedorsfactura_id',
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
        'Proveedorsfacturasadjunto' => array(
            'className' => 'Proveedorsfacturasadjunto',
            'foreignKey' => 'proveedorsfactura_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorsfactura.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorsfactura.proveedor_id']]]]));
    }

    /*
     * Guardo la factura o la factura y el gasto
     */

    public function guardar($data) {
        $resul = '';
        if (isset($data->data['Proveedorsfactura']['guardagasto']) && $data->data['Proveedorsfactura']['guardagasto'] == '1' && $this->checkNumeroFacturaGuardar($data->data['Proveedorsfactura']['numero'], $data->data['Proveedorsfactura']['proveedor_id']) == true) {
            $proveedor = $this->Proveedor->find('first', ['conditions' => ['Proveedor.id' => $data->data['Proveedorsfactura']['proveedor_id']], 'fields' => ['Proveedor.name', 'Proveedor.address', 'Proveedor.cuit', 'Proveedor.matricula']])['Proveedor'];
            // guardo el gasto asociado
            $save = ['l' => $data->data['Proveedorsfactura']['habilitado'] == "1" ? $data->data['Proveedorsfactura']['liquidation_id'] : 0, 'r' => $data->data['Proveedorsfactura']['rubro_id'],
                'd' => "<p>" . h($proveedor['name'] . (!empty($proveedor['cuit']) ? ' - CUIT: ' . $proveedor['cuit'] : '') . (!empty($proveedor['matricula']) ? ' - Mat: ' . $proveedor['matricula'] : '') . (!empty($proveedor['address']) ? ' - ' . $proveedor['address'] : '') . " - " . date("d/m/Y", strtotime(implode('-', $data->data['Proveedorsfactura']['fecha']))) . " - Factura Nº " . $data->data['Proveedorsfactura']['numero'] . (!empty($data->data['Proveedorsfactura']['concepto']) ? ' - ' . $data->data['Proveedorsfactura']['concepto'] : '')) . "</p>",
                'h' => $data->data['Proveedorsfactura']['heredable'] == "1" ? "true" : "false", 'x' => $data->data['Proveedorsfactura']['habilitado'] == "1" ? "true" : "false", 'id' => 0];
            foreach ($data->data['GastosGeneraleDetalle'] as $k => $v) {
                $save += ['c_' . $k => $v['coeficiente_id']];
            }
            $resul = $this->Liquidation->GastosGenerale->addGasto($save);
        }
        if (isset($resul[0]) && !empty($resul[0])) {// error de validación al crear el gasto general
            return false;
        }
        if (!empty($resul)) {
            $data->data['Proveedorsfactura']['gastos_generale_id'] = $resul[1]['id'];
        }
        $data->data['Proveedorsfactura']['saldo'] = abs($data->data['Proveedorsfactura']['importe']); // el saldo tiene q ser el mismo q el importe. Permito importes negativos, 
        $this->create();
        $r = $this->save($data->data['Proveedorsfactura']);
        $id = $this->getInsertId();
        if ($r) {
            // actualizo el saldo del proveedor (cambia el saldo_pesos)
            $this->Proveedor->setSaldo($data->data['Proveedorsfactura']['proveedor_id'], $data->data['Proveedorsfactura']['importe']);
        } else {
            return false;
        }

        if (isset($data->data['Adjunto']) && !empty($data->data['Adjunto'])) {
            $data->data['Proveedorsfactura']['id'] = $id;
            return $this->Proveedorsfacturasadjunto->guardar($data);
        }

        return true;
    }

//array(
//    'Proveedorspago' => array(
//        'proveedor_id' => '1579',
//        'concepto' => 'PP 4 SERVICIOS',
//        'fecha' => '03/05/2021'
//    ),
//    (int) 266 => array(
//        'fac' => array(
//            (int) 94788 => '732.00'
//        ),
//        'efectivo' => '732.00'
//    )
//)
    /*
     * Dada una factura, la paga en efectivo. Interfaz para Proveedorspago->guardar()
     */
    public function pagarEfectivo($proveedorsfactura_id) {
        $this->id = $proveedorsfactura_id;
        $consorcio_id = $this->Liquidation->getConsorcioId($this->field('liquidation_id'));
        $data = ['Proveedorspago' => ['proveedor_id' => $this->field('proveedor_id'), 'concepto' => 'PP ' . $this->field('concepto'), 'fecha' => date("Y-m-d")],
            $consorcio_id => ['fac' => [$proveedorsfactura_id => $this->field('saldo')], 'efectivo' => $this->field('saldo')]];
        return $this->Proveedor->Proveedorspago->guardar($data);
    }

    /*
     * Obtiene las facturas del proveedor y consorcio seleccionados
     */

    public function getFacturas($proveedor_id = null, $consorcio_id = null, $incluirpagas = false, $buscar = '') {
        $facturas = $this->find('all', ['conditions' => ['Consorcio.habilitado' => 1, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']] + (!empty($proveedor_id) ? ['Proveedorsfactura.proveedor_id' => $proveedor_id] : []) + (!empty($consorcio_id) && $consorcio_id !== "-1" ? ['Liquidation.consorcio_id' => $consorcio_id] : []) +
            ($incluirpagas ? [] : ['Proveedorsfactura.saldo >' => 0, 'Proveedorsfactura.importe >' => 0]) + (!empty($buscar) ? ['OR' => ['Proveedorsfactura.numero' => $buscar, 'Proveedorsfactura.concepto like' => '%' . $buscar . '%', 'Proveedor.name like' => '%' . $buscar . '%']] : []),
            'fields' => ['Proveedorsfactura.id', 'Proveedorsfactura.proveedor_id', 'Proveedorsfactura.gastos_generale_id', 'Proveedorsfactura.concepto', 'Proveedorsfactura.fecha', 'Proveedorsfactura.created', 'Proveedorsfactura.numero', 'Proveedorsfactura.importe',
                'Proveedorsfactura.saldo', 'Proveedorsfactura.liquidation_id', 'Consorcio.name', 'Consorcio.id', 'Proveedor.name', 'Liquidation.periodo', 'Liquidation.bloqueada'],
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'right', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]],
            'order' => 'Proveedor.name,Consorcio.code,Proveedorsfactura.proveedor_id,Liquidation.consorcio_id,Proveedorsfactura.fecha',
            'recursive' => 0]);
        return $facturas;
    }

    /*
     * Obtiene las facturas del consorcio generadas en un rango de fechas
     * Se utiliza en la generacion de asientos automaticos
     */

    public function getTotalFacturasPorFecha($consorcio_id, $desde, $hasta) {
        $facturas = $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.consorcio_id' => $consorcio_id,
                'Proveedorsfactura.fecha >=' => $this->fecha($desde), 'Proveedorsfactura.fecha <=' => $this->fecha($hasta)],
            'fields' => ['sum(importe) as total'], 'recursive' => 0]);
        return $facturas[0][0]['total'] ?? 0;
    }

    /*
     * Obtiene las facturas q tienen gastos asociados y se encuentren pagas (todas las facturas asociadas deben estar pagas). 
     * Se utiliza en la carga de gastos generales (se agrega una P cuando el gasto tiene todas las facturas pagas)
     */

    public function getFacturasPagas($gastosgenerales) {
        return $this->find('list', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorsfactura.gastos_generale_id' => $gastosgenerales],
                    'fields' => ['Proveedorsfactura.id', 'Proveedorsfactura.gastos_generale_id'], 'group' => 'Proveedorsfactura.gastos_generale_id', 'having' => 'sum(Proveedorsfactura.saldo)=0', 'recursive' => 0]);
    }

    public function getFacturasDigitales($liquidation_id) {
        $facturas = $this->find('all', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedorsfactura.liquidation_id' => $liquidation_id],
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedor.id=Proveedorsfactura.proveedor_id']],
                ['table' => 'proveedorsfacturasadjuntos', 'alias' => 'Proveedorsfacturasadjunto', 'type' => 'right', 'conditions' => ['Proveedorsfactura.id=Proveedorsfacturasadjunto.proveedorsfactura_id']]],
            'fields' => ['Proveedorsfactura.gastos_generale_id', 'Proveedorsfacturasadjunto.*']]);
        $resul = [];
        if (!empty($facturas)) {
            foreach ($facturas as $v) {
                if (!isset($resul[$v['Proveedorsfactura']['gastos_generale_id']])) {
                    $resul[$v['Proveedorsfactura']['gastos_generale_id']] = [];
                }
                $resul[$v['Proveedorsfactura']['gastos_generale_id']][] = $v['Proveedorsfacturasadjunto'];
            }
        }
        return $resul;
    }

    public function eliminar() {
        $proveedor_id = $this->field('proveedor_id');
        $importe = $this->field('importe');
        if ($this->delete($this->id, true)) {
            // actualizo el saldo del proveedor
            $this->Proveedor->setSaldo($proveedor_id, -$importe);
            return true;
        }
        return false;
    }

    /*
     * Funcion que actualiza el saldo de una factura proveedor
     */

    public function setSaldo($id, $importe) {
        $this->id = $id;
        $this->saveField('saldo', $this->field('saldo') + $importe);
    }

    /*
     * Funcion que obtiene el saldo pendiente de una factura proveedor
     */

    public function getSaldo($id) {
        $r = $this->find('first', array('conditions' => array('Proveedorsfactura.id' => $id), 'fields' => array('saldo')));
        return $r['Proveedorsfactura']['saldo'];
    }

    /*
     * Funcion que verifica si el monto a pagar de la factura (o NC) es menor o igual al saldo
     */

    public function hasSaldo($id, $importe) {
        $r = $this->find('first', array('conditions' => array('Proveedorsfactura.id' => $id), 'fields' => array('saldo')));
        return (bool) (isset($r['Proveedorsfactura']['saldo']) && ($r['Proveedorsfactura']['saldo'] - $importe) >= 0);
    }

    /*
     * Funcion que obtiene el numero de la factura proveedor
     */

    public function getNumeroFactura($id) {
        $this->id = $id;
        return $this->field('numero');
    }

    /*
     * Obtiene las cuentas bancarias de los consorcios asociados a cada factura $facturas
     * idfacturaproveedor => idcuentabancaria
     */

    public function getCuentasBancariasFacturas($facturas) {
        $resul = [];
        foreach ($facturas as $k => $v) {
            $resul[$v['Proveedorsfactura']['id']] = $this->Liquidation->Consorcio->Bancoscuenta->getCuentaBancaria($this->Liquidation->getConsorcioId($v['Proveedorsfactura']['liquidation_id']));
        }
        return $resul;
    }

    public function beforeDelete($cascade = true) {// 20190731 no importa si esta anulado o no, si existe algun pago a proveedor asociado, NO dejo eliminar la factura, porq el recibo del pago queda mal. En este caso, hacer nota de credito
        $count1 = $this->Proveedorspagosfactura->find('count', array('conditions' => array('proveedorsfactura_id' => $this->id, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0,
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']]]));
        $count2 = $this->Proveedorspagosnc->find('count', array('conditions' => array('proveedorsfactura_id' => $this->id, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']), 'recursive' => 0,
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']]]));
        if ($count1 + $count2 === 0) {
            return true;
        }
        return false;
    }

    // Verifico si el número de factura a agregar ya fue cargado
    public function checkNumero() {
        $count = null;
        $controla = $this->Proveedor->Client->controlaNumFactura($_SESSION['Auth']['User']['client_id']);
        $numero = $this->data['Proveedorsfactura']['numero'];       // numero de factura a agregar
        if ($controla) {             // Se controla por proveedor que el numero de factura a cargar no este ya cargado
            $count = $this->find('first', array('conditions' => array('proveedor_id' => !empty($this->id) ? $this->field('proveedor_id') : $this->data['Proveedorsfactura']['proveedor_id'], 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'],
                    'OR' => array('numero' => $numero, 'TRIM(LEADING "0" FROM Proveedorsfactura.numero)' => ltrim($numero, '0'))),
                'recursive' => 0,
                'fields' => 'Proveedorsfactura.id'));
        }
        return (bool) (empty($count));
    }

    // Utilizada en la funcion "guardar" antes de crear el gasto para chequear si el número de factura a agregar ya fue cargado
    public function checkNumeroFacturaGuardar($numerofactura, $proveedor_id) {
        $count = null;
        $controla = $this->Proveedor->Client->controlaNumFactura($_SESSION['Auth']['User']['client_id']);
        if ($controla) {             // Se controla por proveedor que el numero de factura a cargar no este ya cargado
            $count = $this->find('first', array('conditions' => array('proveedor_id' => $proveedor_id, 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'],
                    'OR' => array('numero' => $numerofactura, 'TRIM(LEADING "0" FROM Proveedorsfactura.numero)' => ltrim($numerofactura, '0'))),
                'recursive' => 0,
                'fields' => 'Proveedorsfactura.id'));
        }
        return (bool) (empty($count));
    }

    public function afterSave($created, $options = []) {
        if (isset($this->data['Proveedorsfactura']['importe'])) {
            // actualizo saldo proveedor. Si la factura era de 100$ y ahora $100.10, en el proveedor va a haber 10c de diferencia. 
            // Resto el saldo q tenia (q es el importe original porq se puede editar solo facturas impagas, y le sumo el nuevo importe
            $this->Proveedor->setSaldo($this->field('proveedor_id'), -$this->field('saldo'));
            $this->Proveedor->setSaldo($this->field('proveedor_id'), $this->data['Proveedorsfactura']['importe']);

            $this->saveField('saldo', abs($this->data['Proveedorsfactura']['importe']));
        }
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Proveedorspagosfactura.concepto LIKE' => '%' . $data['buscar'] . '%',
                'Proveedor.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
