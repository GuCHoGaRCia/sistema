<div class="sueldosempleados form">
    <?php echo $this->Form->create('Sueldosempleado', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Sueldosempleado'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio')));
	echo $this->JqueryValidation->input('sueldoscategoria_id', array('label' => __('Categoría')));
	echo $this->JqueryValidation->input('sueldosobrassociale_id', array('label' => __('Obra social')));
	echo $this->JqueryValidation->input('legajo', array('label' => __('Legajo')));
	echo $this->JqueryValidation->input('nombre', array('label' => __('Nombre')));
	echo $this->JqueryValidation->input('dni', array('label' => __('DNI')));
	echo $this->JqueryValidation->input('cuil', array('label' => __('CUIL')));
	echo $this->JqueryValidation->input('hijos', array('label' => __('Hijos'), 'type' => 'number'));
	echo $this->JqueryValidation->input('ingreso', array('label' => __('Fecha ingreso'), 'dateFormat' => 'DMY', 'style' => 'width:98px'));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>