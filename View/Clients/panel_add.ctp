<div class="clients form">
    <?php echo $this->Form->create('Client', array('class' => 'jquery-validation', 'type' => 'file')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cliente'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('code', array('label' => __('Código') . ' *'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('cuit', array('label' => __('CUIT')));
        echo $this->JqueryValidation->input('address', array('label' => __('Dirección') . ' *'));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad') . ' *'));
        echo $this->JqueryValidation->input('telephone', array('label' => __('Teléfono') . ' *'));
        echo $this->JqueryValidation->input('whatsapp', array('label' => __('WhatsApp')));
        echo $this->Form->input('email', array('type' => 'text', 'label' => __('Email') . ' (Ej: juan@gmail.com o si son varias direcciones, separarlas con coma. Ej: juan@gmail.com,pepe@mail.com)' . ' *'));
        echo $this->JqueryValidation->input('identificador_cliente', array('label' => __('Identificador') . ' *'));
        echo $this->JqueryValidation->input('numeroregistro', array('label' => __('Nº inscripción en el registro (matrícula)')));
        echo $this->JqueryValidation->input('web', array('label' => __('Página web')));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('description', array('label' => __('Descripción'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('Client.logo.', array(
            'label' => __('Logo Administración'),
            'id' => 'logoadm',
            'name' => 'logoadm',
            'type' => 'file',
        ));
        echo $this->JqueryValidation->input('Client.firma.', array(
            'label' => __('Firma Administración'),
            'id' => 'firmaadm',
            'name' => 'firmaadm',
            'type' => 'file',
        ));
        echo $this->JqueryValidation->input('usa_plapsa', array('label' => __('Utiliza la Plataforma de Pagos'), 'checked' => 'checked'));
        echo $this->JqueryValidation->input('imprime_cola', array('label' => __('Imprime reportes en Cola de Impresión'), 'checked' => 'checked'));
        echo $this->JqueryValidation->input('consultaspropietarios', array('label' => __('Permite que los Propietarios envien consultas al Administrador')));
        echo $this->JqueryValidation->input('es_manekese', array('label' => __('Contabiliza cartas en MAKENESE S.R.L.'), 'checked' => 'checked'));
        echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado'), 'checked' => 'checked'));
        ?>    
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<script>
    $("#ClientPanelAddForm").submit(function (event) {
        var arch = document.getElementById("logoadm");
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['jpg', 'jpeg', 'png']) === -1) {
                alert('Los archivos a adjuntar deben ser .jpg, .jpeg o .png');
                event.preventDefault();
                return false;
            }
        }
        var arch = document.getElementById("firmaadm");
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['jpg', 'jpeg', 'png']) === -1) {
                alert('Los archivos a adjuntar deben ser .jpg, .jpeg o .png');
                event.preventDefault();
                return false;
            }
        }
    });
</script>
<?php
echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));

