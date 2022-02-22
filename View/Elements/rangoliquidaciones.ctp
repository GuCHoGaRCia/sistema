<?php

/* llamada: <a href="#" onclick='javascript:$("#pid").val("<?= $propietario['Propietario']['id'] ?>");$("#dfechascc").dialog("open")'>Cuenta corriente</a> 
  <a href="#" onclick='javascript:$("#propid").val("<?= $propietario['Propietario']['id'] ?>");$("#cid").val("<?= $propietario['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq();'>Informe de Deuda</a>
 */
?>
<script>
    $(function () {
        $("#l1").select2({language: "es"});
        $("#l2").select2({language: "es"});
        var dialogp = $("#rangoliq").dialog({
            autoOpen: false,
            height: "300",
            width: "450",
            maxWidth: "450px",
            modal: true,
            title: 'Seleccione Liquidación de inicio y fin',
            position: {my: "center", at: "center", of: window},
            buttons: {
                "Ver reporte": function () {
                    if ($("#edc").val() === "1") {
                        <?php /* Para el Estado Disponibilidad Consorcio */ ?>
                        <?php /* Ejemplo como vendria $("#l1").val() o $("#l2").val(),  4-id:3539  Si es mayor a -1 es que encontro el guion */ ?>
                        if (($("#l1").val()).indexOf("-") > -1 && ($("#l2").val()).indexOf("-") > -1) {
                            var l1 = parseFloat(($("#l1").val()).substring(0, ($("#l1").val()).indexOf("-")));
                            var l2 = parseFloat(($("#l2").val()).substring(0, ($("#l2").val()).indexOf("-")));
                        } else {
                            alert('<?= __('Debe seleccionar Liquidación inicial y final') ?>');
                            return false;
                        }
                    } else {
                        var l1 = parseFloat($("#l1").val());
                        var l2 = parseFloat($("#l2").val());
                    }
                    if (l1 === 0 || l2 === 0) {
                        alert('<?= __('Debe seleccionar Liquidación inicial y final') ?>');
                        return false;
                    }
                    if (l2 < l1) {
                        alert('<?= __('La Liquidación de inicio debe ser menor o igual a la de fin') ?>');
                        return false;
                    }
                    $("#rliqs").submit();
                },
                Cancelar: function () {
                    $("#rliqs")[0].reset();
                    dialogp.dialog("close");
                }
            },
            close: function () {
                if (typeof ($("#rliqs")[0]) !== "undefined") {
                    $("#rliqs")[0].reset();
                }
            },
            open: function () {
                event.preventDefault();
            }
        });
    });
<?php /* Obtengo las liquidaciones del consorcio */ ?>
    function getLiq(origen) {
        $.ajax({type: "POST", url: "/sistema/liquidations/getLiquidaciones", cache: false, data: {c: $("#cid").val(), propid: $("#propid").val(), origenllamada: origen}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (origen !== null && (origen === 1 || origen === 4 || origen === 5) && jQuery.isEmptyObject(obj)) {
                    alert("El propietario no posee deuda");
                    $("#rangoliq").dialog("close");
                }
                if (origen !== null && (origen === 2 || origen === 3) && jQuery.isEmptyObject(obj)) {
                    alert("No existen liquidaciones");
                    $("#rangoliq").dialog("close");
                }
                $("#l1 option").remove();
                $("#l1").append($("<option></option>").attr("value", 0).text("Seleccione Liquidación inicial..."));
                if (origen !== null && (origen === 3 || origen === 1 || origen === 4 || origen === 5)) {
                    var aux = obj;
                    var objToArray = [];
                    for (key in aux) {
                        objToArray.push(aux[key]);
                    }
                    var ordenInverso = [];
                    for (i = objToArray.length - 1; i >= 0; i--) {      <?php /* Para invertir el array de obj */ ?>
                        ordenInverso.push(objToArray[i]);
                    }
                    if (origen === 3) {     <?php /* Estado Disponibilidad Consorcio */ ?>
                        $.each(obj, function (j, val) {
                            if (typeof val['bloqueada'] !== 'undefined' && val['bloqueada'] === 0) {
                                $("#l1").append($("<option></option>").attr("value", j + "-id:" + val['liq_id']).text(val['periodo'] + ' ' + '(Liquidación Abierta!)'));
                            } else {
                                $("#l1").append($("<option></option>").attr("value", j + "-id:" + val['liq_id']).text(val['periodo']));
                            }
                        });
                        $("#l2 option").remove();
                        $("#l2").append($("<option></option>").attr("value", 0).text("Seleccione Liquidación final..."));

                        var i = ordenInverso.length;

                        $.each(ordenInverso, function (j, val) {
                            if (typeof val['bloqueada'] !== 'undefined' && val['bloqueada'] === 0) {
                                $("#l2").append($("<option></option>").attr("value", i + "-id:" + val['liq_id']).text(val['periodo'] + ' ' + '(Liquidación Abierta!)'));
                            } else {
                                $("#l2").append($("<option></option>").attr("value", i + "-id:" + val['liq_id']).text(val['periodo']));
                            }
                            i--;
                        });
                    } else {
                        if (origen === 1 || origen === 4 || origen === 5) {     <?php /* Algun informe de deuda */ ?>
                            var ids = [];
                            var k = ordenInverso.length - 1;
                            $.each(obj, function (j, val) {
                                $("#l1").append($("<option></option>").attr("value", j).text(val));
                                ids[k] = j;
                                k--;
                            });
                            $("#l2 option").remove();
                            $("#l2").append($("<option></option>").attr("value", 0).text("Seleccione Liquidación final..."));
                            $.each(ordenInverso, function (j, val) {
                                $("#l2").append($("<option></option>").attr("value", ids[j]).text(val));
                            });
                        }
                    }
                } else {
                    $.each(obj, function (j, val) {
                        $("#l1").append($("<option></option>").attr("value", j).text(val));
                    });
                    $("#l2 option").remove();
                    $("#l2").append($("<option></option>").attr("value", 0).text("Seleccione Liquidación final..."));
                    $.each(obj, function (j, val) {
                        $("#l2").append($("<option></option>").attr("value", j).text(val));
                    });
                }
            } else {
                alert("No se pudieron obtener las liquidaciones");
            }
        });
    }
</script>        
<div id="rangoliq" style="display:none">
    <div class="form">
            <?php echo $this->Form->create($model, ['class' => 'jquery-validation', 'id' => 'rliqs', 'target' => '_blank', 'url' => $url]); ?>
        <p class="error-message" style="font-size:11px">* Campos obligatorios</p>
            <?php
            echo $this->Form->input('edc', ['type' => 'hidden', 'id' => 'edc']);
            echo $this->Form->input('cid', ['type' => 'hidden', 'id' => 'cid']);
            echo $this->Form->input('propid', ['type' => 'hidden', 'id' => 'propid']); 
            echo $this->Form->input('l1', ['id' => 'l1', 'type' => 'select', 'label' => __('Liquidación inicio') . ' *', 'style' => 'width:300px']);
            echo $this->Form->input('l2', ['id' => 'l2', 'type' => 'select', 'label' => __('Liquidación fin') . ' *', 'style' => 'width:300px']);
            ?>
            <?php echo $this->Form->end(); ?>
    </div> 
</div>