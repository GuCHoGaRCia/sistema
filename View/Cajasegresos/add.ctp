<div class="cajasegresos form">
    <?php echo $this->Form->create('Cajasegreso', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Egreso de caja'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('caja_id', array('label' => __('Caja') . ' * Saldo: ' . $saldo[key($saldo)], 'readonly' => 'readonly'));
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio')));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *', 'value' => 0, 'min' => 0, 'step' => 0.01/* , 'max' => $saldo[key($saldo)] */));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#CajasegresoConsorcioId").select2({language: "es"});
        $("#CajasegresoFechaDay").select2({language: "es"});
        $("#CajasegresoFechaMonth").select2({language: "es"});
        $("#CajasegresoFechaYear").select2({language: "es"});
    });
</script>