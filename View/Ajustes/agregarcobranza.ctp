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
        <li><a href="#tabs-1">Ajuste</a></li>
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
        $cobranzatemp = [];
        foreach ($cobranzas as $k => $v) {
            $cobranzatemp[$k] = $v['Cobranza'];
            //$cobranzatemp[$k]['fecha'] .= " " . date("H:i:s", strtotime($cobranzatemp[$k]['created'])); // para q la cobranza si se hizo en el mismo dia del cierre de la liq, quede despues de la liquidacion en el reporte
            $cobranzatemp[$k]['fecha'] = $cobranzatemp[$k]['created'];
            $cobranzatemp[$k] += $v['Cobranzatipoliquidacione'];
        }

        $ajustetemp = [];
        foreach ($ajustes as $k => $v) {
            $ajustetemp[$k] = $v['Ajuste'];
            $ajustetemp[$k] += $v['Ajustetipoliquidacione'];
        }
        $totales = $this->Functions->array_sort(array_merge($saldostemp, $cobranzatemp, $ajustetemp), 'fecha');
        $totalesxtipo = []; // para setear el importe total de deuda de cada tipo de liq
        $totalgeneral = 0;
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
        echo $this->element('fechas', ['url' => ['controller' => 'Reports', 'action' => 'cuentacorrientepropietario'], 'model' => 'Ajuste']);
        ?>
        <div class="cobranzasform">
            <div class="parent" style="display:flex;flex-wrap:wrap">
                <div style="width:380px">
                    <?php echo $this->Form->create('Ajuste', ['class' => 'jquery-validation', 'url' => ['action' => 'add'], 'id' => 'guardarajuste']); ?>
                    <fieldset>
                        <p class="error-message">* Campos obligatorios</p>
                        <?php
                        echo $this->JqueryValidation->input('propietario_id', ['value' => $propietario_id, 'type' => 'hidden']);
                        echo $this->JqueryValidation->input('importe', ['value' => 0, 'type' => 'hidden']);
                        echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *', 'value' => "AJ " . h($datospropietario['Consorcio']['name'] . " - " . $datospropietario['Propietario']['name'] . " (" . $datospropietario['Propietario']['unidad'] . ")"), 'tabindex' => 1]);
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
                        echo $this->Form->end(['id' => 'guardarc', 'label' => __('Guardar')]);
                        ?>
                    </fieldset>
                </div>
                <div>
                    <div class="cctep" style="flex:1;margin-left:10px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $("#tabs").tabs({height: "auto"});
            $("#AjusteConcepto").focus();
            $("#AjusteFechaDay").select2({language: "es"});
            $("#AjusteFechaMonth").select2({language: "es"});
            $("#AjusteFechaYear").select2({language: "es"});
            $("#guardarc").click(function () {
                if ($("#AjusteAddConcepto").val() === "") {
                    alert('<?= __("Debe ingresar un concepto") ?>');
                    return false;
                }
                var total = 0.00;
                $("input[id^='AjusteLt']").each(function () {
                    if ($(this).val() < 0) {
                        total = -1; // pongo el total menor a cero asi al salir del loop es total <= 0
                        return false;
                    }
                    total += parseFloat($(this).val());
                });
                if (total <= 0) {
                    alert('<?= __("Los importes deben ser mayores a cero") ?>');
                    return false;
                }
                $("#AjusteImporte").val(total);
                //if (confirm('<?= __("Desea guardar el ajuste?") ?>')) {
                $("#guardarc").prop('disabled', true);
                $("#guardarajuste").submit();
                //}
                return false;
            });
<?php
/* se llama al hacer click en el tab "Formas de pago" */
$fecha = strtotime(date("Y-m-d") . ' -3 months');
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
        });
    </script>
