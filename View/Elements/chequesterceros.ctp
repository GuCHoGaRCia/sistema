<?php
$cheq = "<b>" . __('Cheques') . " (" . count($chequesterceros) . ")" . "</b> ";
$cheq .= $this->Html->image('new.png', ['alt' => __('Agregar'), 'title' => __('Agregar'), 'style' => 'width:20px;height:20px;cursor:pointer', 'id' => 'crearchequeterc']);
$cheq .= "&nbsp;";
$cheq .= $this->Html->image('view.png', ['title' => __('Seleccionar'), 'id' => 'seleccChequeterc', 'style' => 'width:20px;height:20px;cursor:pointer']);
echo $this->JqueryValidation->input('cheque', ['label' => $cheq, 'type' => 'number', 'readonly' => 'readonly', 'id' => 'totalchequeterc', 'value' => 0, 'min' => 0, 'form' => 'guardarcobranza']);
?>
<script>
    $("#crearchequeterc").on("click", function () {
        $("#ChequeCtc").val("ChT " + $("#CobranzaConcepto").val());
        dialogChequeterc.dialog("open");
    });
    $("#seleccChequeterc").on("click", function () {
        dialogSelChequeterc.dialog("open");
    });
    function mostrarCheques(cid) {
        $("#dialogChequeterclistacheques div").each(function () {
            $(this).hide();
        });
        $("#dialogChequeterclistacheques div[data-cid='" + cid + "']").each(function () {
            $(this).show();
        });
    }
    function parseCT(msg) {
        var cad = $("#dialogChequeterclistacheques").html();
        if (cad.length === 91) {<?php /* cartel "No se encuentran cheques de terceros disponibles para utilizar", lo saco */ ?>
            $("#dialogChequeterclistacheques").html('<label>Seleccione los cheques a utilizar ingresando un importe mayor a cero * Total seleccionado: <span id=\'totseleccht\'>0.00</span></label><div class="info">Haga click en la descripción del cheque para utilizar el saldo disponible</div><br>');
        }
        try {
            var obj = JSON.parse(msg);
            var c = obj["Cheque"];
            $tipocheque = c['fisico'] ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ';
            $("<div class='inline'>" + "<input type='number' min='0' step='0.01' value='0' data-saldo='" + c['saldo'] + "' name='data[Cobranza][lcht_" + c['id'] + "]' id='lcht_" + c['id'] + "' data-val='" + c['importe'] + "' form='guardarcobranza' style='width:120px'/>&nbsp;<span style='cursor:pointer' onclick='javascript:" + '$("#lcht_' + c['id'] + '").val(' + c['saldo'] + ').change()\'' + ">" + $tipocheque + hhh(c['concepto']) + " - Valor: $" + c['importe'] + " - Saldo: $" + c['saldo'] + "</span></div>").appendTo("#dialogChequeterclistacheques");
        } catch (e) {
            return;
        }
        alert('<?= __('El cheque de terceros fue guardado correctamente') ?>');
    }
    $("input[id^='lcht_']").change(function () {
        if (parseFloat($(this).val()) > parseFloat($(this).data('val'))) {
            alert('<?= __('El importe a utilizar del cheque no puede superar el valor del cheque') ?>');
            $(this).val(0);
            $(this).focus();
            return;
        }
        var tch = 0;
        $("input[id^='lcht_']").each(function () {
            tch += parseFloat($(this).val());
        });
        if (parseFloat($("#montototal").val()) - parseFloat($("#transferencia").val()) - parseFloat(tch).toFixed(2) < 0) {
            alert('<?= __('No se puede abonar un importe mayor al monto total') ?>');
            $(this).val(0);
            var tch = 0;
            $("input[id^='lcht_']").each(function () {
                tch += parseFloat($(this).val());
            });
            $(this).focus();
        }
        $("#totseleccht").text(parseFloat(tch).toFixed(2));
    });
    dialogChequeterc = $("#dialogChequeterc").dialog({
        autoOpen: false,
        width: 700,
        modal: true,
        title: "Crear cheque tercero",
        buttons: {
            Guardar: function () {
                var f1 = $("#ChequeCtfe").val();
                var f2 = $("#ChequeCtfp").val();
                var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
                var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
                if (x > y) {
                    alert('<?= __('La Fecha Emisión debe ser menor o igual a la Fecha Vencimiento') ?>');
                    return false;
                }
                fe = f1.substr(6, 4) + "-" + f1.substr(3, 2) + "-" + f1.substr(0, 2);
                fp = f2.substr(6, 4) + "-" + f2.substr(3, 2) + "-" + f2.substr(0, 2);
                if ($("#ChequeCtc").val() === "") {
                    alert('<?= __('Debe ingresar un concepto') ?>');
                    return false;
                }
                if ($("#ChequeCtn").val() === "") {
                    alert('<?= __('Debe ingresar Banco / Número cheque') ?>');
                    return false;
                }
                if (!parseFloat($("#ChequeCti").val())) {
                    alert('<?= __('Debe ingresar el importe') ?>');
                    return false;
                }
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Cheques/agregar", cache: false, data: {fe: fe, fp: fp, c: $("#ChequeCtc").val(), n: $("#ChequeCtn").val(), i: $("#ChequeCti").val(), t: $("#ChequeFisico").val()}}).done(function (msg) {
                    parseCT(msg);
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo agregar el cheque. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo agregar el cheque de terceros");
                    }
                });
                dialogChequeterc.dialog("close");
            },
            Cancelar: function () {
                dialogChequeterc.dialog("close");
            }
        },
        close: function () {
            if (typeof ($("#agregarcheque")[0]) !== "undefined") {
                $("#agregarcheque")[0].reset();
            }
            $("#ChequeCtfp").val('<?= date("d/m/Y") ?>');
            $("#ChequeCtfe").val('<?= date("d/m/Y") ?>');
            $("#ChequeCtc").val($("#CobranzaConcepto").val());
            $("#ChequeCtn").val('');
            $("#ChequeCti").val(0);
            $("#ChequeFisico").val(1);
        }
    });
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
                var tch = 0;
                $("input[id^='lcht_']").each(function () {
                    if ($(this).val() === "" || parseFloat($(this).val()) < 0) {
                        alert('<?= __('El importe de los cheques debe ser mayor o igual a cero') ?>');
                        tch = -1;
                        return false;
                    }
                    tch += parseFloat($(this).val());
                });

                if (tch !== -1) {
                    calcula();
                    dialogSelChequeterc.dialog("close");
                }
            },
        },
        close: function () {
            if (typeof ($("#agregarcheque")[0]) !== "undefined") {
                $("#agregarcheque")[0].reset();
            }
        }
    });
