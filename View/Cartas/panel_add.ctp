<div class="cartas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation')); ?>
    <div class="error" id="err" style="display:none"></div>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Carta Clientes CEONLINE'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('numero', array('label' => __('Destinatario'), 'autocomplete' => 'off'));
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
    //$("<input type='hidden' name='cantidad' id='cantidad'/>").attr("value", 0).prependTo("#CartaPanelAddForm");
    $(function () {
        $("#CartaNumero").focus();
    });
    var i = 0;
    var totales = [];
    var cual = 0;
    $("#CartaNumero").focus();
    var input = $("#CartaNumero");
    var resul = "";
    var tipocarta = "";
    var obleas = [];
    var cant = 0;
    input.keydown(function (evt) {
        if (evt.keyCode === 13 || evt.keyCode === 9) {
            evt.preventDefault();
            $("#err").hide();
            codigo = $("#CartaNumero").val();
            codigo = codigo.toUpperCase();
            if (cual === 0 && codigo !== "" && codigo.length === 12) {<?php /* son los datos del cliente, consorcio y propietario */ ?>
                gD(codigo);
                i++;
                $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][codigo]'/>").attr("value", codigo).prependTo("#CartaPanelAddForm");
                cual = 1;
            } else if (cual === 1 && codigo.length <= 11 && codigo.replace(/[0-9]/g, '') in {S: '', GR: '', CU: '', SU: '', MA: '', GF: '', EU: '', CF: ''}) {
<?php /* (codigo.length === 11 || codigo.length === 10 || codigo.length === 1)
 * son los datos de la oblea del correo (OBLEA O 'S' DE SIMPLE). COMPARO EL IN COMO OBJETOS */ ?>
<?php /* verifico (con las q ya esten guardadas) q la oblea no haya sido utilizada todavía, en caso contrario, tiro error */ ?>
                tipocarta = codigo.replace(/[0-9]/g, '');
                if ($.inArray(codigo, obleas) === -1) {
                    $.ajax({type: "POST", url: "oEU", cache: false, data: {o: codigo}}).done(function (msg) {
                        if (msg === 'false') {
                            if (codigo.toLowerCase() !== 's') {
                                obleas.push(codigo);<?php /* agrego la oblea reciente al array local */ ?>
                            }
                            document.getElementById('si').play();
                            $("<input type='hidden' class='carta_" + i + "' name='data[Carta][" + i + "][oblea]'/>").attr("value", codigo).prependTo("#CartaPanelAddForm");
                            $(" <b> Oblea: " + codigo + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='del(\"" + tipocarta + "\"," + i + ",\"" + codigo + "\")'></b>").appendTo("#c_" + i);
                            actualizaTotales(codigo, false);
                        } else {
                            document.getElementById('no').play();
                            $(".carta_" + i).remove();
                            $("#c_" + i).remove();
                            $("#err").html('La Oblea ' + codigo + ' fue cargada con anterioridad').show();
                            i--;
                        }
                    });
                } else {
                    document.getElementById('no').play();
                    $(".carta_" + i).remove();
                    $("#c_" + i).remove();
                    $("#err").html('La Oblea ' + codigo + ' fue cargada con anterioridad').show();
                    i--;
                }
                $("label[for='CartaNumero']").text('Destinatario');
                cual = 0;
            } else {
                document.getElementById('no').play();
            }
            $("#CartaNumero").val('');
        }
    });
    $("#CartaPanelAddForm").submit(function (event) {
        if (i === 0) {
            alert("<?= __("No se ha cargado ninguna carta") ?>");
            $("#CartaNumero").val('').focus();
            event.preventDefault();
            return false;
        }
        if (cual === 1) {
            alert("<?= __("Falta cargar la oblea de la última carta") ?>");
            $("#CartaNumero").val('').focus();
            event.preventDefault();
            return false;
        }
        if ($("#CartaNumero").val() === "" && confirm('<?= __("Guardar las cartas?") ?>')) {
            $("#CartaNumero").remove();
            return true;
        }
        $("#CartaNumero").val('').focus();
        event.preventDefault();
        return false;
    });
<?php /* muestro el cliente - consorcio - propietario - oblea */ ?>
    function gD(codigo) {
        $.ajax({type: "POST", url: "gD", cache: false, data: {c: codigo}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (obj[0] === null) {
                    alert("<?= __("Cliente invalido") ?>");
                    return;
                }
                if (obj[1] === null) {
                    alert("<?= __("Consorcio invalido") ?>");
                    return;
                }
                if (obj[2] === null) {
                    alert("<?= __("Propietario invalido") ?>");
                    return;
                }
                var cad = "";
                cad += "<li id='c_" + i + "'><b>Administraci&oacute;n:</b> " + obj[0];
                cad += "<b> Consorcio:</b> " + obj[1];
                cad += "<b> Propietario:</b> " + obj[2];
                $(cad + "</li>").prependTo("#listaCartas");
                $("label[for='CartaNumero']").text('Oblea');
            }
        });
    }
    function actualizaTotales(codigo, resta) {
        tipo = codigo.replace(/[0-9]/g, '');
        if (typeof totales[tipo] !== 'undefined') {
            if (resta) {
                totales[tipo]--;
            } else {
                totales[tipo]++;
            }
        } else {
            totales[tipo] = 1;
        }
        var m;
        var text = "";
        for (m in totales) {
            text += m + ": " + totales[m] + ", ";
        }
        $("#totales").text(text);
    }
    function del(tipocarta, id, oblea) {
        $(".carta_" + id).remove();
        $("#c_" + id).remove();
        $("#CartaNumero").focus();
        actualizaTotales(tipocarta, true);
        for (var j = obleas.length - 1; j >= 0; j--) {<?php /* borro la oblea de la lista de obleas locales, deberia hacer break pero no anda, tendria q usar every pero ni bola */ ?>
            if (obleas[j] === oblea) {
                obleas.splice(j, 1);
            }
        }
        i--;
    }
</script>