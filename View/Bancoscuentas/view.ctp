<div class="cajas index">
    <h2><?php
        echo __('Movimientos cuenta bancaria') . '<br><br>' . (isset($this->request->data['Bancoscuenta']['desde']) && !empty($this->request->data['Bancoscuenta']['desde']) ? 'Del ' . $this->request->data['Bancoscuenta']['desde'] : '') .
        (isset($this->request->data['Bancoscuenta']['hasta']) && !empty($this->request->data['Bancoscuenta']['hasta']) ? ' al ' . $this->request->data['Bancoscuenta']['hasta'] . '. ' : '') . 'Saldo pesos al ' . date("d/m/Y") . ': ' . $this->Functions->money($c['Bancoscuenta']['saldo']);
        ?>
    </h2>
    <br>
    <?php
    echo $this->Form->create('Bancoscuenta', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('cuentas', ['label' => false, 'empty' => '', 'options' => $cuentas, 'type' => 'select', 'selected' => isset($c['Bancoscuenta']['id']) ? $c['Bancoscuenta']['id'] : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => isset($this->request->data['Bancoscuenta']['desde']) ? $this->request->data['Bancoscuenta']['desde'] : date("01/m/Y")]);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => isset($this->request->data['Bancoscuenta']['hasta']) ? $this->request->data['Bancoscuenta']['hasta'] : date("d/m/Y")]);
    echo $this->Form->input('incluye_anulados', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.5);border:1px solid grey']);
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;']);
    echo $this->Form->end(__('Ver'));
    if (!isset($movimientos)) {
        echo "<div class='info'>Seleccione Consorcio y Fecha Desde / Hasta y presione Ver</div>";
    } else {
        ?>
        <div id="seccionaimprimir">
            <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                Movimientos <?= h($c['Bancoscuenta']['name'] . " - " . $c['Bancoscuenta']['cuenta'] . " - Saldo: " . $this->Functions->money($c['Bancoscuenta']['saldo'])) ?>
                <?= " - Del " . $this->request->data['Bancoscuenta']['desde'] . " al " . $this->request->data['Bancoscuenta']['hasta'] ?>
            </div>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Forma') ?>&nbsp;&nbsp;</th>
                        <th><?php echo __('Fecha') ?>&nbsp;</th>
                        <th><?php echo __('Concepto') ?></th>
                        <th class="center"><?php echo __('Conciliado') ?></th>
                        <th style='text-align:right'><?php echo __('Créditos') ?>&nbsp;</th>
                        <th style='text-align:right'><?php echo __('Débitos') ?>&nbsp;</th>
                        <th style='text-align:right'><?php echo __('Total') ?>&nbsp;</th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $totalesporformadepago = [];
                    $tipos = [3 => __('Depósito efectivo'), 1 => __('Crédito'), 2 => __('Débito'), 4 => __('Transferencia'), 5 => __('Depósito cheque'), 6 => __('Extracción'), '7' => __('Cheque propio'), '8' => __('Transf. Interbancaria'), '11' => __('Cheque propio ADM'), 9 => __('Interdepósito')];
                    foreach ($formasdepago as $k => $v) {
                        $tipos[$k] = $v['forma'];
                        $totalesporformadepago[$k] = 0;
                    }
                    $saldoanterior = 0;
                    // para los movimientos viejos calculo el saldo (hasta desde)
                    $movimientos2 = array_reverse($movimientos);
                    $d = strtotime(substr($this->request->data['Bancoscuenta']['desde'], 6, 4) . "-" . substr($this->request->data['Bancoscuenta']['desde'], 3, 2) . "-" . substr($this->request->data['Bancoscuenta']['desde'], 0, 2));
                    $h = strtotime(substr($this->request->data['Bancoscuenta']['hasta'], 6, 4) . "-" . substr($this->request->data['Bancoscuenta']['hasta'], 3, 2) . "-" . substr($this->request->data['Bancoscuenta']['hasta'], 0, 2));

                    $ids = [];
                    foreach ($movimientos2 as $k => $row) {
                        if (strtotime($row['fecha']) >= $d && strtotime($row['fecha']) <= $h) {
                            $ids[] = $row['id'] . "-" . strtotime($row['created']);
                            continue;
                        }
                        if (!$row['anulado']) {
                            // es extraccion o transferencia
                            if ($row['tipo'] == 4 || $row['tipo'] == 2 || $row['tipo'] == 7 || $row['tipo'] == 11 || ($row['tipo'] == 6 && $c['Bancoscuenta']['id'] == $row['bancoscuenta_id'])) {
                                //es extraccion (debito) o transferencia a otro banco (debito tambien)
                                $saldoanterior -= $row['importe'];
                            } else {
                                $saldoanterior += $row['importe'];
                            }
                        }
                    }
                    /*
                     * Para los movimientos posteriores a la fecha actual, modifico el saldo para q me de bien.
                     * En la fecha actual va el saldo actual real, los dias posteriores a la fecha actual se hace una muestra de como quedaría el saldo con los
                     * ingresos y egresos q se fuesen haciendo (Ej: un cheque propio q vence dentro de 2 dias), o un cheque tercero q entra en 3 dias 
                     */
                    /* foreach ($movimientos as $row) {
                      if (!$row['anulado']) {
                      $tipo = getTipo($row);
                      if ($tipo == 7) {// cheque propio, ahora en "fecha" viene la fecha del pago a proveedor, no la de vencimiento (virtualfield)
                      if (strtotime($row['fecha_vencimiento']) > strtotime(date("Y-m-d"))) {
                      $saldo -= $row['importe'];
                      $ids[] = $row['id'];
                      } else {
                      //$saldo += $row['importe'];// si hay un cheque q vence a futuro, el inicial queda en cero (y el ultimo saldo de las filas tiene incluido el cheque futuro
                      }
                      } else if (strtotime($row['fecha']) > strtotime(date("Y-m-d"))) {
                      if ($tipo == 4 || ($tipo == 6 && $c['Bancoscuenta']['id'] == $row['bancoscuenta_id'])) {
                      $saldo -= $row['importe'];
                      } else {
                      $saldo += $row['importe'];
                      }
                      }
                      }
                      } */
                    $primeros = true;
                    $saldo = $saldoanterior;
                    foreach ($movimientos as $row) {
                        if (!in_array($row['id'] . "-" . strtotime($row['created']), $ids)) {
                            continue;
                        }
                        if (!$row['anulado']) {
                            // es extraccion o transferencia
                            if ($row['tipo'] == 4 || $row['tipo'] == 2 || $row['tipo'] == 7 || $row['tipo'] == 11 || ($row['tipo'] == 6 && $c['Bancoscuenta']['id'] == $row['bancoscuenta_id'])) {
                                //es extraccion (debito) o transferencia a otro banco (debito tambien)
                                $saldo -= $row['importe'];
                            } else {
                                $saldo += $row['importe'];
                            }
                        }
                    }
                    $last = [];
                    foreach ($movimientos as $row):
                        if (!in_array($row['id'] . "-" . strtotime($row['created']), $ids)) {
                            continue;
                        }
                        $tipo = getTipo($row);
                        $class = $row['anulado'] ? ' class="error-message"' : null;
                        if ($i++ % 2 == 0) {
                            $class = $row['anulado'] ? ' class="altrow error-message"' : ' class="altrow"';
                        }
                        $con = ($tipo == 4 || $tipo == 6 || $tipo == 2 ? 'Bancosextracciones' : ($tipo == 8 ? 'Bancostransferencias' : ($tipo == 7 ? 'Chequespropios' : ($tipo == 11 ? 'Chequespropiosadms' : ($tipo == 5 ? 'Bancosdepositoscheques' : 'Bancosdepositosefectivos')))));
                        // si es transferencia y la cuenta bancaria actual es bancoscuenta_id (entonces destino_id es otra cuenta, entonces es una salida de dinero)
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo h($tipos[$tipo]) ?>&nbsp;&nbsp;</td>
                            <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $row['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $row['fecha']) ?>&nbsp;</td>
                            <td><?= h($row['concepto'] . (isset($row['numero']) ? " #" . $row['numero'] : '')) ?>&nbsp;</td>
                            <td class="center"><?php echo $this->Html->link($this->Html->image(h($row['conciliado'] ? '1' : '0') . '.png', ['title' => __('Conciliar'), 'alt' => $row['conciliado'] ? 'S' : 'N']), ['controller' => $con, 'action' => 'invertir', 'conciliado', h($row['id'])], ['class' => 'status', 'escape' => false]); ?></td>
                            <?php
                            // es extraccion o transferencia
                            if ($row['tipo'] == 4 || $row['tipo'] == 7 || $row['tipo'] == 11 || ($row['tipo'] == 6 && $c['Bancoscuenta']['id'] == $row['bancoscuenta_id'])) {
                                //es extraccion (debito) o transferencia a otro banco (debito tambien)
                                echo "<td>&nbsp;</td>";
                                echo "<td style='text-align:right'>-" . $this->Functions->money($row['importe']) . "&nbsp;</td>";
                                $totalesporformadepago[$tipo] = ($totalesporformadepago[$tipo] ?? 0) - $row['importe'];
                            } else {
                                echo "<td style='text-align:right'>" . $this->Functions->money($row['importe']) . "&nbsp;</td>";
                                echo "<td>&nbsp;</td>";
                                $totalesporformadepago[$tipo] = ($totalesporformadepago[$tipo] ?? 0) + $row['importe'];
                            }
                            if (!in_array($row['id'], $ids)) {
                                $primeros = false;
                            }
                            ?>
                            <td style='text-align:right;<?= $saldo < 0 ? 'color:red' : '' ?>'><?= !$row['anulado'] ? $this->Functions->money($saldo) : '' ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                            <?php
                            if (!$row['anulado']) {
                                // es extraccion o transferencia
                                if ($row['tipo'] == 4 || $row['tipo'] == 2 || $row['tipo'] == 7 || $row['tipo'] == 11 || ($row['tipo'] == 6 && $c['Bancoscuenta']['id'] == $row['bancoscuenta_id'])) {
                                    //es extraccion (debito) o transferencia a otro banco (debito tambien)
                                    $saldo += $row['importe'];
                                } else {
                                    $saldo -= $row['importe'];
                                }
                                $last = $row;
                            }
                            ?>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                    <tr>
                        <td class="borde_tabla"></td>
                        <td colspan='4'>&nbsp;</td>
                        <td colspan="2"><b><?php echo __('Saldo anterior') ?></b></td>
                        <td style='border-top:2px solid black;text-align:right'><?php echo $this->Functions->money(round($saldo, 2)) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td colspan="7"></td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td colspan="7"></td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    //muestro los totales por forma de pago
                    foreach ($totalesporformadepago as $k => $v) {
                        if ($v == 0) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td class="borde_tabla"></td>
                            <td colspan='4'>&nbsp;</td>
                            <td colspan="2"><b><?= h($tipos[$k]) ?></b></td>
                            <td style='border-top:2px solid black;text-align:right'><?php echo $this->Functions->money(round($v, 2)) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="7"></td>
                        <td class="bottom_d"></td>
                    </tr>
            </table>   
        </div>
        <?php
    }
    ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({/*maxDate: '0', */changeYear: true, yearRange: '2016:+1'});
        $("#BancoscuentaCuentas").select2({language: "es", placeholder: '<?= __("Seleccione cuenta...") ?>'});
    });
