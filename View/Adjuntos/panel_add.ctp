<div class="adjuntos form">
    <?php echo $this->Form->create('Adjunto', array('class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple', 'onsubmit' => "return checkFiles();")); ?>
    <fieldset>
		<p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Agregar Adjunto'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('liquidation_id', array('label' => __('Liquidación')));
        echo "<div id='titulos'></div>";
        echo $this->JqueryValidation->input('Adjunto.files.', array(
            'label' => 'Archivos *',
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo()',
        ));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script type="text/javascript">
    var i = 0;
    function checkFiles() {
        var arch = document.getElementById("archivostxt");
        var ok = true;
        if (arch.files.length === 0 || i === 0) {
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
        return ok;
    }
    function addTitulo() {
        var arch = document.getElementById("archivostxt");
        $("#archivostxt").hide();
        for (var k = 0; k < arch.files.length; k++) {
            var ext = arch.files[k].name.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'txt']) === -1) {
                alert('Los archivos a adjuntar deben ser .doc, .xls, .pdf, .xlsx, .docx, .jpg, .png o .txt!');
				$("#archivostxt").show();
            } else {
                // agrego los input con el titulo y la opcion de borrar para cada adjunto
                var cad = "<span id='arch_" + k + "' title='" + arch.files[k].name + "'><label>T&iacute;tulo del archivo '" + arch.files[k].name + "' *</label><input type='text' name='data[Adjunto][" + k + "][titulo]' id='titulo_" + k + "'/>";
                cad += "<img src='<?php echo $this->webroot; ?>img/0.png' onClick='rem(" + k + ")'><br/></span>";
                $(cad).appendTo("#titulos");
                i++;
            }
        }
    }

    function rem(id) {// elimino el adjunto y sus cosas relacionadas
        $("#arch_" + id).remove();
        i--;
        if (i === 0) {
            $("#archivostxt").show();
        }
    }
</script>