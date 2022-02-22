<div class="userProfiles form">
    <?php echo $this->Form->create('UserProfile', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Perfil'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->JqueryValidation->input('descripcion', ['label' => __('Descripcion')]);
        echo $this->JqueryValidation->input('permisos', ['label' => __('Seleccionar Permisos'), 'multiple' => 'multiple', 'options' => $routes]);
        echo $this->JqueryValidation->input('perfil', ['label' => __('Perfil')]);
        echo $this->JqueryValidation->input('urldefecto', ['label' => __('URL Defecto'), 'empty' => 'noticias/index', 'options' => $routes]);
        echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado')]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $('#UserProfileNombre').focus();
        $("#UserProfilePermisos").select2({language: "es", placeholder: 'Seleccione Permisos...'});
        $("#UserProfileUrldefecto").select2({language: "es", placeholder: 'Seleccione URL por defecto...'});
        $("#UserProfileUrldefecto").select2("", "");
    });
</script>