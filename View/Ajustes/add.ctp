<?php
echo $this->Html->script(['i18n/es']);
?>
<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Agregar Ajuste manual'); ?></h2>
        <?= $this->Form->input('consorcio_id', array('label' => __('Consorcio'), 'empty' => __('Seleccione Consorcio...'))) ?>
        <?= $this->Form->input('propietario_id', array('label' => __('Propietario'), 'empty' => __('Seleccione Propietario...'))) ?>
        <div id="formas"></div>
    </fieldset>
    <div id="contenido" style="width:auto;height:auto"></div>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $("#consorcio_id").change(function () {
        $("#propietario_id option").remove();
        $("#propietario_id").hide();
        $("#formas").html('');
        if ($("#consorcio_id").val() !== "") {
            getData($("#consorcio_id").val());
        }
    });
    $("#propietario_id").change(function () {
        $("#formas").html('<br>Cargando datos, espere por favor... <img src="<?php echo $this->webroot; ?>img/loading.gif"/>');
        if ($("#propietario_id").val() !== "") {
            saldo($("#propietario_id").val());
        }
    });
    function getData(e) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                $("#propietario_id option").remove();
                $("#propietario_id").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#propietario_id").append($("<option></option>").attr("value", j).text(val));
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

    function saldo(id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>SaldosCierres/getSaldosPropietario", cache: false, data: {p: id, f: 'Ajustes'}}).done(function (msg) {
            $("#formas").html(msg);
        });
    }
    $(function () {
        $("#consorcio_id").select2({language: "es"});
        $("#propietario_id").select2({language: "es"});
    });
</script>
<style type="text/css">
    .prop{cursor:pointer; line-height: 5px; color:#444}
</style>