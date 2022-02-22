<div class="cheques form">
    <?php echo $this->Form->create('Cheque', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Cheque de terceros'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('fecha_emision', array('label' => __('Fecha de emisión') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('fecha_vencimiento', array('label' => __('Fecha de vencimiento') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto/Banco/Número') . ' *'));
        echo $this->JqueryValidation->input('importe', array('label' => __('Importe') . ' *', 'min' => 0, 'step' => 0.01));
        echo $this->JqueryValidation->input('saldo', array('label' => false, 'value' => 0, 'type' => 'hidden'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'agregar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#ChequeCajaId").select2({language: "es"});
        $("#ChequeFechaEmisionDay").select2({language: "es"});
        $("#ChequeFechaEmisionMonth").select2({language: "es"});
        $("#ChequeFechaEmisionYear").select2({language: "es"});
        $("#ChequeFechaVencimientoDay").select2({language: "es"});
        $("#ChequeFechaVencimientoMonth").select2({language: "es"});
        $("#ChequeFechaVencimientoYear").select2({language: "es"});
    });

    $("#agregar").click(function (event) {
        event.preventDefault();
        var x = new Date($("#ChequeFechaEmisionYear").val() + "-" + $("#ChequeFechaEmisionMonth").val() + "-" + $("#ChequeFechaEmisionDay").val());
        var y = new Date($("#ChequeFechaVencimientoYear").val() + "-" + $("#ChequeFechaVencimientoMonth").val() + "-" + $("#ChequeFechaVencimientoDay").val());
        if (x > y) {
            alert('<?= __('La fecha de emisión debe ser menor o igual a la de vencimiento') ?>');
            return false;
        }
        $("#ChequeAddForm").submit();
    });
</script>