<div class="reports form">
    <?php echo $this->Form->create('Report', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Reporte'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('funcion', array('label' => __('Función')));
        echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado'), 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>