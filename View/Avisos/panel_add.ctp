<div class="avisos form">
    <?php echo $this->Form->create('Aviso', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Enviar Avisos'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'empty' => __('Seleccione consorcio...')));
        ?>
    </fieldset>
    <br>
    Ver/ocultar detalles&nbsp;&nbsp;<a id="ver_ocultar" href="#" style="font-size:14px;" onclick='$("#contlistado").toggle()'> +/-</a>
    <br>
    <div id="contlistado">
        <ul id="contlistado2">
        </ul>                     
    </div>
    <?php echo $this->Form->end(array('id' => 'enviar', 'label' => __('Enviar aviso'))); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script type="text/javascript">
    //getData($("#AvisoLiquidationId").first().val());
    $("#AvisoAddForm").submit(function (event) {
        if ($("input:checkbox:checked").length === 0) {
            alert("<?= __("Debe seleccionar al menos un propietario") ?>");
            return false;
        }
    });

    $("#AvisoConsorcioId").change(function () {
        if ($("#AvisoConsorcioId").val() !== "") {
            $("#enviar").hide();
            getData($("#AvisoConsorcioId").val());
        } else {
            $("#contlistado2").html('');
            $("#contlistado").hide();
        }
    });

    function getData(c) {
        $("#contlistado2").html('');
        $.ajax({type: "POST", url: "getPropietarios", cache: false, data: {con: c}}).done(function (msg) {
            var obj = JSON.parse(msg);
            if (!$.isEmptyObject(obj)) {
                for (j = 0; j < obj.length; j++) {
                    var n = obj[j]['Propietario'];
                    $("#contlistado2").append($("<li><input type='checkbox' name='t_" + j + "' value='" + n['c'] + "' checked='checked'>" + n['n'] + " (" + n['u'] + ") - <a target='_blank' rel='nofollow noopener noreferrer' href='<?php echo $this->webroot; ?>Avisos/view/" + n['l'] + "'>Ver</a></li>"));
                }
                $("#enviar").show();
            } else {
                $("#contlistado2").html('No se encontraron propietarios con email disponible');
            }

        });
    }

</script>