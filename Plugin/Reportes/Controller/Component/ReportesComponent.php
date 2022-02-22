<?php

ini_set('max_execution_time', '120');

class ReportesComponent extends Component {

    public function startup(Controller $controller) {
        $this->Controller = $controller;
    }

    /*
     * Genero el reporte correspondiente desde la cola de impresiones
     */

    public function open($data) {
        $id = json_decode($data['data'], true)['liquidation_id'];
        $this->Controller->set('data', $data['data']);
        $this->Controller->set('info', $this->getLiquidationInfo($id, true));
        if ($data['reporte'] == 'resumengastos' || $data['reporte'] == 'composicionsaldos') {
            $this->getRubrosInfo($id);
        }
        $this->getPropietariosInfo($id);
        $client = $this->getClientInfo($id);
        $this->Controller->set('cliente', $client);
        $consorcio = $this->getConsorcioInfo($id, $client['Client']['id']);
        $this->Controller->set('consorcio', $consorcio);
        $this->Controller->layout = '';
        if (substr($data['reporte'], 0, 6) == 'compos') {
            $this->getCuentasInfo($id);
        }
        $this->Controller->set('plataforma', $this->getPlataformaInfo($client['Client']['id']));
        $this->Controller->set('plataformas', $this->getPlataformasInfo());
        $reporte = $this->getReporteAMostrar($data['reporte'], $client['Client']['id']); // se configura en Datos->Configurar Reportes
        // si no prorratea gastos generales, verifico si incluye los coeficientes en la compos de saldos
        if (substr($data['reporte'], 0, 6) == 'compos' && !$consorcio['Consorcio']['prorrateagastosgenerales']) {
            // si no prorratea GG, entonces puede ser la composicionsaldosparticulares, o con coeficientes (composicionsaldoscoeficientes) o sin prop (composicionsaldosparticularescoeficientessinprop)
            $reporte = str_replace('composicionsaldos', 'composicionsaldosparticulares', $reporte);
            $resul = $this->listarCoeficientes($consorcio['Consorcio']['id']);
            $this->Controller->set('coeficientes', $resul['coef']);
        }
        $this->Controller->render("/Reports/$reporte");
    }

    /*
     * Genero el reporte RESUMEN DE CUENTA
     */

