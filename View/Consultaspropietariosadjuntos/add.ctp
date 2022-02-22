<div class="consultaspropietariosadjuntos form">
    <?php echo $this->Form->create('Consultaspropietariosadjunto', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Consultaspropietariosadjunto'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('client_id', ['label' => __('Client_id')]);
	echo $this->JqueryValidation->input('propietario_id', ['label' => __('Propietario_id')]);
	echo $this->JqueryValidation->input('ruta', ['label' => __('Ruta')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#ConsultaspropietariosadjuntoClientId").select2({language: "es"});$("#ConsultaspropietariosadjuntoPropietarioId").select2({language: "es"});$("#ConsultaspropietariosadjuntoRuta").focus();});
</script>