<?php
echo $this->JqueryValidation->input('efectivo', ['label' => __('Efectivo') . " " . $this->Html->image('view.png', ['title' => __('Ver detalle'), 'id' => 'seleccEfectivo', 'style' => 'width:20px;height:20px;cursor:pointer']),
    'type' => 'number', 'readonly' => 'readonly', 'id' => 'efectivo', 'value' => '0.00', 'min' => 0, 'max' => $max, 'form' => 'guardarcobranza']);
?>
<div id="dialogEfectivo" style="display:block;padding-top:12px">
    <?php
    echo "<span class='info'>Ingrese el importe en efectivo a utilizar de cada Consorcio. Saldo Caja: $ $max</span><br><br>";
    foreach ($bancoscuentas as $k => $v) {
        if ($v['Bancoscuenta']['consorcio_id'] === '0') {// data-bid: cuando pone la lupa en transferencias adm, muestro solo las de la cuenta de administracion seleccionada (x si tiene mas de una)
            echo "<div class='inline' data-cid='ef0'>" . $this->Form->input('eadm', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'readonly' => 'readonly', 'div' => false, 'label' => false, 'id' => 'eadm_' . $v['Bancoscuenta']['id'], 'value' => '0.00',
                'style' => 'width:120px']) . "&nbsp;" . $this->Html->image('view.png', ['title' => __('Ver detalle'), 'data-bid' => $v['Bancoscuenta']['id'], 'class' => 'selEfAdm', 'style' => 'width:20px;height:20px;cursor:pointer']) . "&nbsp;" . $v['Bancoscuenta']['name2'] /* . " - <b>Saldo: <span style='color:" . ($v['Bancoscuenta']['saldo'] >= 0 ? 'green' : 'red') . "'>" . $v['Bancoscuenta']['saldo'] */ . "</span></b>";
            echo "</div>";
        }
    }
    if (isset($consorcio_id) && count($consorcio_id) != 0) {// muestro los consorcios
        foreach ($consorcio_id as $k => $v) {
            echo "<div class='inline' ";
            echo "data-cid='be" . $k . "'" . ">" . $this->Form->input('bc', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'name' => 'data[' . $k . '][efectivo]',
                'id' => 'ef_' . $k, 'value' => '0.00', 'data-cid' => $k, 'form' => 'guardarcobranza', 'style' => 'width:120px']);
            echo "&nbsp;<span class='hand' onClick='completar2($(\"#ef_$k\"),$k)'><<&nbsp;</span>";
            echo "&nbsp;" . $v . "</b>";
            echo "</div>";
        }
    } else {
        echo "<div class='info'>No se encuentran Cajas asociadas</div>";
    }
    ?>
