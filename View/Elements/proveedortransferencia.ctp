<?php
echo $this->JqueryValidation->input('transferencia', ['label' => __('Transferencia') . " " . $this->Html->image('view.png', ['title' => __('Ver detalle'), 'id' => 'seleccCuentasbancarias', 'style' => 'width:20px;height:20px;cursor:pointer']),
    'type' => 'number', 'readonly' => 'readonly', 'id' => 'totaltransferencia', 'min' => 0, 'value' => '0.00', 'form' => 'guardarcobranza']);
?>
<div id="dialogTransferencia" style="display:block">
    <?php
    if (isset($bancoscuentas) && count($bancoscuentas) != 0) {// muestro las cuentas bancarias y un input para ingresar importe a transferir de cada cuenta
        echo "<div class='info'>Ingrese el importe a transferir desde cada Cuenta bancaria</div><br>";
        $trid = 0;
        foreach ($bancoscuentas as $k => $v) {
            //debug($v);
            if ($v['Bancoscuenta']['consorcio_id'] === '0') {// data-bid: cuando pone la lupa en transferencias adm, muestro solo las de la cuenta de administracion seleccionada (x si tiene mas de una)
                echo "<div class='inline' data-cid='bc0'>" . $this->Form->input('ttadm', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'readonly' => 'readonly', 'div' => false, 'label' => false, 'id' => 'ttadm_' . $v['Bancoscuenta']['id'], 'value' => '0.00',
                    'style' => 'width:120px']) . "&nbsp;" . $this->Html->image('view.png', ['title' => __('Ver detalle'), 'data-bid' => $v['Bancoscuenta']['id'], 'class' => 'selTransfAdm', 'style' => 'width:20px;height:20px;cursor:pointer']) . "&nbsp;" . $v['Bancoscuenta']['name2'] . " - <b>Saldo: <span style='color:" . ($v['Bancoscuenta']['saldo'] >= 0 ? 'green' : 'red') . "'>" . $v['Bancoscuenta']['saldo'] . "</span></b>";
                echo "</div>";
            } else {
                echo "<div class='inline' ";
                echo (isset($v['Bancoscuenta']['consorcio_id']) ? "data-cid='bc" . $v['Bancoscuenta']['consorcio_id'] . "'" : "") . ">";
                echo $this->Form->input('bc', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'name' => 'data[' . $v['Bancoscuenta']['consorcio_id'] . '][transferencia][' . $v['Bancoscuenta']['id'] . ']',
                    'id' => 'tr_' . $v['Bancoscuenta']['consorcio_id'] . "_" . $v['Bancoscuenta']['id'], 'data-cid' => $v['Bancoscuenta']['consorcio_id'], 'value' => '0.00',
                    'form' => 'guardarcobranza', 'style' => 'width:120px']);
                echo "&nbsp;<span class='hand' onClick='completar2($(\"#tr_" . $v['Bancoscuenta']['consorcio_id'] . "_" . $v['Bancoscuenta']['id'] . "\")," . $v['Bancoscuenta']['consorcio_id'] . ")'><<&nbsp;</span>";
                echo $v['Bancoscuenta']['name2'] . " - <b>Saldo: <span style='color:" . ($v['Bancoscuenta']['saldo'] >= 0 ? 'green' : 'red') . "'>" . $v['Bancoscuenta']['saldo'] . "</span></b>";
                echo "</div>";
            }
        }
    } else {
        echo "<div class='info'>No se encuentran Cuentas bancarias</div>";
    }
    ?>
</div>
<div id="divdialogselTransfAdm" style="display:block">
    <?php
    //debug($bancoscuentas);
    if (isset($bancoscuentas) && count($bancoscuentas) != 0) {// muestro las cuentas
        echo "<div class='info'>Ingrese el importe a Transferir desde Administracion de cada Cuenta Bancaria</div><br>";
        foreach ($bancoscuentas as $k1 => $v1) {
            if ($v1['Bancoscuenta']['consorcio_id'] == 0) {// es una cuenta de ADM
                echo "<div id='dettadm_" . $v1['Bancoscuenta']['id'] . "'>";
                foreach ($bancoscuentas as $k => $v) {
                    if ($v['Bancoscuenta']['consorcio_id'] != 0) {// muestro todas las cuentas q no sean de ADM
                        echo "<div class='inline' data-cid='tadm" . $v['Bancoscuenta']['consorcio_id'] . "'" . ">"; // id =consorcio_id, cta bancaria adm, cta bancaria consorcio
                        echo $this->Form->input('tadm', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'form' => 'guardarcobranza', 'style' => 'width:120px', 'value' => '0.00',
                            'name' => "data[" . $v['Bancoscuenta']['consorcio_id'] . "][transferenciaadm][" . $v1['Bancoscuenta']['id'] . "][" . $v['Bancoscuenta']['id'] . "]", 'id' => 'tadm_' . $v['Bancoscuenta']['consorcio_id'] . "_" . $v1['Bancoscuenta']['id'] . "_" . $v['Bancoscuenta']['id'], 'data-xx' => $v1['Bancoscuenta']['id'], 'data-cid' => $v['Bancoscuenta']['id']]);
                        echo "&nbsp;<span class='hand' onClick='completar2($(\"#tadm_" . $v['Bancoscuenta']['consorcio_id'] . "_" . $v1['Bancoscuenta']['id'] . "_" . $v['Bancoscuenta']['id'] . "\")," . $v['Bancoscuenta']['consorcio_id'] . ")'><<&nbsp;</span>";
                        echo "&nbsp;" . $v['Bancoscuenta']['name2'] . "</b>";
                        echo "</div>";
                    }
                }
                echo "</div>";
            }
        }
    } else {
        echo "<div class='info'>No se encuentran Cuentas Bancarias</div>";
    }
    ?>
