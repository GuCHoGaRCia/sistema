<?php
// son las formas de pago: efectivo, transferencia (si el consorcio tiene cuenta bancaria asociada) o cheque
?>
<label>Forma de pago</label>
<div class="formadepago">
    <?php
    echo $this->JqueryValidation->input('amount', ['label' => __('Monto total') . ' *', 'id' => 'montototal', 'type' => 'number', 'value' => 0, 'min' => 0, 'readonly' => 'readonly', 'form' => 'guardarcobranza']);

    // los agrego asi porque sino me pone primero chequesdeterceros antes de efectivo
    $busca = $this->Functions->find2($formasdepago, ['forma' => 'Efectivo']);
    if (!empty($busca)) {
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('efectivo', ['label' => __('Efectivo'), 'type' => 'number', /* 'readonly' => 'readonly', */ 'id' => 'efectivo', 'min' => 0, 'value' => 0.00, 'form' => 'guardarcobranza']);
        echo "&nbsp;&nbsp;" . $this->Html->image('sa.png', ['title' => 'Saldo por Efectivo', 'style' => 'cursor:cell', 'onclick' => '$("#efectivo").val(saldox(true));calcula();']);
        echo "</div>";
    } else {
        echo $this->JqueryValidation->input('efectivo', ['type' => 'hidden', 'id' => 'efectivo', 'value' => 0, 'form' => 'guardarcobranza']);
    }
    $busca = $this->Functions->find2($formasdepago, ['forma' => 'Cheque de Terceros']);
    if (!empty($busca)) {
        echo $this->element('chequesterceros');
    } else {
        echo $this->JqueryValidation->input('totalchequeterc', ['type' => 'hidden', 'id' => 'totalchequeterc', 'value' => 0, 'form' => 'guardarcobranza']);
    }


    $fdpbanco = ['' => 'Seleccione'];
    foreach ($formasdepago as $k => $v) {
        if ($v['destino'] === '2' && !empty($bancoscuentas)) {
            $fdpbanco[$v['id']] = $v['forma'];
        }
    }

    if (!empty($fdpbanco)) {
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('transferencia', ['label' => __('Otros'), 'name' => 'data[Cobranza][transferencia]', 'type' => 'number', 'id' => 'transferencia', 'min' => 0, 'value' => 0, 'form' => 'guardarcobranza']);
        echo "&nbsp;&nbsp;" . $this->Html->image('sa.png', ['title' => 'Saldo por banco', 'style' => 'cursor:cell', 'onclick' => '$("#transferencia").val(saldox());calcula();']);
        echo "&nbsp;&nbsp;" . $this->JqueryValidation->input('formadepago', ['label' => 'Forma de pago', 'name' => 'data[Cobranza][formadepago]', 'options' => $fdpbanco, 'form' => 'guardarcobranza']);
        echo "&nbsp;&nbsp;" . $this->JqueryValidation->input('bancoscuenta_id', ['label' => 'Cuenta bancaria', 'name' => 'data[Cobranza][bancoscuenta_id]', 'form' => 'guardarcobranza']);
        echo "</div>";
    } else {
        echo $this->JqueryValidation->input('transferencia', ['type' => 'hidden', 'id' => 'transferencia', 'value' => 0, 'form' => 'guardarcobranza']);
    }
    ?>
</div>
<script>
    $("#CobranzaBancoscuentaId").select2({language: "es"});
    function calcula() {
        var total = 0.00;
        $("input[id^='CobranzaLt']").each(function () {
            if ($(this).val() === "") {
                $(this).val(0);
            }
        });
        $("input[id^='CobranzaLt']").each(function () {
            total += parseFloat($(this).val());
        });
        if (total < 0) {
            alert('<?= __("El importe a guardar debe ser mayor a cero") ?>');
            return false;
        }
        $("#montototal").val(total.toFixed(2));
        var banco = saldox();
        if (banco < 0) {
            alert('<?= __("El importe a transferir debe ser mayor o igual a cero") ?>');
            return false;
        }
        var tcht = 0;
        $("input[id^='lcht_']").each(function () {
            tcht += parseFloat($(this).val());
        });

        $("#totalchequeterc").val(parseFloat(tcht).toFixed(2));
        return true;
    }
    $(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', changeYear: true, yearRange: '2016:+1'});
        /*$("#montototal").change(function () {
         var tot = $(this).val() - saldox();
         if (tot < 0) {
         $("#efectivo").val(0);
         } else {
         $("#efectivo").val(parseFloat(tot).toFixed(2));
         }
         });*/
        $("#transferencia").change(function () {
            if (parseFloat($("#transferencia").val()) < 0) {
                alert('<?= __("El importe a transferir debe ser mayor o igual a cero") ?>');
                $("#transferencia").val(0);
            }
            var tot = parseFloat($("#montototal").val()) - parseFloat($("#totalchequeterc").val()) - parseFloat($(this).val());
            if (tot < 0) {
                alert('<?= __('No se puede transferir un importe mayor al monto total') ?>');
                $("#efectivo").val(parseFloat($("#montototal").val() - $("#totalchequeterc").val()).toFixed(2));
                $("#transferencia").val(0);
            } else {
                $("#efectivo").val(parseFloat(tot).toFixed(2));
            }
        });
    });
    function saldox(esefectivo) {
        var efectivo = esefectivo ? 0 : parseFloat($("#efectivo").val());
        var total = parseFloat(parseFloat($("#montototal").val()) - efectivo - parseFloat($("#totalchequeterc").val()) - parseFloat($("#transferencia").val())).toFixed(2);
        if (total > 0) {
            return total;
        } else {
            return 0;
        }
    }
</script>
<style type="text/css">
    .formadepago{
        border:1px solid gray;
        padding:10px;
        line-height:10px;
    }
    .formadepago h1{
        width:600px;
        font-size:16px;
        font-weight:bold;
        text-align:left;
        margin:0;
        padding:5px;
    }
    .formadepago ul{
        list-style-type:none;
        margin:0;
        padding:0;
        white-space:nowrap;
        font-weight:bold;
        padding:5px;
        color:#fff;
    }
    .formadepago .titulos li{
        list-style-type:none;
        display:inline-block;
    }
    .formadepago .registros li{
        color:#000;
        display:inline-block;
    }
    input[type=checkbox] label{
        top:5px;
    }
    .cobranzasform{
        font-size:1em;
    }
</style> 
