<div class="consultas index">
    <h2><?php echo __('Consultas'); ?></h2>
    <?php echo $this->Form->create('Consulta', array('class' => 'jquery-validation')); ?>
    <span style='margin: 0px 0px 0px 10px;width:24px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="actualizar" title="Recargar chat y adjuntos"></span>
    <span style='margin: 0px 0px 0px 10px;width:32px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="visto" title="Marcar como visto"></span>
    <p style='width:500px;height:24px;position:relative;float:right;right:10px;top:-5px;'><span style='text-decoration:underline;cursor:pointer' id="verificar">Verificar</span>
        <span id='nuevas' style='font-size:9px'></span>
    </p>
    <?php
    echo $this->JqueryValidation->input('client_id', array('label' => false, 'type' => 'select', 'empty' => ''));
    echo "<div id='ConsultaConsultas' style='float:left;padding-left:2px;width:60%;height:200px;border:1px solid #CCC;margin-top:15px'></div>";
    echo "<div id='ConsultaAdjuntos' style='float:right;padding-left:2px;width:40%;height:200px;border:1px solid #CCC;margin-top:9px;position:relative;overflow-y:scroll;'></div>";
    echo $this->Form->input('mensaje', array('label' => '', 'style' => 'width:100%;height:25px;margin-top:2px', 'maxlength' => '3000'));
    echo $this->JqueryValidation->input('Adjunto.files.', array(
        'label' => 'Archivos (opcional)',
        'id' => 'archivostxt',
        'name' => 'archivostxt[]',
        'type' => 'file',
        'multiple' => 'multiple',
    ));
    echo "<div class='inline' style='position:relative;float:right;top:-30px;color:red'><div id='pbt' style='position:relative;top:-17px'></div><div id='progressbar' style='width:100px'></div></div>";
    echo $this->Form->end(['label' => __('Enviar consulta'), 'style' => 'width:200px']);
    ?>
</div>
<div id="error"></div>
<script>
    $('#progressbar').progressbar({value: 0});
    var consultando = false;
    $(function () {
        $("#ConsultaClientId").select2({language: "es", width: "400", placeholder: "<?= __('Seleccione Cliente...') ?>"});
        $("#ConsultaConsultas").css("overflow", "auto");
        $("#ConsultaConsultas").css("overflow-x", "hidden");
        $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
        $("#ConsultaMensaje").focus();
        $("#actualizar").click(function () {
            if ($("#ConsultaClientId").val() !== "") {
                getData();
                $("#ConsultaMensaje").focus();
            }
        });
        $("#visto").click(function () {
            if ($("#ConsultaClientId").val() !== "") {
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/setUnseen", cache: false, data: {cli: $("#ConsultaClientId").val()}}).done(function (msg) {
                    $(".seen").hide();
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la acción");
                    }
                });
            }
        });
        $(document).on('click', '.deladjunto', function () {
            if (confirm('<?= __("Desea eliminar el adjunto?") ?>')) {
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultasadjuntos/delAdjunto", cache: false, data: {cli: $("#ConsultaClientId").val(), id: $(this).attr('id')}}).done(function (msg) {
                    getFiles();
                    $("#ConsultaMensaje").focus();
                }).fail(function (jqXHR, textStatus) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion");
                    }
                });
            }
        });
        $("#ConsultaClientId").change(function () {
            if ($("#ConsultaClientId").val() !== "") {
                getData();
                $("#ConsultaMensaje").focus();
            }
        });

        $("#verificar").click(function () {
            if (!consultando) {
                buscaNuevasConsultas();
            } else {
                alert("Verificación en proceso iniciada anteriormente, espere por favor..");
            }
        });
        window.setInterval(function () {
            buscaNuevasConsultas();
        }, 60000);
        buscaNuevasConsultas();
    });
    function getData() {
        $("#ConsultaConsultas").html("<p style='color:green'>Actualizando consultas...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/getConsultas", cache: false, data: {cli: $("#ConsultaClientId").val()}}).done(function (msg) {
            $("#ConsultaConsultas").html('');
            parse(msg);
            $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
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
        $("#ConsultaAdjuntos").html("<p style='color:green'>Actualizando listado...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/getArchivos", cache: false, data: {cli: $("#ConsultaClientId").val()}}).done(function (msg) {
            $("#ConsultaAdjuntos").html('');
            parseFile(msg);
            $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
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
            $("<p style='color:" + (obj[j]['Consulta']['r'] ? 'green' : 'red') + ";margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + u + obj[j]['Consulta']['m'] + "</p>").prependTo("#ConsultaConsultas");
        }
    }
    function parseFile(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            $("<p style='color:green;margin:5px 0px 0px 0px;'><span style='color:" + (obj[j]['Consultasadjunto']['res'] ? 'green' : 'red') + "'>" + hhh(obj[j][0]['f']) + "</span> - <a href='<?= $this->webroot ?>panel/Consultas/download/" + obj[j]['Consultasadjunto']['l'] + "/" + $("#ConsultaClientId").val() + "'>" + obj[j][0]['r'].substr(0, 50) + "</a><span class='deladjunto' id='" + obj[j]['Consultasadjunto']['id'] + "'></span></p>").prependTo("#ConsultaAdjuntos");
        }
        $('#progressbar').progressbar({value: 0});
        $('#pbt').html('');
    }

    $("#ConsultaPanelIndexForm").submit(function (event) {
        event.preventDefault();
        if ($("#ConsultaClientId").val() === "") {
            alert("Debe seleccionar el cliente");
            return false;
        }
        if ($("#ConsultaMensaje").val() === "") {
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
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/setConsulta", cache: false, data: {c: $("#ConsultaMensaje").val(), cli: $("#ConsultaClientId").val()}}).done(function (msg) {
            if (msg !== '0') {
                if ($('#archivostxt')[0].files.length > 0) {
                    sendFile();
                }
                $("#ConsultaMensaje").val('').focus();
                var old = $("#ConsultaClientId").val();
                $('#ConsultaPanelIndexForm')[0].reset();
                $("#ConsultaClientId").val(old);
                $("#ConsultaConsultas").html('');
                parse(msg);
                $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
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
        data.append('cli', $("#ConsultaClientId").val());
        $.ajax({
            url: '<?= $this->webroot ?>Consultas/setArchivo',
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
    var percentComplete = 0;
    function updateProgress(evt) {
        if (evt.lengthComputable) {
            percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbar').progressbar("option", "value", percentComplete);
            $('#pbt').html(parseFloat(percentComplete).toFixed(2) + "%");
        }
    }
<?php /* Solo verifico nuevas consultas si no está enviando archivos adjuntos! Sino se caga? */ ?>
    function buscaNuevasConsultas() {
        if (percentComplete === 0 || percentComplete === 100) {
            consultando = true;
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/verificar", cache: false, data: {}}).done(function (msg) {
                var obj = JSON.parse(msg);
                var cad = "";
                for (j = 0; j < obj.length; j++) {
                    cad += "&nbsp;&nbsp;&nbsp;<span onClick='selec(" + hhh(obj[j]['c1']['c']) + ")' style='cursor:pointer;text-decoration:underline'>" + $("#ConsultaClientId option[value='" + hhh(obj[j]['c1']['c']) + "']").text() + "</span>";
                }
                $("#nuevas").html(cad);
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la accion");
                }
            }).always(function () {
                consultando = false;
            });
        }
    }

    function selec(id) {
        $("#ConsultaClientId").val(id).trigger('change');
    }
</script>