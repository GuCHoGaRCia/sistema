<?php
$cheq = "<b>" . __('Cheques terceros') . " (<span id='cantcht'></span>)" . "</b> ";
$cheq .= "&nbsp;";
$cheq .= $this->Html->image('view.png', ['title' => __('Seleccionar'), 'id' => 'seleccChequeterc', 'style' => 'width:20px;height:20px;cursor:pointer']);
echo $this->JqueryValidation->input('cheque', ['label' => $cheq, 'type' => 'number', 'readonly' => 'readonly', 'id' => 'totalchequeterc', 'value' => 0, 'min' => 0, 'form' => 'guardarcobranza']);
?>
<script>
    $("#seleccChequeterc").on("click", function () {
        mostrarChequesT();
        dialogSelChequeterc.dialog("open");
    });
    function mostrarChequesT() {
        $("#dialogChequeterclistacheques div div:not(:first)").each(function () {
            $(this).hide();
        });

        var cantcht = 0;
        $("div[id^='c__']").each(function () {
            var strid = $(this).prop('id');
            var id = strid.replace('c__', '');
            if (parseFloat($("#totalfacturas_" + id).html()) > 0) {
                $("#dialogChequeterclistacheques div div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").prop('disabled', false);
                    $(this).show();
                    cantcht++;
                });
            } else {
                $("#dialogChequeterclistacheques div div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").prop('disabled', true);<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
                });
            }
        });
        $("#cantcht").html(cantcht);
    }

    function clickChT(chid) {
        var cid = $("#lcht_" + chid).data('cid');
        if ($("#lcht_" + chid).prop('checked')) {<?php /* esta tildando un cheque terceros */ ?>
            if (parseFloat($("#lcht_" + chid).data('val')) > parseFloat(restante(cid))) {<?php /* cuando tilda actualizo, si destilda solo cambio el total de cheques tercero */ ?>
                alert('<?= __('El importe seleccionado es mayor al importe restante a abonar del Consorcio') ?> ' + consorcios[cid]);
                $("#lcht_" + chid).prop('checked', false);
            } else {
                totxconsor[cid]['cht'] = parseFloat($("#lcht_" + chid).data('val'));
                $("#lcht_" + chid).prop('checked', true);
            }
        } else {
            totxconsor[cid]['cht'] -= parseFloat($("#lcht_" + chid).data('val'));
            $("#lcht_" + chid).prop('checked', false);
        }
    }

    dialogSelChequeterc = $("#dialogChequeterclistacheques").dialog({
        autoOpen: false,
        width: 900,
        modal: true,
        title: "Seleccionar cheque tercero",
        open: function (event, ui) {
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        buttons: {
            Aceptar: function () {
                recalculaTodo();
                dialogSelChequeterc.dialog("close");
            }
        }
    });
    function totxconsorcht() {<?php /* suma los totales de cheques terceros x consorcio (para mostrar total y restante en forma de pago) */ ?>
        $("#totalchequeterc").val(parseFloat(0).toFixed(2));
        $("div[id^='c__']").each(function () {<?php /* inicializo en cero los totales de chequesterceros */ ?>
            var strid = $(this).prop('id');
            var id = strid.replace('c__', '');
            totxconsor[id]['cht'] = 0;
        });
        $("#dialogChequeterclistacheques div div input").each(function () {<?php /* para cada cheque terceros */ ?>
            var cid = $(this).data('cid');
            if (parseFloat($("#totalfacturas_" + cid).html()) > 0 && $("#" + $(this).prop('id')).is(':checked')) {<?php /* si seleccionó algun importe a pagar y tildo el cheque */ ?>
                totxconsor[cid]['cht'] += parseFloat($(this).data('val'));
            }
        });
    }
</script>
<div id="dialogChequeterclistacheques" title="Listado de cheques disponibles" style="display:block">
    <?php
    if (isset($chequesterceros) && count($chequesterceros) != 0) {// muestro los cheques pendientes (si existen)
        //echo "<label>" . __("Seleccione los cheques a utilizar") . " * Total seleccionado: <span id='totseleccht'>0.00</span></label>";
        echo "<div class='info'>Haga click en la casilla del cheque para utilizarlo</div><br>";
        echo "<div id='listachequetinfo'>";
        foreach ($chequesterceros as $k => $v) {// data-cid se utiliza para mostrar solo los cheques del consorcio seleccionado (se puede pagar a Proveedor de un consor con cheques de ese consor)                 dejar data[Cobranza] porq se usa en cobranzas de propietarios '$("#' . $v['Cheque']['id'] . '").val(' . $v['Cheque']['importe'] . ').change()'
            echo "<div class='inline' ";
            $tipocheque = $v['Cheque']['fisico'] == 1 ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ';
            echo (isset($v['Consorcio']['id']) ? "data-cid='" . $v['Consorcio']['id'] . "'" : "") . ">" . $this->Form->input('cheque_id', ['type' => 'checkbox', 'div' => false, 'label' => false, 'name' => 'data[' . $v['Consorcio']['id'] . '][chequeterceros][' . $v['Cheque']['id'] . ']', 'id' => 'lcht_' . $v['Cheque']['id'], 'data-val' => $v['Cheque']['importe'], /* 'data-saldo' => $v['Cheque']['saldo'], */ 'data-cid' => $v['Consorcio']['id'],
                'form' => 'guardarcobranza', 'style' => '-moz-transform:scale(1.5);-webkit-transform:scale(1.5);margin-right:10px', 'onclick' => 'clickChT(' . $v['Cheque']['id'] . ');']) . "&nbsp;$tipocheque" . h($v['Consorcio']['name']) . " - " . h($v['Cheque']['concepto']) . " <b>#" . h($v['Cheque']['banconumero']) . "</b> - Valor: $" . $v['Cheque']['importe'];
            echo "<span onclick='window.open(\"" . $this->webroot . "cobranzas/view/" . $v['Cobranza']['id'] . "\")' class='imgmove' style='background-image:url(" . $this->webroot . "img/icon-info.png);background-repeat:no-repeat;width:14px;height:14px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<div class='info'>No se encuentran cheques de terceros disponibles para utilizar</div>"; //length=91, si lo cambio, ver javascript cad.length
    }
    ?>
</div>