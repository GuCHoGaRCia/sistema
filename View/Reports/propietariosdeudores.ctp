<?php
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
$notas = $info['Nota'];
$liquidation_type = $info['LiquidationsType']['name'];
$periodo = $info['Liquidation']['periodo'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Listado de deudores - <?= h($liquidation_type) ?></title>
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
                font-size: 10px;
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
                line-height:14px;
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
        </style>
    </head>
    <body>
        <?php
        // paso los datos del cliente y del consorcio
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($consorcio, h($liquidation_type), h($periodo), $datoscliente);
        detalle($propietarios, $dataLiquidacion, $cantidadpropietarios, $valordesdereportepropdeudor);

        @separacion(55);
        ?>
    </body>
</html>
<?php

function cabecera($consorcio, $titulo, $periodo, $datoscliente) {
    ?>
    <table style='font-size:11px;width:750px;max-width:750px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <td align="left">
                <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <p style="border-top:2px solid #9baff1;text-align:center;line-height:14px;"><br>
                    <b>Tipo de Liquidaci&oacute;n: </b><?= h($titulo) ?>
                    <br><br>
                    <b>Per&iacute;odo: </b><?= h($periodo) ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function detalle($propietarios, $dataLiquidacion, $cantidadpropietarios, $valordesdereportepropdeudor) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    ?>
    <table border="2px" valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="5" style='text-align:center;font-weight:bold'>PROPIETARIOS DEUDORES</td></tr>
            <tr>
                <th class="totales" style="width:100px"><b>Unidad (<?= $cantidadpropietarios ?>)</b></th>
                <th class="totales" style="width:250px"><b>Propietario</b></th>
                <th class="totales right" style="width:120px"><b>Capital</b></th>
                <th class="totales right" style="width:120px"><b>Inter&eacute;s</b></th>
                <th class="totales right" style="width:120px"><b>Saldo</b></th>
            </tr>
        </thead>
        <?php
        $cerrada = false;
        if (isset($dataLiquidacion['saldos'])) {
            $ajustesycobranzas = $dataLiquidacion['saldos'];
            if (!empty($dataLiquidacion['data'])) {
                $dataLiquidacion = json_decode($dataLiquidacion['data']['Resumene']['data'], true);
            }
        } else {
            $cerrada = true;
            $dataLiquidacion = json_decode($dataLiquidacion['Resumene']['data'], true);
        }

        $sumacapital = $sumainteres = 0;

        foreach ($propietarios as $k => $v) {
            if (isset($dataLiquidacion['remanentes'])) {
                $capital = $dataLiquidacion['remanentes'][$k]['capital'];
                $interes = $dataLiquidacion['remanentes'][$k]['interes'];
            } else {
                $interes = $capital = 0;
            }

            if ($cerrada) {
                $saldo = $capital + $interes;                        // seria la columna saldo en el reporte
                if ($saldo < $valordesdereportepropdeudor) {         // NO se muestra
                    continue;
                }
            } else {
                $interes = $dataLiquidacion['saldosanteriores'][$k]['interes'];
                $capital = $dataLiquidacion['saldosanteriores'][$k]['capital'];

                $totalAjustes = 0;
                if (!empty($ajustesycobranzas)) {
                    if (!empty($ajustesycobranzas[$k]['ajustes'])) {
                        foreach ($ajustesycobranzas[$k]['ajustes'] as $j => $va) {
                            $totalAjustes += $va['Ajustetipoliquidacione']['amount'];
                        }
                    }
                }
                $cobranzas = $ajustesycobranzas[$k]['cobranzas'];
                $saldo = ($capital + $interes) - $cobranzas - $totalAjustes;

                if ($saldo > $valordesdereportepropdeudor) {         // es deudor y se muestra
                    $cuenta = $interes;
                    $suma = $cobranzas + $totalAjustes;
                    $cuenta -= $suma;

                    if ($cuenta < 0) {
                        $capital += $cuenta;
                        $interes = 0;
                    } else {
                        $interes = $cuenta;
                    }
                } else {
                    continue;
                }
            }

//            if (($capital + $interes) < 1) {
//                continue;
//            }
            $sumacapital += round($capital, 2);
            $sumainteres += round($interes, 2);

            echo "<tr id='$k'><td style='text-align:left'><span title = 'Click para borrar' style='cursor:pointer' " . "onclick='recalcula($k, $capital, $interes)'>" . h($v['unidad'] . " (" . $v['code'] . ")") . "</span></td>";
            echo "<td style='text-align:left'>" . h($v['name']) . "</td>";
            echo "<td style='text-align:right'>" . money($capital) . "</td>";
            echo "<td style='text-align:right'>" . money($interes) . "</td>";
            echo "<td style='text-align:right'>" . money(round($capital, 2) + round($interes, 2)) . "</td>"; // columna saldo
            echo "</tr>";
        }
        $sumatotal = $sumacapital + $sumainteres;
        ?>
        <tr class="totales">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><span id="sumacapital"><?= money($sumacapital) ?></span></td>
            <td><span id="sumainteres"><?= money($sumainteres) ?></span></td>   
            <td><span id="sumatotal"><?= money($sumacapital + $sumainteres) ?></span></td> 
        </tr>
    </table>
    <br/>

    <script>
        var sumacapital = <?= isset($sumacapital) ? $sumacapital : 0 ?>;
        var sumainteres = <?= isset($sumainteres) ? $sumainteres : 0 ?>;
        var sumatotal = <?= isset($sumatotal) ? $sumatotal : 0 ?>;
        function recalcula(id, capital, interes) {
            $("#" + id).hide();
            sumacapital -= capital;
            sumainteres -= interes;
            sumatotal -= (capital + interes);

            $("#sumacapital").html(sumacapital.toFixed(2).replace('.', ','));
            $("#sumainteres").html(sumainteres.toFixed(2).replace('.', ','));
            $("#sumatotal").html(sumatotal.toFixed(2).replace('.', ','));
        }
    </script>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
