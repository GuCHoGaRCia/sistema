<?php
// si tiene cuenta bancaria de la administracion, me devuelve el ID, sino cero
//$cuentabancariaadministracion = reset($bancoscuentas)['Bancoscuenta']['consorcio_id'] === '0' ? reset($bancoscuentas)['Bancoscuenta']['id'] : 0;
$cheq = "<b>" . __('Cheques propios') . " (<span id='cantchp'></span>)" . "</b> " . $this->Html->image('new.png', ['alt' => __('Agregar'), 'title' => __('Agregar'), 'style' => 'width:20px;height:20px;cursor:pointer', 'id' => 'crearchequeprop']);
$cheq .= "&nbsp;";
$cheq .= $this->Html->image('view.png', ['title' => __('Ver detalle'), 'id' => 'seleccChequeprop', 'style' => 'width:20px;height:20px;cursor:pointer']);
echo $this->JqueryValidation->input('chequep', ['label' => $cheq, 'type' => 'number', 'readonly' => 'readonly', 'id' => 'totalchequeprop', 'value' => 0, 'min' => 0, 'form' => 'guardarcobranza']);
?>
<script>
    $("#crearchequeprop").on("click", function () {
        $("#ChequespropioC").val($("#CobranzaConcepto").val());
        muestraCuentasBancarias();
        dialogAddChequeprop.dialog("open");
    });
    $("#seleccChequeprop").on("click", function () {
        dialogSelChequeprop.dialog("open");
    });
    $(document).on('change', '#ChequespropioCpb', function () {
        if (parseFloat($(this).val()) in bancoadm) {<?php /* tiene cuenta bancaria de administracion y la seleccionó */ ?>
            $("#selChpAdm").show();
            $("#ChequespropioCpi").val(0);
            $("#ChequespropioCpi").prop('readonly', true);
        } else {
            $("#selChpAdm").hide();
            $("#ChequespropioCpi").prop('readonly', false);
        }
    });
<?php
// mostrarChequesP muestra los Cheques propios y de admasociados a los Consorcios de las facturas seleccionadas (o pago a cuenta) cuyo monto sea >0
?>
    function mostrarChequesP() {
        $("#listachequepinfo div").each(function () {
            $(this).hide();
        });
        var cantchp = 0;
        $("#listachequepinfo div[data-cid='0']").each(function () {<?php /* muestro cheques propios de la administracion (consorcio cero) si TODOS los consorcios asociados tienen facturas seleccionadas */ ?>
            var muestra = true;
            $(this).find("input[class^='dchpadm_']").each(function () {
                var strid = $(this).prop('class');
                var id = strid.replace('dchpadm_', '');
                if (typeof $("#totalfacturas_" + id).html() === "undefined" || parseFloat($("#totalfacturas_" + id).html()) === 0) {<?php /* no tiene facturas seleccionadas, no muestro el cheque propio de adm */ ?>
                    muestra = false;
                    return false;
                }
            });
            if (muestra) {
                cantchp++;<?php /* sumo solo los cheques de adm q tienen todos los consorcios con fact seleccionadas */ ?>
                $(this).show();
            }
        });
        $("div[id^='c__']").each(function () {
            var strid = $(this).prop('id');
            var id = strid.replace('c__', '');
            if (typeof $("#totalfacturas_" + id).html() === "undefined" || parseFloat($("#totalfacturas_" + id).html()) > 0) {
                $("#listachequepinfo div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").prop('disabled', false);
                    $(this).show();
                    cantchp++;
                });
            } else {
                $("#listachequepinfo div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").prop('disabled', true);<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
                });
            }
        });
        $("#cantchp").html(cantchp);
    }
