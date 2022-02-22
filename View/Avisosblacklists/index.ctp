<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="avisosblacklists index">
    <h2><?php echo __('Lista negra de mails'); ?></h2>
    <?php
    echo $this->element('toolbar', ['pagecount' => true, 'filter' => ['enabled' => false], 'pagesearch' => true, 'pagenew' => false, 'model' => 'Avisosblacklist']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('cantidad', __('Intentos de envío')); ?></th>
                <th><?php echo $this->Paginator->sort('dsc', __('Error encontrado')); ?></th>
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
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($avisosblacklist['Avisosblacklist']['email']) ?>&nbsp;</td>
                    <td><?php echo h($avisosblacklist['Avisosblacklist']['cantidad']) ?>&nbsp;</td>
                    <td><?php echo h($avisosblacklist['Avisosblacklist']['dsc']) ?>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $avisosblacklist['Avisosblacklist']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', h($avisosblacklist['Avisosblacklist']['email'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            </script>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="4"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>