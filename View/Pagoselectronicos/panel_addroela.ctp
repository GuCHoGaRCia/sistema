<div class="pagoselectronicos form">
    <?php
    if (isset($data)) {
        if ($data != 0) {
            echo "<div class='success'>Se procesaron $data pagos</div>";
        } else {
            echo "<div class='error'>No se encuentran pagos en la fecha seleccionada</div>";
        }
    }
    echo $this->Form->create('Pagoselectronico', ['class' => 'jquery-validation']);
    ?>
    <fieldset>
        <h2><?php echo __('Procesar pagos ROELA'); ?></h2>
        <div class="info">Seleccione una fecha y se procesar&aacute;n los pagos de ROELA de esa fecha</div>
        <div class="warning">NO PROCESAR SI YA FUERON PROCESADOS, SINO SE DUPLICAN!</div>
        <?php
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'autocomplete' => 'off'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Procesar')); ?>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    var img1;
    if (document.images) {
        img1 = new Image();
        img1.src = "/sistema/img/loading.gif";
    }
    $(document).ready(function () {
        $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
    });
    $("#PagoselectronicoPanelAddForm").submit(function (event) {
        if ($(".dp").val() === "") {
            alert("Debe seleccionar una fecha");
            return false;
        }
        $(".submit").html(img1);
        $(".submit").append("<p style='color:red'>Procesando, espere...</p>");
        $(".submit").prepend("<br>");
        return true;
    });
</script>
<style>
    th.ui-datepicker-week-end,
    td.ui-datepicker-week-end {
        display: none;
    }
</style>