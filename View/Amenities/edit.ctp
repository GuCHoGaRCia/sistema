<div class="amenities form">
    <?php echo $this->Form->create('Amenity', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Amenity'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->Form->input('consorcio_id', ['type' => 'hidden', 'value' => $this->request->data['Amenity']['consorcio_id']]);
        echo $this->Form->input('', ['label' => __('Consorcio'), 'type' => 'text', 'readonly' => 'readonly', 'disabled' => 'disabled', 'value' => h($consorcios[$this->request->data['Amenity']['consorcio_id']])]);
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('reglamento', ['label' => __('Reglamento'), 'class' => 'ckeditor']);
        echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado')]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#AmenityNombre").focus();
    });
</script>