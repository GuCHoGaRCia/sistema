<?php
/* if (empty($saldos['facturas'])) {
  ?>
  <div class="info">No existen facturas pendientes de pago para el Proveedor seleccionado. Puede realizar un pago a cuenta</div>
  <?php
  } */

// me fijo si el cliente tiene Cuenta bancaria de Administracion
//debug($bancoscuentas);
$bancoadm = [];
$keys = $this->Functions->find($bancoscuentas, ['consorcio_id' => '0'], true);
if (!empty($keys)) {
    foreach ($keys as $j) {
        $bancoadm[$bancoscuentas[$j]['Bancoscuenta']['id']] = $bancoscuentas[$j]['Bancoscuenta']['name2'];
    }
}
?>
<br>
<div id="tabs" style="height: auto;min-height:420px">
    <ul>
        <li><a href="#tabs-1">Facturas pendientes</a></li>
        <li><a href="#tabs-2" onclick="check();">Formas de pago</a></li>
    </ul>
    <div id="tabs-1">
        <div class="cobranzasform">
            <?php echo $this->Form->create('Proveedorspago', ['class' => 'jquery-validation', 'url' => ['controller' => 'Proveedorspagos', 'action' => 'add'], 'id' => 'guardarcobranza']); ?>
            <fieldset>
                <p class="error-message">* Campos obligatorios - <a href="#" onclick='javascript:$("#pid").val("<?= $proveedor['Proveedor']['id'] ?>"); $("#dfechascc").dialog("open")'>Ver Cuenta corriente Proveedor</a></p>
                <?php
                echo $this->JqueryValidation->input('proveedor_id', ['value' => $proveedor['Proveedor']['id'], 'type' => 'hidden']);
                echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *', 'tabindex' => 1, 'style' => 'width:368px', 'value' => 'PP ' . $proveedor['Proveedor']['name']]);
                echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:95px', 'tabindex' => 2, 'value' => date("d/m/Y")]);
                echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Seleccione Consorcio...') . ' *', 'options' => ['-1' => 'Consorcios con Facturas pendientes', $consorcio_id], 'empty' => '', 'multiple' => 'multiple'));
                ?>
                <br>
                <div id="contlistado">
                    <ul id="contlistado2" style="list-style-type:none;padding-left:0px !important">
                    </ul>                     
                </div>
            </fieldset>
        </div>
    </div>
    <div id="tabs-2">
        <?php echo $this->element('proveedorformasdepago'); ?>
        <div class='inline'>
            <?php echo $this->Form->end(['id' => 'guardarc', 'label' => __('Guardar')]); ?>
            <img src="<?= $this->webroot ?>img/loading.gif" id="loadingX" />
        </div>
    </div>
</div>
<script>
    $("#tabs").tabs({height: "auto"});<?php /* dejar esto primero asi carga mas rapido la pagina */ ?>
    $("#loadingX").css('display', 'none');
    $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});

    $("#ProveedorspagoConsorcioId").select2({language: "es", placeholder: "Seleccione Consorcio..."});
    var listados = [];
    var tildotodos = 0;
