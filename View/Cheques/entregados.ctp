<div class="cheques index">
    <h2><?php echo __('Cheques entregados a Proveedor'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Cheque', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('proveedor_id', ['label' => false, 'empty' => '', 'type' => 'select', 'selected' => isset($this->request->data['Cheque']['proveedor_id']) ? $this->request->data['Cheque']['proveedor_id'] : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d, 'required' => 'required']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h, 'required' => 'required']);
    echo $this->Form->input('anulado', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => false, 'print' => true, 'model' => 'Cheque']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('proveedorspago_id', __('Proveedor')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_emision', __('Emisión')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha_vencimiento', __('Vencimiento')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('banconumero', __('Número')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="acciones" style='width:50px'><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($cheques as $cheque):
                $class = $cheque['Cheque']['anulado'] ? ' class="error-message"' : null;
                if ($i++ % 2 == 0) {
                    $class = $cheque['Cheque']['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($cheque['Proveedor']['name']); ?></td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_emision']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_vencimiento']) ?>&nbsp;</td>
                    <td><?php echo h($cheque['Cheque']['concepto']) ?>&nbsp;</td>
                    <td><?php echo h($cheque['Cheque']['banconumero']) ?>&nbsp;</td>
                    <td><?php echo h($cheque['Cheque']['importe']) ?>&nbsp;</td>
                    <td class="acciones" style='width:50px'>
                        <?php
                        echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Proveedorspagos', 'action' => 'view', $cheque['Cheque']['proveedorspago_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="7"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#ChequeProveedorId").select2({language: "es", allowClear: true, placeholder: 'Seleccione Proveedor'});
    });
</script>
<style>
    .checkbox{
        width:150px !important;
    }
</style>