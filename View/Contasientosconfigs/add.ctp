<div class="contasientosconfigs form">
    <?php echo $this->Form->create('Contasientosconfig', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Contasientosconfig'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio_id')]);
	echo $this->JqueryValidation->input('config', ['label' => __('Config')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#ContasientosconfigConsorcioId").select2({language: "es"});$("#ContasientosconfigConfig").focus();});
</script>