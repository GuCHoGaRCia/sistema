<div class="users index">
    <h2><?php echo __('Usuarios'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => '')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('username', __('Usuario')); ?></th>
                <th><?php echo $this->Paginator->sort('password', __('Contraseña')); ?></th>
                <th><?php echo $this->Paginator->sort('perfil', __('Perfil')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('eliminacobranzas', __('Elimina cobranzas?')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('is_admin', __('Admin?')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th><?php echo $this->Paginator->sort('lastseen', __('Último logueo')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($users as $user):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($user['Client']['name'], array('controller' => 'Clients', 'action' => 'view', $user['Client']['id'])); ?></td>
                    <td><span class="name" data-value="<?php echo h($user['User']['name']) ?>" data-pk="<?php echo h($user['User']['id']) ?>"><?php echo h($user['User']['name']) ?></span>&nbsp;</td>
                    <td><span class="username" data-value="<?php echo h($user['User']['username']) ?>" data-pk="<?php echo h($user['User']['id']) ?>"><?php echo h($user['User']['username']) ?></span><?= '@' . $user['Client']['identificador_cliente'] ?>&nbsp;</td>
                    <td><span class="password" data-value="" data-pk="<?php echo h($user['User']['id']) ?>">******</span>&nbsp;</td>
                    <td><span class="perfil" data-value="<?php echo h($user['User']['perfil']) ?>" data-pk="<?php echo h($user['User']['id']) ?>"><?php echo h($user['User']['perfil']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($user['User']['eliminacobranzas'] ? '1' : '0') . '.png'), array('controller' => 'Users', 'action' => 'invertir', 'eliminacobranzas', h($user['User']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($user['User']['is_admin'] ? '1' : '0') . '.png'), array('controller' => 'Users', 'action' => 'invertir', 'is_admin', h($user['User']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($user['User']['enabled'] ? '1' : '0') . '.png'), array('controller' => 'Users', 'action' => 'invertir', 'enabled', h($user['User']['id'])), array('class' => 'status', 'escape' => false)); ?></td>		
                    <td><?= !empty($user['User']['lastseen']) ? $this->Time->timeAgoInWords($user['User']['lastseen']) : '--' ?>&nbsp;</td>
                    <td class="acciones">
                        <?php
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $user['User']['id'])));
                        //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $user['User']['id'])));
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $user['User']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $user['User']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="10"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.perfil').editable({
            type: 'text',
            name: 'perfil',
            url: '<?php echo $this->webroot; ?>users/editar',
            placement: 'right',
        });
        $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                if (n) {
                    return n
                }
            }, placement: 'right', url: '<?php echo $this->webroot; ?>Users/editar', });
        $('.username').editable({type: 'text', name: 'username', url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right', });
        $('.password').editable({type: 'text', name: 'password', url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right', success: function (n, r) {
                if (n) {
                    return decodeURIComponent(escape(n));
                }
            },
            display: function (value, response) {
                return false;
            }
        });
    });
</script>