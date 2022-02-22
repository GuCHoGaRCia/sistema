<div class="bancos index">
    <h2><?php echo __('Bancos'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Banco')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Sucursal')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($bancos as $banco):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($banco['Banco']['name']) ?>" data-pk="<?php echo h($banco['Banco']['id']) ?>"><?php echo h($banco['Banco']['name']) ?></span>&nbsp;</td>
                    <td><span class="address" data-value="<?php echo h($banco['Banco']['address']) ?>" data-pk="<?php echo h($banco['Banco']['id']) ?>"><?php echo h($banco['Banco']['address']) ?></span>&nbsp;</td>
                    <td><span class="city" data-value="<?php echo h($banco['Banco']['city']) ?>" data-pk="<?php echo h($banco['Banco']['id']) ?>"><?php echo h($banco['Banco']['city']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $banco['Banco']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $banco['Banco']['id']));
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
<script>
    $(document).ready(function () {
        'use strict';
        $('.city').editable({type: 'text', name: 'city', url: '<?php echo $this->webroot; ?>Bancos/editar', placement: 'right'});
        /*$('.name').editable({type: 'text', name: 'name', success: function (n, r) {
         if (n) {
         //return n
         }
         }, url: '<?php echo $this->webroot; ?>Bancos/editar', placement: 'right'});
         $('.address').editable({type: 'text', name: 'address', success: function (n, r) {
         if (n) {
         //return n
         }
         }, url: '<?php echo $this->webroot; ?>Bancos/editar', placement: 'right'});*/
    });
</script>