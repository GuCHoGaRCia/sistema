<div class="cajasegresos index">
    <h2><?php echo __('Egresos de caja'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Cajasegreso']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('caja_id', __('Caja')); ?></th>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Pesos')); ?></th>
                <th><?php echo $this->Paginator->sort('cheque', __('Cheques')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($cajasegresos as $cajasegreso):
                $class = $cajasegreso['Cajasegreso']['anulado'] ? ' class="error-message tachado"' : null;
                if ($i++ % 2 == 0) {
                    $class = $cajasegreso['Cajasegreso']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                }
                $detallecheque = "";
                if ($cajasegreso['Cajasegreso']['cheque'] > 0 && !empty($cajasegreso['Cajastransferenciascheque'])) {
                    $detallecheque = "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver detalle cheques' onclick='$(\"#ch\").dialog(\"open\");$(\"#ch\").load(\"" . $this->webroot . "Cajastransferenciascheques/listar/e/" . $cajasegreso['Cajasegreso']['id'] . "\");'/>";
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($cajasegreso['Caja']['name']) ?></td>
                    <td><?php echo h($cajasegreso['Bancoscuenta']['name']) ?></td>
                    <td><?php echo h($cajasegreso['Consorcio']['name']) ?></td>
                    <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $cajasegreso['Cajasegreso']['created']) ?>'><span class="fecha"><?php echo $this->Time->format(__('d/m/Y'), $cajasegreso['Cajasegreso']['fecha']) ?></span>&nbsp;</td>
                    <td><span class="concepto"><?php echo h($cajasegreso['Cajasegreso']['concepto']) ?></span>&nbsp;</td>
                    <td><span class="importe"><?php echo $this->Functions->money($cajasegreso['Cajasegreso']['importe']) ?></span>&nbsp;</td>
                    <td><span class="importe"><?php echo $this->Functions->money($cajasegreso['Cajasegreso']['cheque']) . $detallecheque ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        // si es egreso de caja (bancoscuenta_id=0)
                        if (!$cajasegreso['Cajasegreso']['anulado'] && $cajasegreso['Cajasegreso']['bancoscuenta_id'] == 0 && empty($cajasegreso['Cajasegreso']['proveedorspago_id']) && $cajasegreso['Cajasegreso']['user_id'] == $_SESSION['Auth']['User']['id'] && $cajasegreso['Cajasegreso']['movimientoasociado'] == 0 && !$cajasegreso['Cajasegreso']['estransferencia']) {
                            if (substr($cajasegreso['Cajasegreso']['concepto'], 0, 11) != "Anulación ") {
                                echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $cajasegreso['Cajasegreso']['id']), array('escapeTitle' => false), __('Desea anular el movimiento # %s?', h($cajasegreso['Cajasegreso']['concepto'])));
                            }
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="8"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(function () {
        var dialog = $("#ch").dialog({
            autoOpen: false, width: "auto",
            //position: {my: "center center"},
            closeOnEscape: false,
            title: "Detalle de Cheques transferidos (click para más info)",
            modal: true, buttons: {
                Cerrar: function () {
                    $("#ch").html('');
                    dialog.dialog("close");
                }
            }
        });
    });
</script>
<?=
"<div id='ch' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC  ?>