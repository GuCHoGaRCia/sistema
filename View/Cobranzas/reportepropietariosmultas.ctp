<?php
$consorcio = $consorcio['Consorcio'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Reporte Multas</title>
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
                font-size: 12px;
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
        cabecera($consorcio, h($liquidationTypeName));
        detalle($liquidaciones, $propCapitalInteres, $multa, $propietario_id);
        ?>
    </body>
</html>
<?php

function cabecera($consorcio, $titulo) {
    ?>
    <table style='font-size:12px;width:850px;max-width:850px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <td align="left">
                <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <p style="border-top:2px solid #9baff1;text-align:center;line-height:14px;"><br>
                    <b>Tipo de Liquidaci&oacute;n: </b><?= h($titulo) ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function detalle($liquidaciones, $propCapitalInteres, $multa, $propietario_id) {

    $reversed = array_reverse($liquidaciones, true);
    $primerelemento = reset($reversed);

    $p = $propCapitalInteres[$primerelemento['id']]['propietarios'][$propietario_id];
    $nombre = ' - ' . $p['name'] . ' - ' . $p['unidad'] . ' (' . $p['code'] . ')';

    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;width:850px;max-width:850px;'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="4" style='text-align:center;font-size:11px;font-weight:bold'>PROPIETARIO DEUDOR <?= h($nombre) ?></td></tr>
            <tr>
                <th class="totales" style="width:auto"><b>Liquidaci&oacute;n</b></th>
                <th class="totales right" style="width:100px"><b>Capital</b></th>
                <th class="totales right" style="width:100px"><b>Inter&eacute;s</b></th>
                <th class="totales right" style="width:100px"><b>Multa</b></th>

            </tr>
        </thead>
        <?php
        $total = $sumacapital = $sumainteres = $sumamulta = 0;

        foreach ($reversed as $k => $v) {
            if (!empty($propCapitalInteres[$k]['propietarios']) && (!empty($propCapitalInteres[$k]['saldospropietarios']))) {
                $interes = $propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['interes'];
                $capital = $propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['capital'];

                if (isset($propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['cobranzas']) && isset($propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['ajustes'])) {
                    $cobranzas = $propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['cobranzas'];
                    $ajustes = $propCapitalInteres[$k]['saldospropietarios'][$propietario_id]['ajustes'];

                    $totalAjustes = 0;
                    if (!empty($ajustes)) {
                        foreach ($ajustes as $j => $va) {
                            $totalAjustes += $va['Ajustetipoliquidacione']['amount'];
                        }
                    }
                    $cuenta = $interes;
                    $suma = $cobranzas + $totalAjustes;
                    $cuenta -= $suma;

                    if ($cuenta < 0) {
                        $capital += $cuenta;
                        $interes = 0;
                    } else {
                        $interes = $cuenta;
                    }
                }
                $sumacapital += $capital;
                $sumainteres += $interes;
            } else {
                $capital = 0;
                $interes = 0;
            }
            if (!empty($multa[$k]['Gastos_particulares']['amount'])) {
                $valormulta = $multa[$k]['Gastos_particulares']['amount'];
                $sumamulta += $valormulta;
            } else {                // si esta vacio quiere decir que ese mes no recibio multa!, por ende se vuelve a cero el sumamulta
                $valormulta = 0;
                $sumamulta = 0;
            }

            echo "<tr><td style='text-align:left'>" . h($v['periodo']) . "</td>";
            echo "<td style='text-align:right'>" . money($capital) . "</td>";
            echo "<td style='text-align:right'>" . money($interes) . "</td>";
            echo "<td style='text-align:right'>" . money($valormulta) . "</td>";
            echo "</tr>";
        }
        ?>
        <tr class="totales">
            <td>&nbsp;</td>
            <td><?= money($sumacapital) ?></td>
            <td><?= money($sumainteres) ?></td>
            <td><?= money($sumamulta) ?></td>
        </tr>
    </table>
    <br/>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), '', array('negative' => '-'));
}