<?php
echo "var cuentasbancarias = {";
if (!empty($bancoscuentas)) {
    $temp = [];
    foreach ($bancoscuentas as $k => $v) {// para multicuenta bancaria (cheque propio x ejemplo)
        $temp[$v['Bancoscuenta']['consorcio_id']][] = ['id' => $v['Bancoscuenta']['id'], 'nombre' => $v['Bancoscuenta']['name2']];
    }
    foreach ($temp as $k => $s) {
        echo $k . ":{";
        foreach ($s as $v) {
            echo $v['id'] . ":'" . $v['nombre'] . "',";
        }
        echo "},";
    }
}
echo "};";
?>
    function muestraCuentasBancarias() {
        $("#ChequespropioCpb").empty().append('<option value="">Seleccione Cuenta bancaria...</option>');
        $.each(bancoadm, function (k, v) {
            $("#ChequespropioCpb").append('<option value="' + k + '">' + v + '</option>').val("");
        });
        $("#divdialogselChpAdm div:not(:first)").each(function () {<?php /* Oculto los consorcios en chp de administracion */ ?>
            $(this).hide();
        });
        $("div[id^='c__']").each(function () {
            var strid = $(this).prop('id');
            console.log(strid);
            var id = strid.replace('c__', '');
            var d = cuentasbancarias[id];
            if (parseFloat($("#totalfacturas_" + id).html()) > 0 && typeof d !== "undefined") {<?php /* Si tiene pagos y cuenta bancaria del consorcio */ ?>
                $("#divdialogselChpAdm div[data-cid='chpadm" + id + "']").show();
                $.each(d, function (k, v) {
                    $('#ChequespropioCpb').append($('<option>', {
                        value: k.toString(),
                        text: v.toString()
                    }));
                });

            } else {
                $("#divdialogselChpAdm div[data-cid='chpadm" + id + "']").hide();
            }
        });
    }
    function parseCp(msg) {
        try {
            var obj = JSON.parse(msg);
            if (obj['r'] === 0) {
                alert(obj['e']);
                return;
            }
            var cad = "<div class='inline' data-cid='" + obj['e']['Bancoscuenta'].consorcio_id + "' id='delchp_" + obj['e']['Chequespropio'].id + "'>" + "<input type='checkbox' data-val='" + parseFloat(obj['e']['Chequespropio'].importe) + "' onclick='clickChP('" + obj['e']['Chequespropio'].id + ")' data-cid='" + obj['e']['Bancoscuenta'].consorcio_id + "' name='data[" + obj['e']['Bancoscuenta'].consorcio_id + "][chequepropio][" + obj['e']['Chequespropio'].id + "]' id='lchp_" + obj['e']['Chequespropio'].id + "' data-cid='" + obj['e']['Bancoscuenta'].consorcio_id + "' form='guardarcobranza'/>" +
                    "&nbsp;&nbsp;$ " + obj['e']['Chequespropio'].importe + " ChP #" + obj['e']['Chequespropio'].numero + " - " + obj['e']['Bancoscuenta'].name + " - " + obj['e']['Chequespropio'].concepto +
                    "<span class='imgmove' onclick='delchp(" + obj['e']['Chequespropio'].id + "," + parseFloat(obj['e']['Chequespropio'].importe) + ")' style='background-image:url(<?= $this->webroot ?>img/drop.png);background-repeat:no-repeat;width:16px;height:16px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            cad += "</div>";
            $(cad).appendTo("#listachequepinfo");
            recalculaTodo();
        } catch (e) {
            alert(e);
            return;
        }

        alert('<?= __('El cheque propio fue guardado correctamente') ?>');
    }
    function parseCpAdm(msg) {
        try {
            var obj = JSON.parse(msg);
            if (obj['r'] === 0) {
                alert(obj['e']);
                return;
            }
            var cad = "<div class='inline' data-cid='0' id='delchpadm_" + obj['e']['Chequespropiosadm'].id + "'><input type='checkbox' data-val='" + parseFloat(obj['e']['Chequespropiosadm'].importe) + "' data-cid='0000' id='lchpadm_" + obj['e']['Chequespropiosadm'].id + "' onclick='clickChPAdm('" + obj['e']['Chequespropiosadm'].id + ")' data-cid='" + obj['e']['Chequespropiosadmsdetalle'].consorcio_id +
                    "' form='guardarcobranza'/>&nbsp;&nbsp;$ " + obj['e']['Chequespropiosadm'].importe + " ChPAdm #" + obj['e']['Chequespropiosadm'].numero + " - " + obj['e']['Chequespropiosadm'].concepto +
                    "<span class='imgmove' onclick='delchpadm(" + obj['e']['Chequespropiosadm'].id + "," + parseFloat(obj['e']['Chequespropiosadm'].importe) + ")' style='background-image:url(<?= $this->webroot ?>img/drop.png);background-repeat:no-repeat;width:16px;height:16px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            if (Object.keys(obj['e']['Chequespropiosadm']).length > 0) {
                cad += "<img src='<?= $this->webroot ?>img/view.png' style='cursor:pointer;width:16px' title='Ver detalle por Consorcio' onclick='alert($(\"#detchpadm_" + obj['e']['Chequespropiosadm'].id + "\").html())' />";
                cad += "<div style='display:none' id='detchpadm_" + obj['e']['Chequespropiosadm'].id + "'>";
                var inputs = "";
                $.each(obj['e']['Chequespropiosadmsdetalle'], function (k, v) {<?php /* muestro el detalle X adm de los CHP q sean de ADM */ ?>
                    if (v.importe > 0) {<?php /* muestro el detalle de los consorcios con importe mayores a cero */ ?>
                        cad += consorcios[v['Bancoscuenta'].consorcio_id] + ": " + parseFloat(v.importe).toFixed(2) + "\n<br>";
                        inputs += "<input type='hidden' class='dchpadm_" + v['Bancoscuenta'].consorcio_id + "' name='data[" + v['Bancoscuenta'].consorcio_id + "][chpadm][" + obj['e']['Chequespropiosadm'].id + "][" + v.bancoscuenta_id + "]' value='" + parseFloat(v.importe).toFixed(2) + "' form='guardarcobranza'/>";
                    }
                });
                cad += "</div>" + inputs;
            }

            cad += "</div>";
            $(cad).appendTo("#listachequepinfo");
            mostrarChequesP();
            totxconsorchp();
            calculaRestantexConsorcio();
            calcula();
        } catch (e) {
            alert(e);
            return;
        }

        alert('<?= __('El cheque propio fue guardado correctamente') ?>');
    }
    var butt = {};
