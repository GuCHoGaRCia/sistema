<div class="cobranzas index">
    <h2><?php echo __('Listado de Cobranzas'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Cobranza', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select', 'selected' => isset($c) ? $c : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => !empty($d) ? $d : '']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => !empty($h) ? $h : '']);
    echo $this->Form->input('buscar', ['label' => '', 'style' => 'width:85px', 'placeholder' => __('Buscar'), 'value' => !empty($b) ? $b : '']);
    echo $this->Form->input('anulada', ['label' => __('Ver anuladas?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;margin-left:-15px']);
    echo "<div style='position:absolute;top:110px;right:150px'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => false, 'print' => false, 'multidelete' => true, 'model' => 'Cobranza']) . "</div>";
    echo "</div>";
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            COBRANZAS
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('numero', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                    <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                    <th><?php echo $this->Paginator->sort('user_id', __('Usuario')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th><?php echo $this->Paginator->sort('amount', __('Importe')); ?></th>
                    <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?><span class='iom' onclick="mdtoggle()"> <?= $this->Html->image('sa.png', ['title' => 'Anular múltiples registros', 'style' => 'width:20px']) ?></span></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = $total = 0;
                foreach ($cobranzas as $cobranza):
                    $class = $cobranza['Cobranza']['anulada'] ? ' class="error-message"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $cobranza['Cobranza']['anulada'] ? ' class="altrow error-message"' : ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($cobranza['Cobranza']['numero']); ?>&nbsp;&nbsp;&nbsp;</td>
                        <td><?php echo h($cobranza['Consorcio']['name']); ?></td>
                        <td><?php echo h($cobranza['Propietario']['name'] . " - " . $cobranza['Propietario']['unidad'] . " (" . $cobranza['Propietario']['code'] . ")") ?></td>
                        <td><?php echo h($cobranza['User']['name']); ?></td>
                        <td title='<?php echo 'Creada el ' . $this->Time->format(__('d/m/Y H:i:s'), $cobranza['Cobranza']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $cobranza['Cobranza']['fecha']) ?>&nbsp;&nbsp;</td>
                        <td><?php echo h($cobranza['Cobranza']['recibimosde'] . " " . $cobranza['Cobranza']['concepto']); ?></td>
                        <td><?php echo $this->Functions->money($cobranza['Cobranza']['amount']) ?>&nbsp;</td>
                        <td class="acciones" style="width:100px">
                            <?php
                            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $cobranza['Cobranza']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                            //echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $cobranza['Cobranza']['id'])));
                            // mostrar o no el delete?
                            // verifico si la fecha de la cobranza esta incluida en una liquidacion ya bloqueada, en ese caso, no dejo eliminar
                            $permitoeliminar = true;
                            if (!empty($cobranza['Cobranzatipoliquidacione'])) {
                                foreach ($cobranza['Cobranzatipoliquidacione'] as $n => $n1) {
                                    if (isset($bloqueadas[$n1['liquidations_type_id']][$cobranza['Propietario']['consorcio_id']]) && strtotime($cobranza['Cobranza']['created']) < $bloqueadas[$n1['liquidations_type_id']][$cobranza['Propietario']['consorcio_id']]) {
                                        $permitoeliminar = false;
                                        break;
                                    }
                                }
                            }
                            if ($cobranza['Consorcio']['habilitado'] && !$cobranza['Cobranza']['anulada'] && ($cobranza['User']['id'] == $_SESSION['Auth']['User']['id'] || $_SESSION['Auth']['User']['eliminacobranzas']) && $permitoeliminar) {// si no esta anulada y la hizo el usuario actual (o tiene permisos para eliminar)
                                echo $this->Form->postLink($this->Html->image('undo.png', array('title' => __('Anular'))), array('action' => 'delete', $cobranza['Cobranza']['id']), ['escapeTitle' => false], __('Desea anular la cobranza # %s?', h(empty($cobranza['Cobranza']['concepto']) ? $cobranza['Cobranza']['recibimosde'] : $cobranza['Cobranza']['concepto'])));
                                echo $this->Form->input('borrado', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $cobranza['Cobranza']['id'], 'style' => 'box-shadow:none;transform: scale(1.8);margin:6px;position:absolute']);
                            }
                            ?>
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!$cobranza['Cobranza']['anulada']) {
                        $total += $cobranza['Cobranza']['amount'];
                    }
                endforeach;
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="5">&nbsp;</td><td style="border-top:2px solid black;font-weight:bold;text-align:right">TOTAL&nbsp;&nbsp;</td>
                    <td style="border-top:2px solid black;font-weight:bold;text-align:left"><?= $this->Functions->money($total) ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="8"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
        <?php
        if (empty($this->request->data)) {
            echo $this->element('pagination');
        }
        ?>
    </div>
</div>
<script>$(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#CobranzaConsorcio").select2({language: "es", allowClear: true, placeholder: '<?= __('Consorcio...') ?>', width: 250});
    });
</script>
<style>
    .busc{
        margin:-5px -100px !important;
    }
    .busc input[type="text"]{
        width:70px !important;
    }
    #busqform{
        margin-left:0px;
    }
</style>