    public function resumenesdecuentas($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id || isset($_SESSION['Auth']['User']) && $_SESSION['Auth']['User']['client_id'] !== $client['Client']['id'] && !$_SESSION['Auth']['User']['is_admin']) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('plataforma', $this->getPlataformaInfo($client['Client']['id']));
        $this->Controller->set('plataformas', $this->getPlataformasInfo());
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/resumenesdecuentas');
    }

    /*
     * Genero el reporte RESUMEN DE CUENTA con PDF (ej: zeballos)
     */

    public function resumenesdecuentaspdf($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id || isset($_SESSION['Auth']['User']) && $_SESSION['Auth']['User']['client_id'] !== $client['Client']['id'] && !$_SESSION['Auth']['User']['is_admin']) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/resumenesdecuentaspdf');
    }

    /*
     * Genero el reporte RESUMEN DE CUENTA
     */

    public function resumencuenta($liquidation_id, $propietario_id, $link, $client_id = null) {
        //debug($liquidation_id);debug($propietario_id);debug($link);debug($client_id);
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id, true);
        $client = $this->getClientInfo($liquidation_id, $link);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('plataforma', $this->getPlataformaInfo($client['Client']['id']));
        $this->Controller->set('plataformas', $this->getPlataformasInfo());
        $this->Controller->set('propietario_id', $propietario_id);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/resumencuenta');
    }

    /*
     * Genero la REIMPRESION DE CUPON DE PAGO
     */

    public function reimpresioncupon($data) {
        $cliente = $this->obtenerClienteInfo($data['pid']);
        $this->Controller->set('propietario_id', $data['pid']);
        $propietarios = ClassRegistry::init('Propietario');
        $info = $propietarios->find('first', ['conditions' => ['Propietario.id' => $data['pid']]]);
        $this->Controller->set('propinfo', $info);
        $this->Controller->set('fecha', $data['f']);
        $this->Controller->set('concepto', $data['c']);
        $this->Controller->set('importe', $data['i']);
        $this->Controller->set('lt', $data['liquidations_type_id']);
        $this->Controller->set('cliente', $cliente);
        $lt = $this->getLiquidationsTypesPrefijos($cliente);
        $this->Controller->set('prefijos', $lt);
        $consorcio = ClassRegistry::init('Consorcio');
        $this->Controller->set('plataforma', $this->getPlataformaInfo($cliente['Client']['id']));
        $this->Controller->set('plataformas', $this->getPlataformasInfo());
        $this->Controller->set('consorcio', $consorcio->find('first', ['conditions' => ['Consorcio.id' => $info['Propietario']['consorcio_id']], 'recursive' => 0, 'fields' => 'Consorcio.*']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/reimpresioncupon');
    }

    /*
     * Genero el reporte RESUMEN DE GASTOS CON TOTALES
     */

    public function resumengastos($liquidation_id, $link, $client_id = null) {
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getRubrosInfo($liquidation_id);
        $client = $this->getClientInfo($liquidation_id, $client_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/' . $this->getReporteAMostrar('resumengastos', $client['Client']['id']));
    }

    /*
     * Genero el reporte RESUMEN GASTOS PARTICULARES X CUENTA
     */

    public function gastosparticularesporcuenta($liquidation_id, $client_id = null) {
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getCuentasInfo($liquidation_id);
        $client = $this->getClientInfo($liquidation_id, $client_id);
        $this->getPropietarios($liquidation_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/gastosparticularesporcuenta');
    }

    /*
     * Genero el reporte Composicion de Saldos
     */

    public function composicionsaldos($liquidation_id, $link, $client_id = null) {
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $client = $this->getClientInfo($liquidation_id, $client_id);
        $this->Controller->set('cliente', $client);
        $consorcio = $this->getConsorcioInfo($liquidation_id, $client['Client']['id']);
        $this->Controller->set('consorcio', $consorcio);
        $this->Controller->layout = '';
        $this->getCuentasInfo($liquidation_id);
        $reporte = $this->getReporteAMostrar('composicionsaldos', $client['Client']['id']);
        // si no prorratea gastos generales, verifico si incluye los coeficientes en la compos de saldos
        if (!$consorcio['Consorcio']['prorrateagastosgenerales']) {
            //$reporte = ($reporte == 'composicionsaldos' ? 'composicionsaldosparticulares' : ($reporte == 'composicionsaldoscoeficientes' ? 'composicionsaldosparticularescoeficientes' : 'composicionsaldosparticularescoeficientessinprop'));
            $reporte = str_replace('composicionsaldos', 'composicionsaldosparticulares', $reporte);
            $resul = $this->listarCoeficientes($consorcio['Consorcio']['id']);
            $this->Controller->set('coeficientes', $resul['coef']);
        } else {
            $this->getRubrosInfo($liquidation_id);
        }
        $this->Controller->render("/Reports/$reporte");
    }

    /*
     * Genero el reporte ESTADO DE DISPONIBILIDAD del consorcio
     */

    public function edconsorcio($consorcio_id, $l1, $l2, $clientId) {  // $l1 y $l2 son las liqs elegidas en rango liquidaciones
        $c = ClassRegistry::init('Consorcio');
        $client_id = $c->getConsorcioClientId($consorcio_id);
        if (!empty($clientId) && $client_id !== $clientId) {
            $this->Controller->redirect(['controller' => 'consorcios', 'action' => 'index']);
        }
        if ($l1 === $l2) {                          // SI L1 ES IGUAL A L2 SERIA COMO ELEGIR EL ESTADO DE DSIPONIBILIDAD DE UNA LIQ EN LIQUIDACIONES
            $this->Controller->redirect(['controller' => 'Reports', 'action' => 'edliquidacion', $l2]);
        }

        $liq = ClassRegistry::init('Liquidation');
        $fechaClosedL1 = $liq->getLiquidationClosedDate($l1);
        $fechaClosedL2 = $liq->getLiquidationClosedDate($l2);
        $liqTypeIdL1 = $liq->getLiquidationsTypeId($l1);
        $liqTypeIdL2 = $liq->getLiquidationsTypeId($l2);
        $lt = $this->getLiquidationsTypeInfo($client_id);

        $liqsPorTipo = [];
        foreach ($lt as $k => $v) {
            $liqsPorTipo[$k] = [];
        }
        $liqsPorTipo[$liqTypeIdL2][] = $l2;

        $liqs = $liq->getGruposLiquidacionesPorTipoEnRangoDeFechas($consorcio_id, $fechaClosedL1, $fechaClosedL2);
        if (isset($liqs) && $liqs !== 0) {
            foreach ($liqs as $ka => $ve) {
                $liqsPorTipo[$ve][] = $ka;
            }
        }
        $liqsPorTipo[$liqTypeIdL1][] = $l1;     //$liqsPorTipo TIENE TODAS LAS LIQS ELEGIDAS SEGUN RANGO (INCLUIDAS L1 Y L2)

        $prefijos = $this->getLiquidationsTypesPrefijos($client_id);

        $liqPorTipoNuevaYAntigua = [];          //son las liqs mas nueva y mas antigua por tipo segun el rango elegido
        foreach ($liqsPorTipo as $k1 => $v1) {
            if (!empty($v1)) {
                $liqPorTipoNuevaYAntigua[$prefijos[$k1]]['masNueva'] = $v1[0];
                $liqPorTipoNuevaYAntigua[$prefijos[$k1]]['masAntigua'] = $v1[count($v1) - 1];
            }
        }
        /*
          EJ. PREFIJOS
          array(
          (int) 110 => '0',
          (int) 195 => '5',
          (int) 196 => '9'
          )
         */
        $gp = ClassRegistry::init('Proveedorspago');
        $gg = ClassRegistry::init('GastosGenerale');

        $data = [];

        foreach ($liqPorTipoNuevaYAntigua as $k2 => $v2) {                  // CICLO DE LAS LIQS DENTRO DEL RANGO, TENIENDO EN CUENTA LOS TIPOS DE LIQUIDACION         
            $last = $liq->getLastLiquidation($v2['masAntigua']);
            $inicial = $liq->getLiquidationInicial($last);
            if ($inicial == 1) {    // entonces la anterior es liq inicial
                $desde = $liq->getLiquidationCreatedDate($v2['masAntigua']);
            } else {                                                        // Desde y hasta a usar en las funciones se define para cada grupo de tipo de liq,
                $desde = $liq->getLiquidationClosedDate($last);             // segun los que esten en base a las liqs del rango elegido
            }
            $hasta = $liq->getLiquidationClosedDate($v2['masNueva']);

            $liq_id = $v2['masNueva'];
            if ($k2 == 0) {        // ordinarias 
                $this->Controller->set('disponibilidad', $liq->getDisponibilidad($last, $v2['masAntigua']));    // SOLO PARA ORDINARIA
                // resumen se usa solo en liqs ordinarias, para la Disponibilidad y Disponibilidad Paga
                $this->Controller->set('resumen', $this->getTotalesMovimientosResumen($consorcio_id, date("Y-m-d H:i:s", strtotime($desde)), date("Y-m-d H:i:s", strtotime($hasta))));
                $totalgg = ['0' => 0];
                $gastosGenererales = 0;
            }

            $this->Controller->set('disponibilidadpaga' . $k2, $liq->getDisponibilidadPaga($last, $v2['masAntigua']));
            // gastospagos se usa solo para la Disponibilidad Paga 
            $this->Controller->set('gastospagos' . $k2, $gp->getTotalPagosPorLiquidacion($consorcio_id, date("Y-m-d H:i:s", strtotime($desde)), date("Y-m-d H:i:s", strtotime($hasta)), $k2));

            while ($liq_id >= $v2['masAntigua']) {  // SE EMPIEZA POR LAS MAS NUEVAS LIQS SEGUN TIPO Y SE USA LA FUNCION getLastLiquidation PARA IR A LA ANTERIOR
                $data[$k2][$liq_id] = $this->getLiquidationData($liq_id);
                if ($k2 == 0) {
                    $gastosGenererales = $gg->calculaTotalesGastos($liq_id);              // se usa solo para la Disponibilidad, solo en liqs ordinarias
                    $totalgg[$k2] += $gastosGenererales;
                }
                $liq_id = $liq->getLastLiquidation($liq_id);
            }
        }
        $this->Controller->set('data', $data);                            //seteado para todas las liqs del rango elegido, AGRUPANDO POR TIPO DE LIQS
        if (isset($totalgg)) {
            $this->Controller->set('gg', $totalgg);
        }
        $this->Controller->set('periodoL1', $liq->getPeriodo($l1));
        $this->Controller->set('periodoL2', $liq->getPeriodo($l2));
        $this->Controller->set('cliente', $this->getClientInfo($l2));     // con setearlo una vez ya alcanza
        $this->Controller->set('consorcio', $this->getConsorcioInfo($l2, $client_id));

        $this->Controller->layout = '';
        $this->Controller->autoRender = false;
        $view = new View($this->Controller, false);
        echo $view->render('/Reports/edconsorcio');
    }

    /*
     * Genero el reporte ESTADO DE DISPONIBILIDAD de la liquidacion. 
     * Si $gastosgenerales=true, muestro los "Gastos Generales" en vez de los "Gastos Pagos"
     */

    public function edliquidacion($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id, $client_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $liquidation = $this->getLiquidationInfo($liquidation_id, true);
        $this->Controller->set('info', $liquidation);
        $this->Controller->set('cliente', $client);
        $consorcio = $this->getConsorcioInfo($liquidation_id, $client['Client']['id']);
        $this->Controller->set('consorcio', $consorcio);
        $liq = ClassRegistry::init('Liquidation');
        $last = $liq->getLastLiquidation($liquidation_id);
        if ($last == 0) {
            $desde = $liq->getLiquidationCreatedDate($liquidation_id);
        } else {
            $desde = $liq->getLiquidationClosedDate($last);
        }
        //$desde = $liq->getLiquidationClosedDate($last);
        $hasta = $liq->getLiquidationClosedDate($liquidation_id);

        $this->Controller->set('disponibilidad', $liq->getDisponibilidad($last, $liquidation_id));
        $this->Controller->set('disponibilidadpaga', $liq->getDisponibilidadPaga($last, $liquidation_id));

        //if ($liquidation['LiquidationsType']['prefijo'] != 0) {//si no es ordinaria
        $gp = ClassRegistry::init('Proveedorspago');
        $this->Controller->set('gastospagos', $gp->getTotalPagosPorLiquidacion($consorcio['Consorcio']['id'], date("Y-m-d H:i:s", strtotime($desde)), date("Y-m-d H:i:s", strtotime($hasta)), $liquidation['LiquidationsType']['prefijo']));
        //} else {
        $gg = ClassRegistry::init('GastosGenerale');
        $totalgg = $gg->calculaTotalesGastos($liquidation_id);
        $this->Controller->set('gg', $totalgg);
        $this->Controller->set('resumen', $this->getTotalesMovimientosResumen($consorcio['Consorcio']['id'], date("Y-m-d H:i:s", strtotime($desde)), date("Y-m-d H:i:s", strtotime($hasta))));
        //}

        $this->Controller->layout = '';
        $this->Controller->autoRender = false;
        $view = new View($this->Controller, false);
        echo $view->render('/Reports/edliquidacion');
    }

    /*
     * Genero el reporte RESUMEN PERIODO de la liquidacion
     */

    public function resumenperiodo($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id, $client_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id, true));
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $cobranza = ClassRegistry::init('Cobranza');
        $cobranzas = $cobranza->getCobranzas($liquidation_id);
        $detalle = [];
        if (!empty($cobranzas)) {
            foreach ($cobranzas as $k => $v) {
                $detalle[$v['Cobranza']['id']] = $cobranza->getDetalleCobranza($v['Cobranza']['id']);
            }
        }
        $ajuste = ClassRegistry::init('Ajuste');
        $ajustes = $ajuste->getAjustes($liquidation_id);
        $this->Controller->set('ajustes', $ajustes);
        $this->Controller->set('detalle', $detalle);
        $this->detalleGastosyDisponibilidad($liquidation_id);
        $this->Controller->layout = '';
        $this->Controller->autoRender = false;
        $view = new View($this->Controller, false);
        echo $view->render('/Reports/resumenperiodo');
    }

    /*
     * Genero el reporte PLANILLA DE PAGOS (planilla de propietarios x tipo de liq para poder escribir los montos cobrados de cada uno)
     */

    public function planillapagos($liquidation_id, $client_id = null) {
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $client = $this->getClientInfo($liquidation_id, $client_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/planillapagos');
    }

    /*
     * Genero el reporte PLANILLA DE FIRMAS
     */

    public function planillafirmas($consorcio_id) {
        $propietarios = ClassRegistry::init('Propietario');
        $prop = $propietarios->getPropietarios($consorcio_id);
        $c = ClassRegistry::init('Consorcio');
        $this->Controller->set('consorcio', $c->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'recursive' => 0]));
        $this->Controller->set('propietarios', $prop);
        $this->Controller->set('cliente', $c->getConsorcioClienteInfo($consorcio_id));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/planillafirmas');
    }

    /*
     * Genero el reporte PLANILLA DE GASTOS PARTICULARES
     */

    public function planillaparticulares($consorcio_id) {
        $propietarios = ClassRegistry::init('Propietario');
        $prop = $propietarios->getPropietarios($consorcio_id);
        $c = ClassRegistry::init('Consorcio');
        $this->Controller->set('consorcio', $c->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'recursive' => 0]));
        $this->Controller->set('propietarios', $prop);
        $this->Controller->set('cliente', $c->getConsorcioClienteInfo($consorcio_id));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/planillaparticulares');
    }

    public function propietariosdatos($consorcio_id) {
        $propietarios = ClassRegistry::init('Propietario');
        $prop = $propietarios->getPropietarios($consorcio_id);
        $c = ClassRegistry::init('Consorcio');
        $this->Controller->set('consorcio', $c->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'recursive' => 0]));
        $this->Controller->set('propietarios', $prop);
        $this->Controller->set('cliente', $c->getConsorcioClienteInfo($consorcio_id));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/propietariosdatos');
    }

    /*
     * Genera el listado de propietarios con sus coeficientes
     */

    public function coeficientespropietarios($consorcio_id) {
        $resul = $this->listarCoeficientes($consorcio_id);
        $prop = $resul['prop'];
        $coef = $resul['coef'];
        $c = ClassRegistry::init('Consorcio');
        $this->Controller->set('consorcio', $c->find('first', ['conditions' => ['Consorcio.id' => $consorcio_id], 'recursive' => 0]));
        $this->Controller->set('propietarios', $prop);
        $this->Controller->set('coeficientes', $coef);
        $this->Controller->set('nombrescoef', $c->listarCoeficientes($consorcio_id));
        $this->Controller->set('cliente', $c->getConsorcioClienteInfo($consorcio_id));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/coeficientespropietarios');
    }

    public function listarCoeficientes($consorcio_id) {
        $propietarios = ClassRegistry::init('Propietario');
        return $propietarios->listarCoeficientes($consorcio_id);
    }

    /*
     * Genera el listado de propietarios deudores/acreedores
     */

    public function propietariosdeudores($liquidation_id, $consorcio_id, $client_id = null, $cual = 'propietariosdeudores') {
        $liquidation = ClassRegistry::init('Liquidation');
        $propietario = ClassRegistry::init('Propietario');
        $consorcio = ClassRegistry::init('Consorcio');
        $info = $this->getLiquidationInfo($liquidation_id);
        $esinicial = isset($info[0]['inicial']) ? $info[0]['inicial'] : 0;
        $liquidationTypeId = $liquidation->getLiquidationsTypeId($liquidation_id);
        $this->Controller->set('dataLiquidacion', $propietario->getDataResumeneDeLiquidacion($consorcio_id, $liquidationTypeId, $liquidation_id, $esinicial));
        $this->Controller->set('cantidadpropietarios', $propietario->getCount($consorcio_id));
        $this->Controller->set('propietarios', $propietario->getPropietarios($consorcio_id, ['fields' => ['Propietario.id', 'Propietario.name', 'Propietario.unidad', 'Propietario.code']]));
        $this->Controller->set('info', $info);
        $client = $this->getClientInfo($liquidation_id, $client_id);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->set('valordesdereportepropdeudor', $consorcio->getValorDesdeReportePropDeudor($consorcio_id));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/' . $cual);
    }

    /*
     * Genero el reporte Cuenta corriente propietario
     */

    public function cuentacorrientepropietario($pid, $f1, $f2, $origen) {
        $saldos = $this->getSaldosPropietario($pid, $f1, $f2, true); // true=todas las liquidaciones (sino trae solo las bloqueadas)
        $this->Controller->set('data', $saldos);
        // obtengo los movimientos de cada una de las liquidaciones entre f1 y f2 (para mostrar el detalle de movimientos en la CC)
        $movimientos = [];
        $liq = 0;
        $abiertas = [];
        foreach ($saldos['saldos'] as $k => $v) {
            if ($k === "abiertas") { // son los saldos de las liquidaciones abiertas
                $abiertas = $v;
                continue;
            }
            $liq = $v['Liquidation']['id'];
            $movimientos[$v['Liquidation']['id']] = $this->getLiquidationData($v['Liquidation']['id']); // liq_id => resumene->data (json)
        }
        //  $movimientos...
        //  array(
        //       (int) 141 => array(
        //  	'Resumene' => array(
        //              'data' => '{"liquidation_id":"141","tota...
        //          )
        //	),
        //       (int) 145 => array(
        //		'Resumene' => array(
        //              'data' => '{"liquidation_id":"145","tota...
        //		)
        //	),
        $this->Controller->set('abiertas', $abiertas);
        ksort($movimientos); // ordeno x liquidation_id (key)
        $this->Controller->set('movimientos', $movimientos);
        $this->Controller->set('cobranzas', $this->getCobranzasPropietario($pid));
        $cliente = $this->obtenerClienteInfo($pid);
        $this->Controller->set('lt', $this->getLiquidationsTypeInfo($cliente['Client']['id']));
        $this->Controller->set('cliente', $cliente);
        $prop = $this->obtenerPropietarioInfo($pid);
        $this->Controller->set('propietario', $prop);
        $this->getPropietariosAjustes($pid);
        $this->Controller->set('f1', $f1 . " 00:00:00");
        $this->Controller->set('f2', $f2 . " 23:59:59");
        if (isset($origen)) {
            $this->Controller->set('origen', $origen);
        }
        $consorcio = ClassRegistry::init('Consorcio');
        $this->Controller->set('consorcio', $consorcio->find('first', ['conditions' => ['Consorcio.id' => $prop['Propietario']['consorcio_id']], 'recursive' => 0]));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/cuentacorrientepropietario');
    }

    /*
     * Genero el reporte Analitico de Gastos
     */

    public function analiticogastos($l1, $l2) {
        // obtengo los movimientos de cada una de las liquidaciones entre f1 y f2 (para mostrar el detalle de movimientos en la CC)
        $movimientos = $liquidationinfo = $cobranzas = [];
        $movimientos[$l2] = $this->getLiquidationData($l2);
        $l = ClassRegistry::init('Liquidation');
        $liquidationinfo[$l2] = $this->getLiquidationInfo($l2);
        $cobranza = ClassRegistry::init('Cobranza');
        $cobranzas[$l2] = $cobranza->totalCobranzas($l2);
        // para todas las liquidaciones entre $l1 y $l2 traigo el detalle
        while ($l2 != $l1) {
            $l2 = $l->getLastLiquidation($l2);
            $movimientos[$l2] = $this->getLiquidationData($l2);
            $liquidationinfo[$l2] = $this->getLiquidationInfo($l2);
            $cobranzas[$l2] = $cobranza->totalCobranzas($l2);
        }
        $client = $this->getClientInfo($l2);
        $this->getRubrosInfo($l2);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('liquidationinfo', $liquidationinfo);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($l2, $client['Client']['id']));
        $this->Controller->set('movimientos', $movimientos);
        $this->Controller->set('cobranzas', $cobranzas);
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/analiticogastos');
    }

    /*
     * Genero el reporte Cuenta corriente liquidacion. Como el resumen de cuenta pero sin barcode y demas
     */

    public function cuentacorrienteliquidacion($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id, false, true);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/cuentacorrienteliquidacion');
    }

    /*
     * Genero el reporte Cuenta corriente proveedor
     */

    public function cuentacorrienteproveedor($pid, $f1, $f2) {
        $saldos = $this->getMovimientosProveedor($pid, $f1, $f2);
        $this->Controller->set('saldos', $saldos);
        $this->Controller->set('proveedor', $this->obtenerProveedorInfo($pid));
        $this->Controller->set('f1', $f1 . " 00:00:00");
        $this->Controller->set('f2', $f2 . " 23:59:59");
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/cuentacorrienteproveedor');
    }

    /*
     * Genero los recibos para q los administradores entreguen a los propietarios (con el importe de la liquidacion actual)
     */

    public function recibosliquidacion($liquidation_id, $client_id = null, $triple = null) {
        $client = $this->getClientInfo($liquidation_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id, false, true);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('consorcio', $this->getConsorcioInfo($liquidation_id, $client['Client']['id']));
        $this->Controller->layout = '';
        if (empty($triple)) {
            $this->Controller->render('/Reports/recibosliquidacion');
        } else {
            $this->Controller->render('/Reports/recibosliquidaciontriple');
        }
    }

    /*
     * Genero el reporte Cobranzas liquidacion. Son las cobranzas recibidas en esta liquidacion (desde la fecha de cierre de la ultima hasta la fecha actual)
     */

    public function cobranzasrecibidas($liquidation_id, $client_id = null) {
        $client = $this->getClientInfo($liquidation_id);
        if (!empty($client_id) && $client['Client']['id'] !== $client_id) {
            $this->Controller->redirect(['controller' => 'liquidations', 'action' => 'index']);
        }
        $this->Controller->set('data', $this->getLiquidationData($liquidation_id));
        $cobranza = ClassRegistry::init('Cobranza');
        $cobranzas = $cobranza->getCobranzas($liquidation_id);
        $detalle = [];
        if (!empty($cobranzas)) {
            foreach ($cobranzas as $k => $v) {
                $detalle[$v['Cobranza']['id']] = $cobranza->getDetalleCobranza($v['Cobranza']['id']);
            }
        }
        $ajuste = ClassRegistry::init('Ajuste');
        $ajustes = $ajuste->getAjustes($liquidation_id);
        $this->Controller->set('ajustes', $ajustes);
        $consorcio = $this->getConsorcioInfo($liquidation_id, $client['Client']['id']);
        $this->Controller->set('consorcio', $consorcio);
        $bancoscuenta = ClassRegistry::init('Bancoscuenta');
        $this->Controller->set('bancoscuentas', $bancoscuenta->getCuentasBancarias($consorcio['Consorcio']['id'], $client['Client']['id']));
        $this->Controller->set('cobranzas', $cobranzas);
        $this->Controller->set('detalle', $detalle);
        $this->Controller->set('totales', $cobranza->getTotalPorFormadePago($liquidation_id));
        $this->Controller->set('info', $this->getLiquidationInfo($liquidation_id));
        $this->getPropietariosInfo($liquidation_id, false, true);
        $this->Controller->set('cliente', $client);
        $this->Controller->set('formasdepago', $this->getFormasdepago($client['Client']['id']));
        $this->Controller->layout = '';
        $this->Controller->render('/Reports/cobranzasrecibidas');
    }

    /*
     * Obtengo la info del cliente
     */

    public function getClientInfo($liquidation_id, $client_id = null) {
        $client = ClassRegistry::init('Client');
        return $client->getClientInfo($liquidation_id, $client_id);
    }

    /*
     * Obtiene la configuracion de plataforma de un cliente especifico
     */

    public function getPlataformaInfo($client_id = null) {
        $p = ClassRegistry::init('Plataformasdepagosconfig');
        return $p->getConfig($client_id);
    }

    /*
     * Obtiene la lista de las plataformas disponibles
     */

    public function getPlataformasInfo() {
        $p = ClassRegistry::init('Plataformasdepago');
        return $p->get();
    }

    public function getPropietarioConsorcio($propietario_id) {
        $prop = ClassRegistry::init('Propietario');
        return $prop->getPropietarioConsorcio($propietario_id);
    }

    /*
     * Se utiliza para Estado de Disponibilidad y Resumen Caja Banco
     */

    public function getTotalesMovimientosResumen($consorcio, $desde, $hasta) {
        $caja = ClassRegistry::init('Caja');
        return $caja->getTotalesMovimientosResumen($consorcio, $desde, $hasta);
    }

    /*
     * Obtengo la info del cliente a partir del propietario
     */

    public function obtenerClienteInfo($propietario_id) {
        $consorcio = ClassRegistry::init('Consorcio');
        return $consorcio->getConsorcioClienteInfo($this->getPropietarioConsorcio($propietario_id));
    }

    /*
     * Obtengo la info del Consorcio
     */

    public function getConsorcioInfo($liquidation_id, $client) {
        $liquidation = ClassRegistry::init('Liquidation');
        return $liquidation->getConsorcioInfo($liquidation_id, $client);
    }

    /*
     * Obtengo la info del Propietario
     */

    public function obtenerPropietarioInfo($propietario_id) {
        $prop = ClassRegistry::init('Propietario');
        return $prop->find('first', ['conditions' => ['Propietario.id' => $propietario_id]]);
    }

    /*
     * Obtengo la info del Proveedor
     */

    public function obtenerProveedorInfo($proveedor) {
        $prop = ClassRegistry::init('Proveedor');
        return $prop->find('first', ['conditions' => ['Proveedor.id' => $proveedor], 'recursive' => 0]);
    }

    /*
     * Obtengo la info del cierre de la liquidacion
     */

    public function getLiquidationData($liquidation_id) {
        $model = ClassRegistry::init('Resumene');
        $a = $model->find('first', ['conditions' => ['Resumene.liquidation_id' => $liquidation_id], 'fields' => ['Resumene.data']]);
        return $a;
    }

    /*
     * Obtengo la info de la liquidacion
     */

    public function getLiquidationInfo($liquidation_id, $vienedeopen = false) {
        $liquidation = ClassRegistry::init('Liquidation');
        $a = $liquidation->find('first', ['conditions' => ['Liquidation.id' => $liquidation_id, 'Liquidation.cerrada' => 1],
            'fields' => ['Liquidation.name', 'Liquidation.periodo', 'Liquidation.vencimiento', 'Liquidation.limite', '(select inicial from liquidations l where l.id=Liquidation.liquidation_id limit 1) as inicial', 'LiquidationsType.prefijo', 'LiquidationsType.name', 'Nota.*'],
            'recursive' => 0]);
        if (empty($a) && !$vienedeopen) {
            $this->Controller->Flash->error(__('La liquidacion no se encuentra cerrada'));
            $this->Controller->redirect($this->Controller->referer());
        }
        return $a;
    }

    /*
     * Obtengo la info del propietario (si imprime el resumen o no).
     * $vienedelpanelpropiet es para q cuando intenta abrir el P su resumen de cuenta desde el panel, le deje aunque no est� tildado "Imprime RC".
     * Si nosotros queremos imprimir los RC, ahi si me importa si imprime o no. Online lo ven todos los q tengan "Online"
     */

    public function getPropietariosInfo($liquidation_id, $vienedelpanelpropiet = false, $mostrartodos = false) {
        $liquidation = ClassRegistry::init('Liquidation');
        $propietarios = ClassRegistry::init('Propietario');
        $info = $propietarios->getPropietariosInfo($liquidation->getConsorcioId($liquidation_id));
        if (!$vienedelpanelpropiet && !in_array(true, $info, true) && !$mostrartodos) {
            $this->Controller->Flash->error(__('Ning&uacute;n propietario se encuentra tildado para imprimir'));
            $this->Controller->redirect(array('controller' => 'liquidations', 'action' => 'index'));
        }
        $this->Controller->set('propinfo', $info);
    }

    public function getPropietarioInfo($pid) {
        $propietarios = ClassRegistry::init('Propietario');
        $info = $propietarios->find('first', ['conditions' => ['Propietario.id' => $pid]]);
        $this->Controller->set('propinfo', $info);
    }

    /*
     * Obtengo los ajustes del propietario ordenados por fecha para mostrar en la cuenta corriente propietario (cuentacorrientepropietario())
     */

    public function getPropietariosAjustes($propietario_id) {
        $ajustes = ClassRegistry::init('Ajuste');
        $info = $ajustes->getAjustesPropietario($propietario_id);
        $this->Controller->set('ajustes', $info);
    }

    /*
     * Obtengo la info de los propietarios del consorcio
     */

    public function getPropietarios($liquidation_id) {
        $liquidation = ClassRegistry::init('Liquidation');
        $propietarios = ClassRegistry::init('Propietario');
        $info = $propietarios->getPropietarios($liquidation->getConsorcioId($liquidation_id));
        $this->Controller->set('prop', $info);
    }

    /*
     * Obtengo los saldos del propietario para mostrar en la cuenta corriente
     */

    public function getSaldosPropietario($propietario_id, $f1 = null, $f2 = null, $todas = false) {
        $saldoscierre = ClassRegistry::init('SaldosCierre');
        return $saldoscierre->getSaldosPropietario($propietario_id, $f1, $f2, $todas);
    }

    /*
     * Obtengo los saldos del proveedor para mostrar en la cuenta corriente
     */

    public function getMovimientosProveedor($proveedor_id, $f1 = null, $f2 = null) {
        $mov = ClassRegistry::init('Proveedor');
        return $mov->getMovimientosProveedor($proveedor_id, $f1, $f2);
    }

    /*
     * Obtengo las cobranzas del propietario para mostrar en la cuenta corriente
     */

    public function getCobranzasPropietario($propietario_id) {
        $cobranza = ClassRegistry::init('Cobranza');
        return $cobranza->getCobranzasPropietario($propietario_id);
    }

    /*
     * Obtengo la info de los rubros del consorcio
     */

    public function getRubrosInfo($liquidation_id) {
        $liquidation = ClassRegistry::init('Liquidation');
        $rubro = ClassRegistry::init('Rubro');
        $info = $rubro->getRubrosInfo($liquidation->getConsorcioId($liquidation_id));
        $this->Controller->set('rubrosinfo', $info);
    }

    /*
     * Obtengo la info de los rubros del consorcio
     */

    public function getCuentasInfo($liquidation_id) {
        $liquidation = ClassRegistry::init('Liquidation');
        $cgp = ClassRegistry::init('Cuentasgastosparticulare');
        $info = $cgp->getCuentasInfo($liquidation->getConsorcioId($liquidation_id));
        $this->Controller->set('cuentasinfo', $info);
    }

    /*
     * Obtengo la info de los gastos de la liquidacion
     */

    public function getGastosInfo($liquidation_id) {
        $gastos = ClassRegistry::init('GastosGenerale');
        $info = $gastos->listarGastosPorCoeficiente($liquidation_id);
        $this->Controller->set('gastosinfo', $info);
    }

    /*
     * Obtengo la info de los tipos de liquidación
     */

    public function getLiquidationsTypeInfo($client_id) {
        $lt = ClassRegistry::init('LiquidationsType');
        return $lt->getLiquidationsTypes($client_id);
    }

    /*
     * Obtengo los prefijos x tipo de liq
     */

    public function getLiquidationsTypesPrefijos($client_id) {
        $lt = ClassRegistry::init('LiquidationsType');
        return $lt->getLiquidationsTypesPrefijos($client_id);
    }

    public function getReporteAMostrar($reporte, $client_id) {
        $reportes = ClassRegistry::init('Reportsclient');
        return $reportes->getReporteAMostrar($reporte, $client_id);
    }

    public function getFormasdepago($client_id) {
        $reportes = ClassRegistry::init('Formasdepago');
        return $reportes->get(true, $client_id);
    }

    public function detalleGastosyDisponibilidad($liquidation_id) {
        $l = ClassRegistry::init('Liquidation');
        $last = $l->getLastLiquidation($liquidation_id);
        $this->Controller->set('disponibilidad', $l->getDisponibilidad($last));
        $this->getCuentasInfo($liquidation_id);
        $this->getRubrosInfo($liquidation_id);
        $this->getGastosInfo($liquidation_id);
    }

}
