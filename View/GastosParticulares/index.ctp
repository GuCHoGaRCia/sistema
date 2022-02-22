<div class="gastosParticulares index" id="seccionaimprimir">
    <h2><?php echo __('Gastos Particulares'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('GastosParticulare', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => !empty($d) ? $d : '']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => !empty($h) ? $h : '']);
    echo $this->Form->input('buscar', ['label' => '', 'style' => 'width:85px', 'placeholder' => __('Buscar'), 'value' => !empty($buscar) ? $buscar : '']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;']);
    echo "<div style='position:absolute;top:110px;right:150px'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'print' => false, 'multidelete' => true, 'model' => 'GastosParticulare']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('cuentasgastosparticulare_id', __('Cuenta')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('coeficiente_id', __('Coeficiente')); ?></th>
                <th><?php echo $this->Paginator->sort('date', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('description', __('Descripción')); ?></th>
                <th><?php echo $this->Paginator->sort('amount', __('Importe')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('heredable', __('Heredable')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?><span class='iom' onclick="mdtoggle()"> <?= $this->Html->image('sa.png', ['title' => 'Eliminar múltiples registros', 'style' => 'width:20px']) ?></span></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($gastosParticulares as $gastosParticulare):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($gastosParticulare['Consorcio']['name']) ?></td>
                    <td><?php echo h($gastosParticulare['Liquidation']['periodo']); ?></td>
                    <td><?php echo h($gastosParticulare['Cuentasgastosparticulare']['name']); ?></td>
                    <td><?php echo empty($gastosParticulare['Coeficiente']['name']) ? h($gastosParticulare['Propietario']['name'] . " (" . $gastosParticulare['Propietario']['unidad'] . ")") : ''; ?></td>
                    <td><?php echo h($gastosParticulare['Coeficiente']['name']); ?></td>
                    <?php
                    if (!$gastosParticulare['Liquidation']['bloqueada']) {
                        ?>
                        <td><span class="date" data-value="<?php echo h($gastosParticulare['GastosParticulare']['date']) ?>" data-pk="<?php echo h($gastosParticulare['GastosParticulare']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $gastosParticulare['GastosParticulare']['date']); ?></span>&nbsp;</td>
                        <td><span class="description" data-value="<?php echo h($gastosParticulare['GastosParticulare']['description']) ?>" data-pk="<?php echo h($gastosParticulare['GastosParticulare']['id']) ?>"><?php echo h($gastosParticulare['GastosParticulare']['description']) ?></span>&nbsp;</td>
                        <td><span class="amount" data-value="<?php echo h($gastosParticulare['GastosParticulare']['amount']) ?>" data-pk="<?php echo h($gastosParticulare['GastosParticulare']['id']) ?>"><?php echo h($gastosParticulare['GastosParticulare']['amount']) ?></span>&nbsp;</td>
                        <td class="center"><?php echo $this->Html->link($this->Html->image(h($gastosParticulare['GastosParticulare']['heredable'] ? '1' : '0') . '.png', array('title' => __('Es heredable?'))), array('controller' => 'GastosParticulares', 'action' => 'invertir', 'heredable', h($gastosParticulare['GastosParticulare']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                        <?php
                    } else {
                        ?>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $gastosParticulare['GastosParticulare']['date']); ?>&nbsp;</td>
                        <td><?php echo h($gastosParticulare['GastosParticulare']['description']) ?>&nbsp;</td>
                        <td><?php echo h($gastosParticulare['GastosParticulare']['amount']) ?>&nbsp;</td>
                        <td class="center"><?php echo $this->Html->image(h($gastosParticulare['GastosParticulare']['heredable'] ? '1' : '0') . '.png', array('title' => __('Es heredable?'))); ?></td>
                        <?php
                    }
                    ?>
                    <td class="acciones inline" style="width:100px">
                        <?php
                        echo $this->Html->image('view.png', array('url' => array('action' => 'view', $gastosParticulare['GastosParticulare']['id'])));
                        if (!$gastosParticulare['Liquidation']['bloqueada']) {
                            echo $this->Form->postLink($this->Html->image('delete.png'), array('action' => 'delete', $gastosParticulare['GastosParticulare']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($gastosParticulare['GastosParticulare']['description'])));
                            echo $this->Form->input('borrado', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $gastosParticulare['GastosParticulare']['id'], 'style' => 'box-shadow:none;transform: scale(2);margin:8px;position:absolute']);
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="10"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#GastosParticulareConsorcio").select2({language: "es", allowClear: true, placeholder: '<?= __('Seleccione Consorcio...') ?>'});

        $('.date').editable({type: 'date', name: 'date', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>GastosParticulares/editar', placement: 'right'});
        $('.description').editable({type: 'text', name: 'description', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>GastosParticulares/editar', placement: 'left'});
        $('.amount').editable({type: 'text', name: 'amount', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>GastosParticulares/editar', placement: 'left'});
        $("#filterLiquidation").select2({language: "es", placeholder: '<?= __("Seleccione una liquidación...") ?>'});
    });

</script>
<style>
    .iom{
        cursor:pointer;
    }
</style>
<style>
    .busc{
        margin:-5px -100px !important;
    }
    .busc input[type="text"]{
        width:70px !important;
    }
    #busqform{
        margin-left:0px;
    }
</style>
