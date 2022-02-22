<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="avisosblacklists index">
    <h2><?php echo __('Lista negra de mails'); ?></h2>
    <?php
    echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $clients2, 'field' => 'cliente', 'panel' => true], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Avisosblacklist']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Creación')); ?></th>
                <th><?php echo $this->Paginator->sort('cantidad', __('Envíos')); ?></th>
                <th><?php echo $this->Paginator->sort('dsc', __('Error')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($avisosblacklists as $avisosblacklist):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                $c = isset($clients2[$avisosblacklist['Avisosblacklist']['client_id']]) ? $clients2[$avisosblacklist['Avisosblacklist']['client_id']] : (isset($clients[$avisosblacklist['Avisosblacklist']['client_id']]) ? $clients[$avisosblacklist['Avisosblacklist']['client_id']] : '--');
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($c); ?></td>
                    <td><span class="email" data-value="<?php echo h($avisosblacklist['Avisosblacklist']['email']) ?>" data-pk="<?php echo h($avisosblacklist['Avisosblacklist']['id']) ?>"><?php echo h($avisosblacklist['Avisosblacklist']['email']) ?></span>&nbsp;</td>
                    <td><span class="created" title="Modificado el <?php echo h($this->Time->format(__('d/m/Y H:i:s'),$avisosblacklist['Avisosblacklist']['modified'])) ?>" data-value="<?php echo h($this->Time->format(__('d/m/Y H:i:s'),$avisosblacklist['Avisosblacklist']['created'])) ?>" data-pk="<?php echo h($this->Time->format(__('d/m/Y H:i:s'),$avisosblacklist['Avisosblacklist']['id'])) ?>"><?php echo h($this->Time->format(__('d/m/Y H:i:s'),$avisosblacklist['Avisosblacklist']['created'])) ?></span>&nbsp;</td>
                    <td><span class="cantidad" data-value="<?php echo h($avisosblacklist['Avisosblacklist']['cantidad']) ?>" data-pk="<?php echo h($avisosblacklist['Avisosblacklist']['id']) ?>"><?php echo h($avisosblacklist['Avisosblacklist']['cantidad']) ?></span>&nbsp;</td>
                    <td><span class="dsc" data-value="<?php echo h($avisosblacklist['Avisosblacklist']['dsc']) ?>" data-pk="<?php echo h($avisosblacklist['Avisosblacklist']['id']) ?>"><?php echo h($avisosblacklist['Avisosblacklist']['dsc']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $avisosblacklist['Avisosblacklist']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', h($avisosblacklist['Avisosblacklist']['email'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>$(document).ready(function(){$('.email').editable({type:'text', name:'email', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Avisosblacklists/editar', placement:'right'}); $('.cantidad').editable({type:'text', name:'cantidad', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Avisosblacklists/editar', placement:'right'}); $('.dsc').editable({type:'text', name:'dsc', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Avisosblacklists/editar', placement:'right'}); });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="6"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>