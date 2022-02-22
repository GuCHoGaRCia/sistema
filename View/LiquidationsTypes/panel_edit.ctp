<div class="liquidationsTypes form">
    <?php echo $this->Form->create('LiquidationsType', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Tipo de liquidación'); ?></h2>
        <?php 
		echo $this->JqueryValidation->input('id', array('label' => __('id')));
		echo $this->Form->input('client_id', array('label' => __('Cliente')));
		echo $this->JqueryValidation->input('name', array('label' => __('Nombre')));
		echo $this->JqueryValidation->input('enabled', array('label' => __('Hablitado')));
		?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>