<?php /* si no tiene cuenta bancaria, muestro un cartel y quito el boton Guardar (dejo el cancelar) */ ?>
<?php
if (!empty($bancoscuentas)) {
    ?>
        butt['Guardar'] = function () {
            var f1 = $("#ChequespropioCpfe").val();
            var f2 = $("#ChequespropioCpfv").val();
            var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
            var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
            if (x > y) {
                alert('<?= __('La Fecha Emisión debe ser menor o igual a la Fecha Vencimiento') ?>');
                return false;
            }
            if ($("#ChequespropioCpc").val() === "") {
                alert('<?= __('Debe ingresar un concepto') ?>');
                return false;
            }
            if ($("#ChequespropioCpb").val() === "") {
                alert('<?= __('Debe seleccionar una Cuenta bancaria') ?>');
                return false;
            }
            if (!parseFloat($("#ChequespropioCpi").val())) {
                alert('<?= __('Debe ingresar el importe') ?>');
                return false;
            }
            if ($("#ChequespropioCpn").val() === "") {
                alert('<?= __('Debe ingresar el número') ?>');
                return false;
            }
            var id = 0;
            $.each(cuentasbancarias, function (k, v) {<?php /* a partir de la cuenta bancaria obtengo el consorcio asociado */ ?>
                if (Object.keys(v).toString() === $("#ChequespropioCpb").val()) {
                    id = k;
                    return false;
                }
            });
            if (id !== 0 && parseFloat($("#ChequespropioCpi").val()) > parseFloat($("#txcr_" + id).html())) {<?php /* el importe del Chp es mayor al restante, aviso */ ?>
                alert('<?= __('El importe del Cheque propio es mayor al importe restante a abonar') ?>');
                return false;
            }
            fe = f1.substr(6, 4) + "-" + f1.substr(3, 2) + "-" + f1.substr(0, 2);
            fv = f2.substr(6, 4) + "-" + f2.substr(3, 2) + "-" + f2.substr(0, 2);
            var dd = {fe: fe, fv: fv, c: $("#ChequespropioCpc").val(), n: $("#ChequespropioCpn").val(), i: $("#ChequespropioCpi").val(), b: $("#ChequespropioCpb").val()/*, badm: bancoadm*/};<?php /* en el server comparo b==badm para saber si es cheque de adm.NO! Las busco ahi directamente */ ?>
            var d = {};
            var seccion = "Chequespropios";
            if (parseFloat($("#ChequespropioCpb").val()) in bancoadm) {<?php /* es un Chequepropio de administracion */ ?>
                $("input[id^='chpadm_']").each(function () {
                    var strid = $(this).prop('id');<?php /* verifico q las transferencias desde administracion no superen lo restante */ ?>
                    var idc = strid.replace('chpadm_', '');
                    if (parseFloat($("#totalfacturas_" + idc).html()) > 0 && typeof cuentasbancarias[idc] !== "undefined") {<?php /* si tiene facturas para pagar */ ?>
                        d[Object.keys(cuentasbancarias[idc]).toString()] = parseFloat($(this).val()).toFixed(2);
                    }
                });
                seccion = "Chequespropiosadms";
            } else {
                d[id] = parseFloat($("#ChequespropioCpi").val());<?php /* en id tengo el consorcio asociado a la cuenta bancaria, ver el $.each de mas arriba */ ?>
            }
            dd.d = d;
            $.ajax({type: "POST", url: "<?= $this->webroot ?>" + seccion + "/agregar", cache: false, data: dd}).done(function (msg) {
                //$("#test").html("<pre>" + msg + "</pre>");
                //$("input[id^='chpadm_']").each(function () {
                //    var strid = $(this).prop('id');<?php /* verifico q las transferencias desde administracion no superen lo restante */ ?>
                //    var id = strid.replace('chpadm_', '');
                //    if (!isNaN(parseFloat($("#totalfacturas_" + id).html())) && parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                //        totxconsor[id]['chpadm'] += parseFloat($(this).val());
                //    }
                //});
                if (seccion === "Chequespropios") {
                    parseCp(msg);
                } else {
                    parseCpAdm(msg);
                }
                $("#ChequespropioCpfv").val('<?= date('d/m/Y') ?>');
                $("#ChequespropioCpfe").val('<?= date('d/m/Y') ?>');
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo agregar el cheque. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo agregar el cheque propio");
                }
            });
            dialogAddChequeprop.dialog("close");
        };
    <?php
}
?>
    butt['Cancelar'] = function () {
        totxconsorchp();
        calculaRestantexConsorcio();
        calcula();
        if (typeof ($("#agregarcheque")[0]) !== "undefined") {
            $("#agregarcheque")[0].reset();
        }
        dialogAddChequeprop.dialog("close");
    };
    dialogAddChequeprop = $("#dialogChequeprop").dialog({
        autoOpen: false,
        width: 700,
        modal: true,
        title: "Crear cheque propio",
        buttons: butt,
        close: function () {
            if (typeof ($("#agregarcheque")[0]) !== "undefined") {
                $("#agregarcheque")[0].reset();
            }
            //var date = new Date().toLocaleDateString("es-ES");
            //$("#ChequespropioCpfv").val(date);
            //$("#ChequespropioCpfe").val(date);
            $("input[id^='chpadm_']").each(function () {
                $(this).val(0);<?php /* Inicializo en cero todos los ChpAdm, sino la prox q abre tienen valor puesto */ ?>
                //$(this).prop('disabled', false);
            });
            $("#ChequespropioCpb").val("").trigger("change");
            $("#ChequespropioCpn").val('');
            $("#ChequespropioCpi").val(0);
        }
    });
    dialogSelChequeprop = $("#dialogChequeproplistacheques").dialog({
        autoOpen: false,
        width: 700,
        modal: true,
        title: "Ver detalle cheques propios",
        open: function (event, ui) {
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        buttons: {
            Aceptar: function () {
                dialogSelChequeprop.dialog("close");
            },
        },
        close: function () {
            totxconsorchp();
            calculaRestantexConsorcio();
            calcula();
            if (typeof ($("#agregarcheque")[0]) !== "undefined") {
                $("#agregarcheque")[0].reset();
            }
        }
    });
    $("#selChpAdm").on("click", function () {
        dialogselChpAdm.dialog("open");
    });
    dialogselChpAdm = $("#divdialogselChpAdm").dialog({
        autoOpen: false,
        width: 800,
        height: "auto",
        modal: true,
        title: "Ver detalle Cheque Propio Administración",
        closeOnEscape: false,
        open: function (event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        },
        buttons: {
            Aceptar: function () {
                var tt = 0;
                var ttx = 0;
<?php /*
 * inicializo en cero los totales de transf administracion ACA.
 * Lo hago aca porq si selecciono transferencia, y acepto, y vuelvo a abrir y acepto, ya tengo cargado el importe a transferir, 
 * entonces va a superar lo restante (ya esta calculado). Entonces pongo en cero, me fijo si no supera, y ahi guardo en totxconsor el total
 */ ?>
                $("input[id^='chpadm_']").each(function () {
                    var strid = $(this).prop('id');<?php /* inicializo en cero los totales de transf administracion ACA. */ ?>
                    var id = strid.replace('chpadm_', '');
                    totxconsor[id]['chpadm'] = 0;
                });
                $("input[id^='chpadm_']").each(function () {
                    var strid = $(this).prop('id');<?php /* verifico q las transferencias desde administracion no superen lo restante */ ?>
                    var id = strid.replace('chpadm_', '');

                    if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                        $(this).css('border', '1px solid rgb(170, 170, 170)');
                        if ($(this).val() === "" || parseFloat($(this).val()) < 0) {
                            tt = -1;
                            $(this).css('border', '2px solid red');
                            return false;
                        }

                        if (parseFloat($(this).val()) > parseFloat(restante(id))) {<?php /* el importe a transferir es mayor al restante, lo cambio por cero y aviso */ ?>
                            tt = -2;
                            return false;
                        }
                        totxconsor[id]['chpadm'] += parseFloat($(this).val());
                        ttx += parseFloat($(this).val());
                    }
                    $(this).trigger("change");
                });
                if (tt === -1) {
                    alert('<?= __('Los importes deben ser cero o mayores a cero') ?>');
                    return false;
                }
                if (tt === -2) {
                    alert('<?= __('El importe del Cheque propio es mayor al importe restante del Consorcio') ?>');
                    return false;
                }
                $("#ChequespropioCpi").val(parseFloat(ttx).toFixed(2));
                dialogselChpAdm.dialog("close");
            }
        }
    });
