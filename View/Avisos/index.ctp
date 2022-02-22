<div class="avisos index">
    <h2><?php echo __('Avisos'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => false, 'pagenew' => true, 'model' => 'Aviso'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('email', __('Email') . " (" . count($avisos) . ")"); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Puesto en cola')); ?></th>
                <th><?php echo $this->Paginator->sort('recibido', __('Última recepción')); ?></th>
                <th><?php echo $this->Paginator->sort('click', __('Último acceso')); ?></th>
                <th><?php echo $this->Paginator->sort('rechazado', __('Último rechazo')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            //debug($avisos);die;
            foreach ($avisos as $aviso):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($propietarios[strtolower($aviso['Aviso']['email'])]); ?></td>
                    <td><?php echo $aviso['Aviso']['created'] !== "0000-00-00 00:00:00" && !empty($aviso['Aviso']['created']) ? $this->Time->format(__('d/m/Y H:i:s'), $aviso['Aviso']['created']) : "--" ?></td>
                    <td><?php echo $aviso['Aviso']['recibido'] !== "0000-00-00 00:00:00" && !empty($aviso['Aviso']['recibido']) ? $this->Time->format(__('d/m/Y H:i:s'), $aviso['Aviso']['recibido']) : "--" ?></td>
                    <td><?php echo $aviso['Aviso']['click'] !== "0000-00-00 00:00:00" && !empty($aviso['Aviso']['click']) ? $this->Time->format(__('d/m/Y H:i:s'), $aviso['Aviso']['click']) : "--" ?></td>
                    <td><?php echo $aviso['Aviso']['rechazado'] !== "0000-00-00 00:00:00" && !empty($aviso['Aviso']['rechazado']) ? $this->Time->timeAgoInWords($aviso['Aviso']['rechazado']) . "  " . $this->Html->image('icon-info.png', ['title' => h($aviso['Aviso']['eventos'])]) : "--" ?></td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php
    if (empty($this->request->data['filter']['consorcio'])) {
        echo $this->element('pagination'); // lo muestro solo cuando no selecciona ningun consorcio
    }
    ?>
</div>