<div class="chequespropios form">
    <?php echo $this->Form->create('Chequespropio', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Cheque Propio'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('fecha_emision', ['type' => 'hidden']);
        echo $this->JqueryValidation->input('fecha_emision1', ['label' => __('Fecha emisión') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")]);
        echo $this->JqueryValidation->input('fecha_vencimiento', ['type' => 'hidden']);
        echo $this->JqueryValidation->input('fecha_vencimiento1', ['label' => __('Fecha vencimiento') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")]);
        echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *', 'type' => 'text']);
        echo $this->JqueryValidation->input('bancoscuenta_id', ['label' => __('Cuenta bancaria') . ' *', 'empty' => '', 'width:auto']);
        echo $this->JqueryValidation->input('numero', ['label' => __('Número') . ' *', 'style' => 'width:200px', 'type' => 'text']);
        echo $this->JqueryValidation->input('importe', ['label' => __('Importe') . ' *', 'min' => 0, 'step' => 0.01]);
        //echo $this->JqueryValidation->input('anulado', ['label' => __('Anular'), 'type' => 'checkbox']);
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
        $("#ChequespropioBancoscuentaId").select2({language: "es", placeholder: '<?= __('Seleccione cuenta bancaria...') ?>'});
        var img1;
        if (document.images) {
            img1 = new Image();
            img1.src = "<?=$this->webroot?>img/loading.gif";
        }
        $("#guardar").on("click", function (event) {
            if ($(".dp").val() === "") {
                alert("Debe seleccionar una fecha");
                return false;
            }
            var f1 = $("#ChequespropioFechaEmision1").val();
            var f2 = $("#ChequespropioFechaVencimiento1").val();
            var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
            var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
            if (x > y) {
                alert('<?= __('La fecha de emisión debe ser menor o igual a la de vencimiento') ?>');
                return false;
            }

            if ($("#ChequespropioConcepto").val() === "") {
                alert("Debe ingresar un Concepto");
                $("#ChequespropioConcepto").focus();
                return false;
            }
            if ($("#ChequespropioBancoscuentaId").val() === '') {
                alert("<?= __('Debe seleccionar una Cuenta Bancaria') ?>");
                return false;
            }
            if ($("#ChequespropioNumero").val() === "") {
                alert("Debe ingresar un Número de cheque");
                $("#ChequespropioNumero").focus();
                return false;
            }
            if ($("#ChequespropioImporte").val() === "" || isNaN($("#ChequespropioImporte").val())) {
                alert("Debe ingresar un Importe válido");
                $("#ChequespropioImporte").focus();
                return false;
            }
            /*$(".submit").html(img1);
             $(".submit").append("<p style='color:red'>Guardando, espere...</p>");
             $(".submit").prepend("<br>");*/
            $("#ChequespropioFechaEmision").val(f1.substr(6, 4) + "-" + f1.substr(3, 2) + "-" + f1.substr(0, 2));
            $("#ChequespropioFechaVencimiento").val(f2.substr(6, 4) + "-" + f2.substr(3, 2) + "-" + f2.substr(0, 2));
            $("#ChequespropioAddForm").submit();
        });
    });

</script>