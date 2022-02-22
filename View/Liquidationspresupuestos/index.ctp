<div class="liquidationspresupuestos index">
    <h2><?php echo __('Presupuestos'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => false, 'model' => 'Liquidationspresupuesto'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('coeficiente_id', __('Coeficiente')); ?></th>
                <th><?php echo $this->Paginator->sort('total', __('Total')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($liquidationspresupuestos as $liquidationspresupuesto):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($liquidationspresupuesto['c2']['name']); ?></td>
                    <td><?php echo h($liquidationspresupuesto['Liquidation']['periodo']); ?></td>
                    <td><?php echo h($liquidationspresupuesto['Coeficiente']['name']); ?></td>
                    <td><span class="total" data-value="<?php echo h($liquidationspresupuesto['Liquidationspresupuesto']['total']) ?>" data-pk="<?php echo h($liquidationspresupuesto['Liquidationspresupuesto']['id']) ?>"><?php echo h($liquidationspresupuesto['Liquidationspresupuesto']['total']) ?></span>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="4"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <script>
        $(document).ready(function () {
            $('.total').editable({type: 'text', name: 'total', success: function (n) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Liquidationspresupuestos/editar', placement: 'left'});
        });
    </script>
    <?php echo $this->element('pagination'); ?></div>