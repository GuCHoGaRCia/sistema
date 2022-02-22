<div class="userProfiles form">
    <?php echo $this->Form->create('UserProfile', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Editar Perfil'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->JqueryValidation->input('descripcion', ['label' => __('Descripcion')]);
        $selected = json_decode($this->request->data['UserProfile']['permisos']);
        $keys = [];
        foreach ($selected as $s) {
            $busqueda = array_search($s, $routes);
            if ($busqueda !== false) {
                $keys[] = $busqueda;
            }
        }
        echo $this->JqueryValidation->input('permisos', ['label' => __('Seleccionar Permisos'), 'multiple' => 'multiple', 'options' => $routes, 'selected' => $keys]);
        echo $this->JqueryValidation->input('perfil', ['label' => __('Perfil')]);
        echo $this->JqueryValidation->input('urldefecto', ['label' => __('URL Defecto')]);
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
    });
</script>