<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Pagos Proveedor</title>
        <?php
        echo $this->Minify->script(['jq', 'jqui']);
        echo $this->Minify->css(['jquery-ui.min']);
        ?>
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
                font-size: 12px !important;
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
        $datoscliente = $this->element('datoscliente', ['dato' => $_SESSION['Auth']['User']['Client']]);
        cabecera($consorcio['Consorcio'], $datoscliente, $periodo);
        detalle($pagos, $this->webroot);

        @separacion(55);
        ?>
    </body>
</html>
<?php

function cabecera($consorcio, $datoscliente, $periodo) {
    ?>
    <table style='font-size:11px;width:750px;max-width:750px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <td align="left">
                <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <p style="border-top:2px solid #9baff1;text-align:center;line-height:14px;"><br>
                    <b>PAGOS PROVEEDORES<br>Per&iacute;odo: </b><?= h($periodo) ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function detalle($pagos, $webroot) {
    $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <thead>
            <tr>
                <th class="totales" style="width:60px"><b>Fecha</b></th>
                <th class="totales" style="width:150px"><b>Proveedor</b></th>
                <th class="totales" style="width:220px"><b>Concepto</b></th>
                <th class="totales" style="width:200px"><b>Forma de pago</b></th>
                <th class="totales" style="width:100px"><b>Importe</b></th>
            </tr>
        </thead>
        <?php
        $total = 0;

        $totalFormasPago = ['EFECTIVO' => 0, 'EFECTIVO ADMINISTRACION' => 0, 'TRANSFERENCIA' => 0, 'TRANSFERENCIA ADMINISTRACION' => 0, 'CHEQUES TERCEROS' => 0, 'CHEQUES PROPIOS' => 0, 'CHEQUES PROPIOS ADMINISTRACION' => 0, 'PAGOS A CUENTA APLICADOS' => 0, 'NOTAS DE CREDITO APLICADAS' => 0];

        foreach ($pagos as $l => $resul) {

            $proveedorspago = $resul['proveedorspago'];
            $bancoscuentasporconsorcio = $resul['bancoscuentasporconsorcio'];

            $nrosFacturas = '';

            $facturas = $resul['facturas'];
            if (!empty($facturas)) {
                foreach ($facturas as $k => $v) {
                    $consorcioid = $v['Consorcio']['id'];
                    $id = $v['Proveedorsfactura']['id'];
                    $nrosFacturas .= "<a onclick = abrir('" . $id . "'); href='#'>" . h($v['Proveedorsfactura']['numero']) . '</a>, ';
                }
            }
            if (isset($proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id']) && !empty($proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id'])) {
                $consorcioid = $proveedorspago['Proveedorspagosacuenta'][0]['consorcio_id'];
            }

            echo "<tr>";
            echo "<td style='text-align:center'>" . CakeTime::format(__('d/m/Y'), $resul['proveedorspago']['Proveedorspago']['fecha']) . "</td>";
            echo "<td style='text-align:left'>" . h($resul['proveedorspago']['Proveedor']['name']) . "</td>";
            echo "<td style='text-align:left'>" . "<img src='" . $webroot . "img/icon-info.png' class='noimprimir' style='cursor:pointer' title='Ver detalle pago a Proveedor' onclick='window.open(\"" . $webroot . "Proveedorspagos/view/" . $l . "\");'/>" . " " . h($resul['proveedorspago']['Proveedorspago']['concepto']) . "&nbsp;&nbsp;&nbsp;&nbsp;";
            if (!empty($nrosFacturas)) {
                echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facturas: " . substr($nrosFacturas, 0, -2);
            }
            echo "</td>";

            // fila de columna forma de pago
            echo "<td style='text-align:left'>";

            $efectivo = $resul['efectivo'];
            if (!empty($efectivo) && $efectivo['Cajasegreso']['importe'] > 0) {
                $totalFormasPago['EFECTIVO'] += $efectivo['Cajasegreso']['importe'];
                echo h("Efectivo: " . money($efectivo['Cajasegreso']['importe'])) . "<br>";
            }

            $efectivoadm = $resul['efectivoadm'];
            if (!empty($efectivoadm)) {
                $sumaEfectivoAdm = 0;
                foreach ($efectivoadm as $k => $v) {
                    foreach ($v['Administracionefectivosdetalle'] as $r) {
                        if ($r['consorcio_id'] == $consorcioid) {
                            $sumaEfectivoAdm += $r['importe'];
                        }
                    }
                }
                $totalFormasPago['EFECTIVO ADMINISTRACION'] += $sumaEfectivoAdm;
                echo h("Efectivo Adm.: " . money($sumaEfectivoAdm)) . "<br>";
            }

            $transferencia = $resul['transferencia'];
            if (!empty($transferencia)) {
                $sumaTransferencia = 0;
                foreach ($transferencia as $k => $v) {
                    $sumaTransferencia += $v['Bancosextraccione']['importe'];
                }
                $totalFormasPago['TRANSFERENCIA'] += $sumaTransferencia;
                echo h("Transferencia: " . money($sumaTransferencia)) . "<br>";
            }


            $transferenciaadm = $resul['transferenciaadm'];
            if (!empty($transferenciaadm)) {
                $sumaTransferenciaAdm = 0;
                foreach ($transferenciaadm as $k => $v) {
                    foreach ($v['Administraciontransferenciasdetalle'] as $r) {
                        if (in_array($r['bancoscuenta_id'], $bancoscuentasporconsorcio[$consorcioid])) {
                            $sumaTransferenciaAdm += $r['importe'];
                        }
                    }
                }
                $totalFormasPago['TRANSFERENCIA ADMINISTRACION'] += $sumaTransferenciaAdm;
                echo h("Transferencia Adm.: " . money($sumaTransferenciaAdm)) . "<br>";
            }

            //Cheques de terceros
            $cheques = $resul['cheques'];
            if (!empty($cheques)) {
                $sumaCheques = 0;
                if (!empty($proveedorspago['Proveedorspagoscheque'])) {
                    foreach ($proveedorspago['Proveedorspagoscheque'] as $k => $v) {
                        $sumaCheques += $cheques[$v['cheque_id']]['importe'];
                    }
                }
                $totalFormasPago['CHEQUES TERCEROS'] += $sumaCheques;
                echo h("Cheques Terceros: " . money($sumaCheques)) . "<br>";
            }

            $chequespropios = $resul['chequespropios'];
            if (!empty($chequespropios)) {
                $sumaChequesPropios = 0;
                foreach ($chequespropios as $k => $v) {
                    $sumaChequesPropios += $v['importe'];
                }
                $totalFormasPago['CHEQUES PROPIOS'] += $sumaChequesPropios;
                echo h("Cheques Propios: " . money($sumaChequesPropios)) . "<br>";
            }


            $chequespropiosadm = $resul['chequespropiosadm'];
            if (!empty($chequespropiosadm)) {
                $sumaChequesPropiosAdm = 0;
                foreach ($chequespropiosadm as $k => $v) {
                    if ($v['Chequespropiosadmsdetalle']['importe'] > 0 && in_array($v['Chequespropiosadmsdetalle']['bancoscuenta_id'], $bancoscuentasporconsorcio[$consorcioid])) {
                        $sumaChequesPropiosAdm += $v['Chequespropiosadmsdetalle']['importe'];
                    }
                }
                $totalFormasPago['CHEQUES PROPIOS ADMINISTRACION'] += $sumaChequesPropiosAdm;
                echo h("Cheques Propios Adm.: " . money($sumaChequesPropiosAdm)) . "<br>";
            }

            $pagosacuentaaplicados = $resul['pagosacuentaaplicados'];
            if (!empty($pagosacuentaaplicados)) {
                $sumaPagosACuentaAplicados = 0;
                foreach ($pagosacuentaaplicados as $k => $v) {
                    $sumaPagosACuentaAplicados += $v['Proveedorspagosacuenta']['importe'];
                }
                $totalFormasPago['PAGOS A CUENTA APLICADOS'] += $sumaPagosACuentaAplicados;
                echo h("Pagos Cuenta Aplicados: " . money($sumaPagosACuentaAplicados)) . "<br>";
            }


            $notasdecreditoaplicadas = $resul['notasdecreditoaplicadas'];
            if (!empty($notasdecreditoaplicadas)) {
                $sumaNotasDeCreditoAplicadas = 0;
                foreach ($notasdecreditoaplicadas as $k => $v) {
                    $sumaNotasDeCreditoAplicadas += $v['Proveedorspagosnc']['importe'];
                }
                $totalFormasPago['NOTAS DE CREDITO APLICADAS'] += $sumaNotasDeCreditoAplicadas;
                echo h("Notas Crédito Aplicadas: " . money($sumaNotasDeCreditoAplicadas));
            }

            echo "</td>";

            echo "<td style='text-align:right'>" . money($resul['proveedorspago']['Proveedorspago']['importe']) . "</td>";

            echo "</tr>";
            $total += $resul['proveedorspago']['Proveedorspago']['importe'];
        }
        ?>

        <?php
        foreach ($totalFormasPago as $k => $v) {
            if ($v > 0) {
                ?>        
                <tr class="totales" >
                    <td style='text-align:left' colspan="3"><?= h($k) ?></td>
                    <td><?= money($v) ?></td>
                    <td></td>
                </tr>
                <?php
            }
        }
        ?>

                                                                <!--        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">EFECTIVO</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">EFECTIVO DE ADMINISTRACION</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">TRANSFERENCIA</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">TRANSFERENCIA DE ADMINISTRACION</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">CHEQUES DE TERCEROS</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">CHEQUES PROPIOS</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">CHEQUES PROPIOS DE ADMINISTRACION</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">PAGOS A CUENTA APLICADOS</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                        <tr class="totales" >
                                                                            <td style='text-align:left' colspan="3">NOTAS DE CREDITO APLICADAS</td>
                                                                            <td><?= money($total) ?></td>
                                                                            <td></td>
                                                                        </tr>-->
        <tr class="totales" >
            <td style='text-align:left' colspan="4">TOTAL</td>
            <td><?= money($total) ?></td>
        </tr>
    </table>
    <br/>

    <script>
        function abrir(id) {
            $("#rc").dialog("open");
            $("#rc").html("<div class='info' style='width:200px;margin:0 auto'>Cargando...<img src='<?= $webroot ?>img/loading.gif'/></div>");
            $("#rc").load("<?= $webroot ?>Proveedorsfacturas/view/" + id);
        }
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
                },
                open: function () {
                    event.preventDefault();
                }
            });
        });
    </script>
    <?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el detalle            ?>

    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
