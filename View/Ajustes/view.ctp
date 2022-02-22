<?php
if (empty($ajuste)) {
    die("Ajuste inexistente");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Recibo</title>
        <style type="text/css">
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 11px;
                text-align: center;
                border-collapse: collapse;
                border: 2px solid #000;
                line-height:9px;
            }
            .box-table-a th,.box-table-b th{
                font-size: 11px;
                font-weight: normal;
                padding: 8px;
                border-right: 2px solid #000;
                border-left: 2px solid #000;
                color: #039;
            }
            .box-table-a td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #000;
                color: #000;
            }
            .box-table-b td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #000;
                border-bottom: 1px solid #000;
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
                border: 2px solid #000;
                font-weight: 700;
                font-size: 13px;
            }
            @page {
                margin: 5px;
            }
            @media print{
                #mover{
                    vertical-align:bottom;
                }
                .noimprimir{
                    display:none;
                }
            }
        </style>
    </head>
    <body>
        <table style="height:29cm">
            <tr valign="top">
                <td>
                    <?php
                    $datoscliente = $this->element('datoscliente', ['dato' => $ajuste['Client']]);
                    anverso($ajuste, $datoscliente, $this->webroot);
                    reverso($ajuste, $this->webroot);
                    ?>
                </td>
            </tr>
            <tr>
                <td id="mover" valign="top">
                    <?php
                    // duplicado
                    anverso($ajuste, $datoscliente, $this->webroot);
                    reverso($ajuste, $this->webroot);
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php

