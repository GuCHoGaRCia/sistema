<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
$notas = $info['Nota'];
$liquidation_type = $info['LiquidationsType']['name'];
$info = $info['Liquidation'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Planilla de Pagos - <?= h($liquidation_type . " - " . $info['periodo']) ?></title>
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
                font-weight: 600;
                font-size: 12px;
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
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($consorcio, $info, h($liquidation_type . " - " . $info['periodo']), $datoscliente);
        detalle($data);

        // agrego la nota si existe
        if (strlen(trim($notas['composicion'])) > 0) {
            //echo "<span style='width:700px;text-align:center'>" . $notas['composicion'] . "</span>";
        }

        @separacion(55);
        /*
          <script>
          $("#print").on("click", function () {
          $("#print").hide();
          window.print();
          $("#print").show();
          });
          </script>
         */
        ?>
    </body>
</html>
<?php

function cabecera($consorcio, $info, $titulo, $datoscliente) {
    ?>
    <table style='font-size:10px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:750px;max-width:750px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <td align="left">
                <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <p style="border-top:2px solid #9baff1;text-align:center;line-height:14px;"><br>
                    <b>Per&iacute;odo: </b><?= h($info['periodo']) ?><br>
                    <b>Tipo de Liquidaci&oacute;n: </b><?= h($titulo) ?><br>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function detalle($data) {
    $formato = "style='font-size:11px;font-family:Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="6" style='text-align:center;font-weight:bold'>PLANILLA DE PAGOS</td></tr>
            <tr>
                <th class="totales" style="width:90px"><b>Unidad (<?= count($data['prop']) ?>)</b></th>
                <th class="totales" style="width:250px"><b>Propietario</b></th>
                <th class="totales right" style="width:60px"><b>Saldo</b></th>
                <th class="totales right" style="width:100px"><b>Fecha</b></th>
                <th class="totales right" style="width:150px"><b>Forma de Pago</b></th>
                <th class="totales right" style="width:100px"><b>Importe</b></th>
            </tr>
        </thead>
        <?php
        $total = 0;
        foreach ($data['prop'] as $p) {
            echo "<tr><td style='text-align:left'>" . h($p['unidad'] . " (" . $p['code'] . ")") . "</td>";
            echo "<td style='text-align:left'>" . h($p['name']) . "</td>";
            $saldo = floor($data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes']);
            echo "<td style='text-align:right'>" . money($saldo) . "</td>"; // es saldosanteriores o saldo?
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            echo "<tr><td colspan='2'>&nbsp;</td><td></td><td></td><td></td><td></td><tr>";
            $total += $saldo;
        }
        ?>
        <tr class="totales">
            <td colspan="2">TOTAL&nbsp;</td>
            <td><?= money($total) ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
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
