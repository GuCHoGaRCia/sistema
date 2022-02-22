<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$data = json_decode(isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data, true);
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
$esinicial = isset($info[0]['inicial']) ? $info[0]['inicial'] : 0;
$notas = $info['Nota'];
$prefijo = $info['LiquidationsType']['prefijo']; // para el 5º digito de la unidad (0, 5, 9, etc)
$info = $info['Liquidation'];
$periodo = $info['periodo'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Cuenta corriente - <?= h($client['name'] . " - " . $consorcio['name'] . " - " . $periodo) ?></title>
        <?= $this->Minify->script(['jq']); ?>
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
                border: 2px solid #9baff1;
                line-height:8px;
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
        </style>
    </head>
    <body>
        <!--img src="/sistema/img/print2.png" id="print" /-->
        <?php
        $eselprimero = false; // no muestro el FUCKING encabezado
        $i = 1;
        $ultimo = end($data['prop'])['id'];
        echo "<p style='margin:0;text-align:center;width:100%'>" . h($client['name'] . " - " . $consorcio['name'] . " - " . $periodo) . "</p>";
        foreach ($data['prop'] as $p) {
            if (isset($propinfo[$p['id']]) /* && $propinfo[$p['id']] */) {
                // paso los datos del propietario, los totales de ese propietario, los datos del cliente y del consorcio
                //anverso($p, $client, $consorcio, $periodo, $eselprimero);
                reverso($p, $data, $esinicial);
                if ($i == 4) {
                    //echo "<div style='page-break-after:always'></div>";
                    if ($p['id'] !== $ultimo) {
                        echo "<p style='margin:0;text-align:center;width:100%'>" . h($client['name'] . " - " . $consorcio['name'] . " - " . $periodo) . "</p>";
                    }
                    $i = 0;
                }
                $i++;
                $eselprimero = false;
            }
        }
        /*
          <script>
          $("#print").on("click", function () {
          $("#print").hide();
          window.print();
          $("#print").show();
          });
          </script>
         */
        ?>
    </body>
</html>
<?php

function anverso($propietario, $client, $consorcio, $periodo, $eselprimero) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;'";
    if ($eselprimero) {
        ?>
        <table width="750" <?= $formato ?> class="box-table-a" align="center">
            <thead>
                <tr>
                    <td width="130" height="60" rowspan="3" align="center">
                        <img alt="logo" width=100 height=100 src="/sistema/<?= file_exists("files/" . $client['id'] . "/" . $client['id'] . ".jpg") ? "files/" . $client['id'] . "/" . $client['id'] . ".jpg" : "img/0000.png" ?>">
                    </td>
                    <td width="360" rowspan="3" align="left" valign="middle">
                        ADMINISTRACION<br/><br/>
                        <font size=4><?= h($client['name']) ?></font><br/>
                        <br/><?= h($client['address']) ?>
                        <br/><?= h($client['city']) ?>
                        <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                        <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                        <br/>Tel.: <?= h(!empty($client['telephone']) ? $client['telephone'] : '--') ?>
                        <br/>Email: <?= h(!empty($client['email']) ? $client['email'] : '--') ?>
                    </td>
                    <td align="left" valign="middle"><b>Consorcio:</b>
                        <?= h($consorcio['name']) ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="middle">
                        <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="middle">
                        <b>Domicilio:</b>
                        <?= h($consorcio['address']) ?>
                    </td>
                </tr>
            </thead>
            <tr>
                <td align="left" rowspan="3" colspan="2" valign="top" cellspacing="0">
                </td>
                <td align=left valign=bottom><b>Localidad:</b>
                    <?= h($consorcio['city']) ?>
                </td>
            </tr>
            <tr>
                <td align="left" valign="middle" style="border-top:2px solid #9baff1;text-align:center"><b>Per&iacute;odo:</b>
                    <?= h($periodo) ?>
                </td>
            </tr>
        </table>
        <?php
    }
}

function reverso($p, $data, $esinicial) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;'";
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
            <td colspan="6">
                <table border="0" valign="top" cellspacing="0" width="700" <?= $formato ?>>
                    <tr>
                        <td align="left" style="border:0px;padding-left:120px;">
                            <span style="width:60px;float:left;margin:10px 0px 0px -100px">PROPIETARIO </span>
                            <font style="font-size:14px;font-weight:700;"><?= h($p['name'] . " (" . $p['unidad'] . ")") ?></font> - <?= h($p['postal_address']) ?> - <?= h($p['postal_city']) ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
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
                    <td colspan=2>Su pago: <?= date("d/m/Y", strtotime($v['Cobranza']['fecha'])) ?></td>
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
                    <td colspan=2>Ajuste: <?= date("d/m/Y", strtotime($v['Ajuste']['fecha'])) ?></td>
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
        if (isset($data['totales'][$p['id']]['coefgen'])) { // tiene gastos particulares prorrateados
            foreach ($data['totales'][$p['id']]['coefgen'] as $k => $v) {
                $total += $v['tot'];
                ?>
                <tr>
                    <td class="pri"></td>
                    <td colspan=2><?= h($data['descripcioncoeficientes'][$k]) ?> <?= h($v['val']) ?>%</td>
                    <td colspan=3 class="right"><?= money($v['tot']) ?></td>
                </tr>
                <?php
            }
        }
//            <tr>
//                <td class="pri"><b>Gastos particulares</b></td>
//                <td colspan="2"></td>
//                <td colspan="3"></td>
//            </tr>
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
                    ?>
                    <tr>
                        <td class="pri"></td>
                        <td colspan=2><?= h($w['descripcion']) ?> (<?= money($w['total']) ?>) <?= h($v['val']) ?>%</td>
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
            <td colspan="2"></td>
            <td colspan=3 class="right">
                <?php
                echo money($sf);
                ?>
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
