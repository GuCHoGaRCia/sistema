<div class="informepagosadjuntos index">
    <h2><?php echo __('Informepagosadjuntos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Informepagosadjunto']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('informepago_id', __('Informepago Id')); ?></th>
		<th><?php echo $this->Paginator->sort('ruta', __('Ruta')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($informepagosadjuntos as $informepagosadjunto): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($informepagosadjunto['Informepago']['id'], ['controller' => 'Informepagos', 'action' => 'view', $informepagosadjunto['Informepago']['id']]); ?></td>
		<td><span class="ruta" data-value="<?php echo h($informepagosadjunto['Informepagosadjunto']['ruta']) ?>" data-pk="<?php echo h($informepagosadjunto['Informepagosadjunto']['id']) ?>"><?php echo h($informepagosadjunto['Informepagosadjunto']['ruta']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $informepagosadjunto['Informepagosadjunto']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $informepagosadjunto['Informepagosadjunto']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $informepagosadjunto['Informepagosadjunto']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $informepagosadjunto['Informepagosadjunto']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.ruta').editable({type:'text',name:'ruta',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>Informepagosadjuntos/editar',placement:'right'});
});
</script>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>