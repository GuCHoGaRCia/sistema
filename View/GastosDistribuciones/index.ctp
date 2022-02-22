<div class="gastosDistribuciones index">
    <h2><?php echo __('Gastos Distribuciones'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'GastosDistribucione'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($gastosDistribuciones as $gastosDistribucione):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($gastosDistribucione['Consorcio']['name'], ['controller' => 'Consorcios', 'action' => 'view', $gastosDistribucione['Consorcio']['id']]); ?></td>
                    <td><span class="nombre" data-value="<?php echo h($gastosDistribucione['GastosDistribucione']['nombre']) ?>" data-pk="<?php echo h($gastosDistribucione['GastosDistribucione']['id']) ?>"><?php echo h($gastosDistribucione['GastosDistribucione']['nombre']) ?></span>&nbsp;</td>
                    <td class="acciones">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $gastosDistribucione['GastosDistribucione']['id']]]);
                        //echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $gastosDistribucione['GastosDistribucione']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $gastosDistribucione['GastosDistribucione']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', h($gastosDistribucione['GastosDistribucione']['nombre'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.nombre').editable({type: 'text', name: 'nombre', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>GastosDistribuciones/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="3"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>