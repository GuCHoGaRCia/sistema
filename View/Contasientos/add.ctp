<div class="contasientos form">
    <?php echo $this->Form->create('Contasiento', ['class' => 'jquery-validation', 'multiple' => 'multiple']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Asiento'); ?></h2>
        <?php
        $debe = $haber = $dsc = [];
        $cuenta = '';
        $fecha = date("d/m/Y");
        if (isset($this->request->data['Contasiento'])) {//cuando tira algun error, dejo los valores como los seleccionaron
            $cuenta = $debe = $this->request->data['Contasiento']['contcuenta'];
            $debe = $this->request->data['Contasiento']['debe'];
            $haber = $this->request->data['Contasiento']['haber'];
            $dsc = $this->request->data['Contasiento']['dsc'];
            $fecha = $this->request->data['Contasiento']['fecha'];
        }

        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio') . ' *']);
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => $fecha));
        echo $this->JqueryValidation->input('descripcion', ['label' => __('Descripción') . ' *']);
        echo "</div>";

        for ($i = 0; $i <= 14; $i++) {
            echo "<li class='inline' style='list-style-type:decimal-leading-zero'>";
            echo $this->JqueryValidation->input($i, ['label' => $i == 0 ? __('Cuenta') : false, 'class' => 's2', 'empty' => '', 'type' => 'select', 'required' => false, 'name' => 'data[Contasiento][contcuenta][]', 'options' => $contcuentas, 'selected' => $cuenta[$i] ?? '']);
            echo $this->JqueryValidation->input('dsc_' . $i, ['label' => $i == 0 ? __('Descripción') : false, 'name' => 'data[Contasiento][dsc][]', 'style' => 'width:150px', 'value' => $dsc[$i] ?? '']);
            echo $this->JqueryValidation->input('debe_' . $i, ['label' => $i == 0 ? __('Debe') : false, 'type' => 'number', 'data-id' => $i, 'class' => 'd', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][debe][]', 'value' => $debe[$i] ?? 0]);
            echo $this->JqueryValidation->input('haber_' . $i, ['label' => $i == 0 ? __('Haber') : false, 'type' => 'number', 'data-id' => $i, 'class' => 'h', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][haber][]', 'value' => $haber[$i] ?? 0]);
            echo "</li>";
        }
        echo "<div class='inline' style='margin-top:10px'>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style='width:465px;font-weight:bold'></div>";
        echo $this->JqueryValidation->input('', ['label' => false, 'type' => 'number', 'readonly' => 'readonly', 'id' => 'totaldebe', 'disabled' => 'disabled', 'value' => 0]);
        echo $this->JqueryValidation->input('', ['label' => false, 'type' => 'number', 'readonly' => 'readonly', 'id' => 'totalhaber', 'disabled' => 'disabled', 'value' => 0]);
        echo "</div>";
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'class' => 'detalle', 'id' => 'guardar']); ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d'});
        $("#ContasientoConsorcioId").select2({language: "es"});
        $("#ContasientoContejercicioId").select2({language: "es"});
        $(".s2").select2({language: "es", allowClear: true, placeholder: "Seleccione cuenta..."});
        $("#ContasientoDescripcion").focus();
    });
    $("#ContasientoAddForm").submit(function (e) {
        calcula();
        var error = "";
        $('.d').each(function () {
            var id = $(this).data('id');
            if ($("#Contasiento" + id + " :selected").val() === "" && (parseFloat($("#ContasientoDebe" + id).val()) !== 0 || parseFloat($("#ContasientoHaber" + id).val()) !== 0)) {
                error += "Falta seleccionar la Cuenta en el detalle " + (id + 1) + "<br>";
            }
            if ($("#Contasiento" + id + " :selected").val() !== "" && parseFloat($("#ContasientoDebe" + id).val()) === 0 && parseFloat($("#ContasientoHaber" + id).val()) === 0) {
                error += "Falta ingresar Debe o Haber en el detalle " + (id + 1) + "<br>";
            }
            if ($("#ContasientoDsc" + id).val() !== "" && $("#Contasiento" + id + " :selected").val() === "") {
                error += "Falta seleccionar la Cuenta en el detalle " + (id + 1) + "<br>";
            }
            if (parseFloat($("#ContasientoDebe" + id).val()) !== 0 && parseFloat($("#ContasientoHaber" + id).val()) !== 0) {
                error += "Solo Debe o Haber pueden ser distintos de cero en el detalle " + (id + 1) + "<br>";
            }
        });
        if (error !== "") {
            alert(error);
            e.preventDefault();
            return false;
        }

        if ($("#ContasientoDescripcion").val() === "") {
            alert("Debe ingresar una descripción");
            e.preventDefault();
            return false;
        }
        if (parseFloat($("#totaldebe").val()) === 0 && parseFloat($("#totalhaber").val()) === 0) {
            alert("Debe ingresar al menos un detalle del asiento");
            e.preventDefault();
            return false;
        }
        if ($("#totaldebe").val() !== $("#totalhaber").val()) {
            alert("La suma de Debe y Haber deben ser iguales");
            e.preventDefault();
            return false;
        }

        $("#guardar").prop('disabled', true);
        return true;
    });
    $(".d").change(function () {
        calcula();
    });
    $(".h").change(function () {
        calcula();
    });
    function calcula() {
        var total = 0;
        $('.d').each(function () {
            if (isNaN($(this).val())) {
                $(this).val(0);
            }
            var a = parseFloat($(this).val());
            $(this).val(a.toFixed(2));
            total += a;
        });
        $("#totaldebe").val(total.toFixed(2));
        total = 0;
        $('.h').each(function () {
            if (isNaN($(this).val())) {
                $(this).val(0);
            }
            var a = parseFloat($(this).val());
            $(this).val(a.toFixed(2));
            total += a;
        });
        $("#totalhaber").val(total.toFixed(2));
    }

    $(document).keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
        }
    });
    calcula();<?php /* Si tira error en el response, debe recalcular todo con los valores q ya se ingresaron */ ?>
</script>