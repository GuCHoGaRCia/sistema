<?php

App::uses('AppModel', 'Model');

class Contasiento extends AppModel {

    public $displayField = 'descripcion';
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'contejercicio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'contcuenta_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'descripcion' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'fecha' => [
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
                'on' => 'create'
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
        'debehaber' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'manual' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Contejercicio' => [
            'className' => 'Contejercicio',
            'foreignKey' => 'contejercicio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Contcuenta' => [
            'className' => 'Contcuenta',
            'foreignKey' => 'contcuenta_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function get() {
        return $this->find('list', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id']]]);
    }

    /*
     * A partir del Id interno del Asiento, obtiene el numero
     */

    public function getNumero($id) {
        $resul = $this->find('first', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.id' => $id]]);
        if (empty($resul)) {
            return 0;
        } else {
            return $resul['Contasiento']['numero'];
        }
    }

    /*
     * A partir del ID de asiento, obtengo todos los asientos asociados (por el numero)
     */

    public function getAsientoInfo($id) {
        $resul = $this->find('first', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.id' => $id], 'fields' => ['numero', 'contejercicio_id']]);
        if (empty($resul)) {
            return [];
        }

        $resul = $this->find('all', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.numero' => $resul['Contasiento']['numero'], 'Contasiento.contejercicio_id' => $resul['Contasiento']['contejercicio_id']],
            'order' => 'Contasiento.id']);
        if (empty($resul)) {
            return [];
        } else {
            return $resul;
        }
    }

    private function _getAsientosPorEjercicioYCuenta($ejercicio, $cuentas, $mes = null) {
        //$m = !empty($mes) ? ['year(Contasiento.fecha)' => date("Y", strtotime($mes)), 'month(Contasiento.fecha)' => date("m", strtotime($mes))] : [];
        $m = !empty($mes) ? ['Contasiento.fecha <=' => date("Y-m-d", strtotime(date("Y-m-t", strtotime($mes))))] : [];
        return $this->find('all', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.contejercicio_id' => $ejercicio, 'Contasiento.contcuenta_id' => $cuentas] + $m]);
    }

    /*
     * Totaliza cada cuenta con la suma de todos los detalles de asientos de esa cuenta
     */

    private function _getTotalAsientosPorEjercicioYCuenta($ejercicio, $cuentas, $mes = null) {
        $asientos = $this->_getAsientosPorEjercicioYCuenta($ejercicio, $cuentas, $mes);
        $totales = [];
        foreach ($cuentas as $k => $v) {
            $totales[$v] = 0; // inicializo las cuentas en cero
        }
        foreach ($asientos as $k => $v) {
            $totales[$v['Contasiento']['contcuenta_id']] += $v['Contasiento']['debe'] - $v['Contasiento']['haber'];
        }
        return $totales;
    }

    public function beforeSave($options = []) {
        $this->data['Contasiento']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        $this->data['Contasiento']['fecha'] = $this->fecha($this->data['Contasiento']['fecha']);

        return true;
    }

    /*
     * Obtiene el proximo numero de Asiento del ejercicio seleccionado
     */

    private function _getNextNumber($ejercicio) {
        $max = $this->find('first', ['conditions' => ['Contasiento.client_id' => $_SESSION['Auth']['User']['client_id'], 'Contasiento.contejercicio_id' => $ejercicio], 'fields' => ['max(Contasiento.numero) as numero']]);
        return ($max[0]['numero'] ?? 0) + 1;
    }

    //'Contasiento' => array(
    //    'consorcio_id' => '107',
    //    'fecha' => '23/08/2019',
    //    'descripcion' => 'a',
    //    'contcuenta' => array(
    //        (int) 0 => '1',
    //        (int) 1 => '22',
    //        (int) 2 => '',
    //        ...
    //    ),
    //    'dsc' => array(// es la descripcion opcional de cada detalle del asiento (si no se ingresa, se toma 'descripcion' por defecto
    //        (int) 0 => '1',
    //        (int) 1 => '',
    //        (int) 2 => '',
    //        ...        
    //    ),
    //    'debe' => array(
    //        (int) 0 => '2.00',
    //        (int) 1 => '0.00',
    //        (int) 2 => '0.00',
    //        ...
    //    ),
    //    'haber' => array(
    //        (int) 0 => '0.00',
    //        (int) 1 => '2.00',
    //        (int) 2 => '0.00', 
    //        ...
    //    )
    //)
    private function _validaCampos($data) {
        $resul = "";
        $esEdit = isset($data['Contasiento']['id']);
        // verifico que el consorcio sea válido para el cliente actual
        if (!isset($data['Contasiento']['consorcio_id']) || (!$this->Consorcio->canEdit($data['Contasiento']['consorcio_id']))) {
            $resul .= __('El Consorcio es inexistente') . "<br>";
        }
        // obtengo el ejercicio en curso (si existe)
        $valido = true;
        $ejercicio = $this->Contejercicio->getEjercicioActual($data['Contasiento']['consorcio_id']);
        if (empty($ejercicio)) {//no hago canEdit, ya se sabe q si porq se hizo Consorcio->canEdit
            $resul .= __('No se encuentra un Ejercicio en curso para el Consorcio') . "<br>";
            $valido = false;
        }

        // verifico que el mes se encuentre dentro del rango del ejercicio
        if ($valido && !$this->Contejercicio->esMesValido($ejercicio, $this->fecha($data['Contasiento']['fecha']))) {
            $resul .= __('La Fecha debe encontrarse entre el Inicio y el Fin del Ejercicio') . "<br>";
        }

        if (max($data['Contasiento']['contcuenta']) == "") {// '' es menor a cualquier string
            $resul .= __('No se seleccion&oacute; ninguna Cuenta') . "<br>";
        }

        // verifico que las Cuentas sean del cliente
        foreach ($data['Contasiento']['contcuenta'] as $r) {
            if (!empty($r) && !$this->Contcuenta->canEdit($r)) {
                $resul .= __('La Cuenta es inexistente') . "<br>";
            }
        }

        // cuentas, debe, haber, dsc deben tener la misma cantidad de elementos
        if (count($data['Contasiento']['contcuenta']) != count($data['Contasiento']['debe']) || count($data['Contasiento']['debe']) != count($data['Contasiento']['haber']) || count($data['Contasiento']['haber']) != count($data['Contasiento']['dsc'])) {
            $resul .= __('La cantidad de valores ingresados en Debe, Haber o Descripción no se corresponde con la cantidad de Cuentas informadas') . "<br>";
        }
        if (count($data['Contasiento']['debe']) !== count(array_filter($data['Contasiento']['debe'], 'is_numeric')) || count($data['Contasiento']['haber']) !== count(array_filter($data['Contasiento']['haber'], 'is_numeric'))) {
            $resul .= __('Debe ingresar valores numéricos mayores a cero en Debe y Haber') . "<br>";
        }

        // todos positivos o cero
        if (min($data['Contasiento']['debe']) < 0 || min($data['Contasiento']['haber']) < 0) {
            $resul .= __('Los valores en Debe y Haber deben ser mayores o iguales a cero') . "<br>";
        }

        // valido la suma de debe y haber tiene que ser cero 
        if (array_sum($data['Contasiento']['debe']) - array_sum($data['Contasiento']['haber']) != 0) {
            $resul .= __('La suma de Debe y Haber debe ser cero') . "<br>";
        }

        // verifico que cada cuenta seleccionada tenga debe o haber, y q cada debe/haber tenga seleccionada una cuenta
        $x = $data['Contasiento'];
        foreach ($x['contcuenta'] as $k => $v) {
            if ($v == "" && ($x['debe'][$k] != 0 || $x['haber'][$k] != 0)) {
                $resul .= "Falta seleccionar la Cuenta en el detalle " . ($k + 1 == 10 ? $k + 1 : "0" . ($k + 1)) . "<br>";
            }
            if ($v !== "" && ($x['debe'][$k] == 0 && $x['haber'][$k] == 0)) {
                $resul .= "Falta ingresar Debe o Haber en la Cuenta " . ($k + 1 == 10 ? $k + 1 : "0" . ($k + 1)) . "<br>";
            }
            if ($x['dsc'][$k] != "" && $v == "") {
                $resul .= "Falta seleccionar la Cuenta en el detalle " . ($k + 1 == 10 ? $k + 1 : "0" . ($k + 1)) . "<br>";
            }
            if ($v !== "" && $esEdit && $x['dsc'][$k] == "") {
                $resul .= "Falta seleccionar la Descripción en el detalle " . ($k + 1 == 10 ? $k + 1 : "0" . ($k + 1)) . "<br>";
            }
            if ($v !== "" && ($x['debe'][$k] != 0 && $x['haber'][$k] != 0)) {
                $resul .= "Solo Debe o Haber pueden ser distintos de cero en el detalle  " . ($k + 1 == 10 ? $k + 1 : "0" . ($k + 1)) . "<br>";
            }
        }

        if ($esEdit) {//esta editando, verifico q los id sean del mismo cliente y tengan el mismo numero
            //$info = $this->getAsientoInfo($x['id'][0]);
        }

        return $resul;
    }

    /*
     * Guarda los Asientos manuales
     */

    public function guardar($data) {
        $r = $this->_validaCampos($data);
        if (!empty($r)) {
            return $r;
        }
        $ejercicio = $this->Contejercicio->getEjercicioActual($data['Contasiento']['consorcio_id']);
        if (!isset($data['Contasiento']['id'])) {//es uno nuevo
            $numero = $this->_getNextNumber($ejercicio);
        } else {
            $numero = $this->getNumero($data['Contasiento']['id'][0]);
        }

        foreach ($data['Contasiento']['contcuenta'] as $k => $v) {
            if ($v != "" && ($data['Contasiento']['debe'][$k] > 0 || $data['Contasiento']['haber'][$k] > 0)) {
                $d = ['consorcio_id' => $data['Contasiento']['consorcio_id'], 'contejercicio_id' => $ejercicio, 'contcuenta_id' => $v, 'numero' => $numero, 'fecha' => $data['Contasiento']['fecha'],
                    'descripcion' => !empty($data['Contasiento']['dsc'][$k]) ? $data['Contasiento']['dsc'][$k] : $data['Contasiento']['descripcion'], 'debe' => $data['Contasiento']['debe'][$k], 'haber' => $data['Contasiento']['haber'][$k], 'manual' => 1];
                if (!isset($data['Contasiento']['id'][$k])) {
                    $this->create();
                } else {
                    $d += ['id' => $data['Contasiento']['id'][$k]];
                }
                $this->save($d);
            } else {
                // ej: originalmente habia 3 detalles de asientos, lo edita y guarda 2. ($data['Contasiento']['id'] tiene 3 elementos pero $data['Contasiento']['contcuenta'] tiene 2, entonces el tercero hay q eliminarlo
                // era un asiento con 3 detalles y ahora queda con 2. Si no lo borro, me va a quedar un detalle de asiento colgado en la nada
                if (isset($data['Contasiento']['id'][$k])) {//sino, es un detalle sin nada q antes no tenia nada tampoco
                    $this->delete($data['Contasiento']['id'][$k]);
                }
            }
        }
        return '';
    }

    /*
     * Genera los Asientos automaticos para el consorcio (o todos) seleccionados
     */

    public function generarAsientosAutomaticos($consorcio) {
        $resul = $this->_beforeGenerarAsientosAutomaticos($consorcio);
        if ($resul !== "") {
            return $resul;
        }

        if ($consorcio == 0) {//todos los consorcios
            $consorcios = $this->Consorcio->getConsorciosList();
        } else {
            $consorcios = [$consorcio => $this->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consorcio], 'recursive' => 0, 'fields' => 'name2'])['Consorcio']['name2']];
        }
        foreach ($consorcios as $consorcio_id => $v) {
            $config = json_decode($this->Consorcio->Contasientosconfig->getConfig($consorcio_id), true);
            $ejercicio = $this->Contejercicio->getEjercicioInfo($this->Contejercicio->getEjercicioActual($consorcio_id));
            // inicializo los datos genericos de los asientos
            $data = ['client_id' => $this->Consorcio->getConsorcioClientId($consorcio_id), 'consorcio_id' => $consorcio_id, 'contejercicio_id' => $ejercicio['id'], 'descripcion' => '', 'debe' => 0, 'haber' => 0, 'manual' => 0];

            // borro, si existen, los asientos generados del consorcio del ejercicio actual (los manuales no!)
            $this->deleteAll(['consorcio_id' => $consorcio_id, 'contejercicio_id' => $ejercicio['id'], 'manual' => 0], false);
            $inicio = $ejercicio['inicio'] . " 00:00:00";
            $fin = strtotime($ejercicio['fin']) > strtotime("now") ? strtotime("now") : strtotime($ejercicio['fin'] . " 23:59:59");

            while (strtotime($inicio) <= $fin) {
                $data['fecha'] = $this->_ultimoDiaDelMes($inicio);
                $primerdiadelmes = date("Y-m-01", strtotime($inicio));
                $liquidacionesEnElRango = $this->Consorcio->Liquidation->getLiquidacionesEnRangoDeFechas($consorcio_id, $primerdiadelmes, $data['fecha']);

                $interes = $redondeo = $totalcobranzas = 0;
                $totalesxrubro = $totalesxcgp = [];
                if (!empty($liquidacionesEnElRango)) {
                    foreach (array_keys($liquidacionesEnElRango) as $l1) {
                        $gg = $this->Consorcio->Liquidation->Resumene->getLiquidationData($l1);
                        if (empty($gg)) {
                            continue;
                        }

                        $d = json_decode($gg['Resumene']['data'], true);
                        $interes += $this->_sumaInteres($d['saldo']);
                        $redondeo += $this->_sumaRedondeo($d['saldo']);
                        $totalesxrubro = $this->_sumaGastosPorRubro($d['gastosinfo'], $totalesxrubro);
                        $totalesxcgp = $this->_sumaGastosPorCGP($d['totales'], $totalesxcgp);
                    }
                }
                // las cobranzas las busco por fecha, no me importa si hay o no liquidaciones en el rango de fechas este
                $totalcobranzas = $this->Consorcio->Propietario->Cobranza->getCobranzasFecha($consorcio_id, $primerdiadelmes, $data['fecha']);

                $this->_generaAsientosLiquidaciones($data, $interes, $redondeo, $totalesxrubro, $totalesxcgp, $config['liquidaciones']);
                $this->_generaAsientosCobranzas($data, $totalcobranzas, $config['cobranzas']);

                $this->_generaAsientosCompras($data, $config['compras'], $consorcio_id, $inicio, $data['fecha']);
                $this->_generaAsientosPagos($data, $config['pagos'], $consorcio_id, $inicio, $data['fecha']);
                $this->_generaAsientosCajas($data, $config['cajas'], $consorcio_id, $inicio, $data['fecha']);
                $this->_generaAsientosBancos($data, $config['bancos'], $consorcio_id, $inicio, $data['fecha']);
                $inicio = date("Y-m-01 00:00:00", strtotime("+1 month", strtotime($inicio)));
            }
            // reenumerar los asientos manuales!! para cada consorcio (al terminar de generar todos los asientos)
            $this->_reenumerarAsientos($consorcio_id, $ejercicio['id']);
        }

        return $resul;
    }

    private function _reenumerarAsientos($consorcio_id, $ejercicio) {
        $lista = $this->find('all', ['conditions' => ['Contasiento.consorcio_id' => $consorcio_id, 'Contasiento.contejercicio_id' => $ejercicio], 'group' => 'Contasiento.numero', 'order' => 'id']);
        $i = 1;
        foreach ($lista as $v) {
            $this->updateAll(['numero' => $i], ['numero' => $v['Contasiento']['numero']]);
            $i++;
        }
    }

    private function _generaAsientosLiquidaciones($data, $interes, $redondeo, $totalesxrubro, $totalesxcgp, $config) {
        $rubros = $this->Consorcio->Rubro->getRubrosInfo($data['consorcio_id']);
        $cuentasgp = $this->Consorcio->Cuentasgastosparticulare->getCuentasInfo($data['consorcio_id']);
        $totalcierre = 0;
        $segeneraronasientos = false;
        $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);

        foreach ($config['rubros'] as $k => $v) {
            $data['contcuenta_id'] = $v;
            if (isset($totalesxrubro[$k]) && $totalesxrubro[$k] != 0) {// si no esta seteado, no hubo gastos en ese rubro, no genero ningun asiento
                if ($totalesxrubro[$k] > 0) {
                    $data['haber'] = abs($totalesxrubro[$k]);
                    $data['debe'] = 0;
                } else {
                    $data['debe'] = abs($totalesxrubro[$k]);
                    $data['haber'] = 0;
                }

                $data['descripcion'] = 'LIQUIDACIONES - ' . strtoupper($rubros[$k]);
                $this->create();
                $this->save($data, false);
                $segeneraronasientos = true;
                $totalcierre += $data['debe'] - $data['haber'];
            }
        }
        // cuenta interes
        if ($interes > 0) {
            $data['descripcion'] = 'LIQUIDACIONES - INTERES';
            $data['haber'] = $interes;
            $data['debe'] = 0;
            $data['contcuenta_id'] = $config['interes']; // la cuenta de interes
            $this->create();
            $this->save($data, false);
            $totalcierre -= $interes;
            $segeneraronasientos = true;
        }
        //gastos particulares
        foreach ($config['cuentasgp'] as $k => $v) {
            $data['contcuenta_id'] = $v;
            if (isset($totalesxcgp[$k]) && $totalesxcgp[$k] != 0) {// si no esta seteado, no genero ningun asiento
                if ($totalesxcgp[$k] > 0) {
                    $data['haber'] = abs($totalesxcgp[$k]);
                    $data['debe'] = 0;
                } else {
                    $data['debe'] = abs($totalesxcgp[$k]);
                    $data['haber'] = 0;
                }

                $data['descripcion'] = 'LIQUIDACIONES - ' . strtoupper($cuentasgp[$k]);
                $this->create();
                $this->save($data, false);
                $segeneraronasientos = true;
                $totalcierre += $data['debe'] - $data['haber'];
            }
        }

        // cuenta redondeo
        if ($redondeo > 0) {
            $data['descripcion'] = 'LIQUIDACIONES - REDONDEO';
            $data['haber'] = 0;
            $data['debe'] = $redondeo;
            $data['contcuenta_id'] = $config['redondeo']; // la cuenta de interes
            $this->create();
            $this->save($data, false);
            $totalcierre += $redondeo;
            $segeneraronasientos = true;
        }
        if ($segeneraronasientos) {
            // cuenta cierre
            $data['contcuenta_id'] = $config['cierre']; // la cuenta de cierre
            if ($totalcierre > 0) {
                $data['debe'] = 0;
                $data['haber'] = abs($totalcierre);
            } else {
                $data['haber'] = 0;
                $data['debe'] = abs($totalcierre);
            }
            $data['descripcion'] = 'LIQUIDACIONES - CIERRE';
            $this->create();
            $this->save($data, false);
        }
    }

