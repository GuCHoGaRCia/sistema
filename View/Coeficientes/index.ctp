<div class="coeficientes index">
    <h2><?php echo __('Coeficientes'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Coeficiente'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($coeficientes as $coeficiente):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($coeficiente['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $coeficiente['Consorcio']['id'])); ?></td>
                    <td><span class="name" data-value="<?php echo h($coeficiente['Coeficiente']['name']) ?>" data-pk="<?php echo h($coeficiente['Coeficiente']['id']) ?>"><?php echo h($coeficiente['Coeficiente']['name']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($coeficiente['Coeficiente']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Coeficientes', 'action' => 'invertir', 'enabled', h($coeficiente['Coeficiente']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones">
                        <?php
                        echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $coeficiente['Coeficiente']['id'])));
                        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $coeficiente['Coeficiente']['id'])));
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $coeficiente['Coeficiente']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($coeficiente['Coeficiente']['name'])));
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
                    }, url: '<?php echo $this->webroot; ?>Coeficientes/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>