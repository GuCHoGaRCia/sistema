<div class="adjuntos index">
    <h2><?php echo __('Adjuntos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $liquidations, 'field' => 'liquidacion'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Adjunto']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('titulo', __('Título')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprimir', __('Imprimir')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('online', __('Online')); ?></th>
                <th><?php echo $this->Paginator->sort('ruta', __('Adjunto')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($adjuntos as $adjunto):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($adjunto['Liquidation']['name2']); ?></td>
                    <?php
                    if (!$adjunto['Liquidation']['bloqueada']) {
                        ?>
                        <td><span class="titulo" data-value="<?php echo h($adjunto['Adjunto']['titulo']) ?>" data-pk="<?php echo h($adjunto['Adjunto']['id']) ?>"><?php echo h($adjunto['Adjunto']['titulo']) ?></span>&nbsp;</td>
                        <?php
                    } else {
                        ?>
                        <td><?php echo h($adjunto['Adjunto']['titulo']); ?></td>
                        <?php
                    }
                    ?>
                    <td class="center"><?php echo $this->Html->image(($adjunto['Adjunto']['imprimir'] ? '1' : '0') . '.png', array('title' => __('Imprimir'))); ?> </td>
                    <td class="center"><?php echo $this->Html->image(($adjunto['Adjunto']['poneronline'] ? '1' : '0') . '.png', array('title' => __('Online'))); ?> </td>
                    <td><span><?php echo $this->Html->link('Descargar', array('controller' => 'Adjuntos', 'action' => 'download', $this->Functions->_encryptURL($adjunto['Adjunto']['ruta']), 1, 0, $_SESSION['Auth']['User']['client_id'])); ?></span></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        if (!$adjunto['Liquidation']['bloqueada']) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $adjunto['Adjunto']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', h($adjunto['Adjunto']['titulo'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="6"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Adjuntos/editar', placement: 'right'});
        $("#filterLiquidacion").select2({language: "es", placeholder: "<?= __("Seleccione liquidación...") ?>", allowClear: true});
    });
</script>