<div id="gastosgeneralesadd" class="gastosgeneralesadd">
    <h2><?php echo __('Agregar Gastos Generales'); ?></h2>
    <?php
    echo "<div class='inline'>";
    echo $this->Form->create('GastosGenerale', ['class' => 'inline']);
    echo $this->Form->input('liquidation_id', ['label' => false, 'div' => false, 'options' => $liquidations, 'type' => 'select',
        'selected' => isset($this->request->data['GastosGenerale']['liquidation_id']) ? $this->request->data['GastosGenerale']['liquidation_id'] : 0,
        'empty' => 'Seleccione una liquidación...']);
    echo "&nbsp;&nbsp;<img src='" . $this->webroot . "img/loading.gif' style='display:none;width:30px' id='loading'/>";
    echo $this->Form->end();
    echo "</div>"; // inline
    if (empty($coeficientes)) {
        echo "<div class='info'>Seleccione una Liquidaci&oacute;n </div>";
    } else {
        ?>
        <table id='gas' cellspacing=0 style="font-size:12px;font-family:Lucida Grande,Lucida Sans,Arial,sans-serif;width:90%;display:none" align="center">
            <tr class="noborder">
                <th class="esq_i"></th>
                <th><b>Rubros y conceptos</b></th>
                <?php
                $totalcoeficiente = [];
                $coefdesc = [];
                foreach ($coeficientes as $k => $v) {
                    echo "<th class='coeftit'><b>" . h($v) . "</b></th>";
                    $coefdesc[] = $k;
                    $totalcoeficiente[$k] = 0; // inicializo los totales de todos los coeficientes, para q no quede el total vacio
                    $subtotalcoeficiente[$k] = 0; // para el subtotal en cada rubro
                }
                ?>
                <th class='coeftit'><b>TOTALES</b></th>
                <th style='width:120px'>&nbsp;</th>
                <th class="esq_d"></th>
            </tr>
            <?php
            $totalgeneral = 0;
            $rubrocount = 1;
            foreach ($rubros as $k => $v) {
                $totalrubro = 0;
                $linearubro = "";
                $linearubro .= "<tr id='rubro_$k' class='altrow'><td class='borde_tabla noborder'>&nbsp;</td>";
                $linearubro .= "<td colspan='" . (count($coeficientes) + 3) . "'><b>$rubrocount - " . h($v) . "&nbsp;&nbsp;</b>" . $this->Html->image('new.png', ['alt' => __('Agregar'), 'title' => __('Agregar'), 'style' => 'width:15px;height:15px', 'class' => 'agregar', 'onClick' => "agregar($k,'$v')"]) . "<span class='hand' title='Mostrar/ocultar gastos del rubro' onclick=\"toggle('" . "rubro_$k" . "')\">[+/-]</span></td><td></td>";
                $linearubro .= "</tr>";
                foreach ($gastos as $l => $m) {
                    // el rubro es el actual
                    if ($m['GastosGenerale']['rubro_id'] == $k && $m['GastosGenerale']['habilitado']) {
                        $linearubro .= "<tr class='gastodesc rubro_$k' id='gasto_" . $m['GastosGenerale']['id'] . "'><td>&nbsp;</td><td id='gastodesc_" . $m['GastosGenerale']['id'] . "'>" . $m['GastosGenerale']['description'] . "</td>";
                        //si el coeficiente es el actual
                        $totalgasto = 0;
                        foreach ($coeficientes as $r => $s) {
                            $key = $this->Functions->find2($m['GastosGeneraleDetalle'], ['coeficiente_id' => $r]);
                            //debug($key);die;
                            if (is_array($key)) {//no está (cero)
                                $linearubro .= "<td style='text-align:right;width:120px;' data-val='0' class='coef_$r" . "_" . "$k" . "_" . $m['GastosGenerale']['id'] . "'>" . "0.00&nbsp;</td>";
                            } else {
                                $monto = isset($m['GastosGeneraleDetalle'][$key]['amount']) ? $m['GastosGeneraleDetalle'][$key]['amount'] : 0;
                                $linearubro .= "<td style='text-align:right;width:120px;' data-val='" . $monto . "' class='coef_$r" . "_" . "$k" . "_" . $m['GastosGenerale']['id'] . "'>" . $this->Functions->money($monto) . "&nbsp;</td>";
                                $totalrubro += $monto;
                                $totalgasto += $monto;
                                $subtotalcoeficiente[$r] += $monto;
                                $totalcoeficiente[$r] += $monto;
                            }
                        }
                        $heredable = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if ($m['GastosGenerale']['heredable'] == true) {
                            $heredable = "&nbsp;&nbsp;<span title='El gasto es Heredable'><b>H</b></span>";
                        }
                        $modificado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if ($m['GastosGenerale']['modified'] != $m['GastosGenerale']['created']) {
                            $modificado = "&nbsp;&nbsp;<span title='El gasto fue modificado'><b>M</b></span>";
                        }
                        $abonado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if (in_array($m['GastosGenerale']['id'], array_values($facturaspagas))) {
                            $abonado = "&nbsp;&nbsp;&nbsp;<span title='Las Facturas asociadas ya fueron abonadas'><b>P</b></span>";
                        }
                        $habilitado = $this->Html->link($this->Html->image(($m['GastosGenerale']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Gasto Habilitado'), 'class' => 'mueve', 'id' => 'mueve_' . $m['GastosGenerale']['id'], 'style' => 'width:16px;top:-5px')), array('controller' => 'GastosGenerales', 'action' => 'habilita', $m['GastosGenerale']['id'], $this->request->data['GastosGenerale']['liquidation_id']), ['class' => 'status', 'escape' => false]);
                        $linearubro .= "<td style='text-align:right;width:100px'>" . $this->Functions->money($totalgasto) . "&nbsp;</td>";
                        $linearubro .= "<td class='noborder inline' colspan=2 style='vertical-align:middle'>";
                        $linearubro .= "<span class='editgasto imgmove' title='Editar gasto' onClick='editar(" . $m['GastosGenerale']['id'] . ",$k,\"" . str_replace('"', '', $v) . "\"," . ($m['GastosGenerale']['heredable'] ? 1 : 0) . "," . $m['GastosGenerale']['orden'] . ")'></span>";
                        $linearubro .= "<span class='" . (!empty($m['Proveedorsfactura']) ? 'addfact2' : 'addfact') . " imgmove' title='Agregar factura' id='agregarfactura" . $m['GastosGenerale']['id'] . "' onclick='$(\"#fp\").dialog(\"open\");$(\"#fp\").load(\"" . $this->webroot . "Proveedorsfacturas/add2/" . $this->request->data['GastosGenerale']['liquidation_id'] . "/" . $m['GastosGenerale']['id'] . "\");$(\"#fp\").focus();$(\"#fp\").dialog(\"option\", \"title\", \"Agregar factura proveedor\")'></span>";
                        $linearubro .= "$habilitado$heredable$modificado$abonado<span class='delgasto imgmove' title='Eliminar gasto' id='" . $m['GastosGenerale']['id'] . "'></span></td>";
                        $linearubro .= "</tr>"; // termina el gasto
                    } else {// adfact y addfact2 es para saber si se le asigno o no factura al gasto general
                        //echo "<td>y&nbsp;</td>";
                    }
                }
                // agrego el listado de gastos pendientes de asignar (que pertenecen al consorcio y rubros actuales)
                $pendientes = "";
                $cantpendientes = 0;
                foreach ($gastos as $l => $m) {
                    if ($m['GastosGenerale']['rubro_id'] == $k && !$m['GastosGenerale']['habilitado']) {
                        $pendientes .= "<tr class='gastodesc prubro_$k' style='display:none;background:#e9d7de' id='gasto_" . $m['GastosGenerale']['id'] . "'><td>&nbsp;</td><td id='gastodesc_" . $m['GastosGenerale']['id'] . "'>" . $m['GastosGenerale']['description'] . "</td>";
                        //si el coeficiente es el actual
                        $totalgasto = 0;
                        foreach ($coeficientes as $r => $s) {
                            $key = $this->Functions->find2($m['GastosGeneraleDetalle'], ['coeficiente_id' => $r]);
                            if (is_array($key)) {//no está (cero)
                                $pendientes .= "<td style='text-align:right;width:120px;' data-val='0' class='coef_$r" . "_" . "$k" . "_" . $m['GastosGenerale']['id'] . "'>" . "0.00&nbsp;</td>";
                            } else {
                                $monto = isset($m['GastosGeneraleDetalle'][$key]['amount']) ? $m['GastosGeneraleDetalle'][$key]['amount'] : 0;
                                $totalgasto += $monto;
                                $pendientes .= "<td style='text-align:right;width:120px;' data-val='" . $monto . "' class='coef_$r" . "_" . "$k" . "_" . $m['GastosGenerale']['id'] . "'>" . $this->Functions->money($monto) . "&nbsp;</td>";
                            }
                        }
                        $heredable = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if ($m['GastosGenerale']['heredable'] == true) {
                            $heredable = "&nbsp;&nbsp;<span title='El gasto es Heredable'><b>H</b></span>";
                        }
                        $modificado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if ($m['GastosGenerale']['modified'] != $m['GastosGenerale']['created']) {
                            $modificado = "&nbsp;&nbsp;<span title='El gasto fue modificado'><b>M</b></span>";
                        }
                        $abonado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        if (in_array($m['GastosGenerale']['id'], $facturaspagas)) {
                            $abonado .= "&nbsp;&nbsp;&nbsp;<span title='Las Facturas asociadas ya fueron abonadas'><b>P</b></span>";
                        }
                        $habilitado = $this->Html->link($this->Html->image(($m['GastosGenerale']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Gasto Habilitado'), 'class' => 'mueve', 'id' => 'mueve_' . $m['GastosGenerale']['id'], 'style' => 'width:16px;top:-5px')), array('controller' => 'GastosGenerales', 'action' => 'habilita', $m['GastosGenerale']['id'], $this->request->data['GastosGenerale']['liquidation_id']), ['class' => 'status', 'escape' => false]);
                        $pendientes .= "<td style='text-align:right;width:100px'>" . $this->Functions->money($totalgasto) . "&nbsp;</td>";
                        $pendientes .= "<td class='noborder inline' colspan=2 style='vertical-align:middle'>";
                        $pendientes .= "<span class='editgasto imgmove' title='Editar gasto' onClick='editar(" . $m['GastosGenerale']['id'] . ",$k,\"" . str_replace('"', '', $v) . "\"," . ($m['GastosGenerale']['heredable'] ? 1 : 0) . "," . $m['GastosGenerale']['orden'] . ")'></span>";
                        $pendientes .= "<span class='" . (!empty($m['Proveedorsfactura']) ? 'addfact2' : 'addfact') . " imgmove' title='Agregar factura' id='agregarfactura" . $m['GastosGenerale']['id'] . "' onclick='$(\"#fp\").dialog(\"open\");$(\"#fp\").load(\"" . $this->webroot . "proveedorsfacturas/add2/" . $this->request->data['GastosGenerale']['liquidation_id'] . "/" . $m['GastosGenerale']['id'] . "\");$(\"#fp\").focus();$(\"#fp\").dialog(\"option\", \"title\", \"Agregar factura proveedor\")'></span>";
                        $pendientes .= "$habilitado$heredable$modificado$abonado<span class='delgasto imgmove' title='Eliminar gasto' id='" . $m['GastosGenerale']['id'] . "'></span></td>";
                        $pendientes .= "</tr>"; // termina el gasto pendiente de asignar                        
                        $cantpendientes++;
                    }
                }

                $linearubro .= "<tr id='pendientes_$k'><td class='borde_tabla noborder'>&nbsp;</td><td colspan='2'><span class='hand' title='Mostrar/ocultar gastos del rubro' onclick=\"toggle('" . "prubro_$k" . "')\">[+/-] Gastos pendientes de liquidar [ <span id='cantpendientes_$k'" . ($cantpendientes > 0 ? ' class="pendientes"' : '') . ">$cantpendientes</span> ]</span></td><td class='noborder inline' colspan=2>&nbsp;</td></tr>";
                $linearubro .= $pendientes;

                // totales del rubro (para cada coeficiente)
                $linearubro .= "<tr class='gastodesc totalesrubro' id='totalrubro_$k'><td>&nbsp;</td><td>TOTAL RUBRO</td>";
                foreach ($coeficientes as $r => $s) {
                    $linearubro .= "<td class='totalrubro_$k" . "_" . "$r' data-val='0' data-val2='" . $subtotalcoeficiente[$r] . "' style='text-align:right'>" . $this->Functions->money($subtotalcoeficiente[$r]) . "</td>";
                    $subtotalcoeficiente[$r] = 0; // reinicializo el subtotal del coeficiente de este rubro
                }
                $linearubro .= "<td style='text-align:right' data-val='0' class='sumatotalesrubro_$k'>" . $this->Functions->money($totalrubro) . "</td><td colspan='2'>&nbsp;</td></tr>";
                //if ($totalrubro != 0) { // solo muestro los rubros si tienen gastos asociados
                echo $linearubro;
                //}
                $totalgeneral += $totalrubro;
                $rubrocount++;
            }
            echo "<tr class='totalesrubro'><td class='bottom_i'>&nbsp;</td><td><b>TOTAL GENERAL</b></td>";
            foreach ($coeficientes as $k => $v) {
                echo "<td style='width:80px;text-align:right' class='tg_" . $k . "' data-val='" . $totalcoeficiente[$k] . "'><b>" . $this->Functions->money($totalcoeficiente[$k]) . "</b></td>";
            }
            echo "<td style='text-align:right;width:80px;font-weight:bold' class='tg' data-val='0'>" . $this->Functions->money($totalgeneral) . "</td>";
            echo "<td>&nbsp;</td><td class='bottom_d'>&nbsp;</td></tr><br>";
            ?>
        </table>
        <script>
    <?php
    echo "var facturas=[";
    foreach ($facturas as $k => $v) {
        echo "'" . $k . "',";
    }
    echo "];";
    echo "var facturaspagas=[";
    foreach ($facturaspagas as $k => $v) {
        echo "'" . $k . "',";
    }
    echo "];";
    echo "var coefdesc=[";
    foreach ($coefdesc as $v) {
        echo "'" . h($v) . "',";
    }
    echo "];";
    echo "var rubrodesc=[";
    foreach ($rubros as $k => $v) {
        echo "'" . h($k) . "',";
    }
    echo "];";
    ?>
            var rubro = 0;
            $(function () {
                $('#datepicker').editable({
                    datepicker: {
                        todayBtn: 'linked'
                    }
                });
                var dialog, form;
                var dialog = $("#dialog-form").dialog({
                    autoOpen: false,
                    height: "auto",
                    resizable: false,
                    width: "90%",
                    maxWidth: "768px",
                    modal: true,
                    buttons: {
                        Guardar: function () {
                            $("#dialog-form").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                            $("#agregargasto").submit();
                        },
                        Cancelar: function () {
                            $("#agregargasto")[0].reset();
                            CKEDITOR.instances['dsc'].setData('');
                            dialog.dialog("close");
                            //$("#gasto_" + $("#GastosGeneraleId").val()).css("background-color", "#fff");
                        }
                    },
                    close: function () {
                        if (typeof ($("#agregargasto")[0]) !== "undefined") {
                            $("#agregargasto")[0].reset();
                            CKEDITOR.instances['dsc'].setData('');
                        }
                        //$("#gasto_" + $("#GastosGeneraleId").val()).css("background-color", "#fff");
                    }
                });
                form = $("#agregargasto").on("submit", function (event) {
                    event.preventDefault();
    <?php
    $suma = "if(";
    foreach ($coeficientes as $h => $i) {
        $suma .= "$('#coefadd_$h').val()+";
    }
    $suma .= "0==0){alert('Algún coeficiente debe ser distinto de cero');return false;}";
    echo $suma;
    ?>
                    $("#dialog-form").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', true);
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>GastosGenerales/addGasto", cache: false, data: {
                            l: $("#GastosGeneraleLiquidationId").val(),
                            r: $("#GastosGeneraleRubroId").val(),
                            h: $("#GastosGeneraleHeredable").prop('checked'),
                            d: CKEDITOR.instances['dsc'].getData(),
                            id: $("#GastosGeneraleId").val(),
    <?php
    $x = 0;
    foreach ($coeficientes as $h => $i) {
        echo "c_$h: \$('#coefadd_$h').val(),";
        $x++;
    }
    ?>

                            //liq: $("#GastosGeneraleLiquidationId").val(),
                            ord: $("#GastosGeneraleOrden").val(),
                            x: $("#GastosGeneraleHabilitado").prop('checked')
                        }
                    }).done(function (msg) {
                        var obj = JSON.parse(msg);
                        if (jQuery.isEmptyObject(obj[0])) {<?php /* todo ok!! */ ?>
                            $("#dialog-form").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                            dialog.dialog("close");
                            var pos = 0;
                            //if ($("#GastosGeneraleId").val() !== "0") {<?php /* para cuando es edit */ ?>
                            //pos = $("#gasto_" + obj[1].id).prev().prop('id');
                            //$("#gasto_" + obj[1].id).remove();
                            //}
                            $("#gasto_" + obj[1].id).remove();
                            agregaFila(obj[1], pos);
                            actualizaTotales();
                        } else {
                            $("#dialog-form").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                            alert(obj[0].description);
                        }
                    }).fail(function (jqXHR, textStatus) {
                        $("#dialog-form").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                        if (jqXHR.status === 403) {
                            alert("No se pudo guardar el gasto. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo guardar el gasto, intente nuevamente");
                        }
                    });
                }
                );
            });
            function agregaFila(f, p) {<?php /* f['l'] f['r']  */ ?>
                $(".rubro_" + f['r']).show();
                var cadena = "";
                total = 0;
                $.each(coefdesc, function (i, v) {<?php /* para cada coeficiente */ ?>
                    cadena += "<td style='text-align:right;width:120px;' data-val='" + f['c_' + v] + "' class='coef_" + v + "_" + f['r'] + "_" + f['id'] + "'>" + num(f['c_' + v]) + "&nbsp;</td>";
                    total += parseFloat(f['c_' + v]);
                });
                var her = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                if (f['h'] === "true") {
                    her = "&nbsp;&nbsp;<span title='El gasto es Heredable'><b>H</b></span>";
                }
                var mod = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                if (f['m']) {
                    mod = "&nbsp;&nbsp;<span title='El gasto fue modificado'><b>M</b></span>";
                }
                var abo = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                if (f['p']) {
                    abo = "&nbsp;&nbsp;<span title='Las Facturas asociadas ya fueron abonadas'><b>P</b></span>";
                }
                var clase = (facturas.includes(f['id']) ? 'addfact2' : 'addfact');
                var rubro = (f['x'] === "true" ? 'rubro_' : 'prubro_') + f['r'];<?php /* pendientedeasignar es "prubro_xx" sino "rubro_xx" */ ?>

                var final = "<tr class='gastodesc " + rubro + "' id='gasto_" + f['id'] + "'><td>&nbsp;</td><td id='gastodesc_" + f['id'] + "'>" + f['d'] + "</td>" + cadena;
                final += "<td style='text-align:right;width:120px;'>" + num(total) + "&nbsp;</td><td class='noborder' colspan=2><span class='editgasto imgmove' title='Editar gasto' onClick='editar(" + f['id'] + "," + f['r'] + ",\"\"," + (f['h'] === "true" ? 1 : 0) + "," + f['o'] + ")'></span>";
                final += "<span class='" + clase + " imgmove' title='Agregar factura' id='agregarfactura" + f['id'] + "'  onclick='$(\"#fp\").dialog(\"open\");$(\"#fp\").load(\"<?= $this->webroot ?>Proveedorsfacturas/add2/" + f['l'] + "/" + f['id'] + "\");$(\"#fp\").focus();$(\"#fp\").dialog(\"option\", \"title\", \"Agregar factura proveedor\")'></span>";
                final += "<a href='<?= $this->webroot ?>GastosGenerales/habilita/" + f['id'] + "/" + f['l'] + "' class='status'><img src='<?= $this->webroot ?>img/" + ((f['x'] === "true" ? '1' : '0')) + ".png' class='mueve' id='mueve_" + f['id'] + "' title='Gasto Habilitado' style='width:16px;top:-5px'/></a>";
                final += her + mod + abo + "<span class='delgasto imgmove' title='Eliminar gasto' id='" + f['id'] + "'></span></td></tr>";
                //if (p === "0") {
                if (f['x'] === "false") {
                    tr = $('#totalrubro_' + f['r']);
                    $("#cantpendientes_" + f['r']).html(parseFloat($("#cantpendientes_" + f['r']).html()) + 1);
                } else {
                    tr = $('#pendientes_' + f['r']);
                    $("#cantpendientes_" + f['r']).html(parseFloat($("#cantpendientes_" + f['r']).html()) - 1);
                }
                tr.before(final);
                //} else {
                //    $("#" + p).after(final);<?php /* es una edición, agrego la fila despues del anterior (pos) */ ?>
                //}
                var nueva = $("#gasto_" + f['id']);
                nueva.hide();
                nueva.css({"background-color": "#DFF2BF"}).fadeIn(800);
            }
            function agregar(r, v) {
                rubro = r;
                if (rubro !== 0) {
                    $("#GastosGeneraleRubroId").val(r);
                    $("#GastosGeneraleId").prop('value', 0);<?php /* lo inicializo en cero */ ?>

                    $("#dialog-form").dialog("open");
                    $(".ui-dialog-title").html('Agregar gasto rubro: ' + hhh(v));
                }
            }
            function editar(id, r, v, h, o) {
                //$("#gasto_" + id).css({"background-color": "#DFF2BF"});
                $("#GastosGeneraleRubroId").val(r);
                $("#GastosGeneraleOrden").val(o);
                $("#GastosGeneraleId").prop('value', id);
                $("#GastosGeneraleHeredable").prop('checked', h);
                var src = $("#mueve_" + id).prop('src');
                var n = src.indexOf('1.png');
                $("#GastosGeneraleHabilitado").prop('checked', (n === -1 ? 0 : 1));
                $(".ui-dialog-title").html('Editar gasto rubro: ' + hhh(v));
                $.each(coefdesc, function (i, j) {<?php /* para cada coeficiente seteo el valor q tiene actualmente */ ?>
                    $("#coefadd_" + j).val($(".coef_" + j + "_" + r + "_" + id).data('val'));
                });
                CKEDITOR.instances['dsc'].setData($("#gastodesc_" + id).html());
                $("#dialog-form").dialog("open");
            }
            $(document).on('click', '.delgasto', function () {
                var tr = $(this).closest('tr');
                tr.css("background-color", "#FF3700");
                tr.css("color", "#fff");
                if (confirm('<?= __("Desea eliminar el gasto?") ?>')) {
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>GastosGenerales/delGasto", cache: false, data: {id: $(this).attr('id')}}).done(function (msg) {
                        if (msg === "true") {
                            tr.fadeOut(800, function () {
                                tr.remove();<?php /* borro la fila del gasto */ ?>
                                actualizaTotales();
                            });
                        } else {
                            alert('<?= __("El dato no pudo ser eliminado") ?>');
                            tr.css("background-color", "#fff");
                            tr.css("color", "#333");
                        }
                    }).fail(function (jqXHR, textStatus) {
                        if (jqXHR.status === 403) {
                            alert("No se pudo eliminar el gasto. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo eliminar el gasto, intente nuevamente");
                        }
                    });
                } else {
                    tr.css("background-color", "#fff");
                    tr.css("color", "#333");
                }
            });
    <?php
    /*
     * Esta funcion mueve la fila completa al rubro correspondiente al habilitar/deshabilitar un gasto.
     * Si habilita, lo envia al listado de gastos, sino al listado de gastos pendientes de asignar
     */
    ?>
            $(document).on('click', '.mueve', function () {
                var tr = $(this).closest('tr');
                var clases = tr.attr('class');
                var p = this;
                if (p.src.match('1.png')) {<?php /* va a deshabilitar */ ?>
                    tr.css("background-color", "#e9d7de");
                    var crubro = clases.match(/rubro\_\d+/gi)[0];
                    var idrubro = crubro.replace('rubro_', '');
                    tr.removeClass('rubro_' + idrubro);
                    tr.addClass('prubro_' + idrubro);
                    $("#totalrubro_" + idrubro).before(tr);
                } else {<?php /* va a habilitar */ ?>
                    tr.css("background-color", "#fff");
                    var crubro = clases.match(/prubro\_\d+/gi)[0];
                    var idrubro = crubro.replace('prubro_', '');
                    tr.removeClass('prubro_' + idrubro);
                    tr.addClass('rubro_' + idrubro);
                    $("#pendientes_" + idrubro).before(tr);
                }
                var jj = $(".prubro_" + idrubro).css('display');<?php /* cuando muevo un gasto a pendientes, lo muestro si se muestran los q ya estan. Si los gastos estan ocultos, lo oculto */ ?>
                if (jj === 'none') {
                    tr.hide();
                } else {
                    tr.show();
                }
                actualizaTotales();
            });

            function num(n) {
                var res = Number(n).toFixed(2);
                if (!isNaN(res)) {
                    return "" + res.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                } else {
                    return "0.00";
                }
            }
            function toggle(rubro) {
                $("." + rubro).fadeToggle("slow");<?php /* oculto los gastos del rubro */ ?>

            }
            function actualizaTotales() {
                $.each(rubrodesc, function (index, value) {<?php /* para cada rubro */ ?>
                    $.each(coefdesc, function (i, v) {<?php /* para cada coeficiente inicializo en cero los totales */ ?>
                        $(".totalrubro_" + value + "_" + v).data("val2", 0);
                        $(".tg_" + v).data("val", 0);<?php /* inicializo los totales generales */ ?>

                        $(".tg_" + v).text("0.00");
                    });
                    $(".sumatotalesrubro_" + value).data("val", 0);
                });
                $(".tg").data("val", 0);<?php /* total final general */ ?>
                $.each(rubrodesc, function (index, value) {<?php /* para cada rubro */ ?>
                    $.each(coefdesc, function (i, v) {<?php /* para cada coeficiente .totalrubro_$RUBRO_$COEFICIENTE */ ?>
                        $("[class^=" + "coef_" + v + "_" + value + "_]").each(function (j, k) {<?php /* para cada valor del coeficiente .coef_$COEFICIENTE_$RUBRO de cada gasto .coef_$COEFICIENTE_$RUBRO  */ ?>
    <?php /* Dejar el guion bajo "_]" en la linea de arriba, sino rubro 35 y 3558 los suma doble (paso en menna, bella vista x octubre 2018 */ ?>
                            var tr = $(this).closest('tr');
                            if ($(tr).hasClass('rubro_' + value)) {<?php /* Solo actualizo totales de los gastos que no esten en "pendientes" */ ?>
                                if (typeof $(k).data("val") !== "undefined") {
                                    $(".totalrubro_" + value + "_" + v).data("val2", (parseFloat($(".totalrubro_" + value + "_" + v).data("val2")) + parseFloat($(k).data("val"))));
                                    var m1 = parseFloat($(".tg_" + v).data("val")) + parseFloat($(k).data("val"));
                                    $(".tg_" + v).data("val", m1);
                                    $(".tg_" + v).text(num(m1));
                                }
                            }
                        });

                        $(".totalrubro_" + value + "_" + v).text(num($(".totalrubro_" + value + "_" + v).data("val2")));
                        $(".sumatotalesrubro_" + value).data("val", parseFloat($(".sumatotalesrubro_" + value).data("val")) + parseFloat($(".totalrubro_" + value + "_" + v).data("val2")));
                    });
                    $(".sumatotalesrubro_" + value).text(num($(".sumatotalesrubro_" + value).data("val")));
                    $(".tg").data('val', parseFloat($(".tg").data('val') + parseFloat($(".sumatotalesrubro_" + value).data("val"))));
                });
                $(".tg").text(num($(".tg").data('val')));
                $("[id^=pendientes_]").each(function (j, k) {<?php /* Actualizo la cantidad de gastos pendientes de liquidar */ ?>
                    var id = $(this).prop('id');
                    var num = id.replace('pendientes_', '');
                    var cant = 0;
                    $(".prubro_" + num).each(function (j, k) {
                        cant++;
                    });
                    $("#cantpendientes_" + num).html(cant);
                    if (cant > 0) {
                        $("#cantpendientes_" + num).addClass('pendientes');
                    } else {
                        $("#cantpendientes_" + num).removeClass('pendientes');
                    }
                });
            }
        </script>
        <div id="dialog-form" style='display:none'>
            <div class="gastosGenerales form">
                <?php echo $this->Form->create('GastosGenerale', ['class' => 'jquery-validation', 'id' => 'agregargasto']); ?>
                <fieldset>
                    <p class="error-message" style="font-size:11px">* Campos obligatorios</p>
                    <?php
                    echo $this->Form->input('GastosGenerale.liquidation_id', ['type' => 'hidden', 'id' => 'liquid', 'value' => array_keys($liquidations)[0]]);
                    echo $this->Form->input('GastosGenerale.rubro_id', [/* 'type' => 'hidden', */ 'value' => 0, 'options' => $rubros]);
                    echo $this->Form->input('GastosGenerale.id', ['value' => 0]);
                    echo "<div class='inline' style='max-width:65%;width:65%'>";
                    $c = 0;
                    foreach ($coeficientes as $g => $h) {
                        echo $this->Form->input("GastosGeneraleDetalle.$c.coeficiente_id", ['label' => h($h), 'type' => 'number', 'value' => 0, 'step' => 0.01, 'style' => 'width:100px', 'id' => 'coefadd_' . $g, 'required' => 'required', 'tabindex' => $c + 1, /* 'onblur' => 'checkVal("' . 'coefadd_' . $g . '")' */]);
                        $c++;
                    }
                    if (!empty($distribuciones)) {
                        ?>
                        <div class='distribuirgasto'>
                            <fieldset class=".fieldsetdistr">
                                Distribuir gasto
                            </fieldset>
                            <?php
                            echo $this->JqueryValidation->input('distribuciones', ['label' => __('Distribución'), 'style' => 'width:110px']);
                            echo $this->Form->input('totaldistribucion', ['label' => __('Total'), 'style' => 'width:90px', 'type' => 'number', 'step' => '0.01'/* , 'min' => 0 */]);
                            echo $this->Form->button('Calcular', ['type' => 'button', 'id' => 'calcular']);
                            $cad = $cad2 = "";
                            if (isset($distribucionesDetalle)) {
                                foreach ($distribucionesDetalle as $dkey => $dval) {
                                    $cad .= "distdet['" . $dval['GastosDistribucione']['id'] . "'] = [";
                                    foreach ($dval['GastosDistribucionesDetalle'] as $diskey => $disval) {
                                        $cad .= "'c_" . $disval['coeficiente_id'] . "'" . ",";
                                        $cad2 .= "var c_" . $dval['GastosDistribucione']['id'] . "_" . $disval['coeficiente_id'] . "=" . $disval['porcentaje'] . ";";
                                    }
                                    $cad .= "];";
                                }
                            }
                            ?>
                        </div>
                        <script>
                            var distdet = [];
        <?php echo $cad . $cad2; ?>
                            $("#calcular").click(function () {
                                if ($("#GastosGeneraleTotaldistribucion").val() !== "" && !isNaN($("#GastosGeneraleTotaldistribucion").val())) {
                                    $("input[id^='coefadd_']").each(function (i, v) {<?php /* para cada coeficiente */ ?>
                                        var coefid = v.id.substr(v.id.indexOf("_") + 1);<?php /* el id de uno de los coef de la distribucion */ ?>
                                        var porc = eval("c_" + $("#GastosGeneraleDistribuciones").val() + "_" + coefid);
                                        var total = $("#GastosGeneraleTotaldistribucion").val() * porc / 100;
                                        $("#coefadd_" + coefid).val(parseFloat(total).toFixed(2));
                                    });
                                } else {
                                    alert("Debe ingresar un número decimal");
                                }
                            });
                        </script>
                        <?php
                    }
                    echo "</div>";
                    echo "<div id='fp' style='display:none;margin:0 auto;background:#fff'></div>"; // es el div para la factura
                    $c++;
                    echo "<div class='inline'>";
                    echo $this->JqueryValidation->input('heredable', ['label' => __('Heredable'), 'tabindex' => $c]);
                    $c++;
                    echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado'), 'checked' => 'checked', 'tabindex' => $c, 'title' => 'Si se deshabilita, el Gasto pertenece al Consorcio asociado y queda pendiente de asignar a una Liquidación']);
                    $c++;
                    echo $this->JqueryValidation->input('orden', ['label' => false, 'type' => 'number', 'style' => 'width:70px', 'tabindex' => $c, 'placeholder' => 'Orden', 'title' => 'Orden del Gasto dentro del Rubro']);
                    echo "</div>";
                    echo $this->Html->script('ckeditor/ckeditor');
                    echo $this->JqueryValidation->input('GastosGenerale.description', ['label' => __('Descripción') . ' * ', 'class' => 'ckeditor', 'id' => 'dsc', 'tabindex' => $c + 1]);
                    ?>
                </fieldset>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
        <?php
    }
    ?>
    <script>
        var guardar = function () {
            alert("Debe agregar Proveedores antes de agregar Facturas");
            return false;
        };
<?php
if (!empty($proveedors)) {// muestro el "guardar" solamente si tiene algun proveedor cargado, sino no permito guardar
    ?>
            guardar = function () {
                event.preventDefault();
                if (!checkFiles()) {
                    return false;
                }
                if (document.getElementById("archivostxt").files.length > 0 && !fincompress) {
                    alert("Comprimiendo imagenes, espere un instante y vuelva a intentarlo");
                    return false;
                }
                if ($("#ProveedorsfacturaProveedorId").val() === '') {
                    alert("<?= __('Debe seleccionar un Proveedor') ?>");
                    return false;
                }
                if ($("#ProveedorsfacturaImporte").val() === "" || isNaN($("#ProveedorsfacturaImporte").val())) {
                    alert("<?= __('Debe ingresar el Importe') ?>");
                    $("#ProveedorsfacturaImporte").focus();
                    return false;
                }
                if ($("#ProveedorsfacturaNumero").val() === "") {
                    alert("<?= __('Debe ingresar el Numero de factura') ?>");
                    $("#ProveedorsfacturaNumero").focus();
                    return false;
                }
                $("#load").show();
                $("#archivostxt").prop('disabled', true);
                $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', true);

                var fd = new FormData(document.forms.namedItem("agregarfactura"));
                var x = 0;
                for (var pair of formdata.entries()) {
                    fd.append('file' + x, pair[1]);
                    x++;
                }
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedorsfacturas/add2/" + $("#liquid").val() + "/" + $("#ggid").val(), cache: false, data: fd,
                    contentType: false,
                    processData: false
                }).done(function (msg) {
                    try {
                        var obj = JSON.parse(msg);
                        if (obj.e === 1) {
                            $("#archivostxt").prop('disabled', false);
                        } else {
                            facturas.push($("#ggid").val());<?php /* listado de GG q tienen facturas asociadas */ ?>
                            $("#agregarfactura" + $("#ggid").val()).removeClass('addfact');<?php /* cambio carpeta roja x verde, si es q no estaba verde ya */ ?>
                            $("#agregarfactura" + $("#ggid").val()).addClass('addfact2');
                            $("#gasto_" + $("#ggid").val()).css({"background-color": "#DFF2BF"});<?php /* pongo el gasto verde para q sepan q fue modificado */ ?>
                            dialogfp.dialog("close");
                        }
                        $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                        alert(obj.d);
                    } catch (err) {
                        //
                    }
                }).fail(function (jqXHR, textStatus) {
                    $("#archivostxt").prop('disabled', true);
                    $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', true);
                    if (jqXHR.status === 403) {
                        alert("No se pudo guardar la Factura. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo guardar la Factura, intente nuevamente");
                    }
                });
            }

    <?php
}
?>
        var dialogfp = $("#fp").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: true,
            open: function () {
                $(this).html('<div class="info"><img src="<?= $this->webroot ?>img/loading.gif">Cargando... espere por favor</div>');
            },
            buttons: {
                Guardar: guardar,
                Cerrar: function () {
                    dialogfp.dialog("close");
                }
            }
        });
        function checkVal(id) {
            if (isNaN(parseFloat($("#" + id).val()))) {
                setTimeout(function () {
                    $("#" + id).focus();
                }, 20);
            }
        }
        $(function () {
            $("#GastosGeneraleLiquidationId").select2({language: "es", placeholder: "Seleccione una liquidación..."});
            $("#GastosGeneraleDistribuciones").select2({language: "es"});
            if ($('input[name="manual"]').is(":checked")) {
                $(".coeficiente").slideUp();
                $(".listapropietarios").slideDown();
                $(".importe").slideUp();
            } else {
                $(".listapropietarios").slideUp();
                $(".importe").slideDown();
                $(".coeficiente").slideDown();
            }
            $("#gas").slideToggle('fast');
        });
        $("#GastosGeneraleLiquidationId").on("select2:select", function (e) {
            $("#loading").show();
            $("#GastosGeneraleAddForm").submit();
        });
    </script>
</div>
<style>
    tr{
        line-height:1;
    }
    p{
        margin-top: 0.1em; 
        margin-bottom: 0em;
        padding:0;
    }
    .pendientes{
        color:red;font-weight:bold;font-size:15px;
    }
</style>