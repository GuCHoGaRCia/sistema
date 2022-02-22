<?php
// se utiliza para seleccionar un rango de fechas y se abrira la cuenta corriente del proveedor
/* llamada: <a href="#" onclick='javascript:$("#pid").val("<?= $propietario['Propietario']['id'] ?>");$("#dfechascc").dialog("open")'>Cuenta corriente</a> */
?>
<script>
    $(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', changeYear: true, yearRange: '2016:+1'});
        var dialog = $("#dfechascc").dialog({
            autoOpen: false,
            height: "300",
            width: "250",
            maxWidth: "250",
            modal: true,
            title: 'Seleccione fecha de inicio y fin',
            position: {my: "center", at: "center", of: window},
            buttons: {
                "Ver": function () {
                    var f1 = $("#f1").val();
                    var f2 = $("#f2").val();
                    var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
                    var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
                    if (x > y) {
                        alert('<?= __('La fecha de inicio debe ser menor o igual a la de fin') ?>');
                        return false;
                    }
                    $("#vercc").submit();
                },
                Cancelar: function () {
                    $("#vercc")[0].reset();
                    dialog.dialog("close");
                }
            },
            close: function () {
                if (typeof ($("#vercc")[0]) !== "undefined") {
                    $("#vercc")[0].reset();
                }
            },
            open: function () {
                event.preventDefault();
            }
        });
    });
</script>
<div id="dfechascc" style="display:none">
    <div class="form">
        <?php echo $this->Form->create($model, ['class' => 'jquery-validation', 'id' => 'vercc', 'target' => '_blank', 'url' => $url]); ?>
        <p class="error-message" style="font-size:11px">* Campos obligatorios</p>
        <?php
        echo $this->Form->input('pid', ['type' => 'hidden', 'id' => 'pid']);
        $fecha = strtotime(date("Y-m-d") . ' -3 months');
        echo $this->Form->input('f1', ['type' => 'text', 'id' => 'f1', 'class' => 'dp', 'label' => __('Desde') . ' *', 'value' => date("d/m/Y", $fecha), 'style' => 'width:95px']);
        echo $this->Form->input('f2', ['type' => 'text', 'id' => 'f2', 'class' => 'dp', 'label' => __('Hasta') . ' *', 'value' => date("d/m/Y"), 'style' => 'width:95px']);
        ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>