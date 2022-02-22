<div class="cajasingresos form">
    <?php echo $this->Form->create('Cajasingreso', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Ingreso a caja'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('caja_id', array('label' => __('Caja') . ' * Saldo: ' . $saldo[key($saldo)], 'readonly' => 'readonly'));
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio')));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *', 'value' => 0, 'min' => 0, 'step' => 0.01));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#CajasingresoConsorcioId").select2({language: "es"});
        $("#CajasingresoFechaDay").select2({language: "es"});
        $("#CajasingresoFechaMonth").select2({language: "es"});
        $("#CajasingresoFechaYear").select2({language: "es"});
    });
</script>