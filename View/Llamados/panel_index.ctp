<div class="consultas index">
    <h2><?php echo __('Llamados Administraciones'); ?></h2>
    <?php echo $this->Form->create('Llamado', array('class' => 'jquery-validation')); ?>
    <span style='margin: 0px 0px 0px 10px;width:24px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="actualizar" title="Recargar chat y adjuntos"></span>
    <?php
    echo $this->JqueryValidation->input('client_id', array('label' => false, 'type' => 'select', 'empty' => ''));
    echo "<div id='LlamadoLlamados' style='float:left;padding-left:2px;width:60%;height:200px;border:1px solid #CCC;margin-top:15px'></div>";
    echo "<div id='LlamadoAdjuntos' style='float:right;padding-left:2px;width:40%;height:200px;border:1px solid #CCC;margin-top:9px;position:relative;overflow-y:scroll;'></div>";
    echo $this->Form->input('mensaje', array('label' => '', 'style' => 'width:100%;height:25px;margin-top:2px'));
    echo $this->JqueryValidation->input('Adjunto.files.', array(
        'label' => 'Archivos (opcional)',
        'id' => 'archivostxt',
        'name' => 'archivostxt[]',
        'type' => 'file',
        'multiple' => 'multiple',
    ));
    echo "<div class='inline' style='position:relative;float:right;top:-30px;color:red'><div id='pbt' style='position:relative;top:-17px'></div><div id='progressbar' style='width:100px'></div></div>";
    echo $this->Form->end(['label' => __('Enviar'), 'style' => 'width:200px']);
    ?>
</div>
<div id="error"></div>
<script>
    $('#progressbar').progressbar({value: 0});
    var consultando = false;
    $(function () {
        $("#LlamadoClientId").select2({language: "es", width: "400", placeholder: "<?= __('Seleccione Cliente...') ?>"});
        $("#LlamadoLlamados").css("overflow", "auto");
        $("#LlamadoLlamados").css("overflow-x", "hidden");
        $("#LlamadoLlamados").scrollTop($("#LlamadoLlamados")[0].scrollHeight);
        $("#LlamadoAdjuntos").scrollTop($("#LlamadoAdjuntos")[0].scrollHeight);
        $("#LlamadoMensaje").focus();
        $("#actualizar").click(function () {
            if ($("#LlamadoClientId").val() !== "") {
                getData();
                $("#LlamadoMensaje").focus();
            }
        });
        $(document).on('click', '.deladjunto', function () {
            if (confirm('<?= __("Desea eliminar el adjunto?") ?>')) {
                $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Llamadosadjuntos/delAdjunto", cache: false, data: {cli: $("#LlamadoClientId").val(), id: $(this).attr('id')}}).done(function (msg) {
                    getFiles();
                    $("#LlamadoMensaje").focus();
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion");
                    }
                });
            }
        });
        $("#LlamadoClientId").change(function () {
            if ($("#LlamadoClientId").val() !== "") {
                getData();
                $("#LlamadoMensaje").focus();
            }
        });
    });
    function getData() {
        $("#LlamadoLlamados").html("<p style='color:green'>Actualizando llamados...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Llamados/getLlamados", cache: false, data: {cli: $("#LlamadoClientId").val()}}).done(function (msg) {
            $("#LlamadoLlamados").html('');
            parse(msg);
            $("#LlamadoLlamados").scrollTop($("#LlamadoLlamados")[0].scrollHeight);
            getFiles();
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion");
            }
        });
    }
    function getFiles() {
        $("#LlamadoAdjuntos").html("<p style='color:green'>Actualizando listado...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Llamados/getArchivos", cache: false, data: {cli: $("#LlamadoClientId").val()}}).done(function (msg) {
            $("#LlamadoAdjuntos").html('');
            parseFile(msg);
            $("#LlamadoAdjuntos").scrollTop($("#LlamadoAdjuntos")[0].scrollHeight);
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion");
            }
        });
    }
    function parse(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            var u = obj[j]['User']['u'];
            if (u !== "" && u !== null) {
                u = " (" + u + ") - ";
            } else {
                u = " - ";
            }
            $("<p style='color:" + (obj[j]['Llamado']['r'] ? 'green' : 'red') + ";margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + u + obj[j]['Llamado']['m'] + "</p>").prependTo("#LlamadoLlamados");
        }
    }
    function parseFile(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            $("<p style='color:green;margin:5px 0px 0px 0px;'><span style='color:" + (obj[j]['Llamadosadjunto']['res'] ? 'green' : 'red') + "'>" + hhh(obj[j][0]['f']) + "</span> - <a href='<?= $this->webroot ?>panel/Llamados/download/" + obj[j]['Llamadosadjunto']['l'] + "/" + $("#LlamadoClientId").val() + "'>" + obj[j][0]['r'].substr(0, 50) + "</a><span class='deladjunto' id='" + obj[j]['Llamadosadjunto']['id'] + "'></span></p>").prependTo("#LlamadoAdjuntos");
        }
        $('#progressbar').progressbar({value: 0});
        $('#pbt').html('');
    }

    $("#LlamadoPanelIndexForm").submit(function (event) {
        event.preventDefault();
        if ($("#LlamadoClientId").val() === "") {
            alert("Debe seleccionar el cliente");
            return false;
        }
        if ($("#LlamadoMensaje").val() === "") {
            alert("Debe completar la consulta");
            return false;
        }
        var arch = document.getElementById("archivostxt");
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'zip', 'rar', '7z']) === -1) {
                alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg, .png, .txt, .zip, .rar!');
                return false;
            }
        }
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Llamados/setLlamado", cache: false, data: {c: $("#LlamadoMensaje").val(), cli: $("#LlamadoClientId").val()}}).done(function (msg) {
            if (msg !== '0') {
                if ($('#archivostxt')[0].files.length > 0) {
                    sendFile();
                }
                $("#LlamadoMensaje").val('').focus();
                var old = $("#LlamadoClientId").val();
                $('#LlamadoPanelIndexForm')[0].reset();
                $("#LlamadoClientId").val(old);
                $("#LlamadoLlamados").html('');
                parse(msg);
                $("#LlamadoLlamados").scrollTop($("#LlamadoLlamados")[0].scrollHeight);
            } else {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion");
            }
        });
    });
    function sendFile() {
        var data = new FormData();
        jQuery.each($('#archivostxt')[0].files, function (i, file) {
            data.append('file-' + i, file);
        });
        data.append('cli', $("#LlamadoClientId").val());
        $.ajax({
            url: '<?= $this->webroot ?>panel/Llamados/setArchivo',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            xhr: function () {
                myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', updateProgress, false); // for handling the progress of the upload
                }
                return myXhr;
            },
            success: function (data) {
                $("#LlamadoAdjuntos").html('');
                parseFile(data);
                $("#LlamadoAdjuntos").scrollTop($("#LlamadoAdjuntos")[0].scrollHeight);
            }
        });
        $('#progressbar').progressbar("option", "value", 0);
        $('#pbt').html('');
    }
    var percentComplete = 0;
    function updateProgress(evt) {
        if (evt.lengthComputable) {
            percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbar').progressbar("option", "value", percentComplete);
            $('#pbt').html(parseFloat(percentComplete).toFixed(2) + "%");
        }
    }
    function selec(id) {
        $("#LlamadoClientId").val(id).trigger('change');
    }
</script>