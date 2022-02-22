<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Agregar Cobranza por Período'); ?></h2>
        <?php
        echo $this->Form->input('consorcio_id', array('label' => false, 'class' => 'buscajax', 'empty' => __('Seleccione Consorcio...')));
        ?>
        <div id="formas"></div>
    </fieldset>
    <div id="contenido" style="width:auto;height:auto"></div>
</div>

<script>
    var id = <?= !empty($id) ? $id : 0 ?>;
    $("#consorcio_id").select2({language: "es", placeholder: "<?= __("Seleccione consorcio...") ?>"});
    $(".buscajax").on("select2:select", function (e) {
        recargar();
    });
    if (id !== 0) {
        $(".buscajax").val(id).change();
        saldo(id);
    }

    function saldo(id) {
        $("#formas").html('Cargando Propietarios, espere por favor... <img src="<?php echo $this->webroot; ?>img/loading.gif"/>');
        $.ajax({type: "POST", url: "<?= $this->webroot ?>SaldosCierres/getSaldosTipoLiquidacionPropietarios", cache: false, data: {c: id}}).done(function (msg) {
            $("#formas").html(msg);
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudieron obtener los datos. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudieron obtener los datos");
            }
        });
    }
    function recargar() {
        if ($("#consorcio_id").val() !== "") {
            window.location.href = "<?= $this->webroot ?>Cobranzas/periodo/" + $("#consorcio_id").val();
        }
    }
</script>