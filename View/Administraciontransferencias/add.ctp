<div class="administraciontransferencias form">
    <?php echo $this->Form->create('Administraciontransferencia', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Administraciontransferencia'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('proveedorspago_id', ['label' => __('Proveedorspago_id')]);
	echo $this->JqueryValidation->input('bancoscuenta_id', ['label' => __('Bancoscuenta_id')]);
	echo $this->JqueryValidation->input('anulado', ['label' => __('Anulado')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#AdministraciontransferenciaProveedorspagoId").select2({language: "es"});$("#AdministraciontransferenciaBancoscuentaId").select2({language: "es"});$("#AdministraciontransferenciaAnulado").focus();});
</script>