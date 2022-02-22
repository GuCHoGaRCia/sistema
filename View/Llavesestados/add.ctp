<div class="llavesestados form">
    <?php echo $this->Form->create('Llavesestado', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Llavesestado'); ?></h2>
        <?php 
	echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
	?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
$(document).ready(function () {$("#LlavesestadoNombre").focus();});
</script>