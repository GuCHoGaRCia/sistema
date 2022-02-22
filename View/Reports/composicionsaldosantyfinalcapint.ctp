<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Composici&oacute;n de saldos - <?= h($consorcio['Consorcio']['name'] . " - " . $info['Liquidation']['periodo']) ?></title>
        <?= $this->Minify->script(['jq', 'floatth']); ?>
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
    </head>
    <body>
        <!--img src="/sistema/img/print2.png" id="print" /-->
        <?php
        // si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
        $data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
        $client = $cliente['Client'];
        $consorcio = $consorcio['Consorcio'];
        $notas = $info['Nota'];
        $tipoliquidacion = $info['LiquidationsType']['name'];
        $info = $info['Liquidation'];

        // paso los datos del cliente y del consorcio
        cabecera($client, $consorcio, $info['periodo'], $tipoliquidacion);
        detalle($data, $rubrosinfo, $data['gastosinfo'], $consorcio['interes']);

        // agrego la nota si existe
        if (strlen(trim($notas['composicion'])) > 0) {
            echo "<span style='width:700px;text-align:center'>" . $notas['composicion'] . "</span>";
        }

        @separacion(55);

        //Obtener cantidad de resumenes, si los detalles de resumen_c exceden un n�mero configurable, ese res�men se duplica.
        $data_bis = array(); //Array donde guardo los bises.
        $max_filas_detalle = 15;
        $numero_resumen = 1;

        function cabecera($client, $consorcio, $periodo, $tipoliquidacion) {
            ?>
            <table style='font-size:10px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:1050px;max-width:1050px;' class="box-table-a" align="center">
                <thead>
                    <tr>
                        <td width="130" height="80" rowspan="3" align="center">
                            <img alt="logo" width=100 height=100 src="/sistema/<?= file_exists("files/" . $client['id'] . "/" . $client['id'] . ".jpg") ? "files/" . $client['id'] . "/" . $client['id'] . ".jpg" : "img/0000.png" ?>">
                        </td>
                        <td width="360" rowspan="3" align="left" valign="middle">
                            ADMINISTRACION<br/><font size=4><?= h($client['name']) ?></font>
                            <br><?= h($client['address']) ?>
                            <br><?= h($client['city']) ?>
                            <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                            <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                            <br/>Tel.: <?= h(!empty($client['telephone']) ? $client['telephone'] : '--') ?>
                            <br/>Email: <?= h(!empty($client['email']) ? $client['email'] : '--') ?>
                        </td>
                        <td align="left" height="70" rowspan="3">
                            <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                            <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                            <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                            <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                            <p style="border-top:2px solid #9baff1;text-align:center;line-height:7px;"><br>
                                <b>COMPOSICI&Oacute;N DE SALDOS - Per&iacute;odo: </b><input type="text" style="border:0;width:300px;text-align:center;font-weight:bold" value="<?= h($periodo) ?>" />
                                <br><b>Tipo: </b><?= h($tipoliquidacion) ?>
                                <br><br>SA: Saldo Anterior, C: Capital, RA: Redondeo Anterior, I: Inter&eacute;s, COB: Cobranzas, AJ: Ajustes, SR: Saldo Remanente, <br><br>GP: Gastos Particulares, IA: Interes Actual,
                                , SF: Saldo Final, R: redondeo, Saldo Final: SR + [Columnas Gastos] + IA + RA - R
                            </p>
                        </td>
                    </tr>
                </thead>
            </table>
            <?php
        }

        function detalle($data, $rubrosinfo, $gastosinfo, $consorciointeres) {
            $formato = "style='font-size: 8px; font-family: Verdana, Helvetica, sans-serif;width:1050px;max-width:1050px;'";
            $totalgeneral = 0;
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center" id="floatth">
                <thead>
                    <tr>
                        <th class="totales" style="width:50px"><b>Unidad (<?= count($data['prop']) ?>)</b></th>
                        <th class="totales" style="width:250px"><b>Propietario</b></th>
                        <th class="totales right"><b>SAC</b></th>
                        <th class="totales right"><b>RA</b></th>
                        <th class="totales right"><b>SAI</b></th>
                        <th class="totales right"><b>COB</b></th>
                        <th class="totales right"><b>AJ</b></th>
                        <th class="totales right"><b>SR</b></th>
                        <?php
                        foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                            echo "<th class='totales right'><b>" . h($v) . "</b></th>";
                            $gastosgenerales[$k] = 0;
                        }
                        ?>
                        <th class="totales right"><b>GP</b></th>
                        <th class="totales right"><b>IA</b></th>
                        <th class="totales right"><b>RA</b></th>
                        <th class="totales right"><b>SFC</b></th>
                        <th class="totales right"><b>SFI</b></th>
                        <th class="totales right chico"><b>R</b></th>
                        <th class="totales right"><b>Saldo Final</b></th>
                    </tr>
                </thead>
                <?php
                $redondeoinicial = $cobranzas = $redondeoactual = $totalajustes = $remanente = $particulares = $saldofinaltotal = $capital = $interes = $intactualtotal = $saldofinalcap = $saldofinalint = $redondeototal = 0;
                foreach ($data['prop'] as $p) {
                    if (!isset($data['saldosanteriores'][$p['id']]['capital'])) {
                        $data['saldosanteriores'][$p['id']] = ['capital' => 0, 'interes' => 0];
                    }
                    if (!isset($data['remanentes'][$p['id']]['capital'])) {
                        $data['remanentes'][$p['id']] = ['capital' => 0, 'interes' => 0];
                    }
                    $redondeo = round($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes'] - floor($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']), 2);
                    echo "<tr><td style='text-align:left'>" . h($p['unidad']) . "</td>";
                    echo "<td style='text-align:left'>" . h($p['name']) . (!empty($p['estado_judicial']) ? " <b>[" . h($p['estado_judicial']) . "]</b>" : "") . "</td>";
                    //echo "<td>" . money(floor($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes'])) . "</td>"; // SAC + SAI
                    echo "<td>" . money($data['saldosanteriores'][$p['id']]['capital'] < 0 ? $data['saldosanteriores'][$p['id']]['capital'] : ($data['saldosanteriores'][$p['id']]['capital'])) . "</td>"; // SAC
                    $redondeoinicial += $data['saldosanteriores'][$p['id']]['capital'] < 0 ? 0 : $redondeo;
                    echo "<td>" . ($data['saldosanteriores'][$p['id']]['capital'] < 0 ? 0 : money(-$redondeo)) . "</td>"; // RC
                    echo "<td>" . money($data['saldosanteriores'][$p['id']]['interes']) . "</td>"; // SAI

                    $capital += $data['saldosanteriores'][$p['id']]['capital'];
                    $interes += $data['saldosanteriores'][$p['id']]['interes'];

                    // sumo las cobranzas
                    $totalcobranzas = 0;
                    foreach ($data['cobranzas'] as $v) {
                        if ($v['Cobranza']['propietario_id'] == $p['id']) {
                            $totalcobranzas += $v['Cobranzatipoliquidacione']['amount'];
                        }
                    }
                    echo "<td>" . money($totalcobranzas) . "</td>";
                    $cobranzas += $totalcobranzas;

                    // ajustes
                    $keys = find($data['ajustes'], ['propietario_id' => $p['id']], true);
                    $sumaajustes = 0;
                    foreach ($keys as $aj) {
                        $sumaajustes += $data['ajustes'][$aj]['Ajustetipoliquidacione']['amount'];
                    }
                    echo "<td>" . money($sumaajustes) . "</td>";
                    $totalajustes += $sumaajustes;

                    $saldofinal = 0;
                    // saldo remanente
                    $sr = $data['remanentes'][$p['id']]['capital'] < 0 ? $data['remanentes'][$p['id']]['capital'] /* - ($data['saldosanteriores'][$p['id']]['capital'] < 0 ? 0 : $redondeo) */ : $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] - $redondeo;
                    // si tiene saldo a favor y las cobranzas son mayores (le queda saldo a favor), entonces tiene q restar el redondeo también
                    //                  si debia algo,                     y pagó de más
                    $redondeoasumar = $data['saldosanteriores'][$p['id']]['capital'] > 0 && $totalcobranzas > 0 && $totalcobranzas + $sumaajustes > $data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes'] ? 0 : $redondeo;

                    echo "<td>" . money($sr) . "</td>"; // SR
                    $saldofinal += $data['remanentes'][$p['id']]['capital'];
                    $remanente += $sr;
                    $remanenteActual = /* $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] */ $sr; // para el SFC
                    //debug($data['descripcioncoeficientes']);die;

                    foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                        if (isset($data['totales'][$p['id']]['coefgen'][$k])) {
                            $gg = round($data['totales'][$p['id']]['coefgen'][$k]['tot'], 2);
                            echo "<td style = 'text-align:right;width:100px'>" . money($gg) . "</td>";
                            $saldofinal += $gg;
                            $gastosgenerales[$k] += $gg;
                            $remanenteActual += $gg;
                        } else {
                            echo "<td style = 'text-align:right;width:100px'>0</td>";
                        }
                    }
                    $gastosparticulares = 0;
                    if (isset($data['totales'][$p['id']]['detalle'])) { // tiene gastos particulares propios del propietario
                        foreach ($data['totales'][$p['id']]['detalle'] as $v) {
                            $gp = round($v['total'], 2);
                            $gastosparticulares += $gp;
                            $remanenteActual += $gp;
                        }
                    }
                    if (isset($data['totales'][$p['id']]['coefpar'])) { // tiene gastos particulares prorrateados
                        foreach ($data['totales'][$p['id']]['coefpar'] as $v) {
                            foreach ($v['detalle'] as $w) {
                                $gpp = round($w['monto'], 2);
                                $gastosparticulares += $gpp;
                                $remanenteActual += $gpp;
                            }
                        }
                    }
                    echo "<td>" . money($gastosparticulares) . "</td>"; // GP
                    $saldofinal += $gastosparticulares;
                    $particulares += $gastosparticulares;
                    // $intactual es el interes actual mas el anterior (el q se guarda en saldos_cierre)
                    $intactual = ($p['exceptua_interes'] || $data['remanentes'][$p['id']]['capital'] < 0 ? 0 : $data['saldo'][$p['id']]['interes']);
                    echo "<td>" . ($p['exceptua_interes'] ? 0 : money(abs($intactual - $data['remanentes'][$p['id']]['interes']))) . " </td>"; // IA... al IA le resto el IAnterior, despues se suman en SFI
                    echo "<td>" . money($redondeoasumar) . "</td>"; // RA
                    echo "<td>" . money($data['saldo'][$p['id']]['capital'] > 0 ? $data['saldo'][$p['id']]['capital'] : $data['saldo'][$p['id']]['capital']) . "</td>"; // SFC (Muestro saldo capital q es lo q se guarda internamente. (no va $sf). Dejar asi!
                    echo "<td>" . money($data['saldo'][$p['id']]['interes']) . "</td>"; // SFI
                    $capint = $data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes']; // uso el C e I q tengo guardado en saldos_cierre
                    $sf = $capint > 0 ? floor($capint) : $capint; // si tiene saldo a favor, muestro los decimales
                    echo "<td class = 'chico'>" . ($capint > 0 ? money(round($capint - floor($capint), 2)) : 0) . "</td>"; // R
                    echo "<td>" . money($sf) . "</td>"; // Saldo final
                    $intactualtotal += round(($p['exceptua_interes'] ? 0 : abs($intactual - $data['remanentes'][$p['id']]['interes'])), 2); // IAT
                    $redondeoactual += $redondeoasumar;
                    $saldofinalcap += round($data['saldo'][$p['id']]['capital'], 2); //SFCT
                    $saldofinalint += round($data['saldo'][$p['id']]['interes'], 2); // SFIT
                    $redondeototal += round($capint > 0 ? round($capint - floor($capint), 2) : 0); // RT
                    $saldofinaltotal += $sf; // SFT
                    echo "</tr>";
                }

                // muestro los totales
                echo "<tr style='font-weight:bold'><td colspan = '2' style = 'width:300px;text-align:center'>TOTALES</td>";
                echo "<td>" . money($capital) . "</td>";
                echo "<td>" . money(-$redondeoinicial) . "</td>";
                echo "<td>" . money($interes) . "</td>";
                echo "<td>" . money($cobranzas) . "</td>";
                echo "<td>" . money($totalajustes) . "</td>";
                echo "<td>" . money($remanente) . "</td>";
                foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                    echo "<td>" . money($gastosgenerales[$k]) . "</td>";
                }
                echo "<td>" . money($particulares) . "</td>";
                echo "<td>" . money($intactualtotal) . "</td>";
                echo "<td>" . money($redondeoactual) . "</td>";
                echo "<td>" . money($saldofinalcap) . "</td>";
                echo "<td>" . money($saldofinalint) . "</td>";
                echo "<td>" . money($redondeototal) . "</td>";
                echo "<td>" . money($saldofinaltotal) . "</td></tr>";
                ?>
            </table>
            <br/>
        </body></html>
    <?php
    /*
      <script>
      var $table = $('table.box-table-b');
      $table.floatThead();
      $("#print").on("click", function () {
      $table.floatThead('destroy');
      $("#print").hide();
      window.print();
      $table.floatThead();
      $("#print").show();
      });
      </script>
     */
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
        if ($v[$indice[0]][$key[0]] == $value [0]) {
            if ($all) {
                $resul[] = $k;
            } else {
                return $k;
            }
        }
    }
    return $resul;
}
