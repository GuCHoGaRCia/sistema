<div class="coeficientesPropietarios index">
    <h2><?php echo __('Saldos iniciales'); ?></h2>
    <?php
    echo $this->Form->create('SaldosIniciale', ['class' => 'inline']);
    echo $this->JqueryValidation->input('consorcio_id', ['label' => '']);
    echo $this->JqueryValidation->input('liquidations_type_id', ['label' => '']);
    echo $this->Form->end(__('Ver'));
    ?>
    <script>
        $(function () {
            $("#SaldosInicialeConsorcioId").select2({language: "es"});
            $("#SaldosInicialeLiquidationsTypeId").select2({language: "es"});
        });
    </script>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?= __('Propietario'); ?></th>
                <th><?= __('Capital'); ?></th>
                <th><?= __('Inter&eacute;s'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($saldos)) {
                $i = 0;
                foreach ($saldos as $k => $v):
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($v['Propietario']['unidad']) ?> - <?= h($v['Propietario']['name']) ?></td>
                        <td><span class="capital" data-value="<?= $v['SaldosIniciale']['capital'] ?>" data-pk="<?= $v['SaldosIniciale']['id'] ?>"><?= h($v['SaldosIniciale']['capital']) ?></span>&nbsp;</td>
                        <td><span class="interes" data-value="<?= $v['SaldosIniciale']['interes'] ?>" data-pk="<?= $v['SaldosIniciale']['id'] ?>"><?= h($v['SaldosIniciale']['interes']) ?></span>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                <?php endforeach; ?>
            <script>
                $(document).ready(function () {
                    $('.capital').editable({type: 'text', name: 'capital', success: function (n, r) {
                            var total = parseFloat(r) - parseFloat($(this).html());
                            $(".capital").each(function () {
                                total += parseFloat($(this).html());
                            });
                            $("#capital").html("<b>$ " + total.toFixed(2) + "</b>");
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>SaldosIniciales/editar', placement: 'left'
                    });
                    $('.interes').editable({type: 'text', name: 'interes', success: function (n, r) {
                            var total = parseFloat(r) - parseFloat($(this).html());
                            $(".interes").each(function () {
                                total += parseFloat($(this).html());
                            });
                            $("#interes").html("<b>$ " + total.toFixed(2) + "</b>");
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>SaldosIniciales/editar', placement: 'left'
                    });
                    var total = 0;
                    $(".capital").each(function () {
                        total += parseFloat($(this).html());
                    });
                    $("#capital").html("<b>$ " + total.toFixed(2) + "</b>");
                    var total = 0;
                    $(".interes").each(function () {
                        total += parseFloat($(this).html());
                    });
                    $("#interes").html("<b>$ " + total.toFixed(2) + "</b>");
                });
            </script>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"></td>
                <td class="bottom_d"></td>
            </tr>
            <tr>
                <td></td>
                <td><b>TOTALES</b></td>
                <td id='capital'><b>$ 0</b></td>
                <td id='interes'><b>$ 0</b></td>
                <td></td>
            </tr>
            <?php
        } else {
            ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"><?= __('Seleccione consorcio y tipo de liquidaci&oacute;n') ?></td>
                <td class="bottom_d"></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>