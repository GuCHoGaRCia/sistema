<div class="administracionefectivosdetalles index">
    <h2><?php echo __('Administracionefectivosdetalles'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Administracionefectivosdetalle']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('administracionefectivo_id', __('Administracionefectivo Id')); ?></th>
		<th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio Id')); ?></th>
		<th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($administracionefectivosdetalles as $administracionefectivosdetalle): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($administracionefectivosdetalle['Administracionefectivo']['id'], ['controller' => 'administracionefectivos', 'action' => 'view', $administracionefectivosdetalle['Administracionefectivo']['id']]); ?></td>
		<td><?php echo $this->Html->link($administracionefectivosdetalle['Consorcio']['name'], ['controller' => 'consorcios', 'action' => 'view', $administracionefectivosdetalle['Consorcio']['id']]); ?></td>
		<td><span class="importe" data-value="<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['importe']) ?>" data-pk="<?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['id']) ?>"><?php echo h($administracionefectivosdetalle['Administracionefectivosdetalle']['importe']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $administracionefectivosdetalle['Administracionefectivosdetalle']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $administracionefectivosdetalle['Administracionefectivosdetalle']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $administracionefectivosdetalle['Administracionefectivosdetalle']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $administracionefectivosdetalle['Administracionefectivosdetalle']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.importe').editable({type:'text',name:'importe',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>administracionefectivosdetalles/editar',placement:'right'});
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