</script>
<div id="dialogChequeterc" title="Agregar cheque" style="display:block">
    <div class="cheques form">
        <?php echo $this->Form->create('Cheque', ['class' => 'jquery-validation', 'id' => 'agregarcheque']); ?>
        <fieldset>
            <p class="error-message">* Campos obligatorios</p>
            <?php
            echo "<div class='inline'>";
            echo $this->JqueryValidation->input('ctfe', ['label' => __('Fecha emisión') . ' *', 'type' => 'text', 'style' => 'width:110px', 'class' => 'dp', 'value' => date("d/m/Y")]);
            echo $this->JqueryValidation->input('ctfp', ['label' => __('Fecha vencimiento') . ' *', 'type' => 'text', 'style' => 'width:110px', 'class' => 'dp', 'value' => date("d/m/Y")]);
            echo $this->JqueryValidation->input('fisico', ['label' => __('Tipo cheque') . ' *', 'type' => 'select', 'options' => [1 => 'Físico', 0 => 'Echeq']]);
            echo $this->JqueryValidation->input('ctc', ['label' => __('Concepto') . ' *']);
            echo $this->JqueryValidation->input('ctn', ['label' => __('Banco / Número cheque') . ' *']);
            echo $this->JqueryValidation->input('cti', ['label' => __('Importe') . ' *', 'type' => 'number', 'min' => 0, 'step' => 0.01]);
            echo "</div>";
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<div id="dialogChequeterclistacheques" title="Listado de cheques disponibles (click en el concepto para agregar el saldo disponible)" style="display:block">
    <?php
    if (isset($chequesterceros) && count($chequesterceros) != 0) {// muestro los cheques pendientes (si existen)
        echo "<label>" . __("Seleccione los cheques a utilizar ingresando un importe mayor a cero") . " * Total seleccionado: <span id='totseleccht'>0.00</span></label>";
        echo "<div class='info'>Haga click en la descripción del cheque para utilizar el saldo disponible</div><br>";

        foreach ($chequesterceros as $k => $v) {// data-cid se utiliza para mostrar solo los cheques del consorcio seleccionado (se puede pagar a Proveedor de un consor con cheques de ese consor)                 dejar data[Cobranza] porq se usa en cobranzas de propietarios 
            echo "<div class='inline' ";
            $tipocheque = $v['Cheque']['fisico'] == 1 ? '' : '<span style="color:green;font-weight:bold">Echeq</span> - ';
            echo (isset($v['Consorcio']['id']) ? "data-cid='" . $v['Consorcio']['id'] . "'" : "") . ">" . $this->Form->input('cheque_id', ['type' => 'number', 'value' => 0, 'min' => 0, 'step' => 0.01, 'div' => false, 'label' => false, 'name' => 'data[Cobranza][lcht_' . $v['Cheque']['id'] . ']', 'id' => 'lcht_' . $v['Cheque']['id'], 'data-val' => $v['Cheque']['importe'], 'data-saldo' => $v['Cheque']['saldo'],
                'form' => 'guardarcobranza', 'style' => 'width:120px']) . "&nbsp;<span style='cursor:pointer' onclick='javascript:" . '$("#lcht_' . $v['Cheque']['id'] . '").val(' . $v['Cheque']['saldo'] . ').change()' . "'>" . $tipocheque . h($v['Cheque']['concepto']) . " - Valor: $" . $v['Cheque']['importe'] . " - Saldo: $" . $v['Cheque']['saldo'];
            echo "</div>";
        }
    } else {
        echo "<div class='info'>No se encuentran cheques de terceros disponibles para utilizar</div>"; //length=91, si lo cambio, ver javascript cad.length
    }
    ?>
</div>