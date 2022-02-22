<div class="contcuentas index">
    <h2><?php echo __('Cuentas Contables'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Contcuenta']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('conttitulo_id', __('Padre')); ?></th>
                <th><?php echo $this->Paginator->sort('code', __('Código')); ?></th>
                <th><?php echo $this->Paginator->sort('titulo', __('Título')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($contcuentas as $contcuenta):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($hojas[$contcuenta['Contcuenta']['conttitulo_id']]) ?></td>
                    <td><span class="code" data-value="<?php echo h($contcuenta['Contcuenta']['code']) ?>" data-pk="<?php echo h($contcuenta['Contcuenta']['id']) ?>"><?php echo h($contcuenta['Contcuenta']['code']) ?></span>&nbsp;</td>
                    <td><span class="titulo" data-value="<?php echo h($contcuenta['Contcuenta']['titulo']) ?>" data-pk="<?php echo h($contcuenta['Contcuenta']['id']) ?>"><?php echo h($contcuenta['Contcuenta']['titulo']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:100px">
                        <?php
                        //echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $contcuenta['Contcuenta']['id']]]);
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $contcuenta['Contcuenta']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $contcuenta['Contcuenta']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $contcuenta['Contcuenta']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                $('.code').editable({type: 'text', name: 'code', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>contcuentas/editar', placement: 'right'});
                $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>contcuentas/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>