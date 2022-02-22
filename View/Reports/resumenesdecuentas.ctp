<?php
// esta variable la utilizo para mostrar el icon-info en cada cobranza, para mostrar el detalle de la misma en un jquery dialog.
// cuando el admin entra desde el panel del propietario, está logueado, asi que agrego los Minify. En el panel del Propietario
// que accede con el link, no lo necesito, asi q lo saco
$muestraIconoDetalleCobranza = $this->Session->check('Auth.User');
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Resumen de cuenta - <?= h($consorcio['Consorcio']['name'] . " - " . $info['Liquidation']['periodo']) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']); ?>
        <style type="text/css">
            @font-face {
                font-family: '2of5';
                src: url('/sistema/fonts/2of5.woff');
            }
            .barcode1{
                font-family: '2of5';
                font-size:30.6pt;
            }
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 11px;
                text-align: center;
                line-height:9px;
                border: 2px solid #000;
                border-collapse: collapse;
            }
            .box-table-a th,.box-table-b th{
                font-size: 11px;
                font-weight: normal;
                padding: 8px;
                background: none;
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
                text-align:left !important;
                width:120px;
                border:none !important;
            }
            .totales{
                border: 2px solid #000;
                font-weight: 700;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <?php
        // si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
        $data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
        $client = isset($data['client']['Client']) ? $data['client']['Client'] : $cliente['Client']; // si existe en el cierre la info del cliente (al prorratear), sino la actual

        /*
         * Para multiplataforma, si tiene configurada alguna, la seteo en usa_plapsa y en $client el detalle, asi no tengo q hacer tanto cambio
         * en $plataforma tengo la plataforma del cliente (reportescomponent)
         * en $plataformas tengo las plataformas disponibles (reportescomponent)
         * en $data['plataforma']['Plataformasdepagosconfig'], la info de la plataforma para las liquidaciones prorrateadas luego de la implementacion de la multiplataforma
         */
        $consorcio = isset($data['consorcio']) ? $data['consorcio'] : $consorcio['Consorcio'];
        $client['usa_plapsa'] = isset($data['plataforma']['Plataformasdepagosconfig']['plataformasdepago_id']) ? $data['plataforma']['Plataformasdepagosconfig']['plataformasdepago_id'] : $client['usa_plapsa']; // si es un resumen viejo, veo el usa_plapsa q habia antes
        if ($client['usa_plapsa']) {
            $datosplataforma = $data['plataforma']['Plataformasdepagosconfig'] ?? $data['plataforma'] ?? $plataforma['Plataformasdepagosconfig'];
            if (!isset($data['plataforma'])) {// resumenes viejos, para q muestre el codigo de barras y la comision cobrada
                $data['plataforma'] = $plataforma;
                $data['plataformas'] = $plataformas;
                $client['minimo'] = 25;
                $client['comision'] = 3.1;
            } else {
                $client['minimo'] = $datosplataforma['minimo'];
                $client['comision'] = $datosplataforma['comision'];
            }
            $idplataforma = $datosplataforma['plataformasdepago_id'];
            if ($idplataforma == 3) {//es roela
                $pos = $this->Functions->find2($data['plataforma']['Plataformasdepagosconfigsdetalle'], ['consorcio_id' => $consorcio['id']]);
                if ($pos == []) {
                    die("Convenio Roela no encontrado");
                }
                $client['datointerno'] = $data['plataforma']['Plataformasdepagosconfigsdetalle'][$pos]['valor'];
            } else {
                $client['datointerno'] = $datosplataforma['datointerno'];
            }
            $client['codigo'] = $datosplataforma['codigo'];
            $client['plataforma'] = $data['plataformas'][$idplataforma]['modelo'];
            $client['titulo'] = $data['plataformas'][$idplataforma]['titulo'];
        } else {
            $client['plataforma'] = 'Plapsa';
        }
        /*         * ********************************************************************************************************* */
        $notas = $info['Nota'] ?? [];
        $prefijo = $info['LiquidationsType']['prefijo'] ?? 0; // para el 5º digito de la unidad (0, 5, 9, etc)
        $tipoliquidacion = $info['LiquidationsType']['name'];
        $esinicial = isset($info[0]['inicial']) ? $info[0]['inicial'] : 0;
        $info = $info['Liquidation'];
        $periodo = $info['periodo'];

        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        //data = array('liquidation_id' => $liquidation_id, 'totales' => $totales, 'cobranzas' => $cobranzas, 'saldosanteriores' => $saldosanteriores, 
        //   'coeficientes' => $coeficientes, 'prop' => $prop, 'remanentes' => $remanentes, 'descripcioncoeficientes' => $descripcioncoeficientes)
        foreach ($data['prop'] as $p) {
            if (isset($propinfo[$p['id']]) && $propinfo[$p['id']]) {
                // paso los datos del propietario, los totales de ese propietario, los datos del cliente y del consorcio
                anverso($p, $client, $consorcio, $periodo, $info, $datoscliente, $tipoliquidacion);
                reverso($p, $data, $client, $consorcio, $info, $prefijo, $esinicial, $this->webroot, $muestraIconoDetalleCobranza);

                if ($client['usa_plapsa'] && $consorcio['imprime_cpe']) { // muestro la clave de pago electrónico (CPE) si el Cliente utiliza PLAPSA y el Consorcio tiene tildada la opcion "Imprime CPE"
                    $codpagelect = h($client['plataforma']::generaClavePagoElectronico($consorcio['code'], $p['code'], $prefijo, $client['codigo'], $client['datointerno']));
                    //if (strlen($codpagelect) == 14) {
                    echo "<center><br><span class='box-table-a' style='padding:5px;margin-top:10px;font-size:11px;font-weight:bold;color:#000;font-family:Verdana,Helvetica,sans-serif;white-space:nowrap'>";
                    echo "SU CLAVE DE PAGO ELECTR&Oacute;NICO ES: $codpagelect (buscar como: " . h($client['titulo']) . ")</span>";
                    //}
                }

                // agrego la nota si existe
                if (!empty($notas) && strlen(trim($notas['resumencuenta'])) > 0) {
                    echo $notas['resumencuenta'];
                }

                @separacion(55);
                ?>
                <div style='page-break-after:always'></div>
                <?php
            }
        }
        ?>
    </body>
</html>
<?php

function anverso($propietario, $client, $consorcio, $periodo, $info, $datoscliente, $tipoliquidacion) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;border-bottom:0'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a" cellspacing=0 align="center">
        <tr>
            <?= $datoscliente ?>
            <td rowspan=2 align="left" valign="middle">
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
        <tr>
            <td align="left" rowspan="3" colspan="2" valign="top" cellspacing="0">
                <br><br><br><?php /* para q en el sobre ventana entre solo el propiet, y no se vea el saldo */ ?>
                <table border="0" valign="top" cellspacing="0" width="500" <?= $formato ?>>
                    <tr>
                        <td align="left" style="border:0px;padding-left:120px;">
                            <span style="width:60px;float:left;margin:20px 0px 0px -100px">PROPIETARIO
                            </span>
                            <font style="font-size:14px;font-weight:700;"><?= h($propietario['name']) ?></font><BR>
                            <?= h($propietario['postal_address']) ?><br>
                            <?= h($propietario['postal_city']) ?><br>
                            <br><span class="barcode1"><?= convertir_barcode(trim(str_pad($client['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio['code'], 4, "0", STR_PAD_LEFT) . str_pad($propietario['code'], 4, "0", STR_PAD_LEFT))) ?></span>
                            <br><?= wordwrap(h($consorcio['name'] . " - (" . $propietario['code'] . ") " . $propietario['unidad']), 31, "<br />"); ?>
                            <span style="width:140px;float:right;margin-top:-20px;">
                                <?= h($client['name']) ?>
                                <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                                <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                            </span>        
                        </td>
                    </tr>
                </table>
                <br><br><br><?php /* para q en el sobre ventana entre solo el propiet, y no se vea el saldo */ ?>
            </td>
        </tr>
        <tr>
            <td rowspan=2 align="left" valign="middle" style="border-top:2px solid #000;text-align:center"><b>Per&iacute;odo:</b>
                <?= h($periodo) . " <br><br><b>Tipo:</b> " . h($tipoliquidacion) ?>
                <br><br>
                <?= "<b>" . __("Validez") . ":</b> " . date("d/m/Y", strtotime($info['vencimiento'])) ?>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($p, $data, $client, $consorcio, $info, $prefijo, $esinicial, $webroot, $muestraIconoDetalleCobranza) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;border-bottom:1px solid #000'";
    $total = 0;
    if (isset($data['totales'][$p['id']]["tot"])) {
        $total += $data['totales'][$p['id']]["tot"];
    }
    $sant = intval($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para hacer las cuentas uso el saldo sin decimales
    $saldant = ($data['saldosanteriores'][$p['id']]['capital'] + $data['saldosanteriores'][$p['id']]['interes']); // para mostrar con decimales
    $redondeo = round($saldant - intval($saldant), 2);
    $capint = $data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes'];
    $sf = $capint > 0 ? intval($capint) : $capint; // si tiene saldo a favor, muestro los decimales
    $sf = $sf < 0 ? $sf - ($esinicial ? 0.00 : $redondeo) : $sf;
    $rf = $capint - intval(round($capint, 2)); // redondeo final
    $sa = $esinicial || $data['saldosanteriores'][$p['id']]['capital'] < 0 ? $saldant : $sant;
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <td class="pri"><b>Saldo anterior</b></td>
            <td colspan="2">(Capital: <?= money($data['saldosanteriores'][$p['id']]['capital']) ?> | Inter&eacute;s: <?= money($data['saldosanteriores'][$p['id']]['interes']) ?>)</td>
            <td colspan=3 class="right"><?php echo money($esinicial || $data['saldosanteriores'][$p['id']]['capital'] < 0 ? $saldant : $sant) ?></td>
        </tr>
        <tr>
            <td class="pri"><b>Cobranzas</b></td>
            <td colspan="2"></td>
            <td colspan="3"></td>
        </tr>
        <?php
        // sumo las cobranzas
        $totalcobranzas = 0;
        foreach ($data['cobranzas'] as $v) {
            if ($v['Cobranza']['propietario_id'] == $p['id']) {
                ?>
                <tr>
                    <td class="pri"></td>
                    <td colspan=2>Su pago: <?= date("d/m/Y", strtotime($v['Cobranza']['fecha'])) . ($v['Cobranzatipoliquidacione']['solocapital'] ? ' (solo capital)' : '') . (isset($v['Cobranza']['numero']) ? " - Recibo Oficial N&ordm; " . $v['Cobranza']['numero'] : '') ?></td>
                    <td colspan=3 class="right"><?= money(-$v['Cobranzatipoliquidacione']['amount']) ?></td>
                </tr>
                <?php
                $totalcobranzas += round($v['Cobranzatipoliquidacione']['amount'], 2);
            }
        }
        ?>
        <tr>
            <td class="pri"><b>Ajustes</b></td>
            <td colspan="2"></td>
            <td colspan="3"></td>
        </tr>
        <?php
        $sumaajustes = 0.00;
        foreach ($data['ajustes'] as $v) {
            if ($v['Ajuste']['propietario_id'] == $p['id']) {
                $sumaajustes += $v['Ajustetipoliquidacione']['amount'];
                ?>
                <tr>
                    <td class="pri"></td>
                    <td colspan=2>Ajuste: <?= date("d/m/Y", strtotime($v['Ajuste']['fecha'])) . ($v['Ajustetipoliquidacione']['solocapital'] ? ' (solo capital)' : '') ?></td>
                    <td colspan=3 class="right"><?= money(-$v['Ajustetipoliquidacione']['amount']) ?></td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td class="pri"><b>Saldo remanente</b></td>
            <td colspan="2">(Capital: <?= @money($data['remanentes'][$p['id']]['capital']) ?> | Inter&eacute;s: <?= @money($data['remanentes'][$p['id']]['interes']) ?>)</td>
            <td colspan=3 class="right">
                <?php
                // saldo remanente
                $sr = @round($data['remanentes'][$p['id']]['capital'] < 0 ? $data['remanentes'][$p['id']]['capital'] : $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] - ($esinicial ? 0 : $redondeo), 2);

                // si tiene saldo a favor y las cobranzas son mayores (le queda saldo a favor), entonces tiene q restar el redondeo también
                $saldrem = @($data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes']);
                $sr = $data['saldosanteriores'][$p['id']]['capital'] > 0 && $saldrem - $totalcobranzas < 0 && $sr < 0 ? ($saldrem == 0 ? round($saldrem - ($esinicial ? 0.00 : $redondeo), 2) : $saldrem ) : round($sr, 2);
                if ($sr < 0) {// si el $sr es negativo (sado a favor), tengo q sacarle el redondeo. No poner $sf < 0 || porq sino los q tienen saldo final negativo no le resta RANT (esta en cero)
                    $sr = $esinicial ? $sr : $sr - $redondeo;
                }
                echo money($sa - $totalcobranzas - $sumaajustes);
                ?>
            </td>
        </tr>
        <tr>
            <td class="pri"><b>Redondeo Anterior</b></td>
            <td colspan="2"></td>
            <td colspan=3 class="right">
                <?php
                echo money($sf < 0 || $esinicial ? 0 : $redondeo);
                ?>
            </td>
        </tr>
        <tr>
            <td class="pri"><b>Gastos generales</b></td>
            <td colspan="2"></td>
            <td colspan="3"></td>
        </tr>
        <?php
        if (isset($data['totales'][$p['id']]['coefgen'])) { // tiene gastos generales prorrateados
            foreach ($data['totales'][$p['id']]['coefgen'] as $k => $v) {
                $total += $v['tot'];
                if (isset($data['descripcioncoeficientes'][$k]) && isset($v['val'])) {
                    ?>
                    <tr>
                        <td class="pri"></td>
                        <td colspan=2><?= h($data['descripcioncoeficientes'][$k]) ?> <?= $v['val'] > 0 ? "(" . money($v['tot'] * 100 / $v['val']) . ")" : '' ?> <?= $v['val'] ?>% </td>
                        <td colspan=3 class="right"><?= money($v['tot']) ?></td>
                    </tr>
                    <?php
                }
            }
        }
        if (isset($data['totales'][$p['id']]['detalle'])) { // tiene gastos particulares propios del propietario
            foreach ($data['totales'][$p['id']]['detalle'] as $v) {
                ?>
                <tr>
                    <td class="pri"></td>
                    <td colspan=2><?= h($v['descripcion']) ?></td>
                    <td colspan=3 class="right"><?= money($v['total']) ?></td>
                </tr>
                <?php
            }
        }
        if (isset($data['totales'][$p['id']]['coefpar'])) { // tiene gastos particulares prorrateados
            foreach ($data['totales'][$p['id']]['coefpar'] as $v) {
                if (!isset($v['tot'])) {
                    continue;
                }
                $total += $v['tot'];
                foreach ($v['detalle'] as $w) {
                    if (isset($v['val']) && $v['val'] > 0) {// no muestro los q esten en cero
                        ?>
                        <tr>
                            <td class="pri"></td>
                            <td colspan=2><?= h($w['descripcion']) ?> (<?= money($w['total']) ?>) <?= $v['val'] ?>%</td>
                            <td colspan=3 class="right"><?= money($w['monto']) ?></td>
                        </tr>
                        <?php
                    }
                }
            }
        }
        ?>
        <tr>
            <td class="pri"><b>Inter&eacute;s</b></td>
            <td colspan="2"></td>
            <td colspan=3 class="right">
                <?php
                $intactual = ($p['exceptua_interes'] || $data['remanentes'][$p['id']]['capital'] < 0 ? 0 : $data['saldo'][$p['id']]['interes']);
                $intremanente = $p['exceptua_interes'] ? 0 : round(abs($intactual - $data['remanentes'][$p['id']]['interes']), 2);
                echo money($intremanente);
                ?>
            </td>
        </tr>
        <tr>
            <td class="pri"><b>Redondeo Actual</b></td>
            <td colspan="2"></td>
            <td colspan=3 class="right">
                <?php
                echo money($sf < 0 ? 0 : -$rf);
                ?>
            </td>
        </tr>
        <tr class="totales" style='border-bottom:2px solid #000'>
            <td style="text-align:left;border-left:0;border-top:1px solid #000"><b>Total liquidaci&oacute;n</b></td>
            <td colspan="2"><?= ($sf > 0 && !isset($data['consorcio']['imprimeimportebanco'])) || ($sf > 0 && (isset($data['consorcio']['imprimeimportebanco']) && $data['consorcio']['imprimeimportebanco'])) ? "IMPORTE DEPOSITO BANCARIO: " . money($sf + (($p['code'] / 100) - intval(($p['code'] / 100)))) : '' ?></td>
            <td colspan=3 class="right">
                <?php
                echo money($sf);
                ?>
            </td>
        </tr>
    </table>
    <?php
    if (intval($sf) > 0) {
        if ($consorcio['imprime_cod_barras']) {
            if ($consorcio['2_cuotas']) {
                talondepago($client, $consorcio['code'], $prefijo, $p['code'], (intval($sf) / 2), $info['vencimiento'], $info['vencimiento']);
                talondepago($client, $consorcio['code'], $prefijo, $p['code'], (intval($sf) / 2), $info['limite'], $info['limite']);
            } else {
                talondepago($client, $consorcio['code'], $prefijo, $p['code'], intval($sf), $info['vencimiento'], $info['limite']);
            }
        }
        if (isset($consorcio['imprime_talon_banco']) && $consorcio['imprime_talon_banco']) {
            talonbanco($client, $consorcio, $info, $sf, $p);
        }
    } else {
        ?>
        <div style="clear:both;height:120px;"></div>
        <?php
    }
}

function talondepago($client, $consorcio_code, $prefijo, $propietario_code, $totalexpensa, $vencimiento, $limite) {
    ?>
    <table border=0 style='font-size:11px;font-family:Verdana,Helvetica,sans-serif;' align="center">
        <tr>
            <?php
            $c = $totalexpensa * ($client['comision'] / 100);
            if ($client['usa_plapsa']) {
                // 12/02/2020 Si la comision esta configurada cero, no pongo el minimo (asi los q no quieren q salga con comision, sale bien. Y de paso, cuando se reporta a PLAPSA sale sin comision
                if ($client['comision'] > 0 && $client['minimo'] > 0 && $c < $client['minimo']) {// PLAPSA (11/04/2017, modificado desde Gili Meno, Indonesia jaja) es el minimo de comision
                    $c = $client['minimo'];
                }
            } else {
                $c = $totalexpensa * (3.6 / 100); // latuf dijo 3.6 el 07/08/2019
            }

            // se muestran solo los cod de barras con total > 0 y menores a 100000/1.031~96993.21 (comision 3.1). PLAPSA NO SOPORTA IMPORTES > a 100mil
            if (($totalexpensa + $c) < 100000 / (1 + ($client['comision'] / 100))) {
                if ($client['usa_plapsa']) {
                    $codbarras = $client['plataforma']::generaCodigoBarras("2634", $client['codigo'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c, $client['datointerno']);
                } else {
                    $codbarras = Plapsa::generaCodigoBarras("305", $client['code'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c);
                }
                ?>
                <td align=center style="white-space:nowrap">
                    <span class="barcode1"><?= convertir_barcode($codbarras) ?></span>
                    <br>
                    <?php
                    // verifico la longitud del codigo de barras, tiene q ser 42/44/56!!!!!!
                    if (strlen($codbarras) == 42 || strlen($codbarras) == 44 || strlen($codbarras) == 56) {
                        echo $codbarras;
                    } else {
                        die("EL CODIGO DE BARRAS ES INCORRECTO, NO POSEE 42/44/56 CARACTERES!!!!");
                    }
                    ?><br><?= "<b>Fecha l&iacute;mite de pago: </b>" . date("d/m/Y", strtotime($limite)) ?> - <b>Validez: </b><?= date("d/m/Y", strtotime($vencimiento)); ?>
                </td>
                <td align=left width=auto>
                    <table style="border:0px; font-size: 11px; font-family: Verdana, Helvetica, sans-serif;margin:0px;" cellspacing="0" width="160">
                        <tr>							
                            <td align=left style="border:0px">
                                <b>TOTAL</b>
                            </td>
                            <td align=right style="border:0px" >
                                <?= money($totalexpensa) ?>
                            </td>
                        </tr>
                        <?php
                        if ($c > 0) {
                            ?>
                            <tr>
                                <td style="border:0px" align="left">
                                    <b>COMISION</b>
                                </td>
                                <td style="border:0px" align="right">
                                    <?php
                                    echo money($c); //comision PLAPSA 
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td style="border:0px" align="right">
                                <hr align="right" style="visibility:hidden">
                                <b>TOTAL</b>
                            </td>
                            <td style="border:0px" align="right">
                                <hr align="right">
                                <?= money($totalexpensa + $c); //comision PLAPSA                     ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php
            }
            ?>
        </tr>
    </table>

    <?php
}

function talonbanco($client, $consorcio, $info, $sf, $p) {
    $comision = $sf * ($consorcio['talonbancocomision'] / 100);
    if ($comision < $consorcio['talonbancominimo']) {
        $comision = $consorcio['talonbancominimo'];
    }
    ?>
    <table width="765" border="0" cellpadding="0" cellspacing="0" class="resumen" align="center" style='font-family:"Courier New", Courier, mono'>
        <tr>
            <td width="211" style="height:42px;border-bottom:1px dashed black; border-bottom:1px dashed black;">
                <strong style="font-size:14px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo h($consorcio['talonbanconombre']); ?>
            </td>
            <td width="554" style="height:42px;border-bottom:1px dashed black;">
                <strong>Tal&oacute;n Propietario</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
                                <tr>
                                    <td><?= h($client['name']) ?> </td>
                                    <td width="22%"><?php echo $consorcio['name'] ?></td>
                                    <td width="46%" align="center"><table width="60%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td colspan="2"><b><?= h("COD. SERV: " . str_pad($consorcio['talonbancoprefijo'], 4, "0", STR_PAD_LEFT)) ?></b></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="32%"><?php echo h($p['name']); ?></td>
                                    <td><?php echo h($p['unidad'] . " (" . $p['code'] . ")"); ?></td>
                                    <td align="left">Unidad: <?php echo h($p['unidad'] . " (" . $p['code'] . ")"); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="media"><?php echo h($info['periodo']) ?></span></td>
                                    <td align="right"><?php echo money($sf); ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Comisi&oacute;n</td>
                                    <td align="right"><?php echo money($comision); ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td align="right"><strong><?php echo money($sf + $comision); ?></strong></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Validez: <?php echo date("d/m/Y", strtotime($info['vencimiento'])); ?></td>
                                    <td align="left">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="right" colspan=2>
                                        <?php
                                        $codbarras = Plapsa::generaCodigoBarras(305, $consorcio['talonbancocodigo'], $consorcio['code'], '', $p['code'], $info['limite'], $info['limite'], $sf + $comision, $sf + $comision);
                                        ?>
                                        <span class="barcode1"><?= convertir_barcode($codbarras) ?></span>
                                        <br>
                                        <?php
                                        // verifico la longitud del codigo de barras
                                        if (strlen($codbarras) == 42 || strlen($codbarras) == 44 || strlen($codbarras) == 56) {
                                            echo "<span style='margin-left:5px;'>$codbarras</span>";
                                        } else {
                                            die("EL CODIGO DE BARRAS ES INCORRECTO, NO POSEE 42/44/56 CARACTERES!!!!");
                                        }
                                        ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table></td>
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
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}

function convertir_barcode($cadena) {
    if (empty($cadena)) {
        return "";
    }
    $resultado = "<img src='/sistema/img/bar/init.GIF' />";
    while (strlen($cadena) > 1) {
        $num = str_pad(substr($cadena, 0, 2), 2, "0", STR_PAD_LEFT);
        if (!is_numeric($num)) {
            die("error en el codigo de barras");
        }

        if (in_array($num, ['94', '95', '96', '97', '98', '99'])) {
            $resultado .= "<img src='/sistema/img/bar/$num.GIF' />";
        } else {
            $resultado .= h(chr($num + 33)); // uso h() porq si genera el caracter <, el navegador manda fruta
        }
        $cadena = substr($cadena, 2);
    }
    return $resultado . "<img src='/sistema/img/bar/fin.GIF' />";
}
