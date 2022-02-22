<div class="resumenes form">
    <?php echo $this->Form->create('Resumene', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Resumene'); ?></h2>
        <?php 
		echo $this->JqueryValidation->input('liquidation_id', array('label' => __('liquidation_id')));
		echo $this->JqueryValidation->input('data', array('label' => __('data')));
		?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>