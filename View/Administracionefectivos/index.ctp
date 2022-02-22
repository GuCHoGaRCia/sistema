<div class="administracionefectivos index">
    <h2><?php echo __('Administracionefectivos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Administracionefectivo']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('proveedorspago_id', __('Proveedorspago Id')); ?></th>
		<th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Bancoscuenta Id')); ?></th>
		<th><?php echo $this->Paginator->sort('anulado', __('Anulado')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($administracionefectivos as $administracionefectivo): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($administracionefectivo['Proveedorspago']['id'], ['controller' => 'proveedorspagos', 'action' => 'view', $administracionefectivo['Proveedorspago']['id']]); ?></td>
		<td><?php echo $this->Html->link($administracionefectivo['Bancoscuenta']['name'], ['controller' => 'bancoscuentas', 'action' => 'view', $administracionefectivo['Bancoscuenta']['id']]); ?></td>
		<td><span class="anulado" data-value="<?php echo h($administracionefectivo['Administracionefectivo']['anulado']) ?>" data-pk="<?php echo h($administracionefectivo['Administracionefectivo']['id']) ?>"><?php echo h($administracionefectivo['Administracionefectivo']['anulado']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $administracionefectivo['Administracionefectivo']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $administracionefectivo['Administracionefectivo']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $administracionefectivo['Administracionefectivo']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $administracionefectivo['Administracionefectivo']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.anulado').editable({type:'text',name:'anulado',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>administracionefectivos/editar',placement:'right'});
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