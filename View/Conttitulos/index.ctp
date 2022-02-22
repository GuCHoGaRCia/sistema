<div class="conttitulos index">
    <h2><?php echo __('Títulos Contables'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'model' => 'Conttitulo']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Padre') ?></th>
                <th><?php echo __('Código') ?></th>
                <th><?php echo __('Título') ?></th>
                <th><?php echo __('Órden') ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($conttitulos as $conttitulo):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo!empty($conttitulo['Conttitulo']['conttitulo_id']) ? h($titulos[$conttitulo['Conttitulo']['conttitulo_id']]) : '--'; ?></td>
                    <td><span class="code" data-value="<?php echo h($conttitulo['Conttitulo']['code']) ?>" data-pk="<?php echo h($conttitulo['Conttitulo']['id']) ?>"><?php echo h($conttitulo['Conttitulo']['code']) ?></span>&nbsp;</td>
                    <td><?= ($conttitulo['Conttitulo']['conttitulo_id'] != 0 ? str_repeat("&nbsp;", $arbol[$conttitulo['Conttitulo']['conttitulo_id']]) : '') ?><span class="titulo" data-value="<?php echo h($conttitulo['Conttitulo']['titulo']) ?>" data-pk="<?php echo h($conttitulo['Conttitulo']['id']) ?>"><?php echo h($conttitulo['Conttitulo']['titulo']) ?></span>&nbsp;</td>
                    <td><span class="orden" data-value="<?php echo h($conttitulo['Conttitulo']['orden']) ?>" data-pk="<?php echo h($conttitulo['Conttitulo']['id']) ?>"><?php echo h($conttitulo['Conttitulo']['orden']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:100px">
                        <?php
                        //echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $conttitulo['Conttitulo']['id']]]);
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $conttitulo['Conttitulo']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $conttitulo['Conttitulo']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $conttitulo['Conttitulo']['id']));
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
                    }, url: '<?php echo $this->webroot; ?>conttitulos/editar', placement: 'right'});
                $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>conttitulos/editar', placement: 'left'});
                $('.orden').editable({type: 'text', name: 'orden', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>conttitulos/editar', placement: 'left'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="5"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>