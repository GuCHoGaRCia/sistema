<div class="proveedorsfacturas form">
    <?php echo $this->Form->create('Proveedorsfactura', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Factura de proveedor'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('proveedor_id', ['label' => __('Proveedor') . ' *', 'empty' => __('Seleccione Proveedor...'), 'style' => 'width:600px']);
        echo $this->JqueryValidation->input('liquidation_id', ['label' => __('Consorcio / Liquidación') . ' *', 'empty' => __('Seleccione Liquidación...'), 'style' => 'width:600px']);
        echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px']);
        echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *']);
        echo $this->JqueryValidation->input('importe', ['label' => __('Importe') . ' *', 'step' => 0.01]);
        echo $this->JqueryValidation->input('numero', ['label' => __('Número de factura') . ' *']);
        echo $this->JqueryValidation->input('guardagasto', ['label' => __('Crear gasto?'), 'type' => 'checkbox']);
        if (isset($this->request->data['Proveedorsfactura']['guardagasto']) && $this->request->data['Proveedorsfactura']['guardagasto'] == '1') {
            echo "<div id='gg'>";
            echo $this->JqueryValidation->input('rubro_id', ['label' => __('Rubro') . ' *', 'empty' => __('Seleccione Rubro...')]);
            echo "<div class='inline' style='max-width:65%;width:65%'>";
            $c = 0;
            foreach ($coeficientes as $g => $h) {
                $valor = $this->request->data['GastosGeneraleDetalle'][$g]['coeficiente_id'] ?? 0;
                echo $this->Form->input("GastosGeneraleDetalle.$g.coeficiente_id", ['label' => __($h), 'type' => 'number', 'value' => $valor, 'step' => 0.01, 'style' => 'width:100px', 'id' => 'coefadd_' . $g, 'required' => 'required', 'tabindex' => $c + 1]);
                $c++;
            }
            $checkHeredable = $this->request->data['Proveedorsfactura']['heredable'] ?? 0;
            $checkHabilitado = $this->request->data['Proveedorsfactura']['habilitado'] ?? 1;
            echo $this->JqueryValidation->input('heredable', ['label' => __('Heredable'), 'type' => 'checkbox', 'checked' => $checkHeredable]);
            echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado'), 'type' => 'checkbox', 'checked' => $checkHabilitado]);
            echo "</div>";
            if (!empty($distribuciones)) {
                ?>
                <div class='distribuirgastoProveedorFactura inline'>
                    <fieldset class="fieldsetdistr">
                        Distribuir gasto
                    </fieldset>
                    <?php
                    echo $this->JqueryValidation->input('distribuciones', ['label' => __('Distribución'), 'style' => 'width:110px']);
                    echo $this->Form->input('totaldistribucion', ['label' => __('Total'), 'style' => 'width:90px', 'type' => 'number', 'step' => '0.01', 'min' => 0]);
                    echo $this->Form->button('Calcular', ['type' => 'button', 'id' => 'calcular']);
                    $cad = $cad2 = "";
                    if (isset($distribucionesDetalle)) {
                        foreach ($distribucionesDetalle as $dkey => $dval) {
                            $cad .= "distdet['" . $dval['GastosDistribucione']['id'] . "'] = [";
                            foreach ($dval['GastosDistribucionesDetalle'] as $diskey => $disval) {
                                $cad .= "'c_" . $disval['coeficiente_id'] . "'" . ",";
                                $cad2 .= "var c_" . $dval['GastosDistribucione']['id'] . "_" . $disval['coeficiente_id'] . "=" . $disval['porcentaje'] . ";";
                            }
                            $cad .= "];";
                        }
                    }
                    ?>
                </div>
                <script>
                    var distdet = [];
        <?php echo $cad . $cad2; ?>
                    $("#calcular").click(function () {
                        if ($("#ProveedorsfacturaTotaldistribucion").val() !== "" && !isNaN($("#ProveedorsfacturaTotaldistribucion").val())) {
                            $("input[id^='coefadd_']").each(function (i, v) {//para cada coeficiente
                                var coefid = v.id.substr(v.id.indexOf("_") + 1); // el id de uno de los coef de la distribucion
                                var porc = eval("c_" + $("#ProveedorsfacturaDistribuciones").val() + "_" + coefid);
                                var total = $("#ProveedorsfacturaTotaldistribucion").val() * porc / 100;
                                $("#coefadd_" + coefid).val(parseFloat(total).toFixed(2));
                            });
                        } else {
                            alert("Debe ingresar un número decimal");
                        }
                    });
                </script>
                <?php
            }
            echo "</div>";
        }
        ?>

    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar')]); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#ProveedorsfacturaProveedorId").select2({language: "es"});
        $("#ProveedorsfacturaLiquidationId").select2({language: "es"});
        $("#ProveedorsfacturaDistribuciones").select2({language: "es"});
        $("#ProveedorsfacturaRubroId").select2({language: "es"});
        $("#ProveedorsfacturaFechaDay").select2({language: "es"});
        $("#ProveedorsfacturaFechaMonth").select2({language: "es"});
        $("#ProveedorsfacturaFechaYear").select2({language: "es"});

        $("#ProveedorsfacturaGuardagasto").click(function () {
            if (this.checked) {
                if (typeof $("#gg") !== "undefined") {
                    $("#gg").slideToggle("slow");
                }
                $("input[type='submit']").val('<?= __('Siguiente') ?>');
            } else {
                if (typeof $("#gg") !== "undefined") {
                    $("#gg").slideToggle("slow");
                }
                $("input[type='submit']").val('<?= __('Guardar') ?>');
            }

        });
        $("#ProveedorsfacturaAddForm").submit(function (event) {
            if ($("#ProveedorsfacturaProveedorId").val() === '') {
                alert("<?= __('Debe seleccionar un Proveedor') ?>");
                event.preventDefault();
                return false;
            }
            if ($("#ProveedorsfacturaLiquidationId").val() === '') {
                alert("<?= __('Debe seleccionar una Liquidación') ?>");
                event.preventDefault();
                return false;
            }
            if ($("#ProveedorsfacturaGuardagasto").prop("checked") && $("#ProveedorsfacturaRubroId").val() === '') {
                alert("<?= __('Debe seleccionar un Rubro') ?>");
                event.preventDefault();
                return false;
            }
            if (typeof $("#gg").val() !== "undefined") {
                var suma = 0;
                $("input[id^='coefadd_']").each(function (i, v) {//para cada coeficiente
                    var coefid = v.id.substr(v.id.indexOf("_") + 1); // el id de uno de los coef de la distribucion
                    suma += parseFloat($("#coefadd_" + coefid).val());
                });
                if (parseFloat(suma.toFixed(2)) !== parseFloat($("#ProveedorsfacturaImporte").val())) {
                    alert("El importe y la suma de lo prorrateado en cada coeficiente no coinciden");
                    return false;
                }
            }
            //$(".submit").html("<img src='/sistema/img/loading.gif' />");
            return true;
        });
    });
</script>