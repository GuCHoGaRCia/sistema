<div class="cuentasgastosparticulares index">
    <h2><?php echo __('Cuentas de Gastos Particulares'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Cuentasgastosparticulare'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($cuentasgastosparticulares as $cuentasgastosparticulare):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($cuentasgastosparticulare['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $cuentasgastosparticulare['Consorcio']['id'])); ?></td>
                    <td><span class="name" data-value="<?php echo h($cuentasgastosparticulare['Cuentasgastosparticulare']['name']) ?>" data-pk="<?php echo h($cuentasgastosparticulare['Cuentasgastosparticulare']['id']) ?>"><?php echo h($cuentasgastosparticulare['Cuentasgastosparticulare']['name']) ?></span>&nbsp;</td>

                    <td class="acciones">
                        <?php
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $cuentasgastosparticulare['Cuentasgastosparticulare']['id'])));
                        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $cuentasgastosparticulare['Cuentasgastosparticulare']['id'])));
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $cuentasgastosparticulare['Cuentasgastosparticulare']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($cuentasgastosparticulare['Cuentasgastosparticulare']['name'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>$(document).ready(function () {
                $('.name').editable({type: 'text', name: 'name', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Cuentasgastosparticulares/editar', placement: 'right'});
            });</script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="3"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>