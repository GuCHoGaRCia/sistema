<div class="comunicaciones form">
    <?php echo $this->Form->create('Comunicacione', ['class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Comunicación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'multiple' => 'multiple'));
        ?>
        Ver/ocultar detalles&nbsp;&nbsp;<a id="ver_ocultar" href="#" style="font-size:14px;" onclick='$("#contlistado").toggle()'>+/-</a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a id="ver_ocultar" href="#" style="font-size:14px;" onclick='seleccionaTodos()'>Todos los Consorcios</a>
        <br>
        <div id="contlistado" style="display:none">
            <ul id="contlistado2" style="list-style-type:none">
            </ul>                     
        </div>
        <?php
        echo $this->JqueryValidation->input('asunto', ['label' => __('Asunto') . " (al asunto se le antepondrá automáticamente el nombre del Consorcio)"]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('mensaje', ['label' => __('Mensaje'), 'class' => 'ckeditor']);
        echo "<div id='titulos'></div>";
        echo $this->JqueryValidation->input('Adjunto.files.', array(
            'label' => 'Archivos *',
            'div' => false,
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo()',
        ));
        ?>
        <div id="progressbar" style='display:none;width:220px;margin-top:5px'><span style="position:relative;float:left;font-size:12px;font-weight:bold;margin-top:7px">Comprimiendo imagenes... <span id="porc">0%</span></span></div>
    </fieldset>
    <?php
    echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none' /></div>";
    echo $this->element('adjuntos');
    ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#ComunicacioneAsunto").focus();
        $("#ComunicacioneConsorcioId").select2({language: "es"});
    });
</script>
<script type="text/javascript">
    $("#ComunicacioneAddForm").on("submit", function (e) {
        e.preventDefault();
        if ($("input:checkbox:checked").length === 0) {
            alert("<?= __("Debe seleccionar al menos un Propietario") ?>");
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
        $("#archivostxt").prop('disabled', true);
        var fd = new FormData(this);
        var x = 0;
        for (var pair of formdata.entries()) {
            fd.append('file' + x, pair[1]);
            x++;
        }
        fd.delete('data[Comunicacione][mensaje]');<?php /* hago esto porq sino envia vacío el mensaje */ ?>
        fd.append('data[Comunicacione][mensaje]', CKEDITOR.instances.ComunicacioneMensaje.getData());
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Comunicaciones/add",
            data: fd,
            contentType: false,
            processData: false
        }).done(function (msg) {
            var obj = JSON.parse(msg);
            if (obj.e === 1) {
                alert(obj.d);
                $("#load").hide();
                $("#archivostxt").prop('disabled', false);
                $("#guardar").prop('disabled', false);
            } else {
                window.location.href = '<?= $this->webroot ?>Comunicaciones';
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    });
    $("#ComunicacioneConsorcioId").change(function () {
        if (typeof $("#ComunicacioneConsorcioId :selected").val() !== "undefined") {
            $("#enviar").hide();
            getData($("#ComunicacioneConsorcioId").val());
        } else {
            $("#contlistado2").html('');
            $("#contlistado").hide();
        }
    });
    function getData(c) {
        $("#contlistado2").html("<input type='checkbox' checked=checked onClick=\"for (c in document.getElementsByClassName('til')) document.getElementsByClassName('til').item(c).checked = this.checked\" style=\"cursor:pointer\" />&nbsp;Tildar todos - Destildar todos<br>");
        $.ajax({type: "POST", url: "getPropietarios", cache: false, data: {con: c}}).done(function (msg) {
            try {
                var obj = JSON.parse(msg);
                if (!$.isEmptyObject(obj)) {
                    $.each(obj, function (k, v) {
                        $("#contlistado2").append("<b><u>Consorcio " + v[0]['n'] + "</u></b><br>");
                        $.each(obj[k], function (l, m) {
                            var n = m['Propietario'];
                            $("#contlistado2").append($("<li style='margin-left:20px'><input class='til' type='checkbox' name='prop[t_" + hhh(m['cid']) + "_" + l + "]' value='" + hhh(n['c']) + "' checked='checked'>&nbsp;&nbsp;" + hhh(n['n'] + " - " + n['u'] + " (" + n['c'] + ") - " + hhh(n['e'])) + (n['m'] === true ? ' <img src=\'<?php echo $this->webroot; ?>img/police.png\' title=\'El Propietario es Miembro del Consejo\' style=\'width:20px\'/>' : '') + " <a target='_blank' rel='nofollow noopener noreferrer' href='<?php echo $this->webroot; ?>Avisos/view/" + n['l'] + "'>Ver</a></li>"));
                        });
                        $("#contlistado2").append("<hr>");
                    });
                    $("#enviar").show();
                } else {
                    $("#contlistado2").html('No se encontraron propietarios con email disponible');
                }
            } catch (err) {
                //
            }
        });
    }
    function seleccionaTodos() {
        $("#ComunicacioneConsorcioId > option").prop("selected", "selected");
        $("#ComunicacioneConsorcioId").trigger("change");
    }
</script>