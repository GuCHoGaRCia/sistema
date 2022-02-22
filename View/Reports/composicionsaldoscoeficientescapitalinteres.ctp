<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Composici&oacute;n de saldos - <?= h($consorcio['Consorcio']['name'] . " - " . $info['Liquidation']['periodo']) ?></title>
        <?= $this->Minify->script(['jq']); ?>
        <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']); ?>
        <style type="text/css">
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 9px;
                text-align: left;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                background: none;
            }
            .box-table-a th,.box-table-b th{
                font-size: 8px;
                padding:2px;
                color: #000;
                text-align:center;
            }
            .box-table-a td{
                padding: 2px;
                background: none; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-b td{
                padding: 2px;
                background: none; 
                border-left: 2px solid #aabcfe;
                border-bottom: 1px solid #aabcfe;
                color: #000;
                line-height:10px;
                text-align:right;
            }
            .tdleft{
                padding: 2px;
                background: none; 
                border:none;
                color: #000;
            }
            .right{
                text-align:right;
                width:auto;
            }
            .chico{
                min-width:35px;
            }
            .pri{
                text-align:left;
                width:130px;
                border:none !important;
            }
            .totales{
                border: 2px solid #9baff1;
                font-weight: 700;
                font-size: 13px;
            }
            #print{
                position:absolute;
                right:0;
                cursor:pointer;
            }
            @media print{
                table { page-break-after:auto;}
                tr    { page-break-inside:avoid;}
                td    { page-break-inside:auto;}
                thead { display:table-header-group }
            }
        </style>
        <style type="text/css" media="print">
            @page { size: landscape; }
        </style>
    </head>
    <body>
        <?php
        // si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
        $data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
        $client = $cliente['Client'];
        $consorcio = $consorcio['Consorcio'];
        $notas = $info['Nota'];
        $tipoliquidacion = $info['LiquidationsType']['name'];
        $esinicial = isset($info[0]['inicial']) ? $info[0]['inicial'] : 0;
        $info = $info['Liquidation'];
        // paso los datos del cliente y del consorcio
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($consorcio, $info['periodo'], $datoscliente, $tipoliquidacion);

        // verifico si las cuentas de gastos particulares tienen gastos. Si una cuenta no tiene gastos, no se muestra en la composición
        $ocultar = [];
        $cuentasinfotemp = $cuentasinfotemp2 = $data['gpinfo'] ?? $cuentasinfo;
        if (isset($cuentasinfotemp2)) {
            foreach ($cuentasinfotemp2 as $k => $v) { // cuentas gastos particulares
                $sumacero = true;
                foreach ($data['prop'] as $p) {
                    if (isset($data['totales'][$p['id']]['detalle'])) { // tiene gastos particulares propios del propietario
                        foreach ($data['totales'][$p['id']]['detalle'] as $v1) {
                            if (isset($v1['cuenta']) && $v1['cuenta'] == $k) {
                                $sumacero = false;
                                break;
                            }
                        }
                    }
                    if (isset($data['totales'][$p['id']]['coefpar']) && $sumacero) { // tiene gastos particulares prorrateados
                        foreach ($data['totales'][$p['id']]['coefpar'] as $v1) {
                            if (!isset($v1['detalle'])) {
                                continue;
                            }
                            foreach ($v1['detalle'] as $w) {
                                if (isset($w['cuenta']) && $w['cuenta'] == $k) {
                                    $sumacero = false;
                                    break;
                                }
                            }
                            if (!$sumacero) {
                                break;
                            }
                        }
                    }
                }
                if ($sumacero) {
                    unset($cuentasinfotemp[$k]);
                }
            }
        }

        //oculto las columnas con total cero
        $totalporcentajes = [];
        foreach ($data['descripcioncoeficientes'] as $k => $v) {
            $totalporcentajes[$k] = 0;
        }
        foreach ($data['prop'] as $p) {
            foreach ($data['descripcioncoeficientes'] as $k => $v) { // los valores prorrateados
                if (isset($data['totales'][$p['id']]['coefgen'][$k])) {
                    $totalporcentajes[$k] += round($data['totales'][$p['id']]['coefgen'][$k]['tot'], 2);
                }
            }
        }
        foreach ($totalporcentajes as $k => $v) {
            if ($v == 0) {
                unset($data['descripcioncoeficientes'][$k]);
            }
        }

        detalle($data, $rubrosinfo, $cuentasinfotemp, $data['gastosinfo'], $consorcio['interes'], $esinicial);

        // agrego la nota si existe
        if (strlen(trim($notas['composicion'])) > 0) {
            echo "<span style='width:700px;text-align:center'>" . $notas['composicion'] . "</span>";
        }

        @separacion(55);

        //Obtener cantidad de resumenes, si los detalles de resumen_c exceden un n�mero configurable, ese res�men se duplica.
        $data_bis = array(); //Array donde guardo los bises.
        $max_filas_detalle = 15;
        $numero_resumen = 1;

        function cabecera($consorcio, $periodo, $datoscliente, $tipoliquidacion) {
            ?>
            <table style='font-size:10px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:1050px;max-width:1050px;' class="box-table-a" align="center">
                <tr>
                    <?= $datoscliente ?>
                    <td align="left">
                        <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                        <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                        <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                        <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                        <p style="border-top:2px solid #9baff1;text-align:center;line-height:7px;"><br>
                            <b>COMPOSICI&Oacute;N DE SALDOS - Per&iacute;odo: </b><input type="text" style="border:0;width:300px;text-align:center;font-weight:bold" value="<?= h($periodo) ?>" />
                            <br><b>Tipo: </b><?= h($tipoliquidacion) ?>
                            <br><br>SAC: Saldo Anterior Capital, SAI: Saldo Anterior Interes, RANT: Redondeo Anterior, COB: Cobranzas, AJ: Ajustes, SR: Saldo Remanente, <br><br>GP: Gastos Particulares, 
                            IA: Inter&eacute;s Actual, RACT: Redondeo Actual, Saldo Final: +RANT + SR + [Columnas Gastos] + IA - RACT
                        </p>
                    </td>
                <tr>
            </table>
            <?php
        }

        function detalle($data, $rubrosinfo, $cuentasinfo, $gastosinfo, $consorciointeres, $esinicial) {
            $formato = "style='font-size: 8px; font-family: Verdana, Helvetica, sans-serif;width:1050px;max-width:1050px;'";
            $totalgeneral = 0;
            //debug($cuentasinfo);
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center" id="floatth">
                <thead>
                    <tr>
                        <th class="totales" style="width:50px"><b>Unidad (<?= count($data['prop']) ?>)</b></th>
                        <th class="totales" style="width:250px"><b>Propietario</b></th>
                        <?php
                        $gpart = $gastosgenerales = $totalporcentajes = [];
                        foreach ($data['descripcioncoeficientes'] as $k => $v) {
                            echo "<th class='totales right'><b>% " . h($v) . "</b></th>"; // los %
                            $totalporcentajes[$k] = 0;
                        }
                        ?>
                        <th class="totales right"><b>SAC</b></th>
                        <th class="totales right"><b>SAI</b></th>
                        <th class="totales right"><b>COB</b></th>
                        <th class="totales right"><b>AJ</b></th>
                        <th class="totales right"><b>SR</b></th>
                        <th class="totales right"><b>RANT</b></th>
                        <?php
                        foreach ($data['descripcioncoeficientes'] as $k => $v) {
                            echo "<th class='totales right'><b>" . h($v) . "</b></th>"; // los valores prorrateados
                            $gastosgenerales[$k] = 0;
                        }
                        if (isset($cuentasinfo)) {
                            foreach ($cuentasinfo as $k => $v) { // cuentas gastos particulares
                                echo "<th class='totales right'><b>" . h($v) . "</b></th>";
                                $gpart[$k] = 0;
                            }
                        }
                        ?>
                        <th class="totales right"><b>IA</b></th>
                        <th class="totales right"><b>RACT</b></th>
                        <th class="totales right"><b>Saldo Final</b></th>
                        <th class="totales right"><b>Total Mes</b></th>
                    </tr>
                </thead>
                <?php
                $cc = $ii = 0;
                $saldoanterior = $cobranzas = $totalajustes = $remanente = $particulares = $saldofinaltotal = $intactualtotal = $saldofinalcap = $saldofinalint = $ranteriortotal = $ractualtotal = $totalmesgeneral = 0.00;
                foreach ($data['prop'] as $p) {
                    if (!isset($data['saldosanteriores'][$p['id']]['capital'])) {
                        $data['saldosanteriores'][$p['id']] = ['capital' => 0, 'interes' => 0];
                    }
                    if (!isset($data['remanentes'][$p['id']]['capital'])) {
                        $data['remanentes'][$p['id']] = ['capital' => 0, 'interes' => 0];
                    }
                    $sant = intval($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para hacer las cuentas uso el saldo sin decimales
                    $saldant = ($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para mostrar con decimales
                    $redondeo = round($saldant - intval($saldant), 2);
                    echo "<tr><td style='text-align:left;white-space:nowrap'>" . h($p['unidad'] . " (" . $p['code'] . ")") . "</td>";
                    echo "<td style='text-align:left'>" . h($p['name']) . (!empty($p['estado_judicial']) ? " <b>[" . h($p['estado_judicial']) . "]</b>" : "") . "</td>";
                    foreach ($data['descripcioncoeficientes'] as $k => $v) { // los %
                        $totalporcentajes[$k] += isset($data['totales'][$p['id']]['coefpar'][$k]['val']) ? $data['totales'][$p['id']]['coefpar'][$k]['val'] : $data['totales'][$p['id']]['coefgen'][$k]['val'];
                        echo "<td style='text-align:right;width:auto'>" . (isset($data['totales'][$p['id']]['coefpar'][$k]['val']) ? $data['totales'][$p['id']]['coefpar'][$k]['val'] : $data['totales'][$p['id']]['coefgen'][$k]['val']) . "</td>";
                    }
                    $cc += $data['saldosanteriores'][$p['id']]['capital'] - ($esinicial || $saldant < 0 ? 0 : $redondeo);
                    $ii += $data['saldosanteriores'][$p['id']]['interes'];
                    echo "<td>" . money($data['saldosanteriores'][$p['id']]['capital'] - ($esinicial || $saldant < 0 ? 0 : $redondeo)) . "</td>"; // SAC
                    echo "<td>" . money($data['saldosanteriores'][$p['id']]['interes']) . "</td>"; // SAI
                    $sa = $esinicial || $data['saldosanteriores'][$p['id']]['capital'] < 0 ? $saldant : $sant;
                    $saldoanterior += $esinicial || $data['saldosanteriores'][$p['id']]['capital'] < 0 ? $saldant : $sant;
                    $capint = $data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes'];
                    $sf = $capint > 0 ? intval($capint) : $capint; // si tiene saldo a favor, muestro los decimales
                    $sf = $sf < 0 ? $sf - ($esinicial || $saldant < 0 ? 0.00 : $redondeo) : $sf;
                    $rf = $capint - intval($capint); // redondeo final
                    // sumo las cobranzas
                    $totalcobranzas = 0;
                    foreach ($data['cobranzas'] as $v) {
                        if ($v['Cobranza']['propietario_id'] == $p['id']) {
                            $totalcobranzas += $v['Cobranzatipoliquidacione']['amount'];
                        }
                    }
                    echo "<td>" . money(-$totalcobranzas) . "</td>";
                    $cobranzas += $totalcobranzas;

                    // ajustes
                    $keys = find($data['ajustes'], ['propietario_id' => $p['id']], true);
                    $sumaajustes = 0.00;
                    foreach ($keys as $aj) {
                        $sumaajustes += $data['ajustes'][$aj]['Ajustetipoliquidacione']['amount'];
                    }
                    echo "<td>" . money(-$sumaajustes) . "</td>";
                    $totalajustes += $sumaajustes;

                    // saldo remanente
                    $sr = round($data['remanentes'][$p['id']]['capital'] < 0 ? $data['remanentes'][$p['id']]['capital'] : $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] - ($esinicial ? 0 : $redondeo), 2);

                    // si tiene saldo a favor y las cobranzas son mayores (le queda saldo a favor), entonces tiene q restar el redondeo también
                    $saldrem = ($data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes']);
                    $sr = $data['saldosanteriores'][$p['id']]['capital'] > 0 && $saldrem - $totalcobranzas < 0 && $sr < 0 ? ($saldrem == 0 ? round($saldrem - ($esinicial ? 0.00 : $redondeo), 2) : $saldrem ) : round($sr, 2);
                    if ($sr < 0 && $saldant > 0) {// si el $sr es negativo (sado a favor), tengo q sacarle el redondeo. No poner $sf < 0 || porq sino los q tienen saldo final negativo no le resta RANT (esta en cero)
                        $sr = $esinicial ? $sr : $sr - $redondeo;
                    }
                    echo "<td>" . money($sa - $totalcobranzas - $sumaajustes) . "</td>"; // SR
                    echo "<td class='chico'>" . money($sf < 0 || $esinicial || $saldant < 0 ? 0 : $redondeo) . "</td>"; // RANT
                    $ranteriortotal += $sf < 0 || $esinicial || $saldant < 0 ? 0 : $redondeo; // RANT
                    $remanente += $sa - $totalcobranzas - $sumaajustes;

                    $totalmes = 0;
                    foreach ($data['descripcioncoeficientes'] as $k => $v) { // los valores prorrateados
                        if (isset($data['totales'][$p['id']]['coefgen'][$k])) {
                            $gg = round($data['totales'][$p['id']]['coefgen'][$k]['tot'], 2);
                            echo "<td style='text-align:right;width:90px'>" . money($gg) . "</td>";
                            $gastosgenerales[$k] += $gg;
                            $totalmes += $gg;
                        } else {
                            echo "<td style='text-align:right;width:90px'>0</td>";
                        }
                    }

                    if (isset($cuentasinfo)) {
                        foreach ($cuentasinfo as $k => $v) { // cuentas gastos particulares
                            $actual = 0;
                            if (isset($data['totales'][$p['id']]['detalle'])) { // tiene gastos particulares propios del propietario
                                foreach ($data['totales'][$p['id']]['detalle'] as $v1) {
                                    if (isset($v1['cuenta']) && $v1['cuenta'] == $k) {
                                        $gp = round($v1['total'], 2);
                                        $actual += $gp;
                                        if ($k == 1585) {// solo para MASOLA, 1585 es el fondo operativo para la columna Total Mes, ver con RICARDO
                                            $totalmes += $gp;
                                        }
                                    }
                                }
                            }
                            if (isset($data['totales'][$p['id']]['coefpar'])) { // tiene gastos particulares prorrateados
                                foreach ($data['totales'][$p['id']]['coefpar'] as $v1) {
                                    if (!isset($v1['detalle'])) {
                                        continue;
                                    }
                                    foreach ($v1['detalle'] as $w) {
                                        if (isset($w['cuenta']) && $w['cuenta'] == $k) {
                                            $gpp = round($w['monto'], 2);
                                            $actual += $gpp;
                                            $totalmes += $gpp;
                                        }
                                    }
                                }
                            }

                            $gpart[$k] += $actual;
                            echo "<td>" . money($actual) . "</td>"; // Total gastos particulares para la cuenta actual
                        }
                    }
                    $totalmesgeneral += $totalmes;
                    $intactual = ($p['exceptua_interes'] || $data['remanentes'][$p['id']]['capital'] < 0 ? 0 : $data['saldo'][$p['id']]['interes']);
                    $intremanente = $p['exceptua_interes'] ? 0 : round(abs($intactual - $data['remanentes'][$p['id']]['interes']), 2);
                    $intactualtotal += $intremanente; // IAT

                    echo "<td>" . money($intremanente) . "</td>"; // IA, al IA le resto el IAnterior, despues se suman en SFI
                    // si el saldo es a favor, pongo 0 en R y se lo resto al saldo para q de igual q el resumen
                    echo "<td class='chico'>" . money($sf < 0 ? 0 : -$rf) . "</td>"; // RACT
                    $ractualtotal += ($sf < 0 ? 0 : $rf); // RACT
                    echo "<td>" . money($sf) . "</td>"; // Saldo final
                    echo "<td>" . money($totalmes) . "</td>";
                    $saldofinaltotal += $sf; // SFT
                    echo "</tr>";
                }

                // muestro los totales
                echo "<tr style='font-weight:bold'><td colspan='2' style='width:300px;text-align:center'>TOTALES</td>";
                foreach ($data['descripcioncoeficientes'] as $k => $v) { // los %
                    echo "<td>" . $totalporcentajes[$k] . "%</td>";
                }
                echo "<td>" . money($cc) . "</td>";
                echo "<td>" . money($ii) . "</td>";
                echo "<td>" . money(-$cobranzas) . "</td>";
                echo "<td>" . money(-$totalajustes) . "</td>";
                echo "<td>" . money($remanente) . "</td>";
                echo "<td>" . money($ranteriortotal) . "</td>"; // RANTERIOR
                foreach ($data['descripcioncoeficientes'] as $k => $v) { // los valores prorrateados
                    echo "<td>" . money($gastosgenerales[$k]) . "</td>";
                }
                if (isset($cuentasinfo)) {
                    foreach ($cuentasinfo as $k => $v) { // gastos particulares
                        echo "<td>" . money($gpart[$k]) . "</td>";
                    }
                }

                echo "<td>" . money($intactualtotal) . "</td>";
                echo "<td>" . money(-$ractualtotal) . "</td>"; // RACTUAL
                echo "<td>" . money($saldofinaltotal) . "</td>";
                echo "<td>" . money($totalmesgeneral) . "</td>";
                echo "</tr>";
                ?>
            </table>
            <br/>
        </body>
    </html>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}

function find($lista, $valor, $all = false) {
    $key = array_keys($valor);
    $value = array_values($valor);
    $resul = [];

    foreach ($lista as $k => $v) {
        $indice = array_keys($v);
        if ($v[$indice[0]][$key[0]] == $value[0]) {
            if ($all) {
                $resul[] = $k;
            } else {
                return $k;
            }
        }
    }
    return $resul;
}
