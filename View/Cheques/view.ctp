<div class="cajas index" id="seccionaimprimir">
    <h<?= $sinlayout == 0 ? "2" : "4" ?>>Detalle cheque # <?= h($cheque['Cheque']['id'] . " " . $cheque['Cheque']['concepto']) . "" ?></h<?= $sinlayout == 0 ? "2" : "4" ?>>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th style='width:80px'><?php echo __('Fecha') ?></th>
                <th><?php echo __('Caja') ?></th>
                <th><?php echo __('Conceptos') ?></th>
                <th style='text-align:right'><?php echo __('Importe utilizado') ?></th>
                <th style='text-align:right'><?php echo __('Saldo') ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $class = $cheque['Cheque']['anulado'] ? ' class="error-message tachado"' : null;
            if ($i++ % 2 == 0) {
                $class = $cheque['Cheque']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
            }
            $saldo = $cheque['Cheque']['importe'];
            // muestro el saldo inicial del cheque y sus datos
            ?>
            <tr<?php echo $class; ?> style="border-top:1px solid gray">
                <td class="borde_tabla"></td>
                <td><?php echo $this->Time->format(__('d/m/Y'), $cheque['Cheque']['created']) ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?php
                    echo ($cheque['Cheque']['fisico'] ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ') . "Emisi&oacute;n: " . $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_emision']) .
                    " - Vencimiento: " . $this->Time->format(__('d/m/Y'), $cheque['Cheque']['fecha_vencimiento']) .
                    " - N&uacute;mero: " . h($cheque['Cheque']['banconumero']);
                    ?>&nbsp;
                </td>
                <td style='text-align:right'><?= "--" ?>&nbsp;</td>
                <td style='text-align:right'><?= $this->Functions->money($saldo) ?>&nbsp;</td>
                <td class="borde_tabla"></td>
            </tr>
            <?php
            if (!empty($movimientos) && $movimientos !== []) {
                foreach ($movimientos as $row):
                    $class = $cheque['Cheque']['anulado'] ? ' class="error-message tachado"' : null;
                    if ($i++ % 2 == 0) {
                        $class = $cheque['Cheque']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                    }
                    if (isset($row['anulada']) && !$row['anulada']) {
                        $saldo -= $row['Cobranzacheque']['amount'];
                    }
                    if (isset($row['tipo']) && $row['tipo'] == '9') { // Cobranza
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td><?= h($users[$row['user_id']]) ?>&nbsp;</td>
                            <td><?php echo h($row['concepto']) . $this->Html->link($this->Html->image('icon-info.png', ['title' => __("Ver detalle Cobranza")]), ['controller' => 'Cobranzas', 'action' => 'view', $row['Cobranzacheque']['cobranza_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        ?>&nbsp;</td>
                            <td style='text-align:right'><?php echo $this->Functions->money($row['Cobranzacheque']['amount']) ?>&nbsp;</td>
                            <td style='text-align:right'><?php echo $this->Functions->money($saldo) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                    if (isset($row['tipo']) && $row['tipo'] == '1') { // Cajasingreso
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td><?= h($users[$row['user_id']]) ?>&nbsp;</td>
                            <td><?php echo h($row['concepto']) ?>&nbsp;</td>
                            <td style='text-align:right'><?= "--" ?>&nbsp;</td>
                            <td style='text-align:right'><?= "--" ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                    if (isset($row['tipo']) && $row['tipo'] == '5') { // Bancosdepositoscheque
                        $class = $row['anulado'] ? ' class="error-message tachado"' : null;
                        if ($i++ % 2 == 0) {
                            $class = $row['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                        }
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td><?= h($users[$row['user_id']]) ?>&nbsp;</td>
                            <td><?php echo h($row['anulado'] ? $row['concepto'] : $row['concepto']) ?>&nbsp;</td>
                            <td style='text-align:right'><?php echo $row['anulado'] ? '--' : $this->Functions->money($saldo) ?>&nbsp;</td>
                            <td style='text-align:right'>--&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                        if (!$row['anulado']) {//si ya fue depositado, el saldo restante es cero (ya no se utiliza para nada mas)
                            $saldo = 0;
                        }
                    }
                    if (isset($row['tipo']) && $row['tipo'] == '8') { // PP
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td><?= h($users[$row['user_id']]) ?>&nbsp;</td>
                            <td><?php echo h($row['concepto']) ?>&nbsp;</td>
                            <td style='text-align:right'><?php echo $this->Functions->money($cheque['Cheque']['importe']) ?>&nbsp;</td>
                            <td style='text-align:right'><?php echo $this->Functions->money(0) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                endforeach;
            } else {
                echo '<tr><td class="borde_tabla"></td><td colspan="5"><h3>No existen movimientos para el cheque seleccionado</h3></td><td class="borde_tabla"></td></tr>';
            }
            $class = ' class=""';
            if ($i++ % 2 == 0) {
                $class = ' class = "altrow"';
            }
            ?>
            <tr <?= $class ?>>
                <td class="borde_tabla"></td>
                <td colspan="3"></td>
                <td style='text-align:right'><b><?php echo __('Saldo restante') ?></b></td>
                <td style='border-top:2px solid black;text-align:right'><?php echo $this->Functions->money($saldo) ?>&nbsp;</td>
                <td class="borde_tabla"></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<?php
if ($sinlayout == 1) {
    ?>
    <style>
        #seccionaimprimir{
            font-size:12px !important;
        }
    </style>
    <?php
}


    