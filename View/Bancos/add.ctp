<div class="bancos form">
    <?php echo $this->Form->create('Banco', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Banco'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('address', array('label' => __('Sucursal') . ' *'));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad') . ' *'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#BancoName").focus();
    });
</script>