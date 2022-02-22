<div class="cartas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation')); ?>
    <div class="error" id="err" style="display:none"></div>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Carta Clientes TERCEROS'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('client_id', array('label' => __('Seleccione el cliente'), 'options' => $clients2, 'empty' => __('Seleccione el cliente...')));
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio'), 'type' => 'select'));
        echo $this->JqueryValidation->input('numero', array('label' => __('Oblea ("S" para simples)'), 'type' => 'text'));
        echo "<span id='totales'></span>";
        ?>
    </fieldset>
    <?php echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<ul id="listaCartas">
</ul>
<audio id="si" style="display:none;">
    <source src="<?= $this->webroot ?>img/ausi.wav" type="audio/ogg">
</audio>
<audio id="no" style="display:none;">
    <source src="<?= $this->webroot ?>img/auno.wav" type="audio/ogg">
</audio>
<script type="text/javascript">
<?php
echo "var clients = {";
foreach ($clients as $k => $v) {
    echo "$k:'$v',"; //necesito en clients[x] que me devuelva x
}
echo "};";
?>
    $("#CartaClientId").select2({language: "es"});
    $("#CartaConsorcioId").select2({language: "es"});
    var i = 0;
    var submit = false;
    $(".input.text").hide();
    var input = $("#CartaNumero");
    var resul = "";
    var tipocarta = "";
    var codigo = "";
    var totales = [];
    var obleas = [];
    var cual = 1;
    var cant = 0;
    input.keydown(function (evt) {
        if (evt.keyCode === 13 || evt.keyCode === 9) {
            evt.preventDefault();
            $("#err").hide();
            codigo = $("#CartaNumero").val();
            codigo = codigo.toUpperCase();
            cant = 1;
            if (cual === 1 && (codigo.length === 11 || codigo.length === 1) && codigo.replace(/[0-9]/g, '') in {S: '', GR: '', CU: '', SU: '', MA: '', GF: '', EU: '', CF: ''}) {
                if ($("#CartaClientId").val() === "" || $("#CartaConsorcioId").val() === "") {
                    alert("Seleccione cliente y consorcio");
                } else {
                    tipocarta = codigo.replace(/[0-9]/g, '');
                    if ($.inArray(codigo, obleas) === -1) {
                        $.ajax({type: "POST", url: "oEU", cache: false, data: {o: codigo}}).done(function (msg) {
                            if (msg === 'false') {
                                if (tipocarta.toLowerCase() !== 's') {
                                    obleas.push(codigo);<?php /* agrego la oblea reciente al array local */ ?>
                                }
                                oblea = codigo;
                                $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][oblea]'/>").attr("value", codigo).prependTo("#CartaPanelAdd2Form");
                                $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][cliente]'/>").attr("value", $("#CartaClientId").val()).prependTo("#CartaPanelAdd2Form");
                                $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][consorcio]'/>").attr("value", $("#CartaConsorcioId").val()).prependTo("#CartaPanelAdd2Form");
                                $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][tipo]'/>").attr("value", tipocarta).prependTo("#CartaPanelAdd2Form");
                                $("label[for='CartaNumero']").text('Descripción (Propietario, Codigo, etc. o Cantidad para las Simples)');
                                cual = 0;
                                submit = false;
                            } else {
                                document.getElementById('no').play();
                                $("#c_" + i).remove();
                                $("#err").html('La Oblea ' + codigo + ' fue cargada con anterioridad').show();
                                submit = true;<?php /* despues de un error, permito enviar (si hay cartas cargadas) */ ?>
                            }
                        }).fail(function (jqXHR, textStatus) {
                            if (jqXHR.status === 403) {
                                alert("No se pudieron obtener los datos. Verifique que se encuentra logueado en el sistema");
                            } else {
                                alert("No se pudieron obtener los datos");
                            }
                        });
                    } else {
                        document.getElementById('no').play();
                        $("#c_" + i).remove();
                        $("#err").html('La Oblea ' + codigo + ' fue cargada con anterioridad').show();
                        submit = true;<?php /* despues de un error, permito enviar (si hay cartas cargadas) */ ?>
                    }
                }
            } else { // es el propietario/unidad o la cantidad de S
                if (cual === 0 && codigo.length !== 0) {// por si apreta enter al cargar el propietario y esta vacio
                    $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][codigo]'/>").attr("value", codigo).prependTo("#CartaPanelAdd2Form");
                    var cad = "";
                    cad += "<li id='c_" + i + "'><b>Administraci&oacute;n:</b> " + $("#CartaClientId option:selected").text();
                    cad += "<b> Consorcio:</b> " + $("#CartaConsorcioId option:selected").text();
                    cad += "<b> Tipo carta:</b> " + tipocarta;
                    if (tipocarta !== 'S') {
                        cad += "<b> Oblea:</b> " + oblea;
                    }

                    if (tipocarta === 'S' && codigo.replace(/\D/g, '') === codigo) {<?php /* si reemplazo todos los caracteres alfabeticos, y queda igual al codigo, es un numero (CANTIDAD) */ ?>
                        cant = codigo;
                    }
                    cad += "<b> Desc:</b> " + codigo;
                    $(cad + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='del(\"" + tipocarta + "\"," + i + "," + cant + ",\"" + oblea + "\")'></li>").prependTo("#listaCartas");
                    i++;
                    $("label[for='CartaNumero']").text('Oblea ("S" para simples)');
                    //$("#cantidad").attr("value", cant);
                    actualizaTotales(tipocarta);
                    document.getElementById('si').play();
                    cual = 1;
                    submit = true;
                }
            }
            $("#CartaNumero").val('').focus();
        }
    });
    $("#CartaPanelAdd2Form").submit(function (event) {
        if ($("#CartaNumero").val() === "") {
            if (i === 0) {
                alert("<?= __("No se ha cargado ninguna carta") ?>");
                return false;
            }
            if (submit) {
                $("<input type='hidden' name='terc'/>").prependTo("#CartaPanelAdd2Form");<?php /* para identificar q son cartas de terceros */ ?>
                $("#CartaNumero").remove();
                $("#CartaClientId").remove();
                $("#CartaPropietarioId").remove();
                $("#CartaConsorcioId").remove();
                return;
            }
        }
        event.preventDefault();
        $("#CartaNumero").val('').focus();
    });
    $("#CartaConsorcioId").change(function () {
        $("#CartaNumero").val('').focus();
    });
    $("#CartaClientId").change(function () {
        getData($("#CartaClientId").val());
        $("#CartaNumero").val('').focus();
    });
    function getData(cid) {
        $.ajax({type: "POST", url: "getConsorcios", cache: false, data: {cliente: cid}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                $("#CartaConsorcioId option").remove();
                $("#CartaConsorcioId").append($("<option></option>").attr("value", 0).text("Seleccione consorcio..."));
                $.each(obj, function (j, val) {
                    $("#CartaConsorcioId").append($("<option></option>").attr("value", j).text(val));
                });
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudieron obtener los datos. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudieron obtener los datos");
            }
        });
        $(".input.text").show();
    }
    function obleaEnUso(o) {
        if ($.inArray(o, obleas) !== -1) {
            return true;
        }

    }
    function actualizaTotales(codigo) {
        tipo = codigo.replace(/[0-9]/g, '');
        if (typeof totales[tipo] !== 'undefined') {
            totales[tipo] += parseInt(cant);
        } else {
            totales[tipo] = parseInt(cant);
        }
        var m;
        var text = "";
        for (m in totales) {
            text += m + ": " + totales[m] + ", ";
        }
        $("#totales").text(text);
    }
    function del(tipocarta, id, cantidad, oblea) {
        $(".carta_" + id).remove();
        $("#c_" + id).remove();

        $("#CartaNumero").focus();
        if (typeof totales[tipo] !== 'undefined') {
            cant = -cantidad;
        }
        actualizaTotales(tipocarta);
        cant = 0;
        for (var j = obleas.length - 1; j >= 0; j--) {<?php /* borro la oblea de la lista de obleas locales, deberia hacer break pero no anda, tendria q usar every pero ni bola */ ?>
            if (obleas[j] === oblea) {
                obleas.splice(j, 1);
            }
        }
        i--;
    }
</script>