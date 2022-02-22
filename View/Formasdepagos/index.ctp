<div class="formasdepagos index">
    <h2><?php echo __('Forma de pago'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => false, 'model' => 'Formasdepago']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('forma', __('Forma de pago')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('destino', __('Destino')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('orden', __('Orden')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitada', __('Habilitada')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($formasdepagos as $formasdepago):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($formasdepago['Formasdepago']['forma']) ?>&nbsp;</td>
                    <td class="center"><?php echo $formasdepago['Formasdepago']['destino'] == 1 ? 'Caja' : 'Banco' ?>&nbsp;</td>
                    <td class="center"><span class="orden" data-value="<?php echo h($formasdepago['Formasdepago']['orden']) ?>" data-pk="<?php echo h($formasdepago['Formasdepago']['id']) ?>"><?php echo h($formasdepago['Formasdepago']['orden']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($formasdepago['Formasdepago']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Habilitado / Deshabilitado'))), array('controller' => 'Formasdepagos', 'action' => 'invertir', 'habilitada', h($formasdepago['Formasdepago']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.orden').editable({type: 'number', name: 'orden', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Formasdepagos/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>