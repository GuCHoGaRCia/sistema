<div class="saldosCierres index">
    <h2><?php echo __('Saldos al cierre'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'SaldosCierre')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Cliente - Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('capital', __('Capital')); ?></th>
                <th><?php echo $this->Paginator->sort('interes', __('Interés')); ?></th>
                <th><?php echo $this->Paginator->sort('redondeo', __('Redondeo')); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($saldosCierres as $saldosCierre):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($saldosCierre['Client']['name'] . " - " . $saldosCierre['Consorcio']['name']); ?></td>
                    <td><?php echo h($saldosCierre['Liquidation']['name']); ?></td>
                    <td><?php echo $this->Html->link($saldosCierre['Propietario']['name'], array('controller' => 'propietarios', 'action' => 'view', $saldosCierre['Propietario']['id'])); ?></td>
                    <td><span class="capital" data-value="<?php echo h($saldosCierre['SaldosCierre']['capital']) ?>" data-pk="<?php echo h($saldosCierre['SaldosCierre']['id']) ?>"><?php echo h($saldosCierre['SaldosCierre']['capital']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.capital').editable({type: 'text', name: 'capital',success:function(n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>saldosCierres/editar', placement: 'right'});
                });</script>
            <td><span class="interes" data-value="<?php echo h($saldosCierre['SaldosCierre']['interes']) ?>" data-pk="<?php echo h($saldosCierre['SaldosCierre']['id']) ?>"><?php echo h($saldosCierre['SaldosCierre']['interes']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.interes').editable({type: 'text', name: 'interes',success:function(n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>saldosCierres/editar', placement: 'right'});
                });</script>
            <td><span class="redondeo" data-value="<?php echo h($saldosCierre['SaldosCierre']['redondeo']) ?>" data-pk="<?php echo h($saldosCierre['SaldosCierre']['id']) ?>"><?php echo h($saldosCierre['SaldosCierre']['redondeo']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.redondeo').editable({type: 'text', name: 'redondeo',success:function(n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>saldosCierres/editar', placement: 'right'});
                });</script>

            
    <?php
	//<td class="acciones" style="width:auto">
    //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $saldosCierre['SaldosCierre']['id'])));
    //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $saldosCierre['SaldosCierre']['id'])));
    //echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $saldosCierre['SaldosCierre']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $saldosCierre['SaldosCierre']['id']));
	//</td>
    ?>

            <td class="borde_tabla"></td>
            </tr>
<?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="5"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
<?php echo $this->element('pagination'); ?></div>