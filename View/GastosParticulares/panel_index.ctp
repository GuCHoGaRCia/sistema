<div class="gastosParticulares index">
    <h2><?php echo __('Gastos Particulares'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'GastosParticulare')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Cliente - Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('cuentasgastosparticulare_id', __('Cuenta')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('coeficiente_id', __('Coeficiente')); ?></th>
                <th><?php echo $this->Paginator->sort('date', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('description', __('Descripción')); ?></th>
                <th><?php echo $this->Paginator->sort('amount', __('Monto')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($gastosParticulares as $gastosParticulare):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($gastosParticulare['Client']['name'] . " - " . $gastosParticulare['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $gastosParticulare['Consorcio']['id'])); ?></td>
                    <td><?php echo h($gastosParticulare['Liquidation']['name']); ?></td>
                    <td><?php echo h($gastosParticulare['Cuentasgastosparticulare']['name']); ?></td>
                    <td><?php echo $this->Html->link($gastosParticulare['Propietario']['name'], array('controller' => 'Propietarios', 'action' => 'view', $gastosParticulare['Propietario']['id'])); ?></td>
                    <td><?php echo h($gastosParticulare['Coeficiente']['name']); ?></td>
                    <td><span class="date" data-value="<?php echo h($gastosParticulare['GastosParticulare']['date']) ?>" data-pk="<?php echo h($gastosParticulare['GastosParticulare']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $gastosParticulare['GastosParticulare']['date']); ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.date').editable({type: 'date', name: 'date', viewformat: 'dd/mm/yyyy', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>GastosParticulares/editar', placement: 'right'});
                });</script>
            <td><?php echo h(substr(strip_tags($gastosParticulare['GastosParticulare']['description']), 0, 28)) . "..." ?>&nbsp;</td>
            <td><span class="amount" data-value="<?php echo h($gastosParticulare['GastosParticulare']['amount']) ?>" data-pk="<?php echo h($gastosParticulare['GastosParticulare']['id']) ?>"><?php echo h($gastosParticulare['GastosParticulare']['amount']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.amount').editable({type: 'text', name: 'amount', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>GastosParticulares/editar', placement: 'left'});
                });</script>

            <td class="acciones" style="width:100px">
                <?php
                echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $gastosParticulare['GastosParticulare']['id'])));
                echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $gastosParticulare['GastosParticulare']['id'])));
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $gastosParticulare['GastosParticulare']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $gastosParticulare['GastosParticulare']['id']));
                ?>
            </td>
            <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="9"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>