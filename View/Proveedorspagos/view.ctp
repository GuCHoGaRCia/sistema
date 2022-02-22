<?php
$proveedorspago = $resul['proveedorspago'];
if (empty($proveedorspago)) {
    die("Pago a proveedor inexistente");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Recibo Pago Proveedor</title>
        <?php
        if (!isset($this->request->params['pass'][1])) {
            echo$this->Minify->css(['jquery-ui.min']);
            echo $this->Minify->script(['jq', 'jqui']);
        }
        ?>
        <style type="text/css">
            @font-face {
                font-family: "3 of 9 Barcode";
                src: url('<?= $this->webroot ?>css/3of9.woff') format("woff");
            }
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
                /*background: #e8edff;*/
                border-right: 2px solid #000;
                border-left: 2px solid #000;
                color: #039;
            }
            .box-table-a td{
                padding: 4px;
                /*background: #e8edff; */
                border-left: 2px solid #000;
                /*color: #669;*/
            }
            .box-table-b td{
                padding: 4px;
                /*background: #e8edff; */
                border-left: 2px solid #000;
                border-bottom: 1px solid #000;
                /*color: #669;*/
            }
            .tdleft{
                padding: 4px;
                background: #e8edff; 
                border:none;
                /*color: #669;*/
            }
            .right{
                text-align:right;
                min-width:100px;
            }
            .pri{
                text-align:left;
                width:110px;
                border:none !important;
            }
            .totales{
                border: 2px solid #000;
                font-weight: 700;
                font-size: 13px;
            }
            @media print{
                .noimprimir{
                    display:none;
                }
            }
        </style>
    </head>
    <body>
        <?php
        //original

        $datoscliente = $this->element('datoscliente', ['dato' => $_SESSION['Auth']['User']['Client']]);
        $datosproveedor = $this->element('datosproveedor', ['dato' => $proveedorspago['Proveedor']]);
        //anverso($proveedorspago, $datoscliente, $datosproveedor, $this->webroot);
        $pago = $proveedorspago['Proveedorspago'];
        $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif'";
        ?>
        <table width="750" <?= $formato ?> class="box-table-a" align="center">
            <tr>
                <td style="<?= $pago['anulado'] ? 'background-color:red;font-size:22px;color:white;border-bottom:2px solid #000' : '' ?>"><?= $pago['anulado'] ? 'ANULADO' : '' ?></td>
                <td style="text-align:right;font-size:16px;font-weight:700;border-bottom:2px solid #000;border-right:0px;padding:10px">Detalle de Pago a Proveedor</td>
                <td style="font-weight:700;text-align:right;border-left:0px;border-bottom:2px solid #000">Recibo N&ordm; <?= h($pago['numero']) ?></td>
            </tr>
            <tr style="border-bottom:2px solid #000">
                <?= $datoscliente ?>
                <?= $datosproveedor ?>
            </tr>
        </table>
        <?php
        //reverso($proveedorspago, $facturas, $cheques, $chequespropios, $chequespropiosadm, $bancoscuentas, $efectivo, $efectivoadm, $transferencia, $transferenciaadm, $pagosacuentaaplicados, $notasdecreditoaplicadas, $users[$proveedorspago['Proveedorspago']['user_id']], $this->webroot, $consorcios, $bancoscuentasporconsorcio);
        $formato .= ";border-top:0px'";
        $consorcio = "";
        $bancoscuentasporconsorcio = $resul['bancoscuentasporconsorcio'];
        $bancoscuentas = $resul['bancoscuentas'];
        ?>
        <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
            <tr>
                <td class="pri"><b>Fecha</b></td>
                <td colspan=3><?php echo date("d/m/Y", strtotime($pago['fecha'])) . " (" . h($users[$proveedorspago['Proveedorspago']['user_id']]) . ") <span class='noimprimir'>(creado el " . date("d/m/Y H:i:s", strtotime($pago['created'])) . ")</span>" ?></td>
            </tr>
            <tr>
                <td class="pri"><b>Concepto</b></td>
                <td colspan=3><?php echo h($pago['concepto']) ?></td>
            </tr>
            <tr style='border-bottom:2px solid #000 !important'>
                <td class="pri"><b>Facturas abonadas</b></td>
                <td colspan=3 style="text-align:right">
                    <?php
                    $facturas = $resul['facturas'];
                    $totalfacturas = $totalpac = $totalnc = 0;
                    if (!empty($facturas)) {
                        foreach ($facturas as $k => $v) {
                            $consorcioid = $v['Consorcio']['id'];
                            $consorcio = $v['Consorcio']['name'] . " - ";
                            echo "<img src='" . $this->webroot . "img/icon-info.png' class='noimprimir' style='cursor:pointer' title='Ver detalle factura' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorsfacturas/view/" . $v['Proveedorsfactura']['id'] . "\");'/>&nbsp;";
                            echo date("d/m/Y", strtotime($v['Proveedorsfactura']['fecha'])) . " - " . h($v['Consorcio']['name']) . " - " . h($v['Liquidation']['periodo']) . " - ";
                            echo h($v['Proveedorsfactura']['concepto'] . " - Nº " . $v['Proveedorsfactura']['numero'] . " - " . money($v['Proveedorspagosfactura']['importe'])) . "<br>";
                            $totalfacturas += $v['Proveedorspagosfactura']['importe'];
                        }
                    }
                    // si paga facturas y ademas a cuenta, lo muestro
                    echo (!empty($proveedorspago['Proveedorspagosacuenta'][0]['importe']) ? date("d/m/Y", strtotime($pago['fecha'])) . " - " . h($consorcios[$proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id']]) . ' - Pago a Cuenta - ' . money($proveedorspago['Proveedorspagosacuenta'][0]['importe']) : '');
                    if (isset($proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id']) && !empty($proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id'])) {
                        $consorcio = $consorcios[$proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id']] . " - ";
                        $consorcioid = $proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id'];
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td rowspan=10 class="pri"><b>Forma de pago</b></td>
            </tr>
            <tr>
                <?php
                $efectivo = $resul['efectivo'];
                ?>
                <td>Efectivo</td>
                <td colspan=2 class="right"><?= isset($efectivo['Cajasegreso']['importe']) && $efectivo['Cajasegreso']['importe'] > 0 ? h($consorcio . $efectivo['Caja']['name'] . " - " . money($efectivo['Cajasegreso']['importe'])) : '' ?></td>
            </tr>
            <tr>
                <td>Efectivo de Administraci&oacute;n</td>
                <td colspan=2 class="right">
                    <?php
                    $efectivoadm = $resul['efectivoadm'];
                    foreach ($efectivoadm as $k => $v) {
                        foreach ($v['Administracionefectivosdetalle'] as $r) {
                            if ($r['consorcio_id'] == $consorcioid) {
                                echo h($bancoscuentas[$v['Administracionefectivo']['bancoscuenta_id']] . " - " . money($r['importe'])) . "<br>";
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Transferencia</td>
                <td colspan=2 class="right">
                    <?php
                    $transferencia = $resul['transferencia'];
                    if (!empty($transferencia)) {
                        foreach ($transferencia as $k => $v) {
                            echo h($consorcio . $v['Bancoscuenta']['name'] . " - " . money($v['Bancosextraccione']['importe'])) . "<br>";
                        }
                    } else {
                        echo '';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Transferencia de Administraci&oacute;n</td>
                <td colspan=2 class="right">
                    <?php
                    $transferenciaadm = $resul['transferenciaadm'];
                    foreach ($transferenciaadm as $k => $v) {
                        foreach ($v['Administraciontransferenciasdetalle'] as $r) {
                            if (in_array($r['bancoscuenta_id'], $bancoscuentasporconsorcio[$consorcioid])) {
                                echo h($bancoscuentas[$v['Administraciontransferencia']['bancoscuenta_id']] . " - " . money($r['importe'])) . "<br>";
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Cheques terceros</td>
                <td colspan=2 class="right">
                    <?php
                    $cheques = $resul['cheques'];
                    if (!empty($proveedorspago['Proveedorspagoscheque'])) {
                        foreach ($proveedorspago['Proveedorspagoscheque'] as $k => $v) {
                            echo "<img src='" . $this->webroot . "img/icon-info.png' class='noimprimir' style='cursor:pointer' title='Ver detalle cheque' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "cheques/view/" . $v['cheque_id'] . "/1\");'/>";
                            echo ($cheques[$v['cheque_id']]['fisico'] ? '' : ' <span style="font-weight:bold">Echeq</span> - ') . h($cheques[$v['cheque_id']]['concepto'] . " - " . $cheques[$v['cheque_id']]['banconumero'] . " - " . money($cheques[$v['cheque_id']]['importe']));
                            echo "<br>";
                        }
                    } else {
                        echo '';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Cheques propios</td>
                <td colspan=2 class="right">
                    <?php
                    $chequespropios = $resul['chequespropios'];
                    if (!empty($chequespropios)) {
                        foreach ($chequespropios as $k => $v) {
                            echo "<span title='Vencimiento: " . $this->Time->format(__('d/m/Y'), $v['fecha_vencimiento']) . "'>" . h($bancoscuentas[$v['bancoscuenta_id']] . " - Nº " . $v['numero'] . " - " . money($v['importe'])) . "</span>" . "<br>";
                        }
                    } else {
                        echo '';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Cheques propios de Administraci&oacute;n</td>
                <td colspan=2 class="right">
                    <?php
                    $chequespropiosadm = $resul['chequespropiosadm'];
                    if (!empty($chequespropiosadm)) {
                        foreach ($chequespropiosadm as $k => $v) {
                            if ($v['Chequespropiosadmsdetalle']['importe'] > 0 && in_array($v['Chequespropiosadmsdetalle']['bancoscuenta_id'], $bancoscuentasporconsorcio[$consorcioid])) {
                                echo h($bancoscuentas[$v['Chequespropiosadm']['bancoscuenta_id']] . " - " . $bancoscuentas[$v['Chequespropiosadmsdetalle']['bancoscuenta_id']] . " - " . $v['Chequespropiosadm']['concepto'] . " - Nº " . $v['Chequespropiosadm']['numero'] . " - " . money($v['Chequespropiosadmsdetalle']['importe'])) . "<br>";
                            }
                        }
                    } else {
                        echo '';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Pagos a cuenta aplicados</td>
                <td colspan=2 class="right">
                    <?php
                    $notasdecreditoaplicadas = $resul['notasdecreditoaplicadas'];
                    if (!empty($notasdecreditoaplicadas)) {
                        foreach ($notasdecreditoaplicadas as $k => $v) {
                            $totalnc += abs($v['Proveedorspagosnc']['importe']);
                        }
                    }
                    $pagosacuentaaplicados = $resul['pagosacuentaaplicados'];
                    if (!empty($pagosacuentaaplicados)) {
                        foreach ($pagosacuentaaplicados as $k => $v) {
                            echo "<img src='" . $this->webroot . "img/icon-info.png' class='noimprimir' style='cursor:pointer' title='Ver detalle Pago a Cuenta' onclick='window.open(\"" . $this->webroot . "Proveedorspagos/view/" . $v['Proveedorspagosacuenta']['proveedorspago_id'] . "\");'/>";
                            echo h("Pago Proveedor Nº " . $v['Proveedorspagosacuenta']['proveedorspago_id'] . " - " . money($v['Proveedorspagosacuenta']['importe'])) . "<br>";
                            $totalpac += abs($v['Proveedorspagosacuenta']['importe']);
                        }
                    } else {
                        echo '';
                    }
                    // si anulo un PP con un pago a cuenta, no queda un historico del pago a cuenta q tenia. Hago la cuenta, y si hay una diferencia es q el resto era un Pago a cuenta q se aplicó
                    if ($totalfacturas - $pago['importe'] - $totalpac - $totalnc > 0) {
                        echo h("Pago a cuenta aplicado - ") . money($totalfacturas - $pago['importe'] - $totalnc);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Notas de cr&eacute;dito aplicadas</td>
                <td colspan=2 class="right">
                    <?php
                    if (!empty($notasdecreditoaplicadas)) {
                        foreach ($notasdecreditoaplicadas as $k => $v) {
                            echo "<img src='" . $this->webroot . "img/icon-info.png' class='noimprimir' style='cursor:pointer' title='Ver detalle Factura' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorsfacturas/view/" . $v['Proveedorspagosnc']['proveedorsfactura_id'] . "\");'/>";
                            echo h("Nota de Crédito Nº " . $v['Proveedorspagosnc']['proveedorsfactura_id'] . " - " . money($v['Proveedorspagosnc']['importe'])) . "<br>";
                        }
                    } else {
                        echo '';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="pri right" style='border-top:2px solid #000 !important'><b>Importe total</b></td>
                <td colspan=3 class="right" style='border-top:2px solid #000 !important'><b><?php echo "PESOS " . convertir($pago['importe']) . "<br>" . money($pago['importe']) ?></b></td>
            </tr>
        </table>
        <br/>
        <script>
            $(function () {
                var dialog = $("#rc").dialog({
                    autoOpen: false, height: "auto", width: "900", maxWidth: "900",
                    position: {at: "top top"},
                    closeOnEscape: false,
                    modal: true, buttons: {
                        Cerrar: function () {
                            $("#rc").html('');
                            dialog.dialog("close");
                        }
                    }
                });
            });
        </script>
        <?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el detalle                ?>
        <?php
// separo cada recibo en hoja separada, porq ahora pueden ser grandes
//echo "<div style='page-break-after:always'></div>";
// duplicado
//anverso($proveedorspago, $datoscliente, $datosproveedor, $this->webroot);
//reverso($proveedorspago, $facturas, $cheques, $chequespropios, $chequespropiosadm, $bancoscuentas, $efectivo, $efectivoadm, $transferencia, $transferenciaadm, $pagosacuentaaplicados, $notasdecreditoaplicadas, $users[$proveedorspago['Proveedorspago']['user_id']], $this->webroot, $consorcios);
        ?>
    </body>
</html>
<?php

//function anverso($proveedorspago, $datoscliente, $datosproveedor, $this->webroot) {
//    
//}
//
//function reverso($proveedorspago, $facturas, $cheques, $chequespropios, $chequespropiosadm, $bancoscuentas, $efectivo, $efectivoadm, $transferencia, $transferenciaadm, $pagosacuentaaplicados, $notasdecreditoaplicadas, $user, $this->webroot, $consorcios, $bancoscuentasporconsorcio) {
//    
//}

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
