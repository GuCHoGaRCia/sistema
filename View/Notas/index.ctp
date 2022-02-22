<div class="notas index">
    <h2><?php echo __('Notas'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => false, 'model' => 'Nota')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($notas as $nota):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($nota['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $nota['Consorcio']['id'])); ?></td>
                    <td><?php echo h($nota['Liquidation']['periodo']); ?></td>
                    <td class="acciones" style="width:auto">&nbsp;&nbsp;
                        <?php
                        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $nota['Nota']['id'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>