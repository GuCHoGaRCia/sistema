<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$a = (!empty($data['Resumene']['data']) ? $data['Resumene']['data'] : $data);
if (empty($data)) {
    echo "";
} else {
    $data = json_decode($a, true);
    ?>
    <!DOCTYPE html>
    <html lang="es-419">
        <head>
            <title>Resumen del Per&iacute;odo - <?= h($consorcio['Consorcio']['name']) ?></title>
            <?= $this->Minify->script(['jq']); ?>
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
                    border-top: 2px solid #9baff1 !important; 
                    color: #000;
                }
                .box-table-b td{
                    padding: 2px;
                    background: none; 
                    border-left: 2px solid #aabcfe;
                    border-bottom: 1px solid #aabcfe;
                    color: #000;
                    line-height:10px;
                    text-align:left;
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
                    font-size: 14px;
                }
                #print{
                    position:absolute;
                    right:0;
                    cursor:pointer;
                }
            </style>
        </head>
        <body>
            <?php
            $gastosgenerales = [];
            foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                $gastosgenerales[$k] = 0;
            }
            $gpart = [];
            if (isset($cuentasinfo)) {
                foreach ($cuentasinfo as $k => $v) { // cuentas gastos particulares
                    $gpart[$k] = 0;
                }
            }
            $cuentasinfotemp = $cuentasinfo;
            if (isset($cuentasinfo)) {
                foreach ($cuentasinfo as $k => $v) { // cuentas gastos particulares
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
            $saldoanterior = $cobranzas = $totalajustes = $remanente = $particulares = $saldofinaltotal = $intactualtotal = $saldofinalcap = $saldofinalint = $ranteriortotal = $ractualtotal = 0.00;
            foreach ($data['prop'] as $p) {
                $sant = intval($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para hacer las cuentas uso el saldo sin decimales
                $saldant = ($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para mostrar con decimales
                $redondeo = $saldant - intval($saldant);
                $saldoanterior += $info[0]['inicial'] || $data['saldosanteriores'][$p['id']]['capital'] < 0 ? $saldant : $sant;
                $capint = $data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes'];
                $sf = $capint > 0 ? intval($capint) : $capint; // si tiene saldo a favor, muestro los decimales
                $sf = $sf < 0 ? $sf - ($info[0]['inicial'] || $saldant < 0 ? 0.00 : $redondeo) : $sf;
                $saldofinaltotal += $sf;
                $rf = $capint - intval(($capint)); // redondeo final
                // sumo las cobranzas
                $totalcobranzas = 0;
                foreach ($data['cobranzas'] as $v) {
                    if ($v['Cobranza']['propietario_id'] == $p['id']) {
                        $totalcobranzas += round($v['Cobranzatipoliquidacione']['amount'], 2);
                    }
                }
                $cobranzas += $totalcobranzas;

                // saldo remanente
                $sr = round($data['remanentes'][$p['id']]['capital'] < 0 ? $data['remanentes'][$p['id']]['capital'] : $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] - ($info[0]['inicial'] ? 0 : $redondeo), 2);

                // si tiene saldo a favor y las cobranzas son mayores (le queda saldo a favor), entonces tiene q restar el redondeo también
                $saldrem = ($data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes']);
                $sr = $data['saldosanteriores'][$p['id']]['capital'] > 0 && $saldrem - $totalcobranzas < 0 && $sr < 0 ? ($saldrem == 0 ? round($saldrem - ($info[0]['inicial'] ? 0.00 : $redondeo), 2) : $saldrem ) : round($sr, 2);
                if ($sr < 0 && $saldant > 0) {// si el $sr es negativo (sado a favor), tengo q sacarle el redondeo. No poner $sf < 0 || porq sino los q tienen saldo final negativo no le resta RANT (esta en cero)
                    $sr = $info[0]['inicial'] ? $sr : $sr - $redondeo;
                }
                $remanente += $sr;

                foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                    if (isset($data['totales'][$p['id']]['coefgen'][$k])) {
                        $gg = $data['totales'][$p['id']]['coefgen'][$k]['tot'];
                        $gastosgenerales[$k] += $gg;
                    }
                }

                if (isset($cuentasinfo)) {
                    foreach ($cuentasinfo as $k => $v) { // cuentas gastos particulares
                        $actual = 0;
                        if (isset($data['totales'][$p['id']]['detalle'])) { // tiene gastos particulares propios del propietario
                            foreach ($data['totales'][$p['id']]['detalle'] as $v1) {
                                if (isset($v1['cuenta']) && $v1['cuenta'] == $k) {
                                    $gp = $v1['total'];
                                    $actual += $gp;
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
                                        $gpp = $w['monto'];
                                        $actual += $gpp;
                                    }
                                }
                            }
                        }
                        $gpart[$k] += $actual;
                    }
                }

                // si el saldo es a favor, pongo 0 en R y se lo resto al saldo para q de igual q el resumen
                $ranteriortotal += $sf < 0 || $info[0]['inicial'] || $saldant < 0 ? 0 : $redondeo; // RANT
                $ractualtotal += $sf < 0 ? 0 : $rf; // RACT
            }
            ?>
            <table style='font-size:11px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:751px;max-width:751px;border-bottom:0px' class="box-table-a" align="center">
                <tr>
                    <?= $this->element('datoscliente', ['dato' => $cliente['Client']]) ?>
                    <?= $this->element('datosconsorcio', ['dato' => $consorcio['Consorcio']]) ?>
                </tr>
            </table>
            <?php
            $formato = "style='font-size:11px; font-family:Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
            $totalgeneral = 0;
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
                <tr class="totales" style="font-size:15px">
                <tr>
                    <td class="totales" style="width:740px;text-align:center;padding:10px" colspan="4">
                        <b>Resumen del Per&iacute;odo: </b><?= h($info['Liquidation']['periodo']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="totales" style='text-align:left'>
                        <b>Concepto</b>
                    </td>
                    <td class="totales" style="width:100px;text-align:right">
                        <b>Debe</b>
                    </td>
                    <td class="totales" style="width:100px;text-align:right">
                        <b>Haber</b>
                    </td>
                    <td class="totales" style="width:100px;text-align:right">
                        <b>Saldo</b>
                    </td>
                </tr>
            </tr>
            <?php
            $saldototal = $saldoanterior;
            echo "<tr><td style='text-align:left'><b>Expensas per&iacute;odo anterior</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($saldoanterior) . "</td><td></td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";
            // sumo las cobranzas
            $tc = $ti = 0;
            // interes actual
            $intactual = $intremanente = 0;
            foreach ($data['prop'] as $w => $x) {
                foreach ($data['cobranzas'] as $v) {
                    if ($v['Cobranza']['propietario_id'] == $x['id']) {
                        if (!isset($cap[$x['id']])) {// para calcular cap e int cobrados
                            $cap[$x['id']] = (float) $data['saldo'][$x['id']]['capant'];
                            $int[$x['id']] = (float) $data['saldo'][$x['id']]['intant'];
                        }

                        foreach ($ajustes as $a) {
                            if ($a['Ajuste']['propietario_id'] == $x['id']) {
                                $aj = $a['Ajustetipoliquidacione']['amount'];
                                if ($a['Ajustetipoliquidacione']['solocapital']) {
                                    $cap[$x['id']] -= $aj;
                                } else {
                                    $auxinteres = $int[$x['id']];
                                    // si el interes quedó negativo, lo pongo en cero, sino hago interes - ajuste
                                    $int[$x['id']] = ($int[$x['id']] - $aj < 0) ? 0 : $int[$x['id']] - $aj;
                                    $aj -= $auxinteres;
                                    $cap[$x['id']] = ($aj > 0) ? $cap[$x['id']] - $aj : $cap[$x['id']];
                                }
                            }
                        }

                        $c = $i = 0;
                        if ($v['Cobranzatipoliquidacione']['solocapital'] || (float) $int[$x['id']] == 0) {
                            $c = $v['Cobranzatipoliquidacione']['amount'];
                        } else {
                            $totalcobranza = $v['Cobranzatipoliquidacione']['amount'];
                            $i = $int[$x['id']] - $totalcobranza > 0 ? $totalcobranza : $int[$x['id']];
                            $totalcobranza -= $i;
                            $int[$x['id']] -= $i;
                            $cap[$x['id']] -= $c;
                            $c = $v['Cobranzatipoliquidacione']['amount'] - $i;
                        }
                        $tc += $c;
                        $ti += $i;
                    }
                }
                $intactual = ($x['exceptua_interes'] || $data['remanentes'][$x['id']]['capital'] < 0 ? 0 : $data['saldo'][$x['id']]['interes']);
                $intremanente = $x['exceptua_interes'] ? 0 : round(abs($intactual - $data['remanentes'][$x['id']]['interes']), 2);
                $intactualtotal += $intremanente; // IAT
            }
            echo "<tr><td style='text-align:left'><b>Cobranzas</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cobranzas Capital</td>";
            $saldototal -= $tc;
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($tc) . "</td><td></td></tr>";
            echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cobranzas Inter&eacute;s</td>";
            $saldototal -= $ti;
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($ti) . "</td><td></td></tr>";
            echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Cobranzas</td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($tc + $ti) . "</td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";

            // muestro ajustes
            //foreach ($data['ajustes'] as $v) {
            //    $ajustes += isset($v['Ajustetipoliquidacione']['amount']) ? $v['Ajustetipoliquidacione']['amount'] : 0;
            //}
            // ajustes
            $sumaajustes = 0.00;
            foreach ($data['prop'] as $w => $x) {
                $keys = find($data['ajustes'], ['propietario_id' => $x['id']], true);
                foreach ($keys as $aj) {
                    $sumaajustes += $data['ajustes'][$aj]['Ajustetipoliquidacione']['amount'];
                }
            }
            echo "<tr><td style='text-align:left'><b>Ajustes</b></td>";
            $saldototal -= $sumaajustes;
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($sumaajustes) . "</td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";


            echo "<tr><td style='text-align:left'><b>Saldo periodo anterior</b></td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";


            // gastos generales
            echo "<tr><td style='text-align:left'><b>Gastos Generales</b><td>&nbsp;</td><td>&nbsp;</td></td><td></td></tr>";
            $gg = 0;
            foreach ($data['descripcioncoeficientes'] as $k => $v) { // gastos generales
                echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . h($v) . "</td>";
                $gg += $gastosgenerales[$k];
                echo "<td style='text-align:right'>" . $this->Functions->money($gastosgenerales[$k]) . "</td><td></td><td></td></tr>";
            }
            echo "<tr><td style='text-align:left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Gastos Generales</td>";
            $saldototal += $gg;
            echo "<td style='text-align:right'>" . $this->Functions->money($gg) . "</td><td></td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";

            // gastos particulares
            echo "<tr><td style='text-align:left'><b>Gastos Particulares</b><td>&nbsp;</td><td>&nbsp;</td></td><td></td></tr>";
            $gp = 0;
            if (isset($cuentasinfo)) {
                foreach ($cuentasinfo as $k => $v) { // cuentas gastos 
                    if ($gpart[$k] > 0) {
                        echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . h($v) . "</td>";
                        $gp += $gpart[$k];
                        echo "<td style='text-align:right'>" . $this->Functions->money($gpart[$k]) . "</td><td></td><td></td></tr>";
                    }
                }
            }
            echo "<tr><td style='text-align:left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Gastos Particulares</td>";
            $saldototal += $gp;
            echo "<td style='text-align:right'>" . $this->Functions->money($gp) . "</td><td></td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";

            echo "<tr><td><b>Interes Actual</b></td>";
            $saldototal += $intactualtotal;
            echo "<td style='text-align:right'>" . $this->Functions->money($intactualtotal) . "</td><td></td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";

            // redondeos
            echo "<tr><td style='text-align:left'><b>Redondeos</b><td>&nbsp;</td><td>&nbsp;</td></td><td></td></tr>";
            echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Redondeo anterior</td>";
            $saldototal += $ranteriortotal;
            echo "<td style='text-align:right'>" . $this->Functions->money($ranteriortotal) . "</td><td></td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";
            echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Redondeo actual</td>";
            $saldototal -= $ractualtotal;
            echo "<td></td><td style='text-align:right'>" . $this->Functions->money($ractualtotal) . "</td><td style='text-align:right'><b>" . $this->Functions->money($saldototal) . "</b></td></tr>";

            // $saldototal es != a $saldofinaltotal, como en la composicion de saldos
            // muestro los totales
            echo "<tr><td style='width:300px;text-align:center' class='totales'><b>Saldo periodo actual<b></td>";
            echo "<td class='totales'></td><td class='totales'></td><td class='totales' style='text-align:right'>" . $this->Functions->money($saldofinaltotal) . "</td></tr>";
        }
        ?>
    </table>
    <br/>
    <?= "<div style='page-break-after:always'></div>" ?>
</body>
</html>
<?php
/*
  <script>
  $("#print").on("click", function () {
  $("#print").hide();
  window.print();
  $("#print").show();
  }); </script>
 */

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
