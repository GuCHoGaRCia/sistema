<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Carta deudores</title>
        <?= $this->Minify->script(['jq', 'jqui']); ?>
        <style type="text/css">
            .box-table-ax,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 11px;
                text-align: center;
                line-height:9px;
            }
            .box-table-ax th,.box-table-b th{
                font-size: 11px;
                font-weight: normal;
                padding: 8px;
                background: none;
                color: #039;
            }
            .box-table-ax td{
                padding: 4px;
                background: none; 
                color: #000;
            }
            .box-table-b td{
                padding: 4px;
                background: none; 
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
        </style>
    </head>
    <body>
        <?php
        foreach ($propietarios as $k => $v) {
            if (isset($saldos[$k])) {
                anverso($_SESSION['Auth']['User']['Client'], $consorcio['Consorcio']);
                reverso($v, $carta, $_SESSION['Auth']['User']['Client'], $saldos[$k]);
                echo "<div style='page-break-after:always'></div>";
            }
        }
        ?>
    </body>
</html>
<?php

function anverso($client, $consorcio) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;'";
    ?>
    <div style='height:45px'></div>
    <table width="750" <?= $formato ?> class="box-table-ax" align="center">
        <tr>
            <td width="130" height="70" rowspan="3" align="center">
                <img alt="logo" width=50 height=50 src="/sistema/img/<?= file_exists("img/" . $client['code'] . ".jpg") ? $client['code'] . ".jpg" : "0000.png" ?>">
            </td>
            <td width="360" rowspan="3" align="left" valign="middle">
                ADMINISTRACION<br/><br/>
                <font size=4><?= h($client['name']) ?></font><br/>
                <br/><?= h($client['address']) ?>
                <br/><?= h($client['city']) ?>
                <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                <br/>Email: <?= h(!empty($client['email']) ? $client['email'] : '--') ?>
            </td>
            <td align="left" valign="middle"><b>Consorcio:</b>
                <?= h($consorcio['name']) ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
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
                <b></b>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($propietario, $carta, $client, $saldo) {
    $formato = "style='font-size: 14px; font-family: Verdana, Helvetica, sans-serif;line-height:15px'";
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <td colspan="6">
                <table border="0" valign="top" cellspacing="0" width="720" <?= $formato ?>>
                    <tr>
                        <td style="border:0px">
                            <?php
                            $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                            echo "<div style='width:100%;text-align:right'>" . h($client['city'] . ", " . date("d") . " de " . $meses[date("n") - 1] . " de " . date("Y")) . "</div>";
                            echo "<div style='width:100%;text-align:justify'><p>" . $carta . "</p></div>";
                            echo "<div style='width:auto;text-align:center;font-weight:bold;border: 2px solid gray;padding:20px'>Detalle de deuda: " . h($propietario['name2']) . ".<br>Deuda total al d&iacute;a de la fecha: " . money($saldo) . "</div>";
                            echo "<div style='width:100%;margin-top:30px;text-align:right;font-size:16px;font-weight:bold'>" . h($client['name']) . "</div>";
                            // si existe la firma, la muestro
                            $firma = file_exists("files/" . $client['id'] . "/firma.jpg") ? "files/" . $client['id'] . "/firma.jpg" : "";
                            if ($firma !== "") {
                                echo "<div style='margin-top:30px;text-align:right'>" . "<img style='max-height:100px' alt='firma' src='/sistema/$firma' />" . "</div>";
                            }
                            ?>
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
    return CakeNumber::currency(h($valor), '$ ', array('negative' => '-'));
}
