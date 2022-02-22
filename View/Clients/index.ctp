<div class="clients index">
    <h2><?php echo __('Clientes'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => false, 'pagenew' => false, 'model' => 'Client')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Administración')); ?></th>
                <th><?php echo $this->Paginator->sort('code', __('Código')); ?></th>
                <th><?php echo $this->Paginator->sort('cuit', __('CUIT')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('telephone', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('whatsapp', __('WhatsApp')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('numeroregistro', __('Matrícula')); ?></th>
                <th><?php echo $this->Paginator->sort('web', __('Web')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Configuración'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($clients as $client):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($client['Client']['name']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['name']) ?></span>&nbsp;</td>
                    <td><?php echo h($client['Client']['code']) ?>&nbsp;</td>
                    <td><span class="cuit" data-value="<?php echo h($client['Client']['cuit']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['cuit']) ?></span>&nbsp;</td>
                    <td><span class="address" data-value="<?php echo h($client['Client']['address']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['address']) ?></span>&nbsp;</td>
                    <td><span class="city" data-value="<?php echo h($client['Client']['city']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['city']) ?></span>&nbsp;</td>
                    <td><span class="telephone" data-value="<?php echo h($client['Client']['telephone']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['telephone']) ?></span>&nbsp;</td>
                    <td><span class="whatsapp" data-value="<?php echo h($client['Client']['whatsapp']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['whatsapp']) ?></span>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($client['Client']['email']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h(substr($client['Client']['email'], 0, 20)) ?> ...</span>&nbsp;</td>
                    <td><span class="numeroregistro" data-value="<?php echo h($client['Client']['numeroregistro']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['numeroregistro']) ?></span>&nbsp;</td>
                    <td><span class="web" data-value="<?php echo h($client['Client']['web']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['web']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:120px">
                        <span class="contenedorreportes">
                            <?php
                            //debug($config);debug($plataformas);
                            echo $this->Html->image('config.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:300px">
                                <ul>
                                    <li>
                                        <?php
                                        echo "<b>" . __('Plataforma') . ":</b> " . (isset($config['Plataformasdepagosconfig']['plataformasdepago_id']) && isset($plataformas[$config['Plataformasdepagosconfig']['plataformasdepago_id']]) ? $plataformas[$config['Plataformasdepagosconfig']['plataformasdepago_id']] : '-- No utiliza --');
                                        ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['imprime_cola'] ? '1' : '0') . '.png', array('title' => __('Imprime o no la cola de impresion de reportes'))) . __('Imprime Cola'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['consultaspropietarios'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios envien consultas online al Administrador'))) . __('CP'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['informepagospropietarios'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios informen pagos online'))) . __('IP'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['reparacionpropietariosonline'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios vean sus reparaciones online'))) . __('RP'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['controla_numFactura'] ? '1' : '0') . '.png', array('title' => __('Permite que se puedan repetir o no los números de factura por proveedor'))) . __('Controla Número Facturas'); ?>
                                    </li>
                                    <li>
                                        <span onclick='$("#cd").dialog("open")'><?php echo $this->Html->image('edit.png'); ?>Carta deudores</span>
                                    </li>
                                    <li>
                                        <span onclick='$("#rp").dialog("open")'><?php echo $this->Html->image('edit.png'); ?>Recordatorio Pago</span>
                                    </li>
                                    <li>
                                        <span onclick='$("#cea").dialog("open")'><?php echo $this->Html->image('edit.png'); ?>Cuerpo email avisos</span>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->image(h($client['Client']['cargagpdecartas'] ? '1' : '0') . '.png', array('title' => __('Carga automáticamente GP por Cartas'))) . __('Carga GP Cartas'); ?>
                                    </li>
                                </ul>
                            </span>
                        </span>  
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="11"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <div id='cd' style='display:none;margin:0 auto;background:#fff;z-index:1000000'>
        <?php
        /* es el div para la carta deudores */
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->Form->input('cartadeudores', array('label' => __('Carta deudores'), 'class' => 'ckeditor', 'id' => 'dsc', 'type' => 'textarea', 'value' => $cartadeudores));
        ?>
    </div>
    <div id='rp' style='display:none;margin:0 auto;background:#fff;z-index:1000000'>
        <?php
        /* es el div para Recordatorio pago */
        echo $this->Form->input('rid', ['type' => 'hidden', 'id' => 'rid']);
        echo $this->Form->input('recordatoriopago', array('label' => __('Recordatorio pago'), 'class' => 'ckeditor', 'id' => 'dsc3', 'type' => 'textarea', 'value' => $recordatoriopago));
        ?>
    </div>
    <div id='cea' style='display:none;margin:0 auto;background:#fff;z-index:1000000'>
        <?php
        /* es el div para el cuerpo del email de avisos */
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->Form->input('cuerpoemailaviso', array('label' => __('Cuerpo email de avisos'), 'class' => 'ckeditor', 'id' => 'ckcea', 'type' => 'textarea', 'value' => $cuerpoemailavisos));
        ?>
    </div>
    <?php
    echo $this->element('pagination');
    ?>
</div>
<script>
    $(document).ready(function () {
        $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.cuit').editable({type: 'text', name: 'cuit', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.address').editable({type: 'text', name: 'address', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.city').editable({type: 'text', name: 'city', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.telephone').editable({type: 'text', name: 'telephone', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.whatsapp').editable({type: 'text', name: 'whatsapp', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.email').editable({type: 'textarea', name: 'email', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        $('.numeroregistro').editable({type: 'text', name: 'numeroregistro', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        $('.web').editable({type: 'text', name: 'web', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        var dat = "" + CKEDITOR.instances['dsc'].getData() + "";
        var dat2 = "" + CKEDITOR.instances['ckcea'].getData() + "";
        var dat3 = "" + CKEDITOR.instances['dsc3'].getData() + "";
        var dialog = $("#cd").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true, buttons: {
                Aceptar: function () {
                    dat = CKEDITOR.instances['dsc'].getData();
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Clients/editar", cache: false, data: {name: 'cartadeudores', value: dat, pk: <?= $client['Client']['id'] ?>}}).done(function (msg) {
                    }).fail(function (jqXHR, textStatus) {
                        if (jqXHR.status === 403) {
                            alert("No se pudo modificar el dato. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo modificar el dato");
                        }
                    });
                    dialog.dialog("close");
                },
                Cerrar: function () {
                    CKEDITOR.instances['dsc'].setData(dat);<?php /* Almaceno el valor viejo porq no guardó los cambios */ ?>
                    dialog.dialog("close");
                }
            }
        });
        var dialog3 = $("#rp").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true, buttons: {
                Aceptar: function () {
                    dat3 = CKEDITOR.instances['dsc3'].getData();
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Clients/editar", cache: false, data: {name: 'recordatoriopago', value: dat3, pk: <?= $client['Client']['id'] ?>}}).done(function (msg) {
                    }).fail(function (jqXHR, textStatus) {
                        if (jqXHR.status === 403) {
                            alert("No se pudo modificar el dato. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo modificar el dato");
                        }
                    });
                    dialog3.dialog("close");
                },
                Cerrar: function () {
                    CKEDITOR.instances['dsc3'].setData(dat3);<?php /* Almaceno el valor viejo porq no guardó los cambios */ ?>
                    dialog3.dialog("close");
                }
            }
        });
        var dialog2 = $("#cea").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true, buttons: {
                Aceptar: function () {
                    dat2 = CKEDITOR.instances['ckcea'].getData();
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Clients/editar", cache: false, data: {name: 'cuerpoemailaviso', value: dat2, pk: <?= $client['Client']['id'] ?>}}).done(function (msg) {
                    }).fail(function (jqXHR, textStatus) {
                        if (jqXHR.status === 403) {
                            alert("No se pudo modificar el dato. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo modificar el dato");
                        }
                    });
                    dialog2.dialog("close");
                },
                Cerrar: function () {
                    CKEDITOR.instances['ckcea'].setData(dat2);<?php /* Almaceno el valor viejo porq no guardó los cambios */ ?>
                    dialog2.dialog("close");
                }
            }
        });
    });
</script>