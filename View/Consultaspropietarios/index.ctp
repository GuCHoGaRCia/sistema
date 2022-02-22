<div class="consultaspropietarios index">
    <h2><?php echo __('Consultas propietarios'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Consultaspropietario']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('mensaje', __('Consulta')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Última')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('seen', __('Vista')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($consultaspropietarios as $consultaspropietario):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($consultaspropietario['Consorcio']['name'] . " - " . $consultaspropietario['Propietario']['name'] . " - " . $consultaspropietario['Propietario']['unidad'] . " (" . $consultaspropietario['Propietario']['code'] . ")"); ?></td>
                    <td><?php echo h(substr($consultaspropietario['Consultaspropietario']['mensaje'], 0, 50)) ?>...&nbsp;</td>
                    <td><?php echo $this->Time->timeAgoInWords($consultaspropietario['Consultaspropietario']['created']) ?>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(($consultaspropietario['Consultaspropietario']['seen'] ? '1' : '0') . '.png', array('title' => __('Ya fue visualizada la consulta?'))); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $consultaspropietario['Consultaspropietario']['id']]]);
                        //echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $consultaspropietario['Consultaspropietario']['id']]]);
                        //echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $consultaspropietario['Consultaspropietario']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $consultaspropietario['Consultaspropietario']['id']));
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