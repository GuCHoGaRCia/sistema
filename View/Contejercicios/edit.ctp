<div class="contejercicios form">
    <?php echo $this->Form->create('Contejercicio', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Contejercicio'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('id', ['label' => __('Id')]);
	echo $this->JqueryValidation->input('client_id', ['label' => __('Client_id')]);
	echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio_id')]);
	echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
	echo $this->JqueryValidation->input('inicio', ['label' => __('Inicio')]);
	echo $this->JqueryValidation->input('fin', ['label' => __('Fin')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#ContejercicioClientId").select2({language: "es"});$("#ContejercicioConsorcioId").select2({language: "es"});$("#ContejercicioNombre").focus();});
</script>