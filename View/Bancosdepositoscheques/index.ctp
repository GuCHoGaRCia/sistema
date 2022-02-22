<div class="bancosdepositoscheques index">
    <h2><?php echo __('Depósitos de cheques bancarios'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $cuentasBancarias, 'field' => 'cuenta'], 'pagesearch' => true, 'pagenew' => true, 'print' => true, 'model' => 'Bancosdepositoscheque')); ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            BANCOS DEPOSITOS CHEQUES -
            <?php
            echo h((isset($this->request->data['filter']['cuenta']) ? $cuentasBancarias[$this->request->data['filter']['cuenta']] : 'Todas las Cuentas'));
            ?>
        </div>
        <table cellpadding="0" cellspacing="0">  
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('cheque_id', __('Cheque')); ?></th>
                    <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                    <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                    <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ($bancosdepositoscheques as $bancosdepositoscheque):
                    $class = $bancosdepositoscheque['Bancosdepositoscheque']['anulado'] ? ' class="error-message tachado"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $bancosdepositoscheque['Bancosdepositoscheque']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo $this->Html->link($bancosdepositoscheque['Cheque']['concepto'], array('controller' => 'Cheques', 'action' => 'view', $bancosdepositoscheque['Cheque']['id'])); ?></td>
                        <td><?php echo $this->Html->link($bancosdepositoscheque['Bancoscuenta']['name'], array('controller' => 'Bancoscuentas', 'action' => 'view', $bancosdepositoscheque['Bancoscuenta']['id'])); ?></td>
                        <td><?php echo h($bancosdepositoscheque['Bancosdepositoscheque']['concepto']); ?></td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $bancosdepositoscheque['Bancosdepositoscheque']['fecha']) ?>&nbsp;</td>
                        <td><?php echo $this->Functions->money($bancosdepositoscheque['Cheque']['importe']); ?></td>
                        <td class="acciones" style="width:auto">
                            <?php
                            if ($bancosdepositoscheque['Bancosdepositoscheque']['user_id'] == $_SESSION['Auth']['User']['id'] && !$bancosdepositoscheque['Bancosdepositoscheque']['anulado']) {
                                echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $bancosdepositoscheque['Bancosdepositoscheque']['id']), array('escapeTitle' => false), __('Desea anular el movimiento # %s?', $bancosdepositoscheque['Bancosdepositoscheque']['concepto']));
                            }
                            ?>
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="6"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
    </div>
    <?php echo $this->element('pagination'); ?></div>