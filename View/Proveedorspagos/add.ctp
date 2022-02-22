<?php
echo $this->Html->script(['i18n/es']);
?>
<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Agregar Pago Proveedor'); ?></h2>
        <div class="inline">
            <?php echo $this->Form->input('proveedor_id', ['label' => false, 'class' => 'buscajax', 'autocomplete' => 'off', 'empty' => '']); ?>
        </div>
        <div id="formas"></div>
    </fieldset>
    <div id="contenido" style="width:auto;height:auto"></div>
</div>
<div id="test"></div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    var id = <?= !empty($id) ? $id : 0 ?>;
    $(".buscajax").select2({language: "es", placeholder: "Seleccione Proveedor..."});
    $(".buscajax").on("select2:select", function (e) {
        recargar();
    });
    if (id !== 0) {
        $(".buscajax").val(id).change();
        saldo(id);
    }

    function saldo(id) {
        $("#formas").html('<br>Cargando datos, espere por favor... <img src="<?php echo $this->webroot; ?>img/loading.gif"/>');
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedors/getSaldosProveedor", cache: false, data: {p: id}}).done(function (msg) {
            $("#formas").html(msg);
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    }
    function recargar() {
        if ($("#proveedor_id").val() !== "") {
            window.location.href = "<?= $this->webroot ?>Proveedorspagos/add/" + $("#proveedor_id").val();
        }
    }
</script>
<style type="text/css">
    .prop{cursor:pointer; line-height: 5px; color:#444}
</style>