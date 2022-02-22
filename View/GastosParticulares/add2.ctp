<div class="info"><?php echo __('Seleccione propietario/s (si es gasto particular) o coeficiente (si es gasto particular prorrateado), no ambos simultáneamente'); ?></div>
<div class="gastosParticulares form">
    <?php echo $this->Form->create('GastosParticulare', array('class' => 'jquery-validation', 'id' => 'gpform')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <?php
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Liquidación') . ' *'));
        echo $this->JqueryValidation->input('cuentasgastosparticulare_id', array('label' => __('Cuenta GP') . ' *'));
        echo $this->JqueryValidation->input('date', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('description', array('label' => __('Descripción') . ' *', 'style' => 'width:400px'));
        echo $this->JqueryValidation->input('amount', array('label' => __('Importe')));
        echo "</div>";
        echo "<div id='coef'>";
        echo $this->JqueryValidation->input('coeficiente_id', array('label' => __('Coeficiente'), 'empty' => 'Seleccionar...'));
        echo "</div>";
        echo "<div id='prop' class='inline'>";
        echo $this->JqueryValidation->input('propietario_id', array('label' => __('Lista de Propietarios') . " (" . count($propietarios) . ")", 'multiple' => 'multiple', 'style' => 'height:400px;width:400px;border:1px solid gray;'));
        echo "<div style='position:relative;margin-left:5px;margin-top:-200px'><input style='width:60px' type='submit' id='agregar' value='Agregar' /><br><input style='width:60px' type='submit' id='quitar' value='Quitar'/></div>";
        echo "<div style='width:430px;margin-left:5px;height:400px'><label for='listagastos'>Lista de Gastos (<span id='cant'></span>)</label><select multiple='multiple' id='listagastos' style='border:1px solid gray;height:400px;width:430px'></select></div>";
        echo "<div style='border:1px solid gray;position:relative;left:10px;top:10px;width:500px;padding:10px;'><b>Totales por cuenta: </b><br>";
        foreach ($cuentasgastosparticulares as $k => $v) {
            echo "$v: <span id='c$k' style='font-weight:bold'>0.00</span><br>";
        }
        echo "</div>";
        echo "</div>";
        echo $this->JqueryValidation->input('heredable', ['label' => __('Heredables'), 'type' => 'checkbox']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'addgp']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $("#cant").text($("#listagastos option").length);
    $(function () {
        $("#agregar").on("click", function (event) {
            event.preventDefault();
            if ($("#GastosParticulareDescription").val() == "" || isNaN(parseFloat($("#GastosParticulareAmount").val()))) {
                alert("Ingrese un concepto e importe antes de agregar los gastos");
                return false;
            }
            $("#GastosParticularePropietarioId option:selected").clone().each(function () {
                var cad = this.text;
                $(this).attr('dsc', cad.substring(cad.indexOf("-") + 1));//substring(this.text.lastIndexOf("-")+1)
                $(this).attr('i', $("#GastosParticulareAmount").val());
                $(this).attr('d', $("#GastosParticulareDescription").val());
                var f = $("#GastosParticulareDateYear").val() + "-" + $("#GastosParticulareDateMonth").val() + "-" + $("#GastosParticulareDateDay").val();
                $(this).attr('f', f);
                $(this).attr('c', $("#GastosParticulareCuentasgastosparticulareId").val());
                this.text = cad.substring(cad.indexOf("-") + 1) + ' - $' + $("#GastosParticulareAmount").val() + " - " + $("#GastosParticulareCuentasgastosparticulareId option:selected").text() + ' - ' + $("#GastosParticulareDateDay").val() + "/" + $("#GastosParticulareDateMonth").val() + "/" + $("#GastosParticulareDateYear").val() + ' - ' + $("#GastosParticulareDescription").val();
                $('#listagastos').append(this);
                // actualizo los totales
                total = parseFloat($("#c" + $("#GastosParticulareCuentasgastosparticulareId").val()).text());
                $("#c" + $("#GastosParticulareCuentasgastosparticulareId").val()).text((total + parseFloat($("#GastosParticulareAmount").val())).toFixed(2));
            });
            $("#cant").text($("#listagastos option").length);
        });

        $("#quitar").on("click", function (event) {
            event.preventDefault();
            $("#listagastos option:selected").each(function () {
                // actualizo el total
                total = parseFloat($("#c" + $(this).attr('c')).text());
                $("#c" + $(this).attr('c')).text((total - parseFloat($(this).attr('i'))).toFixed(2));
                $(this).remove();
            });
            $("#cant").text($("#listagastos option").length);

        });

        $("#GastosParticulareCoeficienteId").on("change", function (event) {
            if (this.value !== "") {
                $("#prop").hide();
            } else {
                $("#prop").show();
            }
        });

        $("#addgp").click(function (event) {
            event.preventDefault();
            if ($("#GastosParticulareCoeficienteId").val() === "") {
                // no seleccionó coeficiente, entonces $("#listagastos") debe tener datos
                if ($("#listagastos option").length === 0) {
                    alert("Debe agregar algún gasto particular antes de guardar");
                    return false;
                }
                if (typeof $("#gpx") !== "undefined" && $("#gpx").length !== 0) {
                    $("#gpx").remove();
                }
                $("#listagastos option").each(function () {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'gpx',
                        name: 'data[GastosParticulare][gp][]',
                        value: $(this).val() + "#" + $(this).attr('c') + "#" + $(this).attr('f') + "#" + $(this).attr('i') + "#" + $(this).attr('d'),
                    }).appendTo("#gpform");
                });

            }
<?php
//	'_Token' => array(
//		'key' => '24bbb2cca29be6064e0f1c32b96d82be72eed7e9',
//		'fields' => 'e2c088182ec6a3c103708ebd096ad78791196d25%3A',
//		'unlocked' => ''
//	),
//	'GastosParticulare' => array(
//		'liquidation_id' => '214',
//		'cuentasgastosparticulare_id' => '24',
//		'date' => array(
//			'day' => '04',
//			'month' => '01',
//			'year' => '2015'
//		),
//		'description' => 'fr',
//		'amount' => '222',
//		'coeficiente_id' => '',
//		'propietario_id' => '',
//		'gp' => array(
//                            propietario_id#cuentaGP#fecha#importe#desc
//			(int) 0 => '52#13#2013-06-26#111#gp1',
//			(int) 1 => '53#13#2013-06-26#111#gp1',
//			(int) 2 => '54#24#2015-01-04#222#fr',
//			(int) 3 => '55#24#2015-01-04#222#fr',
//			(int) 4 => '56#24#2015-01-04#222#fr'
//		)
//deshabilito este campo asi no se envía 
?>
            $("#GastosParticularePropietarioId").prop("disabled", true);
            $("#addgp").prop('disabled', true);
            $("#gpform").submit();
        });

        $("#GastosParticulareLiquidationId").select2({language: "es", width: "400px"});
        $("#GastosParticulareCuentasgastosparticulareId").select2({language: "es", width: "400px"});
        $("#GastosParticulareCoeficienteId").select2({language: "es", width: "400px"});
        $("#GastosParticulareDateDay").select2({language: "es", width: "130px"});
        $("#GastosParticulareDateMonth").select2({language: "es", width: "132px"});
        $("#GastosParticulareDateYear").select2({language: "es", width: "130px"});
        $("#GastosParticulareDescription").focus();
    });
</script>