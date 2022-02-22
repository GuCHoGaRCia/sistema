<div class="liquidations form">
    <?php echo $this->Form->create('Liquidation', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Liquidación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('liquidations_type_id', array('label' => __('Tipo de liquidación') . ' *'));
        $cual = empty($period) ? $liquidations : $period;
        echo $this->Form->input('liquidation_id', array('label' => __('Liquidación anterior') . ' *', 'readonly' => 'readonly', 'options' => [key($cual) => reset($cual)]));
        // muestro el label de periodo con los ultimos 3 periodos creados (si existen)
        if (count($period) == 0) {
            $label = __('Período') . ' *';
        } else {
            $label = __('Período') . ' *' . ' (los &uacute;ltimos per&iacute;odos creados son: ';
            foreach ($period as $k => $v) {
                $label .= $v . ', ';
            }
            $label[strlen($label) - 2] = ')';
        }
        echo $this->JqueryValidation->input('periodo', array('label' => $label));
        echo $this->JqueryValidation->input('vencimiento', array('label' => __('Vencimiento') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'autocomplete' => 'off'));
        echo $this->JqueryValidation->input('limite', array('label' => __('Límite') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'autocomplete' => 'off'));
        $c = 0;
        foreach ($coeficientes as $k => $v) {
            echo $this->Form->input("Liquidationspresupuesto.$c.total", array('label' => __('Presupuesto') . " ($v)", 'type' => 'text', 'value' => $presupuestos[key($cual)][$k] ?? 0));
            echo $this->Form->input("Liquidationspresupuesto.$c.coeficiente_id", array('type' => 'hidden', 'value' => $k));
            $c++;
        }
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('description', array('label' => __('Descripción'), 'class' => 'ckeditor'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', changeYear: true, yearRange: '2016:+1'});
        $("#LiquidationConsorcioId").select2({language: "es"});
        $("#LiquidationLiquidationsTypeId").select2({language: "es"});
        $("#LiquidationVencimientoDay").select2({language: "es"});
        $("#LiquidationVencimientoMonth").select2({language: "es"});
        $("#LiquidationVencimientoYear").select2({language: "es"});
        $("#LiquidationLimiteDay").select2({language: "es"});
        $("#LiquidationLimiteMonth").select2({language: "es"});
        $("#LiquidationLimiteYear").select2({language: "es"});
    });
    $("#guardar").on("click", function (event) {
<?php /* sino crea 2 veces la liquidacion!! 20190812 13:00, porqqqqqqqqqqqq ??? Chrome Version 76.0.3809.100 */ ?>
        event.preventDefault();
        var f1 = $("#LiquidationVencimiento").val();
        var f2 = $("#LiquidationLimite").val();
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            alert('<?= __('La fecha de vencimiento debe ser menor o igual a la del limite') ?>');
            return false;
        }
        $("#guardar").prop('disabled', true);
        $("#LiquidationAddForm").submit();
    });
</script>