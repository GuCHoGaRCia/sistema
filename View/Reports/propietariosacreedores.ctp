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
        <title>Listado de acreedores - <?= h($liquidation_type) ?></title>
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
        detalle($propietarios, $dataLiquidacion, $cantidadpropietarios);

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

function detalle($propietarios, $dataLiquidacion, $cantidadpropietarios) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="4" style='text-align:center;font-weight:bold'>PROPIETARIOS ACREEDORES</td></tr>
            <tr>
                <th class="totales" style="width:100px"><b>Unidad (<?= $cantidadpropietarios ?>)</b></th>
                <th class="totales" style="width:250px"><b>Propietario</b></th>
                <th class="totales right" style="width:120px"><b>Capital</b></th>
                <th class="totales right" style="width:120px"><b>Saldo</b></th>
            </tr>
        </thead>
        <?php
        $cerrada = false;
        if (isset($dataLiquidacion['data']) && isset($dataLiquidacion['saldos'])) {
            $ajustesycobranzas = $dataLiquidacion['saldos'];
            $dataLiquidacion = json_decode($dataLiquidacion['data']['Resumene']['data'], true);
        } else {
            $cerrada = true;
            $dataLiquidacion = json_decode($dataLiquidacion['Resumene']['data'], true);
        }

        //debug($dataLiquidacion);

        $sumacapital = 0;

        foreach ($propietarios as $k => $v) {
            $interes = $dataLiquidacion['remanentes'][$k]['interes'];
            $capital = $dataLiquidacion['remanentes'][$k]['capital'];

            if ($cerrada) {
                $saldo = 0;
                $saldo = $interes + $capital;

                if ($saldo >= 0) {
                    continue;
                }
            } else {
                $interes = $dataLiquidacion['saldosanteriores'][$k]['interes'];
                $capital = $dataLiquidacion['saldosanteriores'][$k]['capital'];

                $totalAjustes = 0;
                if (!empty($ajustesycobranzas[$k]['ajustes'])) {
                    foreach ($ajustesycobranzas[$k]['ajustes'] as $j => $va) {
                        $totalAjustes += $va['Ajustetipoliquidacione']['amount'];
                    }
                }
                $cobranzas = $ajustesycobranzas[$k]['cobranzas'];
                $saldo = ($interes + $capital) - $cobranzas - $totalAjustes;

                if ($saldo < 0) {         // NO es deudor, es acreedor
                    $cuenta = $interes;
                    $suma = $cobranzas + $totalAjustes;
                    $cuenta -= $suma;

                    if ($cuenta < 0) {
                        $capital += $cuenta;
                    }
                } else {
                    continue;
                }
            }
            if ($capital >= 0) {
                continue;
            }

            $sumacapital += round($capital, 2);

            echo "<tr><td style='text-align:left'>" . h($v['unidad'] . " (" . $v['code'] . ")") . "</td>";
            echo "<td style='text-align:left'>" . h($v['name']) . "</td>";
            echo "<td style='text-align:right'>" . money($capital) . "</td>";
            echo "<td style='text-align:right'>" . money($capital) . "</td>";   // es la columna saldo que tiene que ser igual a capital porque se saco las columnas de interes y redondeo
            echo "</tr>";
        }
        ?>
        <tr class="totales">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><?= money($sumacapital) ?></td>
            <td><?= money($sumacapital) ?></td>  
        </tr>
    </table>
    <br/>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
