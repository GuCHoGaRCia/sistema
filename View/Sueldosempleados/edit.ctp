<div class="sueldosempleados form">
    <?php echo $this->Form->create('Sueldosempleado', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Sueldosempleado'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('id', array('label' => __('Id')));
	echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio_id')));
	echo $this->JqueryValidation->input('sueldoscategoria_id', array('label' => __('Sueldoscategoria_id')));
	echo $this->JqueryValidation->input('sueldosobrassociale_id', array('label' => __('Sueldosobrassociale_id')));
	echo $this->JqueryValidation->input('legajo', array('label' => __('Legajo')));
	echo $this->JqueryValidation->input('nombre', array('label' => __('Nombre')));
	echo $this->JqueryValidation->input('dni', array('label' => __('Dni')));
	echo $this->JqueryValidation->input('cuil', array('label' => __('Cuil')));
	echo $this->JqueryValidation->input('hijos', array('label' => __('Hijos')));
	echo $this->JqueryValidation->input('ingreso', array('label' => __('Ingreso')));
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>