</script>
<style>
    @media print{
        img{
            display:block;
        }
        body{
            margin:15px;
            margin-bottom:25px;
        }
        @page {
            size: auto;
            margin:15px;
            margin-bottom:25px;
        }
    }
</style>
<?php

function getTipo($row) {
    if (isset($row['formasdepago_id']) && $row['formasdepago_id'] != 0) {
        return $row['formasdepago_id'];
    }
    if ($row['tipo'] == 3) {//bancosdepositosefectivo
        if (isset($row['cobranza_id']) && !is_null($row['cobranza_id'])) {
            if ($row['es_transferencia']) {
                return 3; // es un bancosdepositosefectivo con cobranza, TRANSFERENCIA
            } else {
                return 3; // es un bancosdepositosefectivo con cobranza, TRANSFERENCIA    
            }
        } else {
            if (!empty($row['caja_id'])) {
                return 3; // es un bancosdepositosefectivo 
            } else {
                return 1; // es un bancosdepositosefectivo (3) y sin cobranza, CREDITO
            }
        }
    }
    if ($row['tipo'] == 4) {//bancosextracciones
        if ($row['proveedorspago_id'] == 0 && $row['caja_id'] == 0) {
            return 2; // es un DEBITO
        }
        if ($row['proveedorspago_id'] != 0) {
            return 4; // es una transferencia (pago a proveedor)
        }
        return 6;
    }

    if ($row['tipo'] == 7) {
        return 7; // PP Cheque propio
    }

    if (isset($row['consorcio_id']) && is_null($row['consorcio_id']) || isset($row['proveedorspago_id']) && $row['proveedorspago_id'] != 0) {
        return 4; //consorcio_id=null -> transferencia bancaria
    }
    if (isset($row['destino_id'])) {
        return 8; // es transferencia entre bancos. 
    }


    if (isset($row['es_transferencia']) && $row['es_transferencia'] == true || $row['tipo'] == 4) {
        return 4; // transferencia
    }
    if (isset($row['caja_id']) && $row['caja_id'] == 0) {
        return 2; // deposito bancaria
    }
    return $row['tipo'];
}
