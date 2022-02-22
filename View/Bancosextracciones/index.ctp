<div class="bancosextracciones index">
    <h2><?php echo __('Extracción bancaria'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Bancosextraccione')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('caja_id', __('Caja')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($bancosextracciones as $bancosextraccione):
                $class = $bancosextraccione['Bancosextraccione']['anulado'] ? ' class="error-message tachado"' : null;
                if ($i++ % 2 == 0) {
                    $class = $bancosextraccione['Bancosextraccione']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($bancosextraccione['Bancoscuenta']['name']) ?></td>
                    <td><?php echo h($bancosextraccione['Caja']['name']) ?></td>
                    <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $bancosextraccione['Bancosextraccione']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $bancosextraccione['Bancosextraccione']['fecha']) ?>&nbsp;</td>
                    <td><?php echo h($bancosextraccione['Bancosextraccione']['concepto']) ?>&nbsp;</td>
                    <td><?php echo $this->Functions->money($bancosextraccione['Bancosextraccione']['importe']) ?>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        // agrego la lupa para q se pueda ver el detalle del pago asociado a este movimiento
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('controller' => 'Proveedorspagos', 'action' => 'view', $bancosextraccione['Bancosextraccione']['proveedorspago_id'])));
                        if (empty($bancosextraccione['Bancosextraccione']['proveedorspago_id']) && !$bancosextraccione['Bancosextraccione']['anulado'] && $bancosextraccione['Bancosextraccione']['user_id'] == $_SESSION['Auth']['User']['id']) {
                            echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $bancosextraccione['Bancosextraccione']['id']), array('escapeTitle' => false), __('Desea anular el movimiento # %s?', h($bancosextraccione['Bancosextraccione']['concepto'])));
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