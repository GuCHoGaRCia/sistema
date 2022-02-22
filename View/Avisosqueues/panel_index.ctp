<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="avisosqueues index">
    <h2><?php echo __('Avisos en Cola'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Avisosqueue']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('emailfrom', __('De')); ?></th>
                <th><?php echo $this->Paginator->sort('razonsocial', __('Razon social')); ?></th>
                <th><?php echo $this->Paginator->sort('asunto', __('Asunto')); ?></th>
                <th><?php echo $this->Paginator->sort('codigohtml', __('Cuerpo')); ?></th>
                <th><?php echo $this->Paginator->sort('mailto', __('Destinatario')); ?></th>
                <th><?php echo $this->Paginator->sort('whatsapp', __('WhatsApp')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($avisosqueues as $avisosqueue):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($avisosqueue['Avisosqueue']['emailfrom']) ?>&nbsp;</td>
                    <td><?php echo h($avisosqueue['Avisosqueue']['razonsocial']) ?>&nbsp;</td>
                    <td><?php echo h($avisosqueue['Avisosqueue']['asunto']) ?>&nbsp;</td>
                    <td><?php echo ($avisosqueue['Avisosqueue']['codigohtml']) ?>&nbsp;</td>
                    <td><?php echo h($avisosqueue['Avisosqueue']['mailto']) ?>&nbsp;</td>
                    <td><?php echo h($avisosqueue['Avisosqueue']['whatsapp']) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="6"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>