<div class="liquidations form">
    <?php echo $this->Form->create('Liquidation', array('class' => 'jquery-validation', 'url' => 'guardarnueva')); ?>
    <fieldset>
        <h2><?php echo __('Agregar Liquidación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('liquidations_type_id', array('label' => __('Tipo de liquidación') . ' *'));
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Liquidación anterior') . ' *'));
        //echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
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
        echo $this->JqueryValidation->input('vencimiento', array('label' => __('Vencimiento') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('limite', array('label' => __('Límite') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        $c = 0;
        foreach ($coeficientes as $k => $v) {
            echo $this->Form->input("Liquidationspresupuesto.$c.total", array('label' => __('Presupuesto') . " ($v)", 'type' => 'text', 'value' => 0));
            echo $this->Form->input("Liquidationspresupuesto.$c.coeficiente_id", array('type' => 'hidden', 'value' => $k));
            $c++;
        }
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('description', array('label' => __('Descripción'), 'class' => 'ckeditor'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#LiquidationConsorcioId").select2({language: "es"});
        $("#LiquidationLiquidationsTypeId").select2({language: "es"});
        $("#LiquidationLiquidationId").select2({language: "es"});
        $("#LiquidationVencimientoDay").select2({language: "es"});
        $("#LiquidationVencimientoMonth").select2({language: "es"});
        $("#LiquidationVencimientoYear").select2({language: "es"});
        $("#LiquidationLimiteDay").select2({language: "es"});
        $("#LiquidationLimiteMonth").select2({language: "es"});
        $("#LiquidationLimiteYear").select2({language: "es"});
    });
</script>