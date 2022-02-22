<div class="notas form">
    <?php echo $this->Form->create('Nota', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Notas'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id', array('label' => __('id')));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Liquidación')));
        echo $this->JqueryValidation->input('resumencuenta', array('label' => __('Nota resúmen de cuenta'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('resumengasto', array('label' => __('Nota resúmen de gastos'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('resumengastotop', array('label' => __('Nota ARRIBA resúmen de gastos'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('composicion', array('label' => __('Nota composición de saldos'), 'class' => 'ckeditor'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#NotaLiquidationId").select2({language: "es", width: 600});
    });
</script>