</script>
<div id="dialogChequeprop" title="Agregar cheque propio" style="display:block">
    <div class="cheques form">
        <?php
        if (!empty($bancoscuentas)) {
            ?>
            <?php echo $this->Form->create('Chequespropio', ['class' => 'jquery-validation', 'id' => 'agregarcheque']); ?>
            <fieldset>
                <p class="error-message">* Campos obligatorios</p>
                <?php
                echo "<div class='inline'>";
                echo $this->JqueryValidation->input('cpfe', ['label' => __('Fecha emisión') . ' *', 'type' => 'text', 'style' => 'width:90px', 'class' => 'dp', 'value' => date("d/m/Y")]);
                echo $this->JqueryValidation->input('cpfv', ['label' => __('Fecha vencimiento') . ' *', 'type' => 'text', 'style' => 'width:90px', 'class' => 'dp', 'value' => date("d/m/Y")]);
                echo $this->JqueryValidation->input('cpc', ['label' => __('Concepto') . ' *', 'value' => isset($concepto) ? 'ChP ' . $concepto : 'ChP ']);
                echo $this->JqueryValidation->input('cpb', ['label' => __('Cuenta bancaria') . ' *', 'type' => 'select', 'width:auto']);
                echo $this->JqueryValidation->input('cpn', ['label' => __('Número cheque') . ' *', 'style' => 'width:200px']);
                echo $this->JqueryValidation->input('cpi', ['label' => __('Importe') . ' *', 'type' => 'number', 'min' => 0, 'step' => 0.01]) . $this->Html->image('view.png', ['title' => __('Ver detalle'), 'id' => 'selChpAdm', 'style' => 'width:20px;height:20px;cursor:pointer;display:none']);
                echo "</div>";
                ?>
            </fieldset>
            <?php echo $this->Form->end(); ?>
            <?php
        } else {
            echo "<div class='info'>No se pueden realizar transferencias o cheques propios porque el Consorcio no posee una Cuenta bancaria asociada</div>";
        }
        ?>
    </div>
