<div class="cartas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation', 'url' => '/panel/Cartas/envios', 'target' => '_blank')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Envíos del día'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('client_id', array('label' => __('Cliente'), 'options' => ['0' => 'TODOS'] + $clients));
        echo $this->JqueryValidation->input('fecha', array('id' => 'f', 'label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")));
        echo '<br>';
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(array('label' => __('Ver envíos'), 'style' => 'width:200px'));
    echo '<br>';
    ?>
    
    <input type="button" value="Envíos facturados" id="facturados" name="facturados" onclick= "reportefacturados()" />
    <br><br> 
    <input type="button" value="Envíos no facturados" id="nofacturados" name="nofacturados" onclick= "reportenofacturados()" />
    
</div>
<?php
echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));
?>
<?= "<div id='envios' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; ?>

<script>
    $(function () {
        $("#CartaClientId").select2({language: "es"});
        $(".dp").datepicker({dateFormatt: 'Y-m-d', maxDate: '0', changeYear: true, yearRange: '2016:+1'});
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

    function reportefacturados() {
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>panel/Cartas/enviosfacturados",
            data: {f: $("#f").val(), c: $("#CartaClientId").val()}
        }).done(function (msg) {
            try {
                if (msg === '1') {
                    alert('No se encuentran cartas facturadas del dia seleccionado');
                } else {
                    $('#envios').dialog('open');
                    $('#envios').html(msg);
                }
            } catch (err) {
                //
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });

    }
    
    function reportenofacturados() {
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>panel/Cartas/enviosnofacturados",
            data: {f: $("#f").val(), c: $("#CartaClientId").val()}
        }).done(function (msg) {
            try {
                if (msg === '1') {
                    alert('No se encuentran cartas en el dia seleccionado');
                } else {
                    $('#envios').dialog('open');
                    $('#envios').html(msg);
                }
            } catch (err) {
                //
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });

    }   

</script>