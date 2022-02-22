<div class="llaves form">
    <?php echo $this->Form->create('Llave', ['class' => 'jquery-validation', 'id' => 'enviar']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Llave'); ?></h2>
        <?php
        //echo $this->JqueryValidation->input('client_id', ['label' => __('Client_id')]);
        echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio'), 'empty' => __('Seleccione Consorcio...')]);
        echo $this->JqueryValidation->input('propietario_id', ['label' => __('Propietario'), 'empty' => __('Seleccione Propietario...')]);
        //echo $this->JqueryValidation->input('user_id', ['label' => __('User_id')]);
        //echo $this->JqueryValidation->input('numero', ['label' => __('Número')]);
        echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha'), 'value' => date("d/m/Y"), 'class' => 'dp', 'type' => 'text', 'style' => 'width:98px']);
        echo $this->JqueryValidation->input('descripcion', ['label' => __('Descripción')]);
        echo $this->JqueryValidation->input('habilitada', array('label' => __('Habilitada'), 'checked' => 'checked'));
        echo $this->Html->script('ckeditor/ckeditor');
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    $("#LlaveConsorcioId").change(function () {
        $("#LlavePropietarioId option").remove();
        $("#LlavePropietarioId").append($("<option></option>").attr("value", '0').text("Seleccione Propietario..."));
        $("#formas").html('');
        if ($("#LlaveConsorcioId").val() !== "") {
            getData($("#LlaveConsorcioId").val());
        }
    });
    $("#guardar").on("click", function (event) {
        if ($("#LlaveConsorcioId").val() === "") {
            alert("Debe seleccionar un Consorcio y opcionalmente Propietario");
            return false;
        }
        if ($("#LlaveFecha").val() === "") {
            alert("Debe seleccionar una Fecha");
            return false;
        }
        if ($("#LlaveDescripcion").val() === "") {
            alert("Debe ingresar una Descripción de la Llave");
            return false;
        }
        $("#guardar").prop('disabled', true);
        $("#enviar").submit();
    });
    function getData(e) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                //$("#LlavePropietarioId option").remove();
                //$("#LlavePropietarioId").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#LlavePropietarioId").append($("<option></option>").attr("value", j).text(val));
                });
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $("#LlaveConsorcioId").select2({language: "es"});
        $("#LlavePropietarioId").select2({language: "es"});
    });
</script>