</div>
<div id="divdialogselChpAdm" title="Detalle Cheque Propio Administracion" style="display:block">
    <?php
    if (isset($consorcio_id) && count($consorcio_id) != 0) {// muestro los consorcios
        echo "<div class='info'>Ingrese el importe del Cheque Propio de Administracion de cada Consorcio</div><br>";
        foreach ($consorcio_id as $k => $v) {
            echo "<div class='inline' data-cid='chpadm" . $k . "'" . ">";
            echo $this->Form->input('chpadm', ['type' => 'number', 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'id' => 'chpadm_' . $k, 'data-cid' => $k, 'value' => 0.00,
                'style' => 'width:120px']);
            echo "&nbsp;<span class='hand' onClick='completar2($(\"#chpadm_" . $k . "\")," . $k . ")'><<&nbsp;</span>$v";
            echo "</div>";
        }
    } else {
        echo "<div class='info'>No se encuentran Consorcios</div>";
    }
    ?>
</div>
<div id="dialogChequeproplistacheques" title="Cheques propios disponibles" style="display:block">
    <?php
    echo '<div class="info">Cheques propios disponibles</div><br>';
    echo "<div id='listachequepinfo'>";
    if (isset($chequespropios) && count($chequespropios) != 0) {// muestro los cheques pendientes (si existen)
        foreach ($chequespropios as $k => $v) {
            echo "<div class='inline' " . (isset($v['Bancoscuenta']['consorcio_id']) ? "data-cid='" . $v['Bancoscuenta']['consorcio_id'] . "'" : "") . " id='delchp_" . $v['Chequespropio']['id'] . "'>";
            echo $this->Form->input('cheque_id', ['type' => 'checkbox', 'onclick' => 'clickChP(' . $v['Chequespropio']['id'] . ');', 'data-val' => $v['Chequespropio']['importe'], 'div' => false, 'label' => false, 'name' => $v['Bancoscuenta']['consorcio_id'] == 0 ? '' : 'data[' . $v['Bancoscuenta']['consorcio_id'] . '][chequepropio][' . $v['Chequespropio']['id'] . ']', 'id' => 'lchp_' . $v['Chequespropio']['id'], 'data-cid' => $v['Bancoscuenta']['consorcio_id'],
                'form' => 'guardarcobranza']) . "&nbsp;&nbsp;$ " . $v['Chequespropio']['importe'] . " ChP #" . $v['Chequespropio']['numero'] . " - " . $v['Bancoscuenta']['name'] . " - " . $v['Chequespropio']['concepto'];
            echo "<span class='imgmove' onclick='delchp(" . $v['Chequespropio']['id'] . "," . $v['Chequespropio']['importe'] . ")' style='background-image:url(" . $this->webroot . "img/drop.png);background-repeat:no-repeat;width:16px;height:16px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            echo "</div>";
        }
    }
    if (isset($chequespropiosadm) && count($chequespropiosadm) != 0) {// muestro los cheques pendientes (si existen)
        foreach ($chequespropiosadm as $k => $v) {
            echo "<div class='inline' data-cid='0' id='delchpadm_" . $v['Chequespropiosadm']['id'] . "' >";
            echo $this->Form->input('cheque_id', ['type' => 'checkbox', 'data-val' => $v['Chequespropiosadm']['importe'], 'div' => false, 'label' => false, 'id' => 'lchpadm_' . $v['Chequespropiosadm']['id'], 'onclick' => 'clickChPAdm(' . $v['Chequespropiosadm']['id'] . ')', 'data-cid' => '0000', 'form' => 'guardarcobranza', /* 'name' => $v['Bancoscuenta']['consorcio_id'] == 0 ? '' : 'data[' . $v['Bancoscuenta']['consorcio_id'] . '][chequepropioadm][' . $v['Chequespropiosadm']['id'] . ']', */]);
            echo "&nbsp;&nbsp;$ " . $v['Chequespropiosadm']['importe'] . " ChPAdm #" . $v['Chequespropiosadm']['numero'] . " - " . $v['Chequespropiosadm']['concepto'];
            echo "<span class='imgmove' onclick='delchpadm(" . $v['Chequespropiosadm']['id'] . "," . $v['Chequespropiosadm']['importe'] . ")' style='background-image:url(" . $this->webroot . "img/drop.png);background-repeat:no-repeat;width:16px;height:16px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            if (isset($v['Chequespropiosadmsdetalle']) && count($v['Chequespropiosadmsdetalle']) > 0) {
                echo "<img src='" . $this->webroot . "img/view.png' style='cursor:pointer;width:16px' title='Ver detalle Cheque Propio de Administracion por Consorcio' onclick='alert($(\"#detchpadm_" . $v['Chequespropiosadm']['id'] . "\").html())' />";
                echo "<div style='display:none' id='detchpadm_" . $v['Chequespropiosadm']['id'] . "'>";
                $inputs = "";
                foreach ($v['Chequespropiosadmsdetalle'] as $jj) {// la clase dchpadm_ es para el total x consorcio de los chp de adm
                    if ($jj['importe'] > 0) {
                        $inputs .= "<input type='hidden' class='dchpadm_" . $jj['Bancoscuenta']['consorcio_id'] . "' name='data[" . $jj['Bancoscuenta']['consorcio_id'] . "][chpadm][" . $v['Chequespropiosadm']['id'] . "][" . $jj['bancoscuenta_id'] . "]' value='" . $jj['importe'] . "' form='guardarcobranza' />";
                        echo h($consorcio_id[$jj['Bancoscuenta']['consorcio_id']] . ": " . $jj['importe']) . "\n<br>";
                    }
                }
                echo "</div>$inputs";
            }
            echo "</div>";
        }
    }
    echo "</div>";
    //echo "<div class='inline' id='listachequestotal' title='$tch' style='display:none;border-top:2px solid gray'>$ " . $this->Functions->money($tch) . " - Total cheques</div>";
    ?>
