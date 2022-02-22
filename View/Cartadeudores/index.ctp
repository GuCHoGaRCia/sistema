<?php echo $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Cartadeudore']); ?>
<h2><?php echo __('Histórico Cartas deudores enviadas por email'); ?></h2>
<div id="seccionaimprimir">
    <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
        <?php echo __('Histórico Cartas deudores enviadas por email'); ?>
    </div>
    <table cellpadding="0" cellspacing="0" style="width:100%">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th style="width:150px;" class="center"><?php echo $this->Paginator->sort('created', __('Fecha')); ?></th>
                <th class="acciones center" style="width:70px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($cartadeudores as $cartadeudore):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?= $class ?>>
                    <td class="borde_tabla"></td>
                    <td><?= h($cartadeudore['Consorcio']['name']) ?></td>
                    <td><?= h($cartadeudore['Propietario']['name'] . ' - ' . $cartadeudore['Propietario']['unidad'] . " (" . $cartadeudore['Propietario']['code'] . ")") ?></td>
                    <td><?= $this->Time->format(__('d/m/Y H:i:s'), $cartadeudore['Cartadeudore']['created']) ?></td>
                    <td class="acciones center" style="width:70px">
                        <?php
                        echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $cartadeudore['Cartadeudore']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
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
    <?php echo $this->element('pagination'); ?>
</div>