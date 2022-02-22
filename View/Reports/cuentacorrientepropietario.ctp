<?php
/*
 * Es la cuenta corriente del propietario, incluye:
 * Expensas, cobranzas, ajustes, gastosgenerales, gastosparticulares
 */
// los saldos iniciales vienen en un arreglo (0,1,2). Pongo liquidations_type_id como key
$data['iniciales'] = Hash::combine($data['iniciales'], '{n}.SaldosIniciale.liquidations_type_id', '{n}.SaldosIniciale');

$totalesxtipo = []; // para setear el importe total de deuda de cada tipo de liq
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Cuenta corriente Propietario - <?= h($propietario['Propietario']['name2']) ?></title>
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
                background: none; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-bx td{
                padding: 2px;
                background: none; 
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
            .verde{
                background:lightgreen !important;
            }
            @media print{
                .box-table-bx td{ background:none !important}
            }
        </style>
    </head>
    <body>
        <!--img src="/sistema/img/print2.png" id="print" /-->
        <?php
        // paso los datos del cliente y del consorcio
        $client = $cliente['Client'];
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($propietario['Propietario'], $consorcio, $datoscliente);
        detalle($lt, $propietario['Propietario'], $data, $f1, $f2, $movimientos, $this->webroot, $origen);

        function cabecera($propietario, $consorcio, $datoscliente) {
            ?>
        <table style='font-size:12px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:800px;max-width:800px;' class="box-table-ax" align="center">
            <tr>
                    <?= $datoscliente ?>
                <td align="left">
                    <b>Consorcio: </b><?= h($consorcio['Consorcio']['name']) ?><br/>
                    <b>Propietario: </b><?= h($propietario['name']) ?><br/>
                    <b>Unidad: </b><?= h($propietario['unidad']) ?><br/>
                    <b>Domicilio: </b><?= h($propietario['postal_address']) ?><br/>
                    <b>Ciudad: </b><?= h($propietario['postal_city']) ?><br/>
                </td>
            </tr>
        </table>
            <?php
        }

        function detalle($lt, $p, $data, $f1, $f2, $movs, $webroot, $origen) {                        
            $fechaHasta = substr($f2, 0, 10);                                   
            $fechaActual = 0;          
            if($fechaHasta >= date('Y-m-d')){
                $fechaActual = 1;
            }            
            
            $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;width:800px;max-width:800px;margin-top:1px;margin-bottom:5px'";
            foreach ($lt as $k => $v) {
                ?>
        <table valign=top cellspacing=0 <?= $formato ?> class="box-table-bx" align="center">
            <thead>
                <tr>
                    <th colspan="12"><b><?= h($v) ?></b></th>
                </tr>
                <tr>
                    <th class="totales" style="width:250px;text-align:left"><b>Per&iacute;odo</b></th>
                    <th class="totales center" style="width:90px"><b>Fecha</b></th>
                    <th class="totales center" style="width:90px"><b>SAC</b></th>
                    <th class="totales center" style="width:90px"><b>SAI</b></th>
                    <th class="totales right" style="width:120px"><b>Cobranza</b></th>
                    <th class="totales right" style="width:100px"><b>Ajustes</b></th>
                    <th class="totales right" style="width:100px"><b>Remanente</b></th>
                    <th class="totales right" style="width:120px"><b>Expensa</b></th>
                    <th class="totales right" style="width:100px"><b>IA</b></th>
                    <th class="totales center" style="width:40px"><b>RANT</b></th>
                    <th class="totales center" style="width:40px"><b>RACT</b></th>
                    <th class="totales right" style="width:120px"><b>Total</b></th>
                </tr>
            </thead>
            <tr>
                        <?php
                        $first = true;
                        $cobranza = $ajustes = $suma = $redondeoactual = $cap = $int = 0;
                        // muestro el saldo inicial sii está dentro de las fechas indicadas y existe
                        if (isset($data['iniciales'][$k]) && strtotime($data['iniciales'][$k]['created']) > strtotime($f1) && strtotime($data['iniciales'][$k]['created']) < strtotime($f2)) {
                            echo "<tr>";
                            echo "<td style='text-align:left'>SALDO INICIAL</td>"; // periodo
                            echo "<td style='text-align:center'>" . date('d/m/Y', strtotime($data['iniciales'][$k]['created'])) . "</td>"; // fecha cierre
                            echo "<td>&nbsp;</td>"; // Saldo anterior cap
                            echo "<td>&nbsp;</td>"; // Saldo anterior int
                            echo "<td>&nbsp;</td>"; // cobranza
                            echo "<td>&nbsp;</td>"; // ajustes
                            echo "<td>&nbsp;</td>"; // remanente
                            echo "<td>&nbsp;</td>"; // expensa
                            echo "<td>&nbsp;</td>"; // interes actual
                            echo "<td>&nbsp;</td>"; // Redondeo anterior
                            echo "<td>&nbsp;</td>"; // redondeo actual
                            echo "<td>" . money($data['iniciales'][$k]['capital'] + $data['iniciales'][$k]['interes']) . "</td>"; // total
                            echo "</tr>";
                            $first = false;
                            $cap = $data['iniciales'][$k]['capital'];
                            $int = $data['iniciales'][$k]['interes'];
                            $suma = $data['iniciales'][$k]['capital'] + $data['iniciales'][$k]['interes'];
                        }

                        foreach ($data as $datak => $datav) {
                            if ($datak === 'saldos') {
                                foreach ($datav as $r => $j) {
                                    //debug($j);
                                    if (!isset($j['Liquidation']['liquidations_type_id']) || $j['Liquidation']['liquidations_type_id'] !== "$k") {// es un tipo de liquidacion q no es la actual ($k)
                                        continue;
                                    }
                                    $cobranza = (float) $j['SaldosCierre']['cobranzas'];
                                    $ajustes = (float) $j['SaldosCierre']['ajustes'];
                                    /* if ($cobranza > 0) {
                                      // muestro la cobranza q pertenece al periodo anterior
                                      //-$j['SaldosCierre']['cobranzas']
                                      if (!$first) {
                                      echo "<tr>";
                                      echo "<td style='text-align:left'>&nbsp;</td>"; // periodo
                                      echo "<td style='text-align:center'>&nbsp;</td>"; // fecha cierre
                                      echo "<td>&nbsp;</td>"; // Saldo anterior
                                      echo "<td>" . money(-$cobranza) . "</td>"; // cobranza
                                      echo "<td>" . money(-$ajustes) . "</td>"; // ajustes
                                      echo "<td>&nbsp;</td>"; // remanente
                                      echo "<td>&nbsp;</td>"; // expensa
                                      echo "<td>&nbsp;</td>"; // interes actual
                                      echo "<td>&nbsp;</td>"; // Redondeo anterior
                                      echo "<td>&nbsp;</td>"; // redondeo actual
                                      echo "<td>" . money($suma - $redondeoactual - $cobranza - $ajustes) . "</td>"; // total
                                      echo "</tr>";
                                      }
                                      $saldo = $suma - $redondeoactual - $cobranza - $ajustes;
                                      } else {
                                      $saldo = intval($j['SaldosCierre']['capant'] + $j['SaldosCierre']['intant']);
                                      } */
                                    echo "<tr>";
                                    // abro en una pestaña nueva la cuenta corriente porq sino no la pueden imprimir desde la "i" pffff
                                    //echo "<td style='text-align:left'>" . h($j['Liquidation']['periodo']) . "&nbsp;<img src='" . $webroot . "img/icon-info.png' style='cursor:pointer' title='Ver resumen de cuenta' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").html(\"<br>Cargando datos, espere por favor... <img src=" . $webroot . "img/loading.gif />\");$(\"#rc\").load(\"" . $webroot . "reports/resumencuenta/" . $j['Liquidation']['id'] . "/" . $p['id'] . "\");'/>" . "</td>"; // periodo
                                    echo "<td style='text-align:left'>" . h($j['Liquidation']['periodo']) . "&nbsp;<img src='" . $webroot . "img/icon-info.png' style='cursor:pointer' title='Ver resumen de cuenta' onclick='window.open(\"" . $webroot . "reports/resumencuenta/" . $j['Liquidation']['id'] . "/" . $p['id'] . "\")'/></td>"; // periodo
                                    echo "<td style='text-align:center'>" . date('d/m/Y', strtotime($j['Liquidation']['vencimiento'])) . "</td>"; // fecha cierre
                                    $remanente = $j['SaldosCierre']['capant'] + $j['SaldosCierre']['intant'] - $j['SaldosCierre']['redant'] - $cobranza - $ajustes;
                                    $expensa = $j['SaldosCierre']['gastosgenerales'] + $j['SaldosCierre']['gastosparticulares'];
                                    $suma = $remanente + $expensa + $j['SaldosCierre']['interesactual'] + $j['SaldosCierre']['redant'];
                                    echo "<td>" . money(round($j['SaldosCierre']['capant'], 2) - $redondeoactual) . "</td>"; // capital anterior
                                    $redondeoactual = (float) ($suma - intval($suma));
                                    echo "<td>" . money(round($j['SaldosCierre']['intant'], 2)) . "</td>"; // interes anterior
                                    echo "<td>" . money(-$cobranza) . "</td>"; // cobranzas
                                    echo "<td>" . money(-$ajustes) . "</td>"; // ajustes

                                    echo "<td " . ($remanente <= 0 ? ' class="verde"' : '') . ">" . money($remanente) . "</td>"; // remanente

                                    echo "<td>" . money($expensa) . "</td>"; // expensa
                                    echo "<td>" . money($j['SaldosCierre']['interesactual']) . "</td>"; // interes actual
                                    echo "<td>" . money($j['SaldosCierre']['redant']) . "</td>"; // redondeo anterior

                                    echo "<td>" . money(-$redondeoactual) . "</td>"; // redondeo actual
                                    echo "<td title='Cap: " . $j['SaldosCierre']['capital'] . " - Int: " . $j['SaldosCierre']['interes'] . "'>" . money($suma - $redondeoactual) . "</td>"; // total
                                    echo "</tr>";
                                    $cap = $j['SaldosCierre']['capital'];
                                    $int = $j['SaldosCierre']['interes'];
                                    $first = false;
                                }
                            }
                        }                        
                        if ( ( (isset($origen) && $origen === '1') or ($fechaActual === 1) ) && isset($data['saldos']['abiertas'][$k])) {     // origen se setea con el valor 1 si se entra a cobranzas manuales o al agregar un ajuste desde (cobranzas->ajustes), esto es cuando se muestra la cuenta corriente x defecto
                            muestraAbiertas($data['saldos']['abiertas'][$k], $cap, $int, $p['id'], $webroot);
                        }  
                        
                        echo "</table>";
                    }
                    /*
                      $("#print").on("click", function () {
                      window.print();
                      });
                     */
                    ?>
            <script>
                $(function () {
                    var dialog = $("#rc").dialog({
                        autoOpen: false, height: "auto", width: "900", maxWidth: "900",
                        position: {at: "center top"},
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
                <?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC  ?>
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

function muestraAbiertas($ja, $cap, $int, $p, $webroot) {
    foreach ($ja as $j) {
        echo "<tr>";
        echo "<td style='text-align:left'>" . h($j['Liquidation']['periodo']) . "&nbsp;<img src='" . $webroot . "img/icon-info.png' title='Ver cobranzas recibidas' style='cursor:pointer' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").html(\"<br>Cargando datos, espere por favor... <img src=" . $webroot . "img/loading.gif />\");$(\"#rc\").load(\"" . $webroot . "cobranzas/listar/" . $j['Liquidation']['id'] . "/" . $p . "\");'/>" . "</td>"; // periodo
        echo "<td style='text-align:center'><b>En proceso</b></td>"; // fecha cierre
        echo "<td>" . money($cap) . "</td>"; // saldo anterior cap
        echo "<td>" . money($int) . "</td>"; // saldo anterior int
        echo "<td>" . money(-$j['SaldosCierre']['cobranzas']) . "</td>"; // cobranzas
        echo "<td>" . money(-$j['SaldosCierre']['ajustes']) . "</td>"; // cobranzas
        echo "<td>" . money($cap + $int - $j['SaldosCierre']['cobranzas'] - $j['SaldosCierre']['ajustes']) . "</td>"; // remanente
        echo "<td>&nbsp;</td>"; // expensa
        echo "<td>&nbsp;</td>"; // interes actual
        echo "<td>&nbsp;</td>"; // redondeo anterior
        echo "<td>&nbsp;</td>"; // redondeo actual
        echo "<td>&nbsp;</td>"; // total
        echo "</tr>";
    }
}
