<div class="bancostransferencias index">
    <h2><?php echo __('Transferencias Bancarias'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Bancostransferencia')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta Bancaria Orígen')); ?></th>
                <th><?php echo $this->Paginator->sort('destino_id', __('Cuenta Bancaria Destino')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $consorcios = array_keys($consorcios);
            foreach ($bancostransferencias as $bancostransferencia):
                $class = null;
                if (!in_array($bancostransferencia['Destino']['consorcio_id'], $consorcios)) {
                    continue; // para q no muestre los deshabilitados
                }
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($bancostransferencia['Bancoscuenta']['name'], array('controller' => 'Bancoscuentas', 'action' => 'view', $bancostransferencia['Bancoscuenta']['id'])); ?></td>
                    <td><?php echo $this->Html->link($bancostransferencia['Destino']['name'], array('controller' => 'Bancoscuentas', 'action' => 'view', $bancostransferencia['Destino']['id'])); ?></td>
                    <td><span class="fecha" data-value="<?php echo h($bancostransferencia['Bancostransferencia']['fecha']) ?>" data-pk="<?php echo h($bancostransferencia['Bancostransferencia']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $bancostransferencia['Bancostransferencia']['fecha']) ?></span>&nbsp;</td>
                    <td><?php echo h($bancostransferencia['Bancostransferencia']['concepto']) ?>&nbsp;</td>
            <script>
                $(document).ready(function () {
                    $('.concepto').editable({type: 'text', name: 'concepto', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>Bancostransferencias/editar', placement: 'right'});
                });
            </script>
            <td><span class="importe" data-value="<?php echo h($bancostransferencia['Bancostransferencia']['importe']) ?>" data-pk="<?php echo h($bancostransferencia['Bancostransferencia']['id']) ?>"><?php echo h($bancostransferencia['Bancostransferencia']['importe']) ?></span>&nbsp;</td>
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