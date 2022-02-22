<div class="conttitulos form">
    <?php echo $this->Form->create('Conttitulo', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Agregar Título'); ?></h2>
        <?php
        $lista = [];
        foreach ($conttitulos as $aa => $bb) {
            $lista[$aa] = $tit[$aa];
        }
        echo $this->JqueryValidation->input('conttitulo_id', ['label' => __('Padre'), 'options' => ['0' => 'Ninguno'] + $lista]);
        echo $this->JqueryValidation->input('code', ['label' => __('Código')]);
        echo $this->JqueryValidation->input('titulo', ['label' => __('Título')]);
        echo $this->JqueryValidation->input('orden', ['label' => __('Órden'), 'value' => 0]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(document).ready(function () {
        $("#ConttituloConttituloId").select2({language: "es"});
        $("#ConttituloCode").focus();
    });
</script>