<div class="contfunciones form">
    <?php echo $this->Form->create('Contfuncione', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __(' Contfuncione'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('titulo', ['label' => __('Titulo')]);
	echo $this->JqueryValidation->input('descripcion', ['label' => __('Descripcion')]);
	echo $this->JqueryValidation->input('modelo', ['label' => __('Modelo')]);
	echo $this->JqueryValidation->input('funcion', ['label' => __('Funcion')]);
	echo $this->JqueryValidation->input('habilitada', ['label' => __('Habilitada')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#ContfuncioneTitulo").focus();});
</script>