<div class="liquidations index">
    <h2><?php echo __('Liquidaciones'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'pagesearch' => true, 'filter' => ['enabled' => true, 'options' => $cliente, 'field' => 'cliente', 'panel' => true], 'pagenew' => true, 'model' => 'Liquidation')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Cliente - Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidations_type_id', __('Tipo de liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('periodo', __('Período')); ?></th>
                <th><?php echo $this->Paginator->sort('vencimiento', __('Vencimiento')); ?></th>
                <th><?php echo $this->Paginator->sort('limite', __('Límite')); ?></th>
                <th><?php echo $this->Paginator->sort('disponibilidad', __('Disp.')); ?></th>
                <th><?php echo $this->Paginator->sort('disponibilidadpaga', __('Disp. Paga')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('cerrada', __('Prorrateada')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('bloqueada', __('Bloqueada')); ?></th>
                <th class="acciones" style="width:140px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($liquidations as $liquidation):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($liquidation['Client']['name'] . " - " . $liquidation['Consorcio']['name']); ?></td>
                    <td><?php echo h($liquidation['LiquidationsType']['name']); ?></td>
                    <td><span class="name" data-value="<?php echo h($liquidation['Liquidation']['name']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo h($liquidation['Liquidation']['name']) ?></span>&nbsp;</td>
                    <td><span class="periodo" data-value="<?php echo h($liquidation['Liquidation']['periodo']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo h($liquidation['Liquidation']['periodo']) ?></span>&nbsp;</td>
                    <td><span class="vencimiento" data-value="<?php echo h($liquidation['Liquidation']['vencimiento']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['vencimiento']) ?></span>&nbsp;</td>
                    <td><span class="limite" data-value="<?php echo h($liquidation['Liquidation']['limite']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['limite']) ?></span>&nbsp;</td>
                    <td><span class="disponibilidad" data-value= "<?php echo h($liquidation['Liquidation']['disponibilidad']) ?>" data-pk = "<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo h($liquidation['Liquidation']['disponibilidad']) ?></span>&nbsp;</td>
                    <td><span class="disponibilidadpaga" data-value= "<?php echo h($liquidation['Liquidation']['disponibilidadpaga']) ?>" data-pk = "<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo h($liquidation['Liquidation']['disponibilidadpaga']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(h($liquidation['Liquidation']['cerrada'] ? '1' : '0') . '.png', array('title' => __('La liquidación se encuentra cerrada?'))); ?></td>
                    <td class="center"><?php echo $this->Html->image(h($liquidation['Liquidation']['bloqueada'] ? '1' : '0') . '.png', array('title' => __('La liquidación se encuentra bloqueda?'))); ?></td>
                    <td class="acciones" style="width:140px">
                        <?php
                        //echo $this->Form->postLink($this->Html->image('liquidation.png', array('alt' => __('Cerrar liquidacion'), 'title' => __('Cerrar liquidacion'))), array('action' => 'controlesCierres', $liquidation['Liquidation']['id']), array('escape' => false), h(__('Desea cerrar la liquidacion # %s?', $liquidation['Liquidation']['name'])));
                        if ($liquidation['Liquidation']['bloqueada'] == 0) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $liquidation['Liquidation']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($liquidation['Liquidation']['name'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="11"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.name').editable({type: 'text', name: 'name', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'right'});
        $('.periodo').editable({type: 'text', name: 'periodo', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'right'});
        $('.vencimiento').editable({type: 'date', name: 'vencimiento', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'right'});
        $('.limite').editable({type: 'date', name: 'limite', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'left'});
        $('.disponibilidad').editable({type: 'text', name: 'disponibilidad', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'right'});
        $('.disponibilidadpaga').editable({type: 'text', name: 'disponibilidadpaga', success: function (n) {
                if (n) {
                    return 'Error al guardar el dato'
                }
            }, url: '<?php echo $this->webroot; ?>panel/Liquidations/editar', placement: 'right'});
    });
</script>