</div>
<div id="divdialogselEfAdm" style="display:block">
    <?php
    if (isset($bancoscuentas) && count($bancoscuentas) != 0) {// muestro las cuentas
        echo "<div class='info'>Ingrese el importe en Efectivo desde Administracion de cada Cuenta Bancaria</div><br>";
        foreach ($bancoscuentas as $k1 => $v1) {
            if ($v1['Bancoscuenta']['consorcio_id'] == 0) {// es una cuenta de ADM
                echo "<div id='deteadm_" . $v1['Bancoscuenta']['id'] . "'>";
                foreach ($consorcio_id as $k => $v) {
                    if ($k != 0) {// muestro todas las cuentas q no sean de ADM
                        echo "<div class='inline' data-cid='eadm" . $k . "'" . ">";
                        echo $this->Form->input('eadm', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'form' => 'guardarcobranza', 'style' => 'width:120px', 'value' => '0.00',
                            'name' => "data[" . $k . "][efectivoadm][" . $v1['Bancoscuenta']['id'] . "]", 'id' => 'eadm_' . $k . "_" . $v1['Bancoscuenta']['id'], 'data-cid' => $k]);
                        echo "&nbsp;<span class='hand' onClick='completar2($(\"#eadm_" . $k . "_" . $v1['Bancoscuenta']['id'] . "\")," . $k . ")'><<&nbsp;</span>";
                        echo "&nbsp;" . $v . "</b>";
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
    function mostrarEfectivo() {
        $("#dialogEfectivo div").each(function () {
            $(this).hide();
        });
        $("#divdialogselEfAdm[id^='deteadm_']").each(function () {<?php /* Oculto las cuentas en efectivo de administracion */ ?>
            $("div").each(function () {
                $(this).hide();
            });
        });
        $("span[id^='totalfacturas_']").each(function () {
            var xx = parseFloat($(this).html());
            var dcid = $(this).attr('id');
            if (parseFloat(xx) > 0) {
                $("#dialogEfectivo div[data-cid='be" + dcid.replace('totalfacturas_', '') + "']").show();<?php /* Para cada consorcio con importe a abonar > 0 muestro sus cajas */ ?>
                $("#divdialogselEfAdm div[data-cid='eadm" + dcid.replace('totalfacturas_', '') + "']").show();
            } //else {
            //$("#dialogEfectivo div[data-cid='be" + dcid.replace('totalfacturas_', '') + "']").val(0);<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
            //}
        });
        $("#dialogEfectivo div[data-cid='ef0']").show();
    }
    $("#seleccEfectivo").on("click", function () {
        mostrarEfectivo();
        dialogseleccEfectivo.dialog("open");
    });
    dialogseleccEfectivo = $("#dialogEfectivo").dialog({
        autoOpen: false,
        width: 650,
        height: "auto",
        modal: true,
        title: "Ver detalle efectivo",
        closeOnEscape: false,
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        },
        buttons: {
            Aceptar: function () {
                var tt = 0;
                $("input[id^='ef_']").each(function () {
                    $(this).css('border', '1px solid rgb(170, 170, 170)');
                    if ($(this).val() === "" || parseFloat($(this).val()) < 0) {
                        tt = -1;
                        $(this).css('border', '2px solid red');
                        return false;
                    }
                });
                if (tt === -1) {
                    alert('<?= __('Los importes en efectivo deben ser cero o mayores a cero') ?>');
                    return false;
                }
                var efe = 0;
                $("div[id^='c__']").each(function () {<?php /* para cada consorcio con facturas */ ?>
                    var strid = $(this).attr('id');
                    var id = strid.replace('c__', '');
                    if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                        totxconsor[id]['e'] = 0;<?php /* inicializo en cero los totales de efect: DEJARLO sino se cierra la ventana efectivo sin hacer el chequeo */ ?>
                        efe = parseFloat($("#ef_" + id).val()).toFixed(2);
                        if (parseFloat(efe) > 0 && parseFloat(efe) > parseFloat(restante(id))) {
                            tt = id;<?php /* el importe a transferir es mayor al restante, lo cambio por cero y aviso */ ?>
                            return false;
                        }
                    }
                });
                if (tt !== 0) {
                    alert('El importe en efectivo (' + parseFloat(efe).toFixed(2) + ') es mayor al restante (' + parseFloat(restante(tt)).toFixed(2) + ') para el Consorcio "' + consorcios[tt] + '"');
                    return false;
                }
                recalculaTodo();
                dialogseleccEfectivo.dialog("close");
            }
        }
    });
    $(".selEfAdm").on("click", function () {
        var idb = $(this).data('bid');
        $.each(bancoadm, function (k, v) {
            if (parseFloat(k) === parseFloat(idb)) {
                $("#deteadm_" + k).show();
                $("#deteadm_" + k + " div").each(function () {
                    var strid = $(this).data('cid');
                    var id = strid.replace('eadm', '');
                    if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $("#deteadm_" + k).hide();
            }
        });
        dialogselEfAdm.dialog("open");
    });
    dialogselEfAdm = $("#divdialogselEfAdm").dialog({
        autoOpen: false,
        width: 800,
        height: "auto",
        modal: true,
        title: "Ver detalle Efectivo Administración",
        closeOnEscape: false,
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        },
        buttons: {
            Aceptar: function () {
                var error = false;
                $.each(bancoadm, function (k, v) {
                    var ctabco = k;
                    var tt = 0;
                    var ttx = 0;
                    var ttxx = 0;
                    var consorid = 0;
                    var val = 0;
                    $("#deteadm_" + k + " div").each(function () {
                        ttx = 0;
<?php /* verifico q el efectivo desde administracion no superen lo restante */ ?>
                        var strid = $(this).data('cid');
                        consorid = strid.replace('eadm', '');
<?php /*
 * inicializo en cero los totales de efectivo administracion ACA.
 */ ?>
                        totxconsor[consorid]['eadm'][ctabco] = 0;
                        if (parseFloat($("#totalfacturas_" + consorid).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                            $(this).find("input").css('border', '1px solid rgb(170, 170, 170)');
                            val = $(this).find("input").val();
                            if (val === "" || parseFloat(val) < 0) {
                                $(this).find("input").css('border', '2px solid red');
                                $(this).find("input").val('0.00');
                                tt = -1;
                                return false;
                            }
                            if (parseFloat(val) > restante(consorid)) {<?php /* el importe en efectivo es mayor al restante, lo cambio por cero y aviso */ ?>
                                $(this).find("input").css('border', '2px solid red');
                                $(this).find("input").val('0.00');
                                tt = -2;
                                return false;
                            }
                            ttx = parseFloat(val);
                        }
                        ttxx += ttx;
                        totxconsor[consorid]['eadm'][ctabco] = ttx;
                    });
                    if (tt === -1) {
                        alert('<?= __('Los importes en Efectivo deben ser cero o mayores a cero') ?>');
                        error = true;
                        return false;
                    }
                    if (tt === -2) {
                        alert('El importe en Efectivo desde Administración  (' + parseFloat(val).toFixed(2) + ') es mayor al restante (' + parseFloat(restante(consorid)).toFixed(2) + ') para el Consorcio "' + consorcios[consorid] + '"');
                        error = true;
                        return false;
                    }
                    $("#eadm_" + k).val(ttxx);<?php /* Es el input del total de efectivo de cada cta adm */ ?>
                });
                if (!error) {
                    recalculaTodo();
                    dialogselEfAdm.dialog("close");
                }
            }
        }
    });
    function totxconsore() {<?php /* suma los totales de efectivo x consorcio (para mostrar total y restante en forma de pago) */ ?>
        /*$("div[id^='c__']").each(function () {<?php /* inicializo en cero los totales de efectivo */ ?>
         var strid = $(this).attr('id');
         var id = strid.replace('c__', '');
         totxconsor[id]['e'] = 0;
         });*/
        $("div[id^='c__']").each(function () {<?php /* para cada consorcio con facturas */ ?>
            var strid = $(this).attr('id');
            var id = strid.replace('c__', '');
            totxconsor[id]['e'] = 0;//nueva
            if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                if (parseFloat($("#ef_" + id).val()) > 0) {<?php /* si seleccionó algun importe en efectivo */ ?>
                    var to1 = parseFloat($("#ef_" + id).val());
                    totxconsor[id]['e'] = to1;
                }
            }
        });
    }
    function totxconsoreadm() {<?php /* suma los totales de efectivo administracion (para mostrar total y restante en forma de pago) */ ?>
        $.each(bancoadm, function (k, v) {
            //var ctabco = k;
            //var tt = 0;
            var ttx = 0;
            var ttxx = 0;
            $("#deteadm_" + k + " div").each(function () {
                ttx = 0;
                var strid = $(this).data('cid');
                var consorid = strid.replace('eadm', '');
                totxconsor[consorid]['eadm'][k] = 0;
                if (parseFloat($("#totalfacturas_" + consorid).html()) > 0) {
                    var val = $(this).find("input").val();
                    ttx = parseFloat(val);
                }
                ttxx += ttx;
                totxconsor[consorid]['eadm'][k] = ttx;
            });
        });
    }
</script>