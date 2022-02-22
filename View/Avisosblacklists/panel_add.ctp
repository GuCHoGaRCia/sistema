<div class="avisosblacklists form">
    <?php echo $this->Form->create('Avisosblacklist', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar a la lista negra'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('client_id', ['label' => __('Cliente')]);
        echo $this->JqueryValidation->input('email', ['label' => __('Email')]);
        echo $this->JqueryValidation->input('cantidad', ['label' => __('Cantidad'), 'value' => 0]);
        echo $this->JqueryValidation->input('dsc', ['label' => __('Descripción')]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#AvisosblacklistClientId").select2({language: "es"});
        $("#AvisosblacklistEmail").focus();
    });
</script>