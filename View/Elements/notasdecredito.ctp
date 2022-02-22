<?php
$cheq = "<b>" . __('Aplicar nota de crédito') . " (<span id='cantncaa'></span>)" . "</b> ";
$cheq .= $this->Html->image('view.png', ['title' => __('Seleccionar'), 'id' => 'selecnc', 'style' => 'width:20px;height:20px;cursor:pointer']);
echo $this->JqueryValidation->input('importenc', ['label' => $cheq, 'type' => 'number', 'id' => 'notasdecredito', 'value' => 0.00, 'readonly' => 'readonly', 'min' => 0.00, 'step' => 0.01, 'form' => 'guardarcobranza']);
?>

<script>
    $("#selecnc").on("click", function () {
        dialogNC.dialog("open");
    });
    $("#selecnc").on("click", function () {
        mostrarNCAA();
        dialogNC.dialog("open");
    });

    function mostrarNCAA() {
        $("#listanca div").each(function () {
            $(this).hide();
        });

        var cantncaa = 0;
        $("div[id^='c__']").each(function () {
            var strid = $(this).attr('id');
            var id = strid.replace('c__', '');
            if (parseFloat($("#totalfacturas_" + id).html()) > 0 && parseFloat($("#pagoacuenta_" + id).val()) === 0) {<?php /*  no permito aplicar pagos a cuenta si selecciono "Pago a cuenta" como factura */ ?>
                $("#listanca div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").removeAttr('disabled');
                    $(this).show();
                    cantncaa++;
                });
            } else {
                $("#listanca div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").val(parseFloat(0).toFixed(2));
                    $(this).find("input").prop('disabled', true);<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
                });
            }
        });
        $("#cantncaa").html(cantncaa);
    }
    dialogNC = $("#dialogNClista").dialog({
        autoOpen: false,
        width: 900,
        modal: true,
        closeOnEscape: true,
        title: "Seleccionar Nota de crédito a aplicar",
        buttons: {
            Aceptar: function () {
                var res = [];
                $("div[id^='c__']").each(function () {<?php /* para cada consorcio con facturas */ ?>
                    var strid = $(this).attr('id');
                    var id = strid.replace('c__', '');
                    totxconsor[id]['anc'] = 0;<?php /* inicializo en cero los totales */ ?>
                    res[id] = restante(id);<?php /* 19/12/2019 para comparar el total seleccionado de notas de credito con lo restante total del consorcio, sino restante(id) va cambiando con cada NC y no da la cuenta cuando hay mas de 1 NC seleccionada */ ?>
                });
                var totpc = 0;
                var tt = 0;
                var importe = 0;
                $("#listanca input").each(function () {
                    importe = $(this).val();
                    if (isNaN(parseFloat(importe)) || parseFloat(importe) < 0) {
                        $(this).val(0);
                    } else {
                        var id = $(this).data('cid');
                        if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                            if (parseFloat(totxconsor[id]['anc']) + parseFloat(importe) > res[id]) {
                                tt = id;<?php /* el importe a transferir es mayor al restante, lo cambio por cero y aviso */ ?>
                                return false;
                            }
                            totxconsor[id]['anc'] += parseFloat(importe);
                            totpc += parseFloat(importe);
                        }
                    }
                });
                if (tt !== 0) {
                    alert('El importe a aplicar de las Notas de Crédito es mayor al restante para el Consorcio "' + consorcios[tt] + '"');
                    return false;
                }
                $("#notasdecredito").val(parseFloat(totpc).toFixed(2));
                recalculaTodo();
                dialogNC.dialog("close");
            }
        },
        /*close: function () {
         }*/
    });
    var dialogpnc = $("#dnc").dialog({
        autoOpen: false, height: "auto", width: "900", maxWidth: "900",
        position: {at: "center top"},
        closeOnEscape: false,
        modal: true,
        buttons: {
            Cerrar: function () {
                dialogpnc.dialog("close");
            }
        }
    });
</script>
<?= "<div id='dnc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el pago de comprobantes  ?>
<div id="dialogNClista" title="Notas de crédito a utilizar" style="display:block">
    <?php
    if (isset($notasdecreditoaaplicar) && count($notasdecreditoaaplicar) != 0) {// muestro los cheques pendientes (si existen)
        echo "<div class='info'>Haga click en la descripción de la Nota de crédito para aplicarla</div><br>";
        echo "<div id='listanca'>";
        foreach ($notasdecreditoaaplicar as $k => $v) {
            echo "<div class='inline' data-cid='" . $v['Liquidation']['consorcio_id'] . "'>" . $this->Form->input('proveedorsfactura_id', ['type' => 'number', 'min' => 0, 'value' => 0, 'div' => false, 'label' => false, 'name' => 'data[' . $v['Liquidation']['consorcio_id'] . '][anc][' . $v['Proveedorsfactura']['id'] . ']', 'id' => 'ncaa_' . $v['Proveedorsfactura']['id'], 'data-val' => $v['Proveedorsfactura']['saldo'], 'data-cid' => $v['Liquidation']['consorcio_id'],
                'form' => 'guardarcobranza', 'style' => 'width:120px']) . "&nbsp;<span style='cursor:pointer' onclick='javascript:" . '$("#ncaa_' . $v['Proveedorsfactura']['id'] . '").val(parseFloat("' . $v['Proveedorsfactura']['saldo'] . '")).change()' . "'>" . h($consorcio_id[$v['Liquidation']['consorcio_id']]) . " #" . h($v['Proveedorsfactura']['numero']) . " - " . $this->Time->format(__('d/m/Y'), $v['Proveedorsfactura']['fecha']) . " - Total: " . $this->Functions->money($v['Proveedorsfactura']['importe']) . " - Saldo: " . $this->Functions->money($v['Proveedorsfactura']['saldo']) . "</span>";
            echo "<span onclick='$(\"#dnc\").dialog(\"open\");$(\"#dnc\").load(\"" . $this->webroot . "proveedorsfacturas/view2/" . $v['Proveedorsfactura']['id'] . "\");$(\"#dnc\").focus()' class='imgmove' style='background-image:url(" . $this->webroot . "img/icon-info.png);background-repeat:no-repeat;width:14px;height:14px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<div class='info'>No se encuentran Notas de cr&eacute;dito para aplicar</div><br>";
    }
    ?>
</div>
