<div class="bancosextracciones form">
    <?php echo $this->Form->create('Bancosextraccione', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Extracción bancaria'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('bancoscuenta_id', array('label' => __('Cuenta bancaria') . ' *'));
        //echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('caja_id', array('label' => false, 'type' => 'hidden', 'value' => key($cajas)));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *'));
        echo $this->JqueryValidation->input('conciliado', array('label' => __('Conciliado') . ' *', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#BancosextraccioneBancoscuentaId").select2({language: "es"});
        $("#BancosextraccioneConsorcioId").select2({language: "es"});
        $("#BancosextraccioneFechaDay").select2({language: "es"});
        $("#BancosextraccioneFechaMonth").select2({language: "es"});
        $("#BancosextraccioneFechaYear").select2({language: "es"});
    });
</script>