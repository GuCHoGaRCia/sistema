<div class="contcuentas form">
    <?php echo $this->Form->create('Contcuenta', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Cuenta'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('conttitulo_id', ['label' => __('Padre'), 'options' => $hojas]);
        echo $this->JqueryValidation->input('code', ['label' => __('Código')]);
        echo $this->JqueryValidation->input('titulo', ['label' => __('Título')]);
        echo $this->JqueryValidation->input('orden', ['label' => __('Órden'), 'value' => 0]);
        //echo $this->JqueryValidation->input('debehaber', ['label' => __('Tildar para Debe / Destildar para Haber'), 'type' => 'checkbox']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#ContcuentaConttituloId").select2({language: "es"});
        $("#ContcuentaCode").focus();
    });
</script>