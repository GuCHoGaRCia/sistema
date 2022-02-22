<?php
// son las formas de pago: efectivo, transferencia (si el consorcio tiene cuenta bancaria asociada) o cheque
?>
<div class="parent" style="display:flex;flex-wrap:wrap">
    <div class="formadepago">
        <?php
        echo $this->JqueryValidation->input('amount', array('label' => __('Monto a pagar'), 'id' => 'montototal', 'type' => 'number', 'value' => 0, 'min' => 0, 'readonly' => 'readonly', 'form' => 'guardarcobranza'));
        if (!empty($caja)) {
            echo $this->element('proveedorefectivo', ['max' => $caja['Caja']['saldo_pesos']]);
        }
        //if (!empty($bancoscuenta_id)) {
        echo $this->element('proveedortransferencia');
        echo $this->element('chequespropios', ['concepto' => 'PP ' . $proveedor['Proveedor']['name']]);
        //} else {
        //    echo "<div class='info'>No se pueden realizar transferencias o cheques propios porque el Consorcio no posee una Cuenta bancaria asociada</div>";
        //}
        if (!empty($caja)) {
            // el cheque esta en la caja, si no hay cajas configuradas, no hay cheques
            echo $this->element('proveedorchequesterceros', ['concepto' => 'PP ' . $proveedor['Proveedor']['name']]);
            echo $this->element('proveedorpagosacuenta');
        } else {
            echo "<div class='info'>No se puede utilizar efectivo o cheques porque el Usuario no tiene una Caja asociada</div>";
        }
        echo $this->element('notasdecredito');
        ?>
    </div>
    <div>
        <div class="totalesxconsorcio" style="flex:1;margin-left:10px;font-weight:bold">
            <u>Total a pagar por Consorcio</u>
            <?php
            // es el listado de consorcios y su total de facturas seleccionadas (o Pago a cuenta), para
            // facilitar al elegir las formas de pago la visualizacion de cuanto debo pagar de cada consorcio
            foreach ($consorcio_id as $k => $v) {
                echo "<li id='txcd_$k' style='list-style-type:none;display:none'>" . h($v) . " Total: $ <span id='txc_$k'>0.00</span> Restante: $ <span id='txcr_$k'>0.00</span></li>";
            }
            ?>
        </div>
    </div>
</div>

<script>
<?php /* se llama al hacer click en el tab "Formas de pago" y cada vez q se actualiza algun campo (chequepropio, tercero, transf, pagoacuenta) */ ?>
    var total = montototal = 0, transferencia = 0, efectivo = 0, chequepropio = 0, chequetercero = 0, pagosacuenta = 0;
    function calcula() {
        montototal = transferencia = efectivo = chequepropio = chequetercero = pagosacuenta = notasdecredito = 0;
        $("input[id^='f_']").each(function () {
            montototal += parseFloat($(this).val());
        });
        $("input[id^='pagoacuenta_']").each(function () {
            montototal += !isNaN(parseFloat($(this).val())) ? parseFloat($(this).val()) : 0;
        });

        $.each(totxconsor, function (k, v) {
            efectivo += v['e'];
            $.each(v['eadm'], function (k1, v1) {
                efectivo += v1;
            });
            chequepropio += v['chp'] + v['chpadm'];
            chequetercero += v['cht'];
            transferencia += v['t'];
            $.each(v['tadm'], function (k1, v1) {
                transferencia += v1;
            });
            pagosacuenta += v['apc'];
            notasdecredito += v['anc'];
        });
        $("#montototal").val(montototal.toFixed(2));
        $("#efectivo").val(efectivo.toFixed(2));
        $("#totaltransferencia").val(transferencia.toFixed(2));
        $("#totalchequeprop").val(chequepropio.toFixed(2));
        $("#totalchequeterc").val(chequetercero.toFixed(2));
        $("#pagosacuenta").val(pagosacuenta.toFixed(2));
        $("#notasdecredito").val(notasdecredito.toFixed(2));
        $('#tabformasdepago').attr('onclick', null);
    }
    /*function reset() {
     var cero = parseFloat(0).toFixed(2);
     $("input[id^='lcht_']").each(function () {
     $(this).val(cero);
     });
     $("input[id^='tr_']").each(function () {
     $(this).val(cero);
     });
     $("input[id^='tadm_']").each(function () {
     $(this).val(cero);
     });
     $("#selTransfAdm").val(cero);
     $("input[id^='pca_']").each(function () {
     $(this).val(cero);
     });
     $("#efectivo").val(cero);
     $("#montototal").val(cero);
     $("#totalchequeprop").val(cero);
     $("#totalchequeterc").val(cero);
     $("#totaltransferencia").val(cero);
     $("#pagosacuenta").val(cero);
     $("#notasdecredito").val(cero);
     }*/

    $(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', changeYear: true, yearRange: '2016:+1'});
        $("#montototal").change(function () {
            var tot = $(this).val() - $("#chequesprop").val() - $("#chequesterc").val();
            if (tot < 0) {
                $("#efectivo").val(parseFloat(0).toFixed(2));
            } else {
                $("#efectivo").val(tot);
            }
        });
    });

    $("#seleccPagocuenta").button().on("click", function () {
        dialogSelPagocuenta.dialog("open");
    });

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
