<div class="cobranzas form">
    <?php echo $this->Form->create('Carta', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <h2><?php echo __('Agregar gastos por cartas'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha'), 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px'/* , 'value' => date("d/m/Y") */));
        ?>
        <p style='margin: 0px 0px 0px 10px'><img border="0" id="actualizar" src="<?php echo $this->webroot; ?>img/refresh.png" title="<?= __("Verificar cartas enviadas") ?>" style="cursor:pointer;position:relative;float:left;left:100px;top:-27px;"></p>
        <?php
        echo "<div id='Cobranzas' style='float:left;padding-left:2px;width:100%;height:auto;border:1px solid #CCC;'>";
        ?>
        <div id="titulos">
            <ul>
                <li style="padding-right:152px">Consorcio</li>
                <li style="padding-right:146px">Propietario</li>
                <li style="padding-right:35px">Tipo</li>
                <li style="padding-right:79px">C&oacute;digo</li>
                <li style="padding-right:30px">Oblea</li>
                <li style="padding-right:8px">Cargada</li>
            </ul>
            <div id="listado"></div>
        </div>
        <?php
        echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar'));
        echo "</div>";
        ?>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>
<div id="error"></div>
<script>
    var cant = 0;
    $("#Cobranzas").hide();
    $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    $("#actualizar").click(function () {
        $("#listado").html('');
        getData();
    });
    $("#actualizar").click();
    function getData() {
        $("#Cobranzas").show();
        $("<p style='color:green'>Verificando cobranzas...</p>").prependTo("#guardar");
        $.ajax({type: "POST", url: "<?php echo $this->webroot; ?>Cartas/getCartas", cache: false, data: {f: $(".dp").val()}}).done(function (msg) {
            if (msg !== "[]") {
                parse(msg);
            } else {
                $("#listado").html('<br><p style="color:red"><?= __("No hay cartas enviadas en esta fecha") ?></p>');
            }
        });
    }

    function parse(msg) {
        var obj = JSON.parse(msg);
		cant = obj.length;
        if (!$.isEmptyObject(obj)) {
            for (j = 0; j < obj.length; j++) {
                var o = obj[j]['Carta'];
                var fecha = o['f'].substr(-2) + "/" + o['f'].substr(5, 2) + "/" + o['f'].substr(0, 4);
                $("<ul id='ul_" + j + "'></ul>").appendTo("#listado");
                $("<input style='width:215px' disabled value='" + obj[j]['Consorcio']['c'] + "'/>").appendTo("#ul_" + j);
                $("<input style='width:215px' disabled name='data[GastosParticulare][" + j + "][propietario_id]' value='" + obj[j]['Propietario']['p'] + "'/>").appendTo("#ul_" + j);
                $("<input style='width:75px' disabled name='data[GastosParticulare][" + j + "][t]' value='" + obj[j]['Cartastipo']['t'] + "'/>").appendTo("#ul_" + j);
                $("<input style='width:120px' disabled value='" + o['m'] + "'/>").appendTo("#ul_" + j);
                $("<input style='width:80px;text-align:right' disabled value='$ " + parseFloat(o['i']).toFixed(2) + "'/>").appendTo("#ul_" + j);
                $("<input style='width:70px;text-align:right' disabled value='$ " + parseFloat(o['co']).toFixed(2) + "'/>").appendTo("#ul_" + j);
                $("<input style='width:80px;text-align:right' disabled value='$ " + (parseFloat(o['i']) + parseFloat(o['co'])).toFixed(2) + "'/>").appendTo("#ul_" + j);
                $("<img style='margin-left:25px' src='../img/" + (o['ca'] ? 1 : 0) + ".png'/>").appendTo("#ul_" + j);
                $("<input type='checkbox' style='position:absolute;margin-left:80px' name='data[GastosParticulare][" + j + "][gp]'/>").appendTo("#ul_" + j);
            }
        }
    }

    $("#CobranzaAddForm").submit(function (event) {
        if (cant == 0) {
            alert('<?= __("No hay pagos disponibles en esta fecha") ?>');
            return false;
        }
        $(".dp").remove();
    });
</script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/datepicker-es.js"></script>