<div class="contejercicios form">
    <?php echo $this->Form->create('Contejercicio', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Ejercicio Contable'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio')]);
        echo $this->JqueryValidation->input('nombre', ['label' => __('Nombre')]);
        echo $this->JqueryValidation->input('inicio', ['label' => __('Inicio'), 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("01/01/Y")]);
        echo $this->JqueryValidation->input('fin', ['label' => __('Fin'), 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("31/12/Y")]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#ContejercicioConsorcioId").select2({language: "es"});
        $("#ContejercicioNombre").focus();
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+2'});
    });
    $("#guardar").on("click", function (e) {
        var f1 = $("#ContejercicioInicio").val();
        var f2 = $("#ContejercicioFin").val();
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            e.preventDefault();
            alert('<?= __('La fecha de Inicio debe ser menor o igual al Fin') ?>');
            return false;
        }
        $("#ContejercicioAddForm").submit();
    });
</script>