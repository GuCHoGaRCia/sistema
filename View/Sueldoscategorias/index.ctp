
}{njh
+}{´pol
+¿'098<div class="sueldoscategorias index">
    <h2><?php echo __('Categorías de empleados'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Sueldoscategoria')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('codigo', __('Código')); ?></th>
		<th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
		<th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
		                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($sueldoscategorias as $sueldoscategoria): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><span class="codigo" data-value="<?php echo h($sueldoscategoria['Sueldoscategoria']['codigo']) ?>" data-pk="<?php echo h($sueldoscategoria['Sueldoscategoria']['id']) ?>"><?php echo h($sueldoscategoria['Sueldoscategoria']['codigo']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.codigo').editable({type:'text',name:'codigo',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldoscategorias/editar',placement:'right'});});</script>
		<td><span class="nombre" data-value="<?php echo h($sueldoscategoria['Sueldoscategoria']['nombre']) ?>" data-pk="<?php echo h($sueldoscategoria['Sueldoscategoria']['id']) ?>"><?php echo h($sueldoscategoria['Sueldoscategoria']['nombre']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.nombre').editable({type:'text',name:'nombre',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldoscategorias/editar',placement:'right'});});</script>
		<td><span class="importe" data-value="<?php echo h($sueldoscategoria['Sueldoscategoria']['importe']) ?>" data-pk="<?php echo h($sueldoscategoria['Sueldoscategoria']['id']) ?>"><?php echo h($sueldoscategoria['Sueldoscategoria']['importe']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.importe').editable({type:'text',name:'importe',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldoscategorias/editar',placement:'right'});});</script>

		<td class="acciones">
<?php 
        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $sueldoscategoria['Sueldoscategoria']['id'])));
        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $sueldoscategoria['Sueldoscategoria']['id'])));
        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $sueldoscategoria['Sueldoscategoria']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $sueldoscategoria['Sueldoscategoria']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="4"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>