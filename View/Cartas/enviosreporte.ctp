<?php
$totales = []; // totales x consorcio y tipo de carta
$tiposdecartas = [];
$consorcios = [];
$propietarios = [];
foreach ($cartas as $k => $v) {
    if (!isset($totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']])) {
        $totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']] = 0;
    }
    if (!isset($propietarios[$v['Carta']['consorcio_id']])) {
        $propietarios[$v['Carta']['consorcio_id']] = [];
    }
    $totales[$v['Carta']['consorcio_id']][$v['Cartastipo']['id']] += ($v['Carta']['oblea'] == 'S' && ctype_digit($v['Carta']['codigo']) ? $v['Carta']['codigo'] : 1);
    $totales[$v['Carta']['consorcio_id']]['cartas'][] = $v['Carta'];
    $tiposdecartas[$v['Cartastipo']['id']] = $v['Cartastipo']['abreviacion'];
    $consorcios[$v['Carta']['consorcio_id']] = $v['Consorcio'];
    if ($v['Carta']['propietario_id'] != 0) {
        $propietarios[$v['Carta']['consorcio_id']] += [$v['Carta']['propietario_id'] => $v['Propietario']];
    }
}

foreach ($consorcios as $r => $c) {
    foreach ($cartas as $k => $v) {
        if ($v['Consorcio']['id'] == $r) {
            $client = $v['Client'];
            break;
        }
    }

    cabecera($panel);
    anverso($cartas, $r, $fecha, $this->element('datoscliente', ['dato' => $client]));
    reverso($c, $r, $v, $cartas, $propietarios);
    ?>
    </body>
    </html>
    <?php
}

array_map("unlink", glob("*.pdf"));

function anverso($cartas, $r, $fecha, $datoscliente) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;border-bottom:0'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a" align="center">
        <tr>
            <?= $datoscliente ?>
            <td style="border-bottom:0" align="center" valign="middle">
                <img class="logocorreo" width="145" height="47"><br><br>
                <b>Env&iacute;os del dia <?= $fecha ?></b>
            </td>
        </tr>
    </table>
    <?php
}

