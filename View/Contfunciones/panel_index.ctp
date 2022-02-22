<div class="contfunciones index">
    <h2><?php echo __('Contfunciones'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Contfuncione']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('titulo', __('Titulo')); ?></th>
		<th><?php echo $this->Paginator->sort('descripcion', __('Descripcion')); ?></th>
		<th><?php echo $this->Paginator->sort('modelo', __('Modelo')); ?></th>
		<th><?php echo $this->Paginator->sort('funcion', __('Funcion')); ?></th>
		<th><?php echo $this->Paginator->sort('habilitada', __('Habilitada')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($contfunciones as $contfuncione): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><span class="titulo" data-value="<?php echo h($contfuncione['Contfuncione']['titulo']) ?>" data-pk="<?php echo h($contfuncione['Contfuncione']['id']) ?>"><?php echo h($contfuncione['Contfuncione']['titulo']) ?></span>&nbsp;</td>
		<td><span class="descripcion" data-value="<?php echo h($contfuncione['Contfuncione']['descripcion']) ?>" data-pk="<?php echo h($contfuncione['Contfuncione']['id']) ?>"><?php echo h($contfuncione['Contfuncione']['descripcion']) ?></span>&nbsp;</td>
		<td><span class="modelo" data-value="<?php echo h($contfuncione['Contfuncione']['modelo']) ?>" data-pk="<?php echo h($contfuncione['Contfuncione']['id']) ?>"><?php echo h($contfuncione['Contfuncione']['modelo']) ?></span>&nbsp;</td>
		<td><span class="funcion" data-value="<?php echo h($contfuncione['Contfuncione']['funcion']) ?>" data-pk="<?php echo h($contfuncione['Contfuncione']['id']) ?>"><?php echo h($contfuncione['Contfuncione']['funcion']) ?></span>&nbsp;</td>
		<td><span class="habilitada" data-value="<?php echo h($contfuncione['Contfuncione']['habilitada']) ?>" data-pk="<?php echo h($contfuncione['Contfuncione']['id']) ?>"><?php echo h($contfuncione['Contfuncione']['habilitada']) ?></span>&nbsp;</td>

		<td class="acciones" style="width:auto">
<?php 
        		echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $contfuncione['Contfuncione']['id']]]);
        		echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $contfuncione['Contfuncione']['id']]]);
        		echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $contfuncione['Contfuncione']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $contfuncione['Contfuncione']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
		<script>
$(document).ready(function(){$('.titulo').editable({type:'text',name:'titulo',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contfunciones/editar',placement:'right'});$('.descripcion').editable({type:'text',name:'descripcion',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contfunciones/editar',placement:'right'});$('.modelo').editable({type:'text',name:'modelo',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contfunciones/editar',placement:'right'});$('.funcion').editable({type:'text',name:'funcion',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contfunciones/editar',placement:'right'});$('.habilitada').editable({type:'text',name:'habilitada',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>contfunciones/editar',placement:'right'});
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