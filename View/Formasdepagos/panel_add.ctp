<div class="formasdepagos form">
    <?php echo $this->Form->create('Formasdepago', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Forma de pago'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('forma', ['label' => __('Forma')]);
        $options = ['0' => 'Ninguno', '1' => 'Caja', '2' => 'Banco'];
        echo $this->JqueryValidation->input('destino', ['label' => __('Destino'), 'type' => 'select', 'options' => $options]);
        echo $this->JqueryValidation->input('habilitada', array('label' => __('Habilitada')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#FormasdepagoForma").focus();
    });
</script>