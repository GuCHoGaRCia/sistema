<div class="resumenes index">
    <h2><?php echo __('Resumenes'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Resumene')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                		<th><?php echo $this->Paginator->sort('liquidation_id',__('Liquidation Id')); ?></th>
		<th><?php echo $this->Paginator->sort('data',__('Data')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($resumenes as $resumene): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo $this->Html->link($resumene['Liquidation']['name'], array('controller' => 'Liquidations', 'action' => 'view', $resumene['Liquidation']['id'])); ?></td>
		<td><span class="data" data-value="<?php echo h($resumene['Resumene']['data']) ?>" data-pk="<?php echo h($resumene['Resumene']['id']) ?>"><?php echo h($resumene['Resumene']['data']) ?></span>&nbsp;</td>
<script>$(document).ready(function(){$('.data').editable({type:'text',name:'data',success:function(n,r){if(n){return n}},url:'<?php echo $this->webroot; ?>Resumenes/editar',placement:'right'});});</script>

		<td class="acciones">
<?php 
        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $resumene['Resumene']['id'])));
        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $resumene['Resumene']['id'])));
        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $resumene['Resumene']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $resumene['Resumene']['id']));
        ?>
		</td>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>