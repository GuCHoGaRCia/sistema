<div class="reparaciones form">
    <?php echo $this->Form->create('Reparacione', ['class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple', 'id' => 'guardarreparacion']); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Reparación'); ?></h2>
        <?php
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'empty' => ''));
        echo "<div id='prop' style='display:none'>" . $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario'))) . "</div>";
        echo "</div>";
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('reparacionesestado_id', array('label' => __('Estado') . ' *'));
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo "</div>";
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('observaciones', array('label' => __('Observaciones'), 'class' => 'ckeditor'));
        echo "<div style='border:1px solid #aaa;padding:3px;margin-top:5px'>";
        echo $this->JqueryValidation->input('Reparacione.files.', array(
            'label' => 'Adjuntos seleccione uno o más archivos jpg, png, doc, pdf o xls',
            'div' => false,
            //'data-required' => 0,
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo()',
        ));
        ?>
        <div id="progressbar" style='display:none;width:220px;margin-top:5px'><span style="position:relative;float:left;font-size:12px;font-weight:bold;margin-top:7px">Comprimiendo imagenes... <span id="porc">0%</span></span></div>
        <?php
        echo "<div id='titulos' style='margin-left:15px'></div>";
        echo "</div>";
        if (!empty($proveedors)) {
            echo $this->JqueryValidation->input('proveedor_id', ['label' => __('Asignar Proveedor'), 'multiple' => 'multiple']);
        }
        if (!empty($reparacionessupervisores)) {
            echo $this->JqueryValidation->input('reparacionessupervisore_id', ['label' => __('Asignar Supervisor'), 'multiple' => 'multiple']);
        }

        if (!empty($llaves)) {
            echo "<div class='inline' style='border:1px solid #aaa;padding:3px;margin-top:5px'>";
            echo $this->JqueryValidation->input('llave_id', ['label' => __('Entregar llave'), 'empty' => '' /* 'multiple' => 'multiple' */]);
            echo "<div id='destinollave' style='display:none'>";
            echo $this->JqueryValidation->input('dest', ['label' => __('Destinatario'), 'empty' => '', 'options' => ['Proveedor', 'Supervisor', 'Propietario'] /* 'multiple' => 'multiple' */]);
            //echo $this->Form->radio('dest', , ['legend' => false, 'value' => 'E', 'label' => false, 'id' => 'dest', 'style' => 'display:inline;margin-left:30px']);
            echo "</div>";
            echo "<div id='listaProv' style='display:none'>";
            echo $this->JqueryValidation->input('proveedor_id', ['label' => __('Entregar a Proveedor'), 'empty' => '', 'id' => 'eprov']);

            echo "</div>";

            echo "<div id='listaSup' style='display:none'>";
            echo $this->JqueryValidation->input('reparacionessupervisore_id', ['label' => __('Entregar a Supervisor'), 'empty' => '', 'id' => 'esup']);
            echo "</div>";

            echo "<div id='listaProp' style='display:none'>";
            echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'readonly' => 'readonly', 'id' => 'econsor', 'empty' => ''));
            echo "<div id='eprop' style='display:none'>" . $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario'), 'id' => 'seprop', 'empty' => '')) . "</div>";
            echo "</div>";
            echo $this->Html->image('keys.png', ['title' => __('Entregar llave'), 'style' => 'display:none;cursor:pointer', 'id' => 'llave']);
            echo "<br><div id='listallaves'></div>";
            echo "</div>";
        }
        ?>
    </fieldset>
    <?php
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none' /></div>";
    echo $this->element('adjuntos');
    ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    var llaves = [];
    $(function () {
        $("#ReparacioneConsorcioId").select2({language: "es", placeholder: 'Seleccione Consorcio...'});
        $("#ReparacionePropietarioId").select2({language: "es", placeholder: 'Seleccione Propietario...'});
        $("#ReparacioneReparacionesestadoId").select2({language: "es"});
        $("#ReparacioneReparacionessupervisoreId").select2({language: "es", placeholder: 'Seleccione uno o más Supervisores...'});
        $("#ReparacioneProveedorId").select2({language: "es", placeholder: 'Seleccione uno o más Proveedores...'});
        $("#ReparacioneLlaveId").select2({language: "es", placeholder: 'Seleccione una Llave...'});
        $("#ReparacioneDest").select2({language: "es", placeholder: 'Seleccione Destinatario...'});
        $("#econsor").select2({language: "es", placeholder: 'Seleccione Consorcio...'});
        $("#eprov").select2({language: "es", placeholder: 'Seleccione Proveedor...'});
        $("#esup").select2({language: "es", placeholder: 'Seleccione Supervisor...'});
        $("#seprop").select2({language: "es", placeholder: 'Seleccione Propietario...'});
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    });
    $("#ReparacioneConsorcioId").change(function () {
        $("#ReparacionePropietarioId option").remove();
        if ($("#ReparacioneConsorcioId").val() !== "") {
            getData($("#ReparacioneConsorcioId").val(), 'ReparacionePropietarioId');
            $("#prop").slideDown('fast');
        } else {
            $("#prop").hide();
        }
    });
    $("#econsor").change(function () {<?php /* borro los prop y oculto la llave. Cuando selecciona, obtengo prop y muestro el combo */ ?>
        $("#eprop option").remove();
        $("#llave").hide();
        if ($("#econsor").val() !== "") {
            getData($("#econsor").val(), 'seprop');
            $("#eprop").slideDown('fast');
        } else {
            $("#eprop").hide();
        }
    });
    $("#eprop").change(function () {<?php /* oculto la llave si se resetea el propietario */ ?>
        if ($("#seprop").val() !== "") {
            $("#llave").slideDown('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#esup").change(function () {<?php /* oculto la llave si se resetea el supervisor */ ?>
        if ($("#esup").val() !== "") {
            $("#llave").slideDown('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#eprov").change(function () {<?php /* oculto la llave si se resetea el proveedor */ ?>
        if ($("#eprov").val() !== "") {
            $("#llave").slideDown('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#ReparacioneLlaveId").change(function () {
        $("#destinollave").slideDown('fast');
        $("#ReparacioneDest").val('').trigger('change');
        reset();
    });
    $("#ReparacioneDest").change(function () {
        var dest = {0: 'listaProv', 1: 'listaSup', 2: 'listaProp'};
        $.each(dest, function (k, v) {
            $("#" + v).hide();
        });
        reset();
        if ($(this).val() !== "") {
            $("#" + dest[$(this).val()]).slideDown('fast');
        }

    });

    $("#llave").on("click", function (event) {
        if ($.inArray($("#ReparacioneLlaveId :selected").val(), llaves) !== -1) {
            alert("La llave ya fue entregada anteriormente");
            return false;
        }
        var id = $("#ReparacioneLlaveId :selected").val();
        var cad = "<div id='ll" + id + "' style='width:99%'><input type='hidden' name='data[Reparacionesllavesmovimiento][" + id;
        var str = "]' value='" + $("#ReparacioneDest :selected").val() + "#";
        var cad2 = "<u>Llave:</u> " + $("#ReparacioneLlaveId :selected").text() + " - <u>" + $("#ReparacioneDest :selected").text() + ":</u> ";
        if ($("#ReparacioneDest").val() === '0') {<?php /* Proveedor */ ?>
            str += $("#eprov :selected").val();
            cad2 += $("#eprov :selected").text();
        } else {
            if ($("#ReparacioneDest").val() === '1') {<?php /* Supervisor */ ?>
                str += $("#esup :selected").val();
                cad2 += $("#esup :selected").text();
            } else {<?php /* Propietario */ ?>
                str += $("#seprop :selected").val();
                cad2 += $("#econsor :selected").text() + " - " + $("#seprop :selected").text();
            }
        }
        str += "' />";
        cad = cad + str + cad2 + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='remove(" + id + ")'></div>";<?php /* en str tengo el id del destinatario (superv, proveed o propiet) y lo mando en el form */ ?>
        $("#listallaves").prepend(cad);
        llaves.push(id);
        $("#ReparacioneDest").val('').trigger('change');
        reset();
    });

    function reset() {
        $("#econsor").val('').trigger('change');
        $("#eprov").val('').trigger('change');
        $("#esup").val('').trigger('change');
        $("#eprop option").remove();
        $("#llave").hide();
    }
    function remove(id) {
        $("#ll" + id).remove();
        var index = llaves.indexOf("" + id + "");
        if (index > -1) {
            llaves.splice(index, 1);
        }
    }
    function toggle(val) {
        $("#econsor").prop('disabled', val);
        $("#seprop").prop('disabled', val);
        $("#esup").prop('disabled', val);
        $("#eprov").prop('disabled', val);
        $("#archivostxt").prop('disabled', val);
        $("#ReparacioneDest").prop('disabled', val);
        $("#ReparacioneLlaveId").prop('disabled', val);
    }
    $("#guardarreparacion").on("submit", function (e) {
        e.preventDefault();
        if ($("#ReparacioneConsorcioId").val() === "") {
            alert('<?= __('Debe seleccionar un Consorcio') ?>');
            return false;
        }
        if ($("#ReparacioneConcepto").val() === "") {
            $("#ReparacioneConcepto").focus();
            alert('<?= __('Debe ingresar un Concepto') ?>');
            return false;
        }
        if (!checkFiles()) {
            return false;
        }
        if (document.getElementById("archivostxt").files.length > 0 && !fincompress) {
            alert("Comprimiendo imagenes, espere un instante y vuelva a intentarlo");
            return false;
        }
        $("#load").show();
        $("#guardar").prop('disabled', true);
        toggle(true);
        var fd = new FormData(this);
        var x = 0;
        for (var pair of formdata.entries()) {
            fd.append('file' + x, pair[1]);
            x++;
        }
        fd.append('obs', CKEDITOR.instances.ReparacioneObservaciones.getData());
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Reparaciones/add",
            data: fd,
            contentType: false,
            processData: false
        }).done(function (msg) {
            //$("#test").html("<pre>" + msg + "</pre>");
            var obj = JSON.parse(msg);
            if (obj.e === 1) {
                alert(obj.d);
                $("#load").hide();
                $("#guardar").prop('disabled', false);
                toggle(false);
            } else {
                window.location.replace("<?= $this->webroot ?>Reparaciones");
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    });
    function getData(e, id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = jsonParseOrdered(msg);
                $("#" + id + " option").remove();
                $("#" + id).append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#" + id).append($("<option></option>").attr("value", val["k"]).text(val["v"]));
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
