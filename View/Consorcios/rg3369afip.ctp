<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>RG 3369 AFIP</title>
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
        <?php
        $datoscliente = $this->element('datoscliente', ['dato' => $data['cliente']]);
        $datosconsorcio = $this->element('datosconsorcio', ['dato' => $data['consorcio']]);
        cabecera($datoscliente, $datosconsorcio);
        detalle($data);

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
    <table style='font-size:10px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:750px;max-width:750px;border-bottom:0px' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <?= $datosconsorcio ?>
        </tr>
    </table>
    <?php
}

function detalle($data) {
    $formato = "style='font-size: 8px; font-family: Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr><td colspan="6" style='text-align:center;font-weight:bold'>RG 3369 AFIP<br>Per&iacute;odo: <?= h($data['periodo']) ?> - Superficie m&iacute;nima: <?= $data['superficie'] ?> - Monto m&iacute;nimo: <?= $data['monto'] ?></td></tr>
            <tr>
                <th class="totales" style="width:300px;text-align:left"><b>Propietario - Unidad - C&oacute;digo (<?= count($data['propietarios']) ?>)</b></th>
                <th class="totales right" style="width:150px"><b>CUIT</b></th>
                <th class="totales right" style="width:150px"><b>Direcci&oacute;n</b></th>
                <th class="totales right" style="width:150px"><b>Ciudad</b></th>
                <th class="totales right" style="width:150px"><b>Superficie</b></th>
                <th class="totales right" style="width:150px"><b>Monto</b></th>
            </tr>
        </thead>
        <?php
        foreach ($data['propietarios'] as $p) {
            if ($p['monto'] >= $data['monto'] && $p['sup'] >= $data['superficie']) {
                echo "<tr><td style='text-align:left'>" . h($p['name']) . "</td>";
                echo "<td style='text-align:right'>" . h($p['cuit']) . "</td>";
                echo "<td style='text-align:right'>" . h($p['postal_address']) . "</td>";
                echo "<td style='text-align:right'>" . h($p['postal_city']) . "</td>";
                echo "<td style='text-align:right'>" . h($p['sup']) . "</td>";
                echo "<td style='text-align:right'>" . h($p['monto']) . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
    <br/>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}
