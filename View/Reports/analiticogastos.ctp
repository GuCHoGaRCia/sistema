<?php
/*
 * Es el reporte Analitico de Gastos
 */

$totalesxtipo = []; // para setear el importe total de deuda de cada tipo de liq
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Anal&iacute;tico de Gastos</title>
        <?php
        //echo $this->Minify->css(['jquery-ui.min']);
        echo $this->Minify->script(['jq', 'chart']);
        ?>
        <style type="text/css">
            .box-table-ax,.box-table-bx{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 10px;
                text-align: left;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                background: none !important;
            }
            .box-table-ax th,.box-table-bx th{
                font-size: 12px !important;
                padding:2px;
                color: #000;
                text-align:center;
                background: none !important;
            }
            .box-table-ax td{
                padding: 2px;
                background: none !important; 
                border-left: 2px solid #aabcfe;
                border-top: 2px solid #aabcfe !important;
                color: #000;
            }
            .box-table-bx td{
                padding: 2px;
                background: none !important; 
                border-left: 2px solid #aabcfe;
                border-bottom: 2px solid #aabcfe;
                color: #000;
                line-height:14px !important;
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
                font-size: 9px !important;
            }
            #print{
                position:absolute;
                right:0;
                cursor:pointer;
            }
            .ui-dialog-titlebar-close {
                visibility: hidden;
            }
        </style>
        <style type="text/css" media="print">
            @page { size: landscape; }
        </style>
    </head>
    <body style='text-align:center'>
        <?php
        // paso los datos del cliente y del consorcio
        $client = $cliente['Client'];
        $datoscliente = $this->element('datoscliente', ['dato' => $client]);
        $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio['Consorcio']]);
        cabecera($datoscliente, $datosconsorcio);
        detalle($movimientos, $rubrosinfo, $liquidationinfo, $cobranzas, false, true);
        echo "<div style='page-break-after:always'></div>";
        cabecera($datoscliente, $datosconsorcio);
        detalle($movimientos, $rubrosinfo, $liquidationinfo, $cobranzas, true, true);
        echo "<div style='page-break-after:always'></div>";
        cabecera($datoscliente, $datosconsorcio);
        detalle($movimientos, $rubrosinfo, $liquidationinfo, $cobranzas);
        echo "<div style='page-break-after:always'></div>";
        cabecera($datoscliente, $datosconsorcio);
        detalle($movimientos, $rubrosinfo, $liquidationinfo, $cobranzas, true);

        function cabecera($datoscliente, $datosconsorcio) {
            ?>
            <table style='font-size:12px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";border-bottom:0;width:90%;margin-left:auto;margin-right:auto' class="box-table-ax" align="center">
                <tr>
                    <?= $datoscliente ?>
                    <?= $datosconsorcio ?>
                </tr>
            </table>
            <?php
        }

        function detalle($movimientos, $rubrosinfo, $liquidationinfo, $cobranzas, $grafico = false, $ocultarcobranzas = false) {
            $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;margin-top:1px;margin-bottom:5px;width:90%'";
            $totales = [];
            foreach ($movimientos as $a => $b) {
                $data = json_decode($b['Resumene']['data'], true);
                foreach ($rubrosinfo as $k => $v) {
                    if (!isset($totales[$data['liquidation_id']])) {
                        $totales[$data['liquidation_id']] = [];
                    }
                    if (!isset($totales[$data['liquidation_id']][$k])) {
                        $totales[$data['liquidation_id']][$k] = 0.00;
                    }
                    foreach ($data['gastosinfo'] as $l => $m) {
                        if ($m['GastosGenerale']['rubro_id'] == $k) {
                            if (isset($m['GastosGeneraleDetalle'])) {
                                foreach ($m['GastosGeneraleDetalle'] as $j => $p) {
                                    if (is_numeric($j)) {
                                        // es la forma nueva de los GastosGeneraleDetalle
                                        //'GastosGeneraleDetalle' => array(
                                        //    (int) 0 => array(
                                        //            'id' => '3376',
                                        //            'gastos_generale_id' => '1314',
                                        //            'coeficiente_id' => '92',
                                        //            'amount' => '1080.00',
                                        //            'modified' => '2016-11-29 15:41:14'
                                        //    )
                                        //    ....
                                        //}
                                        $totales[$data['liquidation_id']][$k] += isset($p['amount']) ? (float) $p['amount'] : 0;
                                    } else {
                                        // es la forma vieja
                                        //'GastosGeneraleDetalle' => array(
                                        //    'coeficiente_id' => '92',
                                        //    'amount' => '839.63'
                                        //),
                                        if ($j === 'amount') {
                                            $totales[$data['liquidation_id']][$k] += isset($p) ? (float) $p : 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            ?>
            <table valign=top cellspacing=0 <?= $formato ?> class="box-table-bx" align="center">
                <thead>
                    <tr>
                        <th colspan="<?= count($rubrosinfo) + 4 ?>">ANAL&Iacute;TICO DE GASTOS - <?= h(end($liquidationinfo)['Liquidation']['periodo']) . " a " . h(reset($liquidationinfo)['Liquidation']['periodo']); ?></th>
                    </tr>
                    <tr style="">
                        <th class="totales" style="text-align:left;white-space: nowrap"><b>Liquidaci&oacute;n</b></th>
                        <?php
                        $rubros = [];
                        if (!$ocultarcobranzas) {
                            echo '<th class="totales right" style="width:120px"><b>Cobranzas</b></th>';
                        }
                        // para cada liquidacion muestro los totales x rubro
                        $sumafinal = [];
                        $sumatotal = 0;
                        // verifico los q tengan el TOTAL en cero, no los muestro
                        foreach ($liquidationinfo as $k => $v) {
                            $totalliq = 0;
                            foreach ($totales[$v['Liquidation']['id']] as $r => $s) {
                                $totalliq += $s;
                                if (!isset($sumafinal[$r])) {
                                    $sumafinal[$r] = 0;
                                }
                                $sumafinal[$r] += $s;
                                $sumatotal += $s;
                            }
                        }
                        foreach ($rubrosinfo as $l => $m) {
                            if ($sumafinal[$l] != 0) {
                                echo "<th class='totales' style='font-size:9px !important'><b>" . h($m) . "</b></th>";
                                $rubros[] = h($m);
                            }
                        }
                        echo '<th class="totales right" style="width:120px"><b>Total Gastos</b></th>';
                        if (!$ocultarcobranzas) {
                            echo '<th class="totales right" style="width:120px"><b>Saldo</b></th>';
                        }
                        ?>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $saldo = 0;
                    foreach ($liquidationinfo as $k => $v) {
                        echo "<tr><td style='white-space:nowrap;text-align:left'>" . h($v['Liquidation']['periodo']) . "</td>";
                        if (!$ocultarcobranzas) {
                            echo "<td style='border:2px solid #9baff1'>" . money($cobranzas[$v['Liquidation']['id']]) . "</td>";
                        }

                        $totalliq = 0;
                        foreach ($totales[$v['Liquidation']['id']] as $r => $s) {
                            if ($sumafinal[$r] != 0) {
                                $totalliq += $s;
                                echo "<td>" . money($s) . "</td>";
                            }
                        }
                        $saldo += $cobranzas[$v['Liquidation']['id']] - $totalliq;
                        echo "<td style='border:2px solid #9baff1'>" . money($totalliq) . "</td>";
                        if (!$ocultarcobranzas) {
                            echo "<td style='border:2px solid #9baff1'>" . money($cobranzas[$v['Liquidation']['id']] - $totalliq) . "</td>";
                        }
                        echo "</tr>";
                    }
                    $data = [];
                    echo "<tr><td><b>PORCENTAJES</b></td>";
                    if (!$ocultarcobranzas) {
                        echo "<td style='border:2px solid #9baff1'></td>";
                    }
                    foreach ($sumafinal as $k => $v) {
                        $porcentaje = ($sumatotal > 0 ? ($v * 100 / $sumatotal) : 0);
                        if ($porcentaje != 0) {
                            $data[] = $porcentaje;
                            echo "<td><b>" . money($porcentaje) . " %</b></td>";
                        }
                    }
                    echo "<td style='border:2px solid #9baff1'><b>100%</b></td>";
                    if (!$ocultarcobranzas) {
                        echo "<td style='border:2px solid #9baff1'></td></tr>";
                    }
                    echo "<tr><td><b>TOTALES</b></td>";
                    if (!$ocultarcobranzas) {
                        echo "<td style='border:2px solid #9baff1'><b>" . money(array_sum($cobranzas)) . "</b></td>";
                    }
                    foreach ($sumafinal as $k => $v) {
                        $porcentaje = ($sumatotal > 0 ? ($v * 100 / $sumatotal) : 0);
                        if ($porcentaje != 0) {
                            $data[] = $porcentaje;
                            echo "<td><b>" . money($v) . "</b></td>";
                        }
                    }
                    echo "<td style='border:2px solid #9baff1'><b>" . money($sumatotal) . "</b></td>";
                    if (!$ocultarcobranzas) {
                        echo "<td style='border:2px solid #9baff1'><b>" . money($saldo) . "</b></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div style='width:80%;max-width:80%'>
                <canvas id="myChart<?= $ocultarcobranzas ? 'a' : 'b' ?>" height='250' style="display:block;height:300px;width:80% !important"></canvas>
            </div>
            <br><br><br><br>
            <script>
                $("#print").on("click", function () {
                    $("#print").hide();
                    window.print();
                    $("#print").show();
                });

    <?php
    if ($grafico) {
        ?>
                    var myData = [
        <?php
        foreach ($data as $k => $v) {
            echo "$v,";
        }
        ?>
                    ];
                    var data = {
                        labels: [
        <?php
        foreach ($rubrosinfo as $l => $m) {
            if ($sumafinal[$l] != 0) {
                echo "'" . h($m) . "',";
            }
        }
        ?>
                        ],
                        datasets: [
                            {label: "Detalle de Gastos anuales por Rubro",
                                data: myData,
                                showAllTooltips: true,
                                backgroundColor: ["rgba(255, 99, 132,1)", "rgba(255, 159, 64,1)", 'rgba(255, 99, 132,1)', "rgba(255, 205, 86,1)", "rgba(75, 192, 192,1)", "rgba(54, 162, 235,1)", "rgba(153, 102, 255,1)", "rgba(201, 203, 207,1)", "rgba(255, 99, 132,1)", "rgba(255, 159, 64,1)", "rgba(255, 205, 86,1)", "rgba(75, 192, 192,1)", "rgba(54, 162, 235,1)", "rgba(153, 102, 255,1)", "rgba(201, 203, 207,1)", 'rgba(54, 162, 235,1)', 'rgba(255, 206, 86,1)', 'rgba(75, 192, 192,1)', 'rgba(153, 102, 255,1)', 'rgba(255, 159, 64,1)'],
                                //backgroundColor: ['#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#B82E2E', '#316395', '#994499', '#22AA99', '#3B3EAC', '#0099C6', '#DD4477', '#66AA00', '#AAAA11', '#6633CC', '#E67300', '#8B0707', '#329262', '#5574A6', '#3B3EAC'],
                                borderColor: ["rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)", 'rgba(255,99,132,1)', "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)", "rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)", "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)", 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', ],
                                borderWidth: 1
                            }]
                    };
                    var ctx = document.getElementById("myChart<?= $ocultarcobranzas ? 'a' : 'b' ?>");
                    var myDoughnutChart = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            title: {
                                display: true,
                            },
                            scales: {
                                xAxes: [{
                                        gridLines: {
                                            display: false
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            fontSize: 8,
                                        }
                                    }
                                ]
                            }
                        }
                    });
        <?php
    }
    ?>
            </script>
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
