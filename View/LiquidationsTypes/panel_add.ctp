<div class="liquidationsTypes form">
    <?php echo $this->Form->create('LiquidationsType', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Tipo de liquidación'); ?></h2>
        <?php 
		echo $this->Form->input('client_id', array('label' => __('Cliente')));
		echo $this->JqueryValidation->input('name', array('label' => __('Nombre')));
		echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado')));
		?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>