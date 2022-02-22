<div class="cuentasgastosparticulares form">
    <?php echo $this->Form->create('Cuentasgastosparticulare', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cuenta Gastos Particulares'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' * ' . "<img title='Tildar/Destildar todos' src='" . $this->webroot . 'img/1.png' . "' style='cursor:pointer' onClick=\"" . "$(':checkbox').attr('checked', !$(':checkbox').attr('checked'))" . "\" />", 'multiple' => 'checkbox'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>

</script>
<script>
    $(function () {
        $("#CuentasgastosparticulareName").focus();
    });
    /*$("input[id^='CuentasgastosparticulareConsorcioId']").each(function () {
     $(this).prop('checked', true);
     });*/
    $("#CuentasgastosparticulareAddForm").submit(function (event) {
        var check = false;
        $("input[id^='CuentasgastosparticulareConsorcioId']").each(function () {
            if (this.id !== 'CuentasgastosparticulareConsorcioId') {
                if (this.checked) {// si chequeo algun check
                    check = true;
                }
            }
        });
        if (!check) {
            alert("<?= __('Debe seleccionar al menos un Consorcio') ?>");
            event.preventDefault();
            return false;
        }
        return true;
    });
</script>
<style>
    label { display: block; width: 400px !important; }
</style>