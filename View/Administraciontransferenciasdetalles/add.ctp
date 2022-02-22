<div class="administraciontransferenciasdetalles form">
    <?php echo $this->Form->create('Administraciontransferenciasdetalle', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Administraciontransferenciasdetalle'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('administraciontransferencia_id', ['label' => __('Administraciontransferencia_id')]);
	echo $this->JqueryValidation->input('bancoscuenta_id', ['label' => __('Bancoscuenta_id')]);
	echo $this->JqueryValidation->input('importe', ['label' => __('Importe')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#AdministraciontransferenciasdetalleAdministraciontransferenciaId").select2({language: "es"});$("#AdministraciontransferenciasdetalleBancoscuentaId").select2({language: "es"});$("#AdministraciontransferenciasdetalleImporte").focus();});
</script>