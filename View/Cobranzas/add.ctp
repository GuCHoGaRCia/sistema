<div class="cobranzas form">
    <?php echo $this->Form->create('Cobranza', array('class' => 'jquery-validation')); ?>
    <fieldset>
        <h2><?php echo __('Agregar cobranza automática'); ?></h2>
        <?php
        echo $this->JqueryValidation->input('fecha', array('label' => __('Fecha'), 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px'/* , 'value' => date("d/m/Y") */));
        echo $this->JqueryValidation->input('formadepago', array('type' => 'hidden', 'value' => $formadepago));
        $cad = "<b>Archivos de pagos: </b>";
        for ($i = 0; $i < 15; $i++) {
            $ff = APP . WEBROOT_DIR . DS . 'plapsa/' . $_SESSION['Auth']['User']['Client']['code'] . "/" . $_SESSION['Auth']['User']['Client']['code'] . "_RD" . date("Ymd", strtotime("-$i days")) . ".pdf";
            if (file_exists($ff) && filesize($ff) > 0) {
                $cad .= "<u><a target='_blank' rel='nofollow noopener noreferrer' href='https://ceonline.com.ar/sistema/Cobranzas/download/" . $_SESSION['Auth']['User']['Client']['code'] . "_RD" . date("Ymd", strtotime("-$i days")) . ".pdf/" . urlencode($cliente) . "'>" . date("d/m/Y", strtotime("-$i days")) . "</a></u>&nbsp;|&nbsp;";
            }
        }
        ?>
        <p style='margin: 0px 0px 0px 10px'><img border="0" id="actualizar" src="<?php echo $this->webroot; ?>img/refresh.png" title="<?= __("Verificar pagos electrónicos") ?>" style="cursor:pointer;position:relative;float:left;left:100px;top:-27px;"></p>
        <?php
        echo "<div id='Cobranzas' style='float:left;padding-left:2px;width:100%;height:auto;border:1px solid #CCC;'>";
        //echo substr($cad, 0, 26) . (substr($cad, 27, -13) == "" ? '--' : substr($cad, 27, -13)); // le saco el ultimo "&nbsp;|&nbsp;" (13 caracteres)
        echo $cad;
        ?>
        <div id="titulos">
            <ul>
                <li style="padding-right:154px">Consorcio</li>
                <li style="padding-right:147px">Propietario</li>
                <li style="padding-right:35px">Fecha</li>
                <li style="padding-right:80px">Medio</li>
                <li style="padding-right:43px">Saldo</li>
                <li style="padding-right:44px">Pago</li>
                <li style="padding-right:12px">&nbsp;&nbsp;Guardar pago</li>
                <li style='background:none'>&nbsp;<?= "<img title='Tildar/Destildar todos' src='" . $this->webroot . 'img/1.png' . "' style='cursor:pointer' onClick=\"" . "$('.til').attr('checked', !$('.til').attr('checked'))" . "\" />"; ?></li>
            </ul>
            <div id="listado"></div>
            <div id="guardando" style="display:none">
                <br><br>Guardando las cobranzas, espere por favor...<img src="<?php echo $this->webroot; ?>img/loading.gif"/>
            </div>
        </div>
        <?php
        echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar'));
        echo "</div>";
        ?>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<div id="error"></div>
<script>
    var cant = 0;
    $("#Cobranzas").hide();
    $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
    $("#actualizar").click(function () {
        $("#listado").html('');
        getData();
    });
    $("#actualizar").click();

    function getData() {
        $("#Cobranzas").show();
        $("<p class='warning' style='width:400px;font-weight:bold'>Verificando cobranzas, espere unos instantes... <img src='<?php echo $this->webroot ?>img/loading.gif' /></p>").prependTo("#listado");
        $.ajax({type: "POST", url: "<?php echo $this->webroot; ?>Pagoselectronicos/getCobranzas", cache: false, data: {f: $(".dp").val()}}).done(function (msg) {
            $("#listado").html(msg);
            if (msg !== '{"pe":[],"c":[],"p":[],"b":[],"s":[],"tl":[]}') {
                $("#listado").html('');
                parse(msg);
            } else {
                $("#listado").html('<br><p class="info"><?= __("No hay pagos disponibles en esta fecha (o ya fueron guardados anteriormente)") ?></p>');
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    }

    function parse(msg) {
        try {
            var obj = JSON.parse(msg);
            var pe = obj["pe"];
            var c = obj["c"];
            var p = obj["p"];
            var b = obj["b"];
            var s = obj["s"];
            var tl = obj["tl"];
            if (pe.length > 0) {
                for (j = 0; j < pe.length; j++) {
                    var o = pe[j]['p'];
                    var cuentaBancaria = pe[j]['bc'];
                    var lt = pe[j]['lt'];
                    var fecha = o['f'].substr(-2) + "/" + o['f'].substr(5, 2) + "/" + o['f'].substr(0, 4);
                    $("<ul id='ul_" + j + "'></ul>").appendTo("#listado");
                    $("<input style='width:225px' disabled value='" + c[o["cc"]] + "'/>").appendTo("#ul_" + j);
                    $("<input style='width:225px;" + (typeof p[o["cc"]][o["pc"]] === "undefined" ? 'color:red;font-weight:bold' : '') + "' disabled value='" + (typeof p[o["cc"]][o["pc"]] !== "undefined" ? p[o["cc"]][o["pc"]] : 'Unidad indefinida ' + o["pc"]) + " (TL: " + tl[lt] + ")'/>").appendTo("#ul_" + j);
                    $("<input style='width:85px' disabled value='" + fecha + "'/>").appendTo("#ul_" + j);
                    $("<input style='width:125px' disabled value='" + o['m'] + "'/>").appendTo("#ul_" + j);
                    if (cuentaBancaria) {
                        $("<input style='width:90px;text-align:right' disabled value='$ " + parseFloat(s[lt][o["cc"]][o["pc"]]).toFixed(2) + "'/>").appendTo("#ul_" + j);<?php /* Saldo */ ?>
                    } else {
                        $("<input style='width:90px;text-align:right' disabled value='--'/>").appendTo("#ul_" + j);
                    }

                    $("<input style='width:85px;text-align:right' disabled value='$ " + parseFloat(o['i'] - o['co']).toFixed(2) + "'/>").appendTo("#ul_" + j);<?php /* Pago. Para multiplataforma comentar - o['co'] aca y 3 lineas mas abajo */ ?>
                    if (cuentaBancaria) {
                        $("<input type='checkbox' class='til' style='position:absolute;margin-left:48px' name='data[Cobranza][" + j + "][gp]'/>").appendTo("#ul_" + j);<?php /* Tilde */ ?>
                        if (Math.abs(parseFloat(s[lt][o["cc"]][o["pc"]]).toFixed(2) - parseFloat(o['i'] - o['co']).toFixed(2)) > 1) {
                            $("<img src='<?= $this->webroot ?>img/0.png' title='El pago difiere del saldo en más de $1' />").appendTo("#ul_" + j);
                        }
                    } else {
                        $("<img src='<?= $this->webroot ?>img/warning.png' class='imgmove' style='margin-left:39px' title='El Consorcio no posee Cuenta Bancaria asociada' />").appendTo("#ul_" + j);
                    }
<?php
// consorcio_code#propietario_code#bancoscuenta_id#pagoelectronicoid#saldo
?>
                    if (cuentaBancaria) {
                        $("<input type='hidden' name='data[Cobranza][" + j + "][data]' value='" + o['cc'] + "#" + o['pc'] + "#" + b[o["cc"]] + "#" + o['id'] + "#" + s[lt][o["cc"]][o["pc"]] + "'/>").appendTo("#ul_" + j);
                        cant++;
                    }
                }
            }
        } catch (e) {
            alert(e);
            return;
        }
    }

    $("#CobranzaAddForm").submit(function (event) {
        if (cant == 0) {
            alert('<?= __("No hay Pagos automáticos (válidos) disponibles en esta fecha") ?>');
            return false;
        }
        $(".dp").remove();
        $("#listado").hide();
        $("#guardar").hide();
        $("#guardando").show();
    });
</script>
<style>
    th.ui-datepicker-week-end,
    td.ui-datepicker-week-end {
        display: none;
    }
</style>