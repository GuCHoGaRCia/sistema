<div class="bancostransferencias form">
    <?php echo $this->Form->create('Bancostransferencia', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Transferencia Bancaria'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('bancoscuenta_id', array('label' => __('Cuenta Bancaria Orígen') . ' *'));
        echo $this->JqueryValidation->input('destino_id', array('label' => __('Cuenta Bancaria Destino') . ' *'));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#BancostransferenciaBancoscuentaId").select2({language: "es"});
        $("#BancostransferenciaDestinoId").select2({language: "es"});
        $("#BancostransferenciaFechaDay").select2({language: "es"});
        $("#BancostransferenciaFechaMonth").select2({language: "es"});
        $("#BancostransferenciaFechaYear").select2({language: "es"});
    });
</script>