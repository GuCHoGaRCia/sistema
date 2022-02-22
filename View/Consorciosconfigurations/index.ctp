<div class="consorciosconfigurations index">
    <h2><?php echo __('Configuración de Consorcios'); ?></h2>
    <?php echo $this->element('toolbar', ['pagesearch' => true, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'model' => 'Consorciosconfiguration']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidations_type_id', __('Tipo Liquidación')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('enviaraviso', __('Enviar aviso')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('reportarsaldo', __('Reportar saldo')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('onlinerc', __('Online RC')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('onlinerg', __('Online RG')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('onlinecs', __('Online CS')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('imprimerc', __('Imprime RC')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('imprimerg', __('Imprime RG')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('imprimecs', __('Imprime CS')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($consorciosconfigurations as $consorciosconfiguration):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($consorciosconfiguration['Consorcio']['name']); ?></td>
                    <td><?php echo h($lt[$consorciosconfiguration['Consorciosconfiguration']['liquidations_type_id']]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['enviaraviso'] ? '1' : '0') . '.png', array('title' => __('Enviar aviso'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'enviaraviso', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['reportarsaldo'] ? '1' : '0') . '.png', array('title' => __('Reportar Saldo'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'reportarsaldo', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['onlinerc'] ? '1' : '0') . '.png', array('title' => __('Online Resumen cuenta'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'onlinerc', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['onlinerg'] ? '1' : '0') . '.png', array('title' => __('Online Resumen Gastos'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'onlinerg', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['onlinecs'] ? '1' : '0') . '.png', array('title' => __('Online Composición de Saldos'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'onlinecs', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['imprimerc'] ? '1' : '0') . '.png', array('title' => __('Imprime Resumen Cuenta'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'imprimerc', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['imprimerg'] ? '1' : '0') . '.png', array('title' => __('Imprime Resumen Gastos'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'imprimerg', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(($consorciosconfiguration['Consorciosconfiguration']['imprimecs'] ? '1' : '0') . '.png', array('title' => __('Imprime Composición de Saldos'))), array('controller' => 'Consorciosconfigurations', 'action' => 'invertir', 'imprimecs', h($consorciosconfiguration['Consorciosconfiguration']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function(){$('.online').editable({type:'text', name:'online', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'right'}); $('.imprimir').editable({type:'text', name:'imprimir', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'right'}); $('.enviaraviso').editable({type:'text', name:'enviaraviso', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'right'}); $('.reportarsaldo').editable({type:'text', name:'reportarsaldo', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'right'}); $('.imprimerc').editable({type:'text', name:'imprimerc', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'left'}); $('.imprimerg').editable({type:'text', name:'imprimerg', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'left'}); $('.imprimecs').editable({type:'text', name:'imprimecs', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>consorciosconfigurations/editar', placement:'left'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="10"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>