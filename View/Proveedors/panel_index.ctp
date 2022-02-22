<div class="proveedors index">
    <h2><?php echo __('Proveedores'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Proveedor')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('cuit', __('CUIT')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('telephone', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($proveedors as $proveedor):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($proveedor['Client']['name']) ?></td>
                    <td><span class="name" data-value="<?php echo h($proveedor['Proveedor']['name']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['name']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>
            <td><span class="address" data-value="<?php echo h($proveedor['Proveedor']['address']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['address']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.address').editable({type: 'text', name: 'address', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>
            <td><span class="cuit" data-value="<?php echo h($proveedor['Proveedor']['cuit']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['cuit']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.cuit').editable({type: 'text', name: 'cuit', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>
            <td><span class="city" data-value="<?php echo h($proveedor['Proveedor']['city']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['city']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.city').editable({type: 'text', name: 'city', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>
            <td><span class="telephone" data-value="<?php echo h($proveedor['Proveedor']['telephone']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['telephone']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.telephone').editable({type: 'text', name: 'telephone', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>
            <td><span class="email" data-value="<?php echo h($proveedor['Proveedor']['email']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['email']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
            $('.email').editable({type: 'text', name: 'email', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        });</script>

            <td class="acciones" style="width:auto">
                <?php
                //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $proveedor['Proveedor']['id'])));
                //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $proveedor['Proveedor']['id'])));
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $proveedor['Proveedor']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $proveedor['Proveedor']['id']));
                ?>
            </td>
            <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="8"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>