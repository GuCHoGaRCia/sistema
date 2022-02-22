<div class="proveedors form">
    <?php echo $this->Form->create('Proveedor', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Proveedor'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('cuit', array('label' => __('CUIT')));
        echo $this->JqueryValidation->input('matricula', array('label' => __('Matrícula')));
        echo $this->JqueryValidation->input('address', array('label' => __('Dirección')));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad')));
        echo $this->JqueryValidation->input('telephone', array('label' => __('Teléfono')));
        echo $this->JqueryValidation->input('email', array('label' => __('Email')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<script>
    $(document).ready(function () {
        $("#ProveedorName").focus();
    });
</script>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));
?>