<div class="formasdepagos index">
    <h2><?php echo __('Forma de pago'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'filter' => ['enabled' => true, 'panel' => true, 'options' => $client_id, 'field' => 'cliente'], 'model' => 'Formasdepago']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_id', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('forma', __('Forma de pago')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitada', __('Habilitada')); ?></th>
                <th class="acciones" style="width:70px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($formasdepagos as $formasdepago):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($formasdepago['Client']['name']) ?>&nbsp;</td>
                    <td><?php echo h($formasdepago['Formasdepago']['forma']) ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($formasdepago['Formasdepago']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Habilitado / Deshabilitado'))), array('controller' => 'Formasdepagos', 'panel' => true, 'action' => 'invertir', 'habilitada', h($formasdepago['Formasdepago']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        if (in_array($_SESSION['Auth']['User']['username'], ['rcasco', 'mcorzo', 'ecano'])) {
                            echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $formasdepago['Formasdepago']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $formasdepago['Formasdepago']['id']));
                        }
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
