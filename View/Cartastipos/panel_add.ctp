<div class="cartastipos form">
    <?php echo $this->Form->create('Cartastipo', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cartastipo'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('nombre', array('label' => __('nombre')));
	echo $this->JqueryValidation->input('abreviacion', array('label' => __('abreviacion')));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>