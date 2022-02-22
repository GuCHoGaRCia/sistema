<div class="notas index">
    <h2><?php echo __('Notas'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Nota')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Cliente - Consorcio')); ?></th>
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
					<td><?php echo h($nota['Client']['name'] . " - " . $nota['Consorcio']['name']); ?></td>
                    <td><?php echo h($nota['Liquidation']['name']); ?></td>
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