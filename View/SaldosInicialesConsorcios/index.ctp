<div class="coeficientesPropietarios index">
    <h2><?php echo __('Saldos iniciales consorcios'); ?></h2>
    <?php
    //echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'CoeficientesPropietario')); 
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <?php
                foreach ($lt as $v) {
                    echo "<th>" . h($v) . "</th>";
                }
                ?>
                <th colspan="3" style="width:150px">&nbsp;</th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($consorcios as $k => $v):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($v) ?></td>

                    <?php
                    foreach ($lt as $r => $s) {
                        //$totalesCoeficientes[$r['Coeficiente']['id']] += $v[$r['Coeficiente']['id']]['value'];
                        ?>
                        <td><span class="saldo" id="<?= "cid_" . $saldos[$k][$r]['key'] ?>" data-cid="<?= $saldos[$k][$r]['key'] ?>" data-value="<?php echo h($saldos[$k][$r]['value']) ?>" data-pk="<?php echo h($saldos[$k][$r]['key']) ?>"><?php echo h($saldos[$k][$r]['value']) ?></span>&nbsp;</td>
                        <?php
                    }
                    ?>
                    <td colspan="3">&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.saldo').editable({type: 'number', step: '0.01', name: 'saldo', success: function (n, r) {
                        var cid = $(this).data('cid');
                        var total = parseFloat(r) - parseFloat($(this).html());
                        //alert(total);
                        $(".saldo").each(function () {
                            if ($(this).data('cid') === cid) {
                                total += parseFloat($(this).html());
                            }
                        });
                        $("#tot_" + cid).html("<b>" + total.toFixed(5) + "%</b>");
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>SaldosInicialesConsorcios/editar', placement: 'right'
                });
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="<?= count($lt) + 4 ?>"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php //echo $this->element('pagination'); ?></div>
<script>
    $(function () {
        $("#CoeficientesPropietarioConsorcio").select2({language: "es", placeholder: '<?= __('Seleccione un consorcio...') ?>'});
        $("#CoeficientesPropietarioConsorcio").change(function () {
            $("#CoeficientesPropietarioIndexForm").submit();
        });
    });
</script>