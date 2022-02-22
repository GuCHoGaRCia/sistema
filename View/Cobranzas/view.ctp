<?php
if (empty($cobranza)) {
    die("Cobranza inexistente");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
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
            #mover{
                cursor:pointer;
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
                    $datoscliente = $this->element('datoscliente', ['dato' => $cobranza['Client']]);
                    anverso($cobranza, $datoscliente, $this->webroot);
                    reverso($cobranza, $cheque, $tipos, $periodos, $users[$cobranza['Cobranza']['user_id']] ?? null, $this->webroot, $formasdepago);
                    ?>
                </td>
            </tr>
            <tr>
                <td id="mover" valign="top">
                    <script>
<?php
/* Quité jquery porq sino al abrir la 'i' en resumen caja banco (de cobranzas), vuelve a agregar jq.js, 
 * y la segunda vez que se abre el dialog da error de js (dialog is not a function) */
?>
                        document.getElementById("mover").onclick = function () {
                            document.getElementById("mover").style.display = 'none';
                        };
                    </script>
                    <?php
                    // duplicado
                    if (!isset($mostrarsolounrecibo)) {// en el panel del propietario, muestro 1 solo recibo (esta variable la seteo en Cobranzas/ver
                        anverso($cobranza, $datoscliente, $this->webroot);
                        reverso($cobranza, $cheque, $tipos, $periodos, $users[$cobranza['Cobranza']['user_id']] ?? null, $this->webroot, $formasdepago);
                    }
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php

function anverso($cobranza, $datoscliente, $wr) {
    $client = $cobranza['Client'];
    $consorcio = $cobranza['Consorcio'];
    $propietario = $cobranza['Propietario'];
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a">
        <tr>
            <td style="<?= $cobranza['Cobranza']['anulada'] ? 'background-color:red;font-size:22px;color:white;border-bottom:2px solid #000' : '' ?>"><?= $cobranza['Cobranza']['anulada'] ? 'ANULADA' : '' ?></td>
            <td style="text-align:right;font-size:16px;font-weight:700;border-bottom:2px solid #000;border-right:0px;padding:10px">Cobranza Propietario</td>
            <td style="font-weight:700;text-align:right;border-left:0px;border-bottom:2px solid #000">Recibo N&ordm; <?= h($cobranza['Cobranza']['numero']) ?></td>
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

function reverso($cobranza, $cheque, $tipos, $periodos, $user, $wr, $formasdepago) {
    $propietario = $cobranza['Propietario'];
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;border-top:0'";
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b">
        <tr>
            <td class="pri"><b>Fecha</b></td>
            <td colspan=3 style="text-align:center"><?php echo date("d/m/Y", strtotime($cobranza['Cobranza']['fecha'])) . " - " . h($user) . " <span class='noimprimir'>(creada el " . date("d/m/Y H:i:s", strtotime($cobranza['Cobranza']['created'])) . ")</span>" ?></td>
        </tr>
        <tr>
            <td class="pri" style='border-top:2px solid #000 !important'><b>Recibimos de</b></td>
            <td colspan=3>
                <?php
                $recibimos = "CM " . $cobranza['Consorcio']['name'] . " - " . $cobranza['Propietario']['name'] . " (" . $cobranza['Propietario']['unidad'] . ")";
                echo h(!empty($cobranza['Cobranza']['recibimosde']) ? $cobranza['Cobranza']['recibimosde'] : $recibimos);
                ?>
            </td>
        </tr>
        <?php
        $totalcheque = 0; //el total en cheques
        if (!empty($cheque)) {
            foreach ($cheque as $k => $v) {
                $totalcheque += $v['Cobranzacheque']['amount'];
            }
        }
        $concepto = "";
        if (!empty($cobranza['Cobranza']['concepto']) && $cobranza['Cobranza']['concepto'] != $recibimos) {
            $concepto = '<tr><td colspan="3">' . h($cobranza['Cobranza']['concepto']) . '</td></tr>';
        }
        ?>
        <tr>
            <td rowspan=<?= count($tipos) + ($concepto == "" ? 1 : 2) ?> class="pri" style='border-top:2px solid #000 !important'><b>Concepto</b></td>
        </tr>
        <?php
        echo $concepto;
        foreach ($tipos as $k => $v) {
            ?>
            <tr>
                <td colspan=3><?= h($v['LiquidationsType']['name'] . " - Período: " . ($periodos[$v['LiquidationsType']['id']] == 'SI' ? 'Saldo inicial' : ((!empty($cobranza['Cobranza']['concepto']) && $cobranza['Cobranza']['concepto'] != $recibimos) ? $cobranza['Cobranza']['concepto'] : $periodos[$v['LiquidationsType']['id']])) . " (" . money($v['Cobranzatipoliquidacione']['amount']) . ")") ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="pri right" style='border-top:2px solid #000 !important'><b>Importe total</b></td>
            <td colspan="3" class='right' style="width:400px;border-bottom:2px solid #000 !important"><b><?php echo "PESOS " . convertir($cobranza['Cobranza']['amount']) . " - " . money($cobranza['Cobranza']['amount']) ?></b></td>
        </tr>
        <tr>
            <td rowspan=4 class="pri" style='border-top:2px solid #000 !important'><b>Forma de pago</b></td>
        </tr>
        <tr>
            <td colspan=2>Efectivo</td>
            <td class='right'><?= !empty($cobranza['Cajasingreso']['importe']) && $cobranza['Cajasingreso']['importe'] > 0 ? h($cobranza['Caja']['name']) . " - " . money($cobranza['Cajasingreso']['importe']) : '--' ?></td>
        </tr>
        <tr>
            <td colspan=2>
                <?php
                if (isset($cobranza['Bancosdepositosefectivo']['formasdepago_id']) && $cobranza['Bancosdepositosefectivo']['formasdepago_id'] != 0) {
                    echo h($formasdepago[$cobranza['Bancosdepositosefectivo']['formasdepago_id']]['forma'] ?? '');
                } else {
                    if (substr($cobranza['Cobranza']['recibimosde'], 0, 3) === 'CA ') {// si no fue acreditada la pone como interdeposito, asi q miro el concepto primero
                        echo "Cobranza Autom&aacute;tica";
                    } else {
                        echo $cobranza['Bancosdepositosefectivo']['es_transferencia'] ? 'Transferencia' : 'Interdep&oacute;sito';
                    }
                }
                ?>
            </td>
            <td class='right'>
                <?php
                $detallebanco = '--';
                if (!empty($cobranza['Bancosdepositosefectivo']['importe'])) {
                    $detallebanco = h($cobranza['Bancoscuenta']['name']) . " - " . money($cobranza['Bancosdepositosefectivo']['importe']);
                } else if (substr($cobranza['Cobranza']['recibimosde'], 0, 3) === 'CA ') {// es cobranza automatica y si no fue acreditada, en el importe dice --
                    $detallebanco = money($cobranza['Cobranza']['amount']);
                }
                echo $detallebanco;
                ?>
            </td>
        </tr>
        <tr>
            <td colspan=2>Cheques</td>
            <td class='right'>
                <?php
                if (!empty($cheque)) {
                    foreach ($cheque as $k => $v) {
                        if (!empty($user)) {// esta logueado en el sistema (sino, esta ingresando desde el panel de propietario)
                            echo '<a href="' . $wr . 'Cheques/view/' . $v['Cheque']['id'] . '" target="_blank" rel="nofollow noopener noreferrer"><img src="' . $wr . 'img/icon-info.png" title="Ver detalle Cheque" alt=""></a>';
                        }
                        echo ($v['Cheque']['fisico'] ? '' : ' <span style="font-weight:bold">Echeq</span> - ') . date("d/m/Y", strtotime($v['Cheque']['fecha_vencimiento'])) . " " . h($v['Cheque']['banconumero']) . " - " . money($v['Cobranzacheque']['amount']);
                        echo "<br>";
                    }
                } else {
                    echo '--';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
    // si existe la firma, la muestro
    $firma = file_exists("files/" . $cobranza['Client']['id'] . "/firma.jpg") ? "files/" . $cobranza['Client']['id'] . "/firma.jpg" : "";
    if ($firma !== "") {
        echo "<table valign=top cellspacing=0>";
        echo "<tr><td width=600px></td><td style='text-align:center'><img style='max-height:100px' alt='firma' src='" . $wr . "$firma'><br>" . h($cobranza['Client']['name']) . "</td></tr>";
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
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
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
