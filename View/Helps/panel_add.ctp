<div class="helps form">
    <?php echo $this->Form->create('Help', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar ayuda'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('controller', array('label' => __('Sección') . ' *'));
        echo $this->JqueryValidation->input('action', array('label' => __('Acción') . ' *'));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('content', array('label' => __('Contenido') . ' *', 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('soloadmin', array('label' => __('Solo para admin?') . ' *', 'type' => 'checkbox'));
        echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado') . ' *', 'type' => 'checkbox', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>