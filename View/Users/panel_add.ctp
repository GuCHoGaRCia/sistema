<?php
echo $this->Html->script(['jquery.pass']);
?>
<div class="info"><?php echo __('La contrase&ntilde;a debe contener: al menos 8 caracteres incluidos 1 n&uacute;mero, 1 letra may&uacute;scula, 1 letra min&uacute;scula y 1 caracter especial'); ?></div>
<div class="users form">
    <?php echo $this->Form->create('User', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Usuario'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('client_id', array('label' => __('Cliente') . ' *'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('username', array('label' => __('Usuario') . ' *'));
        echo $this->JqueryValidation->input('password', array('label' => __('Contraseña') . ' *', 'id' => 'password', 'data-display' => 'dpassword'/* , 'value' => $clave */));
        echo '<div class="left" id="dpassword"></div><div class="clear"></div>';
        echo $this->JqueryValidation->input('perfil', array('label' => __('Perfil'), 'options' => ['0' => 'Administrador General (defecto)'] + $profiles));
        echo $this->JqueryValidation->input('eliminacobranzas', array('label' => __('Elimina cobranzas?')));
        echo $this->JqueryValidation->input('is_admin', array('label' => __('Admin?')));
        echo $this->JqueryValidation->input('enabled', array('label' => __('Habilitado'), 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $('#UserAddForm').submit(function () {
            if ($("#password").val().length >= 8) {
                return true;
            }
            alert("La contraseña debe tener al menos 8 caracteres");
            return false;
        });
        $('#password, #password2').pStrength({
            'changeBackground': true,
            'onPasswordStrengthChanged': function (passwordStrength, strengthPercentage) {
                var mensaje = '';
                if ($(this).val()) {
                    $.fn.pStrength('changeBackground', this, passwordStrength);
                } else {
                    $.fn.pStrength('resetStyle', this);
                }
                mensaje = 'Insegura';
                if (strengthPercentage > 50 && strengthPercentage <= 70) {
                    mensaje = 'Buena';
                } else if (strengthPercentage > 70 && strengthPercentage <= 95) {
                    mensaje = 'Muy buena';
                } else if (strengthPercentage > 95) {
                    mensaje = 'Excelente';
                }
                $('#' + $(this).data('display')).html('Seguridad de la contrase&ntilde;a: ' + mensaje);
            },
            'onValidatePassword': function (strengthPercentage) {
                $('#' + $(this).data('display')).html($('#' + $(this).data('display')).html() + ' La contrase&ntilde;a es segura, pod&eacute;s continuar');
            }
        });
    });
    $(function () {
        $("#UserClientId").select2({language: "es"});
    });
</script>