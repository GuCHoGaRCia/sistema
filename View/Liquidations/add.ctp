<div class="liquidations form">
    <?php echo $this->Form->create('Liquidation', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Liquidación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('liquidations_type_id', array('label' => __('Tipo de liquidación') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Siguiente')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#LiquidationConsorcioId").select2({language: "es"});
        $("#LiquidationLiquidationsTypeId").select2({language: "es"});
    });
</script>