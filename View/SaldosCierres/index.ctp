<div class="imprimir saldosCierres index">
    <h2><?php echo __('Saldos cierres'); ?></h2>
    <?php
    echo $this->Form->create('SaldosCierre', ['class' => 'inline']);
    echo $this->JqueryValidation->input('liquidation_id', ['label' => '', 'empty' => '']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    if (empty($saldosCierres)) {
        echo "<div class='info'>Seleccione una liquidaci&oacute;n</div>";
    } else {
        ?>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo __('Liquidación') ?></th>
                    <th><?php echo __('Propietario') ?></th>
                    <th><?php echo __('Capital') ?></th>
                    <th><?php echo __('Interés') ?></th>
                    <th><?php echo __('Redondeo') ?></th>
                    <th><?php echo __('Total (C + I - R)') ?></th>
                    <th><?php echo __('Cobranzas') ?></th>
                    <th><?php echo __('Ajustes') ?></th>
                    <th><?php echo __('GG') ?></th>
                    <th><?php echo __('GP') ?></th>
                    <th><?php echo __('IA') ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($saldosCierres)) {
                    $i = 0;
                    foreach ($saldosCierres as $saldosCierre):
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr<?php echo $class; ?>>
                            <td class="borde_tabla"></td>
                            <td><?php echo h($saldosCierre['Liquidation']['name']) ?></td>
                            <td><?php echo h($saldosCierre['Propietario']['name']) ?></td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['capital']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['interes']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money(-$saldosCierre['SaldosCierre']['redondeo']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['capital'] + $saldosCierre['SaldosCierre']['interes'] - $saldosCierre['SaldosCierre']['redondeo']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money(-$saldosCierre['SaldosCierre']['cobranzas']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money(-$saldosCierre['SaldosCierre']['ajustes']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['gastosgenerales']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['gastosparticulares']) ?>&nbsp;</td>
                            <td><?php echo $this->Functions->money($saldosCierre['SaldosCierre']['interesactual']) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="11"></td>
                        <td class="bottom_d"></td>
                    </tr>
                    <?php
                }
                ?>
        </table>
    </div>
    <?php
}
?>
<script>
    $(function () {
        $("#SaldosCierreLiquidationId").select2({language: "es", width: 600, placeholder: '<?= __('Seleccione una liquidacion...') ?>'});
    });
</script>