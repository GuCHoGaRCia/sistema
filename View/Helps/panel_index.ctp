<div class="helps index">
    <h2><?php echo __('Ayudas'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Help')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('controller', __('Sección')); ?></th>
                <th><?php echo $this->Paginator->sort('action', __('Acción')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('soloadmin', __('Solo admin')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitada')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($helps as $help):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="controller" data-value="<?php echo h($help['Help']['controller']) ?>" data-pk="<?php echo h($help['Help']['id']) ?>"><?php echo h($help['Help']['controller']) ?></span>&nbsp;</td>
                    <td><span class="action" data-value="<?php echo h($help['Help']['action']) ?>" data-pk="<?php echo h($help['Help']['id']) ?>"><?php echo h($help['Help']['action']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($help['Help']['soloadmin'] ? '1' : '0') . '.png', array('title' => __('Solo admin'))), array('controller' => 'Helps', 'action' => 'invertir', 'soloadmin', h($help['Help']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($help['Help']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Helps', 'action' => 'invertir', 'enabled', h($help['Help']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $help['Help']['id'])));
                        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $help['Help']['id'])));
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $help['Help']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $help['Help']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>$(document).ready(function () {
        $('.controller').editable({type: 'text', name: 'controller', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Helps/editar', placement: 'right'});
        $('.action').editable({type: 'text', name: 'action', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Helps/editar', placement: 'right'});
    });</script>