    private function _generaAsientosCobranzas($data, $total, $config) {
        if ($total > 0) {
            $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);
            $data['contcuenta_id'] = $config['cobranzas']; // la cuenta de cobranzas
            $data['descripcion'] = 'COBRANZAS';
            $data['haber'] = $total;
            $data['debe'] = 0;
            $this->create();
            $this->save($data, false);

            $data['descripcion'] = 'COBRANZAS - CIERRE';
            $data['contcuenta_id'] = $config['cierre']; // la cuenta de cierre
            $data['haber'] = 0;
            $data['debe'] = $total;
            $this->create();
            $this->save($data, false);
        }
    }

    private function _generaAsientosCompras($data, $config, $consorcio_id, $inicio, $fin) {
        $total = $this->Consorcio->Liquidation->Proveedorsfactura->getTotalFacturasPorFecha($consorcio_id, $inicio, $fin);
        if ($total != 0) {
            $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);
            $data['contcuenta_id'] = $config['facturasproveedor']; // la cuenta de compras
            $data['descripcion'] = 'COMPRAS';
            if ($total > 0) {
                $data['debe'] = $total;
                $data['haber'] = 0;
            } else {
                $data['haber'] = abs($total);
                $data['debe'] = 0;
            }
            $this->create();
            $this->save($data, false);

            $data['descripcion'] = 'COMPRAS - CIERRE';
            $data['contcuenta_id'] = $config['cierre']; // la cuenta de cierre
            if ($total > 0) {
                $data['haber'] = $total;
                $data['debe'] = 0;
            } else {
                $data['debe'] = abs($total);
                $data['haber'] = 0;
            }
            $this->create();
            $this->save($data, false);
        }
    }

    private function _generaAsientosPagos($data, $config, $consorcio_id, $inicio, $fin) {
        $total = $this->Consorcio->Client->Proveedor->Proveedorspago->getTotalPagosPorFecha($consorcio_id, $inicio, $fin);

        if ($total != 0) {
            $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);
            $data['contcuenta_id'] = $config['proveedores']; // la cuenta de pagos
            $data['descripcion'] = 'PAGOS';
            if ($total > 0) {
                $data['debe'] = $total;
                $data['haber'] = 0;
            } else {
                $data['haber'] = abs($total);
                $data['debe'] = 0;
            }
            $this->create();
            $this->save($data, false);

            $data['descripcion'] = 'PAGOS - CIERRE';
            $data['contcuenta_id'] = $config['cierre']; // la cuenta de cierre
            if ($total > 0) {
                $data['haber'] = $total;
                $data['debe'] = 0;
            } else {
                $data['debe'] = abs($total);
                $data['haber'] = 0;
            }
            $this->create();
            $this->save($data, false);
        }
    }

    //"cajas":{"ingresos":"23","pagos":"24","depositosefectivo":"25","depositoscheques":"25","cierre":"2"}
    private function _generaAsientosCajas($data, $config, $consorcio_id, $inicio, $fin) {
        $total = 0;
        $segeneraronasientos = false;
        $ingresos = $this->Consorcio->Cajasingreso->getTotalIngresosFecha($consorcio_id, $inicio, $fin);
        $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);
        if ($ingresos != 0) {
            $data['contcuenta_id'] = $config['ingresos']; // la cuenta de ingresos
            $data['descripcion'] = 'CAJAS - INGRESOS';
            $data['debe'] = 0;
            $data['haber'] = $ingresos;
            $total = -$ingresos;
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }

        $egresos = $this->Consorcio->Cajasegreso->getTotalEgresosPagoProveedorFecha($consorcio_id, $inicio, $fin); // pagos efectivo cheque
        if ($egresos != 0) {
            $data['contcuenta_id'] = $config['pagos']; // la cuenta de pagos
            $data['descripcion'] = 'CAJAS - PAGOS';
            $data['debe'] = $egresos;
            $data['haber'] = 0;
            $total += $egresos;
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }
        $depositos = $this->Consorcio->Cajasegreso->getTotalEgresosDepositosFecha($consorcio_id, $inicio, $fin); //depositos efectivo cheque
        if ($depositos['e'] != 0) {
            $data['contcuenta_id'] = $config['depositosefectivo']; // la cuenta depositos efectivo
            $data['descripcion'] = 'CAJAS - DEPOSITOS EFECTIVO';
            $data['debe'] = $depositos['e'];
            $data['haber'] = 0;
            $total += $depositos['e'];
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }

        if ($depositos['c'] != 0) {
            $data['contcuenta_id'] = $config['depositoscheques']; // la cuenta depositos cheques
            $data['descripcion'] = 'CAJAS - DEPOSITOS CHEQUE';
            $data['debe'] = $depositos['c'];
            $data['haber'] = 0;
            $total += $depositos['c'];
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }
        if ($segeneraronasientos) {
            $data['contcuenta_id'] = $config['cierre']; // la cuenta de cierre
            $data['descripcion'] = 'CAJAS - CIERRE';
            if ($total < 0) {
                $data['debe'] = abs($total);
                $data['haber'] = 0;
            } else {
                $data['haber'] = $total;
                $data['debe'] = 0;
            }
            $this->create();
            $this->save($data, false);
        }
    }

    private function _generaAsientosBancos($data, $config, $consorcio_id, $inicio, $fin) {
        $ingresos = $this->Consorcio->Bancoscuenta->Bancosdepositosefectivo->getTotalTransferencias($consorcio_id, $inicio, $fin);
        $total = -$ingresos; // es negativo, entonces si el cierre es negativo va en el debe, sino haber
        $segeneraronasientos = false;
        $data['numero'] = $this->_getNextNumber($data['contejercicio_id']);
        if ($ingresos > 0) {
            $data['contcuenta_id'] = $config['ingresos']; // la cuenta de ingresos
            $data['descripcion'] = 'BANCOS - INGRESOS';
            $data['debe'] = 0;
            $data['haber'] = $ingresos;
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }

        $egresos = $this->Consorcio->Client->Proveedor->Proveedorspago->getPagosChequePropioPorConsorcio($consorcio_id, $inicio, $fin, null, 0, false);
        $totale = 0;
        foreach ($egresos as $e) {
            $totale += $e['Chequespropio']['importe'];
        }
        $transferencias = $this->Consorcio->Bancoscuenta->Bancosextraccione->getTotalTransferencias($consorcio_id, $inicio, $fin); // pago x transferencia bancaria
        $totale += $transferencias;
        if ($totale > 0) {
            $data['contcuenta_id'] = $config['pagos']; // la cuenta de pagos
            $data['descripcion'] = 'BANCOS - PAGOS';
            $data['debe'] = $totale;
            $data['haber'] = 0;
            $total += $totale;
            $this->create();
            $this->save($data, false);
            $segeneraronasientos = true;
        }

        $cuentas = $this->Consorcio->Bancoscuenta->getCuentasBancarias($consorcio_id);
        $depositosefectivo = $this->Consorcio->Bancoscuenta->Bancosdepositosefectivo->getTotalDepositosEfectivo($consorcio_id, $inicio, $fin); //depositos efectivo cheque
        if (!empty($depositosefectivo)) {
            foreach ($depositosefectivo as $k => $v) {
                if ($v > 0) {
                    $data['contcuenta_id'] = $config['depositosefectivo']; // la cuenta depositos cheque
                    $data['descripcion'] = 'BANCOS - DEPOSITOS EFECTIVO - ' . $cuentas[$k];
                    $data['debe'] = 0;
                    $data['haber'] = $v;
                    $total -= $v;
                    $this->create();
                    $this->save($data, false);
                    $segeneraronasientos = true;
                }
            }
        }

        $depositoscheques = $this->Consorcio->Bancoscuenta->Bancosdepositoscheque->getTotalDepositosCheque($consorcio_id, $inicio, $fin); //depositos efectivo cheque
        if (!empty($depositoscheques)) {
            foreach ($depositoscheques as $k => $v) {
                if ($v > 0) {
                    $data['contcuenta_id'] = $config['depositoscheques']; // la cuenta depositos cheque
                    $data['descripcion'] = 'BANCOS - DEPOSITOS CHEQUE - ' . $cuentas[$k];
                    $data['debe'] = 0;
                    $data['haber'] = $v;
                    $total -= $v;
                    $this->create();
                    $this->save($data, false);
                    $segeneraronasientos = true;
                }
            }
        }


//        $data['contcuenta_id'] = $config['depositoscheques']; // la cuenta de pagos
//        $data['descripcion'] = 'BANCOS - TRANSFERENCIAS INTERBANCARIAS';
//        $data['debe'] = 0;
//        $data['haber'] = $depositos['c'];
//        $total -= $depositos['c'];
//        $this->create();
//        $this->save($data, false);
        if ($segeneraronasientos) {
            foreach ($config['cierre'] as $k => $v) {
                $data['descripcion'] = 'BANCOS - CIERRE - ' . $cuentas[$k];
                $data['contcuenta_id'] = $v; // la cuenta de cierre
                if ($total < 0) {
                    $data['debe'] = abs($total);
                    $data['haber'] = 0;
                } else {
                    $data['haber'] = $total;
                    $data['debe'] = 0;
                }
                $this->create();
                $this->save($data, false);
            }
        }
    }

    private function _sumaInteres($data) {
        $total = 0;
        foreach ($data as $m) {
            $total += $m['interesactual'];
        }
        return $total;
    }

    private function _sumaRedondeo($data) {
        $total = 0;
        foreach ($data as $m) {
            $saldo = $m['capant'] + $m['intant'] + $m['gastosgenerales'] + $m['gastosparticulares'] + $m['interesactual'] - $m['cobranzas'] - $m['ajustes'];
            $total += round($saldo < 0 ? 0 : $saldo - intval($saldo), 2);
        }
        return $total;
    }

    private function _sumaCobranzas($data) {
        $total = 0;
        foreach ($data as $m) {
            $total += $m['cobranzas'];
        }
        return $total;
    }

    /*
     * Obtengo la suma de los totales de gastos x rubro
     * Se utiliza en la Generacion de Asientos Automáticos
     */

    private function _sumaGastosPorRubro($data, $total) {
        foreach ($data as $l => $m) {
            if (!isset($total[$m['GastosGenerale']['rubro_id']])) {
                $total[$m['GastosGenerale']['rubro_id']] = 0;
            }
            if (isset($m['GastosGeneraleDetalle']['amount'])) {
                $total[$m['GastosGenerale']['rubro_id']] += $m['GastosGeneraleDetalle']['amount'];
            } else {
                // forma nueva
                foreach ($m['GastosGeneraleDetalle'] as $k => $v) {
                    $total[$m['GastosGenerale']['rubro_id']] += $v['amount'];
                }
            }
        }

        return $total;
    }

    /*
     * Obtengo la suma de los totales de x cuenta de gastos particulares
     * Se utiliza en la Generacion de Asientos Automáticos
     */

