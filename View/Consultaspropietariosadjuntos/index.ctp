<div class="consultaspropietariosadjuntos index">
    <h2><?php echo __('Consultaspropietariosadjuntos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Consultaspropietariosadjunto']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Client Id')); ?></th>
		<th><?php echo $this->Paginator->sort('propietario_id', __('Propietario Id')); ?></th>
		<th><?php echo $this->Paginator->sort('ruta', __('Ruta')); ?></th>
		                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($consultaspropietariosadjuntos as $consultaspropietariosadjunto): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($consultaspropietariosadjunto['Client']['name'], ['controller' => 'Clients', 'action' => 'view', $consultaspropietariosadjunto['Client']['id']]); ?></td>
		<td><?php echo $this->Html->link($consultaspropietariosadjunto['Propietario']['name'], ['controller' => 'Propietarios', 'action' => 'view', $consultaspropietariosadjunto['Propietario']['id']]); ?></td>
		<td><span class="ruta" data-value="<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['ruta']) ?>" data-pk="<?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']) ?>"><?php echo h($consultaspropietariosadjunto['Consultaspropietariosadjunto']['ruta']) ?></span>&nbsp;</td>

		<td class="acciones">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $consultaspropietariosadjunto['Consultaspropietariosadjunto']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.ruta').editable({type:'text',name:'ruta',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>Consultaspropietariosadjuntos/editar',placement:'right'});
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