<?php
echo "var bancoadm = {";
foreach ($bancoadm as $k => $v) {
    echo "$k:'$v',";
}
echo "};";
echo "var consorcios = {";
foreach ($consorcio_id as $k => $v) {
    echo "$k:'$v',";
}
echo "};";
echo "var totxconsor = {";
foreach ($consorcio_id as $k => $v) {
    echo "$k:{'e':0,'eadm':{";
    foreach ($bancoadm as $kx => $vx) {
        echo "$kx:0,";
    }
    echo "},";
    echo "'chp':0,'chpadm':0,'t':0,'cht':0,'apc':0,'anc':0,'tadm':{";
    foreach ($bancoadm as $kx => $vx) {
        echo "$kx:0,";
    }
    echo "}},";
}
echo "};";
?>
    function recalculaTodo() {
        mostrarEfectivo();
        mostrarChequesP();
        mostrarTransferencia();
        mostrarChequesT();
        mostrarPAC();
        mostrarNCAA();
        totxconsore();
        totxconsoreadm();
        totxconsorchp();
        totxconsort();
        totxconsortadm();
        totxconsorcht();
        totxconsorpac();
        calculaRestantexConsorcio();
        calcula();
        recalcular();
        $(":input[type=number]").each(function () {
            $(this).val(parseFloat($(this).val()).toFixed(2));
        });
    }
    function check() {<?php /* si tildo consorcios y selecciono facturas, recalculo todo, sino deshabilito el tab "Formas de pago" */ ?>
        var t = false;
        if ($("#ProveedorspagoConsorcioId").find('option:selected').length > 0) {
            $("span[id^='totalfacturas_']").each(function () {
                var xx = parseFloat($(this).html());
                if (parseFloat(xx) > 0) {
                    t = true;
                }
            });
        }
        if (!t) {
            $('#tabs').tabs("option", "disabled", [1]);
            alert("Debe seleccionar al menos una Factura o Pago a cuenta");
            return;
        }
        recalculaTodo();
    }
    $(function () {
        $("#ProveedorspagoConcepto").focus();
        $("#ProveedorspagoFechaDay").select2({language: "es"});
        $("#ProveedorspagoFechaMonth").select2({language: "es"});
        $("#ProveedorspagoFechaYear").select2({language: "es"});
        function getData(c, todos) {
            if (todos === 1) {
                $("#contlistado2").html("");<?php /* si tildó algun consorcio y despues TODOS, borro lo que haya y cargo el listado de todos */ ?>
            }
            if ($("#c__" + c).length !== 0) {<?php /* si ya fue cargado el consorcio, no hago nada (ej: elije "Todos" y despues uno que ya esta en "todos", no lo vuelve a agregar a la lista */ ?>
                return;
            }
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedorsfacturas/getFacturas", cache: false, data: {p: <?= $proveedor['Proveedor']['id'] ?>, c: c}}).done(function (msg) {
                try {
                    var obj = JSON.parse(msg);
                    if (!$.isEmptyObject(obj)) {
                        var cad = "";
                        var cid = -1;
                        $.each(obj, function (k, v) {
                            if (cid !== v['Consorcio']['id']) {
                                if (cid !== -1) {
                                    cad += "</div></div>";
                                }
                                cad += "<div id='c__" + v['Consorcio']['id'] + "' style='border:1px dashed black;padding:2px;margin-top:4px'><h5 style='text-align:left;font-weight:bold'>" + v['Consorcio']['name'] + " - <b>Total: $ <span id='totalfacturas_" + v['Consorcio']['id'] + "'>0.00</span></b></h5>";
                                cad += "<input style='width:100px' name='data[" + v['Consorcio']['id'] + "][pac]' type='number' id='pagoacuenta_" + v['Consorcio']['id'] + "' value='0.00' min='0' step='0.01' style='width:90px;margin-top:2px' onchange='recalcular(" + v['Consorcio']['id'] + ");'/>&nbsp;&nbsp;Pago a cuenta";
                                cad += "<div class='inline'>";
                                cid = v['Consorcio']['id'];
                            }

                            var style = v['Proveedorsfactura']['saldo'] === v['Proveedorsfactura']['importe'] ? 'color:red;' : 'color:orange;';
                            var fecha = v['Proveedorsfactura']['fecha'].substr(8, 2) + "/" + v['Proveedorsfactura']['fecha'].substr(5, 2) + "/" + v['Proveedorsfactura']['fecha'].substr(0, 4); //
                            cad += "<input style='width:100px' name='data[" + v['Consorcio']['id'] + "][fac][" + v['Proveedorsfactura']['id'] + "]" + v['Proveedorsfactura']['id'] + "]' type='number' id='" + "f_" + v['Consorcio']['id'] + "_" + v['Proveedorsfactura']['id'] + "' value='0.00' min='0' max='" + v['Proveedorsfactura']['saldo'] + "' step='0.01' style='width:90px;margin-top:2px' onchange='recalcular(" + v['Consorcio']['id'] + ");' />";
                            cad += "<span style='" + style + "line-height:13px;color:$color'>&nbsp;<b><span class='hand' onClick='completar(\"" + v['Proveedorsfactura']['saldo'] + "\",\"" + v['Proveedorsfactura']['id'] + "\",\"" + v['Consorcio']['id'] + "\")'><<&nbsp;</span></b>";
                            cad += fecha + " Num: " + v['Proveedorsfactura']['numero'] + " " + v['Proveedorsfactura']['concepto'] + " - Importe: " + v['Proveedorsfactura']['importe'] + " - Saldo: " + v['Proveedorsfactura']['saldo'] + "</span><br>";
                        });
                        cad += "</div></div>";
                        $("#contlistado2").append(cad);
                        $("#enviar").show();
                    } else {
                        if (c === '-1') {
                            $("#contlistado2").html("<div class='info'>No se encuentran Consorcios con Facturas pendientes de pago</div>");
                            $("#tabs").tabs({disabled: false});
                        } else {
                            //$("#contlistado2").append('<div style="border:1px dashed black;padding:2px;margin-top:4px" id="c' + c + '">No se encontraron facturas pendientes para el Consorcio ' + consorcios[c] + '<br>' + "<div class='inline'><input style='width:100px' name='data[" + c + "][pagoacuenta][" + c + "]' type='number' id='pagoacuenta_" + c + "' value='0.00' min='0' step='0.01' style='width:90px;margin-top:2px' />&nbsp;&nbsp;Pago a cuenta</div></div>");
                            cad = "<div id='c__" + c + "' style='border:1px dashed black;padding:2px;margin-top:4px'><h5 style='text-align:left;font-weight:bold'>" + consorcios[c] + " - <b>Total: $ <span id='totalfacturas_" + c + "'>0.00</span></b> <span style='color:green'>[ Sin facturas pendientes ]</span></h5>";
                            cad += "<input style='width:100px' name='data[" + c + "][pac]' type='number' id='pagoacuenta_" + c + "' value='0.00' min='0' step='0.01' style='width:90px;margin-top:2px' onchange='recalcular(" + c + ");'/>&nbsp;&nbsp;Pago a cuenta";
                            cad += "</div></div>";
                            $("#contlistado2").append(cad);
                            $("#tabs").tabs({disabled: false});<?php /* si selecciona un consor y no tiene facturas pendientes, y elige Pago a cuenta, habilito Formas de pago */ ?>
                        }
                    }
                } catch (err) {
                    //
                }
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo obtener el Listado de Proveedores. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo obtener el Listado de Proveedores");
                }
            });
        }

        $("#ProveedorspagoConsorcioId").on('select2:select', function (e) {
            $("#tabs").tabs({disabled: false});
            if (tildotodos === 0) {
                getData(e.params.data.id, e.params.data.id === "-1" ? 1 : 0);<?php /* -1 es TODOS LOS CONSORCIOS */ ?>
                tildotodos = (e.params.data.id === "-1" ? 1 : 0);
            } else {
                return false;
            }
        });
        $("#ProveedorspagoConsorcioId").on('select2:unselect', function (e) {
            $("#tabs").tabs({disabled: false});
            $("#c__" + e.params.data.id).remove();
            $(".todos").remove();
            recalcular(e.params.data.id);
            tildotodos = 0;
            if (e.params.data.id === '-1') {<?php /* esta quitando TODOS los consorcios, vacio la lista */ ?>
                $("#contlistado2").html("");
                $("#ProveedorspagoConsorcioId").select2('val', '');
            }
        });
        $("#guardarc").click(function () {
            if ($("#CobranzaAdd2Concepto").val() === "") {
                alert('<?= __("Debe ingresar un concepto") ?>');
                return false;
            }
            if ($("#montototal").val() === "0.00") {
                alert('<?= __("El Monto a pagar debe ser mayor a cero") ?>');
                return false;
            }
            /*if (parseFloat($("#efectivo").val()) > parseFloat(<?= $caja['Caja']['saldo_pesos'] ?>)) {
             alert('<?= __("La caja no posee saldo suficiente para pagar en efectivo") ?>');
             return false;
             }*/

            var m1 = parseFloat($("#efectivo").val()) + parseFloat($("#totalchequeprop").val()) + parseFloat($("#totaltransferencia").val()) + parseFloat($("#totalchequeterc").val()) + parseFloat($("#pagosacuenta").val()) + parseFloat($("#notasdecredito").val());
            if (parseFloat(montototal).toFixed(2) != parseFloat(m1).toFixed(2)) {
                alert("El importe a pagar es  " + m1 + ". No puede pagar menos del 'Monto a pagar' (" + montototal + ")");
                return false;
            }
            calcula();

            var quedarestante = false;
            $("span[id^='txc_']").each(function () {<?php /* Verifico que no haya saldo restante sin asignar forma de pago */ ?>
                var strid = $(this).prop('id');
                var cid = strid.replace('txc_', '');
                if (parseFloat($("#txcr_" + cid).html()) > 0) {
                    quedarestante = true;
                }
            });
            if (quedarestante) {
                alert("Existen montos restantes a pagar sin forma de pago asignada");
                return false;
            }

            var conceptos = '\nMonto a pagar: $ ' + $("#montototal").val() + '\nEfectivo: $ ' + $("#efectivo").val() + '\nCheques propios: $ ' + $("#totalchequeprop").val() + '\nTransferencia: $' + $("#totaltransferencia").val();
            conceptos += '\nCheques terceros: $' + $("#totalchequeterc").val() + '\nAplicar pago a cuenta: $ ' + $("#pagosacuenta").val() + '\nAplicar nota de crédito: $ ' + $("#notasdecredito").val();
            if (confirm('<?= __("Desea guardar la siguiente cobranza?") ?>' + conceptos)) {<?php /* Antes de enviar borro los datos q no se usen para q no se envien en cero. Las facturas, transferencias y totales en cero */ ?>
                $("#guardarc").attr('disabled', true);
                $("input[id^='f_']").each(function () {<?php /* No envio las facturas q no esté pagando */ ?>
                    if (parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);

                    }
                });
                $("input[id^='ef_']").each(function () {<?php /* No envio los pagos efectivos q no utilice para este pago (o esten en cero) */ ?>
                    var cid = $(this).data('cid');
                    if (parseFloat($("#txc_" + cid).html()) === 0 || parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='eadm_']").each(function () {<?php /* No envio efectivo desde administracion q no utilice para este pago */ ?>
                    if (parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='lchp_']").each(function () {<?php /* No envio los cheques propios q no utilice para este pago */ ?>
                    if (!$(this).is(':checked')) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='lchpadm_']").each(function () {<?php /* No envio los cheques propios de adm q no utilice para este pago */ ?>
                    if (!$(this).is(':checked')) {
                        $(this).prop('disabled', true);
                        var strid = $(this).prop('id');
                        var cid = strid.replace('lchpadm_', '');
                        $("#lchpadm_" + cid + "_").prop('disabled', true);
                        $("#delchpadm_" + cid + " input:hidden").each(function () {
                            $(this).prop('disabled', true);
                        });
                    }
                });
                $("input[id^='tr_']").each(function () {<?php /* No envio las cuentas bancarias de transferencias q no utilice para este pago */ ?>
                    var cid = $(this).data('cid');
                    if (parseFloat($("#txc_" + cid).html()) === 0 || parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='tadm_']").each(function () {<?php /* No envio las transferencias desde administracion q no utilice para este pago */ ?>
                    if (parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='lcht_']").each(function () {<?php /* No envio los cheques de terceros q no utilice para este pago */ ?>
                    if (!$(this).is(':checked')) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='pagoacuenta_']").each(function () {<?php /* No envio los pagos a cuenta q esten en CERO (en facturas) */ ?>
                    if (parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='pca_']").each(function () {<?php /* No envio los pagos a cuenta para aplicar q no se hayan utilizado */ ?>
                    if (!$(this).is(':checked')) {
                        $(this).prop('disabled', true);
                    }
                });
                $("input[id^='ncaa_']").each(function () {<?php /* No envio las notas de credito q esten en CERO */ ?>
                    if (parseFloat($(this).val()) === 0) {
                        $(this).prop('disabled', true);
                    }
                });
                $("#montototal").prop('disabled', true);
                $("#efectivo").prop('disabled', true);
                $("#totalchequeprop").prop('disabled', true);
                $("#totaltransferencia").prop('disabled', true);
                $("#totalchequeterc").prop('disabled', true);
                $("#pagosacuenta").prop('disabled', true);
                $("#notasdecredito").prop('disabled', true);
                $("#ProveedorspagoConsorcioId_").prop('disabled', true);
                $("#ProveedorspagoConsorcioId").prop('disabled', true);
                $("#loadingX").css('display', 'inline-block !important').show();
                $.ajax({
                    type: "POST",
                    url: "<?= $this->webroot ?>Proveedorspagos/add",
                    data: $("#guardarcobranza").serialize(),
                }).done(function (msg) {
                    //$("#test").html("<pre>" + msg + "</pre>");
                    try {
                        var obj = JSON.parse(msg);
                        if (obj.e === 1) {
                            alert(obj.d);
                        } else {
                            window.location.replace("<?= $this->webroot ?>Proveedorspagos");
                        }
                    } catch (err) {
                        //
                    }
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion, intente nuevamente");
                    }
                });
                $(':input').removeAttr("disabled");
                $(':checkbox').removeAttr("disabled");
            }
            $("#guardarc").attr('disabled', false);
            $("#loadingX").css('display', 'none !important');
            return false;
        });
    });
    function completar(x, y, cid) {<?php /* Completo el total a abonar con el saldo de la factura */ ?>
        $("#f_" + cid + "_" + y).val(parseFloat(x).toFixed(2));
        recalcular(cid);
    }
    function completar2(obj, consor) {<?php /* Completo el val del input obj con el restante del consorcio y recalculo TODO */ ?>
        var jk = restante(consor);
        if (parseFloat(jk) > 0) {
            $(obj).val(parseFloat(jk) + parseFloat($(obj).val()));
        } else {
            //    alert("El importe a asignar es mayor al restante");
        }
        recalculaTodo();
    }
    function totalpagosacuenta() {<?php /* calculo el total de pagos a cuenta */ ?>
        var pagoacuenta = 0;
        $("input[id^='pagoacuenta_']").each(function () {
            pagoacuenta += parseFloat($(this).val());
        });
        return pagoacuenta;
    }
    function recalcular(cid) {
        $("#tabs").tabs({disabled: false});
        $("#totalxconsorcio").html('');
        if ($("#pagoacuenta_" + cid).val() < 0) {<?php /* Si pusieron negativo, lo cambio por cero */ ?>
            $("#pagoacuenta_" + cid).val(parseFloat(0).toFixed(2));
        }
        var totalfacturasrecalcular = parseFloat($("#pagoacuenta_" + cid).val());
        $("input[id^='f_" + cid + "_']").each(function () {
            if ($(this).val() < 0) {<?php /* Si pusieron negativo, lo cambio por cero */ ?>
                $(this).val(parseFloat(0).toFixed(2));
            }
            if (!isNaN(parseFloat($(this).val()))) {
                totalfacturasrecalcular += parseFloat($(this).val());
            }
        });
        $("#totalfacturas_" + cid).html(parseFloat(totalfacturasrecalcular).toFixed(2));

        $("#txc_" + cid).html(parseFloat(totalfacturasrecalcular).toFixed(2));<?php /* los totales x consor en forma de pago */ ?>
        if (parseFloat(totalfacturasrecalcular).toFixed(2) > 0) {
            $("#txcd_" + cid).show();
        } else {
            $("#txcd_" + cid).hide();
        }
    }
    function calculaRestantexConsorcio() {
        $("span[id^='txc_']").each(function () {
            var strid = $(this).attr('id');
            var cid = strid.replace('txc_', '');
            if (parseFloat($("#totalfacturas_" + cid).html()) > 0) {<?php /* si tiene facturas para pagar */ ?>
                //var sumatotal = parseFloat($("#txc_" + cid).html());
                var resta = restante(cid);
                if (resta < 0) {
                    $("#txcr_" + cid).html(parseFloat($("#totalfacturas_" + cid).html()));
                    alert("El total seleccionado para el Consorcio " + consorcios[cid] + " es mayor al total de facturas");
                } else {
                    $("#txcr_" + cid).html(parseFloat(resta).toFixed(2));
                }
                if (parseFloat($("#txcr_" + cid).html()) === 0) {
                    $("#txcr_" + cid).css('color', 'green');
                } else {
                    $("#txcr_" + cid).css('color', 'red');
                }
            } else {
                $("#txcr_" + cid).html("0.00");<?php /* si elige facturas y despues las saca, saco el restante */ ?>
            }
        });
    }
    function restante(cid) {
        var total = parseFloat($("#txc_" + cid).html());
        var x = totxconsor[cid];
        var t1 = 0;
        var e1 = 0;
        $.each(bancoadm, function (k1, v1) {
            t1 += x['tadm'][k1];
            e1 += x['eadm'][k1];
        });
        return parseFloat(total - (x['e'] + e1 + x['chp'] + x['chpadm'] + x['t'] + t1 + x['cht'] + x['apc'] + x['anc'])).toFixed(2);
    }
</script>
<style>
    .hand{color:green;font-weight:bold}
</style>
<?php
echo $this->element('fechas', ['url' => ['controller' => 'Reports', 'action' => 'cuentacorrienteproveedor'], 'model' => 'Proveedor']);
