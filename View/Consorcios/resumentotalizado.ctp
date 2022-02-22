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
<div class="" id="noimprimir">
    <h2><?php echo __('Resumen Caja Banco'); ?></h2>
    <?php
    echo $this->Form->create('Consorcio', ['class' => 'inline']);
    echo $this->JqueryValidation->input('consorcio', ['label' => false, 'empty' => '', 'options' => [0 => __('TODOS')] + $consorcios, 'type' => 'select', 'selected' => isset($this->request->data['Consorcio']['consorcio']) ? $this->request->data['Consorcio']['consorcio'] : 0]);
    echo "<b>Desde</b> " . $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => isset($this->request->data['Consorcio']['desde']) ? $this->request->data['Consorcio']['desde'] : date("01/m/Y")]);
    echo "<b>Hasta</b> " . $this->Form->input('hasta', ['label' => false, 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => isset($this->request->data['Consorcio']['hasta']) ? $this->request->data['Consorcio']['hasta'] : date("d/m/Y")]);
    echo "<span id='a' title='Mes anterior' style='font-size:15px;font-weight:bold;cursor:pointer'>-1 mes</span>&nbsp;&nbsp;<span id='b' title='Mes siguiente' style='font-size:15px;font-weight:bold;cursor:pointer'> +1 mes</span>&nbsp;&nbsp;";
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Ver'), 'id' => 'guardar', 'style' => 'width:50px']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:inline-block'></div>";
    ?>
    <div id="seccionaimprimir" style="display:none;width:100%">
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
                    <th class="esq_i"></th>
                    <th><?php echo __('Consorcio') ?>&nbsp;<span style="cursor:pointer" onclick="toggle2()">[+/-]</span></th>
                    <th style='text-align:right'><?php echo __('Efectivo') ?></th>
                    <th style='text-align:right'><?php echo __('Cheque') ?></th>
                    <th style='text-align:right'><?php echo __('Total') ?></th>
                    <th class="esq_d"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalSaldoCajasAnterior = $totalSaldoBancosAnterior = $totalSaldoConsorciosAnterior = 0;
                $totalMovimientosCajas = $totalMovimientosBancos = $totalMovimientosConsorcios = 0;
                $totalSaldoCajasActual = $totalSaldoCajasActualEfectivo = $totalSaldoCajasActualCheque = $totalSaldoBancosActual = $totalSaldoConsorciosActual = 0;

                foreach ($consorcios as $k => $v) {

                    $saldoDiaActual = ['egresospagosacuenta' => 0, 'ingresosmanuales' => 0, 'egresosmanuales' => 0, 'saldocajaefectivo' => 0, 'saldocajacheque' => 0, 'saldobancoefectivo' => 0, 'saldobancocheque' => 0];
                    if (!empty($movimientosDiaActual)) {
                        $saldoDiaActual['saldocajaefectivo'] = $movimientosDiaActual[$k]['ingresosefectivo'] + $movimientosDiaActual[$k]['ingresosmanuales']['e'] + $movimientosDiaActual[$k]['ingresosextracciones'] - $movimientosDiaActual[$k]['egresospagosproveedorefectivo'] - $movimientosDiaActual[$k]['egresosmanuales']['e'] - $movimientosDiaActual[$k]['bancosdepositosefectivo'];
                        $saldoDiaActual['saldocajacheque'] = $movimientosDiaActual[$k]['ingresoscheque'] + $movimientosDiaActual[$k]['ingresosmanuales']['c'] - $movimientosDiaActual[$k]['egresosmanuales']['c'] - $movimientosDiaActual[$k]['egresospagosproveedorcheque'] - $movimientosDiaActual[$k]['bancosdepositoscheques']; // egresos pac cheque
                        $saldoDiaActual['saldobancoefectivo'] = -$movimientosDiaActual[$k]['egresosdebitos'] - $movimientosDiaActual[$k]['ingresosextracciones'];
                        $saldoDiaActual['saldobancocheque'] = $movimientosDiaActual[$k]['ingresostransferencias'] + $movimientosDiaActual[$k]['ingresostransferenciasinterbancos'] + $movimientosDiaActual[$k]['ingresoscreditos'] + $movimientosDiaActual[$k]['bancosdepositosefectivo'] + $movimientosDiaActual[$k]['bancosdepositoscheques'] - ($movimientosDiaActual[$k]['egresospagosproveedorchequepropio'] + $movimientosDiaActual[$k]['egresospagosproveedortransferencia'] + $movimientosDiaActual[$k]['egresostransferenciasinterbancos']);
                    }
                    ?>
                    <tr style="border:2px solid gray">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<b><?= h($v) ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.consor<?= $k ?>')">[+/-]</span></td>
                        <td style='text-align:right;font-weight:bold' id="efec<?= $k ?>">&nbsp;</td>
                        <td style='text-align:right;font-weight:bold' id="bancheq<?= $k ?>">&nbsp;</td>
                        <td style='text-align:right;font-weight:bold' id="tot<?= $k ?>">&nbsp;</td>
                        <td class="borde_tabla"></td> 
                    </tr>
                    <?php
                    $saldoingresocajaefectivo = $saldos[$k]['desde']['saldocajaefectivo'];
                    $saldoingresocajacheque = $saldos[$k]['desde']['saldocajacheque'];

                    // SALDO CAJA ANTERIOR
                    echo "<tr style='border:3px solid gray;border-bottom:0;font-weight:bold;display:none' class='consor$k'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Caja Anterior</td><td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajacheque) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivo + $saldoingresocajacheque) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";

                    $totalSaldoCajasAnterior += ($saldoingresocajaefectivo + $saldoingresocajacheque);

                    $saldoingresocajaefectivohasta = $saldos[$k]['hasta']['saldocajaefectivo'];
                    $saldoingresocajachequehasta = $saldos[$k]['hasta']['saldocajacheque'];

                    echo "<tr style='border-left:3px solid gray;border-right:3px solid gray;font-weight:bold;display:none' class='consor$k'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Movimientos Caja</td><td style='text-align:right'>" . $this->Functions->money(($saldoingresocajaefectivohasta - $saldoingresocajaefectivo) + $saldoDiaActual['saldocajaefectivo']) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money(($saldoingresocajachequehasta - $saldoingresocajacheque) + $saldoDiaActual['saldocajacheque']) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money(($saldoingresocajaefectivohasta - $saldoingresocajaefectivo) + ($saldoingresocajachequehasta - $saldoingresocajacheque) + $saldoDiaActual['saldocajaefectivo'] + $saldoDiaActual['saldocajacheque']) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";

                    $totalSaldoCajasActualEfectivo += $saldoingresocajaefectivohasta + $saldoDiaActual['saldocajaefectivo'];
                    $totalSaldoCajasActualCheque += $saldoingresocajachequehasta + $saldoDiaActual['saldocajacheque'];

                    //es el saldo total de caja actual por edificio
                    $saldoCajaActualTotal = ($saldoingresocajaefectivohasta + $saldoingresocajachequehasta + $saldoDiaActual['saldocajaefectivo'] + $saldoDiaActual['saldocajacheque']);

                    //es la variable utilizada para mostrar el saldo total de cajas actual sumando todos los edificios
                    $totalSaldoCajasActual += $saldoCajaActualTotal;

                    $totalMovimientosCajas += ( ($saldoingresocajaefectivohasta - $saldoingresocajaefectivo) + ($saldoingresocajachequehasta - $saldoingresocajacheque) );

                    // SALDO CAJA ACTUAL
                    echo "<tr style='border-left:3px solid gray;border-right:3px solid gray;font-weight:bold;display:none' class='consor$k'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Caja Actual</td><td style='text-align:right'>" . $this->Functions->money($saldoingresocajaefectivohasta + $saldoDiaActual['saldocajaefectivo']) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresocajachequehasta + $saldoDiaActual['saldocajacheque']) . "&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoCajaActualTotal) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";

                    //BANCO
                    // MUESTRO LOS SALDOS ANTERIORES
                    $saldoingresobancoefectivo = $saldos[$k]['desde']['saldobancoefectivo'];
                    $saldoingresobancocheque = $saldos[$k]['desde']['saldobancocheque'];
                    $saldoingresobancoefectivohasta = $saldos[$k]['hasta']['saldobancoefectivo'];
                    $saldoingresobancochequehasta = $saldos[$k]['hasta']['saldobancocheque'];
                    ?>
                    <tr style="border:3px solid gray;border-bottom:0;font-weight:bold;display:none" class='consor<?= $k ?>'>
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Banco Anterior")) ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresobancoefectivo + $saldoingresobancocheque) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    $totalSaldoBancosAnterior += ($saldoingresobancoefectivo + $saldoingresobancocheque);
                    ?>
                    <tr style = "border-left:3px solid gray;border-right:3px solid gray;font-weight:bold;display:none" class='consor<?= $k ?>'>
                        <td class = "borde_tabla"></td>
                        <td>&nbsp;
                            <?php echo h(__("Movimientos Banco"))
                            ?>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(($saldoingresobancoefectivohasta - $saldoingresobancoefectivo) + ($saldoingresobancochequehasta - $saldoingresobancocheque) + $saldoDiaActual['saldobancoefectivo'] + $saldoDiaActual['saldobancocheque']) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    $totalSaldoBancosActual += ($saldoingresobancoefectivohasta + $saldoingresobancochequehasta);
                    $totalMovimientosBancos += ( ($saldoingresobancoefectivohasta - $saldoingresobancoefectivo) + ($saldoingresobancochequehasta - $saldoingresobancocheque) );
                    ?>
                    <tr style="border-left:3px solid gray;border-right:3px solid gray;font-weight:bold;display:none" class='consor<?= $k ?>'>
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Banco Actual")) ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money($saldoingresobancoefectivohasta + $saldoingresobancochequehasta + $saldoDiaActual['saldobancoefectivo'] + $saldoDiaActual['saldobancocheque']) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    //CONSORCIO
                    // SALDO CONSORCIO ANTERIOR
                    echo "<tr style='border:3px solid gray;border-bottom:0;font-weight:bold;display:none' class='consor$k'>";
                    echo '<td class="borde_tabla"></td>';
                    echo "<td>&nbsp;Saldo Consorcio Anterior</td><td style='text-align:right'>&nbsp;</td>";
                    echo "<td>&nbsp;</td>";
                    echo "<td style='text-align:right'>" . $this->Functions->money($saldoingresobancoefectivo + $saldoingresocajaefectivo + $saldoingresobancocheque + $saldoingresocajacheque) . "&nbsp;</td>";
                    echo '<td class="borde_tabla"></td>';
                    echo "</tr>";

                    $totalSaldoConsorciosAnterior += ($saldoingresobancoefectivo + $saldoingresocajaefectivo + $saldoingresobancocheque + $saldoingresocajacheque);
                    ?>
                    <tr style="border-left:3px solid gray;border-right:3px solid gray;font-weight:bold;display:none" class='consor<?= $k ?>'>
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Movimientos Consorcio")) ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style='text-align:right'><?= $this->Functions->money(( (($saldoingresobancoefectivohasta + $saldoingresocajaefectivohasta) - ($saldoingresobancoefectivo + $saldoingresocajaefectivo)) + $saldoDiaActual['saldocajaefectivo'] + $saldoDiaActual['saldobancoefectivo'] ) + ( (($saldoingresobancochequehasta + $saldoingresocajachequehasta) - ($saldoingresobancocheque + $saldoingresocajacheque)) + $saldoDiaActual['saldocajacheque'] + $saldoDiaActual['saldobancocheque'] )) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    // es el saldo total actual del consorcio
                    $saldoConsorcioActualTotal = ($saldoingresobancoefectivohasta + $saldoingresocajaefectivohasta + $saldoingresobancochequehasta + $saldoingresocajachequehasta + $saldoDiaActual['saldocajaefectivo'] + $saldoDiaActual['saldobancoefectivo'] + $saldoDiaActual['saldocajacheque'] + $saldoDiaActual['saldobancocheque']);
                    ?>
                    <tr style="border:3px solid gray;border-top:0;font-weight:bold;display:none" class='consor<?= $k ?>'>
                        <td class="borde_tabla"></td>
                        <td>&nbsp;<?php echo h(__("Saldo Consorcio Actual")) ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style='text-align:right' id="totalactual<?= $k ?>"><?= $this->Functions->money($saldoConsorcioActualTotal) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="4"></td>
                        <td class="bottom_d"></td>
                    </tr>
                    <?php
                    //es la variable utilizada para mostrar el saldo total actual de consorcios sumando todos los consorcios
                    $totalSaldoConsorciosActual += $saldoConsorcioActualTotal;
                    $totalMovimientosConsorcios += ( ( (($saldoingresobancoefectivohasta + $saldoingresocajaefectivohasta) - ($saldoingresobancoefectivo + $saldoingresocajaefectivo)) + $saldoDiaActual['saldocajaefectivo'] + $saldoDiaActual['saldobancoefectivo'] ) + ( (($saldoingresobancochequehasta + $saldoingresocajachequehasta) - ($saldoingresobancocheque + $saldoingresocajacheque)) + $saldoDiaActual['saldocajacheque'] + $saldoDiaActual['saldobancocheque'] ) );
                }
                ?>
            </tbody>
        </table>            
        <table>
            <tbody>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan='4'>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

