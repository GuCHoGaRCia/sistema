<div class="reparaciones form">
    <?php echo $this->Form->create('Reparacione', array('class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple', 'onsubmit' => "return checkFiles();")); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Reparación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario') . ' *', 'empty' => __('Edificio')));
        echo $this->JqueryValidation->input('recordatorio', array('label' => __('Recordatorio') . ' *', 'dateFormat' => 'DMY', 'style' => 'width:98px'));
        echo $this->JqueryValidation->input('concepto', array('label' => __('Concepto') . ' *'));
        echo $this->JqueryValidation->input('reparacionesestado_id', array('label' => __('Estado') . ' *', 'type' => 'select'));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('observaciones', array('label' => __('Observaciones'), 'class' => 'ckeditor'));
        echo "<br><div id='titulos'></div>";
        echo $this->JqueryValidation->input('Reparacionesadjunto.files.', array(
            'label' => false,
            'div' => false,
            //'data-required' => 0,
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo()',
        ));
        echo "<br><div id='listaimagenes'>";
        echo "Click en la imagen para eliminar...<br>";
        $dir = /* $this->webroot . */ '/files' . "/" . $_SESSION['Auth']['User']['client_id'] . "/rep/";
        if (count($this->request->data['Reparacionesadjunto']) > 0) {
            foreach ($this->request->data['Reparacionesadjunto'] as $k => $v) {
                echo "<img data-original='" . $this->webroot . $dir . basename($v['ruta']) . "' title='" . __('Eliminar') . " " . h($v['titulo']) . "' class='lazy propimgdel' id='" . $v['id'] . "' />";
            }
        }
        echo "</div>";
        ?>
    </fieldset>

    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), [], __('Desea cancelar?')); ?>
<script type="text/javascript" src="/sistema/js/ll.js"></script>
<script>
    $(function () {
        $("img.lazy").lazyload();
        $("#ReparacionePropietarioId").select2({language: "es"});
        $("#ReparacioneReparacionesestadoId").select2({language: "es"});
        $("#ReparacioneRecordatorioDay").select2({language: "es"});
        $("#ReparacioneRecordatorioMonth").select2({language: "es"});
        $("#ReparacioneRecordatorioYear").select2({language: "es"});
    });
    $(document).on('click', '.propimgdel', function () {
        var img = $(this);
        img.css('border', '4px solid red');
        if (confirm('<?= __("Desea eliminar la imagen seleccionada?") ?>')) {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Reparaciones/delImagen", cache: false, data: {id: $(this).attr('id')}}).done(function (msg) {
                if (msg === "true") {
                    img.fadeOut(800, function () {
                        img.remove(); // borro la imagen del html
                    });
                } else {
                    alert('<?= __("El dato no pudo ser eliminado") ?>');
                    img.css("border", "0");
                }
            });
        } else {
            img.css("border", "0");
        }
    });
</script>
<style>
    .propimgdel{
        width:150px;padding:3px;cursor:pointer
    }
    #listaimagenes{
        float:right;padding-left:2px;width:70%;height:auto;min-height:100px;border:1px solid #CCC;margin-top:-45px;position:relative;overflow:hidden
    }
</style>
<?php
echo $this->element('adjuntos');
