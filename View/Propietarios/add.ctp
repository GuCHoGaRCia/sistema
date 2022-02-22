<div class="propietarios form">
    <?php echo $this->Form->create('Propietario', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Propietario'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('code', array('label' => __('Código') . ' *'));
        echo $this->JqueryValidation->input('unidad', array('label' => __('Unidad') . ' *'));
        echo $this->JqueryValidation->input('orden', array('label' => __('Orden'), 'value' => 0));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->Form->input('email', array('label' => __('Email'), 'type' => 'text'));
        echo $this->JqueryValidation->input('address', array('label' => __('Dirección') . ' *'));
        echo $this->JqueryValidation->input('postal_address', array('label' => __('Dirección postal') . ' *'));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad') . ' *'));
        echo $this->JqueryValidation->input('postal_city', array('label' => __('Ciudad postal') . ' *'));
        echo $this->JqueryValidation->input('telephone', array('label' => __('Teléfono')));
        echo $this->JqueryValidation->input('whatsapp', array('label' => __('WhatsApp')));
        echo $this->JqueryValidation->input('superficie', array('label' => __('Superficie')));
        echo $this->JqueryValidation->input('poligono', array('label' => __('Polígono')));
        echo $this->JqueryValidation->input('cuit', array('label' => __('CUIT')));
        echo $this->JqueryValidation->input('estado_judicial', array('label' => __('Estado judicial')));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('observations', array('label' => __('Observaciones'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('imprime_resumen_cuenta', array('label' => __('Imprime resumen cuenta'), 'checked' => 'checked'));
        echo $this->JqueryValidation->input('sistema_online', array('label' => __('Sistema Online')));
        echo $this->JqueryValidation->input('exceptua_interes', array('label' => __('Exceptúa interés')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#PropietarioConsorcioId").select2({language: "es"});
        $("#PropietarioCode").focus();
        
        $("#PropietarioCode").on('keyup', function () {
            $("#PropietarioOrden").val($(this).val());
        });
        $("#PropietarioAddress").on('keyup', function () {
            $("#PropietarioPostalAddress").val($(this).val());
        });
        $("#PropietarioCity").on('keyup', function () {
            $("#PropietarioPostalCity").val($(this).val());
        });
    });
</script>