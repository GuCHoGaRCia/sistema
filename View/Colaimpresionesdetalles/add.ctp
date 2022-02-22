<div class="colaimpresionesdetalles form">
    <?php echo $this->Form->create('Colaimpresionesdetalle', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Colaimpresionesdetalle'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('colaimpresione_id', ['label' => __('Colaimpresione_id')]);
	echo $this->JqueryValidation->input('reporte', ['label' => __('Reporte')]);
	echo $this->JqueryValidation->input('impreso', ['label' => __('Impreso')]);
	echo $this->JqueryValidation->input('online', ['label' => __('Online')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#ColaimpresionesdetalleColaimpresioneId").select2({language: "es"});$("#ColaimpresionesdetalleReporte").focus();});
</script>