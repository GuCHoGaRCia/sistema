<div class="bancosdepositosefectivos form">
    <?php echo $this->Form->create('Bancosdepositosefectivo', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Crédito Bancario'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('caja_id', array('label' => false, 'type' => 'hidden', 'value' => 0));
        echo $this->JqueryValidation->input('bancoscuenta_id', array('label' => __('Cuenta bancaria') . ' *'));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *'));
        echo $this->JqueryValidation->input('conciliado', array('label' => __('Conciliado') . ' *', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index2'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#BancosdepositosefectivoBancoscuentaId").select2({language: "es"});
        $("#BancosdepositosefectivoFechaDay").select2({language: "es"});
        $("#BancosdepositosefectivoFechaMonth").select2({language: "es"});
        $("#BancosdepositosefectivoFechaYear").select2({language: "es"});
    });
</script>