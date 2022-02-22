<div class="cartas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation', 'target' => '_blank')); ?>
    <fieldset>
        <p class="error-message">* Campos obligatorios</p>
        <h2><?php echo __('Ver boleta imposición'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => date("d/m/Y")));
        ?>
    </fieldset>
    <?php echo $this->Form->end(array('label' => __('Ver boleta imposición'), 'style' => 'width:200px')); ?>
</div>
<?php
echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));
?>
<script>
    $(function () {
        $(".dp").datepicker({dateFormatt: 'Y-m-d', maxDate: '0', changeYear: true, yearRange: '2016:+1'});
    });
</script>