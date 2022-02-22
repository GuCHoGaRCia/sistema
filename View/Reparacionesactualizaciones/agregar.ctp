<div class="reparacionesactualizaciones form">
    <?php echo $this->Form->create('Reparacionesactualizacione', ['class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple', 'id' => 'guardarreparacion']); ?>
    <fieldset>
        <h3><?php echo __('Actualizar reparación') . h(" " . $reparaciones['Consorcio']['name'] . " - " . ($reparaciones['Propietario']['name2'] ?? '')) ?></h3>
        <?php
        echo $this->JqueryValidation->input('reparacione_id', ['type' => 'hidden', 'value' => $reparaciones['Reparacione']['id']]);
        if (!isset($_SESSION['Auth']['User']['client_id'])) {
            $client_id = $this->Functions->_encryptURL(reset($c));
            echo $this->JqueryValidation->input('supervisor', ['type' => 'hidden', 'value' => $this->Functions->_encryptURL(key($supervisor))]);
            echo $this->JqueryValidation->input('c', ['type' => 'hidden', 'value' => $client_id]);
        }
        echo $this->JqueryValidation->input('consorcio_id', ['type' => 'hidden', 'value' => $reparaciones['Reparacione']['consorcio_id'], 'form' => 'guardarreparacion']);
        $estadoanterior = empty($reparaciones['Reparacionesactualizacione']) ? $reparaciones['Reparacione']['reparacionesestado_id'] : (isset($reparaciones['Reparacionesactualizacione'][0]['reparacionesestado_id']) ? $reparaciones['Reparacionesactualizacione'][0]['reparacionesestado_id'] : $reparaciones['Reparacione']['reparacionesestado_id']);
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('reparacionesestado_id', ['label' => __('Estado') . ' (Anterior: ' . $reparacionesestados[$estadoanterior] . ')', 'selected' => $estadoanterior]);
        echo $this->JqueryValidation->input('fecha', ['label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:95px', 'value' => date("d/m/Y")]);
        echo $this->JqueryValidation->input('concepto', ['label' => __('Concepto') . ' *']);
        echo "</div>";
        echo $this->JqueryValidation->input('observaciones', ['label' => __('Observaciones'), 'class' => 'ckeditor']);
        echo "<div style='border:1px solid #aaa;padding:3px;margin-top:5px'>";
        echo $this->JqueryValidation->input('Reparacionesactualizacione.files.', array(
            'label' => 'Adjuntos seleccione uno o más archivos jpg, png, doc, pdf o xls',
            'div' => false,
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
        $finalizar = $asignar = [];
        foreach ($reparaciones['Reparacionesactualizacione'] as $v) {
            foreach ($v['Reparacionesactualizacionessupervisore'] as $w) {
                if ($w['finalizado']) {
                    if (!isset($asignar[$w['reparacionessupervisore_id']])) {
                        $asignar[$w['reparacionessupervisore_id']] = $reparacionessupervisores[$w['reparacionessupervisore_id']];
                    }
                } else {
                    if (!isset($asignar[$w['reparacionessupervisore_id']])) {
                        $finalizar[$w['reparacionessupervisore_id']] = $reparacionessupervisores[$w['reparacionessupervisore_id']];
                    }
                }
            }
        }
        $asignar = array_diff($reparacionessupervisores, $finalizar);
        if (!empty($reparacionessupervisores)) {
            echo "<div class='inline'>";
            echo $this->JqueryValidation->input('reparacionessupervisore_id', ['label' => __('Asignar Supervisor') . " (" . count($asignar) . ")", 'multiple' => 'multiple', 'options' => $asignar]);
            if (count($finalizar) > 0) {
                echo $this->JqueryValidation->input('reparacionessupervisorefinalizar_id', ['label' => __('Finalizar Supervisor') . " (" . count($finalizar) . ")", 'multiple' => 'multiple', 'options' => $finalizar]);
            }
            echo "</div>";
        }

        $finalizar = $asignar = [];
        foreach ($reparaciones['Reparacionesactualizacione'] as $v) {
            foreach ($v['Reparacionesactualizacionesproveedore'] as $w) {
                if ($w['finalizado']) {
                    if (!isset($asignar[$w['proveedor_id']])) {
                        $asignar[$w['proveedor_id']] = $proveedors[$w['proveedor_id']];
                    }
                } else {
                    if (!isset($asignar[$w['proveedor_id']])) {
                        $finalizar[$w['proveedor_id']] = $proveedors[$w['proveedor_id']];
                    }
                }
            }
        }
        $asignar = array_diff($proveedors, $finalizar);
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('proveedor_id', ['label' => __('Asignar Proveedor') . " (" . count($asignar) . ")", 'multiple' => 'multiple', 'options' => $asignar]);
        if (count($finalizar) > 0) {
            echo $this->JqueryValidation->input('proveedorfinalizar_id', ['label' => __('Finalizar Proveedor') . " (" . count($finalizar) . ")", 'multiple' => 'multiple', 'options' => $finalizar]);
        }
        echo "</div>";
        //debug(array_diff($llaves,$llavesentregadas));
        $llavesdisponibles = array_diff($llaves, $llavesentregadas);
        if (!empty($llaves)) {
            echo "<div style='border:1px solid #aaa;padding:3px;margin-top:5px'>";
            echo "<div class='inline'>";
            echo $this->JqueryValidation->input('llave_id', ['label' => __('Entregar llave') . " (" . count($llavesdisponibles) . ")", 'empty' => '', 'options' => $llavesdisponibles]);
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
            echo "</div>"; //class='inline'
        }
        if (!empty($llavesentregadas)) {
            echo "<div class='inline'>";
            echo $this->JqueryValidation->input('recibirllave', ['label' => __('Recibir llave') . " (" . count($llavesentregadas) . ")", 'empty' => '', 'options' => $llavesentregadas]);
            echo $this->Html->image('keys.png', ['title' => __('Recibir llave'), 'style' => 'display:none;cursor:pointer', 'id' => 'rllave']);
            //echo "<br><div id='listallavesrecibidas'></div>";
            echo "<br>";
            echo "</div>";
        }
        echo "<div id='listallaves'></div>"; //inline
        ?>
    </fieldset>
    <?php
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none' /></div>";
    echo $this->element('adjuntos');
    ?>
</div>
<?php
if (isset($_SESSION['Auth']['User']['Client']['id'])) {// en agregar de Reparacionessupervisores, saco el cancelar, sino me redirije a reparaciones/index q no tiene acceso el supervisor (no esta logueado)
    echo '<br>' . $this->Html->link(__('Cancelar'), ['controller' => 'Reparaciones', 'action' => 'index'], [], __('Desea cancelar?'));
}
?>
<script>
    $("#guardarreparacion").on("submit", function (e) {
        e.preventDefault();
        if ($("#ReparacionesactualizacioneConcepto").val() === "") {
            alert('<?= __('Debe ingresar un Concepto') ?>');
            return false;
        }
        if ($("#ReparacionesactualizacioneConsorcioId").val() === "") {
            alert('<?= __('Debe seleccionar un Consorcio') ?>');
            return false;
        }
<?php /* deshabilito los input q no sirven */ ?>
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
        fd.append('obs', CKEDITOR.instances.ReparacionesactualizacioneObservaciones.getData());
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Reparacionesactualizaciones/agregar",
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
</script>
<script>
    var llaves = [];
    $(document).ready(function () {
        $("#ReparacionesactualizacioneUserId").select2({language: "es"});
        $("#ReparacionesactualizacioneReparacionesestadoId").select2({language: "es"});
        $("#ReparacionesactualizacioneReparacionessupervisoreId").select2({language: "es", placeholder: 'Asignar Supervisores...'});
        $("#ReparacionesactualizacioneReparacionessupervisorefinalizarId").select2({language: "es", placeholder: 'Finalizar Supervisores...'});
        $("#ReparacionesactualizacioneProveedorId").select2({language: "es", placeholder: 'Asignar Proveedores...'});
        $("#ReparacionesactualizacioneProveedorfinalizarId").select2({language: "es", placeholder: 'Finalizar Proveedores...'});
        $("#ReparacionesactualizacioneLlaveId").select2({language: "es", placeholder: 'Seleccione una Llave...'});
        $("#ReparacionesactualizacioneRecibirllave").select2({language: "es", placeholder: 'Seleccione una Llave...'});
        $("#ReparacionesactualizacioneDest").select2({language: "es", placeholder: 'Seleccione Destinatario...'});
        $("#econsor").select2({language: "es", placeholder: 'Seleccione Consorcio...'});
        $("#eprov").select2({language: "es", placeholder: 'Seleccione Proveedor...'});
        $("#esup").select2({language: "es", placeholder: 'Seleccione Supervisor...'});
        $("#seprop").select2({language: "es", placeholder: 'Seleccione Propietario...'});
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
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
    $("#ReparacionesactualizacioneRecibirllave").change(function () {
        if ($("#ReparacionesactualizacioneRecibirllave").val() !== "") {
            $("#rllave").slideDown('fast');
        } else {
            $("#llave").hide();
        }
    });
    $("#ReparacionesactualizacioneLlaveId").change(function () {
        $("#destinollave").slideDown('fast');
        $("#ReparacionesactualizacioneDest").val('').trigger('change');
        reset();
    });
    $("#ReparacionesactualizacioneDest").change(function () {
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
        if ($.inArray($("#ReparacionesactualizacioneLlaveId :selected").val(), llaves) !== -1) {
            alert("La llave ya fue entregada anteriormente");
            return false;
        }
        var id = $("#ReparacionesactualizacioneLlaveId :selected").val();
        var cad = "<div id='ll" + id + "' style='width:99%'><input type='hidden' form='guardarreparacion' name='data[Reparacionesllavesmovimiento][" + id;
        var str = "]' value='" + $("#ReparacionesactualizacioneDest :selected").val() + "#";
        var cad2 = "<u>Llave entregada:</u> " + $("#ReparacionesactualizacioneLlaveId :selected").text() + " - <u>" + $("#ReparacionesactualizacioneDest :selected").text() + ":</u> ";
        if ($("#ReparacionesactualizacioneDest").val() === '0') {<?php /* Proveedor */ ?>
            str += $("#eprov :selected").val();
            cad2 += $("#eprov :selected").text();
        } else {
            if ($("#ReparacionesactualizacioneDest").val() === '1') {<?php /* Supervisor */ ?>
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
        $("#ReparacionesactualizacioneDest").val('').trigger('change');
        reset();
    });
    $("#rllave").on("click", function (event) {
        if ($.inArray($("#ReparacionesactualizacioneRecibirllave :selected").val(), llaves) !== -1) {
            alert("La llave ya fue recibida");
            return false;
        }
        var id = $("#ReparacionesactualizacioneRecibirllave :selected").val();
        var cad = "<div id='ll" + id + "' style='width:99%'><input type='hidden' form='guardarreparacion' name='data[Reparacionesllavesmovimiento][r][" + id;
        cad += "]' value='" + $("#ReparacionesactualizacioneRecibirllave :selected").val() + "'>";
        cad += "<u>Llave recibida:</u> " + $("#ReparacionesactualizacioneRecibirllave :selected").text() + "</u> " + " <img src='<?= $this->webroot ?>img/drop.png' style='cursor:pointer' onclick='remove(" + id + ")'></div>";
        $("#listallaves").prepend(cad);
        llaves.push(id);
        $("#ReparacionesactualizacioneRecibirllave").val('').trigger('change');
        reset();
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
        $("#ReparacionesactualizacioneDest").prop('disabled', val);
        $("#ReparacionesactualizacioneLlaveId").prop('disabled', val);
    }
    function getData(e, id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                $("#" + id + " option").remove();
                $("#" + id).append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#" + id).append($("<option></option>").attr("value", j).text(val));
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

    if (CKEDITOR.instances.ReparacionesactualizacioneObservaciones != null && CKEDITOR.instances.ReparacionesactualizacioneObservaciones != 'undefined') {
        CKEDITOR.instances.ReparacionesactualizacioneObservaciones.destroy();
    }
    CKEDITOR.replace('ReparacionesactualizacioneObservaciones', {
        language: 'es',
        toolbar: 'Full',
        toolbar_Full:
                [{name: 'clipboard', items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo']},
                    {name: 'editing', items: ['Find', 'Replace']},
                    {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat']},
                    {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']},
                    {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']},
                    {name: 'styles', items: ['Font', 'FontSize']},
                    {name: 'colors', items: ['TextColor']},
                    {name: 'tools', items: ['Maximize']},
                    {name: 'document', items: ['Source', 'Print']},
                ],
    });
</script>
<style>
    hr{border-top: 2px dashed #222;}
</style>