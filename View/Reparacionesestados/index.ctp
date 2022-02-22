<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="reparacionesestados index">
    <h2><?php echo __('Reparacionesestados'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Reparacionesestado']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($reparacionesestados as $reparacionesestado):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span style="font-weight:bold;color:<?= h($reparacionesestado['Reparacionesestado']['color']) ?>" class="nombre" data-value="<?php echo h($reparacionesestado['Reparacionesestado']['nombre']) ?>" data-pk="<?php echo h($reparacionesestado['Reparacionesestado']['id']) ?>"><?php echo h($reparacionesestado['Reparacionesestado']['nombre']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $reparacionesestado['Reparacionesestado']['id']]]);
                        //echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $reparacionesestado['Reparacionesestado']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $reparacionesestado['Reparacionesestado']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>$(document).ready(function () {
                $('.nombre').editable({type: 'text', name: 'nombre', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Reparacionesestados/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="2"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>