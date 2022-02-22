<div class="proveedorsfacturas index">
    <h2><?php echo __('Facturas de proveedores'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0' id='noimprimir'>";
    echo $this->Form->create('Proveedorsfactura', ['class' => 'inline', 'method' => 'post', 'id' => 'form']);
    echo $this->JqueryValidation->input('proveedor_id', ['label' => false, 'empty' => '', 'options' => $proveedores, 'required' => false, 'type' => 'select', 'selected' => isset($p) ? $p : '']);
    echo $this->JqueryValidation->input('consorcio_id', ['label' => false, 'empty' => '', 'options' => $consorcios, 'required' => false, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->JqueryValidation->input('buscar', ['label' => false, 'type' => 'text', 'style' => 'width:100px', 'placeholder' => 'Buscar...', 'value' => h($b)]);
    //echo $this->Form->input('pagas', ['label' => __('Incluir pagas y NC?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    //echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;']);
    echo $this->Form->end(['label' => __('Ver'), 'id' => 'ver', 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:80%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'print' => true, 'model' => 'Proveedorsfactura']) . "</div></div>";
    //debug($proveedorsfacturas);
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            FACTURAS PENDIENTES - <?= h((isset($proveedores[$p]) ? $proveedores[$p] : 'Todos los Proveedores') . " - " . (isset($consorcios[$c]) ? $consorcios[$c] : 'Todos los Consorcios')) ?>
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo __('Proveedor') ?></th>
                    <th><?php echo __('Consorcio') ?></th>
                    <th><?php echo __('Concepto') ?></th>
                    <th><?php echo __('Fecha') ?></th>
                    <th style="text-align:right"><?php echo __('Número') ?></th>
                    <th style="text-align:right"><?php echo __('Importe') ?></th>
                    <th style="text-align:right"><?php echo __('Saldo') ?></th>
                    <th class="acciones" style="width:130px"><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $espost = ($this->request->is('post') && (!empty($p) || !empty($c)));
                $first = true; // la primera vez muestro el nombre del proveedor, sino no lo muestra
                $total = $saldo = $subtotal = $subsaldo = $consor = $proveedor = 0;
                $mostro = false;
                foreach ($proveedorsfacturas as $proveedorsfactura) {
                    $saldo += $proveedorsfactura['Proveedorsfactura']['saldo'];
                    $total += $proveedorsfactura['Proveedorsfactura']['importe'];
                    if ($espost) {
                        if (empty($c)) {// cuando no selecciona consorcio
                            if ($consor == 0) {
                                $consor = $proveedorsfactura['Consorcio']['id'];
                            } else if ($consor != $proveedorsfactura['Consorcio']['id']) {
                                $consor = $proveedorsfactura['Consorcio']['id'];
                                ?>
                                <tr class="altrow">
                                    <td class="borde_tabla"></td>
                                    <td colspan="4">&nbsp;</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?></td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subsaldo) ?></td>
                                    <td>&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <tr>
                                    <td class="borde_tabla"></td>
                                    <td colspan="8">&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <?php
                                $mostro = true;
                                $subtotal = $subsaldo = 0;
                            }
                        }
                        if (empty($p)) {// cuando no selecciona proveedor
                            if ($proveedor == 0) {
                                $proveedor = $proveedorsfactura['Proveedorsfactura']['proveedor_id'];
                            } else if ($proveedor != $proveedorsfactura['Proveedorsfactura']['proveedor_id']) {
                                $proveedor = $proveedorsfactura['Proveedorsfactura']['proveedor_id'];
                                ?>
                                <tr class="altrow">
                                    <td class="borde_tabla"></td>
                                    <td colspan="4" style="border:none">&nbsp;</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?></td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subsaldo) ?></td>
                                    <td>&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <tr>
                                    <td class="borde_tabla"></td>
                                    <td colspan="8">&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <?php
                                $mostro = true;
                                $subtotal = $subsaldo = 0;
                            }
                        }
                    }

                    $subsaldo += $proveedorsfactura['Proveedorsfactura']['saldo'];
                    $subtotal += $proveedorsfactura['Proveedorsfactura']['importe'];

                    $class = $proveedorsfactura['Proveedorsfactura']['saldo'] == 0 ? ' class="success-message"' : ($proveedorsfactura['Proveedorsfactura']['saldo'] != abs($proveedorsfactura['Proveedorsfactura']['importe']) ? ' style="color:orange;font-weight:bold"' : ' class="error-message"');
                    ?>
                    <tr<?php echo $class; ?> style="border-bottom:1px dotted gray">
                        <td class="borde_tabla"></td>
                        <td><?php echo ($mostro || $first || !$espost ? h($proveedorsfactura['Proveedor']['name']) : '') ?>&nbsp;</td>
                        <td><?php echo h($proveedorsfactura['Consorcio']['name']) ?></td>
                        <?php
                        if (/* $proveedorsfactura['Liquidation']['bloqueada'] == 0 && */$proveedorsfactura['Proveedorsfactura']['saldo'] == abs($proveedorsfactura['Proveedorsfactura']['importe'])) {
                            ?>
                            <td><span class="concepto" data-value= "<?= h($proveedorsfactura['Proveedorsfactura']['concepto']) ?>" data-pk="<?= h($proveedorsfactura['Proveedorsfactura']['id']) ?>"><?= h(substr($proveedorsfactura['Proveedorsfactura']['concepto'], 0, 100)) . (strlen($proveedorsfactura['Proveedorsfactura']['concepto']) > 100 ? '...' : '') ?></span></td>
                            <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $proveedorsfactura['Proveedorsfactura']['created']) ?>'>
                                <span class="fecha" data-value= "<?= h($proveedorsfactura['Proveedorsfactura']['fecha']) ?>" data-pk="<?= h($proveedorsfactura['Proveedorsfactura']['id']) ?>">
                                    <?php echo $this->Time->format(__('d/m/Y'), $proveedorsfactura['Proveedorsfactura']['fecha']) ?>
                                </span>
                            </td>
                            <td style="text-align:right"><span class="numero" data-value= "<?= h($proveedorsfactura['Proveedorsfactura']['numero']) ?>" data-pk="<?= h($proveedorsfactura['Proveedorsfactura']['id']) ?>"><?php echo h($proveedorsfactura['Proveedorsfactura']['numero']) ?></span></td>
                            <td style="text-align:right"><span class="importe" data-value= "<?= h($proveedorsfactura['Proveedorsfactura']['importe']) ?>" data-pk="<?= h($proveedorsfactura['Proveedorsfactura']['id']) ?>"><?php echo $this->Functions->money($proveedorsfactura['Proveedorsfactura']['importe']) ?></span></td>
                            <?php
                        } else {
                            ?>
                            <td><?= h($proveedorsfactura['Proveedorsfactura']['concepto']) ?></td>
                            <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $proveedorsfactura['Proveedorsfactura']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $proveedorsfactura['Proveedorsfactura']['fecha']) ?></td>
                            <td style="text-align:right"><?php echo h($proveedorsfactura['Proveedorsfactura']['numero']) ?></td>
                            <td style="text-align:right"><?php echo $this->Functions->money($proveedorsfactura['Proveedorsfactura']['importe']) ?></td>
                            <?php
                        }
                        ?>
                        <td style="text-align:right"><?php echo $this->Functions->money($proveedorsfactura['Proveedorsfactura']['saldo']) ?></td>
                        <td class="acciones" style="width:130px;padding-left:10px">
                            <?php
                            echo $this->Html->image((isset($proveedorsfactura['Proveedorsfactura']['gastos_generale_id']) && $proveedorsfactura['Proveedorsfactura']['gastos_generale_id'] == 0 ? 'view.png' : 'view2.png'), ['title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => '$("#viewf").dialog("open");$("#viewf").html("<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'' . $this->webroot . 'img/loading.gif' . '\'/></div>");$("#viewf").load("' . $this->webroot . 'Proveedorsfacturas/view/' . $proveedorsfactura['Proveedorsfactura']['id'] . '");']);
                            if ($proveedorsfactura['Proveedorsfactura']['saldo'] == abs($proveedorsfactura['Proveedorsfactura']['importe'])) {
                                echo $this->Html->image((empty($proveedorsfactura['Proveedorsfacturasadjunto']) ? 'factura.png' : 'factura2.png'), ['title' => 'Agregar factura digital', 'id' => 'fimg' . $proveedorsfactura['Proveedorsfactura']['id'], 'style' => 'cursor:pointer', 'onclick' => 'fid=' . $proveedorsfactura['Proveedorsfactura']['id'] . ';$("#addfd").dialog("open");$("#addfd").html("<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'' . $this->webroot . 'img/loading.gif' . '\'/></div>");$("#addfd").load("' . $this->webroot . "Proveedorsfacturas/addfd/" . $proveedorsfactura['Proveedorsfactura']['id'] . '");$("#addfd").focus()']);
                            }
                            if ($proveedorsfactura['Proveedorsfactura']['importe'] > 0 && $proveedorsfactura['Proveedorsfactura']['saldo'] > 0) {//solo facturas (nc no)
                                echo $this->Form->postLink($this->Html->image('liquidation.png', array('alt' => __('Pagar en efectivo'), 'title' => __('Pagar en efectivo'))), array('action' => 'pagarEfectivo', $proveedorsfactura['Proveedorsfactura']['id']), array('escapeTitle' => false), __('Pagar en efectivo # %s?', "$ " . $proveedorsfactura['Proveedorsfactura']['importe'] . " de " . $proveedorsfactura['Proveedorsfactura']['concepto']));
                            }
                            if ($proveedorsfactura['Proveedorsfactura']['saldo'] == abs($proveedorsfactura['Proveedorsfactura']['importe'])) {
                                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $proveedorsfactura['Proveedorsfactura']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $proveedorsfactura['Proveedorsfactura']['concepto']));
                            }
                            ?>
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <?php
                    if ($mostro) {
                        $mostro = false;
                    }
                    $first = false;
                }
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4">&nbsp;</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?></td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subsaldo) ?></td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4">&nbsp;</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">TOTAL</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($total) ?></td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($saldo) ?></td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="8"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
    </div>
    <?php
    if (!$espost) {
        echo $this->element('pagination');
    }
    ?>
