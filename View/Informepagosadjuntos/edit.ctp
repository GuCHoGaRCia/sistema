<div class="informepagosadjuntos form">
    <?php echo $this->Form->create('Informepagosadjunto', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Informepagosadjunto'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('id', ['label' => __('Id')]);
	echo $this->JqueryValidation->input('informepago_id', ['label' => __('Informepago_id')]);
	echo $this->JqueryValidation->input('ruta', ['label' => __('Ruta')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#InformepagosadjuntoInformepagoId").select2({language: "es"});$("#InformepagosadjuntoRuta").focus();});
</script>