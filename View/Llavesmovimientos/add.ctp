<div class="llavesmovimientos form">
    <?php echo $this->Form->create('Llavesmovimiento', ['class' => 'jquery-validation', 'id' => 'enviamovimiento']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Movimiento de Llave') . " - " . h("#" . $llaves['Llave']['numero'] . " - " . $llaves['Llave']['descripcion']) ?></h2>
        <?php
        echo "<div class='inline'>";
        //echo $this->JqueryValidation->input('llave', ['label' => __('Llave'), 'type' => 'text', 'readonly' => 'readonly', 'value' => $llaves['Llave']['descripcion']]);
        echo $this->JqueryValidation->input('llave_id', ['type' => 'hidden', 'value' => $llaves['Llave']['id']]);
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:95px', 'value' => date("d/m/Y")));
        echo $this->JqueryValidation->input('titulo', ['label' => __('Descripción')]);
        echo $this->JqueryValidation->input('llavesestado_id', ['label' => __('Estado'), 'empty' => '', 'options' => array_diff($llavesestados, [$llaves['Llave']['llavesestado_id'] => $llavesestados[$llaves['Llave']['llavesestado_id']]])]);
        echo "</div>";
        echo "<div id='entregar' style='border:1px solid #aaa;padding:3px;margin-top:5px'>"; //para mostrar o no recibir/entregar. Se muestra si selecciona en el combo llavesestado_id "Entregada" o "Recibida"
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('llave_id', ['label' => __('Entregar llave'), 'readonly' => 'readonly', 'readonly' => 'readonly', 'selected' => $llaves['Llave']['id'], 'options' => [$llaves['Llave']['id'] => $llaves['Llave']['descripcion']]]);
        echo "<div id='destinollave' style='display:none'>";
        echo $this->JqueryValidation->input('dest', ['label' => __('Destinatario'), 'empty' => '', 'options' => ['Proveedor', 'Supervisor', 'Propietario'] /* 'multiple' => 'multiple' */]);
        //echo $this->Form->radio('dest', , ['legend' => false, 'value' => 'E', 'label' => false, 'id' => 'dest', 'style' => 'display:inline;margin-left:30px']);
        echo "</div>";
        echo "<div id='listaProv' style='display:none'>";
        echo $this->JqueryValidation->input('proveedor_id', ['label' => __('Entregar a Proveedor'), 'empty' => '', 'id' => 'eprov']);
        echo "</div>";

        echo "<div id='listaSup' style='display:none'>";
        //debug($reparacionessupervisores);
        echo $this->JqueryValidation->input('reparacionessupervisore_id', ['label' => __('Entregar a Supervisor'), 'empty' => '', 'id' => 'esup']);
        echo "</div>";

        echo "<div id='listaProp' style='display:none'>";
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'readonly' => 'readonly', 'id' => 'econsor', 'empty' => ''));
        echo "<div id='eprop' style='display:none'>" . $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario'), 'id' => 'seprop', 'empty' => '')) . "</div>";
        echo "</div>";
        echo $this->Html->image('keys.png', ['title' => __('Entregar llave'), 'style' => 'display:none;cursor:pointer', 'id' => 'llave']);
        echo "</div>"; //class='inline'
        echo "</div>";
        echo "<div id='recibir' style='border:1px solid #aaa;padding:3px;margin-top:5px'>";
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('recibirllave', ['label' => __('Recibir llave'), 'readonly' => 'readonly', 'selected' => $llaves['Llave']['id'], 'options' => [$llaves['Llave']['id'] => $llaves['Llave']['descripcion']]]);
        echo $this->Html->image('keys.png', ['title' => __('Recibir llave'), 'style' => 'display:none;cursor:pointer', 'id' => 'rllave']);
        //echo "<br><div id='listallavesrecibidas'></div>";
        echo "</div>";
        echo "</div>";
        echo "<div id='listallaves'></div>";
        ?>
    </fieldset>
    <?php echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none' /></div>"; ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['controller' => 'llaves', 'action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    var llaves = [];
    $("#entregar").hide();
    $("#recibir").hide();
    $(document).ready(function () {
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
<?php /* //$("#LlavesmovimientoLlaveId").select2({language: "es", placeholder: 'Seleccione llave...'}); */ ?>
        $("#LlavesmovimientoLlavesestadoId").select2({language: "es", placeholder: 'Seleccione estado...'});
        $("#LlavesmovimientoProveedorId").select2({language: "es"});
        $("#LlavesmovimientoConsorcioId").select2({language: "es"});
        $("#LlavesmovimientoPropietarioId").select2({language: "es"});
        //$("#LlavesmovimientoRecibirllave").change();
        $("#LlavesmovimientoDest").select2({language: "es", placeholder: 'Seleccione Destinatario...'});
        $("#econsor").select2({language: "es", placeholder: 'Seleccione Consorcio...'});
        $("#eprov").select2({language: "es", placeholder: 'Seleccione Proveedor...'});
        $("#esup").select2({language: "es", placeholder: 'Seleccione Supervisor...'});
        $("#seprop").select2({language: "es", placeholder: 'Seleccione Propietario...'});
<?php /* //$("#LlavesmovimientoRecibirllave").select2({language: "es"}).val('<?= $llaves['Llave']['id'] ?>').trigger('change'); */ ?>
    });
    $("#enviamovimiento").on("submit", function (e) {
        e.preventDefault();
        if ($("#LlavesmovimientoTitulo").val() === "") {
            $("#LlavesmovimientoTitulo").focus();
            alert("Ingrese una descripción");
            return false;
        }
        if ($("#LlavesmovimientoLlavesestadoId").val() === "") {
            alert("Debe seleccionar un estado");
            return false;
        }
        if ($("#LlavesmovimientoLlavesestadoId").val() === '2' && !$("#ll<?= $llaves['Llave']['id'] ?>").length) {
            alert("Debe seleccionar un destinatario de la Llave");
            return false;
        }
        $("#load").show();
        $("#guardar").prop('disabled', true);
        var fd = new FormData(this);
        toggle(true);
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Llavesmovimientos/add",
            data: fd,
            contentType: false,
            processData: false
        }).done(function (msg) {
<?php /* $("#test").html("<pre>" + msg + "</pre>"); */ ?>
            var obj = JSON.parse(msg);
            if (obj.e === 1) {
                alert(obj.d);
                $("#load").hide();
                $("#guardar").prop('disabled', false);
            } else {
                window.location.reload();
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    });
    function toggle(val) {
        $("#econsor").prop('disabled', val);
        $("#seprop").prop('disabled', val);
        $("#esup").prop('disabled', val);
        $("#eprov").prop('disabled', val);
        $("#LlavesmovimientoDest").prop('disabled', val);
        $("#LlavesmovimientoLlaveId").prop('disabled', val);
    }
    $("#LlavesmovimientoConsorcioId").change(function () {
        $("#LlavesmovimientoPropietarioId option").remove();
        $("#LlavesmovimientoPropietarioId").hide();
        if ($("#LlavesmovimientoConsorcioId").val() !== "") {
            getData($("#LlavesmovimientoConsorcioId").val());
        }
    });

    $("#LlavesmovimientoLlavesestadoId").change(function () {
        $("#entregar").hide('fast');
        $("#recibir").hide('fast');
        if ($("#LlavesmovimientoLlavesestadoId").val() === '1') {
            $("#recibir").show('fast');
            $("#LlavesmovimientoLlaveId").change();
            $("#LlavesmovimientoRecibirllave").change();
        } else {
            if ($("#LlavesmovimientoLlavesestadoId").val() === '2') {
                $("#entregar").show('fast');
                $("#LlavesmovimientoLlaveId").change();
            }
        }
    });
    $("#LlavesmovimientoLlaveId").change(function () {
        $("#destinollave").show('fast');
        $("#LlavesmovimientoDest").val('').trigger('change');
        reset();
    });
    $("#LlavesmovimientoRecibirllave").change(function () {
        if ($("#LlavesmovimientoRecibirllave").val() !== "") {
            $("#rllave").show('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#LlavesmovimientoDest").change(function () {
        var dest = {0: 'listaProv', 1: 'listaSup', 2: 'listaProp'};
        $.each(dest, function (k, v) {
            $("#" + v).hide();
        });
        reset();
        if ($(this).val() !== "") {
            $("#" + dest[$(this).val()]).show('fast');
        }

    });
    $("#rllave").on("click", function (event) {
        if ($.inArray($("#LlavesmovimientoRecibirllave :selected").val(), llaves) !== -1) {
            alert("La llave ya fue recibida");
            return false;
        }
        var id = $("#LlavesmovimientoRecibirllave :selected").val();
        var cad = "<div id='ll" + id + "' style='width:99%'><input type='hidden' form='enviamovimiento' name='data[Llavesmovimiento][r][" + id;
        cad += "]' value='" + $("#LlavesmovimientoRecibirllave :selected").val() + "'>";
        cad += "<u>Llave recibida:</u> " + hhh($("#LlavesmovimientoRecibirllave :selected").text()) + "</u> " + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='remove(" + id + ")'></div>";
        $("#listallaves").prepend(cad);
        llaves.push(id);
        $("#LlavesmovimientoRecibirllave").val('').trigger('change');
        reset();
    });
    $("#LlavesmovimientoLlaveId").change(function () {
        $("#destinollave").show('fast');
        $("#LlavesmovimientoDest").val('').trigger('change');
        reset();
    });
    $("#LlavesmovimientoDest").change(function () {
        var dest = {0: 'listaProv', 1: 'listaSup', 2: 'listaProp'};
        $.each(dest, function (k, v) {
            $("#" + v).hide();
        });
        reset();
        if ($(this).val() !== "") {
            $("#" + dest[$(this).val()]).show('fast');
        }

    });

    $("#llave").on("click", function (event) {
        if ($.inArray($("#LlavesmovimientoLlaveId").val(), llaves) !== -1) {
            alert("La llave ya fue entregada anteriormente");
            return false;
        }
        var id = $("#LlavesmovimientoLlaveId").val();
        var cad = "<div id='ll" + id + "' style='width:99%'><input type='hidden' name='data[Llavesmovimiento][e][" + id;
        var str = "]' value='" + hhh($("#LlavesmovimientoDest :selected").val()) + "#";
        var cad2 = "<u>Llave:</u> " + hhh($("#LlavesmovimientoLlaveId").text()) + " - <u>" + hhh($("#LlavesmovimientoDest :selected").text()) + ":</u> ";
        if ($("#LlavesmovimientoDest").val() === '0') {<?php /* Proveedor */ ?>
            str += $("#eprov :selected").val();
            cad2 += hhh($("#eprov :selected").text());
        } else {
            if ($("#LlavesmovimientoDest").val() === '1') {<?php /* Supervisor */ ?>
                str += $("#esup :selected").val();
                cad2 += hhh($("#esup :selected").text());
            } else {<?php /* Propietario */ ?>
                str += $("#seprop :selected").val();
                cad2 += hhh($("#econsor :selected").text()) + " - " + hhh($("#seprop :selected").text());
            }
        }
        str += "' />";
        cad = cad + str + cad2 + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='remove(" + id + ")'></div>";<?php /* en str tengo el id del destinatario (superv, proveed o propiet) y lo mando en el form */ ?>
        $("#listallaves").prepend(cad);
        llaves.push(id);
        $("#LlavesmovimientoDest").val('').trigger('change');
        reset();
    });
    $("#econsor").change(function () {<?php /* borro los prop y oculto la llave. Cuando selecciona, obtengo prop y muestro el combo */ ?>
        $("#eprop option").remove();
        $("#llave").hide();
        if ($("#econsor").val() !== "") {
            getData($("#econsor").val(), 'seprop');
            $("#eprop").show('fast');
        } else {
            $("#eprop").hide();
        }
    });
    $("#eprop").change(function () {<?php /* oculto la llave si se resetea el propietario */ ?>
        if ($("#seprop").val() !== "") {
            $("#llave").show('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#esup").change(function () {<?php /* oculto la llave si se resetea el supervisor */ ?>
        if ($("#esup").val() !== "") {
            $("#llave").show('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#eprov").change(function () {<?php /* oculto la llave si se resetea el proveedor */ ?>
        if ($("#eprov").val() !== "") {
            $("#llave").show('fast');
        } else {
            $("#llave").hide();
        }
    });
    function reset() {
        $("#econsor").val('').trigger('change');
        $("#eprov").val('').trigger('change');
        $("#esup").val('').trigger('change');
        $("#eprop option").remove();
        $("#llave").hide();
        $("#rllave").hide();
    }
    function remove(id) {
        console.log(llaves);
        $("#ll" + id).remove();
        var index = llaves.indexOf("" + id + "");
        if (index > -1) {
            llaves.splice(index, 1);
        }
        console.log(llaves);
    }
    function getData(e) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                $("#seprop option").remove();
                $("#seprop").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#seprop").append($("<option></option>").attr("value", j).text(hhh(val)));
                });
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        });
    }
</script>