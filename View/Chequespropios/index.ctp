<div class="chequespropios index">
    <h2><?php echo __('Cheques Propios'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Chequespropio', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('cuenta', ['label' => false, 'empty' => '', 'options' => [0 => __('Todas')] + $cuentas, 'type' => 'select', 'selected' => isset($this->request->data['Chequespropio']['cuenta']) ? $this->request->data['Chequespropio']['cuenta'] : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h]);
    echo $this->Form->input('anulado', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->JqueryValidation->input('buscar', ['label' => false, 'type' => 'text', 'style' => 'width:90px', 'placeholder' => 'Número...', 'value' => h($b)]);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'model' => 'Chequespropio']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('user_id', __('Usuario')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_emision', __('Fecha emisión')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_vencimiento', __('Fecha vencimiento')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('numero', __('Número')); ?></th>
                <th style='text-align:right'><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="acciones" style="width:70px">&nbsp;<?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = $total = 0;
            foreach ($chequespropios as $chequespropio):
                $class = $chequespropio['Chequespropio']['anulado'] ? ' class="error-message"' : null;
                if ($i++ % 2 == 0) {
                    $class = $chequespropio['Chequespropio']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($chequespropio['Bancoscuenta']['name']) ?>&nbsp;</td>
                    <td><?php echo h($chequespropio['User']['name']); ?></td>
                    <?php
                    if (!$chequespropio['Chequespropio']['anulado'] && $chequespropio['Chequespropio']['proveedorspago_id'] == 0) {
                        ?>
                        <td><span class="fecha_emision" data-value="<?php echo h($chequespropio['Chequespropio']['fecha_emision']) ?>" data-pk="<?php echo h($chequespropio['Chequespropio']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropio']['fecha_emision']) ?></span>&nbsp;</td>
                        <td><span class="fecha_vencimiento" data-value="<?php echo h($chequespropio['Chequespropio']['fecha_vencimiento']) ?>" data-pk="<?php echo h($chequespropio['Chequespropio']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropio']['fecha_vencimiento']) ?></span>&nbsp;</td>
                        <td><span class="concepto" data-value= "<?php echo h($chequespropio['Chequespropio']['concepto']) ?>" data-pk = "<?php echo h($chequespropio['Chequespropio']['id']) ?>"><?php echo h($chequespropio['Chequespropio']['concepto']) ?></span>&nbsp;</td>
                        <td><span class="numero" data-value= "<?php echo h($chequespropio['Chequespropio']['numero']) ?>" data-pk = "<?php echo h($chequespropio['Chequespropio']['id']) ?>"><?php echo h($chequespropio['Chequespropio']['numero']) ?></span>&nbsp;</td>
                        <td style='text-align:right'><span class="importe" data-value= "<?php echo h($chequespropio['Chequespropio']['importe']) ?>" data-pk = "<?php echo h($chequespropio['Chequespropio']['id']) ?>"><?php echo h($chequespropio['Chequespropio']['importe']) ?></span>&nbsp;</td>
                        <?php
                    } else {
                        ?>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropio']['fecha_emision']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropio']['fecha_vencimiento']) ?>&nbsp;</td>
                        <td><?php echo h($chequespropio['Chequespropio']['concepto']) ?>&nbsp;</td>
                        <td><?php echo h($chequespropio['Chequespropio']['numero']) ?></td>
                        <td style='text-align:right'><?php echo $this->Functions->money(h($chequespropio['Chequespropio']['importe'])) ?>&nbsp;</td>
                        <?php
                    }
                    ?>
                    <td class="acciones" style="width:70px">&nbsp;
                        <?php
                        if (!$chequespropio['Chequespropio']['anulado'] && $chequespropio['Chequespropio']['proveedorspago_id'] != 0) {
                            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Proveedorspagos', 'action' => 'view', $chequespropio['Chequespropio']['proveedorspago_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        }
                        if (!$chequespropio['Chequespropio']['anulado'] && $chequespropio['Chequespropio']['proveedorspago_id'] == 0) {
                            echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $chequespropio['Chequespropio']['id']), ['escapeTitle' => false], __('Desea anular el Cheque propio # %s?', h($chequespropio['Chequespropio']['concepto'])));
                        }
                        if (!empty($chequespropio['Chequespropiosadm'])) {// muestro el detalle de Cheques Propios de Administracion
                            ?>
                            <span class="contenedorreportes">
                                <?php
                                echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                                ?>
                                <span class="listareportes" style="width:300px;font-size:10px;right:150px">
                                    <ul>
                                        <li>
                                            <?php
                                            foreach ($chequespropio['Chequespropiosadm'] as $jj) {
                                                echo h($consorcio_id[$jj['consorcio_id']]) . ": " . $this->Functions->money($jj['importe']) . "<br>";
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                </span>
                            </span>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
                $total += $chequespropio['Chequespropio']['importe'];
            endforeach;
            ?>
            <tr>
                <td class="borde_tabla"></td>
                <td colspan="5">&nbsp;</td><td style="border-top:2px solid black;font-weight:bold;text-align:right">TOTAL</td>
                <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($total) ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="borde_tabla"></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="8"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>$(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#ChequespropioCuenta").select2({language: "es", placeholder: '<?= __('Consorcio...') ?>'});
        $('.concepto').editable({type: 'text', name: 'concepto', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Chequespropios/editar', placement: 'right'});
        $('.fecha_emision').editable({type: 'date', name: 'fecha_emision', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Chequespropios/editar', placement: 'right'});
        $('.fecha_vencimiento').editable({type: 'date', name: 'fecha_vencimiento', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Chequespropios/editar', placement: 'right'});
        $('.numero').editable({type: 'text', name: 'numero', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Chequespropios/editar', placement: 'left'});
        $('.importe').editable({type: 'text', name: 'importe', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Chequespropios/editar', placement: 'left'});
    });
</script>
<style>
    .checkbox{
        width:150px !important;
    }
</style>