<script src='/sistema/js/spectrum.js'></script>
<link rel='stylesheet' href='/sistema/css/spectrum.css' />
<div class="reparacionesestados form">
    <?php echo $this->Form->create('Reparacionesestado', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Estado reparación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->JqueryValidation->input('color', ['label' => __('Color'), 'readonly' => 'readonly']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#ReparacionesestadoNombre").focus();
        $("#ReparacionesestadoColor").spectrum({'color': '#<?= $this->request->data['Reparacionesestado']['color'] ?>', preferredFormat: "hex", showPaletteOnly: true});
    });
</script>