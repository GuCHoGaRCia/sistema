<style>
    #propform{font-size:80% !important;line-height:10px}
</style>
<div class="propietarios form" id="propform">
    <?php echo $this->Form->create('Propietario', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <h2><?php echo __('Editar Propietario'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('consorcio_id', array('label' => __('Consorcio') . ' *'));
        echo $this->JqueryValidation->input('code', array('label' => __('Código') . ' *'));
        echo $this->JqueryValidation->input('unidad', array('label' => __('Unidad') . ' *'));
        echo $this->JqueryValidation->input('orden', array('label' => __('Orden')));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->Form->input('email', array('label' => __('Email'), 'type' => 'text'));
        echo $this->JqueryValidation->input('address', array('label' => __('Dirección') . ' *'));
        echo $this->JqueryValidation->input('postal_address', array('label' => __('Dirección postal') . ' *'));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad') . ' *'));
        echo $this->JqueryValidation->input('postal_city', array('label' => __('Ciudad postal') . ' *'));
        echo $this->JqueryValidation->input('telephone', array('label' => __('Teléfono')));
        echo $this->JqueryValidation->input('whatsapp', array('label' => __('WhatsApp')));
        echo $this->JqueryValidation->input('superficie', array('label' => __('Superficie')));
        echo $this->JqueryValidation->input('poligono', array('label' => __('Polígono')));
        echo $this->JqueryValidation->input('cuit', array('label' => __('CUIT')));
        echo $this->JqueryValidation->input('estado_judicial', array('label' => __('Estado judicial')));
        echo $this->JqueryValidation->input('observations', array('label' => __('Observaciones'), 'class' => 'ckeditor'));
        echo $this->JqueryValidation->input('imprime_resumen_cuenta', array('label' => __('Imprime resumen cuenta')));
        echo $this->JqueryValidation->input('sistema_online', array('label' => __('Sistema Online')));
        echo $this->JqueryValidation->input('exceptua_interes', array('label' => __('Exceptúa interés')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Guardar'), 'class' => 'guardar']); ?>
</div>
<script>
    CKEDITOR.replaceAll();
    $(".guardar").on("click", function (event) {
        event.preventDefault();
        envia('<?= $this->request->data['Propietario']['id'] ?>');
    });
</script>