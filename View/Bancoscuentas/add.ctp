<div class="bancoscuentas form">
    <?php echo $this->Form->create('Bancoscuenta', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cuenta Bancaria'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio'), 'empty' => __('Seleccione consorcio...')));
        echo $this->JqueryValidation->input('banco_id', array('label' => __('Banco') . ' *'));
        echo $this->JqueryValidation->input('cuenta', array('label' => __('Cuenta') . ' *'));
        echo $this->JqueryValidation->input('cbu', array('label' => __('CBU') . ' *'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('defectocobranzaautomatica', array('label' => __('Cuenta por defecto Cobranzas Automáticas') . ' *'));
        echo $this->JqueryValidation->input('habilitada', array('label' => __('Habilitada') . ' *', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#BancoscuentaConsorcioId").select2({language: "es"});
        $("#BancoscuentaBancoId").select2({language: "es"});
    });
</script>