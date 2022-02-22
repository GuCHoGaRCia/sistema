<b><?= isset($datospropietario['Propietario']['name']) ? "Consultas Propietario: " . h($datospropietario['Propietario']['name'] . " (" . $datospropietario['Propietario']['unidad'] . ")") : '' ?></b>
<div class="consultas index">
    <span style='margin: 0px 0px 0px 10px;width:24px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100;background-image:url(<?= $this->webroot ?>img/refresh.png);background-repeat:no-repeat;' id="actualizar<?= $pid ?>" title="Recargar chat y adjuntos"></span>
    <?php
    //debug($this->request);
    if (strtolower($this->request->params['controller']) === 'consultaspropietarios') {//muestro el marcar como visto solo en consultaspropietarios del sistema (para el admin), no en Avisos, sino el propiet puede marcarlas como vistas
        ?>
        <script>$("#actualizar<?= $pid ?>").click();</script>
        <span style='margin: 0px 0px 0px 10px;width:32px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="visto" title="Marcar como visto"></span>
        <?php
    }
    ?>
    <?php
    echo "<div id='ConsultaspropietarioConsultas$pid' style='float:left;padding-left:2px;width:60%;height:300px;border:1px solid #CCC;margin-top:9px'></div>";
    echo "<div id='ConsultaspropietarioAdjuntos$pid' style='float:right;padding-left:2px;width:40%;height:300px;border:1px solid #CCC;margin-top:-15px;position:relative;overflow-y:scroll;'></div>";
    echo $this->Form->input('mensaje' . $pid, array('label' => '', 'style' => 'width:100%;height:30px;margin-top:2px'));
    echo $this->Form->input('Adjunto.files.', array(
        'label' => 'Archivos (opcional)',
        'id' => 'archivostxt' . $pid,
        'name' => 'archivostxt' . $pid . '[]',
        'type' => 'file',
        'multiple' => 'multiple',
        'required' => false
    ));
    echo "<div id='progressbar$pid' style='position:relative;float:right;width:30%;top:10px;'></div>";
    echo $this->Form->end(['label' => __('Enviar consulta'), 'id' => 'formc' . $pid, 'style' => 'width:150px']);
    ?>
