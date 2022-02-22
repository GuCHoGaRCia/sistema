<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Resumen de gastos - <?= h($consorcio['Consorcio']['name'] . " - " . $info['Liquidation']['periodo']) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?= $this->Minify->script(['jq']); ?>
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
                padding-top:20px;
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
                padding:4px;
                line-height:14px;
            }
            .cien{
                text-align:right;
                width:auto;
                border-right: 2px solid #9baff1;
            }
            .ochenta{
                text-align:right;
                width:auto;
                border-right: 2px solid #9baff1;
            }
            .box-table-a,.box-table-b p{
                font-size:12px !important;
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
        // si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
        $data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
        $client = $cliente['Client'];
        $consorcio = $consorcio['Consorcio'];
        $notas = @$info['Nota'];
        $tipoliquidacion = $info['LiquidationsType']['name'];
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($client, $consorcio, @$info['Liquidation']['periodo'], $datoscliente, $tipoliquidacion);

        // agrego la nota si existe
        if (strlen(trim($notas['resumengastotop'])) > 0) {
            echo "<div style='width:100%;max-width:742px;margin:0 auto;border:2px solid #9baff1;padding:2px'>" . $notas['resumengastotop'] . "</div>";
        }

        detalle($data, $rubrosinfo, $data['gastosinfo']);

        // agrego la nota si existe
        if (strlen(trim($notas['resumengasto'])) > 0) {
            echo "<div style='width:100%;max-width:742px;margin:0 auto;border:2px solid #9baff1;border-top:0;padding:2px'>" . $notas['resumengasto'] . "</div>";
        }

        @separacion(55);
        ?>
    </body>
</html>
<?php

function cabecera($client, $consorcio, $periodo, $datoscliente, $tipoliquidacion) {
    ?>
    <table style='font-size:11px !important;font-family:Verdana,Helvetica,sans-serif;width:100%;max-width:750px;border-bottom:0' class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <td align="left">
                <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
                <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
                <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                <p style="border-top:2px solid #9baff1;text-align:center;"><br><b>RESUMEN DE GASTOS<br><br>
                        Per&iacute;odo: </b><input type="text" style="border:0;width:200px;text-align:center" value="<?= h($periodo) ?>"/>
                    <br><b>Tipo: </b><?= h($tipoliquidacion) ?>
                </p>
            </td>
        </tr>
    </table>
    <?php
}

function detalle($data, $rubrosinfo, $gastosinfo) {
    $formato = "style='font-size:11px; font-family: Verdana, Helvetica, sans-serif;width:100%;max-width:750px'";
    $totalgeneral = 0;
    ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <th class="totales" style="padding-left:15px"><b>Rubros y conceptos</b></th>
            <?php
            if (isset($data['descripcioncoeficientes']) && count($data['descripcioncoeficientes']) > 0) {
                foreach ($data['descripcioncoeficientes'] as $v) {
                    echo "<th class='totales'><b>" . h($v) . "</b></th>";
                }
            }
            ?>
            <th class="totales" style="text-align:right"><b>TOTALES</b></th>
        </tr>
        <?php
        $rubrocount = 1;
        $totalcoeficiente = [];
        /*
         * Incluí la descripcion de los rubros en la "foto" de la tabla resumenes, por lo tanto si en la foto está $data['rubrosinfo'] quiere decir q es una liquidacion
         * prorrateada nueva (hecha a partir de este cambio), entonces se guardó en la foto la info de los rubros. Sino sigo como antes
         */
        if (isset($data['rubrosinfo']) && count($data['rubrosinfo']) > 0) {
            $rubrosinfo = $data['rubrosinfo'];
        }

        /*
         * Esto lo usa Menna, Giles y algun otro mas
         * quito del listado de rubros el ultimo, muestro normalmente y luego muestro el ultimo rubro solo, y le sumo los totales anteriores
         */

        ////////////////////////////////////////////
        $ultimo = array_slice($rubrosinfo, -1, 1, true); // obtengo el ultimo con key y value
        array_pop($rubrosinfo); // quito el ultimo
        ////////////////////////////////////////////


        foreach ($rubrosinfo as $k => $v) {
            $totalrubro = 0;
            $tiene = false;
            foreach ($data['descripcioncoeficientes'] as $o => $l) {
                $totalcadarubro[$o] = 0;
            }
            $linearubro = "<tr>";
            $linearubro .= "<td class='rubrotitle' colspan='" . (count($data['descripcioncoeficientes']) + 2) . "'><b>$rubrocount - " . h($v) . "</b></td>";
            $linearubro .= "</tr>";
            if (isset($gastosinfo) && count($gastosinfo) > 0) {
                foreach ($gastosinfo as $l => $m) {
                    $totalgasto = 0;
                    // el rubro es el actual
                    if ($m['GastosGenerale']['rubro_id'] == $k) {
                        $linearubro .= "<tr><td>" . $m['GastosGenerale']['description'];
                        if (isset($data['facturasdigitales'][$m['GastosGenerale']['id']]) && !empty(isset($data['facturasdigitales'][$m['GastosGenerale']['id']]))) {
                            $linearubro .= "<span class='noimprimir'><u>Facturas digitales:</u> ";
                            $ladj = "";
                            foreach ($data['facturasdigitales'][$m['GastosGenerale']['id']] as $facturas) {
                                $ladj .= "<a href='/sistema/adjuntos/download/" . h($facturas['url']) . "/1/0/" . $data['client']['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer' title='Descargar adjunto'>" . h($facturas['titulo']) . "</a> - ";
                            }
                            $linearubro .= substr($ladj, 0, -3) . "</span>";
                        }
                        $linearubro .= "&nbsp;</td>";
                        //si el coeficiente es el actual
                        foreach ($data['descripcioncoeficientes'] as $r => $s) {
                            if (!isset($totalcoeficiente[$r])) {
                                $totalcoeficiente[$r] = 0;
                            }
                            if (!isset($m['GastosGeneraleDetalle']['coeficiente_id'])) {// forma nueva
                                $key = find2($m['GastosGeneraleDetalle'], ['coeficiente_id' => $r]);
                                if ($key === []) {//no está (cero)
                                    $linearubro .= "<td class='ochenta'>" . money(0) . "&nbsp;</td>";
                                } else {
                                    $tiene = true;
                                    $linearubro .= "<td class='ochenta'>" . money($m['GastosGeneraleDetalle'][$key]['amount']) . "&nbsp;</td>";
                                    $totalrubro += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalcoeficiente[$r] += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalgasto += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalcadarubro[$r] += $m['GastosGeneraleDetalle'][$key]['amount'];
                                }
                            } else {
                                if ($m['GastosGeneraleDetalle']['coeficiente_id'] == $r) {
                                    $tiene = true;
                                    $linearubro .= "<td class='ochenta'>" . money($m['GastosGeneraleDetalle']['amount']) . "&nbsp;</td>";
                                    $totalrubro += $m['GastosGeneraleDetalle']['amount'];
                                    $totalcoeficiente[$r] += $m['GastosGeneraleDetalle']['amount'];
                                    $totalgasto += $m['GastosGeneraleDetalle']['amount'];
                                    $totalcadarubro[$r] += $m['GastosGeneraleDetalle']['amount'];
                                } else {
                                    $linearubro .= "<td class='cien'>0.00&nbsp;</td>";
                                }
                            }
                        }
                        $linearubro .= "<td class='cien'>" . money($totalgasto) . "&nbsp;</td></tr>";
                    } else {
                        //echo "<td>y&nbsp;</td>";
                    }
                }
            }
            if ($tiene) { // solo muestro los rubros si tienen gastos asociados
                echo $linearubro;
                echo "<tr><td style='padding:5px 5px 5px 15px'><b>TOTAL RUBRO</b></td>";
                $sumarubro = 0;
                foreach ($data['descripcioncoeficientes'] as $r => $s) {
                    echo "<td class='cien'><b>" . money($totalcadarubro[$r]) . "</b>&nbsp;</td>";
                    $sumarubro += $totalcadarubro[$r];
                }
                echo "<td class='cien'><b>" . money($sumarubro) . "</b>&nbsp;</td>";
            }
            $totalgeneral += $totalrubro;
            $rubrocount++;
        }
        echo "<tr class='totales'><td><b>TOTAL</b></td>";
        foreach ($totalcoeficiente as $tc) {
            echo "<td class='cien'><b>" . money($tc) . "</b></td>";
        }
        echo "<td class='cien'><b>" . money($totalgeneral) . "</b></td>";
        echo "</tr>";

        ////////////////////////////////////////////
        $totalcoeficiente2 = [];
        foreach ($ultimo as $k => $v) {
            foreach ($data['descripcioncoeficientes'] as $o => $l) {
                $totalcadarubro[$o] = 0;
            }
            $totalrubro = 0;
            $tiene = false;
            $linearubro = "<tr>";
            $linearubro .= "<td class='rubrotitle' colspan='" . (count($data['descripcioncoeficientes']) + 2) . "'><b>$rubrocount - " . h($v) . "</b></td>";
            $linearubro .= "</tr>";
            if (isset($gastosinfo) && count($gastosinfo) > 0) {
                foreach ($gastosinfo as $l => $m) {
                    $totalgasto = 0;
                    // el rubro es el actual
                    if ($m['GastosGenerale']['rubro_id'] == $k) {
                        $linearubro .= "<tr><td>" . $m['GastosGenerale']['description'] . "&nbsp;</td>";
                        //si el coeficiente es el actual
                        foreach ($data['descripcioncoeficientes'] as $r => $s) {
                            if (!isset($totalcoeficiente2[$r])) {
                                $totalcoeficiente2[$r] = 0;
                            }
                            if (!isset($m['GastosGeneraleDetalle']['coeficiente_id'])) {// forma nueva
                                $key = find2($m['GastosGeneraleDetalle'], ['coeficiente_id' => $r]);
                                if ($key === []) {//no está (cero)
                                    $linearubro .= "<td class='ochenta'>" . money(0) . "&nbsp;</td>";
                                } else {
                                    $tiene = true;
                                    $linearubro .= "<td class='ochenta'>" . money($m['GastosGeneraleDetalle'][$key]['amount']) . "&nbsp;</td>";
                                    $totalrubro += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalcoeficiente2[$r] += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalgasto += $m['GastosGeneraleDetalle'][$key]['amount'];
                                    $totalcadarubro[$r] += $m['GastosGeneraleDetalle'][$key]['amount'];
                                }
                            } else {
                                if ($m['GastosGeneraleDetalle']['coeficiente_id'] == $r) {
                                    $tiene = true;
                                    $linearubro .= "<td class='ochenta'>" . money($m['GastosGeneraleDetalle']['amount']) . "&nbsp;</td>";
                                    $totalrubro += $m['GastosGeneraleDetalle']['amount'];
                                    $totalcoeficiente2[$r] += $m['GastosGeneraleDetalle']['amount'];
                                    $totalgasto += $m['GastosGeneraleDetalle']['amount'];
                                    $totalcadarubro[$r] += $m['GastosGeneraleDetalle']['amount'];
                                } else {
                                    $linearubro .= "<td class='cien'>0.00&nbsp;</td>";
                                }
                            }
                        }
                        $linearubro .= "<td class='cien'>" . money($totalgasto) . "&nbsp;</td></tr>";
                    } else {
                        //echo "<td>y&nbsp;</td>";
                    }
                }
            }
            if ($tiene) { // solo muestro los rubros si tienen gastos asociados
                echo $linearubro;
                $sumarubro = 0;
                echo "<tr><td style='padding:5px 5px 5px 15px'><b>TOTAL RUBRO</b></td>";
                foreach ($data['descripcioncoeficientes'] as $r => $s) {
                    echo "<td class='cien'><b>" . money($totalcadarubro[$r]) . "</b>&nbsp;</td>";
                    $sumarubro += $totalcadarubro[$r];
                }
                $totalgeneral += $totalrubro;
                echo "<td class='cien'><b>" . money($sumarubro) . "</b>&nbsp;</td>";
                echo "<tr class='totales'><td><b>TOTAL GENERAL</b></td>";
                foreach ($totalcoeficiente2 as $l => $tc) {
                    echo "<td class='cien'><b>" . money($tc + $totalcoeficiente[$l]) . "</b></td>";
                }

                echo "<td class='cien'><b>" . money($totalgeneral) . "</b></td>";
                echo "</tr>";
            }
            /* if ($tiene) { // solo muestro los rubros si tienen gastos asociados
              echo $linearubro;
              echo "<tr><td style='padding:5px 5px 5px 15px'><b>TOTAL RUBRO</b></td>";
              $sumarubro = 0;
              foreach ($data['descripcioncoeficientes'] as $r => $s) {
              echo "<td class='cien'><b>" . money($totalcadarubro[$r]) . "</b>&nbsp;</td>";
              $sumarubro += $totalcadarubro[$r];
              }
              echo "<td class='cien'><b>" . money($sumarubro) . "</b>&nbsp;</td>";
              } */
        }

        ////////////////////////////////////////////
        ?>
    </table>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}

function find2($lista, $valor, $all = false) {
    $key = array_keys($valor);
    $value = array_values($valor);
    $resul = [];

    foreach ($lista as $k => $v) {
        //$indice = array_keys($v);
        if ($v[$key[0]] == $value[0]) {
            if ($all) {
                $resul[] = $k;
            } else {
                return $k;
            }
        }
    }
    return $resul;
}
