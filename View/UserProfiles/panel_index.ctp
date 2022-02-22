<div class="userProfiles index">
    <h2><?php echo __('Perfiles de usuarios'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'UserProfile']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('descripcion', __('Descripcion')); ?></th>
                <th><?php echo $this->Paginator->sort('permisos', __('Permisos')); ?></th>
                <th><?php echo $this->Paginator->sort('perfil', __('Perfil')); ?></th>
                <th><?php echo $this->Paginator->sort('urldefecto', __('URL Defecto')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitado', __('Habilitado')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($userProfiles as $userProfile):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?= $class ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="nombre" data-value="<?php echo h($userProfile['UserProfile']['nombre']) ?>" data-pk="<?php echo h($userProfile['UserProfile']['id']) ?>"><?php echo h($userProfile['UserProfile']['nombre']) ?></span>&nbsp;</td>
                    <td><span class="descripcion" data-value="<?php echo h($userProfile['UserProfile']['descripcion']) ?>" data-pk="<?php echo h($userProfile['UserProfile']['id']) ?>"><?php echo h($userProfile['UserProfile']['descripcion']) ?></span>&nbsp;</td>
                    <td>
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('config.png', array('id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:300px;max-height:100px;font-size:11px;overflow-y:scroll">
                                <ul>
                                    <?php
                                    $routes = json_decode($userProfile['UserProfile']['permisos']);
                                    foreach ($routes as $r) {
                                        echo "<li>" . h($r) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </span>
                        </span>  
                    </td>
                    <td><span class="perfil" data-value="<?php echo h($userProfile['UserProfile']['perfil']) ?>" data-pk="<?php echo h($userProfile['UserProfile']['id']) ?>"><?php echo h($userProfile['UserProfile']['perfil']) ?></span>&nbsp;</td>
                    <td><span class="urldefecto" data-value="<?php echo h($userProfile['UserProfile']['urldefecto']) ?>" data-pk="<?php echo h($userProfile['UserProfile']['id']) ?>"><?php echo h($userProfile['UserProfile']['urldefecto']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($userProfile['UserProfile']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'UserProfiles', 'panel' => true, 'action' => 'invertir', 'habilitado', h($userProfile['UserProfile']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $userProfile['UserProfile']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $userProfile['UserProfile']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $userProfile['UserProfile']['id']));
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
                    }, url: '<?php echo $this->webroot; ?>userProfiles/editar', placement: 'right'});
                $('.descripcion').editable({type: 'text', name: 'descripcion', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>userProfiles/editar', placement: 'right'});
                $('.habilitado').editable({type: 'text', name: 'habilitado', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>userProfiles/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="7"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<style>
    .listareportes{
        margin-left:-325px;
    }
</style>