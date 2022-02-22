<div class="cajas form">
    <?php echo $this->Form->create('Caja', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Transferir entre cajas'); ?></h2>
        <?php
        //echo $this->JqueryValidation->input('cajas', ['label' => false, /* 'type' => 'hidden' */]);
        echo $this->JqueryValidation->input('destinos', ['label' => __('Caja destino') . ' *']);
        echo $this->JqueryValidation->input('importe', ['label' => __('Importe pesos') . " (saldo caja = " . $saldo[$cajaid] . ")", 'type' => 'number', 'min' => 0, 'value' => 0.00, 'step' => 0.01]);
        echo "<label>Listado de cheques disponibles para Transferir</label>";
        if (!empty($cheques)) {
            foreach ($cheques as $k => $v) {
                $id = $v['Cheque']['id'];
                echo $this->Form->input("Cheque.$id.cheque_id", ['id' => "lch$id", 'type' => 'checkbox', 'label' => h($v['Cheque']['conceptoimporte']), 'style' => 'left:15px', 'value' => $id]);
            }
        } else {
            ?>
            <div class="info" style='margin-left:20px'>No se encuentran cheques disponibles para transferir</div>
            <?php
        }
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Transferir'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#CajaDestinos").select2({language: "es"});
        $("#CajaImporte").focus();
    });
    $("#guardar").on("click", function (event) {
        event.preventDefault();
        if (parseFloat($("#CajaImporte").val()) === 0 && $("input[id^=lch]:checked").length === 0) {
            alert("El importe debe ser mayor a cero o debe transferir uno o más cheques");
            return false;
        }

        $("input[id^=lch]").each(function () {<?php /* Deshabilito los cheques NO seleccionados, asi no se envian al pedo */ ?>
            if (!$(this).is(":checked")) {
                $(this).remove();
            }
        });
        $("#CajaTransferenciasForm").submit();
    });
</script>
<style>
    label{
        width:100% !important;
    }
</style>