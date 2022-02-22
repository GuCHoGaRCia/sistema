<div class="bancoscuentas index">
    <h2><?php echo __('Cuentas Bancarias'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Bancoscuenta')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('banco_id', __('Banco')); ?></th>
                <th><?php echo $this->Paginator->sort('cbu', __('CBU')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('saldo', __('Saldo actual')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($bancoscuentas as $bancoscuenta):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($bancoscuenta['Client']['name'] . " - " . $bancoscuenta['Consorcio']['name']); ?></td>
                    <td><?php echo h($bancoscuenta['Banco']['name']); ?></td>
                    <td><span class="cbu" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['cbu']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['cbu']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.cbu').editable({type: 'text', name: 'cbu', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'right'});
        });</script>
            <td><span class="name" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['name']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['name']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'right'});
        });</script>
            <td><span class="saldo"><?php echo h($bancoscuenta['Bancoscuenta']['saldo']) ?></span>&nbsp;</td>
            <td class="acciones">
                <?php
                //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $bancoscuenta['Bancoscuenta']['id'])));
                //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $bancoscuenta['Bancoscuenta']['id'])));
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $bancoscuenta['Bancoscuenta']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $bancoscuenta['Bancoscuenta']['id']));
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
    <?php echo $this->element('pagination'); ?></div>