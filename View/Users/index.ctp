<div class="users index">
    <h2><?php echo __('Usuarios'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'User')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('username', __('Usuario')); ?></th>
                <th><?php echo $this->Paginator->sort('password', __('Contraseña')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($users as $user):
                //a los usuarios q no sean ricardo, esteban, marce, marcela no les muestro estos mismos usuarios 
                if (!in_array($_SESSION['Auth']['User']['username'], ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster']) && in_array($user['User']['username'], ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster'])) {
                    continue;
                }
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($user['User']['name']) ?>" data-pk="<?php echo h($user['User']['id']) ?>"><?php echo h($user['User']['name']) ?></span>&nbsp;</td>
                    <td><span class="username" data-value="<?php echo h($user['User']['username']) ?>" data-pk="<?php echo h($user['User']['id']) ?>"><?php echo h($user['User']['username']) ?></span><?= '@' . $user['Client']['identificador_cliente'] ?>&nbsp;</td>
                    <td><span class="password" data-value="" data-pk="<?php echo h($user['User']['id']) ?>">******</span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($user['User']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Users', 'action' => 'invertir', 'enabled', h($user['User']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $user['User']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $user['User']['name']));
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
                    }, url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right'});
            });
            $('.username').editable({type: 'text', name: 'username', success: function (n, r) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right'});
            $('.password').editable({type: 'text', name: 'password', url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right',
                success: function (n, r) {
                    if (n) {
                        return decodeURIComponent(escape(n));
                    }
                },
                display: function (value, response) {
                    return false;
                },
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="5"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>