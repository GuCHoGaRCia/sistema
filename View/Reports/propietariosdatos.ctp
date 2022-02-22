<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Listado de Propietarios - <?= h($consorcio['name']) ?></title>
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
            .left{
                text-align:left;
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
        <style type="text/css" media="print">
            @page { size: landscape; }
            body{margin:1px}
        </style>
    </head>
    <body>
        <?php
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio]);
        cabecera($datoscliente, $datosconsorcio);
        detalle($propietarios);

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

function cabecera($datoscliente, $datosconsorcio) {
    ?>
    <table style='font-size:12px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:90%;border-bottom:0px' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <?= $datosconsorcio ?>
        </tr>
    </table>
    <?php
}

function detalle($data) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;width:90%'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="7" style='text-align:center;font-weight:bold'>LISTADO DE PROPIETARIOS</td></tr>
            <tr>
                <th class="totales" style="text-align:left"><b>Propietario - Unidad - C&oacute;digo(<?= count($data) ?>)</b></th>
                <th class="totales left"><b>Email</b></th>
                <th class="totales"><b>Direcci&oacute;n</b></th>
                <th class="totales"><b>Ciudad</b></th>
                <th class="totales"><b>Tel&eacute;fono</b></th>
                <th class="totales"><b>Whatsapp</b></th>
                <th class="totales"><b>Sup.</b></th>
                <th class="totales"><b>Pol.</b></th>
            </tr>
        </thead>
        <?php
        foreach ($data as $p) {
            echo "<tr><td style='text-align:left'>" . h($p['name'] . " - " . $p['unidad'] . " (" . $p['code'] . ")") . "</td>";
            echo "<td style='text-align:left'>" . h(str_replace(',', ' ', $p['email'])) . "</td>";
            echo "<td style='text-align:left'>" . h($p['postal_address']) . "</td>";
            echo "<td style='text-align:left'>" . h($p['postal_city']) . "</td>";
            echo "<td style='text-align:left'>" . h($p['telephone']) . "</td>";
            echo "<td style='text-align:left'>" . h($p['whatsapp']) . "</td>";
            echo "<td style='text-align:center'>" . h($p['superficie']) . "</td>";
            echo "<td style='text-align:center'>" . h($p['poligono']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <br/>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}
