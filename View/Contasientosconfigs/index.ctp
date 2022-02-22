<div class="contasientosconfigs index">
    <h2><?php echo __('Contasientosconfigs'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Contasientosconfig']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio Id')); ?></th>
		<th><?php echo $this->Paginator->sort('config', __('Config')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($contasientosconfigs as $contasientosconfig): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($contasientosconfig['Consorcio']['name'], ['controller' => 'consorcios', 'action' => 'view', $contasientosconfig['Consorcio']['id']]); ?></td>
		<td><span class="config" data-value="<?php echo h($contasientosconfig['Contasientosconfig']['config']) ?>" data-pk="<?php echo h($contasientosconfig['Contasientosconfig']['id']) ?>"><?php echo h($contasientosconfig['Contasientosconfig']['config']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $contasientosconfig['Contasientosconfig']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $contasientosconfig['Contasientosconfig']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $contasientosconfig['Contasientosconfig']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $contasientosconfig['Contasientosconfig']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.config').editable({type:'text',name:'config',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contasientosconfigs/editar',placement:'right'});
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