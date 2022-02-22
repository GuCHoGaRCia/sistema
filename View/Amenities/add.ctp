<div class="amenities form">
    <?php echo $this->Form->create('Amenity', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Amenitie'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio')]);
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('reglamento', ['label' => __('Reglamento'), 'class' => 'ckeditor']);
        //echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado')/* , 'checked' => 'checked' */]); // deshabilitado x defecto, para q agreguen los Turnos primero
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#AmenityConsorcioId").select2({language: "es"});
        $("#AmenityNombre").focus();
    });
</script>