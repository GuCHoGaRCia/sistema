<?php echo $this->Html->css(array('bootstrap-editable.css', '/css/select2.min.css'), 'stylesheet', array('inline' => false)); ?>
<div class="clients index">
    <h2><?php echo __('Clientes'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Nombre'); ?></th>
                <th><?php echo __('CUIT'); ?></th>
                <th><?php echo __('Dirección'); ?></th>
                <th><?php echo __('Ciudad'); ?></th>
                <th><?php echo __('Teléfono'); ?></th>
                <th><?php echo __('Email'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <tr class="altrow">
                <td class="borde_tabla"></td>
                <td><span class="name" data-value="<?php echo h($client['Client']['name']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['name']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.name').editable({type: 'text', name: 'name', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        <td><span class="cuit" data-value="<?php echo h($client['Client']['cuit']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['cuit']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.cuit').editable({type: 'text', name: 'cuit', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        <td><span class="address" data-value="<?php echo h($client['Client']['address']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['address']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.address').editable({type: 'text', name: 'address', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        <td><span class="city" data-value="<?php echo h($client['Client']['city']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['city']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.city').editable({type: 'text', name: 'city', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        <td><span class="telephone" data-value="<?php echo h($client['Client']['telephone']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['telephone']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.telephone').editable({type: 'text', name: 'telephone', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        <td><span class="email" data-value="<?php echo h($client['Client']['email']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['email']) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.email').editable({type: 'text', name: 'email', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
            });</script>
        </td>
        <td class="borde_tabla"></td>
        </tr>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="6"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
</div>

<div class="users index">
    <label>Usuarios relacionados</label>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Nombre'); ?></th>
                <th><?php echo __('Usuario'); ?></th>
                <th><?php echo __('Contraseña'); ?></th>
                <th class="center"><?php echo __('Habilitado'); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($client['User'] as $user):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span><?php echo h($user['name']) ?></span>&nbsp;</td>
                    <td><span class="username" data-value="<?php echo h($user['username']) ?>" data-pk="<?php echo h($user['id']) ?>"><?php echo h($user['username']) ?></span><?= '@' . $_SESSION['Auth']['User']['Client']['identificador_cliente'] ?>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.username').editable({type: 'text', name: 'username', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>Users/editar', placement: 'right'});
                });</script>
            <td><span class="password" data-value="" data-pk="<?php echo h($user['id']) ?>">******</span>&nbsp;</td>
            <script>
                $(document).ready(function () {
                    $('.password').editable({
                        type: 'text',
                        name: 'password',
                        url: '<?php echo $this->webroot; ?>Users/editar',
                        placement: 'right',
                        display: function (value, response) {
                            return false;
                        },
                    });
                });
            </script>
            <td class="center"><?php echo $this->Html->link($this->Html->image(h($user['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Users', 'action' => 'invertir', 'enabled', h($user['id'])), array('class' => 'status', 'escape' => false)); ?></td>
            <td class="acciones">
                <?php
                //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('controller' => 'Users', 'action' => 'view', $user['id'])));
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('controller' => 'Users', 'action' => 'delete', $user['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $user['name']));
                ?>
            </td>
            <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="5"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
</div>