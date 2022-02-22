<?php

$client = $cliente['Client'];
$consorcio = $consorcio['Consorcio'];
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Informe de Deuda</title>
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
                font-size: 10px;
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
        <?php
        // paso los datos del cliente y del consorcio
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        cabecera($consorcio, $datoscliente);
        detalle($propietario_id, $datosPropietario, $liqselegidas, $liquidaciones, $saldospropietario, $liqAnterior, $saldosPropietarioLiqAnterior);

        @separacion(55);
        ?>
    </body>
</html>
<?php

function cabecera($consorcio, $datoscliente) {
    ?>
<table style='font-size:11px;width:850px;max-width:850px;border-bottom:0' class="box-table-a" align="center">
    <tr>
            <?= $datoscliente ?>
        <td align="left">
            <b>Consorcio: </b><?= h($consorcio['name']) ?><br/>
            <b>Domicilio: </b><?= h($consorcio['address']) ?><br/>
            <b>Localidad: </b><?= h($consorcio['city']) ?><br/>
            <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
            <p style="border-top:2px solid #9baff1;text-align:center;line-height:14px;"><br>
                <b>Tipo de Liquidaci&oacute;n: </b><?= h('ORDINARIA') ?>
                <br><br>
            </p>
        </td>
    </tr>
</table>
    <?php
}

function detalle($propietario_id, $datosPropietario, $liqselegidas, $liquidaciones, $saldospropietario, $liqAnterior, $saldosPropietarioLiqAnterior) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;width:850px;max-width:850px;'";
    ?>
<table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
    <thead>
        <tr>
            <td colspan="9" style='text-align:center;font-size:12px;font-weight:bold'>Propietario Deudor: <?= $datosPropietario[$propietario_id]['name'] ?></td> 
        </tr>
        <tr>
            <td colspan="9" style='text-align:center;font-size:10px;font-weight:bold'>Unidad: <?= $datosPropietario[$propietario_id]['unidad'] ?> <br> Poligono: <?= $datosPropietario[$propietario_id]['poligono'] ?> &nbsp;&nbsp; UF: <?= $datosPropietario[$propietario_id]['code'] ?></td>
        </tr>

        <tr>
            <th class="totales" style="width:270px"><b>Periodo</b></th> 
            <th class="totales" style="width:80px"><b>Fecha Vto.</b></th>
            <th id='ajuste' class="totales" style="width:120px"><span title = 'Click para borrar columna de ajuste' style='cursor:pointer' onclick='borraColumnaDeAjuste()'><b>Ajuste</b></span></th>
            <th id='cobranza' class="totales" style="width:120px"><span title = 'Click para borrar columna de cobranza' style='cursor:pointer' onclick='borraColumnaDeCobranza()'><b>Cobranza</b></span></th>
            <th class="totales right" style="width:120px"><b>Capital</b></th>
            <th class="totales right" style="width:120px"><b>Capital Acumulado</b></th>
            <th id='interes' class="totales right" style="width:120px"><span title = 'Click para borrar columnas de interés y sub-total' style='cursor:pointer' onclick='borraColumnasDeInteresYSubTotal()'><b>Inter&eacute;s</b></span></th>
            <th id='interesA' class="totales right" style="width:120px"><span title = 'Click para borrar columnas de interés y sub-total' style='cursor:pointer' onclick='borraColumnasDeInteresYSubTotal()'><b>Inter&eacute;s Acumulado</b></span></th>
            <th id='subtotal' class="totales" style="width:120px"><b>Sub-Total</b></th>
        </tr>
    </thead>
        <?php
        /*
          Ejemplo estructura de $liqselegidas:
          array(
          (int) 4952 => 'ABRIL 2018',
          (int) 2738 => 'Saldo inicial TATOMAR IX (ORDINARIA)'
          )
         */
        
        //$sumacobranzas = $sumaajustes = 0;

        $capital = $interes = $capitalacumulado = $interesacumulado = $cobranzas = $ajustes = 0;
        
        if(!empty($liqAnterior) && !empty($saldosPropietarioLiqAnterior)){           
            $fechaVencimiento = date("d/m/Y", strtotime($liqAnterior['vencimiento']));
            
            $cap = money($saldosPropietarioLiqAnterior['capital']);
            $int = money($saldosPropietarioLiqAnterior['interes']);
            
            echo "<tr><td style='text-align:left'>" . h('Saldo anterior') . "</td>";
            echo "<td style='text-align:left'>" . h($fechaVencimiento) . "</td>";
            echo "<td id='ajuste" . $liqAnterior['id'] . "' style='text-align:right; border-right:2px solid #9baff1'></td>";
            echo "<td id='cobranza" . $liqAnterior['id'] . "' style='text-align:right; border-right:2px solid #9baff1'></td>";
            echo "<td style='text-align:right'>" . $cap . "</td>";
            echo "<td style='text-align:right; border-right:2px solid #9baff1'>" . $cap . "</td>";
            echo "<td id='interes" . $liqAnterior['id'] . "' style='text-align:right'>" . $int . "</td>";
            echo "<td id='interesA" . $liqAnterior['id'] . "' style='text-align:right; border-right:2px solid #9baff1'>" . $int . "</td>";
            echo "<td id='subtotal" . $liqAnterior['id'] . "' style='text-align:right; border-right:2px solid #9baff1'>" . money($saldosPropietarioLiqAnterior['capital'] + $saldosPropietarioLiqAnterior['interes']) . "</td>";
            echo "</tr>";
        }       
        
        foreach ($liqselegidas as $k => $v) {
            $fechaDiaMesAnio = date("d/m/Y", strtotime($liquidaciones[$k]['vencimiento']));

            $capital = $liquidaciones[$k]['inicial'] ? $saldospropietario[$k]['capital'] : $saldospropietario[$k][$propietario_id]['gastosgenerales'] + $saldospropietario[$k][$propietario_id]['gastosparticulares'];
            $interes = $liquidaciones[$k]['inicial'] ? $saldospropietario[$k]['interes'] : $saldospropietario[$k][$propietario_id]['interesactual'];
            $interesacumulado = $liquidaciones[$k]['inicial'] ? $saldospropietario[$k]['interes'] : $saldospropietario[$k][$propietario_id]['interes'];
            $capitalacumulado = $liquidaciones[$k]['inicial'] ? $saldospropietario[$k]['capital'] : $saldospropietario[$k][$propietario_id]['capital'];

            $cobranzas = $liquidaciones[$k]['inicial'] ? 0 : $saldospropietario[$k][$propietario_id]['cobranzas'];
            $ajustes = $liquidaciones[$k]['inicial'] ? 0 : $saldospropietario[$k][$propietario_id]['ajustes'];
            //$sumacobranzas += $cobranzas;
            //$sumaajustes += $ajustes;

            echo "<tr><td style='text-align:left'>" . h($v['periodo']) . "</td>";
            echo "<td style='text-align:left'>" . h($fechaDiaMesAnio) . "</td>";
            echo "<td id='ajuste" . $k . "' style='text-align:right; border-right:2px solid #9baff1'>" . money($ajustes) . "</td>";
            echo "<td id='cobranza" . $k . "' style='text-align:right; border-right:2px solid #9baff1'>" . money($cobranzas) . "</td>";
            echo "<td style='text-align:right'>" . money($capital) . "</td>";
            echo "<td style='text-align:right; border-right:2px solid #9baff1'>" . money($capitalacumulado) . "</td>";
            echo "<td id='interes" . $k . "' style='text-align:right'>" . money($interes) . "</td>";
            echo "<td id='interesA" . $k . "' style='text-align:right; border-right:2px solid #9baff1'>" . money($interesacumulado) . "</td>";
            echo "<td id='subtotal" . $k . "' style='text-align:right; border-right:2px solid #9baff1'>" . money($capitalacumulado + $interesacumulado) . "</td>";
            echo "</tr>";
        }
        ?>
    <tr class="totales">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="borrarajustes">&nbsp;</td>
        <td class="borrarcobranzas">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="border-right : 2px solid #9baff1;"><span id="capitalacumulado"><?= money($capitalacumulado) ?></span></td>
        <td class="borrar">&nbsp;</td>
        <td class="borrar"><span><?= money($interesacumulado) ?></span></td>
        <td class="borrar" style="border-right : 2px solid #9baff1;"><span><?= money($capitalacumulado + $interesacumulado) ?></span></td>
    </tr>
