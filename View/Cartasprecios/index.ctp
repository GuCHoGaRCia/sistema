<div class="cartasprecios index">
    <h2><?php echo __('Cartas precios'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => false, 'pagenew' => false, 'model' => 'Cartasprecio')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
		<th><?php echo $this->Paginator->sort('cartastipo_id', __('Tipo de carta')); ?></th>
		<th><?php echo $this->Paginator->sort('importe', __('Precio')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 0;
            foreach ($cartasprecios as $cartasprecio): 
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }            
                ?>
		<tr<?php echo $class; ?>>
			<td class="borde_tabla"></td>
		<td><?php echo h($cartasprecio['Cartastipo']['nombre']) ?>&nbsp;</td>
		<td><?php echo h($cartasprecio['Cartasprecio']['importe']) ?>&nbsp;</td>

		<?php /*<td class="acciones" style="width:auto">
<?php 
        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $cartasprecio['Cartasprecio']['id'])));
        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $cartasprecio['Cartasprecio']['id'])));
        //echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $cartasprecio['Cartasprecio']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $cartasprecio['Cartasprecio']['id']));
        ?>
		</td>
		*/?>
		<td class="borde_tabla"></td>
	</tr>
	<?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="2"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>