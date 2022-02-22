<div class="sueldosempleados index">
    <h2><?php echo __('Sueldosempleados'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Sueldosempleado')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio Id')); ?></th>
		<th><?php echo $this->Paginator->sort('sueldoscategoria_id', __('Sueldoscategoria Id')); ?></th>
		<th><?php echo $this->Paginator->sort('sueldosobrassociale_id', __('Sueldosobrassociale Id')); ?></th>
		<th><?php echo $this->Paginator->sort('legajo', __('Legajo')); ?></th>
		<th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
		<th><?php echo $this->Paginator->sort('dni', __('Dni')); ?></th>
		<th><?php echo $this->Paginator->sort('cuil', __('Cuil')); ?></th>
		<th><?php echo $this->Paginator->sort('hijos', __('Hijos')); ?></th>
		<th><?php echo $this->Paginator->sort('ingreso', __('Ingreso')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($sueldosempleados as $sueldosempleado): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($sueldosempleado['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $sueldosempleado['Consorcio']['id'])); ?></td>
		<td><?php echo $this->Html->link($sueldosempleado['Sueldoscategoria']['id'], array('controller' => 'sueldoscategorias', 'action' => 'view', $sueldosempleado['Sueldoscategoria']['id'])); ?></td>
		<td><?php echo $this->Html->link($sueldosempleado['Sueldosobrassociale']['id'], array('controller' => 'sueldosobrassociales', 'action' => 'view', $sueldosempleado['Sueldosobrassociale']['id'])); ?></td>
		<td><span class="legajo" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['legajo']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['legajo']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.legajo').editable({type:'text',name:'legajo',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>
		<td><span class="nombre" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['nombre']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['nombre']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.nombre').editable({type:'text',name:'nombre',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>
		<td><span class="dni" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['dni']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['dni']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.dni').editable({type:'text',name:'dni',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>
		<td><span class="cuil" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['cuil']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['cuil']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.cuil').editable({type:'text',name:'cuil',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>
		<td><span class="hijos" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['hijos']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['hijos']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.hijos').editable({type:'text',name:'hijos',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>
		<td><span class="ingreso" data-value="<?php echo h($sueldosempleado['Sueldosempleado']['ingreso']) ?>" data-pk="<?php echo h($sueldosempleado['Sueldosempleado']['id']) ?>"><?php echo h($sueldosempleado['Sueldosempleado']['ingreso']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.ingreso').editable({type:'text',name:'ingreso',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>sueldosempleados/editar',placement:'right'});});</script>

		<td class="acciones" style="width:auto">
<?php 
        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $sueldosempleado['Sueldosempleado']['id'])));
        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $sueldosempleado['Sueldosempleado']['id'])));
        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $sueldosempleado['Sueldosempleado']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $sueldosempleado['Sueldosempleado']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="10"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>