//      "18299":{
//         "coefgen":{
//            "393":{
//               "val":"1.54000",
//               "tot":3744.66
//            },
//            "394":{
//               "val":"1.42000",
//               "tot":-197.27
//            },
//            "395":{
//               "val":"3.33000",
//               "tot":183.31
//            }
//         },
//         "coefpar":{
//            "393":{
//               "val":"1.54000",
//               "tot":7.7,
//               "detalle":[
//                  {
//                     "total":"500.00",//gasto prorrateado
//                     "cuenta":"591",
//                     "descripcion":"asasdas",
//                     "monto":7.7
//                  }
//               ]
//            },
//            "394":{
//               "val":"1.42000"
//            },
//            "395":{
//               "val":"3.33000"
//            }
//         },
//         "tot":299,
//            'detalle' => array(//gasto directo al prop
//			(int) 0 => array(
//				'total' => '285.00',
//				'descripcion' => 'FRANQUEO',
//				'cuenta' => '723'
//			),
//			(int) 1 => array(
//				'total' => '385.00',
//				'descripcion' => 'FRANQUEO CITACION ASAMBLEA',
//				'cuenta' => '723'
//			)
//		)
//      },
    private function _sumaGastosPorCGP($data, $total) {
        foreach ($data as $l => $m) {
            if (isset($m['detalle']) && !empty($m['detalle'])) {// gasto directo al prop
                foreach ($m['detalle'] as $bb) {
                    if (!isset($total[$bb['cuenta']])) {
                        $total[$bb['cuenta']] = 0;
                    }
                    $total[$bb['cuenta']] += $bb['total'];
                }
            }

            if (isset($m['coefpar']) && !empty($m['coefpar'])) {
                foreach ($m['coefpar'] as $rr) {
                    if (isset($rr['detalle']['cuenta'])) {// gasto prorrateado
                        if (!isset($total[$rr['detalle']['cuenta']])) {
                            $total[$rr['detalle']['cuenta']] = 0;
                        }
                        $total[$rr['detalle']['cuenta']] += $rr['detalle']['monto'];
                    }
                }
            }
        }

        return $total;
    }

    /*
     * Obtengo el ultimo dia del mes desde una fecha
     */

    private function _ultimoDiaDelMes($fecha) {
        return date("Y-m-t 23:59:59", strtotime($fecha));
    }

    private function _beforeGenerarAsientosAutomaticos($consorcio = null) {
        // verifico que el consorcio sea válido para el cliente actual
        if (is_null($consorcio)) {
            return __('El Consorcio es inexistente');
        }
        if ($consorcio == 0) {//todos los consorcios
            $consorcios = $this->Consorcio->getConsorciosList();
        } else {
            $consorcios = [$consorcio => $this->Consorcio->getConsorcioName($consorcio)];
        }
        $resul = "";
        foreach ($consorcios as $k => $v) {
            $valido = true;
            $ejercicio = $this->Contejercicio->getEjercicioActual($k);
            if (empty($ejercicio)) {
                $resul .= 'No se encuentra un Ejercicio en curso para el Consorcio ' . h($v) . "<br>";
                $valido = false;
            }
            if ($this->Consorcio->Contasientosconfig->hasIncompleteConfig($k)) {
                $resul .= 'La Configuraci&oacute;n contable del Consorcio ' . h($v) . " se encuentra incompleta<br>";
                $valido = false;
            }
        }
        return $resul;
    }

    /*
     * Obtiene los asientos del consorcio, ejercicio y mes seleccionados. Se utiliza en el Balance
     */

    public function getAsientosConsorcio($consorcio, $ejercicio = null, $mes = null) {
        if (!isset($consorcio) || (!$this->Consorcio->canEdit($consorcio))) {// puede ser cero?
            return ['e' => 1, 'd' => __('El Consorcio es inexistente')];
        }
        if (!empty($ejercicio) && !$this->Contejercicio->canEdit($ejercicio)) {
            return ['e' => 1, 'd' => __('El Ejercicio es inexistente')];
        }

        if (empty($ejercicio)) {
            $ejercicio = $this->Contejercicio->getEjercicioActual($consorcio);
            if (empty($ejercicio)) {
                return ['e' => 1, 'd' => __('No se encuentra un Ejercicio en curso para el Consorcio')];
            }
        }
        $cuentas = $this->Contcuenta->get();
        if (empty($cuentas)) {
            return ['e' => 1, 'd' => __('No se encuentran Cuentas contables creadas')];
        }
        return ['e' => 0, 'd' => $this->_getTotalAsientosPorEjercicioYCuenta($ejercicio, array_keys($cuentas), $mes)];
    }

    public function guardarConfiguracion($data) {
        $r = $this->_validaConfiguracion($data);

        if (!empty($r)) {
            return $r;
        }
        $this->create();

        return ['e' => 0];
    }

