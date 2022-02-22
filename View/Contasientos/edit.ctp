<div class="contasientos form">
    <?php echo $this->Form->create('Contasiento', ['class' => 'jquery-validation', 'multiple' => 'multiple']); ?>
    <fieldset>
        <h2><?php echo __('Editar Asiento'); ?></h2>
        <?php
        $debe = $haber = 0;

        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('', ['label' => false, 'type' => 'hidden', 'name' => 'data[Contasiento][consorcio_id]', 'value' => $this->request->data[0]['Contasiento']['consorcio_id']]);
        echo $this->Form->input('', ['label' => 'Consorcio', 'type' => 'text', 'disabled' => 'disabled', 'id' => 'consor', 'value' => $consorcios[$this->request->data[0]['Contasiento']['consorcio_id']]]);
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y", strtotime($this->request->data[0]['Contasiento']['fecha']))));
        echo "</div>";
        $j = 0;
        foreach ($this->request->data as $k => $v) {
            echo "<li class='inline' style='list-style-type:decimal-leading-zero'>";
            echo $this->JqueryValidation->input('', ['label' => false, 'type' => 'hidden', 'name' => 'data[Contasiento][id][]', 'value' => $v['Contasiento']['id']]);
            echo $this->JqueryValidation->input($j, ['label' => $j == 0 ? __('Cuenta') : false, 'class' => 's2', 'empty' => '', 'style' => 'width:200px', 'type' => 'select', 'required' => false, 'name' => 'data[Contasiento][contcuenta][]', 'options' => $contcuentas, 'selected' => $v['Contasiento']['contcuenta_id']]);
            echo $this->JqueryValidation->input('dsc_' . $j, ['label' => $j == 0 ? __('Descripción') : false, 'name' => 'data[Contasiento][dsc][]', 'style' => 'width:150px', 'value' => h($v['Contasiento']['descripcion'])]);
            echo $this->JqueryValidation->input('debe_' . $j, ['label' => $j == 0 ? __('Debe') : false, 'type' => 'number', 'data-id' => $j, 'style' => 'width:100px', 'class' => 'd', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][debe][]', 'value' => $v['Contasiento']['debe']]);
            echo $this->JqueryValidation->input('haber_' . $j, ['label' => $j == 0 ? __('Haber') : false, 'type' => 'number', 'data-id' => $j, 'style' => 'width:100px', 'class' => 'h', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][haber][]', 'value' => $v['Contasiento']['haber']]);
            echo "</li>";
            $debe += $v['Contasiento']['debe'];
            $haber += $v['Contasiento']['haber'];
            $j++;
        }

        for ($i = $j; $i <= 14; $i++) {
            echo "<li class='inline' style='list-style-type:decimal-leading-zero'>";
            echo $this->JqueryValidation->input($i, ['label' => false, 'class' => 's2', 'empty' => '', 'type' => 'select', 'style' => 'width:200px', 'required' => false, 'name' => 'data[Contasiento][contcuenta][]', 'options' => $contcuentas]);
            echo $this->JqueryValidation->input('dsc_' . $i, ['label' => false, 'name' => 'data[Contasiento][dsc][]', 'style' => 'width:150px', 'value' => $dsc[$i] ?? '']);
            echo $this->JqueryValidation->input('debe_' . $i, ['label' => false, 'type' => 'number', 'data-id' => $i, 'class' => 'd', 'style' => 'width:100px', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][debe][]', 'value' => $debe[$i] ?? 0]);
            echo $this->JqueryValidation->input('haber_' . $i, ['label' => false, 'type' => 'number', 'data-id' => $i, 'class' => 'h', 'style' => 'width:100px', 'step' => 0.01, 'min' => 0, 'name' => 'data[Contasiento][haber][]', 'value' => $haber[$i] ?? 0]);
            echo "</li>";
        }
        echo "<div class='inline' style='margin-top:10px'>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style='width:365px;font-weight:bold'></div>";
        echo $this->Form->input('', ['label' => false, 'type' => 'text', 'disabled' => 'disabled', 'style' => 'width:100px;text-align:right', 'id' => 'totaldebe', 'disabled' => 'disabled', 'value' => $this->Functions->money($debe)]);
        echo $this->Form->input('', ['label' => false, 'type' => 'text', 'disabled' => 'disabled', 'style' => 'width:100px;text-align:right', 'id' => 'totalhaber', 'disabled' => 'disabled', 'value' => $this->Functions->money($haber)]);
        echo "</div>";
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'class' => 'detalle', 'id' => 'guardar']); ?>
</div>
<script>
    $(document).ready(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d'});
        $("#ContasientoConsorcioId").select2({language: "es"});
        $(".s2").select2({language: "es", allowClear: true, placeholder: "Seleccione cuenta..."});
        $("#consor").focus();
    });
    $("#ContasientoEditForm").submit(function (e) {
        e.preventDefault();
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
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Contasientos/edit",
            data: $("#ContasientoEditForm").serialize(),
        }).done(function (msg) {
            if (msg === "") {
                alert("El Asiento fue editado correctamente");
                $("#verasiento").dialog('close');
            } else {
                alert(msg);
            }
        }).fail(function (j, a) {
            if (j.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
        $("#guardar").prop('disabled', false);

        return false;
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