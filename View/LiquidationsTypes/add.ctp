<div class="liquidationsTypes form">
    <?php echo $this->Form->create('LiquidationsType', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Tipo de liquidación'); ?></h2>
        <?php
        echo $this->Form->input('client_id', array('type' => 'hidden', 'value' => $_SESSION['Auth']['User']['client_id']));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('prefijo', array('label' => __('Prefijo'), /* 'options' => [0 => '0 (Expensa ordinaria)', 5 => '5 (Expensa extraordinaria)', 9 => '9 (Fondo)'] */ 'type' => 'number', 'min' => 0, 'max' => 9));
        echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado'), 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $("#LiquidationsTypeName").focus();
</script>