//	'liquidaciones' => array(
//		'rubros' => array(
//			(int) 7667 => '4',
//			(int) 7668 => '3',
//			(int) 7669 => '5',
//			(int) 7670 => '22',
//			(int) 7671 => '10',
//			(int) 7672 => '19',
//			(int) 7673 => '23'
//		),
//		'interes' => '24',
//		'cuentasgp' => array(
//			(int) 2168 => '5',
//			(int) 2169 => '21',
//			(int) 2170 => '10'
//		),
//		'redondeo' => '9',
//		'cierre' => '23'
//	),
//	'cobranzas' => array(
//		'cobranzas' => '17',
//		'cierre' => '2'
//	),
//	'compras' => array(
//		'facturasproveedor' => '16',
//		'cierre' => '5'
//	),
//	'pagos' => array(
//		'proveedores' => '3',
//		'cierre' => '11'
//	),
//	'cajas' => array(
//		'ingresos' => '2',
//		'pagos' => '5',
//		'depositos' => '19',
//		'cierre' => '18'
//	),
//	'bancos' => array(
//		'depositosefectivo' => '5',
//		'depositoscheques' => '20',
//		'transferenciasinterbancos' => '23'
//	)
    private function _validaConfiguracion($data) {
        $resul = "";
        if (empty($data) || !isset($data['liquidaciones']) || !isset($data['cobranzas']) || !isset($data['compras']) || !isset($data['pagos']) || !isset($data['cajas']) || !isset($data['bancos'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }

        if (!isset($data['liquidaciones']['rubros']) || !isset($data['liquidaciones']['interes']) || !isset($data['liquidaciones']['cuentasgp']) || !isset($data['liquidaciones']['redondeo']) || !isset($data['liquidaciones']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if (!isset($data['cobranzas']['cobranzas']) || !isset($data['cobranzas']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if (!isset($data['compras']['facturasproveedor']) || !isset($data['compras']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if (!isset($data['pagos']['proveedores']) || !isset($data['pagos']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if (!isset($data['cajas']['ingresos']) || !isset($data['cajas']['pagos']) || !isset($data['cajas']['depositos']) || !isset($data['cajas']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if (!isset($data['bancos']['depositosefectivo']) || !isset($data['bancos']['depositoscheques']) || !isset($data['bancos']['transferenciasinterbancos']) || !isset($data['bancos']['cierre'])) {
            $resul = __('La Configuración es incorrecta') . "<br>";
        }
        if ($resul != "") {
            return $resul;
        }
        foreach ($data['liquidaciones']['rubros'] as $k => $v) {
            if (!$this->Consorcio->Rubro->canEdit($k)) {
                $resul .= "El Rubro es inexistente" . "<br>";
            }
            if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }

        if (!isset($data['liquidaciones']['interes']) || is_numeric($data['liquidaciones']['interes']) && !$this->Contcuenta->canEdit($data['liquidaciones']['interes'])) {
            $resul .= "La Cuenta contable es inexistente" . "<br>";
        }
        foreach ($data['liquidaciones']['cuentasgp'] as $k => $v) {
            if (!$this->Consorcio->Cuentasgastosparticulare->canEdit($k)) {
                $resul .= "La Cuenta de Gastos particulares es inexistente" . "<br>";
            }
            if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }

        if (is_numeric($data['liquidaciones']['redondeo']) && !$this->Contcuenta->canEdit($data['liquidaciones']['redondeo'])) {
            $resul .= "La Cuenta contable es inexistente" . "<br>";
        }
        if (is_numeric($data['liquidaciones']['cierre']) && !$this->Contcuenta->canEdit($data['liquidaciones']['cierre'])) {
            $resul .= "La Cuenta contable es inexistente" . "<br>";
        }

        foreach ($data['cobranzas'] as $k => $v) {
            if ($v !== "" && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }
        foreach ($data['compras'] as $k => $v) {
            if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }
        foreach ($data['pagos'] as $k => $v) {
            if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }
        foreach ($data['cajas'] as $k => $v) {
            if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }
        foreach ($data['bancos'] as $k => $v) {
            if ($k === 'cierre') {
                foreach ($v as $k1 => $v1) {
                    if (is_numeric($v1) && !$this->Contcuenta->canEdit($v1)) {
                        $resul .= "La Cuenta contable es inexistente" . "<br>";
                    }
                    if (is_numeric($k1) && !$this->Consorcio->Bancoscuenta->canEdit($k1)) {
                        $resul .= "La Cuenta bancaria es inexistente" . "<br>";
                    }
                }
            } else if (is_numeric($v) && !$this->Contcuenta->canEdit($v)) {
                $resul .= "La Cuenta contable es inexistente" . "<br>";
            }
        }

        return $resul;
    }

    public function borrar($contasiento_id) {
        $gp = $this->find('first', ['conditions' => ['Contasiento.id' => $contasiento_id], 'fields' => 'Contasiento.numero,Contasiento.consorcio_id,Contasiento.contejercicio_id']);
        if (!empty($gp)) {
            $this->deleteAll(['consorcio_id' => $gp['Contasiento']['consorcio_id'], 'contejercicio_id' => $gp['Contasiento']['contejercicio_id'], 'numero' => $gp['Contasiento']['numero']], false);
        }
    }

}
