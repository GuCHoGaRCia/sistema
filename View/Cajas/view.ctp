<div class="cajas index">
    <h2><?php
        echo __('Movimientos caja') . ' "' . h($c['Caja']['name']) . '"<br><br>' . (isset($this->request->data['Caja']['desde']) && !empty($this->request->data['Caja']['desde']) ? 'Desde el ' . $this->request->data['Caja']['desde'] . '. ' : '') . 'Pesos: ' .
        $this->Functions->money($c['Caja']['saldo_pesos']) . ' Cheques: ' . $this->Functions->money($c['Caja']['saldo_cheques']) . ' Total: ' . $this->Functions->money($c['Caja']['saldo_pesos'] + $c['Caja']['saldo_cheques']);
        ?></h2><br>
    <?php
    echo $this->Form->create('Caja', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->JqueryValidation->input('cajas', ['label' => false, 'empty' => '', 'options' => $cajas, 'type' => 'select', 'selected' => isset($c['Caja']['id']) ? $c['Caja']['id'] : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => isset($this->request->data['Caja']['desde']) ? $this->request->data['Caja']['desde'] : date("01/m/Y")]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => isset($this->request->data['Caja']['hasta']) ? $this->request->data['Caja']['hasta'] : date("d/m/Y")]);
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;']);
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
    if (!isset($movimientos)) {
        echo "<div class='info'>Seleccione Caja, Fecha Desde y Hasta y presione Ver</div>";
    } else {
        ?>
        <div id="seccionaimprimir">
            <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                <?php
                echo __('Movimientos caja') . ' "' . h($c['Caja']['name']) . '"<br>' . (isset($this->request->data['Caja']['desde']) && !empty($this->request->data['Caja']['desde']) ? 'Desde el ' . $this->request->data['Caja']['desde'] . '. ' : '') . 'Pesos: ' .
                $this->Functions->money($c['Caja']['saldo_pesos']) . ' Cheques: ' . $this->Functions->money($c['Caja']['saldo_cheques']) . ' Total: ' . $this->Functions->money($c['Caja']['saldo_pesos'] + $c['Caja']['saldo_cheques']);
                ?>
            </div>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Tipo') ?></th>
                        <th><?php echo __('Fecha') ?></th>
                        <th><?php echo __('Concepto') ?></th>
                        <th style='text-align:right'><?php echo __('Ingreso pesos') ?></th>
                        <th style='text-align:right'><?php echo __('Ingreso cheques') ?></th>
                        <th style='text-align:right'><?php echo __('Egreso pesos') ?></th>
                        <th style='text-align:right'><?php echo __('Egreso cheques') ?></th>
                        <th style='text-align:right'><?php echo __('Total') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $tipos = [1 => __('Ingreso'), 2 => __('Egreso')];
                    // $saldop = $c['Caja']['saldo_pesos'];
                    // $saldoc = $c['Caja']['saldo_cheques'];
                    $saldop = 0;
                    $saldoc = 0;
                    $detalleingresos = [];
                    if (isset($movimientos['di'])) {
                        $detalleingresos = $movimientos['di'];
                        unset($movimientos['di']);
                    }
                    $ipesos = $icheques = $epesos = $echeques = 0;
                    //$fechaCambio = strtotime("2018-11-05");
                    foreach ($movimientos as $row):
                        $class = $row['anulado'] || substr($row['concepto'], 0, 11) == "Anulación " ? ' class="error-message tachado"' : null;
                        if ($i++ % 2 == 0) {
                            $class = $row['anulado'] || substr($row['concepto'], 0, 11) == "Anulación " ? ' class="altrow error-message tachado"' : ' class="altrow"';
                        }
                        // modifico el saldo solo para los movimientos NO anulados
                        if ($row['tipo'] == '1') {//es ingreso
                            $saldop += $row['importe'];
                            $saldoc += $row['cheque'];
                        } else {
                            $saldop -= $row['importe'];
                            $saldoc -= $row['cheque'];
                        }
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo h($tipos[$row['tipo']]) ?></td>
                            <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $row['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td>
                                <?php
                                echo h($row['concepto']);
                                if (!empty($row['cobranza_id'])) {
                                    echo $this->Html->link($this->Html->image('icon-info.png', ['title' => __("Ver detalle Cobranza")]), ['controller' => 'Cobranzas', 'action' => 'view', $row['cobranza_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                                }
                                ?>
                                &nbsp;
                            </td>
                            <?php
                            if ($row['tipo'] == '1') {//es ingreso
                                $ipesos += $row['importe'];
                                $icheques += $row['cheque'];
                                echo "<td style='text-align:right'>" . $this->Functions->money($row['importe']) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money($row['cheque']) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money(0) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money(0) . "</td>";
                            } else {//es egreso
                                $epesos -= $row['importe'];
                                $echeques -= $row['cheque'];
                                echo "<td style='text-align:right'>" . $this->Functions->money(0) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money(0) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money(-$row['importe']) . "</td>";
                                echo "<td style='text-align:right'>" . $this->Functions->money(isset($row['cheque']) ? -$row['cheque'] : 0) . " ";
                                echo "</td>";
                            }
                            ?>
                            <td style='text-align:right' title='<?= 'Pesos: ' . $this->Functions->money($saldop) . ' - Cheque: ' . $this->Functions->money($saldoc) ?>'><?= $this->Functions->money($saldop + $saldoc) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="borde_tabla"></td>
                        <td colspan='3'>&nbsp;</td>
                        <td style='border-top:2px solid black;text-align:right'><b><?= $this->Functions->money($ipesos) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right'><b><?= $this->Functions->money($icheques) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right'><b><?= $this->Functions->money($epesos) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right'><b><?= $this->Functions->money($echeques) ?></b></td>
                        <td style='border-top:2px solid black;text-align:right' title='<?= 'Pesos: ' . $this->Functions->money($saldop) . ' - Cheque: ' . $this->Functions->money($saldoc) ?>'><?= $this->Functions->money($saldop + $saldoc) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="8"></td>
                        <td class="bottom_d"></td>
                    </tr>
            </table>
            <?php
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
        $("#CajaCajas").select2({language: "es", placeholder: '<?= __("Seleccione caja...") ?>'});
    });
    $("#noimprimir").submit(function (event) {
        if ($("#CajaCajas").val() === "") {
            alert('<?= __('Seleccione una Caja') ?>');
            return false;
        }
        var f1 = $("#CajaDesde").val();
        var f2 = $("#CajaHasta").val();
        if (f1 === "" || f2 === "") {
            alert('<?= __('Seleccione fecha Desde y Hasta') ?>');
            return false;
        }
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            alert('<?= __('La fecha Desde debe ser menor o igual a Hasta') ?>');
            return false;
        }
        $("#load").show();
        return true;
    });

</script>

