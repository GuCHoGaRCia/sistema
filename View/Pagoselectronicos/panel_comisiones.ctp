<style>
    @media print {
        .titulo{
            display:block;
        }
        img{
            display:none;
        }
        #seccionaimprimir{
            font-size:12px !important;
        }
        body{
            margin:15px;
            margin-bottom:25px;
        }
        @page {
            size: auto;
            margin:15px;
            margin-bottom:25px;
        }
    }
    .titulo{
        display:none;
    }
</style>
<div class="consorcios index" id="noimprimir">
    <h2>Comisiones PLAPSA</h2>
    <?php
    echo $this->Form->create('Pagoselectronico', ['class' => 'inline']);
    echo "<b>Desde</b> " . $this->Form->input('desde', ['label' => false, 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'autocomplete' => 'off', 'value' => isset($this->request->data['Pagoselectronico']['desde']) ? $this->request->data['Pagoselectronico']['desde'] : date("01/m/Y")]);
    echo "<b>Hasta</b> " . $this->Form->input('hasta', ['label' => false, 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'autocomplete' => 'off', 'value' => isset($this->request->data['Pagoselectronico']['hasta']) ? $this->request->data['Pagoselectronico']['hasta'] : date("d/m/Y")]);
    echo "<span id='a' title='Mes anterior' style='font-size:15px;font-weight:bold;cursor:pointer'>-1 mes</span>&nbsp;&nbsp;<span id='b' title='Mes siguiente' style='font-size:15px;font-weight:bold;cursor:pointer'> +1 mes</span>&nbsp;&nbsp;";
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Ver'), 'id' => 'guardar', 'style' => 'width:50px']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
    if (empty($pagoselectronicos)) {
        echo "<div class='info'>Seleccione Cliente...</div>";
    } else {
        ?>
        <div id="seccionaimprimir" style="display:block;width:100%">
            <div class="titulo" style="margin-top:3px;padding:8px;padding-bottom:0;border:2px dashed #000;text-align:center;font-weight:bold;width:100%">
                <?php
                echo __('COMISIONES PLAPSA') . " " .
                (isset($this->request->data['Pagoselectronico']['desde']) && !empty($this->request->data['Pagoselectronico']['desde']) ? ' desde ' . $this->request->data['Pagoselectronico']['desde'] : '') .
                (isset($this->request->data['Pagoselectronico']['hasta']) && !empty($this->request->data['Pagoselectronico']['hasta']) ? ' hasta ' . $this->request->data['Pagoselectronico']['hasta'] : '');
                ?>
            </div>
            <table cellpadding="0" cellspacing="0" style="width:700px">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Cont') ?></th>
                        <th><?php echo __('Cliente') ?></th>
                        <th style='text-align:right'><?php echo __('Cobranza') ?></th>
                        <th style='text-align:right'><?php echo __('Comision') ?></th>
                        <th style='text-align:right'><?php echo __('Total') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalimporte = $totalcomision = $totalcantidad = $i = $ultimo = 0;
                    foreach ($pagoselectronicos as $k => $v) {
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        if ($ultimo == $v['Pagoselectronico']['client_code']) {
                            continue;
                        }
                        echo "<tr$class>";
                        echo '<td class="borde_tabla"></td>';
                        echo "<td>" . h($v['Pagoselectronico']['client_code']) . "</td>";
                        echo "<td>" . (isset($clients[$v['Pagoselectronico']['client_code']]) ? h($clients[$v['Pagoselectronico']['client_code']]) : '<b>crear cliente ' . h($v['Pagoselectronico']['client_code']) . '</b>') . "</td>";
                        echo "<td style='text-align:right'>" . $this->Functions->money($v[0]['importe']) . "&nbsp;</td>";
                        echo "<td style='text-align:right'>" . $this->Functions->money($v[0]['comision']) . "&nbsp;</td>";
                        echo "<td style='text-align:right'>" . $v[0]['cantidad'] . "&nbsp;</td>";
                        echo '<td class="borde_tabla"></td>';
                        echo "</tr>";
                        $totalimporte += $v[0]['importe'];
                        $totalcomision += $v[0]['comision'];
                        $totalcantidad += $v[0]['cantidad'];
                        $ultimo = $v['Pagoselectronico']['client_code'];
                    }

                    // TOTALES
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($totalimporte) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($totalcomision) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $totalcantidad . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="5"></td>
                        <td class="bottom_d"></td>
                    </tr>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({/*maxDate: '0',*/ minDate: new Date(2016, 6, 1), changeYear: true, yearRange: '2016:+1'});
        $("#PagoselectronicoClient").select2({language: "es", placeholder: '<?= __("Seleccione Cliente...") ?>', allowClear: true});
        $("#PagoselectronicoComisionesForm").submit(function (event) {
            if ($("#PagoselectronicoClient").val() === "") {
                alert('<?= __('Seleccione un Pagoselectronico') ?>');
                return false;
            }
            var f1 = $("#PagoselectronicoDesde").val();
            var f2 = $("#PagoselectronicoHasta").val();
            if (f1 === "" || f2 === "") {
                alert('<?= __('Seleccione fecha Desde y Hasta') ?>');
                return false;
            }
            var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
            var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
            if (x > y) {
                alert('<?= __('La fecha Desde debe ser menor o igual a Hasta') ?>');
                return false;
            }
            $("#load").show();
            $("#guardar").prop('disabled', true);
            return true;
        });
    });
    $("#a").click(function () {
        var d = $("#PagoselectronicoDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#PagoselectronicoDesde").val($.datepicker.formatDate("dd/mm/yy", addMonths(x, -1)));
        var d = $("#PagoselectronicoDesde").val();
        var dias = new Date(d.substr(6, 4), d.substr(3, 2), 0).getDate();
        var d = $("#PagoselectronicoHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#PagoselectronicoHasta").val($.datepicker.formatDate(dias + "/mm/yy", addMonths(x, -1)));
        $(".dp").change();
    });
    $("#b").click(function () {
        var d = $("#PagoselectronicoDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#PagoselectronicoDesde").val($.datepicker.formatDate("dd/mm/yy", addMonths(x, 1)));
        var d = $("#PagoselectronicoDesde").val();
        var dias = new Date(d.substr(6, 4), d.substr(3, 2), 0).getDate();
        var d = $("#PagoselectronicoHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#PagoselectronicoHasta").val($.datepicker.formatDate(dias + "/mm/yy", addMonths(x, 1)));
        $(".dp").change();
    });


    function isLeapYear(year) {
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
    }

    function getDaysInMonth(year, month) {
        return [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    function addMonths(date, value) {
        var d = new Date(date),
                n = date.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth() + value);
        d.setDate(Math.min(n, getDaysInMonth(d.getFullYear(), d.getMonth())));
        return d;
    }
</script>

