<div class="cartasprecios form">
    <?php echo $this->Form->create('Cartasprecio', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Precio de carta de cliente'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('client_id', array('label' => __('Cliente')));
	echo $this->JqueryValidation->input('cartastipo_id', array('label' => __('Tipo de carta')));
	echo $this->JqueryValidation->input('importe', array('label' => __('Importe')));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>