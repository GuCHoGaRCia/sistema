<div class="colaimpresionesdetalles index">
    <h2><?php echo __('Colaimpresionesdetalles'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Colaimpresionesdetalle']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('colaimpresione_id', __('Colaimpresione Id')); ?></th>
		<th><?php echo $this->Paginator->sort('reporte', __('Reporte')); ?></th>
		<th><?php echo $this->Paginator->sort('impreso', __('Impreso')); ?></th>
		<th><?php echo $this->Paginator->sort('online', __('Online')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($colaimpresionesdetalles as $colaimpresionesdetalle): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($colaimpresionesdetalle['Colaimpresione']['id'], ['controller' => 'colaimpresiones', 'action' => 'view', $colaimpresionesdetalle['Colaimpresione']['id']]); ?></td>
		<td><span class="reporte" data-value="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['reporte']) ?>" data-pk="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['id']) ?>"><?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['reporte']) ?></span>&nbsp;</td>
		<td><span class="impreso" data-value="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['impreso']) ?>" data-pk="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['id']) ?>"><?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['impreso']) ?></span>&nbsp;</td>
		<td><span class="online" data-value="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['online']) ?>" data-pk="<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['id']) ?>"><?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['online']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $colaimpresionesdetalle['Colaimpresionesdetalle']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $colaimpresionesdetalle['Colaimpresionesdetalle']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $colaimpresionesdetalle['Colaimpresionesdetalle']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $colaimpresionesdetalle['Colaimpresionesdetalle']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.reporte').editable({type:'text',name:'reporte',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>colaimpresionesdetalles/editar',placement:'right'});$('.impreso').editable({type:'text',name:'impreso',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>colaimpresionesdetalles/editar',placement:'right'});$('.online').editable({type:'text',name:'online',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>colaimpresionesdetalles/editar',placement:'right'});
});
</script>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>