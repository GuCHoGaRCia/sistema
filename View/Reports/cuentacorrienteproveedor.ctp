<?php
/*
 * Es la cuenta corriente del Proveedor, incluye:
 * Facturas y Pagos
 */
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Cuenta corriente Proveedor - <?= $proveedor['Proveedor']['name2'] ?></title>
        <?php
        if (!isset($this->request->data['pid'])) {
            // si NO viene de cobranza manual, incluyo esto. Sino no anda el link cuenta corriente y al buscar otro prop sin recargar la pagina
            // se rompe todo mal..
            echo $this->Minify->css(['jquery-ui.min']);
            echo $this->Minify->script(['jq', 'jqui']);
        }
        ?>
        <style type="text/css">
            .box-table-ax,.box-table-bx{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 10px;
                text-align: left;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                background: none !important;
            }
            .box-table-ax th,.box-table-bx th{
                font-size: 12px !important;
                padding:2px;
                color: #000;
                text-align:center;
                background: none !important;
            }
            .box-table-ax td{
                padding: 2px;
                background: none !important; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-bx td{
                padding: 2px;
                background: none !important; 
                border-left: 2px solid #aabcfe;
                border-bottom: 2px solid #aabcfe;
                color: #000;
                line-height:14px !important;
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
            .ui-dialog-titlebar-close {
                visibility: hidden;
            }
        </style>
        <style type="text/css" media="print">
            @page { size: landscape; }
        </style>
    </head>
    <body>
        <!--img src="/sistema/img/print2.png" id="print" /-->
        <?php
        // paso los datos del cliente y del consorcio
        cabecera($proveedor);
        detalle($proveedor, $saldos, $f1, $f2);

        function cabecera($proveedor) {
            ?>
            <table style='font-size:12px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif"' class="box-table-ax" align="center">
                <tr>
                    <td height="80" rowspan="3" align="center">
                        <img alt="logo" width=100 height=100 src="/sistema/img/<?= file_exists("img/" . $_SESSION['Auth']['User']['Client']['code'] . ".jpg") ? $_SESSION['Auth']['User']['Client']['code'] . ".jpg" : "0000.png" ?>" />
                    </td>
                    <td rowspan="3" align="left" valign="middle">
                        ADMINISTRACION<br/><font size=4><?= h($_SESSION['Auth']['User']['Client']['name']) ?></font>
                        <br><?= h($_SESSION['Auth']['User']['Client']['address']) ?>
                        <br><?= h($_SESSION['Auth']['User']['Client']['city']) ?>
                        <br/>CUIT: <?= h(!empty($_SESSION['Auth']['User']['Client']['cuit']) && $_SESSION['Auth']['User']['Client']['cuit'] !== "00-00000000-0" ? $_SESSION['Auth']['User']['Client']['cuit'] : '--') ?>
                        <br/>Mat.: <?= h(!empty($_SESSION['Auth']['User']['Client']['numeroregistro']) ? $_SESSION['Auth']['User']['Client']['numeroregistro'] : '--') ?>
                        <br/>Tel.: <?= h(!empty($_SESSION['Auth']['User']['Client']['telephone']) ? $_SESSION['Auth']['User']['Client']['telephone'] : '--') ?>
                        <br/>Email: <?= h(!empty($_SESSION['Auth']['User']['Client']['email']) ? $_SESSION['Auth']['User']['Client']['email'] : '--') ?>
                    </td>
                    <td align="left" height="70" rowspan="3">
                        <b>Proveedor: </b><?= h($proveedor['Proveedor']['name']) ?><br/>
                        <b>Direcci&oacute;n: </b><?= h($proveedor['Proveedor']['address']) ?><br/>
                        <b>CUIT: </b><?= h($proveedor['Proveedor']['cuit']) ?><br/>
                        <b>Matr&iacute;cula: </b><?= h($proveedor['Proveedor']['matricula']) ?><br/>
                        <b>Ciudad: </b><?= h($proveedor['Proveedor']['city']) ?><br/>
                        <b>Tel&eacute;fono: </b><?= h($proveedor['Proveedor']['telephone']) ?><br/>
                        <b>Email: </b><?= h($proveedor['Proveedor']['email']) ?><br/>
                    </td>
                </tr>
            </table>
            <?php
        }

        function detalle($proveedor, $saldos, $f1, $f2) {
            $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;margin-top:1px;margin-bottom:5px'";
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-bx" align="center">
                <thead>
                    <tr>
                        <th colspan="6" style="color:<?= $saldos['proveedor']['Proveedor']['saldo'] > 0 ? 'red' : 'green' ?>"><b><?= h(__("CUENTA CORRIENTE PROVEEDOR - SALDO ACTUAL: " . $saldos['proveedor']['Proveedor']['saldo'])) ?></b></th>
                    </tr>
                    <tr>
                        <th class="totales" style="text-align:left"><b>Concepto</b></th>
                        <th class="totales center"><b>Consorcio</b></th>
                        <th class="totales center" style="width:70px"><b>Fecha</b></th>
                        <th class="totales right" style="width:80px"><b>Factura</b></th>
                        <th class="totales right" style="width:80px"><b>Pago</b></th>
                        <th class="totales right" style="width:80px"><b>Saldo</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $saldo = $saldos['proveedor']['Proveedor']['saldo'];
                    //debug($saldos);
                    $first = true;
                    foreach ($saldos['saldos'] as $k => $v) {
                        if (strtotime($v['fecha']) > strtotime($f2)) { // la fecha es menor igual a la fecha de fin (f1 ya la comparo en Proveedor::getMovimientosProveedor())
                            if (isset($v['saldo'])) {
                                $saldo -= $v['importe'];
                            } else {
                                $saldo += $v['importe'];
                            }
                            continue;
                        }
                        if (isset($v['saldo'])) {
                            // es factura
                            echo "<tr>";
                            echo "<td style='text-align:left'>" . h($v['concepto'] . " #" . $v['numero']) . "&nbsp;<img src='/sistema/img/icon-info.png' title='Ver Factura proveedor' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"/sistema/proveedorsfacturas/view2/" . $v['id'] . "\");'/>" . "</td>";
                            echo "<td style='text-align:left'>" . h($v['Consorcio']['name'] . " - " . $v['Liquidation']['periodo']) . "</td>"; // consorcio - liquidacion
                            echo "<td style='text-align:center'>" . date('d/m/Y', strtotime($v['fecha'])) . "</td>"; // fecha
                            echo "<td>" . money($v['importe']) . "</td>"; // importe factura
                            echo "<td>--</td>"; // saldo anterior
                            if ($first) {
                                echo "<td><b>" . money($saldo) . "</b></td>"; // saldo
                                $first = false;
                            } else {
                                echo "<td>" . money($saldo) . "</td>"; // saldo
                            }

                            $saldo -= $v['importe'];
                            echo "</tr>";
                        } else {
                            // es pago
                            echo "<tr>";
                            echo "<td style='text-align:left'>" . h($v['concepto'] . " Recibo #" . $v['numero']) . "&nbsp;<img src='/sistema/img/icon-info.png' title='Ver Pago proveedor' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"/sistema/proveedorspagos/view/" . $v['id'] . "/1\");'/>" . "</td>";
                            echo "<td style='text-align:left'>--</td>"; // consorcio - liquidacion
                            echo "<td style='text-align:center'>" . date('d/m/Y', strtotime($v['fecha'])) . "</td>"; // fecha
                            echo "<td>--</td>"; // importe factura
                            echo "<td>" . money(-$v['importe']) . "</td>"; // importe pago
                            if ($first) {
                                echo "<td><b>" . money($saldo) . "</b></td>"; // saldo
                                $first = false;
                            } else {
                                echo "<td>" . money($saldo) . "</td>"; // saldo    
                            }

                            $saldo += $v['importe'];
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
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
                    $(window).on('resize', function () {
                        $(".box-table-ax").css('width', $(".box-table-bx").width());
                    });
                    $(".box-table-ax").css('width', $(".box-table-bx").width());
                });
            </script>
            <?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC   ?>
        </body>
    </html>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
