<div class="comunicaciones form">
    <div class="info">Seleccione uno o m&aacute;s Consorcios, ingrese el asunto, el mensaje y opcionalmente uno o m&aacute;s adjuntos. 
        Un mail personalizado para Propietario de cada Consorcio será enviado una vez presione "Guardar"</div>
    <?php echo $this->Form->create('Comunicacione', ['class' => 'jquery-validation', 'type' => 'file', 'multiple' => 'multiple', 'onsubmit' => "return checkFiles();"]); ?>
    <fieldset>
        <h2><?php echo __('Agregar Comunicación'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        //echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *', 'multiple' => 'multiple'));
        echo $this->JqueryValidation->input('asunto', ['label' => __('Asunto') . " (al asunto se le antepondrá automáticamente el nombre del Consorcio)"]);
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('mensaje', ['label' => __('Mensaje'), 'class' => 'ckeditor']);
        /* echo "<div id='titulos'></div>";
          echo $this->JqueryValidation->input('Adjunto.files.', array(
          'label' => 'Archivos *',
          'id' => 'archivostxt',
          'name' => 'archivostxt[]',
          'type' => 'file',
          'multiple' => 'multiple',
          'onChange' => 'addTitulo()',
          )); */
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(function () {
        $("#ComunicacioneAsunto").focus();
        $("#ComunicacioneConsorcioId").select2({language: "es"});
    });
</script>