<div class="consorcios form">
    <?php echo $this->Form->create('Consorcio', array('class' => 'jquery-validation', /* 'target' => '_blank', */ 'onsubmit' => 'return false;')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h3><?php echo __('Enviar/Imprimir Carta Deudor'); ?></h3>
        <h3><?= h($consorcio['Consorcio']['name']) ?></h3>
        <input id="noenviar" type='checkbox' checked=checked onClick="for (c in document.getElementsByClassName('til'))
                    document.getElementsByClassName('til').item(c).checked = this.checked" style="cursor:pointer" />&nbsp;Tildar todos - Destildar todos!<br>
               <?php
               foreach ($propietarios as $k => $v) {
                   if (isset($saldos[$k])) {
                       $label = h($v['name2']) . " - Saldo: " . $this->Functions->money($saldos[$v['id']]) . " <b title='Cantidad de Liquidaciones adeudadas'>#" . $cantidad[$k] . "</b>";
                       $label .= "&nbsp;" . (!empty($v['email']) ? $this->Html->image('send.png', ['title' => 'Puede enviar la Carta Deudores por email']) : '');
                       echo $this->JqueryValidation->input($k, array('label' => $label, 'id' => "cb" . $k, 'type' => 'checkbox', 'checked' => 'checked', 'class' => 'til'));
                   }
               }
               echo $this->Html->script('ckeditor/ckeditor');
               echo $this->JqueryValidation->input('cartadeudores', array('label' => __('Descripción'), 'class' => 'ckeditor', 'type' => 'textarea', 'value' => $cartadeudores));
               ?>
    </fieldset>
    <style>
        .checkbox label{
            width:600px;
        }
    </style>
    <?php
    echo $this->Form->end();
    echo "<br>";
    echo $this->JqueryValidation->input('Enviar email', array('type' => 'button', 'div' => false, 'id' => 'enviaremail', 'onclick' => 'enviaremail()', 'label' => false));
    echo "&nbsp;&nbsp;&nbsp;";
    echo $this->JqueryValidation->input('Imprimir', array('type' => 'button', 'div' => false, 'id' => 'imprimir', 'onclick' => 'imprimir()', 'label' => false));
    echo "&nbsp;&nbsp;<img src='" . $this->webroot . "img/loading.gif' style='display:none;width:30px' id='loading'/>";
    $url = $this->webroot . "Consorcios/cartadeudores/" . $consorcio['Consorcio']['id'];
    ?>
</div>
<div id="vista"></div>
<script>
    $(function () {
        var dialog1 = $("#vista").dialog({
            autoOpen: false, height: "auto", width: "700", maxWidth: "700",
            position: {at: "top top"},
            closeOnEscape: true,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            modal: true,
            buttons: {
                Cerrar: function () {
                    dialog1.dialog("close");
                }
            }
        });
    });
    function enviaremail() {
        if ($('input[id^="cb"]:checkbox:checked').length === 0) {
            alert("<?= __("Debe seleccionar al menos un Propietario") ?>");
            return false;
        }
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        $("#loading").show();
        $("#enviaremail").prop('disabled', true);
        $("#imprimir").prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Consorcios/cartadeudoresemail",
            data: $("#ConsorcioCartadeudoresForm").serialize()
        }).done(function (msg) {
            alert(msg);
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        }).always(function () {
            $("#loading").hide();
            $("#enviaremail").prop('disabled', false);
            $("#imprimir").prop('disabled', false);
        });

    }
    function imprimir() {
        if ($('input[id^="cb"]:checkbox:checked').length === 0) {
            alert("<?= __("Debe seleccionar al menos un Propietario") ?>");
            return false;
        }
        $('input[id^="cb"]').each(function () {
            if (this.id !== "noenviar" && !this.checked) {<?php /* no envio los vacios o cero */ ?>
                $(this).prop('disabled', true);
            }
        });
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        $("#loading").show();
        $("#enviaremail").prop('disabled', true);
        $("#imprimir").prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "<?= $url ?>",
            data: $("#ConsorcioCartadeudoresForm").serialize(),
            success: function (data) {
                var win = window.open('', '_blank');
                win.document.write(data);
            }
        }).done(function (msg) {

        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        }).always(function () {
            $("#loading").hide();
            $("#enviaremail").prop('disabled', false);
            $("#imprimir").prop('disabled', false);
            $('input[id^="cb"]').each(function () {
                if (this.id !== "noenviar" && !this.checked) {<?php /* no envio los vacios o cero */ ?>
                    $(this).prop('disabled', false);
                }
            });
        });
    }
</script>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>