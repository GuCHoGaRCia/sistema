<?php
// si viene desde "colaimpresiones", los datos estan en $data, sino en $data['Resumene']['data']
$data = json_decode(isset($data['Colaimpresione']['data']) ? $data['Colaimpresione']['data'] : (isset($data['Resumene']['data']) ? $data['Resumene']['data'] : $data), true);
$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
$notas = $info['Nota'];
$prefijo = $info['LiquidationsType']['prefijo']; // para el 5º digito de la unidad (0, 5, 9, etc)
$info = $info['Liquidation'];
$periodo = $info['periodo'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Cobranzas recibidas - <?= h($consorcio['name'] . " - " . $periodo) ?></title>
        <?= $this->Minify->script(['jq']) ?>
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
                border-top: 2px solid #9baff1 !important; 
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
                width:300px;
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
        <img src="/sistema/img/print2.png" id="print" />
        <?php
        $eselprimero = true;
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio]);
        anverso($consorcio, $periodo, $eselprimero, $datoscliente, $datosconsorcio);
        reverso($data['prop'], $cobranzas, $ajustes, $detalle, $totales, $data['saldo'], $bancoscuentas); // data['saldo'] se usa en conjunto con $cobranzas para saber cuanto pagó de cap e interes
        ?>
        <script>
            $("#print").on("click", function () {
                $("#print").hide();
                window.print();
                $("#print").show();
            });
        </script>
    </body>
</html>
<?php

function anverso($consorcio, $periodo, $eselprimero, $datoscliente, $datosconsorcio) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;border-bottom:0px;'";
    if ($eselprimero) {
        ?>
        <table width="750" <?= $formato ?> class="box-table-a" align="center">
            <tr>
                <?= $datoscliente ?>
                <?= $datosconsorcio ?>
            </tr>
            <tr>
                <td align="left" colspan=3 valign="middle" style="border-top:2px solid #9baff1;text-align:center">
                    <b>
                        Cobranzas liquidaci&oacute;n - <?= h($consorcio['name'] . " - " . $periodo) ?>
                    </b>
                </td>
            </tr>
        </table>
        <?php
    }
}

function reverso($propietarios, $cobranzas, $ajustes, $detalle, $totales, $saldos, $bancoscuentas) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;'";
    $total = 0;
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <td colspan="3" class="pri"><b>Propietario</b></td>
            <td>Fecha</td>
            <td>Capital</td>
            <td>Inter&eacute;s</td>
            <td>Importe</td>
        </tr>
        <?php
        $cap = $int = [];
        $tc = $ti = 0;
        //debug($cobranzas);
        foreach ($propietarios as $w => $x) {
            foreach ($cobranzas as $v) {
                if ($v['Cobranza']['propietario_id'] == $x['id']) {
                    if (!isset($cap[$x['id']])) {// para calcular cap e int cobrados
                        $cap[$x['id']] = (float) $saldos[$x['id']]['capant'];
                        $int[$x['id']] = (float) $saldos[$x['id']]['intant'];
                    }
                    $total += $v['Cobranzatipoliquidacione']['amount']; // el total de cobranzas

                    foreach ($ajustes as $a) {
                        if ($a['Ajuste']['propietario_id'] == $x['id']) {
                            $aj = $a['Ajustetipoliquidacione']['amount'];
                            if ($a['Ajustetipoliquidacione']['solocapital']) {
                                $cap[$x['id']] -= $aj;
                            } else {
                                $auxinteres = $int[$x['id']];
                                // si el interes quedó negativo, lo pongo en cero, sino hago interes - ajuste
                                $int[$x['id']] = ($int[$x['id']] - $aj < 0) ? 0 : $int[$x['id']] - $aj;
                                $aj -= $auxinteres;
                                $cap[$x['id']] = ($aj > 0) ? $cap[$x['id']] - $aj : $cap[$x['id']];
                            }
                        }
                    }

                    $formadepago = "";
                    $d = $detalle[$v['Cobranza']['id']];
                    if (!empty($d['Cajasingreso']['id']) && $d['Cajasingreso']['importe'] > 0) {// efectivo?=
                        $formadepago .= " - Efectivo";
                    }
                    if (!empty($d['Bancosdepositosefectivo']['id'])) {// transferencia o interdeposito?
                        $formadepago .= $d['Bancosdepositosefectivo']['es_transferencia'] ? ' - Transferencia' : ' - Interdepósito';
                    }
                    if (empty($d['Bancosdepositosefectivo']['id']) && $d['Cajasingreso']['cheque'] > 0) {
                        $formadepago .= ' - Cheque';
                    }
                    // en $saldos tengo capant e intant, con eso más $data['cobranzas'] (si hay alguna), veo cuanto pagó de capital e interés cada propietario así lo muestro
                    //foreach($v['cobranzas'])
                    $c = $i = 0;
                    if ($v['Cobranzatipoliquidacione']['solocapital'] || (float) $int[$x['id']] == 0) {
                        $c = $v['Cobranzatipoliquidacione']['amount'];
                    } else {
                        $totalcobranza = $v['Cobranzatipoliquidacione']['amount'];
                        $i = $int[$x['id']] - $totalcobranza > 0 ? $totalcobranza : $int[$x['id']];
                        $totalcobranza -= $i;
                        $int[$x['id']] -= $i;
                        $cap[$x['id']] -= $c;
                        $c = $v['Cobranzatipoliquidacione']['amount'] - $i;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="pri"><?= h($x['name'] . " - " . $x['unidad'] . " (" . $x['code'] . ")" . $formadepago) ?></td>
                        <td><?= date("d/m/Y", strtotime($v['Cobranza']['fecha'])) ?></td>
                        <td><?= money($c) ?></td>
                        <td><?= money($i) ?></td>
                        <td class="right"><?= money($v['Cobranzatipoliquidacione']['amount']) ?></td>
                    </tr>
                    <?php
                    $tc += $c;
                    $ti += $i;
                }
            }
        }
        foreach ($totales as $r => $r1) {
            if (is_int($r)) {// banco
                foreach ($r1 as $s => $s1) {
                    if ($s1 == 0) {
                        continue;
                    }
                    ?>
                    <tr class="totales">
                        <td colspan="6" class="pri"><b><?= h($s . " (" . $bancoscuentas[$r] . ")") ?></b></td>
                        <td class="right"><?= money($s1) ?></td>
                    </tr>
                    <?php
                }
            } else {// caja
                if ($r1 == 0) {
                    continue;
                }
                ?>
                <tr class="totales">
                    <td colspan="6" class="pri"><b><?= h($r) ?></b></td>
                    <td class="right"><?= money($r1) ?></td>
                </tr>
                <?php
            }
        }
        ?>
        <tr class="totales">
            <td colspan="4" class="pri"><b>Total cobranzas</b></td>
            <td class="right"><?= money($tc) ?></td>
            <td class="right"><?= money($ti) ?></td>
            <td class="right"><?= money($total) ?></td>
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
