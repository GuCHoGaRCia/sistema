<div class="bancosdepositoscheques form">
    <?php echo $this->Form->create('Bancosdepositoscheque', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Depósito de cheque bancario'); ?></h2>
        <?php
        echo "<label>" . __("Seleccione los cheques a depositar") . " *</label>";
        foreach ($cheques as $k => $v) {
            $tipocheque = $v['Cheque']['fisico'] ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ';
            echo $this->Form->input("Cheque." . $v['Cheque']['id'] . ".cheque_id", array('label' => __('Cheques') . ' *', 'type' => 'checkbox', 'label' => $tipocheque . h((isset($v['Caja']['name']) ? $v['Caja']['name'] . " - " : '') . $v['Consorcio']['name'] . " - " . $v['Cheque']['conceptoimporte']), 'value' => $v['Cheque']['id']));
        }
        echo $this->JqueryValidation->input('bancoscuenta_id', array('label' => __('Cuenta bancaria') . ' *'));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('conciliado', array('label' => __('Conciliado') . ' *', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script type="text/javascript">
    $("#BancosdepositoschequeAddForm").submit(function (event) {
        if ($("input:checkbox:checked").length === 0) {
            alert("<?= __("Debe seleccionar al menos un cheque para depositar") ?>");
            return false;
        }
    });
    $("#BancosdepositoschequeBancoscuentaId").select2({language: "es"});
</script>
<style>
    label{
        width:100% !important;
    }
</style>