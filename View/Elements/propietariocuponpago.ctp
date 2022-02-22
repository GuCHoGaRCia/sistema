<?php
// se utiliza para seleccionar un rango de fechas y se abrira la cuenta corriente del propietario
/* llamada: <a href="#" onclick='javascript:$("#pid").val("<?= $propietario['Propietario']['id'] ?>");$("#dfechascc").dialog("open")'>Cuenta corriente</a> */
?>
<script>
    $(function () {
        var dcupon = $("#cupon").dialog({
            autoOpen: false,
            height: "440",
            width: "370",
            maxWidth: "350px",
            modal: true,
            title: 'Ingrese vencimiento, concepto e importe',
            position: {my: "center", at: "center", of: window},
            buttons: {
                "Generar cupón": function () {
                    var f1 = $("#fx").val();
                    var x = f1.split("/");
                    var expireDate = new Date(x[2], x[1] - 1, x[0]);
                    var todayDate = new Date();
                    if (expireDate < todayDate) {
                        alert("La fecha de vencimiento debe ser mayor o igual a la actual");
                        return false;
                    }
                    if ($("#conc").val() === "") {
                        alert("Ingrese un concepto");
                        $("#conc").focus();
                        return false;
                    }
                    if (isNaN(parseFloat($("#imp").val())) || parseFloat($("#imp").val()) <= 0) {
                        alert("El importe debe ser mayor a cero");
                        $("#imp").focus();
                        return false;
                    }
                    $("#vercp").submit();
                },
                Cancelar: function () {
                    $("#vercp")[0].reset();
                    dcupon.dialog("close");
                }
            },
            close: function () {
                if (typeof ($("#vercp")[0]) !== "undefined") {
                    $("#vercp")[0].reset();
                }
            }
        });
    });
</script>
<div id="cupon">
    <div class="form">
        <?php echo $this->Form->create('Propietario', ['class' => 'jquery-validation', 'id' => 'vercp', 'target' => '_blank', 'url' => ['controller' => 'Reports', 'action' => 'reimpresioncupon']]); ?>
        <p class="error-message" style="font-size:11px">* Campos obligatorios</p>
        <?php
        echo $this->Form->input('pid', ['type' => 'hidden', 'id' => 'pidx']);
        echo $this->Form->input('liquidations_type_id', ['label' => __('Tipo de liquidación') . ' *', 'options' => $liquidations_type_id]);
        echo $this->Form->input('f', ['type' => 'text', 'class' => 'dp', 'id' => 'fx', 'label' => __('Vencimiento') . ' *', 'value' => date("d/m/Y", strtotime(date("Ymd") . " +10 days")), 'style' => 'width:98px']);
        echo $this->Form->input('c', ['type' => 'text', 'id' => 'conc', 'label' => __('Concepto') . ' *']);
        echo $this->Form->input('i', ['type' => 'number', 'id' => 'imp', 'label' => __('Importe') . ' *', 'value' => 0.00, 'min' => 0, 'step' => 0.01, 'style' => 'width:98px']);
        ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>