<div class="adjuntos index">
    <h2><?php echo __('Adjuntos'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Adjunto')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Cliente - Consorcio - Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('titulo', __('Título')); ?></th>
                <th><?php echo $this->Paginator->sort('ruta', __('Adjunto')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
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
                    <td><?php echo h($adjunto['Client']['name'] . " - " . $adjunto['Consorcio']['name'] . " - " . $adjunto['Liquidation']['name']); ?></td>
                    <td><span class="titulo" data-value="<?php echo h($adjunto['Adjunto']['titulo']) ?>" data-pk="<?php echo h($adjunto['Adjunto']['id']) ?>"><?php echo h($adjunto['Adjunto']['titulo']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>Adjuntos/editar', placement: 'right'});
                });</script>
            <td><span><?php echo $this->Html->link('Descargar', array('controller' => 'Adjuntos', 'action' => 'download', $adjunto['Adjunto']['ruta'], 0, $_SESSION['Auth']['User']['client_id'])); ?></span></td>
            <td class="acciones">
                <?php
                if ($adjunto['Liquidation']['cerrada'] == 0) {
                    echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $adjunto['Adjunto']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $adjunto['Adjunto']['id']));
                }
                ?>
            </td>
            <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>