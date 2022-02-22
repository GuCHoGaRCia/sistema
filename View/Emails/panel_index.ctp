<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="emails index">
    <h2><?php echo __('Emails'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Email']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
				<th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('asunto', __('Asunto')); ?></th>
                <th class="acciones"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($emails as $email):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
					<td><?php echo h($email['Client']['name']) ?>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($email['Email']['email']) ?>" data-pk="<?php echo h($email['Email']['id']) ?>"><?php echo h($email['Email']['email']) ?></span>&nbsp;</td>
                    <td><span class="asunto" data-value="<?php echo h($email['Email']['asunto']) ?>" data-pk="<?php echo h($email['Email']['id']) ?>"><?php echo h($email['Email']['asunto']) ?></span>&nbsp;</td>
                    <td class="acciones">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $email['Email']['id']]]);
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $email['Email']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $email['Email']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $email['Email']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="4"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>$(document).ready(function(){$('.email').editable({type:'text', name:'email', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Emails/editar', placement:'right'}); $('.asunto').editable({type:'text', name:'asunto', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Emails/editar', placement:'right'}); $('.html').editable({type:'text', name:'html', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Emails/editar', placement:'right'}); });
</script>