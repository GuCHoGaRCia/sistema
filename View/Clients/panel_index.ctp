<div class="clients index">
    <h2><?php echo __('Clientes'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Client')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('code', __('Cód.')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('cuit', __('CUIT')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('telephone', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('identificador_cliente', __('Identif.')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th class="acciones" style="width:130px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($clients as $client):
                $class = $client['Client']['enabled'] ? ' class="success-message"' : ' class="error-message"';
                if ($i++ % 2 == 0) {
                    $class = $client['Client']['enabled'] ? ' class="altrow success-message"' : ' class="altrow error-message"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="code" data-value="<?php echo h($client['Client']['code']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['code']) ?></span>&nbsp;</td>
                    <td><span class="name" data-value="<?php echo h($client['Client']['name']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['name']) ?></span>&nbsp;</td>
                    <td><span class="cuit" data-value="<?php echo h($client['Client']['cuit']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['cuit']) ?></span>&nbsp;</td>
                    <td><span class="address" data-value="<?php echo h($client['Client']['address']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['address']) ?></span>&nbsp;</td>
                    <td><span class="city" data-value="<?php echo h($client['Client']['city']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['city']) ?></span>&nbsp;</td>
                    <td><span class="telephone" data-value="<?php echo h($client['Client']['telephone']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['telephone']) ?></span>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($client['Client']['email']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h(substr($client['Client']['email'], 0, 15)) ?>...</span>&nbsp;</td>
                    <td><span class="identificador_cliente" data-value="<?php echo h($client['Client']['identificador_cliente']) ?>" data-pk="<?php echo h($client['Client']['id']) ?>"><?php echo h($client['Client']['identificador_cliente']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($client['Client']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Clients', 'action' => 'invertir', 'enabled', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:130px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('config.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:400px;margin-left:-425px">
                                <ul>
                                    <li>
                                        <?php
                                        echo __('Configurar Plataforma');
                                        echo $this->Html->image('config.png', array('title' => __('Configurar Plataforma'), 'data-pid' => $client['Client']['id'], 'onclick' => '$("#editp").dialog("open");$("#editp").load("' . $this->webroot . 'panel/Plataformasdepagosconfigs/edit/' . $client['Client']['id'] . '");'));
                                        ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['imprime_cola'] ? '1' : '0') . '.png', array('title' => __('Imprime o no la cola de impresion de reportes'))), array('controller' => 'Clients', 'action' => 'invertir', 'imprime_cola', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Imprime Cola'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['consultaspropietarios'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios envien consultas online al Administrador'))), array('controller' => 'Clients', 'action' => 'invertir', 'consultaspropietarios', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Consultas Propietarios Online'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['informepagospropietarios'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios informen pagos online'))), array('controller' => 'Clients', 'action' => 'invertir', 'informepagospropietarios', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Informe Pago Propietarios Online'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['reparacionpropietariosonline'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios vean sus reparaciones online'))), array('controller' => 'Clients', 'action' => 'invertir', 'reparacionpropietariosonline', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Reparaciones Propietarios Online'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['controla_numFactura'] ? '1' : '0') . '.png', array('title' => __('Permite que se puedan repetir o no los números de factura por proveedor'))), array('controller' => 'Clients', 'action' => 'invertir', 'controla_numFactura', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Controla Número Facturas'); ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['amenities'] ? '1' : '0') . '.png', array('title' => __('Permite que los Propietarios gestionen sus Amenities online'))), array('controller' => 'Clients', 'action' => 'invertir', 'amenities', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Amenities Online'); ?>
                                    </li>
                                    <li>
                                        <span onclick='javascript:carta(<?= $client['Client']['id'] ?>, "<?= $client['Client']['name'] ?>")'><?php echo $this->Html->image('edit.png'); ?>Carta deudores</span>
                                        <input type='hidden' id='cd<?= $client['Client']['id'] ?>' value='<?= $client['Client']['cartadeudores'] ?>' >
                                    </li>
                                    <li>
                                        <span onclick='javascript:recordatorio(<?= $client['Client']['id'] ?>, "<?= $client['Client']['name'] ?>")'><?php echo $this->Html->image('edit.png'); ?>Recordatorio Pago</span>
                                        <input type='hidden' id='rp<?= $client['Client']['id'] ?>' value='<?= $client['Client']['recordatoriopago'] ?>' >
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($client['Client']['cargagpdecartas'] ? '1' : '0') . '.png', array('title' => __('Carga automáticamente GP por Cartas'))), array('controller' => 'Clients', 'action' => 'invertir', 'cargagpdecartas', h($client['Client']['id'])), array('class' => 'status', 'escape' => false)) . __('Carga GP Cartas'); ?>
                                    </li>
                                </ul>
                            </span>
                        </span>  
                        <?php
                        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $client['Client']['id'])));
                        if (!empty($client['Client']['email'])) {
                            echo $this->Html->link($this->Html->image('link.png', array('title' => __('Abrir Consultas Cliente'))), array('action' => 'link', $client['Client']['id']), array('escapeTitle' => false, 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer'), __('Se abrira una ventana nueva con las consultas del Cliente'));
                        }
                        if ($client['Client']['id'] !== '1' && in_array($_SESSION['Auth']['User']['username'], ['rcasco', 'ecano'])) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $client['Client']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $client['Client']['name']));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="10"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?= "<div id='editp' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC     ?>
    <div id='cd' style='display:none;margin:0 auto;background:#fff;z-index:1000000'>
        <?php
        /* es el div para la carta deudores */
        echo $this->Html->script('ckeditor/ckeditor');
        echo $this->Form->input('cid', ['type' => 'hidden', 'id' => 'cid']);
        echo $this->Form->input('cartadeudores', array('label' => __('Carta deudores'), 'class' => 'ckeditor', 'id' => 'dsc', 'type' => 'textarea'));
        ?>
    </div>
    <div id='rp' style='display:none;margin:0 auto;background:#fff;z-index:1000000'>
        <?php
        /* es el div para Recordatorio pago */
        echo $this->Form->input('rid', ['type' => 'hidden', 'id' => 'rid']);
        echo $this->Form->input('recordatoriopago', array('label' => __('Recordatorio pago'), 'class' => 'ckeditor', 'id' => 'dsc2', 'type' => 'textarea'));
        ?>
    </div>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $.fn.editable.defaults.mode = 'inline';
    var dat = dat2 = "";
    $(document).ready(function () {
        $('.code').editable({type: 'text', name: 'code', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.name').editable({type: 'text', name: 'name', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.city').editable({type: 'text', name: 'city', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.cuit').editable({type: 'text', name: 'cuit', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.telephone').editable({type: 'text', name: 'telephone', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.email').editable({type: 'textarea', name: 'email', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        $('.identificador_cliente').editable({type: 'text', name: 'identificador_cliente', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        $('.address').editable({type: 'text', name: 'address', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'right'});
        $('.cartadeudores').editable({type: 'text', name: 'cartadeudores', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Clients/editar', placement: 'left'});
        var dialog = $("#cd").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true, buttons: {
                Aceptar: function () {
                    dat = CKEDITOR.instances['dsc'].getData();
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Clients/editar", cache: false, data: {name: 'cartadeudores', value: dat, pk: $("#cid").val()}}).done(function (msg) {
                        $("#cd" + $("#cid").val()).val(dat);
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
        var dialog2 = $("#rp").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true, buttons: {
                Aceptar: function () {
                    dat2 = CKEDITOR.instances['dsc2'].getData();
                    $.ajax({type: "POST", url: "<?= $this->webroot ?>Clients/editar", cache: false, data: {name: 'recordatoriopago', value: dat2, pk: $("#rid").val()}}).done(function (msg) {
                        $("#rp" + $("#rid").val()).val(dat2);
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
                    CKEDITOR.instances['dsc2'].setData(dat2);<?php /* Almaceno el valor viejo porq no guardó los cambios */ ?>
                    dialog2.dialog("close");
                }
            }
        });
        var dialog1 = $("#editp").dialog({
            autoOpen: false, height: "auto", width: "700", maxWidth: "700",
            position: {at: "top top"},
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            modal: true,
            buttons: {
                Cerrar: function () {
                    $("#editp").html('');
                    dialog1.dialog("close");
                }
            }
        });
    });
    function carta(cid, name) {
        $("#cd").dialog("open");
        $("#cd").dialog("option", "title", "Editar carta deudor Cliente " + name);
        CKEDITOR.instances["dsc"].setData($("#cd" + cid).val());
        $("#cid").val(cid);
    }
    function recordatorio(rid, name) {
        $("#rp").dialog("open");
        $("#rp").dialog("option", "title", "Editar recordatorio pago Cliente " + name);
        CKEDITOR.instances["dsc2"].setData($("#rp" + rid).val());
        $("#rid").val(rid);
    }
    function envia(id) {
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>panel/Plataformasdepagosconfigs/edit/" + id,
            cache: false,
            data: $("#PlataformasdepagosconfigPanelEditForm").serialize()
        }).done(function (msg) {
            $("input").removeClass('error');
            var obj = JSON.parse(msg);
            if (obj.e === 1) {
                $.each(obj.d, function (index, value) {
                    $("input[name$='[" + index + "]']").closest("div").addClass('error');
                    alert(value);
                });
            } else {
                $("#editp").html('');
                $(".ui-dialog-content").dialog("close");
                alert("El dato se guardó correctamente");
                //location.reload();
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    }
</script>
<style>
    .editableform .form-control{
        width:150px !important;
    }
</style>