</div>
<script>
    function delchp(n, i) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Chequespropios/delChequepropio", cache: false, data: {id: n}}).done(function (msg) {
            if (msg === "true") {
                $("#delchp_" + n).remove();
                mostrarChequesP();
                totxconsorchp();
                calculaRestantexConsorcio();
                calcula();
            } else {
                alert("No se pudo eliminar el cheque propio");
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo eliminar el cheque propio. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo eliminar el cheque propio");
            }
        });
    }
    function delchpadm(n, i) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Chequespropiosadms/delChequepropio", cache: false, data: {id: n}}).done(function (msg) {
            if (msg === "true") {
                $("#delchpadm_" + n).remove();
                mostrarChequesP();
                totxconsorchp();
                calculaRestantexConsorcio();
                calcula();
            } else {
                alert("No se pudo eliminar el cheque propio de Administracion");
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo eliminar el cheque propio de Administracion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo eliminar el cheque propio de Administracion");
            }
        });
    }
    function totxconsorchp() {<?php /* suma los totales de cheques propios x consorcio (para mostrar total y restante en forma de pago) */ ?>
        $("#totalchequeprop").val(parseFloat(0).toFixed(2));
        $("div[id^='c__']").each(function () {<?php /* inicializo en cero los totales de chequespropios y chpadm */ ?>
            var strid = $(this).prop('id');
            var id = strid.replace('c__', '');
            totxconsor[id]['chp'] = 0;
            totxconsor[id]['chpadm'] = 0;
        });
        $("div[id^='c__']").each(function () {<?php /* para cada consorcio con facturas */ ?>
            var strid = $(this).prop('id');
            var id = strid.replace('c__', '');
            if (parseFloat($("#totalfacturas_" + id).html()) > 0) {<?php /* si seleccionó algun importe a pagar */ ?>
                $("#listachequepinfo div[data-cid='" + id + "'] input[id^='lchp_']").each(function () {<?php /* para cada cheque propio */ ?>
                    if ($("#" + $(this).prop('id')).is(':checked')) {
                        totxconsor[id]['chp'] += parseFloat($(this).data('val'));
                    }
                });
                /*$(".dchpadm_" + id).each(function () {<?php /* para cada cheque propio de adm del Consorcio id */ ?>
                 totxconsor[id]['chpadm'] += parseFloat($(this).val());
                 });*/


            }
        });
        $("#listachequepinfo div[data-cid='0']").each(function () {
            var muestra = true;<?php /* SUMO cheques propios de la administracion (consorcio cero) si TODOS los consorcios asociados tienen facturas seleccionadas */ ?>
            $(this).find("input[class^='dchpadm_']").each(function () {
                var strid = $(this).prop('class');
                var id = strid.replace('dchpadm_', '');
                if (parseFloat($("#totalfacturas_" + id).html()) == 0) {<?php /* no tiene facturas seleccionadas, no SUMO el cheque propio de adm */ ?>
                    muestra = false;
                    return false;
                }
            });
            if (muestra) {<?php /* el ChPAdm se muestra, entonceso agrego a los totales el detalle de cada consorcio */ ?>
                $(this).find("input:checkbox").each(function () {
                    if ($(this).is(':checked')) {
                        var strid1 = $(this).prop('id');
                        var id = strid1.replace('lchpadm_', '');
                        $("#delchpadm_" + id + " input[type='hidden']").each(function () {
                            var strid2 = $(this).prop('class');
                            if (strid2 !== "") {<?php /* $this->Form->input me crea un hidden aparte del q estoy creando, y no tiene definido class HDP! */ ?>
                                var id2 = strid2.replace('dchpadm_', '');
                                totxconsor[id2]['chpadm'] += parseFloat($(this).val());
                            }
                        });
                    }
                });
            }
        });
    }
    function clickChP(chid) {
        var cid = $("#lchp_" + chid).data('cid');
        if ($("#lchp_" + chid).prop('checked')) {<?php /* esta tildando un cheque propio */ ?>
            if (parseFloat($("#lchp_" + chid).data('val')) > parseFloat(restante(cid))) {<?php /* cuando tilda actualizo, si destilda solo cambio el total de cheques propios */ ?>
                alert('<?= __('El importe seleccionado es mayor al importe restante a abonar del Consorcio') ?> ' + consorcios[cid]);
                $("#lchp_" + chid).prop('checked', false);
            } else {
                totxconsor[cid]['chp'] += parseFloat($("#lchp_" + chid).data('val'));
                $("#lchp_" + chid).prop('checked', true);
            }
        } else {
            totxconsor[cid]['chp'] -= parseFloat($("#lchp_" + chid).data('val'));
            $("#lchp_" + chid).prop('checked', false);
        }
    }
    function clickChPAdm(chid) {
        if ($("#lchpadm_" + chid).prop('checked')) {<?php /* esta tildando un cheque propio de administracion */ ?>
            $("#delchpadm_" + chid + " input[type='hidden']").each(function () {<?php /* Son los importes x consorcio del ChpAdm */ ?>
                var strid2 = $(this).prop('class');
                if (strid2 !== "") {
                    var id2 = strid2.replace('dchpadm_', '');
                    if (parseFloat($(this).val()) > parseFloat(restante(id2))) {
                        alert('<?= __('El importe seleccionado es mayor al importe restante a abonar del Consorcio') ?> ' + consorcios[id2]);
                        $("#lchpadm_" + chid).prop('checked', false);
                    } else {
                        totxconsor[id2]['chpadm'] = parseFloat($(this).val()).toFixed(2);
                    }
                }
            });
        } else {
            $("#delchpadm_" + chid + " input[type='hidden']").each(function () {<?php /* Son los importes x consorcio del ChpAdm */ ?>
                var strid2 = $(this).prop('class');
                if (strid2 !== "") {
                    var id2 = strid2.replace('dchpadm_', '');
                    totxconsor[id2]['chpadm'] -= parseFloat($(this).val()).toFixed(2);
                }
            });
        }
    }
</script>