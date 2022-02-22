<div class="info">Verifique que la suma de los coeficientes sea 100%</div>
<div class="gastosDistribuciones form">
    <?php echo $this->Form->create('GastosDistribucione', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Distribuciones de gastos'); ?></h2>
        <?php
        echo $this->Form->input('consorcio_id', ['type' => 'hidden']);
        echo $this->Form->input('nombre', ['type' => 'hidden']);
        $c = 0;
        foreach ($coeficientes as $k => $v) {
            echo $this->Form->input("GastosDistribucionesDetalle.$c.porcentaje", array('label' => __('Porcentaje') . " ($v)", 'type' => 'number', 'value' => 0, 'min' => 0, 'max' => 100, 'step' => 0.01));
            echo $this->Form->input("GastosDistribucionesDetalle.$c.coeficiente_id", array('type' => 'hidden', 'value' => $k));
            $c++;
        }
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {

    });
</script>