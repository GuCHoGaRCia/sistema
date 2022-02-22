<div class="cajasingresos index">
    <h2><?php echo __('Ingresos a caja'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'model' => 'Cajasingreso']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('caja_id', __('Caja')); ?></th>
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
            foreach ($cajasingresos as $cajasingreso):
                $class = $cajasingreso['Cajasingreso']['anulado'] ? ' class="error-message tachado"' : null;
                if ($i++ % 2 == 0) {
                    $class = $cajasingreso['Cajasingreso']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                }
                $detallecheque = "";
                if ($cajasingreso['Cajasingreso']['cheque'] > 0 && !empty($cajasingreso['Cajastransferenciascheque'])) {
                    $detallecheque = "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver detalle cheques' onclick='$(\"#ch\").dialog(\"open\");$(\"#ch\").load(\"" . $this->webroot . "Cajastransferenciascheques/listar/i/" . $cajasingreso['Cajasingreso']['id'] . "\");'/>";
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($cajasingreso['Bancoscuenta']['name']); ?></td>
                    <td><?php echo h($cajasingreso['Consorcio']['name']); ?></td>
                    <td><?php echo h($cajasingreso['Caja']['name']); ?></td>
                    <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $cajasingreso['Cajasingreso']['created']) ?>'><span class="fecha"><?php echo $this->Time->format(__('d/m/Y'), $cajasingreso['Cajasingreso']['fecha']) ?></span>&nbsp;</td>
                    <td><span class="concepto"><?php echo h($cajasingreso['Cajasingreso']['concepto']) ?></span>&nbsp;</td>
                    <td><span class="importe"><?php echo $this->Functions->money($cajasingreso['Cajasingreso']['importe']) ?></span>&nbsp;</td>
                    <td><span class="importe"><?php echo $this->Functions->money($cajasingreso['Cajasingreso']['cheque']) . $detallecheque ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        if (!$cajasingreso['Cajasingreso']['anulado'] && $cajasingreso['Cajasingreso']['bancoscuenta_id'] == 0 && empty($cajasingreso['Cajasingreso']['cobranza_id']) && $cajasingreso['Cajasingreso']['user_id'] == $_SESSION['Auth']['User']['id'] && $cajasingreso['Cajasingreso']['movimientoasociado'] == 0 && !$cajasingreso['Cajasingreso']['estransferencia']) {
                            if (substr($cajasingreso['Cajasingreso']['concepto'], 0, 5) !== "ADChT") {
                                echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $cajasingreso['Cajasingreso']['id']), ['escapeTitle' => false], __('Desea anular el movimiento # %s?', h($cajasingreso['Cajasingreso']['concepto'])));
                            }
                        }
                        if (!empty($cajasingreso['Cajasingreso']['cobranza_id'])) {
                            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Cobranzas', 'action' => 'view', $cajasingreso['Cajasingreso']['cobranza_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
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