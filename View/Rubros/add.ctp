<div class="rubros form">
    <?php echo $this->Form->create('Rubro', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Rubro'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' * ' . "<img title='Tildar/Destildar todos' src='" . $this->webroot . 'img/1.png' . "' style='cursor:pointer' onClick=\"" . "$(':checkbox').attr('checked', !$(':checkbox').attr('checked'))" . "\" />", 'multiple' => 'checkbox'));
        echo $this->JqueryValidation->input('name1', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('name2', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name3', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name4', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name5', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name6', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name7', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name8', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name9', array('label' => __('Nombre')));
        echo $this->JqueryValidation->input('name10', array('label' => __('Nombre')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#RubroName").focus();
    });
    /*$("input[id^='RubroConsorcioId']").each(function () {
     $(this).prop('checked', true);
     });*/
    $("#RubroAddForm").submit(function (event) {
        var check = false;
        $("input[id^='RubroConsorcioId']").each(function () {
            if (this.id !== 'RubroConsorcioId') {
                if (this.checked) {// si chequeo algun check
                    check = true;
                }
            }
        });
        if (!check) {
            alert("<?= __('Debe seleccionar al menos un Consorcio') ?>");
            event.preventDefault();
            return false;
        }
        return true;
    });
</script>
<style>
    label { display: block; width: 400px !important; }
</style>