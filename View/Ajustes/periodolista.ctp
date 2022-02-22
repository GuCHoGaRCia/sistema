<?php
//$lt
//$propietarios
//$saldos
/* debug($propietarios);
  debug($lt); */
?>
<div class="coeficientesPropietarios index" id='tabla'>
    <?php echo $this->Form->create('Ajuste', array('class' => 'jquery-validation', 'url' => ['controller' => 'Ajustes', 'action' => 'periodo'], 'id' => 'formperiodo')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr class='tit'>
                <td class="esq_i"></td>
                <th><?= __('Propietario') . " (" . count($propietarios) . ")" ?></th>
                <?php
                $totales = [];
                $sumatotal = 0;
                foreach ($lt as $k => $v) {
                    echo "<th class='center'><span title='Completar con los importes adeudados' style='cursor:pointer;text-decoration:underline' onclick='completar($k)'>" . h($v) . "</span>&nbsp;&nbsp;&nbsp;<span style='font-size:10px;cursor:pointer;text-decoration:underline' onclick='vaciar($k)'>[ Vaciar ]</span></th>";
                    $totales[$k] = 0;
                }
                ?>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($propietarios as $k => $v):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($v['name2']) . (!empty($v['estado_judicial']) ? "<b> [ " . h($v['estado_judicial']) . " ]</b>" : '') ?></td>
                    <?php
                    foreach ($lt as $r => $s1) {
                        $total = 0;
                        $s = ['capital' => 0, 'interes' => 0];
                        if (isset($saldos[$r][$k])) {// si no esta seteado, son liq iniciales
                            $s = $saldos[$r][$k];
                            $total = ($s['capital'] + $s['interes']); // le saque el floor() porq a veces da mal los decimales
                        }
                        $cob = $this->Functions->find($cobranzas[$r], ['propietario_id' => $k], true);
                        foreach ($cob as $j => $h) {
                            $total -= $cobranzas[$r][$h]['Cobranzatipoliquidacione']['amount'];
                        }
                        $aj = $this->Functions->find($ajustes[$r], ['propietario_id' => $k], true);
                        foreach ($aj as $j => $h) {
                            $total -= $ajustes[$r][$h]['Ajustetipoliquidacione']['amount'];
                        }
                        $redondeo = round($s['capital'] + $s['interes'] - intval($s['capital'] + $s['interes']), 2);
                        $total = $total > 0 ? round($total - $redondeo, 2) : $total;
                        //$totales[$r] += $total <= 0 ? 0 : $total;
                        $sumatotal += $total <= 0 ? 0 : $total;
                        ?>
                        <td class='center inline'>
                            <?php
                            echo $this->JqueryValidation->input('f_' . $k . '_' . $r, ['label' => __('Fecha ajuste'), 'id' => 'f_' . $k . '_' . $r, 'type' => 'text', 'style' => 'width:90px', /* 'readonly' => 'readonly', */ 'class' => 'dp' . " in_" . $r]);
                            echo $this->JqueryValidation->input('c_' . $k . '_' . $r, ['label' => __('Importe'), 'class' => "in_" . $r, 'id' => 'c_' . $k . '_' . $r, 'type' => 'text', 'style' => 'width:100px;font-weight:bold !important;color:green', 'data-total' => $total <= 0 ? '' : $total, 'placeholder' => $this->Functions->money($total), 'value' => '']);
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class='tot' style="height:25px;font-size:15px;font-weight:bold">
                <td class="borde_tabla"></td>
                <td><b>TOTALES</b></td>
                <?php
                foreach ($lt as $k => $v) {
                    echo "<td class='totales center' id='tot_" . $k . "'>" . $this->Functions->money($totales[$k]) . "</td>";
                }
                ?>
                <td class="borde_tabla"></td>
            </tr>
            <tr class="altrow" style="height:25px;font-size:15px;font-weight:bold">
                <td class="borde_tabla"></td>
                <td>TOTAL GENERAL: <span id="totgen">0</span></td>
                <td colspan="<?= count($lt) ?>"></td>
                <td class="borde_tabla"></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="<?= count($lt) + 1 ?>"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php
    echo $this->JqueryValidation->input('consorcio_id', ['type' => 'hidden', 'value' => $cid]); // consorcio_id
//    echo "<div class='inline' style='font-size:15px;font-weight:bold'><br>Forma de pago (efectivo, transferencia o interdep&oacute;sito). Si no se encuentran disponibles las opci&oacute;nes \"Transferencia\" o \"Interdep&oacute;sito\", deber&aacute; crear una cuenta bancaria para el consorcio:<br><br> ";
//    $opt = ['E' => '&nbsp;Efectivo']; //, 
//    if (!empty($cb)) {
//        $opt += ['T' => '&nbsp;Transferencia', 'I' => '&nbsp;Interdep&oacute;sito'];
//        echo $this->JqueryValidation->input('bancoscuenta_id', ['type' => 'hidden', 'value' => h($cb)]);
//    }
//    echo $this->Form->radio('fdp', $opt, ['legend' => false, 'value' => 'E', 'label' => false, 'style' => 'display:inline;margin-left:30px']);
//    echo "</div><br>";
    echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar'));
    ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['controller' => 'ajustes', 'action' => 'periodo'], [], __('Desea cancelar?')); ?>
<script>
<?php
echo "var totals = [";
$count = count($lt);
for ($i = 0; $i < $count - 1; $i++) {
    echo "0.00,";
}
echo "0.00];";
?>
    function vaciar(i) {
        $('.in_' + i).each(function () {
            $(this).val('');
        });
        $('.fp_' + i).each(function () {
            $(this).val('');
        });
        actualiza();
    }
    function completar(i) {
        $('.in_' + i).each(function () {
            if (parseFloat($(this).data('total')) > 0) {
                $(this).val(parseFloat($(this).data('total')).toFixed(2));
                $(this).css('font-weight', 'bold');
            }
        });
        actualiza();
    }
    function actualiza() {
<?php
echo "var totals = [";
$count = count($lt);
for ($i = 0; $i < $count - 1; $i++) {
    echo "0.00,";
}
echo "0.00];";
?>
        $("#totgen").html(0);
        var $dataRows = $("#tabla tr:not('.tit, .tot')");
        $dataRows.each(function () {
            $(this).find('input[class^="in_"]').each(function (i) {
                var val = parseFloat($(this).val());
                if (val < 0) {
                    return false;
                }
                if (!isNaN(val)) {
                    totals[i] += val;
                }
            });
        });
        $("#tabla td.totales").each(function (i) {
            $(this).html(parseFloat(totals[i]).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            var c = parseFloat($("#totgen").text().replace(",", ""));
            $("#totgen").html((c + parseFloat(totals[i])).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });
    }
    $(document).ready(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', changeYear: true, yearRange: '2016:+1'});
        // inicializo el total
        $('input[class^="in_"]').blur(function () {
            var num = $(this).val();
            if (/^(?:[1-9]\d*|0)?(?:\.\d+)?$/.test(num)) {
                $(this).css("border", "1px solid #aaa");
            } else {
                $(this).css("border", "2px solid red");
                alert("<?= __("Los importes deben ser mayores o iguales a cero") ?>");
            }
            actualiza();
        });

        $(document).on('click', '#guardar', function () {
            if (parseFloat($("#totgen").text().replace(",", "")) === 0) {
                alert("<?= __("Debe agregar al menos una cobranza") ?>");
                return false;
            }
            $('input[class^="in_"]').each(function (i) {
                if ($(this).val() !== "") {
                    if (isNaN(parseFloat($(this).val()))) {
                        alert("<?= __("Los importes deben ser mayores o iguales a cero") ?>");
                        event.preventDefault();
                        return false;
                    }
                }
            });

//            var tipo = 'en Efectivo';
//            if ($("input[type='radio']:checked").val() === "T") {
//                tipo = 'por Transferencia';
//            }
//            if ($("input[type='radio']:checked").val() === "I") {
//                tipo = 'por Interdepósito';
//            }
            $('input[class^="in_"]').each(function () {
                if ($(this).val() === "" || parseFloat($(this).val()) === 0) {
                    $(this).attr('disabled', true);<?php /* no envio los vacios o cero */ ?>
                }
                var cad = $(this).attr('id');
                if ($("#f_" + cad.substr(2)).val() === "") {
                    $("#f_" + cad.substr(2)).attr('disabled', true);<?php /* deshabilito la fecha asociada si esta vacia (no me importa si cargo importe o no) */ ?>
                }
            });
            $("#guardar").prop('disabled', true);
            $("#formperiodo").submit();
        });

        $(document).keypress(function (e) {
            if (e.which === 13) {
                e.preventDefault();
            }
        });
    });

</script>
<style>
    input::placeholder {
        font-weight:normal;
    }
</style>