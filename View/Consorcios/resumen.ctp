<style>
    @media print {
        .titulo{
            display:block;
        }
        img{
            display:none;
        }
        #seccionaimprimir{
            font-size:12px !important;
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
<div class="consorcios index" id="noimprimir">
    <h2>Resumen Caja Banco</h2>
    <?php
    if ($procesando) {
        echo "<div class='warning'>Nos encontramos actualmente re-procesando los Saldos históricos hasta el dia de la fecha. En unos instantes finalizaremos</div>";
    }
    echo $this->Form->create('Consorcio', ['class' => 'inline']);
    echo $this->JqueryValidation->input('consorcio', ['label' => false, 'empty' => '', 'options' => [0 => __('TODOS')] + $consorcios, 'type' => 'select', 'selected' => isset($this->request->data['Consorcio']['consorcio']) ? $this->request->data['Consorcio']['consorcio'] : '']);
    echo "<b>Desde</b> " . $this->Form->input('desde', ['label' => false, 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'autocomplete' => 'off', 'value' => isset($this->request->data['Consorcio']['desde']) ? $this->request->data['Consorcio']['desde'] : date("01/m/Y")]);
    echo "<b>Hasta</b> " . $this->Form->input('hasta', ['label' => false, 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'autocomplete' => 'off', 'value' => isset($this->request->data['Consorcio']['hasta']) ? $this->request->data['Consorcio']['hasta'] : date("d/m/Y")]);
    echo "<span id='a' title='Mes anterior' style='font-size:15px;font-weight:bold;cursor:pointer'>-1 mes</span>&nbsp;&nbsp;<span id='b' title='Mes siguiente' style='font-size:15px;font-weight:bold;cursor:pointer'> +1 mes</span>&nbsp;&nbsp;";
    echo "<span id='c' title='Dia anterior' style='font-size:15px;font-weight:bold;cursor:pointer'>-1 dia</span>&nbsp;&nbsp;<span id='d' title='Dia siguiente' style='font-size:15px;font-weight:bold;cursor:pointer'> +1 dia</span>&nbsp;&nbsp;";
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Ver'), 'id' => 'guardar', 'style' => 'width:50px']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
    //debug($movimientos);
    if (empty($movimientos)) {
        echo "<div class='info'>Seleccione Consorcio...</div>";
    } else {
        //debug($this->request->data['Consorcio']['desde']);
        //debug($movimientos);
        //debug($saldos);
        ?>
        <div id="seccionaimprimir" style="display:block;width:100%">
            <div class="titulo" style="margin-top:3px;padding:8px;padding-bottom:0;border:2px dashed #000;text-align:center;font-weight:bold;width:100%">
                <?php
                echo mb_strtoupper(__('RESUMEN') . " " . h((isset($this->request->data['Consorcio']['consorcio']) && !empty($this->request->data['Consorcio']['consorcio']) ? ' ' . $consorcios[$this->request->data['Consorcio']['consorcio']] : '') .
                                (isset($this->request->data['Consorcio']['desde']) && !empty($this->request->data['Consorcio']['desde']) ? ' desde ' . $this->request->data['Consorcio']['desde'] : '') .
                                (isset($this->request->data['Consorcio']['hasta']) && !empty($this->request->data['Consorcio']['hasta']) ? ' hasta ' . $this->request->data['Consorcio']['hasta'] : '')));
                ?>
            </div>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('CAJA') ?> <span style="cursor:pointer" onclick="toggle2()">[+/-]</span></th>
                        <th style='text-align:right'><?php echo __('Efectivo') ?></th>
                        <th style='text-align:right'><?php echo __('Cheque') ?></th>
                        <th style='text-align:right'><?php echo __('Total') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $saldoingresocajaefectivo = $saldos['saldocajaefectivo'];
                    $saldoingresocajacheque = $saldos['saldocajacheque'];
                    // SALDO CAJA ANTERIOR
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Caja Anterior</td><td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajacheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo + $saldoingresocajacheque) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    //inicializo con los saldos
                    $totalingresocajaefectivo = 0;
                    $totalingresocajacheque = 0;
                    $ingresocobranzaefectivo = 0;
                    $ingresocobranzacheque = 0;

                    // ingresos efectivo cheque
                    $listaingresos = [];
                    //debug($movimientos['ingresos']['cobranzas']);
                    if (isset($movimientos['ingresos']['cobranzas']) && count($movimientos['ingresos']['cobranzas']) > 0) {
                        foreach ($movimientos['ingresos']['cobranzas'] as $a => $b) {
                            $fila = [1 => "", 2 => 0, 3 => 0, 4 => color($b['anulado'])];
                            $ingresocobranzaefectivo += (float) $b['importe'];
                            $ingresocobranzacheque += (float) $b['cheque'];
                            $fila[1] = $this->Time->format(__('d/m/Y'), $b['created']) . " - " . h($b['concepto']) . "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "cobranzas/view/" . $a . "\");'/>";
                            if ($b['importe'] != 0) {// muestro solo los q tienen importe>0 ?
                                $fila[2] = $b['importe'];
                            }
                            if ($b['cheque'] != 0) {
                                $fila[3] = $b['cheque'];
                            }
                            $listaingresos[] = $fila;
                        }
                    }
                    $totalingresocajaefectivo += $ingresocobranzaefectivo;
                    $totalingresocajacheque += $ingresocobranzacheque;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Cobranzas Efectivo Cheque") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdiec')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresocobranzaefectivo) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresocobranzacheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresocobranzaefectivo + $ingresocobranzacheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($listaingresos)) {
                        foreach ($listaingresos as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . $l[4] . "\" class=\"xxdiec\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . $l[1] . "</td><td style='text-align:right'>" . $this->Functions->money($l[2]) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>" . $this->Functions->money($l[3]) . "&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    $ingresosmanualesefectivo = $ingresosmanualescheque = 0;
                    if (!empty($movimientos['ingresos']['otros'])) {
                        foreach ($movimientos['ingresos']['otros'] as $l) {//muestro el detalle
                            $ingresosmanualesefectivo += $l['Cajasingreso']['importe'];
                            $ingresosmanualescheque += $l['Cajasingreso']['cheque'];
                        }
                    }
                    $totalingresocajaefectivo += $ingresosmanualesefectivo;
                    $totalingresocajacheque += $ingresosmanualescheque;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Manuales") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdetim')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresosmanualesefectivo) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresosmanualescheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresosmanualesefectivo + $ingresosmanualescheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['ingresos']['otros'])) {
                        foreach ($movimientos['ingresos']['otros'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none;" . color($l['Cajasingreso']['anulado']) . "\" class=\"xxdetim\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'><span title='" . $this->Time->format(__('d/m/Y H:i:s'), $l['Cajasingreso']['created']) . "'>" . h($this->Time->format(__('d/m/Y'), $l['Cajasingreso']['created']) . " - " . $l['Cajasingreso']['concepto']);
                            echo "</span></td><td style='text-align:right'>" . $this->Functions->money($l['Cajasingreso']['importe']) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>" . $this->Functions->money($l['Cajasingreso']['cheque']) . "&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    $ingresoextraccionbancaria = 0;
                    if (!empty($movimientos['ingresos']['extracciones'])) {
                        foreach ($movimientos['ingresos']['extracciones'] as $l) {//muestro el detalle
                            $ingresoextraccionbancaria += $l['Cajasingreso']['importe'];
                        }
                    }
                    $totalingresocajaefectivo += $ingresoextraccionbancaria;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Extracción Bancaria") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdieb')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresoextraccionbancaria) ?>&nbsp;</td>
                        <td style='text-align:right'>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresoextraccionbancaria) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['ingresos']['extracciones'])) {
                        foreach ($movimientos['ingresos']['extracciones'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none;" . color($l['Cajasingreso']['anulado']) . "\" class=\"xxdieb\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Cajasingreso']['created']) . " - " . $cuentas[$l['Cajasingreso']['bancoscuenta_id']] . " - " . $l['Cajasingreso']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money($l['Cajasingreso']['importe']) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }


                    // egresos PP efectivo cheques
                    $egresoproveedorefectivo = $egresoproveedorcheque = 0;
                    if (!empty($movimientos['egresos']['pagosproveedor']['efectivocheque'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['efectivocheque'] as $l) {//sumo el detalle
                            $egresoproveedorefectivo += $l['Cajasegreso']['importe'];
                            $egresoproveedorcheque += $l['Cajasegreso']['cheque'];
                        }
                    }

                    $totalingresocajaefectivo -= $egresoproveedorefectivo;
                    $totalingresocajacheque -= $egresoproveedorcheque;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Pago Proveedor Efectivo Cheque") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdepp')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresoproveedorefectivo) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresoproveedorcheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-($egresoproveedorefectivo + $egresoproveedorcheque)) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    //debug($movimientos['egresos']['pagosproveedor']);
                    if (!empty($movimientos['egresos']['pagosproveedor']['efectivocheque'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['efectivocheque'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Proveedorspago']['anulado']) . "\" class=\"xxdepp\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['created']) . " - " . $proveedors[$l['Proveedorspago']['proveedor_id']] . " - " . $l['Proveedorspago']['concepto']);
                            echo "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorspagos/view/" . $l['Proveedorspago']['id'] . "/1\");'/>";
                            echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['importe']) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['cheque']) . "&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }
                    //debug($movimientos['egresos']['pagosproveedor']['acuenta']);
                    /* if (!empty($movimientos['egresos']['pagosproveedor']['acuenta'])) {
                      foreach ($movimientos['egresos']['pagosproveedor']['acuenta'] as $l) {//muestro el detalle
                      if (muestra($l['Proveedorspago']) && !is_null($l['Cajasegreso']['importe'])) {
                      echo "<tr style=\"display:none" . color($l['Proveedorspago']['anulado']) . "\" class=\"depp\">";
                      echo '<td class="borde_tabla"></td>';
                      echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['fecha']) . " - " . $l['Proveedorspago']['concepto']);
                      echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['importe']) . "&nbsp;</td>";
                      echo "<td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['cheque']) . "&nbsp;</td><td>&nbsp;</td>";
                      echo '<td class="borde_tabla"></td>';
                      echo "</tr>";
                      }
                      }
                      } */

                    $egresosmanualesefectivo = $egresosmanualescheque = 0;
                    if (!empty($movimientos['egresos']['otros'])) {
                        foreach ($movimientos['egresos']['otros'] as $l) {//muestro el detalle
                            $egresosmanualesefectivo += $l['Cajasegreso']['importe'];
                            $egresosmanualescheque += $l['Cajasegreso']['cheque'];
                        }
                    }
                    $totalingresocajaefectivo -= $egresosmanualesefectivo;
                    $totalingresocajacheque -= $egresosmanualescheque;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Manuales") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdetem')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresosmanualesefectivo) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresosmanualescheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-($egresosmanualesefectivo + $egresosmanualescheque)) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['egresos']['otros'])) {
                        foreach ($movimientos['egresos']['otros'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Cajasegreso']['anulado']) . "\" class=\"xxdetem\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Cajasegreso']['created']) . " - " . $l['Cajasegreso']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['importe']) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>" . $this->Functions->money(-$l['Cajasegreso']['cheque']) . "&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    $ingresodepositosefectivo = $ingresodepositoscheque = 0; // uso esto mismo para salida de caja y para deposito al banco
                    if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
                        foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {//muestro el detalle
                            $ingresodepositosefectivo += $l['Bancosdepositosefectivo']['importe'];
                        }
                    }
                    if (!empty($movimientos['egresos']['depositos']['cheque'])) {
                        foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {//muestro el detalle
                            $ingresodepositoscheque += $l['Bancosdepositoscheque']['importe'];
                        }
                    }

                    $totalingresocajaefectivo -= $ingresodepositosefectivo;
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Depósito") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxded1')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-($ingresodepositosefectivo)) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-($ingresodepositoscheque)) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(-($ingresodepositosefectivo + $ingresodepositoscheque)) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    //debug($movimientos);
                    if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
                        foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositosefectivo']['anulado']) . "\" class=\"xxded1\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositosefectivo']['created']) . " - " . $cuentas[$l['Bancosdepositosefectivo']['bancoscuenta_id']] . " - " . $l['Bancosdepositosefectivo']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Bancosdepositosefectivo']['importe']) . "&nbsp;</td>";
                            echo "<td style='text-align:right'>&nbsp;</td><td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }
                    if (!empty($movimientos['egresos']['depositos'])) {
                        foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositoscheque']['anulado']) . "\" class=\"xxded1\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositoscheque']['created']) . " - " . $cuentas[$l['Bancosdepositoscheque']['bancoscuenta_id']] . " - " . $l['Bancosdepositoscheque']['concepto']);
                            echo "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(-$l['Bancosdepositoscheque']['importe']) . "&nbsp;</td>";
                            echo "<td>&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }
                    $totalingresocajacheque -= $ingresodepositoscheque; // cheques q salieron de la caja
                    // TOTAL CAJA
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Movimientos Caja</td><td style='text-align:right'>" . $this->Functions->money($totalingresocajaefectivo) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($totalingresocajacheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($totalingresocajaefectivo + $totalingresocajacheque) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    // SALDO CAJA ACTUAL
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Caja Actual</td><td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo + $totalingresocajaefectivo) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajacheque + $totalingresocajacheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money(($saldoingresocajaefectivo + $saldoingresocajacheque) + ($totalingresocajaefectivo + $totalingresocajacheque)) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="4"></td>
                        <td class="bottom_d"></td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('BANCO') ?></th>
                        <th style='text-align:right'><?php echo __('Total') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // MUESTRO LOS SALDOS ANTERIORES
                    $saldoingresobancoefectivo = $saldos['saldobancoefectivo'];
                    $saldoingresobancocheque = $saldos['saldobancocheque'];
                    ?>
                    <tr style="border:3px solid gray;font-weight:bold">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Banco Anterior")) ?></td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresobancoefectivo + $saldoingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    $ingresotransferencia = 0;
                    if (!empty($movimientos['transferencias'])) {
                        foreach ($movimientos['transferencias'] as $l) {//muestro el detalle
                            $ingresotransferencia += $l['Bancosdepositosefectivo']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Cobranzas Transferencia") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdit')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresotransferencia) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['transferencias'])) {
                        foreach ($movimientos['transferencias'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositosefectivo']['anulado']) . "\" class=\"xxdit\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositosefectivo']['created']) . " - " . $cuentas[$l['Bancosdepositosefectivo']['bancoscuenta_id']] . " - " . $l['Bancosdepositosefectivo']['concepto']);
                            echo "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "cobranzas/view/" . $l['Bancosdepositosefectivo']['cobranza_id'] . "/1\");'/>";
                            echo "</td><td style='text-align:right'>" . $this->Functions->money($l['Bancosdepositosefectivo']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    // ingresostransferenciasinterbancos
                    $ingresostransferenciasinterbancos = 0;
                    if (!empty($movimientos['ingresostransferenciasinterbancos'])) {
                        foreach ($movimientos['ingresostransferenciasinterbancos'] as $l) {//muestro el detalle
                            $ingresostransferenciasinterbancos += $l['Bancostransferencia']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Transferencias Interbancarias") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxditi')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresostransferenciasinterbancos) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['ingresostransferenciasinterbancos'])) {
                        foreach ($movimientos['ingresostransferenciasinterbancos'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancostransferencia']['anulado']) . "\" class=\"xxditi\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancostransferencia']['created']) . " - De " . $cuentas[$l['Bancostransferencia']['bancoscuenta_id']] . " a " . $cuentas[$l['Bancostransferencia']['destino_id']] . " - " . $l['Bancostransferencia']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money($l['Bancostransferencia']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    // ingresoscreditosbancarios
                    $ingresoscreditosbancarios = 0;
                    if (!empty($movimientos['creditos'])) {
                        foreach ($movimientos['creditos'] as $l) {//muestro el detalle
                            $ingresoscreditosbancarios += $l['Bancosdepositosefectivo']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Créditos Bancarios") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdicb')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money($ingresoscreditosbancarios) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['creditos'])) {
                        foreach ($movimientos['creditos'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositosefectivo']['anulado']) . "\" class=\"xxdicb\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositosefectivo']['created']) . " - " . $cuentas[$l['Bancosdepositosefectivo']['bancoscuenta_id']] . " - " . $l['Bancosdepositosefectivo']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money($l['Bancosdepositosefectivo']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }


                    $ingresodepositosefectivo = $ingresodepositoscheque = 0;
                    if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
                        foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {//muestro el detalle
                            $ingresodepositosefectivo += $l['Bancosdepositosefectivo']['importe'];
                        }
                    }
                    if (!empty($movimientos['egresos']['depositos']['cheque'])) {
                        foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {//muestro el detalle
                            $ingresodepositoscheque += $l['Bancosdepositoscheque']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Ingresos Depósito") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxded')">[+/-]</span></td>
                        <td colspan="1" style='text-align:right'><?= $this->Functions->money($ingresodepositosefectivo + $ingresodepositoscheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
                        foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositosefectivo']['anulado']) . "\" class=\"xxded\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositosefectivo']['created']) . " - " . $cuentas[$l['Bancosdepositosefectivo']['bancoscuenta_id']] . " - " . $l['Bancosdepositosefectivo']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money($l['Bancosdepositosefectivo']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    if (!empty($movimientos['egresos']['depositos'])) {
                        foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosdepositoscheque']['anulado']) . "\" class=\"xxded\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosdepositoscheque']['created']) . " - " . $cuentas[$l['Bancosdepositoscheque']['bancoscuenta_id']] . " - " . $l['Bancosdepositoscheque']['concepto']) . "</td>";
                            echo "<td style='text-align:right'>" . $this->Functions->money($l['Bancosdepositoscheque']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    // egresosdebitosbancarios
                    $egresosdebitosbancarios = 0;
                    if (!empty($movimientos['debitos'])) {
                        foreach ($movimientos['debitos'] as $l) {//muestro el detalle
                            $egresosdebitosbancarios += $l['Bancosextraccione']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Débitos Bancarios") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdedb')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresosdebitosbancarios) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['debitos'])) {
                        foreach ($movimientos['debitos'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosextraccione']['anulado']) . "\" class=\"xxdedb\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosextraccione']['created']) . " - " . $cuentas[$l['Bancosextraccione']['bancoscuenta_id']] . " - " . $l['Bancosextraccione']['concepto']);
                            echo "</td><td style='text-align:right'>-" . $this->Functions->money($l['Bancosextraccione']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Extracción Bancaria") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdeeb')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$ingresoextraccionbancaria) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['ingresos']['extracciones'])) {
                        foreach ($movimientos['ingresos']['extracciones'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Cajasingreso']['anulado']) . "\" class=\"xxdeeb\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Cajasingreso']['created']) . " - " . $cuentas[$l['Cajasingreso']['bancoscuenta_id']] . " - " . $l['Cajasingreso']['concepto']);
                            echo "</td><td style='text-align:right'>-" . $this->Functions->money($l['Cajasingreso']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    // egresos pago proveedor cheque propio
                    //debug($movimientos['egresos']['pagosproveedor']['chequepropio']);
                    $egresoproveedorchequepropio = 0;
                    if (!empty($movimientos['egresos']['pagosproveedor']['chequepropio'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['chequepropio'] as $l) {
                            $egresoproveedorchequepropio += $l['Chequespropio']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Pago Proveedor Cheque Propio") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdetppcht')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresoproveedorchequepropio) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['egresos']['pagosproveedor']['chequepropio'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['chequepropio'] as $l) {//muestro el detalle
                            $importechequep = $cta = 0;
                            //if (strtotime($l['Chequespropio']['fecha_vencimiento']) <= strtotime($h) && strtotime($l['Proveedorspago']['created']) >= strtotime($d . " 00:00:00") && strtotime($l['Proveedorspago']['created']) <= strtotime($h . " 23:59:59")) {
                            $importechequep += $l['Chequespropio']['importe'];
                            $cta = $l['Chequespropio']['bancoscuenta_id'] ?? 0;
                            //}

                            if ($cta != 0) {
                                echo "<tr style=\"display:none" . color($l['Proveedorspago']['anulado']) . "\" class=\"xxdetppcht\">";
                                echo '<td class="borde_tabla"></td>';
                                echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['created']) . " - " . $cuentas[$cta] . " - " . $l['Proveedorspago']['concepto']);
                                echo "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorspagos/view/" . $l['Proveedorspago']['id'] . "/1\");'/>";
                                echo "</td><td style='text-align:right'>" . $this->Functions->money(-$importechequep) . "&nbsp;</td>";
                                echo '<td class="borde_tabla"></td>';
                                echo "</tr>";
                            }
                        }
                    }

                    $egresostransferencias = 0;
                    if (!empty($movimientos['egresos']['pagosproveedor']['transferencia'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['transferencia'] as $l) {//muestro el detalle
                            $egresostransferencias += $l['Bancosextraccione']['importe'];
                        }
                    }
                    /* if (!empty($movimientos['egresos']['pagosproveedor']['acuenta'])) {
                      foreach ($movimientos['egresos']['pagosproveedor']['acuenta'] as $l) {//pago a cuenta x transferencia
                      if (isset($l['Bancosextraccione']['importe']) && !is_null($l['Bancosextraccione']['importe']) && muestra($l['Bancosextraccione'])) {//pago a cuenta x transferencia
                      $egresostransferencias += $l['Bancosextraccione']['importe'];
                      }
                      }
                      } */
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Pago Proveedor Transferencia") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdetppt')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresostransferencias) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    //debug($movimientos['egresos']['pagosproveedor']); 
                    if (!empty($movimientos['egresos']['pagosproveedor']['transferencia'])) {
                        foreach ($movimientos['egresos']['pagosproveedor']['transferencia'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancosextraccione']['anulado']) . "\" class=\"xxdetppt\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancosextraccione']['created']) . " - " . $cuentas[$l['Bancosextraccione']['bancoscuenta_id']] . " - " . $l['Bancosextraccione']['concepto']);
                            echo "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorspagos/view/" . $l['Bancosextraccione']['proveedorspago_id'] . "/1\");'/>";
                            echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Bancosextraccione']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }
                    /* if (!empty($movimientos['egresos']['pagosproveedor']['acuenta'])) {
                      foreach ($movimientos['egresos']['pagosproveedor']['acuenta'] as $l) {
                      if (isset($l['Bancosextraccione']['importe']) && !is_null($l['Bancosextraccione']['importe']) && muestra($l['Bancosextraccione'])) {//pago a cuenta x transferencia
                      echo "<tr style=\"display:none" . color($l['Proveedorspago']['anulado']) . "\" class=\"xxdetppt\">";
                      echo '<td class="borde_tabla"></td>';
                      echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['fecha']) . " - " . $cuentas[$l['Bancosextraccione']['bancoscuenta_id']] . " - " . $l['Proveedorspago']['concepto']);
                      echo "&nbsp;<img src='" . $this->webroot . "img/icon-info.png' title='Ver movimiento' onclick='$(\"#rc\").dialog(\"open\");$(\"#rc\").load(\"" . $this->webroot . "proveedorspagos/view/" . $l['Proveedorspago']['id'] . "/1\");'/>";
                      echo "</td><td style='text-align:right'>&nbsp;</td>";
                      echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(-$l['Bancosextraccione']['importe']) . "&nbsp;</td>";
                      echo '<td class="borde_tabla"></td>';
                      echo "</tr>";
                      }
                      }
                      } */

                    // egresostransferenciasinterbancos
                    $egresostransferenciasinterbancos = 0;
                    if (!empty($movimientos['egresostransferenciasinterbancos'])) {
                        foreach ($movimientos['egresostransferenciasinterbancos'] as $l) {//muestro el detalle
                            $egresostransferenciasinterbancos += $l['Bancostransferencia']['importe'];
                        }
                    }
                    ?>
                    <tr style="border-top:1px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?php echo __("Egresos Transferencias Interbancarias") ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.xxdeti')">[+/-]</span></td>
                        <td style='text-align:right'><?= $this->Functions->money(-$egresostransferenciasinterbancos) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if (!empty($movimientos['egresostransferenciasinterbancos'])) {
                        foreach ($movimientos['egresostransferenciasinterbancos'] as $l) {//muestro el detalle
                            echo "<tr style=\"display:none" . color($l['Bancostransferencia']['anulado']) . "\" class=\"xxdeti\">";
                            echo '<td class="borde_tabla"></td>';
                            echo "<td style='padding-left:50px'>" . h($this->Time->format(__('d/m/Y'), $l['Bancostransferencia']['created']) . " - De " . $cuentas[$l['Bancostransferencia']['bancoscuenta_id']] . " a " . $cuentas[$l['Bancostransferencia']['destino_id']] . " - " . $l['Bancostransferencia']['concepto']);
                            echo "</td><td style='text-align:right'>" . $this->Functions->money(-$l['Bancostransferencia']['importe']) . "&nbsp;</td>";
                            echo '<td class="borde_tabla"></td>';
                            echo "</tr>";
                        }
                    }

                    $totalingresobancoefectivo = -$egresosdebitosbancarios - $ingresoextraccionbancaria;
                    $totalingresobancocheque = $ingresotransferencia + $ingresostransferenciasinterbancos + $ingresoscreditosbancarios + ($ingresodepositosefectivo + $ingresodepositoscheque);
                    $totalingresobancocheque -= ($egresoproveedorchequepropio + $egresostransferencias + $egresostransferenciasinterbancos);
                    ?>
                    <tr style="border:3px solid gray;font-weight:bold">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Movimientos Banco")) ?></td>
                        <td style='text-align:right'><?= $this->Functions->money($totalingresobancoefectivo + $totalingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr style="border:3px solid gray;font-weight:bold">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Banco Actual")) ?></td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresobancoefectivo + $saldoingresobancocheque + $totalingresobancoefectivo + $totalingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="4"></td>
                        <td class="bottom_d"></td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('CONSORCIO') ?></th>
                        <th style='text-align:right'><?php echo __('Caja') ?></th>
                        <th style='text-align:right'><?php echo __('Banco') ?></th>
                        <th style='text-align:right'><?php echo __('Total') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SALDO CONSORCIO ANTERIOR
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Consorcio Anterior</td><td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo + $saldoingresocajacheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresobancoefectivo + $saldoingresobancocheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresobancoefectivo + $saldoingresocajaefectivo + $saldoingresobancocheque + $saldoingresocajacheque) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    ?>
                    <tr style="border:3px solid gray;font-weight:bold">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Movimientos Consorcio")) ?></td>
                        <td style='text-align:right'><?= $this->Functions->money($totalingresocajaefectivo + $totalingresocajacheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($totalingresobancoefectivo + $totalingresobancocheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($totalingresocajaefectivo + $totalingresobancoefectivo + $totalingresocajacheque + $totalingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr style="border:3px solid gray;font-weight:bold">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Consorcio Actual")) ?></td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresocajaefectivo + $saldoingresocajacheque + $totalingresocajaefectivo + $totalingresocajacheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresobancoefectivo + $saldoingresobancocheque + $totalingresobancoefectivo + $totalingresobancocheque) ?>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresocajaefectivo + $saldoingresocajacheque + $totalingresocajaefectivo + $totalingresocajacheque + $saldoingresobancoefectivo + $saldoingresobancocheque + $totalingresobancoefectivo + $totalingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="4"></td>
                        <td class="bottom_d"></td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('CHEQUES A FUTURO') ?> <span style="cursor:pointer" onclick="toggle3()">[+/-]</span></th>
                        <th><?php echo __('Concepto') ?></th>
                        <th><?php echo __('Fecha Emisión') ?></th>
                        <th><?php echo __('Fecha Vencimiento') ?></th>
                        <th style='text-align:right'><?php echo __('Importe') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($movimientos['chequesafuturo'] as $v) {
                        $total += $v['Chequespropio']['importe'];
                        echo "<tr class=\"chequefuturo\" style='display:none;border:3px solid gray;font-weight:bold'>";
                        echo '<td class="borde_tabla"></td>';
                        echo "<td>&nbsp;</td>";
                        echo "<td>" . h($v['Chequespropio']['concepto']) . "&nbsp;</td>";
                        echo "<td>" . $this->Time->format(__('d/m/Y'), $v['Chequespropio']['fecha_emision']) . "&nbsp;</td>";
                        echo "<td>" . $this->Time->format(__('d/m/Y'), $v['Chequespropio']['fecha_vencimiento']) . "&nbsp;</td>";
                        echo "<td style='text-align:right'>" . $this->Functions->money($v['Chequespropio']['importe']) . "&nbsp;</td>";
                        echo '<td class="borde_tabla"></td>';
                        echo "</tr>";
                    }
                    echo "<tr style='border:3px solid gray;font-weight:bold'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td colspan='3'>&nbsp;</td>";
                    echo "<td>Total</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($total) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";
                    ?>
                </tbody>
            </table>
        </div>
        <?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC                                                           ?>
        <script>
            function toggle(detalle) {
                $(detalle).fadeToggle("fast");
            }
            function toggle2() {
                $('[class^="xx"]').fadeToggle("fast");
            }
            function toggle3() {
                $('[class^="chequefuturo"]').fadeToggle("fast");
            }
            var dialog = $("#rc").dialog({
                autoOpen: false, height: "auto", width: "900", maxWidth: "900",
                position: {at: "center top"},
                closeOnEscape: false,
                modal: true, buttons: {
                    Cerrar: function () {
                        $("#rc").html('');
                        dialog.dialog("close");
                    }
                }
            });
        </script>
        <style>
            .titulo{
                display:block;
            }
        </style>
        <?php
    }
    ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({/*maxDate: '0',*/ minDate: new Date(2016, 6, 1), changeYear: true, yearRange: '2016:+1'});
        $("#ConsorcioConsorcio").select2({language: "es", placeholder: '<?= __("Seleccione consorcio...") ?>', allowClear: true});
        $("#ConsorcioResumenForm").submit(function (event) {
            if ($("#ConsorcioConsorcio").val() === "") {
                alert('<?= __('Seleccione un Consorcio') ?>');
                return false;
            }
            var f1 = $("#ConsorcioDesde").val();
            var f2 = $("#ConsorcioHasta").val();
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
            $("#guardar").prop('disabled', true);
            return true;
        });
    });
    $("#a").click(function () {
        var d = $("#ConsorcioDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#ConsorcioDesde").val($.datepicker.formatDate("dd/mm/yy", addMonths(x, -1)));
        var d = $("#ConsorcioDesde").val();
        var dias = new Date(d.substr(6, 4), d.substr(3, 2), 0).getDate();
        var d = $("#ConsorcioHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#ConsorcioHasta").val($.datepicker.formatDate(dias + "/mm/yy", addMonths(x, -1)));
        $(".dp").change();
    });
    $("#b").click(function () {
        var d = $("#ConsorcioDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#ConsorcioDesde").val($.datepicker.formatDate("dd/mm/yy", addMonths(x, 1)));
        var d = $("#ConsorcioDesde").val();
        var dias = new Date(d.substr(6, 4), d.substr(3, 2), 0).getDate();
        var d = $("#ConsorcioHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        $("#ConsorcioHasta").val($.datepicker.formatDate(dias + "/mm/yy", addMonths(x, 1)));
        $(".dp").change();
    });

    $("#c").click(function () {
        var d = $("#ConsorcioDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        x.setDate(x.getDate() - 1);
        $("#ConsorcioDesde").val($.datepicker.formatDate("dd/mm/yy", x));
        var d = $("#ConsorcioHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        x.setDate(x.getDate() - 1);
        $("#ConsorcioHasta").val($.datepicker.formatDate("dd/mm/yy", x));
        $(".dp").change();
    });
    $("#d").click(function () {
        var d = $("#ConsorcioDesde").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        x.setDate(x.getDate() + 1);
        $("#ConsorcioDesde").val($.datepicker.formatDate("dd/mm/yy", x));
        var d = $("#ConsorcioHasta").val();
        var x = new Date(d.substr(6, 4), d.substr(3, 2) - 1, d.substr(0, 2), 0, 0, 0);
        x.setDate(x.getDate() + 1);
        $("#ConsorcioHasta").val($.datepicker.formatDate("dd/mm/yy", x));
        $(".dp").change();
    });
    function isLeapYear(year) {
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
    }

    function getDaysInMonth(year, month) {
        return [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    function addMonths(date, value) {
        var d = new Date(date),
                n = date.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth() + value);
        d.setDate(Math.min(n, getDaysInMonth(d.getFullYear(), d.getMonth())));
        return d;
    }
</script>
<?php
/*
 * Los movimientos anulados anteriores al 2019-05-12 00:00:00 no se muestran, el resto si
 */

//function muestra($data) {
//    if (strtotime($data['created']) <= strtotime('2019-05-12 00:00:00') && $data['anulado']) {
//        return false;
//    }
//    return true;
//}

function color($anulado) {
    if ($anulado) {
        return ";color:red";
    } else {
        return "";
    }
}
