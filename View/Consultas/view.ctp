<script>
    $(document).ready(function () {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");
        var edge = ua.indexOf('Edge/');
        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./) || edge > 0) {
            $("#todo").html("Utilice <a href='https://www.mozilla.org/es-AR/' target='_blank' rel='nofollow noopener noreferrer'>Mozilla Firefox</a> v40+ o <a href='https://www.google.com/chrome/' target='_blank' rel='nofollow noopener noreferrer'>Google Chrome</a> v40+ para ingresar al sistema");
        }
    });
</script>
<div class="consultas index" id="todo">
    <h4><?php echo __('Consultas') . ' - ' . h($name) ?></h4>
    <?php echo $this->Form->create('Consulta', array('class' => 'jquery-validation')); ?>
    <span style='margin: 0px 0px 0px 10px;width:24px;height:24px;cursor:pointer;position:relative;float:right;right:20px;top:18px;z-index:100' id="actualizar"></span>
    <?php
    echo "<div id='ConsultaConsultas' style='float:left;padding-left:2px;width:60%;height:300px;border:1px solid #CCC;margin-top:9px'></div>";
    echo "<div id='ConsultaAdjuntos' style='float:right;padding-left:2px;width:40%;height:300px;border:1px solid #CCC;margin-top:-15px;position:relative;overflow-y:scroll;'></div>";
    echo $this->Form->input('mensaje', array('label' => '', 'style' => 'width:100%;height:30px;margin-top:2px'));
    echo $this->JqueryValidation->input('Adjunto.files.', array(
        'label' => 'Archivos (opcional)',
        'id' => 'archivostxt',
        'name' => 'archivostxt[]',
        'type' => 'file',
        'multiple' => 'multiple',
    ));
    echo "<div id='progressbar' style='position:relative;float:right;width:100px;top:-30px;'></div>";
    echo $this->JqueryValidation->input('link', array('type' => 'hidden', 'value' => $link));
    echo $this->Form->end(__('Enviar'));
    ?>
</div>
<div id="error"></div>
<?php
/*
  <h4>Envios postales</h4>
  <div class="cartas form">
  <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation')); ?>
  <fieldset>
  <?php
  echo $this->JqueryValidation->input('client_id', ['type' => 'hidden', 'value' => $link]);
  echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")));
  ?>
  </fieldset>
  <?php echo $this->Form->end(array('label' => __('Ver envíos'), 'style' => 'width:200px', 'onclick' => "abre()")); ?>
  </div>
 */
?>
<?php
//echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));
?>
<script>
    $(function () {
        $("#CartaClientId").select2({language: "es"});
        $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
    });
</script>
<script>
    function abre() {
        var str = $("#CartaFecha").val();
        var res = str.replace(/\//gi, "-");
        $('#envios').dialog('open');
        $('#envios').html('<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'<?= $this->webroot . "img/loading.gif" ?>\'/></div>');
        $('#envios').load('<?= $this->webroot . "Cartas/enviosadm/" . $link ?>/' + res);
    }
    $("form").submit(function (e) {
        e.preventDefault();    //para que no me habra otra pestaña al hacer clic en el boton enviar envios
        return false;
    });
    $(document).ready(function () {
        var dialog = $("#envios").dialog({
            autoOpen: false, height: "auto", width: "800", maxWidth: "950",
            position: {at: "center top"},
            closeOnEscape: true,
            modal: true, buttons: {
                Cerrar: function () {
                    $("#envios").html('');
                    dialog.dialog("close");
                }
            }
        });
    });
</script>
<?= "<div id='envios' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; ?>
<script>
    $('#progressbar').progressbar({value: 0});
    $(function () {
        $("#ConsultaConsultas").css("overflow", "auto");
        $("#ConsultaConsultas").css("overflow-x", "hidden");
        $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
        $("#ConsultaMensaje").focus();
        window.setInterval(function () {
            $("#actualizar").click();
        }, 60000);
        $("#actualizar").click(function () {
            getData();
            getFiles();
            $("#ConsultaMensaje").focus();
        });
        $("#actualizar").click();
<?php /*
  //oculto el boton de actualizar en el chrome, se rompe el json (le agrega "No se pudo obtener blabla" como si estuviera deslogueado)
  var br = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
  if (br) {
  $('#actualizar').css("background-image", "none");
  }

 */
?>
    });
    function getData() {
        $("#ConsultaConsultas").html("<p style='color:green'>Actualizando consultas...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/getConsultas", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {cli: $("#ConsultaLink").val()}}).done(function (msg) {
            $("#ConsultaConsultas").html('');
            parse(msg);
            $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        });
    }
    function getFiles() {
        $("#ConsultaAdjuntos").html("<p style='color:green'>Actualizando listado...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/getArchivos", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {cli: $("#ConsultaLink").val()}}).done(function (msg) {
            $("#ConsultaAdjuntos").html('');
            parseFile(msg);
            $("#ConsultaAdjuntos").scrollTop($("#ConsultaAdjuntos")[0].scrollHeight);
        });
    }
    function setData(c) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/setConsulta", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {c: c, cli: $("#ConsultaLink").val()}}).done(function (msg) {
            $("#ConsultaConsultas").html('');
            parse(msg);
            $("#ConsultaConsultas").scrollTop($("#ConsultaConsultas")[0].scrollHeight);
        });
    }

    function parse(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            $("<p style='color:" + (obj[j]['Consulta']['r'] ? 'red' : 'green') + ";margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + " - " + obj[j]['Consulta']['m'] + "</p>").prependTo("#ConsultaConsultas");
        }
    }

    function parseFile(msg) {
        var obj = JSON.parse(msg);
        for (j = 0; j < obj.length; j++) {
            $("<p style='color:green;margin:5px 0px 0px 0px;'>" + obj[j][0]['f'] + " - <a href='<?= $this->webroot ?>Consultas/download/" + obj[j]['Consultasadjunto']['l'] + "/" + $("#ConsultaLink").val() + "'>" + obj[j][0]['r'].substr(0, 50) + "</a></p>").prependTo("#ConsultaAdjuntos");
        }
        $('#progressbar').progressbar({value: 0});
    }
    $("#ConsultaViewForm").submit(function (event) {
        event.preventDefault();
        if ($("#ConsultaMensaje").val() === "") {
            alert("Debe completar la consulta");
            return false;
        }
        setData($("#ConsultaMensaje").val());
        if ($('#archivostxt')[0].files.length > 0) {
            sendFile();
        }
        $("#ConsultaMensaje").val('').focus();
        $('#ConsultaViewForm')[0].reset();
    });
    function sendFile() {
        var data = new FormData();
        jQuery.each($('#archivostxt')[0].files, function (i, file) {
            data.append('file-' + i, file);
        });
        data.append('cli', $("#ConsultaLink").val());
        $.ajax({
            url: '<?= $this->webroot ?>Consultas/setArchivo',
            data: data,
            headers: {"X-Requested-With": "XMLHttpRequest"},
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
    }
    function updateProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbar').progressbar("option", "value", percentComplete);
        }
    }
</script>