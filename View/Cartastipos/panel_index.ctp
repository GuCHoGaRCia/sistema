<div class="cartastipos index">
    <h2><?php echo __('Tipo de cartas'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Cartastipo')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
		<th><?php echo $this->Paginator->sort('abreviacion', __('Abreviación')); ?></th>
		                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($cartastipos as $cartastipo): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><span class="nombre" data-value="<?php echo h($cartastipo['Cartastipo']['nombre']) ?>" data-pk="<?php echo h($cartastipo['Cartastipo']['id']) ?>"><?php echo h($cartastipo['Cartastipo']['nombre']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.nombre').editable({type:'text',name:'nombre',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>Cartastipos/editar',placement:'right'});});</script>
		<td><span class="abreviacion" data-value="<?php echo h($cartastipo['Cartastipo']['abreviacion']) ?>" data-pk="<?php echo h($cartastipo['Cartastipo']['id']) ?>"><?php echo h($cartastipo['Cartastipo']['abreviacion']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.abreviacion').editable({type:'text',name:'abreviacion',success:function(n){if(n){return n}},url:'<?php echo $this->webroot; ?>Cartastipos/editar',placement:'right'});});</script>

		<td class="acciones" style="width:auto">
<?php 
        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $cartastipo['Cartastipo']['id'])));
        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $cartastipo['Cartastipo']['id'])));
        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $cartastipo['Cartastipo']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $cartastipo['Cartastipo']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>