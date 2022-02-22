<div class="emails form">
    <?php echo $this->Form->create('Email', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Email y volver a enviar'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
		echo $this->JqueryValidation->input('client_id', array('label' => __('Cliente') . ' *'));
        echo $this->Form->input('email', ['type' => 'text', 'label' => __('Emails') . ' (Ej: juan@gmail.com o si son varias direcciones, separarlas con coma. Ej: juan@gmail.com,pepe@mail.com)']);
        echo $this->JqueryValidation->input('asunto', ['label' => __('Asunto')]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('html', ['label' => __('Mensaje'), 'class' => 'ckeditor']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Volver a enviar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#EmailClientId").select2({language: "es"});
        $("#EmailEmail").focus();
    });
</script>