<div class="chequespropios index">
    <h2><?php echo __('Cheques Propios de Administración'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Chequespropiosadm', ['class' => 'inline', 'id' => 'noimprimir']);
    //echo $this->Form->input('cuenta', ['label' => false, 'empty' => '', 'options' => [0 => __('Todas')] + $cuentas, 'type' => 'select', 'selected' => isset($this->request->data['Chequespropiosadm']['cuenta']) ? $this->request->data['Chequespropiosadm']['cuenta'] : 0]);
    echo $this->Form->input('consorcio_id', ['label' => false, 'empty' => '', 'type' => 'select', 'selected' => isset($this->request->data['Chequespropiosadm']['consorcio_id']) ? $this->request->data['Chequespropiosadm']['consorcio_id'] : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d, 'required' => 'required']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h, 'required' => 'required']);
    echo $this->Form->input('anulado', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => false, 'print' => true, 'model' => 'Chequespropiosadm']) . "</div>";
    echo "</div>";
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            CHEQUES PROPIOS ADMINISTRACION - 
            <?php
            echo h((isset($this->request->data['Chequespropiosadm']['consorcio_id']) && isset($consorcios[$this->request->data['Chequespropiosadm']['consorcio_id']]) ? $consorcios[$this->request->data['Chequespropiosadm']['consorcio_id']] : 'Todos los Consorcios'));
            ?>
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('user_id', __('Usuario')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha_emision', __('Fecha emisión')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha_vencimiento', __('Fecha vencimiento')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th><?php echo $this->Paginator->sort('numero', __('Número')); ?></th>
                    <th style='text-align:right'><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                    <th class="acciones" style="width:auto">&nbsp;<?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = $total = 0;
                foreach ($chequespropios as $chequespropio):
                    //echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Chequespropiosadms', 'action' => 'getInfo', $chequespropio['Chequespropiosadm']['id']], ['target' => '_blank', 'escape' => false]);
                    $class = $chequespropio['Chequespropiosadm']['anulado'] ? ' class="error-message"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $chequespropio['Chequespropiosadm']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($chequespropio['User']['name']); ?></td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropiosadm']['fecha_emision']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $chequespropio['Chequespropiosadm']['fecha_vencimiento']) ?>&nbsp;</td>
                        <td><?php echo h($chequespropio['Chequespropiosadm']['concepto']) ?>&nbsp;</td>
                        <td><?php echo h($chequespropio['Chequespropiosadm']['numero']) ?>&nbsp;</td>
                        <td style='text-align:right'><?php echo $this->Functions->money(h($chequespropio['Chequespropiosadm']['importe'])) ?>&nbsp;</td>
                        <td class="acciones" style="width:auto">&nbsp;
                            <?php
                            $id = isset($chequespropio['Chequespropiosadmsdetalle'][0]['proveedorspago_id']) ? $chequespropio['Chequespropiosadmsdetalle'][0]['proveedorspago_id'] : 0;
                            if (!$chequespropio['Chequespropiosadm']['anulado'] && $id != 0) {
                                echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Proveedorspagos', 'action' => 'view2', $id], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                            }
                            if (!$chequespropio['Chequespropiosadm']['anulado'] && $id == 0 && $chequespropio['User']['id'] == $_SESSION['Auth']['User']['id']) {
                                echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $chequespropio['Chequespropiosadm']['id']), ['escapeTitle' => false], __('Desea anular el Cheque propio # %s?', h($chequespropio['Chequespropiosadm']['concepto'])));
                            }
                            if (!empty($chequespropio['Chequespropiosadmsdetalle'])) {// muestro el detalle de Cheques Propios de Administracion
                                ?>
                                <span class="contenedorreportes">
                                    <?php
                                    echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                                    ?>
                                    <span class="listareportes" style="width:auto;font-size:12px;right:200px;line-height:13px">
                                        <ul>
                                            <li>
                                                <?php
                                                foreach ($chequespropio['Chequespropiosadmsdetalle'] as $jj) {
                                                    echo h($cuentas[$jj['bancoscuenta_id']]) . ": <b>" . $this->Functions->money($jj['importe']) . "</b><br>";
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
                    $total += $chequespropio['Chequespropiosadm']['importe'];
                endforeach;
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4">&nbsp;</td><td style="border-top:2px solid black;font-weight:bold;text-align:right">TOTAL</td>
                    <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($total) ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="7"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
<script>$(document).ready(function(){
    $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    $('.fecha_emision').editable({type:'text', name:'fecha_emision', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'right'}); $('.fecha_vencimiento').editable({type:'text', name:'fecha_vencimiento', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'right'}); $('.concepto').editable({type:'text', name:'concepto', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'right'}); $('.importe').editable({type:'text', name:'importe', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'right'}); $('.saldo').editable({type:'text', name:'saldo', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'left'}); $('.anulado').editable({type:'text', name:'anulado', success:function(n){if (n){return n}}, url:'<?php echo $this->webroot; ?>Chequespropiosadms/editar', placement:'left'});
    $("#ChequespropiosadmConsorcioId").select2({language: "es", allowClear:true, placeholder: '<?= __('Seleccione Consorcio...') ?>'});
    });
</script>
<style>
    .checkbox{
        width:150px !important;
    }
</style>