<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
$client = isset($data['client']['Client']) ? $data['client']['Client'] : $cliente['Client']; // si existe en el cierre la info del cliente (al prorratear), sino la actual
$consorcio = isset($data['consorcio']) ? $data['consorcio'] : $consorcio['Consorcio'];
$notas = $info['Nota'];
$prefijo = $info['LiquidationsType']['prefijo']; // para el 5º digito de la unidad (0, 5, 9, etc)
$esinicial = isset($info[0]['inicial']) ? $info[0]['inicial'] : 0;
$info = $info['Liquidation'];
$periodo = $info['periodo'];
$files = [];
//$datoscliente = $this->element('datoscliente', ['dato' => $client]);
//data = array('liquidation_id' => $liquidation_id, 'totales' => $totales, 'cobranzas' => $cobranzas, 'saldosanteriores' => $saldosanteriores, 
//   'coeficientes' => $coeficientes, 'prop' => $prop, 'remanentes' => $remanentes, 'descripcioncoeficientes' => $descripcioncoeficientes)
//require_once(dirname(__FILE__) . '/../../webroot/js/html2pdf/html2pdf.class.php');
//require_once dirname(__FILE__) . '/../../webroot/js/html/vendor/autoload.php';
// borro los archivos anteriores de la misma administracion
if ($_SERVER['SERVER_PORT'] == '80') {
    $ruta = "d:\\dropbox\\dev\\sistema\\webroot\\files\\pdf\\";
} else {
    $ruta = "C:\\xampp\\htdocs\\sistema\\webroot\\files\\pdf\\";
}
$arch = glob("$ruta" . str_pad($client['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio['code'], 4, "0", STR_PAD_LEFT) . "0*"); // obtengo todos los archivos del cliente y consorcio
foreach ($arch as $ar) { // iterate files
    if (is_file($ar)) {
        unlink($ar); // delete file
    }
}
foreach ($data['prop'] as $p) {
    //if (isset($propinfo[$p['id']]) && $propinfo[$p['id']]) {
        ob_start();
        // paso los datos del propietario, los totales de ese propietario, los datos del cliente y del consorcio
        cabecera($consorcio['name'], $periodo);
        anverso($p, $client, $consorcio, $periodo, $info, $client);
        reverso($p, $data, $client, $consorcio, $info, $prefijo, $esinicial);

        if ($client['usa_plapsa'] && $consorcio['imprime_cpe']) { // muestro la clave de pago electrónico (CPE) si el Cliente utiliza PLAPSA y el Consorcio tiene tildada la opcion "Imprime CPE"
            $codpagelect = generaClavePagoElectronico($client['code'], $consorcio['code'], $p['code'], $prefijo);
            if (strlen($codpagelect) == 14) {
                echo "<center><br><span class='box-table-a' style='padding:5px;margin-top:10px;font-size:11px;font-weight:bold;color:#666699;font-family:Helvetica,sans-serif;'>";
                echo "SU CLAVE DE PAGO ELECTR&Oacute;NICO ES: $codpagelect (buscar como: Plataforma de Pagos)</span>";
            }
        }

        // agrego la nota si existe
        if (strlen(trim($notas['resumencuenta'])) > 0) {
            echo $notas['resumencuenta'];
        }

        @separacion(55);
        ?><div style='page-break-after:always'></div>
        <?php
        echo "</body></html>";
        $output = ob_get_contents();

        // guardo el PDF!
//        $html2pdf = new Html2Pdf();
//$html2pdf->writeHTML($output);
//$html2pdf->output();

        /* $html2pdf = new HTML2PDF('P', 'A4', 'es');
          $html2pdf->pdf->SetDisplayMode('fullpage');
          @$html2pdf->WriteHTML($output); */
        $codprop = str_pad($client['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio['code'], 4, "0", STR_PAD_LEFT) . str_pad($p['code'], 4, "0", STR_PAD_LEFT);
        $archivo = $ruta . slug($codprop) . cleanString($periodo) . ".html";
        $files[] = basename($archivo);
        //$html2pdf->Output("$archivo", 'F');
        $fh = fopen($archivo, "w");
        fwrite($fh, $output);
        fclose($fh);
    //}
}
// borro todo y muestro solo esto

ob_start();
ob_flush();
ob_end_clean();

echo "Se generaron los siguientes archivos:<br>";
foreach ($files as $k => $v) {
    echo "<a href='/sistema/files/pdf/$v' target='_blank' rel='nofollow noopener noreferrer'>$v</a><br>";
}

function cabecera($consorcio, $periodo) {
    ?>
    <!DOCTYPE html>
    <html lang="es-419">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Resumen de cuentas - <?= h1($consorcio . " - " . $periodo) ?></title>
            <style type="text/css">
                .box-table-a,.box-table-b{
                    font-family: "helvetica";
                    font-size: 11px;
                    text-align: center;
                    border-collapse: collapse;
                    border: 2px solid #9baff1;
                    line-height:9px;
                }
                .box-table-a th,.box-table-b th{
                    font-size: 11px;
                    font-weight: normal;
                    padding: 8px;
                    background: none;
                    border-right: 2px solid #9baff1;
                    border-left: 2px solid #9baff1;
                    color: #039;
                }
                .box-table-a td{
                    padding: 4px;
                    background: none; 
                    border-left: 2px solid #aabcfe;
                    color: #000;
                }
                .box-table-b td{
                    padding: 4px;
                    background: none; 
                    border-left: 2px solid #aabcfe;
                    border-bottom: 1px solid #aabcfe;
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
                    width:120px;
                    border:none !important;
                }
                .totales{
                    border: 2px solid #9baff1;
                    font-weight: 700;
                    font-size: 13px;
                }
            </style>
        </head>
        <body>
            <?php
        }

        function anverso($propietario, $client, $consorcio, $periodo, $info, $dato) {
            $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;'";
            ?>
            <table width="750" <?= $formato ?> class="box-table-a" align="center">
                <tr>
                    <td width="120" rowspan="3" align="center">
                        <img alt="logo" width=100 height=100 src="/sistema/<?= file_exists("files/" . $dato['id'] . "/" . $dato['id'] . ".jpg") ? "files/" . $dato['id'] . "/" . $dato['id'] . ".jpg" : "img/0000.png" ?>">
                    </td>
                    <td rowspan="3" align="left" valign="middle">
                        <?= __("ADMINISTRACION") ?><br/><br/>
                        <p style="font-size:16px;font-weight:bold;margin:0;line-height:16px"><?= h1($dato['name']) ?></p>
                        <br/><?= __("Direcci&oacute;n") ?>: <?= h1($dato['address']) ?>
                        <br/><?= __("Ciudad") ?>: <?= h1($dato['city']) ?>
                        <br/><?= __("CUIT") ?>: <?= !empty($dato['cuit']) && $dato['cuit'] !== "00-00000000-0" ? h1($dato['cuit']) : '--' ?>
                        <br/><?= __("Mat.") ?>: <?= !empty($dato['numeroregistro']) ? h1($dato['numeroregistro']) : '--' ?>
                        <br/><?= __("Tel.") ?>: <?= !empty($dato['telephone']) ? h1($dato['telephone']) : '--' ?>
                        <br/><?= __("Email") ?>: <?= !empty($dato['email']) ? h1($dato['email']) : '--' ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="middle" style="border-top:2px solid #aabcfe">

                    </td>
                </tr>
                <tr>
                    <td rowspan=2 align="left" valign="middle">
                        <b>Consorcio: </b><?= h1($consorcio['name']) ?>
                        <br><br>
                        <b>CUIT: </b><?= h1(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                        <br><br>
                        <b>Domicilio: </b><?= h1($consorcio['address']); ?>
                        <br><br>
                        <b>Localidad:</b>
                        <?= h1($consorcio['city']) ?>
                        <br><br>
                        <b>Unidad:</b>
                        <?= h1($propietario['unidad'] . " - Codigo: " . $propietario['code']) ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" rowspan="3" colspan="2" valign="top" cellspacing="0">
                        <table border="0" valign="top" cellspacing="0" width="500" <?= $formato ?>>
                            <tr>
                                <td align="left" style="border:0px;padding-left:120px;">
                                    <span style="width:60px;float:left;margin:20px 0px 0px -100px">PROPIETARIO
                                    </span>
                                    <font style="font-size:14px;font-weight:700;"><?= h1($propietario['name']) ?></font><BR>
                                    <?= h1($propietario['postal_address']) ?><br>
                                    <?= h1($propietario['postal_city']) ?><br>
                                    <br><?= convertir_barcode(trim(str_pad($client['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio['code'], 4, "0", STR_PAD_LEFT) . str_pad($propietario['code'], 4, "0", STR_PAD_LEFT))) ?>
                                    <br><?= wordwrap(($consorcio['name'] . " - (" . $propietario['code'] . ") " . $propietario['unidad']), 31, "<br />"); ?>
                                    <span style="width:140px;float:right;margin-top:-20px;">
                                        <?= h1($client['name']) ?>
                                        <br><?= h1($client['address']) ?>
                                        <br><?= h1($client['city']) ?>
                                        <br/>CUIT: <?= h1(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                                        <br/>Mat.: <?= h1(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                                    </span>        
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td rowspan=2 align="left" valign="middle" style="border-top:2px solid #9baff1;text-align:center"><b>Per&iacute;odo:</b>
                        <?= h1($periodo) ?>
                        <br><br>
                        <?= "<b>" . __("Validez") . ":</b> " . date("d/m/Y", strtotime($info['vencimiento'])) ?>
                    </td>
                </tr>
            </table>
            <?php
        }

        function reverso($p, $data, $client, $consorcio, $info, $prefijo, $esinicial) {
            $formato = "style='font-size: 10px; font-family:  Helvetica, sans-serif;border-top:0px'";
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
                            <td colspan=2>Su pago: <?= date("d/m/Y", strtotime($v['Cobranza']['fecha'])) . ($v['Cobranzatipoliquidacione']['solocapital'] ? ' (solo capital)' : '') . (isset($v['Cobranza']['numero']) ? " - Recibo #" . $v['Cobranza']['numero'] : '') ?></td>
                            <td colspan=3 class="right"><?= money($v['Cobranzatipoliquidacione']['amount']) ?></td>
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
                foreach ($data['ajustes'] as $v) {
                    if ($v['Ajuste']['propietario_id'] == $p['id']) {
                        ?>
                        <tr>
                            <td class="pri"></td>
                            <td colspan=2>Ajuste: <?= date("d/m/Y", strtotime($v['Ajuste']['fecha'])) /* . " " . $v['Ajuste']['concepto'] */ ?></td>
                            <td colspan=3 class="right"><?= money($v['Ajustetipoliquidacione']['amount']) ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td class="pri"><b>Saldo remanente</b></td>
                    <td colspan="2">(Capital: <?= money($data['remanentes'][$p['id']]['capital']) ?> | Inter&eacute;s: <?= money($data['remanentes'][$p['id']]['interes']) ?>)</td>
                    <td colspan=3 class="right">
                        <?php
                        // saldo remanente
                        $sr = round($data['remanentes'][$p['id']]['capital'] < 0 ? $data['remanentes'][$p['id']]['capital'] : $data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes'] - ($esinicial ? 0 : $redondeo), 2);

                        // si tiene saldo a favor y las cobranzas son mayores (le queda saldo a favor), entonces tiene q restar el redondeo también
                        $saldrem = ($data['remanentes'][$p['id']]['capital'] + $data['remanentes'][$p['id']]['interes']);
                        $sr = $data['saldosanteriores'][$p['id']]['capital'] > 0 && $saldrem - $totalcobranzas < 0 && $sr < 0 ? ($saldrem == 0 ? round($saldrem - ($esinicial ? 0.00 : $redondeo), 2) : $saldrem ) : round($sr, 2);
                        if ($sr < 0) {// si el $sr es negativo (sado a favor), tengo q sacarle el redondeo. No poner $sf < 0 || porq sino los q tienen saldo final negativo no le resta RANT (esta en cero)
                            $sr = $esinicial ? $sr : $sr - $redondeo;
                        }
                        echo money($sr);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="pri"><b>Gastos generales</b></td>
                    <td colspan="2"></td>
                    <td colspan="3"></td>
                </tr>
                <?php
                if (isset($data['totales'][$p['id']]['coefgen'])) { // tiene gastos particulares prorrateados
                    foreach ($data['totales'][$p['id']]['coefgen'] as $k => $v) {
                        $total += $v['tot'];
                        if (isset($data['descripcioncoeficientes'][$k]) && isset($v['val'])) {
                            ?>
                            <tr>
                                <td class="pri"></td>
                                <td colspan=2><?= h1($data['descripcioncoeficientes'][$k]) ?> <?= $v['val'] > 0 ? "(" . money($v['tot'] * 100 / $v['val']) . ")" : '' ?> <?= $v['val'] ?>% </td>
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
                            <td colspan=2><?= h1($v['descripcion']) ?></td>
                            <td colspan=3 class="right"><?= money($v['total']) ?></td>
                        </tr>
                        <?php
                    }
                }
                if (isset($data['totales'][$p['id']]['coefpar'])) { // tiene gastos particulares prorrateados
                    foreach ($data['totales'][$p['id']]['coefpar'] as $v) {
                        $total += $v['tot'];
                        foreach ($v['detalle'] as $w) {
                            ?>
                            <tr>
                                <td class="pri"></td>
                                <td colspan=2><?= h1($w['descripcion']) ?> (<?= money($w['total']) ?>) <?= $v['val'] ?>%</td>
                                <td colspan=3 class="right"><?= money($w['monto']) ?></td>
                            </tr>
                            <?php
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
                    <td class="pri"><b>Redondeo Anterior</b></td>
                    <td colspan="2"></td>
                    <td colspan=3 class="right">
                        <?php
                        echo money($sf < 0 || $esinicial ? 0 : $redondeo);
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
                <tr class="totales">
                    <td style="text-align:left;"><b>Total liquidaci&oacute;n</b></td>
                    <td colspan="2"><?= $sf > 0 ? "IMPORTE DEPOSITO BANCARIO: " . money($sf + (($p['code'] / 100) - intval(($p['code'] / 100)))) : '' ?></td>
                    <td colspan=3 class="right">
                        <?php
                        echo money($sf);
                        ?>
                    </td>
                </tr>
            </table>
            <br/>

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
            <table border=0 style='font-size:11px;font-family:Helvetica,sans-serif;' align="center">
                <tr>
                    <?php
                    $c = $totalexpensa * ($client['comision'] / 100);
                    if ($client['usa_plapsa']) {
                        if ($c < 18.15) {// PLAPSA (11/04/2017, modificado desde Gili Meno, Indonesia jaja) es el minimo de comision
                            $c = 18.15;
                        }
                    } else {
                        if ($c < 3.03) {// RIPSA
                            $c = 3.03;
                        }
                    }
                    if ($client['code'] === '1154') {//1154 san miguel
                        // chanchada espantosa para el Cliente SAN MIGUEL debido a q el Administrador no quiere andar explicando a SUS propietarios que se cobra una comisión por pagar a traves de PLAPSA.
                        // Antes de utilizar nuestro sistema YA USABAN PLAPSA y no se detallaba en el resumen de cuenta el importe de comision (y se la cobraban). Ahora que es
                        // más transparente (se muestra el importe de comision q se cobra), la gente se queja y el administrador prefiere ocultarlo!! Cero transparente. Despues tiene q andar haciendo malabares
                        // porq PLAPSA le deposita menos y ese importe faltante se lo tiene q cobrar en la proxima liquidacion (como GP o anda a saber q van a hacer). Pero bueno...
                        $c = 0;
                    }
                    if (($totalexpensa + $c) < 100000) {
                        if ($client['usa_plapsa']) {
                            $codbarras = generaCodigoBarras("138", $client['code'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c);
                        } else {
                            $codbarras = generaCodigoBarras("305", $client['code'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c);
                        }
                        ?>
                        <td align=center>
                            <font size=6><?= convertir_barcode($codbarras) ?></font>
                            <BR>
                            <?php
                            // verifico la longitud del codigo de barras, tiene q ser 42 !!!!!!
                            if (strlen($codbarras) == 42 || strlen($codbarras) == 46) {
                                echo $codbarras;
                            } else {
                                die("EL CODIGO DE BARRAS ES INCORRECTO, NO POSEE 42/56 CARACTERES!!!!");
                            }
                            ?><BR><?= "<b>Fecha l&iacute;mite de pago: </b>" . date("d/m/Y", strtotime($limite)) ?></td>
                        <td align=center width="auto">
                            <b>Validez</b> <br><?= date("d/m/Y", strtotime($vencimiento)); ?>
                        </td>
                        <td align=left width=auto>
                            <table style="border:0px; font-size: 11px; font-family:  Helvetica, sans-serif;margin:0px;" cellspacing="0" width="180">
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
                                        <?= money($totalexpensa + $c); //comision PLAPSA              ?>
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
                    <td width="211" style="height:42px; border-top:1px dashed black; border-bottom:1px dashed black;">
                        <strong style="font-size:14px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo h1($consorcio['talonbanconombre']); ?>
                    </td>
                    <td width="554" style="height:42px; border-top:1px dashed black; border-bottom:1px dashed black;">
                        <strong>Tal&oacute;n Propietario</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="1">
                                        <tr>
                                            <td><?= h1($client['name']) ?> </td>
                                            <td width="22%"><?php echo $consorcio['name'] ?></td>
                                            <td width="46%" align="center"><table width="60%"  border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td colspan="2"><b><?= h1("COD. SERV: " . str_pad($consorcio['talonbancoprefijo'], 4, "0", STR_PAD_LEFT)) ?></b></td>
                                                    </tr>
                                                </table></td>
                                        </tr>
                                        <tr>
                                            <td width="32%"><?php echo h1($p['name']); ?></td>
                                            <td><?php echo h1($p['unidad'] . " (" . $p['code'] . ")"); ?></td>
                                            <td align="left">Unidad: <?php echo h1($p['unidad'] . " (" . $p['code'] . ")"); ?></td>
                                        </tr>
                                        <tr>
                                            <td><span class="media"><?php echo h1($info['periodo']) ?></span></td>
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
                                                $codbarras = generaCodigoBarras($consorcio['talonbancoprefijo'], $consorcio['talonbancocodigo'], $consorcio['code'], '', $p['code'], $info['limite'], $info['limite'], $sf + $comision, $sf + $comision);
                                                echo convertir_barcode($codbarras) . "<BR>";
                                                // verifico la longitud del codigo de barras, tiene q ser 42/56 !!!!!!
                                                if (strlen($codbarras) == 42 || strlen($codbarras) == 46) {
                                                    echo "<span style='margin-left:5px;'>$codbarras</span>";
                                                } else {
                                                    die("EL CODIGO DE BARRAS ES INCORRECTO, NO POSEE 42/56 CARACTERES!!!!");
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

        function generaClavePagoElectronico($cod_pago_elect, $cod_unidad, $prefijo, $codigoClientePlataformaPago) {
            //codigo cliente (4 caracteres), codigo de consorcio (4 caracteres), codigo unidad (5 caracteres), d�gito verificador
            $clave = "";
            if ($codigoClientePlataformaPago != "0") {
                $clave = str_pad($codigoClientePlataformaPago, 4, "0", STR_PAD_LEFT) . str_pad($cod_pago_elect, 4, "0", STR_PAD_LEFT) . $prefijo . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT);
                $clave .= obtieneDigitoVerificador($clave, 13);
            }
            return $clave;
        }

        function obtieneDigitoVerificador($banc_barcode, $hasta) {
            $secuencia = '1357935793579357935793579357935793579357935793579';
            $checksum = 0;
            $cont = 0;
            while ($cont < $hasta) {
                $checksum += substr($secuencia, $cont, 1) * substr($banc_barcode, $cont, 1);
                $cont += 1;
            }
            $checksum = intval($checksum / 2);
            $checksum = $checksum % 10;
            return $checksum; // d�gito $hasta+1
        }

        function generaCodigoBarras($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2) {
            if ($prefijo == "138") { // es un codigo de barras de PLAPSA (46 caracteres)
                return generaCodigoBarrasV2($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2);
            }
            $banc_barcode = "";
            if ($prefijo != "" && $cod_cliente != "") {
                $banc_barcode = $prefijo . str_pad($cod_cliente, 4, "0", STR_PAD_LEFT);
            }
            $banc_barcode .= str_pad($cod_consor, 4, "0", STR_PAD_LEFT) . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT);
            //2012-10-15 00:00:00	
            $vto1 = strtotime($vto1);
            $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto1), date("d", $vto1), date("Y", $vto1)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto1)) + 1;
            $fecha_juliana = substr(date("Y", $vto1), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
            $banc_barcode .= str_pad(round($banco_total * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;
            $vto2 = strtotime($vto2);
            $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto2), date("d", $vto2), date("Y", $vto2)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto2)) + 1;
            $fecha_juliana = substr(date("Y", $vto2), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
            $banc_barcode .= str_pad(round($banco_total2 * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;

            $secuencia = '1357935793579357935793579357935793579357935793579';
            $checksum = $cont = 0;
            while ($cont < 41) {
                $checksum += substr($secuencia, $cont, 1) * substr($banc_barcode, $cont, 1);
                $cont += 1;
            }
            return $banc_barcode . (intval($checksum / 2)) % 10;
        }

        function generaCodigoBarrasV2($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2) {
            $banc_barcode = $prefijo . "0" . str_pad($cod_cliente, 4, "0", STR_PAD_LEFT) . str_pad($cod_consor, 4, "0", STR_PAD_LEFT) . $prefijoltype . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT); // 138 . "01100"...
            $banc_barcode .= obtieneDigitoVerificador($banc_barcode, 17);
            $vto1 = strtotime($vto1);
            $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto1), date("d", $vto1), date("Y", $vto1)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto1)) + 1;
            $fecha_juliana = substr(date("Y", $vto1), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
            $banc_barcode .= str_pad(round($banco_total * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;
            $vto2 = strtotime($vto2);
            $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto2), date("d", $vto2), date("Y", $vto2)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto2)) + 1;
            $fecha_juliana = substr(date("Y", $vto2), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
            $banc_barcode .= str_pad(round($banco_total2 * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;

            $banc_barcode .= obtieneDigitoVerificador($banc_barcode, 44);
            $banc_barcode .= obtieneDigitoVerificador($banc_barcode, 45);
            return $banc_barcode;
        }

        function convertir_barcode($cadena) {
            $resultado = "<img src=\"/sistema/img/bar/init.GIF\">";
            while (strlen($cadena) > 1) {
                $resultado .= "<img src=\"/sistema/img/bar/" . substr($cadena, 0, 2) . ".GIF\">";
                $cadena = substr($cadena, 2);
            }
            $resultado .= "<img src=\"/sistema/img/bar/fin.GIF\">";
            return $resultado;
        }

        function cleanString($text) {
            $utf8 = [
                '/[áàâãªä]/u' => 'a', '/[ÁÀÂÃÄ]/u' => 'A', '/[ÍÌÎÏ]/u' => 'I', '/[íìîï]/u' => 'i', '/[éèêë]/u' => 'e',
                '/[ÉÈÊË]/u' => 'E', '/[óòôõºö]/u' => 'o', '/[ÓÒÔÕÖ]/u' => 'O', '/[úùûü]/u' => 'u', '/[ÚÙÛÜ]/u' => 'U',
                '/ç/' => 'c', '/Ç/' => 'C', '/ñ/' => 'n', '/Ñ/' => 'N', '/–/' => '-', // UTF-8 hyphen to "normal" hyphen
                '/[’‘‹›‚]/u' => ' ', // Literally a single quote
                '/[“”«»„]/u' => ' ', // Double quote
                '/ /' => ' ', // nonbreaking space (equiv. to 0x160)
                '/\//' => ' ', // nonbreaking space (equiv. to 0x160)
            ];
            return preg_replace(array_keys($utf8), array_values($utf8), $text);
        }

        function h1($dato) {
            return ($dato);
        }

        function slug($z) {
            //$z = strtolower($z);
            //$z = preg_replace('/[^a-z0-9 -]+/', '', $z);
            $z = str_replace(' ', '-', $z);
            $z = str_replace('/', ' ', $z);
            return trim($z, '-');
        }
        