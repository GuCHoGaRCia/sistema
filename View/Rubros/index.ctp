<div class="rubros index">
    <h2><?php echo __('Rubros'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Rubro'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('orden', __('Orden')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitado', __('Habilitado')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($rubros as $rubro):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($rubro['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $rubro['Consorcio']['id'])); ?></td>
                    <td><span class="name" data-value="<?php echo h($rubro['Rubro']['name']) ?>" data-pk="<?php echo h($rubro['Rubro']['id']) ?>"><?php echo h($rubro['Rubro']['name']) ?></span>&nbsp;</td>
                    <td class="center"><span class="orden" data-value="<?php echo h($rubro['Rubro']['orden']) ?>" data-pk="<?php echo h($rubro['Rubro']['id']) ?>"><?php echo h($rubro['Rubro']['orden']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($rubro['Rubro']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Habilitado / Deshabilitado'))), array('controller' => 'Rubros', 'action' => 'invertir', 'habilitado', h($rubro['Rubro']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $rubro['Rubro']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($rubro['Rubro']['name'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Rubros/editar', placement: 'right'});
                $('.orden').editable({type: 'text', name: 'orden', success: function (n, r) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Rubros/editar', placement: 'left'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="5"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>