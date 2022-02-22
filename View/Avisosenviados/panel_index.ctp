<div class="avisosenviados index">
    <h2><?php echo __('Avisos Enviados por Mes'); ?></h2>
    <?php
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    echo "<div class='inline' style='margin:-5px 0 0 0' id='noimprimir'>";
    echo $this->Form->create('Avisosenviado', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('client_id', ['label' => false, 'empty' => '', 'options' => $clients, 'required' => false, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->Form->input('ano', ['label' => false, 'empty' => '', 'options' => [date("Y") - 1, date("Y"), date("Y") + 1], 'required' => false, 'type' => 'select', 'selected' => isset($a) ? $a : 1]);
    echo $this->Form->input('mes', ['label' => false, 'empty' => '', 'options' => $meses, 'required' => false, 'type' => 'select', 'selected' => isset($m) ? $m : '']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('month', __('Mes')); ?></th>
                <th><?php echo $this->Paginator->sort('cantidad', __('Cantidad')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = $total = 0;
            foreach ($avisosenviados as $avisosenviado):
                $total += $avisosenviado['Avisosenviado']['cantidad'];
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($avisosenviado['Client']['name']) ?></td>
                    <td><?php echo h($avisosenviado['Consorcio']['name']) ?></td>
                    <td><?php echo h($meses[$avisosenviado['Avisosenviado']['month'] - 1] . " de " . $avisosenviado['Avisosenviado']['year']) ?>&nbsp;</td>
                    <td><?php echo h($avisosenviado['Avisosenviado']['cantidad']) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr style="border-top:2px solid #000">
                <td class="borde_tabla"></td>
                <td colspan="3"><b>Total</b></td>
                <td><b><?php echo h($total) ?></b>&nbsp;</td>
                <td class="borde_tabla"></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $(function () {
        $("#AvisosenviadoClientId").select2({language: "es", placeholder: "<?= __('Seleccione cliente...') ?>", allowClear: true});
        $("#AvisosenviadoAno").select2({language: "es", placeholder: "<?= __('Seleccione año...') ?>", allowClear: true});
        $("#AvisosenviadoMes").select2({language: "es", placeholder: "<?= __('Seleccione mes...') ?>", allowClear: true});
    });
</script>