<div class="consorcios form">
    <?php echo $this->Form->create('Consorcio', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Editar Consorcio'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('id');
        echo $this->JqueryValidation->input('code', array('label' => __('Código') . ' *'));
        echo $this->JqueryValidation->input('name', array('label' => __('Nombre') . ' *'));
        echo $this->JqueryValidation->input('cuit', array('label' => __('CUIT') . ' *'));
        echo $this->JqueryValidation->input('address', array('label' => __('Dirección') . ' *'));
        echo $this->JqueryValidation->input('city', array('label' => __('Ciudad') . ' *'));
        echo $this->JqueryValidation->input('telephone', array('label' => __('Teléfono')));
        echo $this->JqueryValidation->input('interes', array('label' => __('Interés') . ' *'));
        echo $this->JqueryValidation->input('imprime_cod_barras', array('label' => __('Imprime código de barras')));
        echo $this->JqueryValidation->input('imprime_cpe', array('label' => __('Muestra la Clave de Pago Electrónico (PLAPSA)')));
        echo $this->JqueryValidation->input('prorrateagastosgenerales', array('label' => __('Prorratea Gastos Generales')));
        echo $this->JqueryValidation->input('imprimeimportebanco', array('label' => __('Imprime Importe Depósito Bancario Resumen Cuenta')));
        echo $this->JqueryValidation->input('2_cuotas', array('label' => __('Dos cuotas')));
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->JqueryValidation->input('description', array('label' => __('Descripción'), 'class' => 'ckeditor'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>