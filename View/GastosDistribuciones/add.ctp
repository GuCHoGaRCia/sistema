<div class="gastosDistribuciones form">
    <?php echo $this->Form->create('GastosDistribucione', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Distribuciones de gastos'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio')]);
	echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Siguiente')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#GastosDistribucioneConsorcioId").select2({language: "es"});$("#GastosDistribucioneNombre").focus();});
</script>