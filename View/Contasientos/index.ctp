<div class="contasientos index">
    <h2><?php echo __('Asientos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Contasiento']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('contejercicio_id', __('Ejercicio')); ?></th>
                <th><?php echo $this->Paginator->sort('cuentaorigen_id', __('Origen')); ?></th>
                <th><?php echo $this->Paginator->sort('cuentadestino_id', __('Destino')); ?></th>
                <th><?php echo $this->Paginator->sort('descripcion', __('Descripción')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Período')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('debehaber', __('Debe/Haber')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('manual', __('Manual')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($contasientos as $contasiento):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($contasiento['Consorcio']['name']); ?></td>
                    <td><?php echo h($contasiento['Contejercicio']['nombre']); ?></td>
                    <td><?php echo h($contasiento['Cuentaorigen']['codigo'] . " - " . $contasiento['Cuentaorigen']['titulo']); ?></td>
                    <td><?php echo h($contasiento['Cuentadestino']['codigo'] . " - " . $contasiento['Cuentadestino']['titulo']); ?></td>
                    <td><span class="descripcion" data-value="<?php echo h($contasiento['Contasiento']['descripcion']) ?>" data-pk="<?php echo h($contasiento['Contasiento']['id']) ?>"><?php echo h($contasiento['Contasiento']['descripcion']) ?></span>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('m/Y'), $contasiento['Contasiento']['fecha']) ?></td>
                    <td><span class="importe" data-value="<?php echo h($contasiento['Contasiento']['importe']) ?>" data-pk="<?php echo h($contasiento['Contasiento']['id']) ?>"><?php echo h($contasiento['Contasiento']['importe']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($contasiento['Contasiento']['debehaber'] ? '1' : '0') . '.png', array('title' => __('Debe / Haber'))), array('controller' => 'Contasientos', 'action' => 'invertir', 'debehaber', h($contasiento['Contasiento']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->image(h($contasiento['Contasiento']['manual'] ? '1' : '0') . '.png', array('title' => __('Manual'))); ?></td>
                    <td class="acciones" style="width:100px">
                        <?php
                        //echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'url' => ['action' => 'view', $contasiento['Contasiento']['id']]]);
                        echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $contasiento['Contasiento']['id']]]);
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $contasiento['Contasiento']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $contasiento['Contasiento']['id']));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(document).ready(function(){$('.descripcion').editable({type:'text', name:'descripcion', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>contasientos/editar', placement:'right'}); $('.importe').editable({type:'text', name:'importe', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>contasientos/editar', placement:'right'}); $('.debehaber').editable({type:'text', name:'debehaber', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>contasientos/editar', placement:'left'}); $('.manual').editable({type:'text', name:'manual', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>contasientos/editar', placement:'left'});
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