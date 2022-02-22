<div class="sueldosobrassociales form">
    <?php echo $this->Form->create('Sueldosobrassociale', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Sueldosobrassociale'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('codigo', array('label' => __('Codigo')));
	echo $this->JqueryValidation->input('nombre', array('label' => __('Nombre')));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>