<!--                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO CAJAS ANTERIOR</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($totalSaldoCajasAnterior) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL MOVIMIENTOS CAJAS</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($totalMovimientosCajas) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>-->

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO EFECTIVO CAJAS ACTUAL</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalSaldoCajasActualEfectivo) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO CHEQUE CAJAS ACTUAL</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalSaldoCajasActualCheque) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO CAJAS ACTUAL</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="border-top:3px solid gray;font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalSaldoCajasActual) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan='4'>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

<!--                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO BANCOS ANTERIOR</td>
                    <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($totalSaldoBancosAnterior) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL MOVIMIENTOS BANCOS</td>
                    <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($totalMovimientosBancos) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO BANCOS ACTUAL</td>
                    <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($totalSaldoBancosActual) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>-->

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO CONSORCIOS ANTERIOR</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalSaldoConsorciosAnterior) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL MOVIMIENTOS CONSORCIOS</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalMovimientosConsorcios) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL SALDO CONSORCIOS ACTUAL</td>
                    <td style='width:50px'>&nbsp;</td>
                    <td style="border-top:3px solid gray;font-weight:bold;text-align:right;width:100px"><?= $this->Functions->money($totalSaldoConsorciosActual) ?>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
<style>
    .titulo{
        display:block;
    }
    @media print {
        table{
            font-size:12px !important;
            font-weight:400 !important;
        }
        .acciones {display:none;}
        table thead{line-height:10px}
    }
</style>
<script>
    function toggle(detalle) {
        $(detalle).fadeToggle("fast");
    }
    var v = false;
    function toggle2() {
        if (v) {
            $('[class^="consor"]').hide();
            v = false;
        } else {
            $('[class^="consor"]').show();
            v = true;
        }
    }
</script>
<script>
    $(function () {
        $(".dp").datepicker({maxDate: '0', minDate: new Date(2016, 0, 1), changeYear: true, yearRange: '2016:+1'});
        $("#ConsorcioConsorcio").select2({language: "es", placeholder: '<?= __("Seleccione consorcio...") ?>', allowClear: true});

        $("td[id^='efec']").each(function () {
            var strid = $(this).attr('id');
            var cid = strid.replace('efec', '');
            $("#efec" + cid).html($("#efectivo" + cid).html());
            $("#bancheq" + cid).html($("#bancocheque" + cid).html());
            $("#tot" + cid).html($("#totalactual" + cid).html());
        });
        $("#seccionaimprimir").show();
        $("#load").hide();
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

    $('#guardar').on('click', function (e) {
        e.preventDefault();
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
        $("form").submit();
    });
</script>