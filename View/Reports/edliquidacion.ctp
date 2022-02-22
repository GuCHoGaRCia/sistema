<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$a = (!empty($data['Resumene']['data']) ? $data['Resumene']['data'] : $data);
if (empty($data)) {
    echo "No se encuentran datos<br>";
} else {
    $data = json_decode($a, true);
    ?>
    <!DOCTYPE html>
    <html lang="es-419">
        <head>
            <title>Estado de Disponibilidad - <?= h($consorcio['Consorcio']['name']) ?></title>
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
                    border-top: 2px solid #9baff1 !important;
                    color: #000; 
                }
                .box-table-b td{
                    padding: 2px;
                    background: none; 
                    border-left: 2px solid #aabcfe;
                    border-bottom: 1px solid #aabcfe;
                    color: #000;
                    line-height:10px;
                    text-align:left;
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
                    font-size: 14px;
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
            $client = $cliente['Client'];
            $consorcio = $consorcio['Consorcio'];
            $periodo = $info['Liquidation']['periodo'];
            $gastosinfo = $data['gastosinfo'];
            $consorciointeres = $consorcio['interes'];
            $datoscliente = $this->element('datoscliente', ['dato' => $client]);
            $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio]);
            ?>
            <table style='font-size:11px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:750px;max-width:750px;border-bottom:0px' class="box-table-a" align="center">
                <tr>
                    <?= $datoscliente ?>
                    <?= $datosconsorcio ?>
                </tr>
            </table>
            <?php
            $formato = "style='font-size:11px; font-family:Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
            $totalgeneral = 0;
            $totalcobranzas = 0;

            foreach ($data['cobranzas'] as $v) {
                $totalcobranzas += round($v['Cobranzatipoliquidacione']['amount'], 2);
            }
            if ($info['LiquidationsType']['prefijo'] == 0) {// solo para ordinarias
                ?>
                <table border="2px" valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
                    <tr class="totales" style="font-size:15px">
                    <tr>
                        <td class="totales" style="width:750px;text-align:center;padding:10px" colspan="4">
                            <b>Estado Disponibilidad Per&iacute;odo: </b><?= h($periodo); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="totales" style='text-align:left'>
                            <b>Concepto</b>
                        </td>
                        <td class="totales" style="width:100px;text-align:right">
                            <b>Debe</b>
                        </td>
                        <td class="totales" style="width:100px;text-align:right">
                            <b>Haber</b>
                        </td>
                        <td class="totales" style="width:100px;text-align:right">
                            <b>Saldo</b>
                        </td>
                    </tr>
                </tr>
                <?php
                $saldo = round($disponibilidad, 2);
                echo "<tr><td style='text-align:left'><b>Saldo anterior</b></td><td></td><td></td>";
                echo "<td style='text-align:right'><b>" . $this->Functions->money($saldo) . "</b></td></tr>";

                $ingresos = $totalcobranzas;
                $saldo += $ingresos;
                $egresos = $gg;

                echo "<tr><td style='text-align:left'><b>Cobranzas</b></td>";
                echo "<td style='text-align:right'>" . $this->Functions->money($ingresos) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

                $saldo -= $egresos;
                echo "<tr><td style='text-align:left'><b>Gastos</b></td>";
                echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($egresos) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

                $saldo += array_sum($resumen['ingresosmanuales']);
                echo "<tr><td style='text-align:left'><b>Ingresos manuales</b></td>";
                echo "<td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['ingresosmanuales'])) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

                $saldo -= array_sum($resumen['egresosmanuales']);
                echo "<tr><td style='text-align:left'><b>Egresos manuales</b></td>";
                echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['egresosmanuales'])) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

                echo "<tr><td style='width:300px;text-align:center' class='totales'><b>Total<b></td>";
                echo "<td class='totales'>&nbsp;</td><td class='totales'>&nbsp;</td><td class='totales' style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
                ?>
            </table>
            <br/>
            <?php
        }
        ?>
        <table border="2px" valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
            <tr class="totales" style="font-size:15px">
            <tr>
                <td class="totales" style="width:750px;text-align:center;padding:10px" colspan="4">
                    <b>Estado Disponibilidad PAGA Per&iacute;odo: </b><?= h($periodo); ?>
                </td>
            </tr>
            <tr>
                <td class="totales" style='text-align:left'>
                    <b>Concepto</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Debe</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Haber</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Saldo</b>
                </td>
            </tr>
        </tr>
        <?php
        $saldo = round($disponibilidadpaga, 2);
        echo "<tr><td style='text-align:left'><b>Saldo anterior</b></td><td></td><td></td>";
        echo "<td style='text-align:right'><b>" . $this->Functions->money($saldo) . "</b></td></tr>";
        $ingresos = $totalcobranzas;
        $saldo += $ingresos; // en egresospagosproveedor estan incluidos los egresospagosacuenta, por eso lo comentamos! (gracias mati!)
        /* if ($info['LiquidationsType']['prefijo'] == 0) {
          $egresos = array_sum($resumen['egresospagosacuenta']) + $resumen['egresospagosproveedorefectivo'] + $resumen['egresospagosproveedorcheque'] + $resumen['egresospagosproveedorchequepropio'] + $resumen['egresospagosproveedortransferencia'] +
          $resumen['egresospagosproveedorefectivoadm'] + $resumen['egresospagosproveedorchequepropioadm'] + $resumen['egresospagosproveedortransferenciaadm'];
          } else { */
        $egresos = $gastospagos /* + array_sum($resumen['egresospagosacuenta']) */;
        //}

        echo "<tr><td style='text-align:left'><b>Cobranzas</b></td>";
        echo "<td style='text-align:right'>" . $this->Functions->money($ingresos) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

        $saldo -= $egresos;
        echo "<tr><td style='text-align:left'><b>Gastos pagos</b></td>";
        echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($egresos) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

        if ($info['LiquidationsType']['prefijo'] == 0) {// solo para ordinarias
            $saldo += array_sum($resumen['ingresosmanuales']);
            echo "<tr><td style='text-align:left'><b>Ingresos manuales</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['ingresosmanuales'])) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= array_sum($resumen['egresosmanuales']);
            echo "<tr><td style='text-align:left'><b>Egresos manuales</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['egresosmanuales'])) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";


            $saldo += $resumen['ingresoscreditos'];
            echo "<tr><td style='text-align:left'><b>Cr&eacute;ditos</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($resumen['ingresoscreditos']) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= $resumen['egresosdebitos'];
            echo "<tr><td style='text-align:left'><b>D&eacute;bitos</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($resumen['egresosdebitos']) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo += $resumen['ingresostransferenciasinterbancos'];
            echo "<tr><td style='text-align:left'><b>Ingresos transferencias interbancarias</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($resumen['ingresostransferenciasinterbancos']) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= $resumen['egresostransferenciasinterbancos'];
            echo "<tr><td style='text-align:left'><b>Egresos transferencias interbancarias</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($resumen['egresostransferenciasinterbancos']) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        }


        echo "<tr><td style='width:300px;text-align:center' class='totales'><b>Total<b></td>";
        echo "<td class='totales'>&nbsp;</td><td class='totales'>&nbsp;</td><td class='totales' style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        ?>
    </table>
    <?= "<div style='page-break-after:always'></div>" ?>
    </body>
    </html>
    <?php
}
?>