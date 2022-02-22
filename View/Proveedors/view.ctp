<div class="cajas index">
    <h2><?php echo __('Movimientos Proveedor'); ?></h2>
    <?php
    echo "<div id='noimprimir'>";
    echo $this->Form->create('Proveedor', ['class' => 'inline']);
    echo $this->JqueryValidation->input('proveedores', ['label' => false, 'empty' => '', 'options' => $proveedores, 'type' => 'select', 'selected' => isset($p['Proveedor']['id']) ? $p['Proveedor']['id'] : 0]);
    echo $this->JqueryValidation->input('consorcios', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde')]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta')]);
    echo $this->Form->input('incluye_pagas', ['label' => __('Incluir pagas y NC?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.5);border:1px solid grey']);
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;']);
    echo $this->Form->end(__('Ver'));
    echo "</dvi>";
    if (empty($movimientos['saldos'])) {
        echo "<div class='info'>El Proveedor seleccionado no posee facturas pendientes</div>";
    } else {
        ?>
        <div id="seccionaimprimir" style='width:100%'>
            <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                <?php
                echo __('Movimientos Proveedor') . ' "' . h($p['Proveedor']['name']) . '"' . (isset($this->request->data['Proveedor']['desde']) && !empty($this->request->data['Proveedor']['desde']) ? 'Desde el ' . $this->request->data['Proveedor']['desde'] . '. ' : '') .
                ' - Saldo: <span ' . ($p['Proveedor']['saldo'] <= 0 ? 'class="success-message"' : 'class="error-message"') . ">" . $this->Functions->money($p['Proveedor']['saldo']) . '</span>';
                ?>
            </div>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th style="width:50px"><?php echo __('#') ?></th>
                        <th style="width:70px"><?php echo __('Tipo') ?></th>
                        <th style="width:80px"><?php echo __('Fecha') ?></th>
                        <th><?php echo __('Consorcio') ?></th>
                        <th><?php echo __('Concepto') ?></th>
                        <th style='text-align:right'><?php echo __('Debe') ?></th>
                        <th style='text-align:right'><?php echo __('Haber') ?></th>
                        <th style='text-align:right'><?php echo __('Saldo') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $tipos = [1 => __('Ingreso'), 2 => __('Egreso')/* , 3 => __('Depósito'), 4 => __('Extracción') */];
                    $saldop = $p['Proveedor']['saldo'];
                    $detalleingresos = [];
                    if (isset($movimientos['di'])) {
                        $detalleingresos = $movimientos['di'];
                        unset($movimientos['di']);
                    }
                    $totalpagos = $totalfacturas = 0;
                    foreach ($movimientos['saldos'] as $row):
                        $class = isset($row['saldo']) && $row['saldo'] == 0 || $row['tipo'] == '8' ? ' class="success-message"' : (isset($row['saldo']) && $row['saldo'] != abs($row['importe']) ? ' style="color:orange;font-weight:bold"' : ' class="error-message"');
                        if ($i++ % 2 == 0) {
                            $class = isset($row['saldo']) && $row['saldo'] == 0 || $row['tipo'] == '8' ? ' class="altrow success-message"' : (isset($row['saldo']) && $row['saldo'] != abs($row['importe']) ? ' style="color:orange;font-weight:bold"' : ' class="altrow error-message"');
                        }
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <?php
                            if ($row['tipo'] == '8') {// proveedorspago
                                ?>
                                <td><?= h('P ' . $row['numero']) ?></td>
                                <td><?= h('Pago') ?></td>
                                <td><?= $this->Time->format(__('d/m/Y'), $row['fecha']) ?></td>
                                <td><?= !empty($row['Consorcio']['name']) ? h($row['Consorcio']['name'] /* . " - " . $row['Liquidation']['periodo'] */) : '--' ?></td>
                                <td><?= h($row['concepto']) . " " . $this->Html->link($this->Html->image('icon-info.png', ['title' => __("Ver detalle Pago Proveedor")]), ['controller' => 'Proveedorspagos', 'action' => 'view', $row['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) ?></td>
                                <td style='text-align:right'>--</td>
                                <td style='text-align:right'>-<?= $this->Functions->money($row['importe']) ?></td>
                                <td style='text-align:right'><?= $this->Functions->money($saldop) ?></td>
                                <?php
                                $totalpagos -= $row['importe'];
                                $saldop += $row['importe'];
                            } else {// proveedorsfactura (tipo=10)
                                ?>
                                <td><?= h('F. ' . $row['numero']) ?></td>
                                <td><?= h('Factura') ?></td>
                                <td><?= $this->Time->format(__('d/m/Y'), $row['fecha']) ?></td>
                                <td><?= h($row['Consorcio']['name'] /* . " - " . $row['Liquidation']['periodo'] */) ?></td>
                                <td><?= h($row['concepto']) . " " . $this->Html->link($this->Html->image('icon-info.png', ['title' => __("Ver detalle Factura Proveedor")]), ['controller' => 'Proveedorsfacturas', 'action' => 'view', $row['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) ?></td>
                                <td style='text-align:right'><?= $row['importe'] > 0 ? $this->Functions->money($row['importe']) : '' ?></td>
                                <td style='text-align:right'><?= $row['importe'] < 0 ? $this->Functions->money($row['importe']) : '' ?></td>
                                <td style='text-align:right'><?= $this->Functions->money($saldop) ?></td>
                                <?php
                                $totalfacturas += $row['importe'];
                                $saldop -= $row['importe'];
                            }
                            ?>

                            <td class="borde_tabla"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="borde_tabla"></td>
                        <td colspan='5'>&nbsp;</td>
                        <td style='border-top:2px solid black;text-align:right;<?= ($totalfacturas > 0) ? 'color:#C00' : 'color:#4F8A10' ?>'><b><?= $this->Functions->money($totalfacturas) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right;color:#4F8A10'><b><?= $this->Functions->money($totalpagos) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right;<?= ($totalfacturas + $totalpagos > 0) ? 'color:#C00' : 'color:#4F8A10' ?>'><b><?= $this->Functions->money($totalfacturas + $totalpagos) ?></b></td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="9"></td>
                        <td class="bottom_d"></td>
                    </tr>
            </table>
        </div>
    </div>
    <?php
}
?>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
        $("#ProveedorProveedores").select2({language: "es", placeholder: '<?= __("Seleccione proveedor...") ?>'});
        $("#ProveedorConsorcios").select2({language: "es", placeholder: '<?= __("Seleccione consorcio...") ?>', allowClear: true});
    });
    $("#ProveedorViewForm").submit(function (event) {
        var f1 = $("#ProveedorDesde").val();
        var f2 = $("#ProveedorHasta").val();
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            alert('<?= __('La fecha Desde debe ser menor o igual a Hasta') ?>');
            return false;
        }
        return true;
    });
</script>


