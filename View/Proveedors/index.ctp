<div class="proveedors index">
    <h2><?php echo __('Proveedores'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Proveedor')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Razón Social')); ?></th>
                <th><?php echo $this->Paginator->sort('nombrefantasia', __('Nombre Fantasía')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('cuit', __('CUIT')); ?></th>
                <th><?php echo $this->Paginator->sort('matricula', __('Matrícula')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('telephone', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('saldo', __('Saldo')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($proveedors as $proveedor):
                //$class = $proveedor['Proveedor']['saldo'] > 0 ? ' class="error-message"' : ' class="success-message"';
                $class = '';
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($proveedor['Proveedor']['name']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['name']) ?></span>&nbsp;</td>
                    <td><span class="nombrefantasia" data-value="<?php echo h($proveedor['Proveedor']['nombrefantasia']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['nombrefantasia']) ?></span>&nbsp;</td>
                    <td><span class="address" data-value="<?php echo h($proveedor['Proveedor']['address']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['address']) ?></span>&nbsp;</td>
                    <td><span class="cuit" data-value="<?php echo h($proveedor['Proveedor']['cuit']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['cuit']) ?></span>&nbsp;</td>
                    <td><span class="matricula" data-value="<?php echo h($proveedor['Proveedor']['matricula']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['matricula']) ?></span>&nbsp;</td>
                    <td><span class="city" data-value="<?php echo h($proveedor['Proveedor']['city']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['city']) ?></span>&nbsp;</td>
                    <td><span class="telephone" data-value="<?php echo h($proveedor['Proveedor']['telephone']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['telephone']) ?></span>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($proveedor['Proveedor']['email']) ?>" data-pk="<?php echo h($proveedor['Proveedor']['id']) ?>"><?php echo h($proveedor['Proveedor']['email']) ?></span>&nbsp;</td>
                    <td <?= $proveedor['Proveedor']['saldo'] > 0 ? ' class="error-message"' : ' class="success-message"' ?>><?php echo h($proveedor['Proveedor']['saldo']) ?>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Proveedors/view/<?= $proveedor['Proveedor']['id'] ?>">Ver Movimientos</a>
                                    </li>
                                </ul>
                            </span>
                        </span>
                        <a href="<?php echo $this->webroot; ?>Proveedorspagos/add/<?= $proveedor['Proveedor']['id'] ?>" title="Pagar a Proveedor"><img src="<?= $this->webroot ?>img/liquidation.png" ></a>
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('title' => __('Eliminar'))), array('action' => 'delete', $proveedor['Proveedor']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $proveedor['Proveedor']['name']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="10"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.nombrefantasia').editable({type: 'text', name: 'nombrefantasia', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.address').editable({type: 'text', name: 'address', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.cuit').editable({type: 'text', name: 'cuit', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.matricula').editable({type: 'text', name: 'matricula', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.city').editable({type: 'text', name: 'city', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'right'});
        $('.telephone').editable({type: 'text', name: 'telephone', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'left'});
        $('.email').editable({type: 'text', name: 'email', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedors/editar', placement: 'left'});
    });
</script>