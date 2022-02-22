<div class="llavesestados index">
    <h2><?php echo __('Llaves Estados'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => false, 'pagenew' => false, 'model' => 'Llavesestado']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($llavesestados as $llavesestado):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($llavesestado['Llavesestado']['nombre']) ?>&nbsp;</td>
                    <td class="acciones" style="width:100px">
                        <?php
                        //echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $llavesestado['Llavesestado']['id']]]);
                        //echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $llavesestado['Llavesestado']['id']]]);
                        //echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $llavesestado['Llavesestado']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $llavesestado['Llavesestado']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="2"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>