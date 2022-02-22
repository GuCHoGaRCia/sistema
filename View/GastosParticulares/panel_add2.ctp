<div class="info"><?php echo __('Seleccione propietario (si es gasto particular) o coeficiente (si es gasto particular prorrateado), no ambos simultáneamente'); ?></div>
<div class="gastosParticulares form">
    <?php echo $this->Form->create('GastosParticulare', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Gastos Particulares'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Liquidación') . ' *'));
        echo $this->JqueryValidation->input('cuentasgastosparticulare_id', array('label' => __('Cuenta GP') . ' *'));
        echo $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario'), 'empty' => 'Seleccionar...'));
        echo $this->JqueryValidation->input('coeficiente_id', array('label' => __('Coeficiente'), 'empty' => 'Seleccionar...'));
        echo $this->JqueryValidation->input('date', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
		echo $this->Html->script('ckeditor/ckeditor');
		echo $this->JqueryValidation->input('description', array('label' => __('Descripción'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('amount', array('label' => __('Importe') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>