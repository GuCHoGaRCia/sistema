<div class="avisoswhatsapps index">
    <h2><?php echo __('Historial Avisos enviados por WhatsApp'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Avisoswhatsapp']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('created', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('resul', __('Resul')); ?></th>
                <th><?php echo $this->Paginator->sort('numero', __('Número')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($avisoswhatsapps as $avisoswhatsapp):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                $res = json_decode($avisoswhatsapp['Avisoswhatsapp']['resul'], true);
                ?>
                <tr<?= $class ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Time->format(__('d/m/Y H:i:s'), $avisoswhatsapp['Avisoswhatsapp']['created']) ?>&nbsp;</td>
                    <td><?php echo h($res['message'] ?? $avisoswhatsapp['Avisoswhatsapp']['resul']) ?>&nbsp;</td>
                    <td><?php echo h($avisoswhatsapp['Avisoswhatsapp']['numero']) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="3"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>