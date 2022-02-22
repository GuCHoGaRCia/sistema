<div class="cheques index">
    <h2><?php echo __('Cheques depositados'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $cuentasBancarias, 'field' => 'cuenta'], 'pagesearch' => true, 'pagenew' => false, 'print' => true, 'model' => 'Cheque')); ?>
    <div id="seccionaimprimir" style='width:100%;font-size:14px !important'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            CHEQUES DEPOSITADOS - 
            <?php
            echo h((isset($this->request->data['filter']['cuenta']) ? $cuentasBancarias[$this->request->data['filter']['cuenta']] : 'Todas las Cuentas'));
            ?>
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('caja_id', __('Caja')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha_emision', __('Emisión')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha_vencimiento', __('Vencimiento')); ?></th>
                    <th><?php echo $this->Paginator->sort('Bancosdepositoscheque.fecha', __('Depósito')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                    <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                    <th><?php echo $this->Paginator->sort('saldo', __('Saldo')); ?></th>
                    <th class="acciones" style='width:50px'><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ($cheques as $cheque):
                    $class = $cheque['Bancosdepositoscheque']['anulado'] ? ' class="error-message"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $cheque['Bancosdepositoscheque']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($cheque['Caja']['name']); ?></td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_emision']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_vencimiento']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Bancosdepositoscheque']['fecha']) ?>&nbsp;</td>
                        <td><?php echo h($cheque['Cheque']['concepto'] . ' - ' . $cheque['Bancosdepositoscheque']['concepto']) ?>&nbsp;</td>
                        <td><?php echo h($cheque['Consorcio']['name'] . ' - ' . $cheque['Bancoscuenta']['name']) ?>&nbsp;</td>
                        <td><?php echo h($cheque['Cheque']['importe']) ?>&nbsp;</td>
                        <td><?php echo h($cheque['Cheque']['saldo']) ?>&nbsp;</td>
                        <td class="acciones" style='width:50px'>
                            <span class="contenedorreportes">
                                <?php
                                echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                                ?>
                                <span class="listareportes">
                                    <ul>
                                        <li>
                                            <a <?php echo $this->Html->image('view.png', ['style' => 'cursor:pointer', 'onclick' => "$('#rcd').dialog('open'); $('#rcd').html('<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'" . $this->webroot . "img/loading.gif" . "\'/></div>'); $('#rcd').load('" . $this->webroot . "Cheques/view/" . $cheque['Cheque']['id'] . "/1" . "');"]); ?>Movimientos del cheque</a>
                                        </li>
                                    </ul>
                                </span>
                            </span> 
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="9"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
    </div>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        var dialog = $("#rcd").dialog({
            autoOpen: false, height: "auto", width: "950", maxWidth: "950",
            position: {at: "center top"},
            closeOnEscape: true,
            modal: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog).hide();
            },
            buttons: {
                Cerrar: function () {
                    $("#rcd").html('');
                    dialog.dialog("close");
                }
            }
        });
    });
</script>
<?=
"<div id='rcd' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RCD  ?>