function reverso($c, $r, $v1, $cartas, $propietarios) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;'";
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b"  align="center">
        <tr>
            <td colspan="5"><b>Consorcio: <?= h($c['name']) ?></b></td>
        </tr>
    </table>
    <table id='xx<?= $v1['Consorcio']['id'] ?>' width=750 valign=top cellspacing=0 cellpadding=0 style='border-top:0px;font-size: 10px; font-family: Verdana, Helvetica, sans-serif;' class='box-table-b' align='center'>
        <tr style='border-bottom: 2px solid #9baff1'>
            <?php
            //if (isset($propietarios[$v1['Consorcio']['id']][$v1['Propietario']['id']]['name'])) {
            ?>  
            <th class="propietario<?= $v1['Consorcio']['id'] ?>" style='cursor:pointer;'><b>Propietario</b></th>
            <th class="unidad<?= $v1['Consorcio']['id'] ?>" style='cursor:pointer;'><b>Unidad</b></th>
            <th class="codigo<?= $v1['Consorcio']['id'] ?>" style='cursor:pointer;'><b>C&oacute;digo</b></th>
            <?php
            //} else {
            ?>  
    <!--                <th colspan="3"><b>Propietario</b></th>-->
            <?php
            //}
            ?>
            <th style='width:100px'><b>Tipo</b></th>
            <th style='width:70px'><b>Oblea/Cantidad</b></th>
        </tr>
        <?php
        $totales = [];

        foreach ($cartas as $k => $v) {
            if ($v['Consorcio']['id'] != $r) {
                continue;
            }
            // es el consorcio actual
            echo '<tr>';
            if (isset($propietarios[$v1['Consorcio']['id']][$v['Propietario']['id']]['name'])) {
                echo "<td style='text-align:left' >" . (h($propietarios[$v1['Consorcio']['id']][$v['Propietario']['id']]['name']));
                echo "<td style='text-align:left' >" . (h($propietarios[$v1['Consorcio']['id']][$v['Propietario']['id']]['unidad']));
                echo "<td style='text-align:left' >" . (h($propietarios[$v1['Consorcio']['id']][$v['Propietario']['id']]['code']));
            } else {
                //echo "<tr><td style='text-align:left' colspan=3>" . (h($v['Cartastipo']['abreviacion'] == 'S' ? h($v['Carta']['propietario_id'] == '0' ? '-' : $v['Carta']['codigo']) : $v['Carta']['codigo'] ));
                echo "<td style='text-align:left' >" . (h($v['Cartastipo']['abreviacion'] == 'S' ? h($v['Carta']['propietario_id'] == '0' ? (is_numeric($v['Carta']['codigo']) ? '-' : $v['Carta']['codigo']) : $v['Carta']['codigo']) : $v['Carta']['codigo']) );
                echo "<td style='text-align:left' >" . (h('-'));
                echo "<td style='text-align:left' >" . (h('-'));
            }

            //echo "<tr><td style='text-align:left' colspan=3>" . (isset($propietarios[$v['Consorcio']['id']][$v['Propietario']['id']]['name']) ? h($propietarios[$v['Consorcio']['id']][$v['Propietario']['id']]['name'] . " - " . $propietarios[$v['Consorcio']['id']][$v['Propietario']['id']]['unidad'] . " (" . $propietarios[$v['Consorcio']['id']][$v['Propietario']['id']]['code'] . ")") : h($v['Carta']['codigo']));
            if ($v['Carta']['robada']) {
                echo "<span style='color:red;font-weight:bold'> [ROBADA]</span>";
            }
            echo "</td>";
            echo "<td>" . h($v['Cartastipo']['abreviacion']) . "</td>";
            // si es Simple entonces, 
            //       si el propietario_id=0 
            //              entonces es una simple cargada por terceros (en codigo esta la cantidad)
            //       sino
            //              '1' (porq en codigo esta el 500000460015 leido del scanner)
            // sino
            //      muestro la oblea (GR01010101 etc)
            //echo "<td>" . ($v['Cartastipo']['abreviacion'] == 'S' ? h($v['Carta']['propietario_id'] == '0' ? $v['Carta']['codigo'] : '1' ) : h($v['Carta']['oblea'])) . "</td>";

            echo "<td>" . ($v['Cartastipo']['abreviacion'] == 'S' ? h($v['Carta']['propietario_id'] == '0' ? (is_numeric($v['Carta']['codigo']) ? $v['Carta']['codigo'] : '1') : '1') : h($v['Carta']['oblea'])) . "</td>";

            if (!isset($totales[$v['Cartastipo']['abreviacion']])) {
                $totales[$v['Cartastipo']['abreviacion']] = 0;
            }
            $totales[$v['Cartastipo']['abreviacion']] += ($v['Carta']['oblea'] == 'S' && ctype_digit($v['Carta']['codigo']) && $v['Carta']['propietario_id'] == '0' ? $v['Carta']['codigo'] : 1);
            echo '</tr>';
        }
        echo "</table><table width=750 valign=top cellspacing=0 cellpadding=0 style='border-top:0px' class='box-table-b' align='center'>";
        echo '<tr class="totales" style="border-top:0px"><td colspan=5>TOTALES</td></tr>';
        foreach ($totales as $r => $r1) {
            if ($r1 > 0) {
                ?>
                <tr>
                    <td class="right" colspan="4"><b><?= h($r) ?></b></td>
                    <td class="right" style='width:100px'><?= h($r1) ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
    <br>
    <div style='page-break-after:always'></div>
    <script>
        var table<?= $v1['Consorcio']['id'] ?> = $('#xx<?= $v1['Consorcio']['id'] ?>');
        $('.propietario<?= $v1['Consorcio']['id'] ?>, .unidad<?= $v1['Consorcio']['id'] ?>, .codigo<?= $v1['Consorcio']['id'] ?>')
                .wrapInner('<span title="Ordenar por columna"/>')
                .each(function () {
                    var th = $(this),
                            thIndex = th.index(),
                            inverse = false;
                    th.click(function () {
                        table<?= $v1['Consorcio']['id'] ?>.find('td').filter(function () {
                            return $(this).index() === thIndex;
                        }).sortElements(function (a, b) {
                            if ($.text([a]) === $.text([b]))
                                return 0;
                            return $.text([a]) > $.text([b]) ?
                                    inverse ? -1 : 1
                                    : inverse ? 1 : -1;
                        }, function () {
                            return this.parentNode;
                        });
                        inverse = !inverse;
                    });
                });
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
                .logocorreo{
                    border:0px;
                    background-image:url(data:image/png;base64,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);
                    background-repeat:no-repeat;
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
            <script>
        jQuery.fn.sortElements = (function () {
            var sort = [].sort;
            return function (comparator, getSortable) {
                getSortable = getSortable || function () {
                    return this;
                };
                var placements = this.map(function () {
                    var sortElement = getSortable.call(this),
                            parentNode = sortElement.parentNode,
                            nextSibling = parentNode.insertBefore(
                                    document.createTextNode(''),
                                    sortElement.nextSibling
                                    );
                    return function () {
                        if (parentNode === this) {
                            throw new Error(
                                    "No puedes ordenar elementos si alguno es descendiente de otro."
                                    );
                        }
                        parentNode.insertBefore(this, nextSibling);
                        parentNode.removeChild(nextSibling);
                    };
                });
                return sort.call(this, comparator).each(function (i) {
                    placements[i].call(getSortable.call(this));
                });
            };
        })();
            </script>
        </head>
        <body>
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
        