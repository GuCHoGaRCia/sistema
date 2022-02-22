<?php
if (empty($propietario_id)) {
    ?>
    <div class="error">El Código ingresado no pertenece a un Propietario habilitado</div>
    <?php
    die;
}
?>
<br>
<div id="tabs" style="height:auto">
    <ul>
        <li><a href="#tabs-1" id="tabcobranza" >Cobranza</a></li>
    </ul>
    <div id="tabs-1">
        <?php
        //echo $this->element('cuentacorrientepropietario');
        $saldostemp = [];
        $saldosini = [];

        // junto los modelos y los borro para q sea mas facil mostrar los datos
        foreach ($saldos['saldos'] as $k => $v) {
            $saldostemp[$k] = $v['SaldosCierre'];
            $saldostemp[$k] += $v['Liquidation'];
        }

        if (isset($saldos['iniciales'])) {
            foreach ($saldos['iniciales'] as $k => $v) {
                $saldosini[$v['SaldosIniciale']['liquidations_type_id']] = $v['SaldosIniciale'];
            }
        }
        $cobranzatemp = $ccc = [];
        foreach ($cobranzas as $k => $v) {
            $cobranzatemp[$k] = $v['Cobranza'];
            //$cobranzatemp[$k]['fecha'] .= " " . date("H:i:s", strtotime($cobranzatemp[$k]['created'])); // para q la cobranza si se hizo en el mismo dia del cierre de la liq, quede despues de la liquidacion en el reporte
            $cobranzatemp[$k]['fecha'] = $cobranzatemp[$k]['created'];
            $cobranzatemp[$k] += $v['Cobranzatipoliquidacione'];

            // dialog historico cobranzas
            if (!isset($ccc[$v['Cobranza']['id']])) {
                $ccc[$v['Cobranza']['id']] = $v['Cobranza'];
                $ccc[$v['Cobranza']['id']]['monto'] = 0;
            }
            $ccc[$v['Cobranza']['id']]['monto'] += $v['Cobranzatipoliquidacione']['amount'];
        }

        // dialog historico cobranzas
        echo "<div id='historicocobranzas' style='display:none'><h3>Hist&oacute;rico Cobranzas</h3>";
        foreach (array_reverse($ccc, true) as $c) {
            echo "<p>" . date('d/m/Y', strtotime($c['fecha'])) . " - " . $this->Functions->money($c['monto']) . " ";
            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'cobranzas', 'action' => 'view', $c['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
            echo "</p>";
        }
        echo "</div>";

        $ajustetemp = [];
        foreach ($ajustes as $k => $v) {
            $ajustetemp[$k] = $v['Ajuste'];
            $ajustetemp[$k] += $v['Ajustetipoliquidacione'];
            $ajustetemp[$k]['fecha'] .= " 00:00:00"; // para q la cobranza si se hizo en el mismo dia del cierre de la liq, quede despues de la liquidacion en el reporte
        }
        $totales = $this->Functions->array_sort(array_merge($saldostemp, $cobranzatemp, $ajustetemp), 'fecha');
        //debug($totales);
        $totalesxtipo = []; // para setear el importe total de deuda de cada tipo de liq
        $totalgeneral = 0;
        // ordeno el resultado, sino quedan los index 1,2,0,3,4
        // ksort($totales);
        // muestro los saldos para cada tipo de liquidación
        foreach ($liquidations_type_id as $index => $tipos) {
            // muestro el saldo inicial
            $saldo = $totalcapital = $totalinteres = 0;
            if (isset($saldosini[$index])) {
                $saldo = $saldosini[$index]['capital'] + $saldosini[$index]['interes'];
            }
            // muestro las liq y las cobranzas
            foreach ($totales as $dato) {
                if (isset($dato['liquidations_type_id']) && "$index" === $dato['liquidations_type_id']) {
                    if (isset($dato['closed'])) { // es una liquidacion
                        $saldo = $dato['capital'] + $dato['interes'] - round($dato['capital'] + $dato['interes'] - intval($dato['capital'] + $dato['interes']), 2);
                    }
                    if (isset($dato['amount'])) { // es una cobranza o ajuste
                        $saldo -= $dato['amount'];
                    }
                }
            }
            $totalgeneral += $saldo;
            $totalesxtipo[$index] = $saldo;
        }
        $this->set('totalesxtipo', $totalesxtipo);
        ?>
        <div class="cobranzasform">
            <?php
            if (!empty($datospropietario['Propietario']['estado_judicial'])) {
                echo "<div class='warning'>El estado judicial del Propietario es: " . h($datospropietario['Propietario']['estado_judicial']) . "</div>";
            }
            ?>
            <div class="parent" style="display:flex;flex-wrap:wrap">
                <div style="width:380px">
                    <p class="error-message">* Campos obligatorios - 
                        <a href="#" onclick='javascript:$("#pid").val("<?= $datospropietario['Propietario']['id'] ?>");$("#dfechascc").dialog("open")'>Cta. Cte.</a> - 
                        <a href="#" onclick='javascript:$("#pidx").val("<?= $datospropietario['Propietario']['id'] ?>");$("#cupon").dialog("open")'>Cup&oacute;n Pago</a>
                        <a href="#" onclick='$("#historicocobranzas").dialog("open")'>Hist&oacute;rico cobranzas</a>
                    </p>
                    <?php echo $this->Form->create('Cobranza', ['class' => 'jquery-validation', 'url' => ['controller' => 'Cobranzas', 'action' => 'add2'], 'id' => 'guardarcobranza']); ?>
                    <fieldset>
                        <?php
                        $concepto = h("CM " . $datospropietario['Consorcio']['name'] . " - " . $datospropietario['Propietario']['name'] . " - " . $datospropietario['Propietario']['unidad'] . " (" . $datospropietario['Propietario']['code'] . ")"); // se usa tambien al agregar cheque, se le pone el mismo concepto precargado
                        echo "<label>Recibimos de: <input type='text' name='data[Cobranza][recibimosde]' value='$concepto' readonly='readonly' /></label>";
                        echo $this->JqueryValidation->input('propietario_id', ['value' => $propietario_id, 'type' => 'hidden']);
                        echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *', /* 'value' => $concepto, */ 'tabindex' => 1]);
                        echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px', 'tabindex' => 2]);
                        $index = 3;
                        foreach ($liquidations_type_id as $k => $v) {
                            $tot = round($this->get('totalesxtipo')[$k], 2);
                            echo "<div class='inline'>";
                            echo $this->JqueryValidation->input('lt_' . $k, ['label' => h($v), 'type' => 'number', 'min' => 0, 'step' => 0.01, 'required' => 'required', 'value' => ($tot > 0 ? $tot : 0), 'tabindex' => $index]);
                            echo $this->JqueryValidation->input('chk_' . $k, ['label' => __('Sólo capital?'), 'type' => 'checkbox', 'style' => 'margin-top:15px']);
                            echo "</div>";
                            $index++;
                        }
                        ?>
                    </fieldset>
                </div>
                <div>
                    <div class="cctep" style="flex:1;margin-left:10px;">
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo $this->element('propietarioformasdepago');
        echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardarc']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
        ?>
    </div>
    <?php
    echo $this->element('fechas', ['url' => ['controller' => 'Reports', 'action' => 'cuentacorrientepropietario', 'Cobranza'], 'model' => 'Cobranza']);
    echo $this->element('propietariocuponpago');
    ?>
    <script>
        $(function () {
            var dialog = $("#historicocobranzas").dialog({
                autoOpen: false, height: "500", width: "500", maxWidth: "500",
                position: {at: "center top"},
                closeOnEscape: false,
                modal: true, buttons: {
                    Cerrar: function () {
                        dialog.dialog("close");
                    }
                }
            });
            $("input[id^='CobranzaLt']").change(function () {
                calcula();
            });
            $("#tabs").tabs({height: "auto"});
            $("#CobranzaConcepto").focus();
            $("#CobranzaFechaDay").select2({language: "es"});
            $("#CobranzaFechaMonth").select2({language: "es"});
            $("#CobranzaFechaYear").select2({language: "es"});
            $("#guardarc").click(function () {
                if ($("#montototal").val() === "0") {
                    alert('<?= __("El importe debe ser mayor a cero") ?>');
                    return false;
                }
                if ($("#transferencia").val() > 0 && $("#formadepago").val() === '') {
                    alert('<?= __("Debe seleccionar Forma de Pago") ?>');
                    return false;
                }
                if (parseFloat($("#totalchequeterc").val()) > 0) {<?php /* quito los cheques que estén en cero (sin utilizar) */ ?>
                    $("input[id^='lcht_']").each(function () {
                        if ($(this).val() === '0') {
                            $(this).remove();
                        }
                    });
                }
                /*$("input[id^='banco_']").each(function () {
                 var id = $(this).attr('id');
                 var id2 = id.replace('banco_', '');
                 if (parseFloat($(this).val()) === 0) {
                 $(this).prop('disabled', true);<?php /* quito el banco y la cuenta bancaria asociada */ ?>
                 $("bancoscuenta_id_" + id2).prop('disabled', true);
                 }
                 });*/
                var total = 0;
                $("input[id^='CobranzaLt']").each(function () {
                    total += parseFloat($(this).val());
                });
                var todos = parseFloat(parseFloat($("#efectivo").val()) + parseFloat($("#totalchequeterc").val()) + parseFloat($("#transferencia").val()));
                if (parseFloat(total).toFixed(2) !== parseFloat(todos).toFixed(2)) {
                    alert('<?= __("El importe abonado debe ser igual a la suma de los importes a pagar") ?>');
                    return false;
                }
                $("#load").show();
                $("#guardarc").prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "<?= $this->webroot ?>Cobranzas/add2",
                    data: $("#guardarcobranza").serialize()
                }).done(function (msg) {
                    try {
                        var obj = JSON.parse(msg);
                        if (obj.e === 1) {
                            alert(obj.d);
                        } else {
                            window.location.replace("<?= $this->webroot ?>Cobranzas/add2/" + consorcio_id);
                        }
                    } catch (err) {
                        //
                    }
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion, intente nuevamente");
                    }
                }).always(function (jqXHR, textStatus) {
                    $("#guardarc").prop('disabled', false);
                    $("#load").hide();
                });
            });
<?php
/* se llama al hacer click en el tab "Formas de pago" */
$fecha = strtotime(date("Y-m-d") . ' -6 months');
?>
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Reports/cuentacorrientepropietario", cache: false, data: {pid: <?= $datospropietario['Propietario']['id'] ?>, f1: '<?= date("d/m/Y", $fecha) ?>', f2: '<?= date("d/m/Y") ?>', origen: '1'}}).done(function (msg) {
                $(".cctep").html(msg);
                $("#print").hide();
                $(".box-table-ax").hide();
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    $(".cctep").html("No se pudo obtener la cuenta corriente. Verifique que se encuentra logueado en el sistema");
                } else {
                    $(".cctep").html("No se pudo obtener la cuenta corriente");
                }
            });
            calcula();
        });
    </script>