</div>
<div id="error"></div>
<script>
    var p<?= $pid ?> = '<?= $pid ?>';
    var link<?= $pid ?> = '<?= !isset($id) ? '[]' : "$id" ?>';
    var cl<?= $pid ?> = '<?= $cl ?>';
    $(function () {
        $("#ConsultaspropietarioConsultas<?= $pid ?>").css("overflow", "auto");
        $("#ConsultaspropietarioConsultas<?= $pid ?>").css("overflow-x", "hidden");
        $("#mensaje<?= $pid ?>").focus();
        $("#actualizar<?= $pid ?>").click(function () {
            getData<?= $pid ?>();
            getFiles<?= $pid ?>();
            $("#mensaje<?= $pid ?>").focus();
        });
        $("#actualizar<?= $pid ?>").click();
        $("#visto").click(function () {
            $("#mensaje<?= $pid ?>").focus();
        });
        $("#ConsultaspropietarioConsultas<?= $pid ?>").scrollTop($("#ConsultaspropietarioConsultas<?= $pid ?>")[0].scrollHeight);
        $("#ConsultaspropietarioAdjuntos<?= $pid ?>").scrollTop($("#ConsultaspropietarioAdjuntos<?= $pid ?>")[0].scrollHeight);
<?php
if (isset($_SESSION['Auth']['User']['id']) && $link !== '[]') {// es un administrador viendo las consultas de sus propietarios (puede borrar los adjuntos)
    ?>
            $(document).on('click', '.deladjunto<?= $pid ?>', function () {
                if (confirm('<?= __("Desea eliminar el adjunto?") ?>')) {
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietariosadjuntos/delAdjunto", headers: {"X-Requested-With": "XMLHttpRequest"},
                        cache: false, data: {id: $(this).attr('id'), cli: cl<?= $pid ?>}}).done(function (msg) {
                        getFiles<?= $pid ?>();
                        $("#mensaje<?= $pid ?>").focus();
                    });
                }
            });
    <?php
}
?>
        $("#mensaje<?= $pid ?>").keypress(function (event) {
            if (event.which === 13) {
                $("#formc<?= $pid ?>").click();
            }
        });
        $("#formc<?= $pid ?>").click(function () {
            var arch = document.getElementById("archivostxt<?= $pid ?>");
            if ($("#mensaje<?= $pid ?>").val() === "" && arch.files.length === 0) {
                alert("Debe completar la consulta");
                return false;
            }
            var arch = document.getElementById("archivostxt<?= $pid ?>");
            for (var k = 0; k < arch.files.length; k++) {
                var ext = arch.files[k].name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png']) === -1) {
                    alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg, .png!');
                    return false;
                }
            }
            setData<?= $pid ?>($("#mensaje<?= $pid ?>").val());
            if ($('#archivostxt' + p<?= $pid ?>)[0].files.length > 0) {
                sendFile<?= $pid ?>();
            }
            $("#mensaje<?= $pid ?>").val('').focus();
            document.getElementById("archivostxt<?= $pid ?>").value = "";
        });
        $("#visto").click(function () {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietarios/setUnseen", cache: false,
                headers: {"X-Requested-With": "XMLHttpRequest"},
                data: {pid: '<?= $pid ?>'}}).done(function (msg) {
                $(".seen").hide();
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la acción. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la acción");
                }
            });
        });
        $('#progressbar<?= $pid ?>').progressbar({value: 0});
    });
    function getData<?= $pid ?>() {
        $("#ConsultaspropietarioConsultas<?= $pid ?>").html("<p style='color:green'>Actualizando consultas...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietarios/getConsultas", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {cl: cl<?= $pid ?>, p: p<?= $pid ?><?= !empty($id) ? ",link:'$id'" : "" ?>}}).done(function (msg) {
            $("#ConsultaspropietarioConsultas<?= $pid ?>").html('');
            parse<?= $pid ?>(msg);
            $("#ConsultaspropietarioConsultas<?= $pid ?>").scrollTop($("#ConsultaspropietarioConsultas<?= $pid ?>")[0].scrollHeight);
        });
    }
    function getFiles<?= $pid ?>() {
        $("#ConsultaspropietarioAdjuntos<?= $pid ?>").html("<p style='color:green'>Actualizando listado...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietarios/getArchivos", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {cl: cl<?= $pid ?>, p: p<?= $pid ?><?= !empty($id) ? ",link:'$id'" : "" ?>}}).done(function (msg) {
            $("#ConsultaspropietarioAdjuntos<?= $pid ?>").html('');
            parseFile<?= $pid ?>(msg);
            $("#ConsultaspropietarioAdjuntos<?= $pid ?>").scrollTop($("#ConsultaspropietarioAdjuntos<?= $pid ?>")[0].scrollHeight);
        });
    }
    function setData<?= $pid ?>(c) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietarios/setConsultasPropietario", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {c: c, cl: cl<?= $pid ?>, p: p<?= $pid ?><?= !empty($id) ? ",link:'$id'" : "" ?><?= empty($link) ? ",l:1" : ",l:0" /* agrego l para saber q es respuesta (cuando envio el chat desde el administrador, no desde el panel propietario) */ ?>}}).done(function (msg) {
            $("#ConsultaspropietarioConsultas<?= $pid ?>").html('');
            parse<?= $pid ?>(msg);
            $("#ConsultaspropietarioConsultas<?= $pid ?>").scrollTop($("#ConsultaspropietarioConsultas<?= $pid ?>")[0].scrollHeight);
        });
    }

    function parse<?= $pid ?>(msg) {
        if (msg === '0') {
            alert("El dato es inexistente");
            return false;
        }
        try {
            var obj = JSON.parse(msg);
            for (j = 0; j < obj.length; j++) {
                $("<p style='color:" + (obj[j]['Consultaspropietario']['r'] ? 'green' : 'red') + ";margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + " - " + obj[j]['Consultaspropietario']['m'] + "</p>").prependTo("#ConsultaspropietarioConsultas<?= $pid ?>");
            }
        } catch (err) {
            /* */
        }
    }

    function parseFile<?= $pid ?>(msg) {
        if (msg === '0') {
            alert("El dato es inexistente");
            return false;
        }
        try {
            var obj = JSON.parse(msg);
            for (j = 0; j < obj.length; j++) {
<?php
if (isset($_SESSION['Auth']['User']['id']) && empty($link)) { /* es administrador, lo dejo borrar los adjuntos */
    ?>
                    $("<p style='color:green;margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + " - <a href='<?= $this->webroot ?>Consultaspropietarios/download/" + obj[j]['Consultaspropietariosadjunto']['l'] + "/" + cl<?= $pid ?> + "'>" + obj[j][0]['r'].substr(0, 50) + "...</a><span class='deladjunto<?= $pid ?>' style='background-image:url(<?= $this->webroot ?>img/drop.png);background-repeat:no-repeat;width:16px;height:16px;display:inline-block;margin-left:5px;cursor:pointer;' id='" + obj[j]['Consultaspropietariosadjunto']['id'] + "'></span></p>").prependTo("#ConsultaspropietarioAdjuntos<?= $pid ?>");
    <?php
} else {
    ?>
                    $("<p style='color:green;margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + " - <a href='<?= $this->webroot ?>Consultaspropietarios/download/" + obj[j]['Consultaspropietariosadjunto']['l'] + "/" + cl<?= $pid ?> + "/" + "<?= $link ?>" + "'>" + obj[j][0]['r'].substr(0, 50) + "...</a></p>").prependTo("#ConsultaspropietarioAdjuntos<?= $pid ?>");
    <?php
}
?>
            }
            $('#progressbar' + p<?= $pid ?>).progressbar({value: 0});
        } catch (err) {
            /* */
        }
    }

    function sendFile<?= $pid ?>() {
        var data = new FormData();
        jQuery.each($('#archivostxt' + p<?= $pid ?>)[0].files, function (i, file) {
            data.append('file-' + i, file);
        });
<?php
if (isset($pid)) {
    echo "data.append('p','" . $pid . "');";
}
echo isset($id) ? "data.append('link','" . $id . "');" : '';
?>
        $.ajax({
            url: '<?= $this->webroot ?>Consultaspropietarios/setArchivo',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            headers: {"X-Requested-With": "XMLHttpRequest"},
            type: 'POST',
            xhr: function () {
                myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', updateProgress<?= $pid ?>, false);
                }
                return myXhr;
            },
            success: function (data) {
                $("#ConsultaspropietarioAdjuntos<?= $pid ?>").html('');
                parseFile<?= $pid ?>(data);
                $("#ConsultaspropietarioAdjuntos<?= $pid ?>").scrollTop($("#ConsultaspropietarioAdjuntos<?= $pid ?>")[0].scrollHeight);
                $('#progressbar' + p<?= $pid ?>).progressbar({value: 0});
            }
        });
    }
    function updateProgress<?= $pid ?>(evt) {
        if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbar' + p<?= $pid ?>).progressbar("option", "value", percentComplete);
        }
    }

</script>