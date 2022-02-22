<style>
    .checkbox{
        width:150px !important;
    }
    label{
        width:285px !important;
        max-width:285px;
    }
</style>
<div class="cheques index">
    <h2><?php echo __('Cheques de terceros'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Cheque', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'options' => [0 => __('Todos')] + $consorcios, 'type' => 'select', 'selected' => isset($this->request->data['Cheque']['consorcio']) ? $this->request->data['Cheque']['consorcio'] : 0]);
    echo $this->Form->input('anulado', ['label' => "Incluir anulados / sin cobranza asignada", 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'margin-left:130px;width:50px']);
    // no permito crear cheques de terceros x aca, solo desde Cobranzas
    //echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'model' => 'Cheque']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('caja_id', __('Caja')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_emision', __('Fecha de emisión')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_vencimiento', __('Fecha de vencimiento')); ?></th>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('banconumero', __('Banco/Número')); ?></th>
                <th style="text-align:right"><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th style="text-align:right"><?php echo $this->Paginator->sort('saldo', __('Saldo')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = $total = $echeq = 0;
            foreach ($cheques as $cheque):
                $class = $cheque['Cheque']['anulado'] ? ' class="error-message"' : null;
                if ($i++ % 2 == 0) {
                    $class = $cheque['Cheque']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                }
                $total += $cheque['Cheque']['importe'];
                if (!$cheque['Cheque']['fisico']) {
                    $echeq += $cheque['Cheque']['importe'];
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($cheque['Caja']['name']); ?></td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_emision']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_vencimiento']) ?>&nbsp;</td>
                    <td><?php echo isset($consorcios[$cheque['Propietario']['consorcio_id']]) ? h($consorcios[$cheque['Propietario']['consorcio_id']]) : '<b>sin asignar a cobranza</b>' ?>&nbsp;</td>
                    <td><?php echo ($cheque['Cheque']['fisico'] ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ') . h($cheque['Cheque']['concepto']) ?>&nbsp;</td>
                    <td><?php echo h($cheque['Cheque']['banconumero']) ?>&nbsp;</td>
                    <td style="text-align:right"><?php echo h($cheque['Cheque']['importe']) ?>&nbsp;</td>
                    <td style="text-align:right"><?php echo h($cheque['Cheque']['saldo']) ?>&nbsp;</td>
                    <td class="acciones" style="width:100px">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open'); $('#rc').html('<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'" . $this->webroot . "img/loading.gif" . "\'/></div>'); $('#rc').load('" . $this->webroot . "Cheques/view/" . $cheque['Cheque']['id'] . "/1" . "');"]);
                        if ($cheque['Caja']['user_id'] == $_SESSION['Auth']['User']['id'] /* && !$cheque['Cheque']['anulado'] && !$cheque['Cheque']['depositado'] && $cheque['Cheque']['importe'] == $cheque['Cheque']['saldo'] */ && $sepuedeanular[$cheque['Cheque']['id']]) {
                            // es un cheque perteneciente a la caja del usuario actual, puede anularlo
                            echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $cheque['Cheque']['id']), array('escapeTitle' => false), __('Desea anular el cheque # %s?', h($cheque['Cheque']['concepto'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
            endforeach;
            if (isset($this->request->data['Cheque']['consorcio'])) {
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan='6'>&nbsp;</td>
                    <td style='text-align:right'><b>Físicos: $ <?= $this->Functions->money($total - $echeq) ?></b></td>
                    <td colspan='2'>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan='6'>&nbsp;</td>
                    <td style='text-align:right'><b>Echeq: $ <?= $this->Functions->money($echeq) ?></b></td>
                    <td colspan='2'>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan='6'>&nbsp;</td>
                    <td style='border-top:2px solid black;text-align:right'><b>Total: $ <?= $this->Functions->money($total) ?></b></td>
                    <td colspan='2'>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
            }
            ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="9"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        var dialog = $("#rc").dialog({
            autoOpen: false, height: "auto", width: "950", maxWidth: "950",
            position: {at: "center top"},
            closeOnEscape: true,
            modal: true, buttons: {
                Cerrar: function () {
                    $("#rc").html('');
                    dialog.dialog("close");
                }
            }
        });
        $("#ChequeConsorcio").select2({language: "es", placeholder: '<?= __('Seleccione consorcio...') ?>', allowClear: true});
    });
</script>
<?=
"<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC  ?>