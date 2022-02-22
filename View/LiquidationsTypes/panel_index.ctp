<div class="liquidationsTypes index">
    <h2><?php echo __('Tipos de liquidaciones'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'LiquidationsType')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('prefijo', __('Prefijo unidad')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($liquidationsTypes as $liquidationsType):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($liquidationsType['Client']['name']) ?></td>
                    <td><span class="name" data-value="<?php echo h($liquidationsType['LiquidationsType']['name']) ?>" data-pk="<?php echo h($liquidationsType['LiquidationsType']['id']) ?>"><?php echo h($liquidationsType['LiquidationsType']['name']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>LiquidationsTypes/editar', placement: 'right'});
                });</script>
            <td class="center"><?php echo h($liquidationsType['LiquidationsType']['prefijo']) ?>&nbsp;</td>
            <td class="center"><?php echo $this->Html->link($this->Html->image(h($liquidationsType['LiquidationsType']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'LiquidationsTypes', 'action' => 'invertir', 'enabled', h($liquidationsType['LiquidationsType']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
            <td class="acciones" style="width:auto">
                <?php
                //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $liquidationsType['LiquidationsType']['id'])));
                //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $liquidationsType['LiquidationsType']['id'])));
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $liquidationsType['LiquidationsType']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $liquidationsType['LiquidationsType']['id']));
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
    <?php echo $this->element('pagination'); ?></div>