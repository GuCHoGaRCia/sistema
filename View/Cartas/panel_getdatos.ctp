<div class="cartas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <h2><?php echo __('Agregar Carta'); ?></h2>
        <?php
        //echo $this->JqueryValidation->input('client_id', array('label' => __('Seleccione el cliente'), 'empty' => 'Sin asignar'));
        echo $this->JqueryValidation->input('numero', array('label' => __('Datos de la carta')));
        //echo $this->JqueryValidation->input('propietario_id', array('label' => __('Propietario / Unidad')));
        //echo $this->JqueryValidation->input('tipo', array('label' => __('tipo')));
        ?>
    </fieldset>
    <?php echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>
<script type="text/javascript">
    var i = 0;
    $("<input type='hidden' name='cantidad' id='cantidad'/>").attr("value", 0).prependTo("#CartaAddForm");
    var cual = 0;
    $("#CartaNumero").focus();
    $(document).keypress(function (e) {
        if (e.which === 13) {
            codigo = $("#CartaNumero").val();
            if (cual === 0) {// son los datos del cliente, consorcio y propietario
                i++;
                $("<input type='hidden' name='carta[" + i + "][codigo]'/>").attr("value", codigo).prependTo("#CartaAddForm");
                cual = 1;
            }
            if (cual === 1 && codigo.length === 11) { // son los datos de la oblea del correo
                $("<input type='hidden' name='carta[" + i + "][oblea]'/>").attr("value", codigo).prependTo("#CartaAddForm");
                $("#cantidad").attr("value", i);
                cual = 0;
            }
        }
    });

    $("#CartaAddForm").submit(function (event) {
        if ($("#CartaNumero").val() === "") {
            return;
        }
        event.preventDefault();
        $("#CartaNumero").val('').focus();
    });
</script>