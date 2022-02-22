<div class="amenitiesreservas form">
    <?php echo $this->Form->create('Amenitiesreserva', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Amenitiesreserva'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('amenitie_id', ['label' => __('Amenitie_id')]);
	echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha')]);
	echo $this->JqueryValidation->input('amenitiesturno_id', ['label' => __('Amenitiesturno_id')]);
	echo $this->JqueryValidation->input('propietario_id', ['label' => __('Propietario_id')]);
	echo $this->JqueryValidation->input('cancelado', ['label' => __('Cancelado')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#AmenitiesreservaAmenitieId").select2({language: "es"});$("#AmenitiesreservaFecha").focus();$("#AmenitiesreservaAmenitiesturnoId").select2({language: "es"});$("#AmenitiesreservaPropietarioId").select2({language: "es"});});
</script>