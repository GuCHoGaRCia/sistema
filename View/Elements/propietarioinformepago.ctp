<b><?= isset($datospropietario['Propietario']['name']) ? "Informe pago Propietario: " . h($datospropietario['Propietario']['name'] . " (" . $datospropietario['Propietario']['unidad'] . ")") : '' ?></b>
<div class="consultas index">
    <?php
    echo "<div style='float:left;padding-left:2px;width:60%;height:80px;border:1px solid #CCC;margin-top:9px;padding-bottom:15px;overflow-y:scroll;'><span style='position:relative;right:0;text-decoration:underline'>Pagos</span><div style='font-size:11px' id='informepagos$pid'></div></div>";
    echo "<div style='float:right;padding-left:2px;width:40%;height:80px;border:1px solid #CCC;margin-top:9px;position:relative;overflow-y:scroll;padding-bottom:15px'><span style='position:relative;text-align:right;text-decoration:underline'>Comprobantes</span><div style='font-size:11px' id='comprobantepagos$pid'></div></div>";
    echo $this->Form->input('importe' . $pid, ['placeholder' => __('Importe') . ' *', 'label' => false, 'type' => 'text', 'required' => 'required']);
    echo $this->Form->input('fecha' . $pid, ['placeholder' => __('Fecha') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px', 'label' => false, 'class' => 'dp', 'required' => 'required'/*, 'readonly' => 'readonly'*/]);
    echo $this->Form->input('banco' . $pid, ['label' => false, 'type' => 'select', 'options' => $datos['bancos'][$cl]]);
    echo $this->Form->input('formasdepago' . $pid, ['label' => false, 'type' => 'select', 'options' => $formasdepago]);
    echo $this->Form->input('operacion' . $pid, ['placeholder' => __('Nº Operación'), 'label' => false, 'type' => 'text']);
    echo $this->Form->input('observaciones' . $pid, ['placeholder' => __('Observaciones'), 'label' => false, 'type' => 'text']);
    echo $this->Form->input('Adjunto.files.', [
        'label' => 'Adjuntar comprobante',
        'id' => 'archivostxtp' . $pid,
        'name' => 'archivostxtp' . $pid . '[]',
        'type' => 'file',
        'multiple' => 'multiple',
        'required' => false
    ]);
    echo "<div id='progressbarp$pid' style='position:relative;float:right;width:30%;top:10px'></div>";
    echo $this->Form->end(['label' => __('Informar pago'), 'id' => 'formp' . $pid, 'style' => 'width:200px']);
    ?>
</div>
<div id="error"></div>
<script>
    var p<?= $pid ?> = '<?= $pid ?>';
    var link<?= $pid ?> = '<?= !isset($id) ? '[]' : "$id" ?>';
    var cl<?= $pid ?> = '<?= $cl ?>';
    $('#progressbarp<?= $pid ?>').progressbar({value: 0});
    $(function () {
        $("#importe<?= $pid ?>").focus();
<?php
if (isset($_SESSION['Auth']['User']['id']) && $link !== '[]') {// es un administrador viendo las consultas de sus propietarios (puede borrar los adjuntos)
    ?>
            $(document).on('click', '.deladjunto<?= $pid ?>', function () {
                if (confirm('<?= __("Desea eliminar el adjunto?") ?>')) {
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Informepagos/delAdjunto", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false, data: {id: $(this).attr('id'), cli: cl<?= $pid ?>}}).done(function (msg) {
                        getDataP<?= $pid ?>();
                    });
                }
            });
    <?php
}
?>
        $("#formp<?= $pid ?>").click(function () {
            var mensaje = "";
            if ($("#importe<?= $pid ?>").val() === "" || isNaN($("#importe<?= $pid ?>").val())) {
                mensaje += "Debe completar el importe con un importe válido<br>";
            }
            var f1 = $("#fecha<?= $pid ?>").val();
            if ($("#fecha<?= $pid ?>").val() === "" || f1.length !== 10) {
                mensaje += "Debe completar la fecha<br>";
            }
            if ($("#banco<?= $pid ?>").val() === "") {
                mensaje += "Debe seleccionar el banco<br>";
            }
            var arch = document.getElementById("archivostxtp<?= $pid ?>");
<?php
/*
  if (arch.files.length === 0) {
  mensaje += "Debe adjuntar el comprobante<br>";
  }
 */
?>
            if (mensaje !== "") {
                alert(mensaje);
                return false;
            }
            for (var k = 0; k < arch.files.length; k++) {
                var ext = arch.files[k].name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png']) === -1) {
                    alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg o .png!');
                    return false;
                }
            }
            $("#formp<?= $pid ?>").prop('disabled', true);
            $("#formp<?= $pid ?>").val('Informando pago, espere...');
            $("#formp<?= $pid ?>").css('background-image', 'linear-gradient(to bottom, #ff1a00, #ff2a00)');
            setDataP<?= $pid ?>();
            return true;
        });
    });
    function setDataP<?= $pid ?>() {
        var d = new FormData();
        jQuery.each($('#archivostxtp' + <?= $pid ?>)[0].files, function (i, file) {
            d.append('file-' + i, file);
        });
<?php
if (isset($pid)) {
    echo "d.append('p','" . $pid . "');";
}
echo isset($id) ? "d.append('link','" . $id . "');" : '';
?>
        d.append('f', $("#fecha<?= $pid ?>").val());
        d.append('i', $("#importe<?= $pid ?>").val());
        d.append('b', $("#banco<?= $pid ?>").val());
        d.append('fp', $("#formasdepago<?= $pid ?>").val());
        d.append('o', $("#observaciones<?= $pid ?>").val());
        d.append('cl', cl<?= $pid ?>);
        try {
            $.ajax({
                type: "POST",
                url: "<?= $this->webroot ?>Informepagos/setInformePago",
                contentType: false,
                processData: false,
                cache: false,
                headers: {"X-Requested-With": "XMLHttpRequest"},
                data: d,
                xhr: function () {
                    myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', updateProgressp<?= $pid ?>, false);
                    }
                    return myXhr;
                },
                success: function (msg) {
                    if (msg === 'true') {
                        alert("El pago fue informado correctamente");
                        $("input[type='text']").val('');
                        $("#importe<?= $pid ?>").val('')
                        document.getElementById("archivostxtp<?= $pid ?>").value = "";
                    } else {
                        //alert(msg);
                        alert("El pago no pudo ser informado correctamente, intente nuevamente");
                    }
                    $('#progressbarp' + p<?= $pid ?>).progressbar({value: 0});
                    $("#formp<?= $pid ?>").val('Informar');
                    $("#formp<?= $pid ?>").prop('disabled', false);
                    $("#formp<?= $pid ?>").css('background-image', 'linear-gradient(to bottom, #0088CC, #0044CC)');
                    act<?= $pid ?>();
                }}
            );
        } catch (err) {
            //alert(err);
        }
    }
    function getDataP<?= $pid ?>() {
        $("#informepagos<?= $pid ?>").html("<p style='color:green'>Actualizando...</p>");
        $("#comprobantepagos<?= $pid ?>").html("<p style='color:green'>Actualizando...</p>");
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Informepagos/getInformePago", headers: {"X-Requested-With": "XMLHttpRequest"}, cache: false,
            data: {
                cl: cl<?= $pid ?>,
                p: p<?= $pid ?><?= !empty($id) ? ",link:'$id'" : "" ?>,

            }
        }).done(function (msg) {
            $("#informepagos<?= $pid ?>").html('');
            parseP<?= $pid ?>(msg);
            $("#comprobantepagos<?= $pid ?>").html('');
            parseFileP<?= $pid ?>(msg);
        });
    }

    function parseP<?= $pid ?>(msg) {
        if (msg === '0') {
            alert("El dato es inexistente");
            return false;
        }
        try {
            var obj = JSON.parse(msg);
            for (j = 0; j < obj.length; j++) {
                var estado = "";
                var motivo = "title='PAGO PENDIENTE DE PROCESAR'";
                if (obj[j]['Informepago']['v']) {
                    estado = "<img src='<?= $this->webroot ?>img/1.png' title='PAGO ACEPTADO' style='display:inline;width:20px;height:20px'/>";
                    motivo = "title='PAGO ACEPTADO'";
                } else if (obj[j]['Informepago']['r']) {
                    estado = "<img src='<?= $this->webroot ?>img/0.png' title='PAGO RECHAZADO: " + hhh(obj[j]['Informepago']['m']) + "' style='display:inline;width:16px;height:16px'/>";
                    motivo = "title='PAGO RECHAZADO: " + hhh(obj[j]['Informepago']['m']) + "'";
                }
                $("<p " + motivo + " style='margin:5px 0px 0px 0px;" + (obj[j]['Informepago']['r'] ? 'text-decoration:line-through;color:red' : (obj[j]['Informepago']['v'] ? 'color:green' : '')) + "'>" + estado + " " + hhh(obj[j][0]['f'] + " $" + obj[j]['Informepago']['i'] + " " + obj[j]['Formasdepago']['forma'] + " " + obj[j]['Banco']['b'] + " " + obj[j]['Informepago']['o']) + "</p></span>").prependTo("#informepagos<?= $pid ?>");
            }
        } catch (err) {
            //
        }
    }

    function parseFileP<?= $pid ?>(msg) {
        if (msg === '0') {
            alert("El dato es inexistente");
            return false;
        }
        try {
            var obj = JSON.parse(msg);
            var c = ['green', 'blue'];
            for (j = 0; j < obj.length; j++) {
                var adj = obj[j]['Informepagosadjunto'];
                if (adj.length > 0) {//color:" + c[j % 2] + ";
                    var cad = "<p style='margin:5px 0px 0px 0px;'>" + obj[j][0]['f'];
                    for (k = 0; k < adj.length; k++) {
                        cad += " - <a target='_blank' rel='nofollow noopener noreferrer' href='<?= $this->webroot ?>Informepagos/download/" + adj[k]['url'] + "/" + <?= $pid ?> + "/<?= $link ?>" + "' >Compr. " + (k + 1) + "</a>";
                    }
                    cad += "</p>";
                    $(cad).prependTo("#comprobantepagos<?= $pid ?>");
                }
            }
        } catch (err) {
            //
        }
    }

    function updateProgressp<?= $pid ?>(evt) {
        if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            $('#progressbarp' + p<?= $pid ?>).progressbar("option", "value", percentComplete);
        }
    }
    function act<?= $pid ?>() {
        getDataP<?= $pid ?>();
    }

</script>