</div>
<script>
    var fid;
    $(function () {
        $("#ProveedorsfacturaProveedorId").select2({language: "es", placeholder: "<?= __('Seleccione proveedor...') ?>", allowClear: true});
        $("#ProveedorsfacturaConsorcioId").select2({language: "es", placeholder: "<?= __('Seleccione consorcio...') ?>", allowClear: true});
    });
    $("#ver").click(function (event) {
        $("#form").submit();
    });
    $(function () {
        $('.concepto').editable({type: 'text', name: 'concepto', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedorsfacturas/editar', placement: 'right'});
        $('.fecha').editable({type: 'date', name: 'fecha', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedorsfacturas/editar', placement: 'left'});
        $('.numero').editable({type: 'text', name: 'numero', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedorsfacturas/editar', placement: 'left'});
        $('.importe').editable({type: 'text', name: 'importe', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Proveedorsfacturas/editar', placement: 'left'});
        var dialog1 = $("#viewf").dialog({
            autoOpen: false, height: "auto", width: "700", maxWidth: "700",
            position: {at: "top top"},
            closeOnEscape: true,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            modal: true,
            buttons: {
                Cerrar: function () {
                    dialog1.dialog("close");
                }
            }
        });
        var dialog2 = $("#addfd").dialog({
            autoOpen: false, height: "auto", width: "700", maxWidth: "700",
            position: {at: "top top"},
            closeOnEscape: true,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            modal: true,
            buttons: {
                Guardar: function () {
                    event.preventDefault();
                    if (!checkFiles()) {
                        return false;
                    }
                    if (document.getElementById("archivostxt").files.length > 0 && !fincompress) {
                        alert("Comprimiendo imagenes, espere un instante y vuelva a intentarlo");
                        return false;
                    }
                    $("#load").show();
                    $("#archivostxt").prop('disabled', true);
                    $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', true);

                    var fd = new FormData(document.forms.namedItem("agregaadjunto"));
                    var x = 0;
                    for (var pair of formdata.entries()) {
                        fd.append('file' + x, pair[1]);
                        x++;
                    }
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedorsfacturas/addfd/" + fid, cache: false, data: fd,
                        contentType: false,
                        processData: false
                    }).done(function (msg) {
                        try {
                            var obj = JSON.parse(msg);
                            if (obj.e === 1) {
                                $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                            } else {
                                $("#fimg" + fid).prop('src', '/sistema/img/factura2.png');<?php /* cambio roja x verde, si es q no estaba verde ya */ ?>
                                $("#addfd").html("<div class='info' style='width:200px;margin:0 auto'>Cargando...<img src='<?= $this->webroot ?>img/loading.gif' /></div>");
                                $("#addfd").load("<?= $this->webroot ?>Proveedorsfacturas/addfd/" + fid);
                                $("#addfd").focus()
                            }
                            alert(obj.d);
                        } catch (err) {
                            $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                        }
                    }).fail(function (jqXHR, textStatus) {
                        $("#fp").parent().find('.ui-dialog-buttonset button:eq(0)').prop('disabled', false);
                        if (jqXHR.status === 403) {
                            alert("No se pudo guardar la Factura. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo guardar la Factura, intente nuevamente");
                        }
                    });
                },
                Cerrar: function () {
                    dialog2.dialog("close");
                }
            }
        });
    });
</script>
<style>
    @media print{
        .titulo{display:inline-block !important}
        .acciones{display:none;}
        table thead{line-height:10px}
        #seccionaimprimir,.seccionaimprimir{
            position: absolute;
            left:0;
            top:0;
        }
        @page{
            size:auto;
            margin:10px;
            margin-bottom:0;
        }
        table{
            font-size:14px !important;
            font-weight:400 !important;
        }
        a:link:after, a:visited:after{    
            content:"" !important;    
            font-size:90% !important; 
        }
        img{
            display:none;
        }
    }
</style>
<?php
echo "<div id='viewf' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>";
echo "<div id='addfd' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>";
?>