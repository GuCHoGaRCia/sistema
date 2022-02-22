<div class="emails form">
    <?php echo $this->Form->create('Email', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Enviar Emails'); ?></h2>
        <?php
        //echo $this->Form->input('email', ['type' => 'text', 'label' => __('Emails') . ' (Ej: juan@gmail.com o si son varias direcciones, separarlas con coma. Ej: juan@gmail.com,pepe@mail.com)']);
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'empty' => __('Seleccione consorcio...'), 'multiple' => 'multiple'));
        ?>
        Ver/ocultar detalles&nbsp;&nbsp;<a id="ver_ocultar" href="#" style="font-size:14px;" onclick='$("#contlistado").toggle()'> +/-</a>
        <br>
        <div id="contlistado">
            <ul id="contlistado2" style="list-style-type:none">
            </ul>                     
        </div>
        <?php
        echo $this->JqueryValidation->input('asunto', ['label' => __('Asunto')]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('html', ['label' => __('Mensaje'), 'class' => 'ckeditor']);
        ?>
    </fieldset>

    <?php echo $this->Form->end(['id' => 'enviar', 'label' => __('Enviar email')]); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#EmailConsorcioId").select2({language: "es"});
    });
    $("#EmailAddForm").submit(function (event) {
        if ($("input:checkbox:checked").length === 0) {
            alert("<?= __("Debe seleccionar al menos un Propietario") ?>");
            return false;
        }
    });
    $("#EmailConsorcioId").change(function () {
        if ($("#EmailConsorcioId").val() !== "") {
            $("#enviar").hide();
            getData($("#EmailConsorcioId").val());
        } else {
            $("#contlistado2").html('');
            $("#contlistado").hide();
        }
    });
    function getData(c) {
        $("#contlistado2").html("<input type='checkbox' checked=checked onClick=\"for (c in document.getElementsByClassName('til')) document.getElementsByClassName('til').item(c).checked = this.checked\" style=\"cursor:pointer\" />&nbsp;Tildar todos - Destildar todos!<br><br>");
        $.ajax({type: "POST", url: "getPropietarios", cache: false, data: {con: c}}).done(function (msg) {
            try {
                var obj = JSON.parse(msg);
                if (!$.isEmptyObject(obj)) {
                    $.each(obj, function (k, v) {
                        $("#contlistado2").append("<h4>Consorcio " + v[0]['n'] + "</h4><br>");
                        $.each(obj[k], function (l, m) {
                            var n = m['Propietario'];
                            $("#contlistado2").append($("<li><input class='til' type='checkbox' name='t_" + k + "_" + l + "' value='" + n['c'] + "' checked='checked'>&nbsp;&nbsp;" + n['n'] + " (" + n['u'] + ") - <a target='_blank' rel='nofollow noopener noreferrer' href='<?php echo $this->webroot; ?>Avisos/view/" + n['l'] + "'>Ver</a></li>"));
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
</script>