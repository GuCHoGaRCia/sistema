<div class="consultas index">
    <h2><?php echo __('Consultas'); ?></h2>
    <?php echo $this->Form->create('Consulta', array('class' => 'jquery-validation')); ?>
    <span style='margin: 0px 0px 0px 10px;width:24px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="actualizar" title="Recargar chat y adjuntos"></span>
    <span style='margin: 0px 0px 0px 10px;width:32px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="visto" title="Marcar como visto"></span>
    <?php
    echo "<div id='ConsultaConsultas' style='float:left;padding-left:2px;width:60%;height:200px;border:1px solid #CCC;margin-top:9px'></div>";
    echo "<div id='ConsultaAdjuntos' style='float:right;padding-left:2px;width:40%;height:200px;border:1px solid #CCC;margin-top:-15px;position:relative;overflow-y:scroll;'></div>";
    echo $this->Form->input('mensaje', array('label' => '', 'style' => 'width:100%;height:25px;margin-top:2px'));
    echo $this->JqueryValidation->input('Adjunto.files.', array(
        'label' => 'Archivos (opcional)',
        'id' => 'archivostxt',
        'name' => 'archivostxt[]',
        'type' => 'file',
        'multiple' => 'multiple',
    ));
    echo "<div class='inline' style='position:relative;float:right;top:-30px;color:red'><div id='pbt' style='position:relative;top:-17px'></div><div id='progressbar' style='width:100px'></div></div>";
    echo $this->Form->end(['label' => __('Enviar consulta'), 'style' => 'width:150px']);
    ?>
</div>
<div id="error"></div>
<script>
    $('#progressbar').progressbar({value: 0});
    $(function () {
        $("#ConsultaConsultas").css("overflow", "auto");
        $("#ConsultaConsultas").css("overflow-x", "hidden");
        $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
        $("#ConsultaMensaje").focus();
        $("#actualizar").click(function () {
            getData();
            getFiles();
            $("#ConsultaMensaje").focus();
        });
        $("#actualizar").click();
        $("#visto").click(function () {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/setUnseen", cache: false, data: {}}).done(function (msg) {
                $(".seen").hide();
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la acción");
                }
            });
        });
    });
    function getData() {
        $("#ConsultaConsultas").html("<p style='color:green'>Actualizando consultas...</p>");
        $.ajax({type: "POST", url: "consultas/getConsultas", cache: false, data: {}}).done(function (msg) {
            $("#ConsultaConsultas").html('');
            parse(msg);
            $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        }).fail(function (jqXHR, textStatus) {
            alert(textStatus);
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la acción");
            }
        });
    }
    function getFiles() {
        $("#ConsultaAdjuntos").html("<p style='color:green'>Actualizando listado...</p>");
        $.ajax({type: "POST", url: "consultas/getArchivos", cache: false, data: {}}).done(function (msg) {
            $("#ConsultaAdjuntos").html('');
            parseFile(msg);
            $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la acción");
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
            $("<p style='color:" + (obj[j]['Consulta']['r'] ? 'red' : 'green') + ";margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + u + obj[j]['Consulta']['m'] + "</p>").prependTo("#ConsultaConsultas");
        }
    }
    function parseFile(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            $("<p style='color:green;margin:5px 0px 0px 0px;'><span style='color:" + (obj[j]['Consultasadjunto']['res'] ? 'red' : 'green') + "'>" + hhh(obj[j][0]['f']) + " - <a href='<?= $this->webroot ?>Consultas/download/" + obj[j]['Consultasadjunto']['l'] + "/" +<?= $_SESSION['Auth']['User']['client_id'] ?> + "'>" + obj[j][0]['r'].substr(0, 50) + "</a></p>").prependTo("#ConsultaAdjuntos");
        }
        $('#progressbar').progressbar({value: 0});
        $('#pbt').html('');
    }

    $("#ConsultaIndexForm").submit(function (event) {
        event.preventDefault();
        if ($("#ConsultaMensaje").val() === "") {
            alert("Debe completar la consulta");
            return false;
        }
        var arch = document.getElementById("archivostxt");
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'txt']) === -1) {
                alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg, .png o .txt!');
                return false;
            }
        }
        $.ajax({type: "POST", url: "consultas/setConsulta", cache: false, data: {c: $("#ConsultaMensaje").val()}}).done(function (msg) {
            if (msg !== '0') {
                if ($('#archivostxt')[0].files.length > 0) {
                    sendFile();
                }
                $("#ConsultaMensaje").val('').focus();
                $('#ConsultaIndexForm')[0].reset();
                $("#ConsultaConsultas").html('');
                parse(msg);
                $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
            } else {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la acción");
            }
        });
    });
    function sendFile() {
        var data = new FormData();
        jQuery.each($('#archivostxt')[0].files, function (i, file) {
            data.append('file-' + i, file);
        });
        $.ajax({
            url: 'consultas/setArchivo',
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
                $("#ConsultaAdjuntos").html('');
                parseFile(data);
                $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
            }
        });
        $('#progressbar').progressbar("option", "value", 0);
        $('#pbt').html('');
    }
    function updateProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbar').progressbar("option", "value", percentComplete);
            $('#pbt').html(parseFloat(percentComplete).toFixed(2) + "%");
        }
    }
</script>