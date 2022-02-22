<div class="bancoscuentas form">
    <?php echo $this->Form->create('Bancoscuenta', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cuenta Bancaria'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('banco_id', array('label' => __('Banco') . ' *'));
        echo $this->JqueryValidation->input('cbu', array('label' => __('CBU') . ' *'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>