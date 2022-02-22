<div class="cartasprecios index">
    <h2><?php echo __('Cartas precios'); ?>&nbsp;<a href="<?=$this->webroot?>panel/Cartasprecios/generar">Generar precios</a></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'pagesearch' => true, 'filter' => ['enabled' => true, 'panel' => true, 'options' => $clients, 'field' => 'cliente'], 'pagenew' => true, 'model' => 'Cartasprecio'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('cartastipo_id', __('Tipo de carta')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
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
                    <td><?php echo $this->Html->link($cartasprecio['Client']['name'], array('controller' => 'Clients', 'action' => 'view', $cartasprecio['Client']['id'])); ?></td>
                    <td><?php echo h($cartasprecio['Cartastipo']['nombre']); ?></td>
                    <td><span class="importe" data-value="<?php echo h($cartasprecio['Cartasprecio']['importe']) ?>" data-pk="<?php echo h($cartasprecio['Cartasprecio']['id']) ?>"><?php echo h($cartasprecio['Cartasprecio']['importe']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $cartasprecio['Cartasprecio']['id'])));
                        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $cartasprecio['Cartasprecio']['id'])));
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $cartasprecio['Cartasprecio']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $cartasprecio['Cartasprecio']['id']));
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
    <?php echo $this->element('pagination'); ?>
    <script>$(document).ready(function () {
            $('.importe').editable({type: 'text', name: 'importe', success: function (n) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Cartasprecios/editar', placement: 'left'});
        });</script>
</div>