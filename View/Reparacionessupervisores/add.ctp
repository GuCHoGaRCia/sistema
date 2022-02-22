<div class="reparacionessupervisores form">
    <?php echo $this->Form->create('Reparacionessupervisore', ['class' => 'jquery-validation', 'id' => 'enviar']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Supervisor'); ?></h2>
        <?php
        //echo $this->JqueryValidation->input('client_id', ['label' => __('Client_id')]);
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->JqueryValidation->input('direccion', ['label' => __('Dirección')]);
        echo $this->JqueryValidation->input('telefono', ['label' => __('Teléfono')]);
        echo $this->JqueryValidation->input('email', ['label' => __('Email'), 'type' => 'text']);
        echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado'), 'checked' => 'checked']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#ReparacionessupervisoreNombre").focus();
    });
    $("#enviar").on("submit", function () {
        if ($("#ReparacionessupervisoreNombre").val() === "") {
            $("#ReparacionessupervisoreNombre").focus();
            alert('<?= __('Debe ingresar un Nombre') ?>');
            return false;
        }
        $("#guardar").prop('disabled', true);
        return true;
    });
</script>