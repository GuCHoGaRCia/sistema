<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Resumen de gastos Particulares por Cuenta - <?= h($consorcio['Consorcio']['name'] . " - " . $info['Liquidation']['periodo']) ?></title>
        <style type="text/css">
            .encabezado {font-family: "Courier New, Courier, mono"; font-size:14px;}
            .resumen {font-family: "Courier New, Courier, mono"; font-size:11px;}
            .media {font-family: "Courier New, Courier, mono"; font-size:12px;}
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
                font-size: 10px;
                text-align: left;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                background: none;
            }
            .box-table-a th,.box-table-b th{
                font-size: 10px;
                padding: 3px;
                color: #000;
                border: 1px solid #9baff1;
            }
            .box-table-a td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-b td p{
                margin:0;
                margin-top: 0.2em; 
            }
            .box-table-b td{
                padding:0;
                padding-left: 15px;
                background: none; 
                border-left: 2px solid #aabcfe;
                border-bottom: 1px solid #aabcfe;
                color: #000;
                line-height:12px;
            }
            .tdleft{
                padding: 4px;
                background: none; 
                border:none;
                color: #000;
            }
            td.rubrotitle{
                padding:0;
                margin:0;
                border-bottom: 4px solid #aabcfe;
                line-height:12px;
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
            .totales td{
                border: 2px solid #9baff1;
                font-size: 11px;
                padding:2px;
                line-height:14px;
            }
            .cien{
                text-align:right;
                width:100px
            }
            .ochenta{
                text-align:right;width:80px
            }
        </style>
    </head>
    <body>
        <?php
        // si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
        $data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
        $client = $cliente['Client'];
        $consorcio = $consorcio['Consorcio'];
        $notas = @$info['Nota'];
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($consorcio, @$info['Liquidation']['periodo'], $datoscliente);
        detalle($data, $cuentasinfo, $data['totales'], $prop);

        @separacion(55);

        //Obtener cantidad de resumenes, si los detalles de resumen_c exceden un n�mero configurable, ese res�men se duplica.

        function cabecera($consorcio, $periodo, $datoscliente) {
            ?>
            <table style='font-size:11px;font-family:Verdana,Helvetica,sans-serif;width:780px;max-width:780px;border-bottom:0' class="box-table-a" align="center">
                <tr>
                    <?= $datoscliente ?>
                    <td align="left">
                        <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                        <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                        <b>Localidad: </b><?= h($consorcio['city']); ?><br/>
                        <b>CUIT: </b><?= h($consorcio['cuit']); ?><br/>
                        <p style="border-top:2px solid #9baff1;text-align:center;"><br><b>Per&iacute;odo: </b><?= h($periodo); ?></p>
                    </td>
                </tr>
            </table>
            <?php
        }

        function detalle($data, $cuentasinfo, $gastosinfo, $prop) {
            $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;width:780px;max-width:780px;'";
            $totalgeneral = 0;
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
                <tr><td colspan="2" style='text-align:center;font-weight:bold'>GASTOS PARTICULARES POR CUENTA</td></tr>
                <tr>
                    <th class="totales" style="width:450px"><b>Cuentas</b></th>
                    <th class="totales" style="text-align:right"><b>TOTALES</b></th>
                </tr>
                <?php
                $rubrocount = 1;
                $totalcoeficiente = $gpart = [];
                /*
                 * Incluí la descripcion de las Cuentas Gastos Particulares en la "foto" de la tabla resumenes, por lo tanto si en la foto está $data['gpinfo'] quiere decir q es una liquidacion
                 * prorrateada nueva (hecha a partir de este cambio), entonces se guardó en la foto la info de las CGP. Sino sigo como antes
                 */
                if (isset($data['gpinfo']) && count($data['gpinfo']) > 0) {
                    $cuentasinfo = $data['gpinfo'];
                }
                foreach ($cuentasinfo as $k => $v) {
                    $yamostreprorrateables = false;
                    $totalcuenta = 0;
                    $linearubro = "<tr>";
                    $linearubro .= "<td colspan=2 class='rubrotitle'><b>$rubrocount - " . h($v) . "</b></td>";
                    $linearubro .= "</tr>";
                    if (isset($gastosinfo) && count($gastosinfo) > 0) {
                        foreach ($gastosinfo as $l => $m) {
                            if (isset($m['detalle'])) {
                                foreach ($m['detalle'] as $oo) {
                                    if (isset($oo['cuenta']) && $oo['cuenta'] == $k) {
                                        $linearubro .= "<tr><td>" . h($oo['descripcion']) . " - " . h($prop[$l]['name']) . " (" . h($prop[$l]['unidad']) . ")</td>";
                                        $linearubro .= "<td class='cien'>" . h($oo['total']) . "&nbsp;</td></tr>";
                                        $totalcuenta += $oo['total'];
                                    }
                                }
                            }
                            if (!$yamostreprorrateables && isset($m['coefpar'])) {// en cada prop esta el prorrateable, lo muestro 1 sola vez
                                foreach ($m['coefpar'] as $oo) {
                                    if (isset($oo['detalle']) && !empty($oo['detalle'])) {
                                        foreach ($oo['detalle'] as $oo1) {
                                            if (isset($oo1['cuenta']) && $oo1['cuenta'] == $k) {
                                                $linearubro .= "<tr><td>" . h($oo1['descripcion']) . "</td>";
                                                $linearubro .= "<td class='cien'>" . h($oo1['total']) . "&nbsp;</td></tr>";
                                                $totalcuenta += $oo1['total'];
                                            }
                                        }
                                    }
                                }
                                $yamostreprorrateables = true;
                            }
                        }
                    }
                    if ($totalcuenta != 0) { // solo muestro los rubros si tienen gastos asociados
                        echo $linearubro;
                        echo "<tr><td><b>TOTAL CUENTA</b></td>";
                        echo "<td class='cien'>" . money($totalcuenta) . "&nbsp;</td><tr>";
                    }
                    $totalgeneral += $totalcuenta;
                    $rubrocount++;
                }
                echo "<tr class='totales'><td><b>TOTAL GENERAL</b></td>";
                echo "<td class='cien'><b>" . money($totalgeneral) . "</b></td>";
                echo "</tr>";
                ?>
            </table>
            <br/>
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
