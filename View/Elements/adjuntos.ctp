<script type="text/javascript">
    var i = 0;
    var formdata = new FormData();
    var fotos = [];<?php /* Para saber el index de las fotos en los archivos que eligio el usuario */ ?>
    var procesadas = 0;
    var fincompress = false;
    var refreshIntervalId = null;

    function checkFiles() {
        var ok = true;
        var arch = document.getElementById("archivostxt");
        if ($("#archivostxt").data('required') && (arch.files.length === 0 || i === 0)) {
            alert('Seleccione uno o mas archivos');
            return false;
        }

        $("input[id^='titulo_']").each(function () {
            if ($(this).val() === "") {
                alert("Ingrese un titulo para el adjunto");
                $(this).focus();
                ok = false;
                return false;
            }
        });
<?php
//formdata.append($(this).prop('name'), $(this).prop('value'));
if (isset($inputs)) {
    foreach ($inputs as $v) {
        ?>
                formdata.append('<?= $v[1] ?>', $("#<?= $v[0] ?>").prop('value'));
        <?php
    }
}
?>
        return ok;
    }

    function compress(source_img_obj) {
        var cvs = document.createElement('canvas');
        cvs.width = source_img_obj.naturalWidth;
        cvs.height = source_img_obj.naturalHeight;
        var ctx = cvs.getContext("2d").drawImage(source_img_obj, 0, 0);
        var newImageData = cvs.toDataURL('image/jpeg', 0.70);<?php /* 0.70 es el radio de compresión */ ?>
        var result_image_obj = new Image();
        result_image_obj.src = newImageData;
        return result_image_obj;
    }

    function urltoFile(url, filename, mimeType) {
        mimeType = mimeType || (url.match(/^data:([^;]+);/) || '')[1];
        return (fetch(url).then(function (res) {
            return res.arrayBuffer();
        }).then(function (buf) {
            return new File([buf], filename, {type: mimeType});
        })
                );
    }

    function addTitulo() {
        i = 0;
        formdata = new FormData();
        fotos = [];<?php /* Para saber el index de las fotos en los archivos que eligio el usuario */ ?>
        procesadas = 0;
        fincompress = false;
        refreshIntervalId = null;
        var arch = document.getElementById("archivostxt");
        if (arch.files.length > 0) {
            $("span[id^='arch_']").remove();
        }
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png']) === -1) {
                alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg o .png!');
            } else {
                if ($.inArray(ext, ['jpg', 'jpeg', 'png']) !== -1) {
                    fotos.push('file' + i);
                }
                formdata.append('file' + i, arch.files[k]);
                var cad = "<span id='arch_" + k + "' title='" + arch.files[k].name + "'><label>T&iacute;tulo del archivo '" + arch.files[k].name + "' *</label>" +
                        "<input type='text' name='data[Adjunto][" + k + "][titulo]' id='titulo_" + k + "' value=''/>";
                cad += "<img src='<?php echo $this->webroot; ?>img/0.png' onClick='rem(" + k + ")'><br/></span>";
                $(cad).appendTo("#titulos");<?php /* agrego los input con el titulo y la opcion de borrar para cada adjunto */ ?>
                i++;
            }
        }
        if (fotos.length > 0) {
            $("#progressbar").progressbar({value: 0}).slideToggle('fast');
        }
        $.each(fotos, function (k, v) {
            var j = arch.files[fotos[k].replace('file', '')];
            reader = new FileReader();
            reader.onload = function (event) {
                var i = new Image();
                i.onload = function () {
                    urltoFile(compress(i).src, j.name, 'image/jpg').then(function (res) {
                        formdata.set(fotos[k], res);
                        procesadas++;
                        if (procesadas === fotos.length) {
                            $("#progressbar").slideToggle('slow');
                        }
                        if ($("#progressbar").length) {
                            var va = procesadas * 100 / fotos.length;
                            $("#progressbar").progressbar({value: va});
                            $("#porc").html(parseFloat(va).toFixed(0) + "%");
                        }
                    });
                };
                i.src = event.target.result;
            };
            reader.readAsDataURL(j);
        });
        refreshIntervalId = setInterval(corte, 500);
    }

    function corte() {
        if (procesadas === fotos.length) {
            fincompress = true;
            clearInterval(refreshIntervalId);
        }
    }

    function rem(id) {<?php /* elimino el adjunto y sus cosas relacionadas */ ?>
        $("#arch_" + id).remove();
        i--;
        $("#progressbar").progressbar({value: 0});
        if (i <= 0) {
            document.getElementById("archivostxt").value = "";
        }
    }
</script>