<div class="plataformasdepagos form">
    <?php echo $this->Form->create('Plataformasdepago', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Plataformasdepago'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('titulo', ['label' => __('Titulo')]);
	echo $this->JqueryValidation->input('habilitada', ['label' => __('Habilitada')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#PlataformasdepagoTitulo").focus();});
</script>