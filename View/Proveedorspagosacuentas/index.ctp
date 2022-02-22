<style>
    .busc{
        width:70px !important;
        margin:-15px !important;
        /*z-index:0;*/
    }
    .busc input[type="text"]{
        width:50px !important;
    }
    #busqform{
        /*margin-left:200px;*/
    }
</style>
<div class="proveedorspagosacuentas index">
    <h2><?php echo __('Pagos a cuenta a Proveedores pendientes de aplicar'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0 ; z-index 0'>";
    echo $this->Form->create('Proveedorspagosacuenta', ['class' => 'inline', 'method' => 'post']);
    echo $this->JqueryValidation->input('proveedor_id', ['label' => false, 'empty' => '', 'options' => $proveedores, 'required' => false, 'type' => 'select', 'selected' => isset($p) ? $p : '']);
    echo $this->JqueryValidation->input('consorcio_id', ['label' => false, 'empty' => '', 'options' => $consorcios, 'required' => false, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => !empty($d) ? $d : date('d/m/Y')]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => !empty($h) ? $h : date('d/m/Y')]);
    echo $this->Form->input('incluiraplicados', ['label' => __('Incluir aplicados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;margin-left:-15px']);
    echo '</div>';
    echo "<div style='position:absolute;top:108px;left:90%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Proveedorspagosacuenta']) . "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th style="width:50px"><?= __('#') ?></th>
                <th><?php echo $this->Paginator->sort('proveedor_id', __('Proveedor')); ?></th>
                <th><?= __('Fecha') ?></th>
                <th><?= __('Creado') ?></th>
                <th style="text-align:center"><?= __('Aplicado') ?></th>
                <th style="text-align:right"><?= __('Importe') ?></th>
                <th class="acciones" style="width:50px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = $importe = 0;
            foreach ($proveedorspagosacuentas as $proveedorspagosacuenta):
                if (!isset($consorcios[$proveedorspagosacuenta['Proveedorspagosacuenta']['consorcio_id']])) {
                    continue;
                }
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                $importe += $proveedorspagosacuenta['Proveedorspagosacuenta']['importe'];
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($proveedorspagosacuenta['Proveedorspago']['numero']) ?></td>
                    <td><?php echo h($proveedorspagosacuenta['Proveedor']['name']) . " (" . h($consorcios[$proveedorspagosacuenta['Proveedorspagosacuenta']['consorcio_id']]) . ")" ?></td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $proveedorspagosacuenta['Proveedorspago']['fecha']) ?></td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $proveedorspagosacuenta['Proveedorspago']['created']) ?></td>
                    <td style="text-align:center">
                        <?php
                        if ($proveedorspagosacuenta['Proveedorspagosacuenta']['proveedorspagoaplicado_id'] != 0) {
                            echo $this->Html->link($this->Html->image('1.png', ['title' => __('Ver Pago a cuenta aplicado')]), ['controller' => 'Proveedorspagos', 'action' => 'view', $proveedorspagosacuenta['Proveedorspagosacuenta']['proveedorspagoaplicado_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        } else {
                            echo $this->Html->image('0.png', array('title' => __('Pago a cuenta sin Aplicar')));
                        }
                        ?>
                    </td>
                    <td style="text-align:right"><?php echo $this->Functions->money($proveedorspagosacuenta['Proveedorspagosacuenta']['importe']) ?>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver Pago a Proveedor Asociado')]), ['controller' => 'Proveedorspagos', 'action' => 'view', $proveedorspagosacuenta['Proveedorspago']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="borde_tabla"></td>
                <td colspan="4">&nbsp;</td>
                <td style="font-weight:bold;text-align:right">TOTAL</td>
                <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($importe) ?>&nbsp;</td>
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
<script>
    $(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#ProveedorspagosacuentaProveedorId").select2({language: "es", placeholder: "<?= __('Seleccione proveedor...') ?>", allowClear: true});
        $("#ProveedorspagosacuentaConsorcioId").select2({language: "es", placeholder: "<?= __('Seleccione consorcio...') ?>", allowClear: true});
        $(".busc input[type='text']").attr('placeholder', '####');
    });
    $("#ProveedorspagosacuentaIndexForm").submit(function (event) {
        if ($("#ProveedorspagosacuentaProveedorId").val() === "" && $("#ProveedorspagosacuentaConsorcioId").val() === "") {
            alert("Debe seleccionar Proveedor o Consorcio");
            return false;
        }
        return true;
    });
</script>