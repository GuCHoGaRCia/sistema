<div class="coeficientesPropietarios index">
    <h2><?php echo __('Coeficientes Propietarios'); ?></h2>
    <?php
    echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => false, 'pagenew' => false, 'model' => 'CoeficientesPropietario']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Unidad - Propietario') . " (" . count($prop) . ")" ?></th>
                <?php
                $totalesCoeficientes = [];
                foreach ($coeficientes as $v) {
                    $totalesCoeficientes[$v['Coeficiente']['id']] = 0;
                    echo "<th>" . h($v['Coeficiente']['name']) . "</th>";
                }
                ?>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($prop as $k => $v):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($v['prop']['name']) ?> - <?php echo h($v['prop']['unidad'] . " (" . $v['prop']['code'] . ")"); ?></td>

                    <?php
                    foreach ($coeficientes as $r) {
                        $totalesCoeficientes[$r['Coeficiente']['id']] += $v[$r['Coeficiente']['id']]['value'];
                        ?>
                        <td><span class="value" id="<?= "cid_" . $r['Coeficiente']['id'] ?>" data-cid="<?= $r['Coeficiente']['id'] ?>" data-value="<?php echo h($v[$r['Coeficiente']['id']]['value']) ?>" data-pk="<?php echo h($v[$r['Coeficiente']['id']]['key']) ?>"><?php echo h($v[$r['Coeficiente']['id']]['value']) ?></span>&nbsp;</td>
                        <?php
                    }
                    ?>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.value').editable({type: 'text', name: 'value', success: function (n, r) {
                        var cid = $(this).data('cid');
                        var total = parseFloat(r) - parseFloat($(this).html());
                        //alert(total);
                        $(".value").each(function () {
                            if ($(this).data('cid') === cid) {
                                total += parseFloat($(this).html());
                            }
                        });
                        $("#tot_" + cid).html("<b>" + total.toFixed(5) + "%</b>");
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>coeficientesPropietarios/editar', placement: 'left'
                });
                $("#filterConsorcio").select2({language: "es", placeholder: "<?= __("Seleccione consorcio...") ?>", allowClear: true});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="<?= count($coeficientes) + 1 ?>"></td>
            <td class="bottom_d"></td>
        </tr>
        <tr>
            <td></td>
            <td><b>TOTALES</b></td>
            <?php
            foreach ($coeficientes as $r) {
                echo "<td id='tot_" . $r['Coeficiente']['id'] . "'><b>" . $totalesCoeficientes[$r['Coeficiente']['id']] . "%</b></td>";
            }
            ?>
            <td></td>
        </tr>
    </table>
</div>