<div class="sueldoscategorias form">
    <?php echo $this->Form->create('Sueldoscategoria', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Sueldoscategoria'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('codigo', array('label' => __('Codigo')));
	echo $this->JqueryValidation->input('nombre', array('label' => __('Nombre')));
	echo $this->JqueryValidation->input('importe', array('label' => __('Importe')));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>