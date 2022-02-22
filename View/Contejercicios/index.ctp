<div class="contejercicios index">
    <h2><?php echo __('Ejercicios Contables'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Contejercicio']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('inicio', __('Inicio')); ?></th>
                <th><?php echo $this->Paginator->sort('fin', __('Fin')); ?></th>
                <th class="acciones" style="width:120px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($contejercicios as $contejercicio):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($contejercicio['Consorcio']['name'], ['controller' => 'consorcios', 'action' => 'view', $contejercicio['Consorcio']['id']]); ?></td>
                    <td><?php echo h($contejercicio['Contejercicio']['nombre']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $contejercicio['Contejercicio']['inicio']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $contejercicio['Contejercicio']['fin']) ?>&nbsp;</td>
                    <td class="acciones" style="width:120px">
                        <?php
                        //echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $contejercicio['Contejercicio']['id']]]);
                        //echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $contejercicio['Contejercicio']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $contejercicio['Contejercicio']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $contejercicio['Contejercicio']['id']));
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