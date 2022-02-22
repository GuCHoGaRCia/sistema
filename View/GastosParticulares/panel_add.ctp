<div class="gastosParticulares form">
    <?php echo $this->Form->create('GastosParticulare', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Gastos Particulares'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Seleccione la liquidación (solo se mostrarán las liquidaciones abiertas)') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Siguiente')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>