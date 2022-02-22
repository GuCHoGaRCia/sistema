<div class="plataformasdepagosconfigs form">
    <?php echo $this->Form->create('Plataformasdepagosconfig', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Configurar Plataforma') . " <span style='color:red'>" . h($name) . "</span>" ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('plataformasdepago_id', ['label' => __('Plataforma'), 'options' => ['0' => '-- No utiliza --'] + $plataformasdepagos]);
        echo "<div id='detalle' style='display:none'>";
        echo $this->JqueryValidation->input('datointerno', ['label' => __('6 Dígitos Dato interno')]);
        echo $this->JqueryValidation->input('minimo', ['label' => __('Mínimo'), 'min' => 0, /* 'type' => 'hidden', */]);
        echo $this->JqueryValidation->input('comision', ['label' => __('Comisión (ej 3.1%)'), 'min' => 0, /* 'type' => 'hidden', */]);
        echo $this->JqueryValidation->input('codigo', ['label' => __('Código de Cliente en la Plataforma')]);
        echo "</div>";
        echo "<div id='detalleplapsa' style='display:none'>";
        echo $this->JqueryValidation->input('informamailpropietarios', array('label' => __('Informa Mail Propietario en Saldos'), 'type' => 'checkbox'));

        echo "</div>";
        echo "<div id='detalleroela' style='display:none'>";
        echo "<div class='info'>10 D&iacute;gitos n&uacute;mero convenio ROELA (ej: 5150022836). Dejar en blanco si no poseen</div>";
        foreach ($consorcios as $k => $v) {
            $value = "";
            $key = $this->Functions->find2($this->request->data['Plataformasdepagosconfigsdetalle'], ['consorcio_id' => $k]);
            if ($key !== []) {
                $value = $this->request->data['Plataformasdepagosconfigsdetalle'][$key]['valor'];
            }
            echo $this->JqueryValidation->input('valor', ['label' => h('Número convenio ' . $v), 'class' => 'roela', 'name' => "data[Plataformasdepagosconfigsdetalle][$k]", 'min' => 0, 'maxlength' => 10, 'div' => false, 'value' => $value]);
        }
        echo "</div>";
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'class' => 'guardar']); ?>
</div>
<script>
    $(document).ready(function () {
        $("#PlataformasdepagosconfigDatointerno").focus();
    });
    $(".guardar").on("click", function (event) {
        event.preventDefault();
        var error = -1;

        if ($("#PlataformasdepagosconfigPlataformasdepagoId").val() === '3') {
            $(".roela").each(function () {
                if ($(this).val().length !== 10 && $(this).val().length !== 0) {
                    error = $(this);
                    return false;
                }
            });
        }
        if (error !== -1) {
            alert("Los Convenios deben ser numeros de 10 digitos (o vacio si no poseen)");
            $(error).focus();
            return false;
        }
        envia(<?= $this->request->data['Plataformasdepagosconfig']['id'] ?>);
    });
    if ($("#PlataformasdepagosconfigPlataformasdepagoId").val() !== '0') {
        $("#detalleplapsa").hide();
        if ($("#PlataformasdepagosconfigPlataformasdepagoId").val() === '3') {
            $("#detalleroela").show('fast');
            $("#detalleplapsa").hide();
        } else {
            $("#detalle").show();
        }
        if ($("#PlataformasdepagosconfigPlataformasdepagoId").val() === '1') {
            $("#detalleplapsa").show('fast');
        }
    } else {
        $("#detalle").hide();
    }
    $("#PlataformasdepagosconfigPlataformasdepagoId").on('change', function (e) {
        $("#detalleplapsa").hide();
        if ($(this).val() === '0') {
            $("#detalle").hide('fast');
            $("#detalleroela").hide('fast');
        } else if ($(this).val() === '3') {
            $("#detalleroela").show('fast');
            $("#detalle").hide('fast');
        } else if ($(this).val() === '1') {
            $("#detalleroela").hide('fast');
            $("#detalle").show('fast');
            $("#detalleplapsa").show('fast');
        } else {
            $("#detalleroela").hide('fast');
            $("#detalle").show('fast');
        }
    });
</script>
