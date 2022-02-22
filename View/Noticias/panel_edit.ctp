<div class="noticias form">
    <?php echo $this->Form->create('Noticia', array('class' => 'jquery-validation')); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Noticia'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('titulo', array('label' => __('Título')));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('noticia', array('label' => __('Noticia'), 'class' => 'ckeditor'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>