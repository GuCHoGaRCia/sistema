<div class="amenitiesreservas index">
    <h2><?php echo __('Amenitiesreservas'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Amenitiesreserva']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('amenitie_id', __('Amenitie Id')); ?></th>
		<th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
		<th><?php echo $this->Paginator->sort('amenitiesturno_id', __('Amenitiesturno Id')); ?></th>
		<th><?php echo $this->Paginator->sort('propietario_id', __('Propietario Id')); ?></th>
		<th><?php echo $this->Paginator->sort('cancelado', __('Cancelado')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($amenitiesreservas as $amenitiesreserva): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($amenitiesreserva['Amenitie']['id'], ['controller' => 'amenities', 'action' => 'view', $amenitiesreserva['Amenitie']['id']]); ?></td>
		<td><span class="fecha" data-value="<?php echo h($amenitiesreserva['Amenitiesreserva']['fecha']) ?>" data-pk="<?php echo h($amenitiesreserva['Amenitiesreserva']['id']) ?>"><?php echo h($amenitiesreserva['Amenitiesreserva']['fecha']) ?></span>&nbsp;</td>
		<td><?php echo $this->Html->link($amenitiesreserva['Amenitiesturno']['id'], ['controller' => 'amenitiesturnos', 'action' => 'view', $amenitiesreserva['Amenitiesturno']['id']]); ?></td>
		<td><?php echo $this->Html->link($amenitiesreserva['Propietario']['name'], ['controller' => 'propietarios', 'action' => 'view', $amenitiesreserva['Propietario']['id']]); ?></td>
		<td><span class="cancelado" data-value="<?php echo h($amenitiesreserva['Amenitiesreserva']['cancelado']) ?>" data-pk="<?php echo h($amenitiesreserva['Amenitiesreserva']['id']) ?>"><?php echo h($amenitiesreserva['Amenitiesreserva']['cancelado']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $amenitiesreserva['Amenitiesreserva']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $amenitiesreserva['Amenitiesreserva']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $amenitiesreserva['Amenitiesreserva']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $amenitiesreserva['Amenitiesreserva']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.fecha').editable({type:'text',name:'fecha',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>amenitiesreservas/editar',placement:'right'});$('.cancelado').editable({type:'text',name:'cancelado',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>amenitiesreservas/editar',placement:'left'});
});
</script>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="6"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>