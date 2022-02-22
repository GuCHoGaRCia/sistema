<div class="bancosextracciones index">
    <h2><?php echo __('Débitos bancarios'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Bancosextraccione', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('cuenta', ['label' => false, 'empty' => '', 'options' => [0 => __('Todas')] + $cuentas, 'type' => 'select', 'selected' => isset($this->request->data['Bancosextraccione']['cuenta']) ? $this->request->data['Bancosextraccione']['cuenta'] : 0]);
    echo $this->Form->input('anulado', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'taction' => 'add2', 'model' => 'Bancosextraccione']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="center"><?php echo __('Conciliado') ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
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
                    <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $bancosextraccione['Bancosextraccione']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $bancosextraccione['Bancosextraccione']['fecha']) ?>&nbsp;</td>
                    <td><?php echo h($bancosextraccione['Bancosextraccione']['concepto']) ?>&nbsp;</td>
                    <td><?php echo $this->Functions->money($bancosextraccione['Bancosextraccione']['importe']) ?>&nbsp;</td>
                    <td class="center"><?php echo $bancosextraccione['Bancosextraccione']['anulado'] ? '' : $this->Html->link($this->Html->image(h($bancosextraccione['Bancosextraccione']['conciliado'] ? '1' : '0') . '.png', ['title' => __('Conciliar')]), ['controller' => 'Bancosextracciones', 'action' => 'invertir', 'conciliado', h($bancosextraccione['Bancosextraccione']['id'])], ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="acciones" style="width:100px">
                        <?php
                        // agrego la lupa para q se pueda ver el detalle del pago asociado a este movimiento
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('controller' => 'Proveedorspagos', 'action' => 'view', $bancosextraccione['Bancosextraccione']['proveedorspago_id'])));
                        if (empty($bancosextraccione['Bancosextraccione']['proveedorspago_id']) && !$bancosextraccione['Bancosextraccione']['anulado'] && $bancosextraccione['Bancosextraccione']['user_id'] == $_SESSION['Auth']['User']['id'] && !$bancosextraccione['Bancosextraccione']['anulado']) {
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
<script>
    $("#BancosextraccioneCuenta").select2({language: "es", allowClear: true, placeholder: '<?= __('Seleccione cuenta...') ?>'});
</script>
<style>
    .checkbox{
        width:150px !important;
    }
</style>