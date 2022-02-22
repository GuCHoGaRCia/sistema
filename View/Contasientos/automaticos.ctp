<div class="contasientos form">
    <?php echo $this->Form->create('Contasiento', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <h2><?php echo __('Generar Asientos Automáticos'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('consorcio_id', ['label' => __('Consorcio'), 'options' => ['0' => 'TODOS LOS CONSORCIOS'] + $consorcios, 'selected' => $consorcio ?? 0]);
        ?>
    </fieldset>
    <?php echo $this->Form->end(['label' => __('Generar Asientos'), 'style' => 'width:200px', 'id' => 'generar']); ?>
</div>
<script>
    $(document).ready(function () {
        $("#ContasientoConsorcioId").select2({language: "es"});
        $("#ContasientoMes").select2({language: "es"});
    });
    $("#ContasientoAutomaticosForm").submit(function (e) {
        $("#generar").prop('disabled', true);
        return true;
    });
</script>