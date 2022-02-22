<?php
$cheq = "<b>" . __('Aplicar pago a cuenta') . " (<span id='cantpac'></span>)" . "</b> ";
$cheq .= "&nbsp;";
$cheq .= $this->Html->image('view.png', ['title' => __('Seleccionar'), 'id' => 'selecc', 'style' => 'width:20px;height:20px;cursor:pointer']);
echo $this->JqueryValidation->input('importepc', ['label' => $cheq, 'type' => 'number', 'id' => 'pagosacuenta', 'value' => 0.00, 'readonly' => 'readonly', 'min' => 0.00, 'step' => 0.01, 'form' => 'guardarcobranza']);
?>

<script>
    $("#selecc").on("click", function () {
        mostrarPAC();
        dialogSelProveedorspagosacuenta.dialog("open");
    });

    function mostrarPAC() {
        $("#listapac div").each(function () {
            $(this).hide();
        });

        var cantpac = 0;
        $("div[id^='c__']").each(function () {
            var strid = $(this).attr('id');
            var id = strid.replace('c__', '');
            if (parseFloat($("#totalfacturas_" + id).html()) > 0 && parseFloat($("#pagoacuenta_" + id).val()) === 0) {<?php /*  no permito aplicar pagos a cuenta si selecciono "Pago a cuenta" como factura */ ?>
                $("#listapac div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").removeAttr('disabled');
                    $(this).show();
                    cantpac++;
                });
            } else {
                $("#listapac div[data-cid='" + id + "']").each(function () {
                    $(this).find("input").prop('disabled', true);<?php /* Si elije factura y forma de pago de un consor, y despues no la quiere pagar, tengo q sacar todo */ ?>
                    $(this).find("input").prop('checked', false);<?php /* Si elije factura, y aplica pac, y despues agrega pac en facturas pendientes, tengo q destildar el paca */ ?>
                });
            }
        });
        $("#cantpac").html(cantpac);
    }

    /*$("input[id^='pca_']").change(function () {
     var tpca = 0;
     $("input[id^='pca_']").each(function () {
     tpca += parseFloat($(this).val());
     });
     $("#listapca").html('$ ' + parseFloat(tpca) + ' - Total Pagos a cuenta a aplicar');
     });*/

    function totxconsorpac() {<?php /* suma los totales de cheques terceros x consorcio (para mostrar total y restante en forma de pago) */ ?>
        $("#pagosacuenta").val(parseFloat(0).toFixed(2));
        $("div[id^='c__']").each(function () {<?php /* inicializo en cero los totales de chequesterceros */ ?>
            var strid = $(this).attr('id');
            var id = strid.replace('c__', '');
            totxconsor[id]['apc'] = 0;
        });
        $("#listapac input").each(function () {<?php /* para cada cheque terceros */ ?>
            var cid = $(this).data('cid');
            if (parseFloat($("#totalfacturas_" + cid).html()) > 0 && $("#" + $(this).attr('id')).is(':checked')) {<?php /* si seleccionó algun importe a pagar y tildo el cheque */ ?>
                totxconsor[cid]['apc'] += parseFloat($(this).data('val'));
            }
        });
    }
    function clickPAC(pacid) {
        var cid = $("#pca_" + pacid).data('cid');
        if ($("#pca_" + pacid).prop('checked')) {<?php /* esta tildando un pago a cuenta */ ?>
            if (parseFloat($("#pca_" + pacid).data('val')) > parseFloat(restante(cid))) {<?php /* cuando tilda actualizo, si destilda solo cambio el total de pagosacuenta */ ?>
                alert('<?= __('El importe seleccionado es mayor al importe restante a abonar del Consorcio') ?> ' + consorcios[cid]);
                $("#pca_" + pacid).prop('checked', null);
            } else {
                totxconsor[cid]['apc'] = parseFloat($("#pca_" + pacid).data('val'));
                $("#pca_" + pacid).prop('checked', 'checked');
            }
        } else {
            totxconsor[cid]['apc'] -= parseFloat($("#pca_" + pacid).data('val'));
            $("#pca_" + pacid).prop('checked', null);
        }
    }
    dialogSelProveedorspagosacuenta = $("#dialogProveedorspagosacuentalista").dialog({
        autoOpen: false,
        width: 900,
        modal: true,
        closeOnEscape: true,
        title: "Seleccionar Pago a cuenta a aplicar",
        buttons: {
            Aceptar: function () {
                /*var apc = 0;
                 $("input[id^='pca_']").each(function () {
                 if (parseFloat($(this).val()) !== 0 && parseFloat($(this).val()) !== parseFloat($(this).data('val'))) {
                 alert("Debe aplicar todo el Pago a cuenta o ingresar cero en caso de no utilizarlo");
                 $(this).val(parseFloat(0).toFixed(2));
                 $(this).focus();
                 apc = -1;
                 return false;
                 }
                 });*/
                //if (apc !== -1) {
                recalculaTodo();
                dialogSelProveedorspagosacuenta.dialog("close");
                //}
            }
        },
        close: function () {
        }
    });
    dialogpp = $("#ppac").dialog({
        autoOpen: false, height: "auto", width: "900", maxWidth: "900",
        position: {at: "center top"},
        closeOnEscape: true,
        modal: true,
        open: function (event, ui) {
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        buttons: {
            Cerrar: function () {
                dialogpp.dialog("close");
            }
        }
    });
</script>
<?php
echo "<div id='ppac' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el pac
?>
<div id="dialogProveedorspagosacuentalista" title="Pagos a cuenta a utilizar" style="display:block">
    <?php
    if (isset($pagosacuentaparaaplicar) && count($pagosacuentaparaaplicar) != 0) {// muestro los pac pendientes (si existen)
        echo "<div class='info'>Haga click en la descripción del Pago a cuenta para aplicarlo</div><br>";
        echo "<div id='listapac'>";
        foreach ($pagosacuentaparaaplicar as $k => $v) {
            echo "<div class='inline' " . (isset($v['Proveedorspagosacuenta']['consorcio_id']) ? "data-cid='" . $v['Proveedorspagosacuenta']['consorcio_id'] . "'" : "") . ">" . $this->Form->input('proveedorspagosacuenta_id', ['type' => 'checkbox', 'div' => false, 'label' => false, 'name' => 'data[' . $v['Proveedorspagosacuenta']['consorcio_id'] . '][paca][' . $v['Proveedorspagosacuenta']['id'] . ']', 'id' => 'pca_' . $v['Proveedorspagosacuenta']['id'], 'data-val' => $v['Proveedorspagosacuenta']['importe'], 'data-cid' => $v['Proveedorspagosacuenta']['consorcio_id'],
                'form' => 'guardarcobranza', 'style' => '-moz-transform:scale(1.5);-webkit-transform:scale(1.5);margin-right:10px', 'onclick' => 'clickPAC(' . $v['Proveedorspagosacuenta']['id'] . ')']) . "&nbsp;" . (isset($consorcio_id[$v['Proveedorspagosacuenta']['consorcio_id']]) ? $consorcio_id[$v['Proveedorspagosacuenta']['consorcio_id']] : 'Administracion') . " - PP " . h($v['Proveedorspago']['concepto']) . " - " . $this->Time->format(__('d/m/Y'), $v['Proveedorspago']['fecha']) . " - " . $this->Functions->money($v['Proveedorspagosacuenta']['importe']);
            echo "<span onclick='window.open(\"" . $this->webroot . "proveedorspagos/view/" . $v['Proveedorspago']['id'] . "\")' class='imgmove' style='background-image:url(" . $this->webroot . "img/icon-info.png);background-repeat:no-repeat;width:14px;height:14px;display:inline-block;margin-left:5px;cursor:pointer;'></span>";
            echo "</div>";
        }
        echo "</div>";
    }
    ?>
</div>
