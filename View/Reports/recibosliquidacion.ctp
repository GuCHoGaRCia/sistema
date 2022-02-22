<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
$notas = $info['Nota'];
$prefijo = $info['LiquidationsType']['prefijo']; // para el 5ยบ digito de la unidad (0, 5, 9, etc)
$info = $info['Liquidation'];
$periodo = $info['periodo'];
$validez = $info['vencimiento'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Recibos - <?= h($consorcio['name'] . " - " . $periodo) ?></title>
        <?= $this->Minify->script(['jq']); ?>
        <style type="text/css">
            .box-table-ax,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 11px;
                text-align: center;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                line-height:9px;
            }
            .box-table-ax th,.box-table-b th{
                font-size: 11px;
                font-weight: normal;
                padding: 8px;
                background: none;
                border-right: 2px solid #9baff1;
                border-left: 2px solid #9baff1;
                color: #039;
            }
            .box-table-ax td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-b td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #aabcfe;
                border-bottom: 1px solid #aabcfe;
                color: #000;
            }
            .tdleft{
                padding: 4px;
                background: none; 
                border:none;
                color: #000;
            }
            .right{
                text-align:right;
                min-width:100px;
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
            @media print {
                body {margin:0}
            }
        </style>
    </head>
    <body style="margin:0">
        <!--img src="/sistema/img/print2.png" id="print" /-->
        <?php
        /*
         * Muestro los recibos en el siguiente formato:
         * 1 2 3 4 5    o   1 2 3 4 5
         * 6 7 8 9 10       6 7 8 9
         * siendo cada columna una hoja. Entonces al troquelar el papel, todas las partes de arriba se ponen sobre las partes de abajo y quedan ordenadas
         * Pedido por marcelita corzo el 21/07/2016
         */
        $dsc = "RECIBO PARA EL PROPIETARIO DEL PERIODO ";
        $i = 0;
        $cantidad = count($data['prop']);
        $mitad = ceil($cantidad / 2);
        //echo "cantidad=$cantidad, mitad=$mitad<br>";
        $lista = [];
        $index = 1;
        foreach ($data['prop'] as $p) {
            $lista[$index] = $p;
            $index++;
        }

        for ($c = 1; $c <= $mitad; $c++) {
            //echo $c . "-" . ($mitad + $c) . "<br>";
            // paso los datos del propietario, los totales de ese propietario, los datos del cliente y del consorcio
            $dsc = $dsc == "RECIBO PARA EL PROPIETARIO DEL PERIODO " ? "RECIBO PARA EL ADMINISTRADOR DEL PERIODO " : "RECIBO PARA EL PROPIETARIO DEL PERIODO ";
            anverso($lista[$c], $client, $consorcio, $dsc . $periodo, $validez);
            reverso($lista[$c], $data, $client, $consorcio, $info, $prefijo);
            @separacion(60);
            $dsc = $dsc == "RECIBO PARA EL PROPIETARIO DEL PERIODO " ? "RECIBO PARA EL ADMINISTRADOR DEL PERIODO " : "RECIBO PARA EL PROPIETARIO DEL PERIODO ";
            anverso($lista[$c], $client, $consorcio, $dsc . $periodo, $validez);
            reverso($lista[$c], $data, $client, $consorcio, $info, $prefijo);
            @separacion(90);
            if (isset($lista[$mitad + $c])) {
                // paso los datos del propietario, los totales de ese propietario, los datos del cliente y del consorcio
                $dsc = $dsc == "RECIBO PARA EL PROPIETARIO DEL PERIODO " ? "RECIBO PARA EL ADMINISTRADOR DEL PERIODO " : "RECIBO PARA EL PROPIETARIO DEL PERIODO ";
                anverso($lista[$mitad + $c], $client, $consorcio, $dsc . $periodo, $validez);
                reverso($lista[$mitad + $c], $data, $client, $consorcio, $info, $prefijo);
                @separacion(85);
                $dsc = $dsc == "RECIBO PARA EL PROPIETARIO DEL PERIODO " ? "RECIBO PARA EL ADMINISTRADOR DEL PERIODO " : "RECIBO PARA EL PROPIETARIO DEL PERIODO ";
                anverso($lista[$mitad + $c], $client, $consorcio, $dsc . $periodo, $validez);
                reverso($lista[$mitad + $c], $data, $client, $consorcio, $info, $prefijo);
            }

            /*
             * IMPORTANTE PARA ESTA VERGA DE REPORTE
             * EN FIREFOX, LA SEPARACION DEBE SER TIPO 40 Y 70, O PONER EN 90% EL REPORTE
             */

            //if ($i++ % 2 != 0) {
            echo "<div style='page-break-after:always'></div>";
            //}
        }
        ?>
    </body>
</html>
<?php

function anverso($propietario, $client, $consorcio, $periodo, $validez) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;'";
    $fechaValidez = date("d/m/Y", strtotime($validez));
    ?>
    <div style='height:20px'></div>
    <table width="800" <?= $formato ?> class="box-table-ax" align="center">
        <tr>
            <td width="130" height="70" rowspan="3" align="center">
                <img alt="logo" width=100 height=100 src="/sistema/<?= file_exists("files/" . $client['id'] . "/" . $client['id'] . ".jpg") ? "files/" . $client['id'] . "/" . $client['id'] . ".jpg" : "img/0000.png" ?>">
            </td>
            <td width="360" rowspan="3" align="left" valign="middle">
                ADMINISTRACION<br/><br/>
                <font size=4><?= h($client['name']) ?></font><br/>
                <br/><?= h($client['address']) ?>
                <br/><?= h($client['city']) ?>
                <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                <br/>Tel.: <?= h(!empty($client['telephone']) ? $client['telephone'] : '--') ?>
                <br/>Email: <?= h(!empty($client['email']) ? $client['email'] : '--') ?>
            </td>
            <td align="left" valign="middle"><b>Consorcio:</b>
                <?= h($consorcio['name']) ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? h($consorcio['cuit']) : '--') ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
                <b>Domicilio:</b>
                <?= h($consorcio['address']) ?>
                <br><b>Localidad:</b><?= h($consorcio['city']) ?>
            </td>
        </tr>  
        <tr>
            <td align="center" rowspan="3" colspan="3" valign="top" cellspacing="0">
                <b><?= h($periodo) ?></b><br><br>
                <b>VALIDEZ: &nbsp;<?= h($fechaValidez) ?></b>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($propietario, $data, $client, $consorcio, $info, $prefijo) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;'";
    $total = 0;
    if (isset($data['totales'][$propietario['id']]["tot"])) {
        $total += $data['totales'][$propietario['id']]["tot"];
    }
    ?>
    <table width=800 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <td colspan="6">
                <table border="0" valign="top" cellspacing="0" width="780" <?= $formato ?>>
                    <tr> 
                        <td align="center" style="border:0px;text-align:center;width:350px;">
                            <span style="width:50px;float:left;margin:20px 0px 0px -100px"></span>
                            <font style="font-size:13px;font-weight:700;"><?= h($propietario['name'] . " - " . $propietario['unidad'] . " (" . $propietario['code'] . ") ") ?></font><br>
                            <?= h($propietario['postal_address']) ?><br>
                            <?= h($propietario['postal_city']) ?><br>
                        </td>
                        <td class="right" style="border:0px;width:150px;text-align:center">
                            <font style="font-size:12px;font-weight:700">IMPORTE ABONADO<br>
                            <?php
                            $totalexpensa = $data['saldo'][$propietario['id']]['capital'] + $data['saldo'][$propietario['id']]['interes'];
                            echo money(floor($totalexpensa));
                            ?>
                            </font>
                        </td>
                        <td class="right" style="text-align:left">
                            <font style="font-size:13px;font-weight:700">FIRMA<br>

                            </font>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php
}

function separacion($px) {
    ?>
    <div id="separacion" style="height:<?= $px ?>px;clear:both;">
    </div>
    <?php
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
