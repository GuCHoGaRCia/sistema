<div class="administraciontransferenciasdetalles index">
    <h2><?php echo __('Administraciontransferenciasdetalles'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Administraciontransferenciasdetalle']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('administraciontransferencia_id', __('Administraciontransferencia Id')); ?></th>
		<th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Bancoscuenta Id')); ?></th>
		<th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($administraciontransferenciasdetalles as $administraciontransferenciasdetalle): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($administraciontransferenciasdetalle['Administraciontransferencia']['id'], ['controller' => 'administraciontransferencias', 'action' => 'view', $administraciontransferenciasdetalle['Administraciontransferencia']['id']]); ?></td>
		<td><?php echo $this->Html->link($administraciontransferenciasdetalle['Bancoscuenta']['name'], ['controller' => 'bancoscuentas', 'action' => 'view', $administraciontransferenciasdetalle['Bancoscuenta']['id']]); ?></td>
		<td><span class="importe" data-value="<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['importe']) ?>" data-pk="<?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']) ?>"><?php echo h($administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['importe']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $administraciontransferenciasdetalle['Administraciontransferenciasdetalle']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.importe').editable({type:'text',name:'importe',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>administraciontransferenciasdetalles/editar',placement:'right'});
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