<div class="adjuntos form">
    <?php
    echo $this->Form->create('Adjunto', array('class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple'));
    ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Adjunto'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('liquidation_id', array('label' => false, 'empty' => ''));
        ?>
        <div id="progressbar" style='display:none;width:220px;margin-top:5px'><span style="position:relative;float:left;font-size:12px;font-weight:bold;margin-top:7px">Comprimiendo imagenes... <span id="porc">0%</span></span></div>
        <?php
        echo "<div id='titulos'></div>";
        echo $this->JqueryValidation->input('Adjunto.files.', array(
            'label' => 'Archivos *',
            'div' => false,
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'data-required' => true,
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo();'
        ));
        echo $this->JqueryValidation->input('imprimir', array('label' => 'Imprimir'));
        echo $this->JqueryValidation->input('poneronline', array('label' => 'Online', 'checked' => 'checked'));
        ?>
    </fieldset>
    <?php echo "<div class='inline'>" . $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
    ?>
</div>
<div id="test"></div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $("#AdjuntoAddForm").submit(function (event) {
        event.preventDefault();
        if (!checkFiles()) {
            return false;
        }
        if (document.getElementById("archivostxt").files.length > 0 && !fincompress) {
            alert("Comprimiendo imagenes, espere un instante y vuelva a intentarlo");
            return false;
        }
        if ($("#AdjuntoLiquidationId :selected").val() === "") {
            alert("Seleccione una Liquidación");
            return false;
        }
        if (!$("#AdjuntoPoneronline").is(':checked') && !$("#AdjuntoImprimir").is(':checked')) {
            alert("Seleccione Imprimir y/o Online");
            return false;
        }
        $("#load").show();
        $("#archivostxt").prop('disabled', true);
        $("#guardar").prop('disabled', true);
        var fd = new FormData(this);
        var x = 0;
        for (var pair of formdata.entries()) {
            fd.append('file' + x, pair[1]);
            x++;
        }
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Adjuntos/add",
            data: fd,
            contentType: false,
            processData: false
        }).done(function (msg) {
            window.location.replace("<?= $this->webroot ?>Adjuntos");
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
            $("#guardar").prop('disabled', false);
        });
    });

    $(function () {
        $("#AdjuntoLiquidationId").select2({language: "es", placeholder: "<?= __("Seleccione la liquidación... *") ?>"});
    });
</script>
<?php echo $this->element('adjuntos'); ?>