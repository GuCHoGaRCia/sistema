<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Coeficientes Propietarios - <?= h($consorcio['name']) ?></title>
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
        <img src="/sistema/img/print2.png" id="print" />
        <?php
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio]);
        cabecera($datoscliente, $datosconsorcio);
        detalle($propietarios, $coeficientes, $nombrescoef);

        @separacion(55);
        ?>
        <script>
            $("#print").on("click", function () {
                $("#print").hide();
                window.print();
                $("#print").show();
            });
        </script>
    </body>
</html>
<?php

function cabecera($datoscliente, $datosconsorcio) {
    ?>
    <table style='font-size:10px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:750px;max-width:750px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <?= $datosconsorcio ?>
        </tr>
    </table>
    <?php
}

function detalle($propietarios, $coeficientes, $nombrescoef) {
    $formato = "style='font-size: 8px; font-family: Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr>
                <th class="totales" style="width:300px;text-align:left"><b>Propietario - Unidad - C&oacute;digo(<?= count($propietarios) ?>)</b></th>
                <?php
                foreach ($nombrescoef as $k => $v) {
                    echo '<th class="totales right" style="width:150px"><b>' . h($v) . '</b></th>';
                }
                ?>
            </tr>
        </thead>
        <?php
        $total = [];
        foreach ($propietarios as $k => $v) {
            echo "<tr><td style='text-align:left'>" . h($v['name'] . " - " . $v['unidad'] . " (" . $v['code'] . ")") . "</td>";
            foreach ($nombrescoef as $r => $s) {
                echo '<td style="text-align:center;width:150px">' . h($coeficientes[$k][$r]['value']) . '</td>';
                if (!isset($total[$r])) {
                    $total[$r] = 0;
                }
                $total[$r] += $coeficientes[$k][$r]['value'];
            }
            echo "</tr>";
        }
        ?>
        <tr>
            <td style="text-align:left;font-weight:bold">TOTALES</td>
            <?php
            foreach ($nombrescoef as $r => $s) {
                echo '<td style="text-align:center;width:150px;font-size:12px"><b>' . h($total[$r]) . ' %</b></td>';
            }
            ?>
        </tr>
    </table>
    <br/>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}
