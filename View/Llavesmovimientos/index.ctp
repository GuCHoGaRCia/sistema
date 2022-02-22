<div class="llavesmovimientos index">
    <h2><?php echo __('Llaves Movimientos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Llavesmovimiento']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('numero', __('Llave')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('titulo', __('Título')); ?></th>
                <th><?php echo $this->Paginator->sort('llavesestado_id', __('Estado')); ?></th>
                <th><?php echo $this->Paginator->sort('proveedor_id', __('Proveedor')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($llavesmovimientos as $llavesmovimiento):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h(str_pad($llavesmovimiento['Llave']['numero'], 4, "0", STR_PAD_LEFT)) ?></td>
                    <td><span class="fecha" data-value="<?php echo h($llavesmovimiento['Llavesmovimiento']['fecha']) ?>" data-pk="<?php echo h($llavesmovimiento['Llavesmovimiento']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $llavesmovimiento['Llavesmovimiento']['fecha']) ?></span>&nbsp;</td>
                    <td><span class="titulo" data-value="<?php echo h($llavesmovimiento['Llavesmovimiento']['titulo']) ?>" data-pk="<?php echo h($llavesmovimiento['Llavesmovimiento']['id']) ?>"><?php echo h($llavesmovimiento['Llavesmovimiento']['titulo']) ?></span>&nbsp;</td>
                    <td><?php echo h($llavesmovimiento['Llavesestado']['nombre']) ?></td>
                    <td><?php echo $this->Html->link($llavesmovimiento['Proveedor']['name'], ['controller' => 'proveedors', 'action' => 'view', $llavesmovimiento['Proveedor']['id']]); ?></td>
                    <td><?php echo $this->Html->link($llavesmovimiento['Consorcio']['name'], ['controller' => 'consorcios', 'action' => 'view', $llavesmovimiento['Consorcio']['id']]); ?></td>
                    <td><?php echo $this->Html->link($llavesmovimiento['Propietario']['name'], ['controller' => 'propietarios', 'action' => 'view', $llavesmovimiento['Propietario']['id']]); ?></td>

                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $llavesmovimiento['Llavesmovimiento']['id']]]);
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $llavesmovimiento['Llavesmovimiento']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $llavesmovimiento['Llavesmovimiento']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $llavesmovimiento['Llavesmovimiento']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.fecha').editable({type: 'date', viewformat: 'dd/mm/yyyy', name: 'fecha', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>llavesmovimientos/editar', placement: 'right'});
                $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>llavesmovimientos/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="8"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>