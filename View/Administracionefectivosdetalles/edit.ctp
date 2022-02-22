<div class="administracionefectivosdetalles form">
    <?php echo $this->Form->create('Administracionefectivosdetalle', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Administracionefectivosdetalle'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('id', ['label' => __('Id')]);
	echo $this->JqueryValidation->input('administracionefectivo_id', ['label' => __('Administracionefectivo_id')]);
	echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio_id')]);
	echo $this->JqueryValidation->input('importe', ['label' => __('Importe')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#AdministracionefectivosdetalleAdministracionefectivoId").select2({language: "es"});$("#AdministracionefectivosdetalleConsorcioId").select2({language: "es"});$("#AdministracionefectivosdetalleImporte").focus();});
</script>