</table>
<br/>

<script>
<?php
//echo "var sumacobranzas = " . $sumacobranzas . ";";
//echo "var sumaajustes = " . $sumaajustes . ";";
/*
    if (sumacobranzas === 0) {
        borraColumnaDeCobranza();
        //$("#cobranza").hide();
        //$(".borrarcobranzas").hide();
        //$("td[id^='cobranza']").each(function () {
        //    $(this).hide();
        //});
    }
    if (sumaajustes === 0) {
        $("#ajuste").hide();
        $(".borrarajustes").hide();
        $("td[id^='ajuste']").each(function () {
            $(this).hide();
        });
    }
    */
?>

    function borraColumnaDeAjuste() {
        $("#ajuste").hide();
        $(".borrarajustes").hide();
        $("td[id^='ajuste']").each(function () {
            $(this).hide();
        });
    }
    function borraColumnaDeCobranza() {
        $("#cobranza").hide();
        $(".borrarcobranzas").hide();
        $("td[id^='cobranza']").each(function () {
            $(this).hide();
        });
    }
    function borraColumnasDeInteresYSubTotal() {
        $("#interes").hide();
        $("#interesA").hide();
        $("#subtotal").hide();
        $(".borrar").hide();
        $("td[id^='interes']").each(function () {
            $(this).hide();
        });
        $("td[id^='subtotal']").each(function () {
            $(this).hide();
        });
        $('.box-table-b').css('border', '20px solid #9baff1 !important');
    }
</script>
    <?php
}

function separacion() {
    echo "<div style='page-break-after:always'></div>";
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}