</div>
<script>
    function mostrarTransferencia() {
        $("#dialogTransferencia div:not(:first)").each(function () {
            $(this).hide();
        });
        $("#divdialogselTransfAdm[id^='dettadm_']").each(function () {<?php /* Oculto las cuentas en transf de administracion */ ?>
            $("div").each(function () {
                $(this).hide();
            });
        });
        $("span[id^='totalfacturas_']").each(function () {
            var xx = parseFloat($(this).html());
            var dcid = $(this).attr('id');
            if (parseFloat(xx) > 0) {
                $("#dialogTransferencia div[data-cid='bc" + dcid.replace('totalfacturas_', '') + "']").show();<?php /* Para cada consorcio con importe a abonar > 0 muestro sus Ctas bancarias */ ?>
                $("#divdialogselTransfAdm div[data-cid='tadm" + dcid.replace('totalfacturas_', '') + "']").show();
            } else {
                $("#dialogTransferencia div[data-cid='bc" + dcid.replace('totalfacturas_', '') + "']").val(parseFloat(0).toFixed(2));<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
                $("#divdialogselTransfAdm div[data-cid='tadm" + dcid.replace('totalfacturas_', '') + "']").val(parseFloat(0).toFixed(2));
            }
        });
        $("#dialogTransferencia div[data-cid='bc0']").show();
    }
    $("#seleccCuentasbancarias").on("click", function () {
        mostrarTransferencia();
        dialogSelTransferencia.dialog("open");
    });
    dialogSelTransferencia = $("#dialogTransferencia").dialog({
        autoOpen: false,
        width: 800,
        height: "auto",
        modal: true,
        title: "Ver detalle transferencia",
        closeOnEscape: false,
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        },
        buttons: {
            Aceptar: function () {
                var tt = 0;
                $("input[id^='tr_']").each(function () {
                    $(this).css('border', '1px solid rgb(170, 170, 170)');
                    if ($(this).val() === "" || parseFloat($(this).val()) < 0) {
                        tt = -1;
                        $(this).css('border', '2px solid red');
                        return false;
                    }
                });
                if (tt === -1) {
                    alert('<?= __('Los importes a transferir deben ser cero o mayores a cero') ?>');
                    return false;
                }
                var tt = 0;
                var atransferir = 0;
                $("input[id^='tr_']").each(function () {
                    var strid = $(this).attr('id');
                    var id = strid.replace('tr_', '');
                    var res = id.split("_");
                    if (parseFloat($("#totalfacturas_" + res[0]).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                        totxconsor[res[0]]['t'] = 0;<?php /* inicializo en cero los totales de transf */ ?>
                        atransferir = parseFloat($(this).val()) /*+ parseFloat($("#tadm_" + id).val())*/;
                        if (atransferir > 0 && atransferir > restante(res[0])) {
                            $(this).css('border', '2px solid red');
                            $(this).val(parseFloat(0).toFixed(2));
                            tt = res[0];<?php /* el importe a transferir es mayor al restante, lo cambio por cero y aviso */ ?>
                            return false;
                        }
                    }
                });
                if (tt !== 0) {
                    alert('El importe a transferir (' + parseFloat(atransferir).toFixed(2) + ') es mayor al restante (' + parseFloat(restante(tt)).toFixed(2) + ') para el Consorcio "' + consorcios[tt] + '"');
                    return false;
                } else {
                    recalculaTodo();
                    dialogSelTransferencia.dialog("close");
                }
            }
        }
    });

    $(".selTransfAdm").on("click", function () {
        var idb = $(this).data('bid');
        $.each(bancoadm, function (k, v) {
            if (parseFloat(k) === parseFloat(idb)) {
                $("#dettadm_" + k).show();
                $("#dettadm_" + k + " div").each(function () {
                    var strid = $(this).data('cid');
                    var id = strid.replace('tadm', '');
                    if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $("#dettadm_" + k).hide();
            }
        });
        dialogselTransfAdm.dialog("open");
    });
    dialogselTransfAdm = $("#divdialogselTransfAdm").dialog({
        autoOpen: false,
        width: 800,
        height: "auto",
        modal: true,
        title: "Ver detalle Transferencia Administración",
        closeOnEscape: false,
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        },
        buttons: {
            Aceptar: function () {
                //totxconsort();
                //totxconsortadm();
                var error = false;
                $.each(bancoadm, function (k, v) {
                    var ctabco = k;
                    var tt = 0;
                    var ttx = 0;
                    var ttxx = 0;
                    var consorid = 0;
                    var val = 0;
                    $("#dettadm_" + k + " div").each(function () {
                        ttx = 0;
<?php /* verifico q las transferencias desde administracion no superen lo restante */ ?>
                        var strid = $(this).data('cid');
                        consorid = strid.replace('tadm', '');
<?php /*
 * inicializo en cero los totales de transf administracion ACA.
 * Lo hago aca porq si selecciono transferencia, y acepto, y vuelvo a abrir y acepto, ya tengo cargado el importe a transferir, 
 * entonces va a superar lo restante (ya esta calculado). Entonces pongo en cero, me fijo si no supera, y ahi guardo en totxconsor el total
 */ ?>
                        totxconsor[consorid]['tadm'][ctabco] = 0;
                        if (parseFloat($("#totalfacturas_" + consorid).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                            $(this).find("input").css('border', '1px solid rgb(170, 170, 170)');
                            val = $(this).find("input").val();
                            if (val === "" || parseFloat(val) < 0) {
                                $(this).find("input").css('border', '2px solid red');
                                $(this).find("input").val('0.00');
                                tt = -1;
                                return false;
                            }
                            if (parseFloat(val) > restante(consorid)) {<?php /* el importe a transferir es mayor al restante, lo cambio por cero y aviso */ ?>
                                $(this).find("input").css('border', '2px solid red');
                                $(this).find("input").val('0.00');
                                tt = -2;
                                return false;
                            }
                            ttx = parseFloat(val);
                        }
                        ttxx += ttx;
                        totxconsor[consorid]['tadm'][ctabco] = ttx;
                    });
                    if (tt === -1) {
                        alert('<?= __('Los importes a transferir deben ser cero o mayores a cero') ?>');
                        error = true;
                        return false;
                    }
                    if (tt === -2) {
                        alert('El importe en Transferencia desde Administración (' + parseFloat(val).toFixed(2) + ') es mayor al restante (' + parseFloat(restante(consorid)).toFixed(2) + ') para el Consorcio "' + consorcios[consorid] + '"');
                        error = true;
                        return false;
                    }
                    $("#ttadm_" + k).val(parseFloat(ttxx).toFixed(2));
                });
                if (!error) {
                    recalculaTodo();
                    dialogselTransfAdm.dialog("close");
                }
            }
        }
    });
    function totxconsort() {<?php /* suma los totales de transferencias x consorcio (para mostrar total y restante en forma de pago) */ ?>
        $("div[id^='c__']").each(function () {<?php /* para cada consorcio con facturas */ ?>
            var strid = $(this).attr('id');
            var id = strid.replace('c__', '');
            totxconsor[id]['t'] = 0;
            /*if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
             if (parseFloat($("#tr_" + id).val()) > 0) {<?php /* si seleccionó algun importe a transferir */ ?>
             var to1 = parseFloat($("#tr_" + id).val());
             totxconsor[id]['t'] = to1;
             }
             }*/
        });
        $("input[id^='tr_']").each(function () {
            var strid = $(this).attr('id');
            var id = strid.replace('tr_', '');
            var res = id.split("_");
            if (parseFloat($("#totalfacturas_" + res[0]).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                totxconsor[res[0]]['t'] += parseFloat($(this).val());
            }
        });
    }
    function totxconsortadm() {<?php /* suma los totales de transf administracion (para mostrar total y restante en forma de pago) */ ?>
        $.each(bancoadm, function (k, v) {
            var ctabco = k;
            var ttx = 0;
            var ttxx = 0;
            $("#dettadm_" + k + " div").each(function () {
                ttx = 0;
                var strid = $(this).data('cid');
                var consorid = strid.replace('tadm', '');
                totxconsor[consorid]['tadm'][ctabco] = 0;
                if (parseFloat($("#totalfacturas_" + consorid).html()) > 0) {
                    var val = $(this).find("input").val();
                    ttx = parseFloat(val);
                }
                ttxx += ttx;
                totxconsor[consorid]['tadm'][ctabco] = ttx;
            });
        });
    }
</script>