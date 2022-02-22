<?php
$totales = []; // totales x consorcio y tipo de carta
$tiposdecartas = [];
$consorcios = [];
//$propietarios = [];

foreach ($cartas as $k => $v) {
    if (!isset($totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']])) {
        $totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']] = 0;
    }
    $totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']] += $v[0]['cant'];
    $tiposdecartas[$v['Cartastipo']['id']] = $v['Cartastipo']['abreviacion'];
    $consorcios[$v['Carta']['consorcio_id']] = $v;
}

anverso($fecha);
foreach ($consorcios as $r => $c) {
    cabecera($panel);   
    reverso($c, $this->webroot, $totales, $tiposdecartas);
}

function anverso($fecha) {
    $formato = "style='font-size:14px;font-family:Verdana,Helvetica,sans-serif;border-bottom:0'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a" align="center">
        <tr>
            <?php //$datoscliente ?>
            <td style="border-bottom:0" align="center" valign="middle">
    <!--                <img class="logocorreo" width="145" height="47"><br>--><br>
                <b>Env&iacute;os del dia hasta <?= $fecha ?></b>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($c, $webroot, $totales, $tiposdecartas) {
    $fechacreacion = date("d-m-Y", strtotime( $c['Carta']['created'] ));    
    $fechacreacionymd = date("Y-m-d", strtotime( $c['Carta']['created'] ));    
    $style = $facturado = $check = "";
    if ($c['Carta']['facturado'] === true) {
        $facturado = 'FACTURADO';
        $style = 'background-color:red; font-size:22px; color:white;';
        $check = 'checked';
    }

    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;'";
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b"  align="center">
        <tr><td><span id='facturado<?= $c['Consorcio']['id'] ?>' style='<?= $style ?>'><?= $facturado ?></span></td>
            <td colspan="3"><b>Adminstrador: <?= h($c['Client']['name']) ?>&nbsp;&nbsp;&nbsp;&nbsp;</b>
                <b>Consorcio: <?= h($c['Consorcio']['name']) ?></b></td>   
            <td> <input type='hidden' id='fechacreacion<?= $c['Consorcio']['id'] ?>' value="<?= $fechacreacionymd ?>" />
                 <input type='checkbox' <?= $check ?> style="transform: scale(1.4)" onclick="facturar(<?= $c['Consorcio']['id'] ?>)" id="checkbox<?= $c['Consorcio']['id'] ?>"/> Facturado 
            </td>
        </tr>  
    </table>
    <table id='xx' width=750 valign=top cellspacing=0 cellpadding=0 style='border-top:0px;font-size: 10px; font-family: Verdana, Helvetica, sans-serif;' class='box-table-b' align='center'>

        <?php
        echo "</table><table width=750 valign=top cellspacing=0 cellpadding=0 style='border-top:0px' class='box-table-b' align='center'>";
        echo "<tr class=\"totales\" style=\"border:0px\"><td colspan=5>TOTALES del dia $fechacreacion</td></tr>";


        $totaltodascartas = 0;
        foreach ($totales[$c['Consorcio']['id']] as $r => $r1) {
            $totaltodascartas += $r1;
            ?>
            <tr style='border-top: 0 !important'>
                <td class="right" colspan="4"><b><?= h($tiposdecartas[$r]) ?></b></td>
                <td class="right" style='width:100px'><?= h($r1) ?></td>
            </tr>
            <?php
        }
        ?>
        <tr style='border-top: solid #aabcfe !important'>
            <td class="right" colspan="4"><b>TOTAL DE CARTAS</b></td>
            <td class="right" style='width:100px'><?= h($totaltodascartas) ?></td>
        </tr>        


    </table>
    <br><br><br>

    <script>
        function facturar(id) {
            $.ajax({
                type: "POST",
                url: "<?= $webroot ?>panel/Cartas/gF",
                data: {f: $('#fechacreacion' + id).val(), check: $('#checkbox' + id).is(':checked'), consorcio: id}
            }).done(function (msg) {
                try {
                    if (msg === '1') {
                        $("#facturado" + id).html('FACTURADO');
                        $("#facturado" + id).css({'background-color': 'red', 'font-size': '22px', 'color': 'white'});
                    } else if (msg === '2') {
                        $("#facturado" + id).html('');
                    } else {
                        alert('No se pudo realizar la accion solicitada');
                    }
                } catch (err) {
                    //
                }
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la accion, intente nuevamente");
                }
            });
        }
    </script>
    <?php
}

function cabecera($panel) {
    ?>
    <!DOCTYPE html>
    <html lang="es-419">
        <head>
            <meta charset="UTF-8">
            <title>Envio de cartas</title>
            <?php
            if ($panel === false) {
                ?>
                <script type="text/javascript" src="/sistema/js/jq.js"></script>
                <?php
            }
            ?>
            <style type="text/css">
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
                    color: #000;
                }
                .box-table-b td{
                    padding: 10px;
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
        <?php
    }

    function separacion($px) {
        ?>
        <div id="separacion" style="height:<?= $px ?>px;clear:both;">
        </div>
        <?php
    }

    function money($valor) {
        return CakeNumber::currency(h($valor), '', array('negative' => '-'));
    }
    