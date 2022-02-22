<div class="pagoselectronicos index">
    <h2><?php echo __('Pagos Roela'); ?></h2>
    <?php
    echo $this->element('toolbar', ['filter' => ['enabled' => true, 'options' => $clients, 'field' => 'client', 'panel' => true], 'pagesearch' => true, 'multidelete' => true, 'pagenew' => true, 'taction' => 'addroela', 'model' => 'Pagoselectronico']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('client_code', __('Cliente')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_code', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_code', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('prefijo', __('Prefijo')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_proc', __('Fecha Procesado')); ?></th>
                <th><?php echo $this->Paginator->sort('medio', __('Medio')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th><?php echo $this->Paginator->sort('comision', __('Comisión')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('cobranza_id', __('Cargado')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($pagoselectronicos as $pagoselectronico):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h(isset($clients[$pagoselectronico['Pagoselectronico']['client_code']]) ? $clients[$pagoselectronico['Pagoselectronico']['client_code']] : 'Cliente ' . $pagoselectronico['Pagoselectronico']['client_code']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['consorcio_code']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['propietario_code']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['prefijo']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $pagoselectronico['Pagoselectronico']['fecha']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $pagoselectronico['Pagoselectronico']['fecha_proc']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['medio']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['importe']) ?>&nbsp;</td>
                    <td><?php echo h($pagoselectronico['Pagoselectronico']['comision']) ?>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(h($pagoselectronico['Pagoselectronico']['cobranza_id'] ? '1' : '0') . '.png', array('title' => __('El pago fue cargado'))); ?></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $pagoselectronico['Pagoselectronico']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $pagoselectronico['Pagoselectronico']['id']));
                        echo $this->Form->input('borrado', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $pagoselectronico['Pagoselectronico']['id'], 'style' => 'box-shadow:none;transform: scale(2);margin:8px;position:absolute']);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="11"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>