function anverso($ajuste, $datoscliente, $wr) {
    $client = $ajuste['Client'];
    $consorcio = $ajuste['Consorcio'];
    $propietario = $ajuste['Propietario'];
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a">
        <tr>
            <td style="<?= $ajuste['Ajuste']['anulado'] ? 'background-color:red;font-size:22px;color:white;border-bottom:2px solid #000' : '' ?>"><?= $ajuste['Ajuste']['anulado'] ? 'ANULADO' : '' ?></td>
            <td style="text-align:right;font-size:16px;font-weight:700;border-bottom:2px solid #000;border-right:0px;padding:10px">Ajuste Propietario</td>
            <td style="font-weight:700;text-align:right;border-left:0px;border-bottom:2px solid #000"></td>
        </tr>
        <tr>
            <?= $datoscliente ?>
            <td align="left" valign="middle">
                <b>Consorcio: </b><?= h($consorcio['name']) ?>
                <br><br>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <br><br>
                <b>Domicilio: </b><?= h($consorcio['address']); ?>
                <br><br>
                <b>Localidad:</b>
                <?= h($consorcio['city']) ?>
                <br><br>
                <b>Unidad:</b>
                <?= h($propietario['unidad'] . " - Código: " . $propietario['code']) ?>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($ajuste, $wr) {
    $propietario = $ajuste['Propietario'];
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;border-top:0'";
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b">
        <tr>
            <td class="pri"><b>Fecha</b></td>
            <td colspan=3 style="text-align:center"><?php echo date("d/m/Y", strtotime($ajuste['Ajuste']['fecha'])) . " - " . h($ajuste['User']['name']) . " <span class='noimprimir'>(creado el " . date("d/m/Y H:i:s", strtotime($ajuste['Ajuste']['created'])) . ")</span>" ?></td>
        </tr>
        <tr>
            <td class="pri" style='border-top:2px solid #000 !important'><b>Concepto</b></td>
            <td colspan=3><?= h($ajuste['Ajuste']['concepto']) ?></td>
        </tr>
        <tr>
            <td rowspan=<?= count($ajuste['Ajustetipoliquidacione'])+1 ?> class="pri" style='border-top:2px solid #000 !important'><b>Detalle</b></td>
        </tr>
        <?php
        foreach ($ajuste['Ajustetipoliquidacione'] as $k => $v) {
            ?>
            <tr>
                <td colspan=3 class='right'><?= h($v['LiquidationsType']['name'] . " -" . ($v['solocapital'] ? ' (Solo Capital) - ' : ' ') . money($v['amount'])) ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="pri right" style='border-top:2px solid #000 !important'><b>Importe total</b></td>
            <td colspan="3" class='right' style="width:400px;border-bottom:2px solid #000 !important"><b><?php echo "PESOS " . convertir($ajuste['Ajuste']['importe']) . " - " . money($ajuste['Ajuste']['importe']) ?></b></td>
        </tr>
    </table>
    <?php
    // si existe la firma, la muestro
    $firma = file_exists("files/" . $ajuste['Client']['id'] . "/firma.jpg") ? "files/" . $ajuste['Client']['id'] . "/firma.jpg" : "";
    if ($firma !== "") {
        echo "<table valign=top cellspacing=0>";
        echo "<tr><td width=600px></td><td style='text-align:center'><img style='max-height:100px' alt='firma' src='" . $wr . "$firma'><br>" . $ajuste['Client']['name'] . "</td></tr>";
        echo "</table>";
    }
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

function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false) {
    $converted = '';
    $decimales = '';
    $negativo = (bool) ($number < 0);
    if (/* ($number < 0) || */ ($number > 999999999)) {
        return '';
    }
    $div_decimales = explode('.', $number);
    if (count($div_decimales) > 1) {
        $number = $div_decimales[0];
        $decNumberStr = (string) $div_decimales[1];
        if (strlen($decNumberStr) == 1) {
            $decNumberStr .= "0";
        }
        //if (strlen($decNumberStr) == 2) {
        $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
        $decCientos = substr($decNumberStrFill, 6);
        $decimales = convertGroup($decCientos);
        //}
    } else if (count($div_decimales) == 1 && $forzarCentimos) {
        $decimales = 'CERO ';
    }
    $numberStr = (string) $number;
    $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
    $millones = substr($numberStrFill, 0, 3);
    $miles = substr($numberStrFill, 3, 3);
    $cientos = substr($numberStrFill, 6);
    if (intval($millones) > 0) {
        if ($millones == '001') {
            $converted .= 'UN MILLON ';
        } else if (intval($millones) > 0) {
            $converted .= sprintf('%sMILLONES ', convertGroup($millones));
        }
    }
    if (intval($miles) > 0) {
        if ($miles == '001') {
            $converted .= 'MIL ';
        } else if (intval($miles) > 0) {
            $converted .= sprintf('%sMIL ', convertGroup($miles));
        }
    }
    if (intval($cientos) > 0) {
        if ($cientos == '001') {
            $converted .= 'UN ';
        } else if (intval($cientos) > 0) {
            $converted .= sprintf('%s ', convertGroup($cientos));
        }
    }
    if (empty($decimales)) {
        $valor_convertido = $converted . strtoupper($moneda);
    } else {
        $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
    }
    return $negativo ? 'MENOS ' . $valor_convertido : $valor_convertido;
}

function convertGroup($n) {
    $UNIDADES = ['', 'UN ', 'DOS ', 'TRES ', 'CUATRO ', 'CINCO ', 'SEIS ', 'SIETE ', 'OCHO ', 'NUEVE ', 'DIEZ ', 'ONCE ', 'DOCE ', 'TRECE ', 'CATORCE ', 'QUINCE ', 'DIECISEIS ', 'DIECISIETE ', 'DIECIOCHO ', 'DIECINUEVE ', 'VEINTE '];
    $DECENAS = ['VENTI', 'TREINTA ', 'CUARENTA ', 'CINCUENTA ', 'SESENTA ', 'SETENTA ', 'OCHENTA ', 'NOVENTA ', 'CIEN '];
    $CENTENAS = ['CIENTO ', 'DOSCIENTOS ', 'TRESCIENTOS ', 'CUATROCIENTOS ', 'QUINIENTOS ', 'SEISCIENTOS ', 'SETECIENTOS ', 'OCHOCIENTOS ', 'NOVECIENTOS '];

    $output = '';
    if ($n == '100') {
        $output = "CIEN ";
    } else if ($n[0] !== '0') {
        $output = $CENTENAS[$n[0] - 1];
    }
    $k = intval(substr($n, 1));
    if ($k <= 20) {
        $output .= $UNIDADES[$k];
    } else {
        if (($k > 30) && ($n[2] !== '0')) {
            $output .= sprintf('%sY %s', $DECENAS[intval($n[1]) - 2], $UNIDADES[intval($n[2])]);
        } else {
            $output .= sprintf('%s%s', $DECENAS[intval($n[1]) - 2], $UNIDADES[intval($n[2])]);
        }
    }
    return $output;
}
