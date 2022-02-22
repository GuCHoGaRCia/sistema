<?php echo $this->Html->css(['bootstrap-editable.css'], 'stylesheet', ['inline' => false]); ?>
<div class="ajustes index">
    <h2><?php echo __('Ajustes'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Ajuste', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select', 'selected' => isset($c) ? $c : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Fecha Desde'), 'value' => !empty($d) ? $d : date('d/m/Y')]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Fecha Hasta'), 'value' => !empty($h) ? $h : date('d/m/Y')]);
    echo $this->Form->input('anulado', ['label' => __('Ver anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:90%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Ajuste']) . "</div>";
    echo "</div>";
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            AJUSTES
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                    <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                    <th><?php echo $this->Paginator->sort('user_id', __('Usuario')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                    <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = $total = 0;
                foreach ($ajustes as $ajuste):
                    $class = $ajuste['Ajuste']['anulado'] ? ' class="error-message"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $ajuste['Ajuste']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($ajuste['Consorcio']['name']); ?></td>
                        <td><?php echo $this->Html->link($ajuste['Propietario']['name'], ['controller' => 'Propietarios', 'action' => 'view', $ajuste['Propietario']['id']]); ?></td>
                        <td><?php echo h($ajuste['User']['name']); ?></td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $ajuste['Ajuste']['fecha']) ?>&nbsp;</td>
                        <td><?php echo h($ajuste['Ajuste']['concepto']) ?>&nbsp;</td>
                        <td><?php echo h($ajuste['Ajuste']['importe']) ?>&nbsp;</td>
                        <td class="acciones" style="width:auto">
                            <?php
                            // verifico si la fecha del ajuste esta incluida en una liquidacion ya bloqueada, en ese caso, no dejo eliminar
                            $permitoeliminar = true;
                            if (!empty($ajuste['Ajustetipoliquidacione'])) {
                                foreach ($ajuste['Ajustetipoliquidacione'] as $n => $n1) {
                                    if (isset($bloqueadas[$n1['liquidations_type_id']][$ajuste['Propietario']['consorcio_id']]) && strtotime($ajuste['Ajuste']['created']) < $bloqueadas[$n1['liquidations_type_id']][$ajuste['Propietario']['consorcio_id']]) {
                                        $permitoeliminar = false;
                                        break;
                                    }
                                }
                            }
                            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $ajuste['Ajuste']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                            if ($ajuste['Consorcio']['habilitado'] && !$ajuste['Ajuste']['anulado'] && $ajuste['User']['id'] == $_SESSION['Auth']['User']['id'] && $permitoeliminar) {// si no esta anulada y la hizo el usuario actual
                                echo $this->Form->postLink($this->Html->image('undo.png', ['alt' => __('Anular'), 'title' => __('Anular')]), ['action' => 'delete', $ajuste['Ajuste']['id']], ['escapeTitle' => false], __('Desea anular el ajuste # %s?', $ajuste['Ajuste']['concepto']));
                            }
                            ?>
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    $total += $ajuste['Ajuste']['importe'];
                endforeach;
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4">&nbsp;</td><td style="border-top:2px solid black;font-weight:bold;text-align:right">TOTAL&nbsp;&nbsp;</td>
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
        <?php
        if (!$this->request->is('post')) {
            echo $this->element('pagination');
        }
        ?>
    </div>
</div>
<script>$(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#AjusteConsorcio").select2({language: "es", allowClear: true, placeholder: '<?= __('Consorcio...') ?>'});
    });
</script>
<style>
    .busc{
        margin:-5px -380px !important;
    }
    .busc input[type="text"]{
        width:70px !important;
    }
    #busqform{
        margin-left:180px;
    }
</style>