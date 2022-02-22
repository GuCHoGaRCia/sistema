<div class="propietarios index">
    <h2><?php echo __('Propietarios'); ?></h2>
    <?php
    echo $this->Html->script(['ckeditor/ckeditor']);
    echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'export' => true, 'model' => 'Propietario']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio') . " (" . count($propietarios) . ")"); ?></th>
                <th style="width:40px"><?php echo $this->Paginator->sort('code', __('Cód')); ?></th>
                <th><?php echo $this->Paginator->sort('unidad', __('Unidad')); ?></th>
                <th style="width:40px"><?php echo $this->Paginator->sort('orden', __('Ord')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('postal_address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('postal_city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('telephone', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('whatsapp', __('WhatsApp')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprime_resumen_cuenta', __('Impr. RC')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('sistema_online', __('Online')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('exceptua_interes', __('Exc. Int.')); ?></th>
                <th class="acciones" style="width:160px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;

            function codigoAccesoApp($email) {//dafne_mdp@hotmail.com
                $arr = str_split(strtolower($email) . "!$*~|#aA<>3bñ " . strtoupper($email));
                $total = 139099;
                foreach ($arr as $v) {
                    $total += (ord($v) * ord($v));
                }
                return str_pad(substr(($total * $total), -6, 6), 6, 0, STR_PAD_RIGHT);
            }

            foreach ($propietarios as $propietario):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($propietario['Consorcio']['name'], array('controller' => 'Consorcios', 'action' => 'view', $propietario['Consorcio']['id'])); ?></td>
                    <td><span class="code" data-value="<?php echo h($propietario['Propietario']['code']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['code']) ?></span>&nbsp;</td>
                    <td><span class="unidad" data-value="<?php echo h($propietario['Propietario']['unidad']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['unidad']) ?></span>&nbsp;</td>
                    <td><span class="orden" data-value="<?php echo h($propietario['Propietario']['orden']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['orden']) ?></span>&nbsp;</td>
                    <td><span class="name" data-value="<?php echo h($propietario['Propietario']['name']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['name']) ?></span>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($propietario['Propietario']['email']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h(substr($propietario['Propietario']['email'], 0, 20)) ?>...</span>&nbsp;</td>
                    <td><span class="postal_address" data-value="<?php echo h($propietario['Propietario']['postal_address']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['postal_address']) ?></span>&nbsp;</td>
                    <td><span class="postal_city" data-value="<?php echo h($propietario['Propietario']['postal_city']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h($propietario['Propietario']['postal_city']) ?></span>&nbsp;</td>
                    <td><span class="telephone" data-value="<?php echo h($propietario['Propietario']['telephone']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h(substr($propietario['Propietario']['telephone'], 0, 20)) ?>...</span>&nbsp;</td>
                    <td><span class="whatsapp" data-value="<?php echo h($propietario['Propietario']['whatsapp']) ?>" data-pk="<?php echo h($propietario['Propietario']['id']) ?>"><?php echo h(substr($propietario['Propietario']['whatsapp'], 0, 20)) ?>...</span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($propietario['Propietario']['imprime_resumen_cuenta'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Propietarios', 'action' => 'invertir', 'imprime_resumen_cuenta', h($propietario['Propietario']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($propietario['Propietario']['sistema_online'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Propietarios', 'action' => 'invertir', 'sistema_online', h($propietario['Propietario']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($propietario['Propietario']['exceptua_interes'] ? '1' : '0') . '.png', array('title' => __('Exceptúa interés'))), array('controller' => 'Propietarios', 'action' => 'invertir', 'exceptua_interes', h($propietario['Propietario']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="acciones" style="width:150px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('config.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:250px;margin-left:-275px">
                                <ul>
                                    <?php
                                    if (!empty($propietario['Propietario']['email'])) {
                                        echo "<li style='font-size:12px'>";
                                        echo __('Codigo Acceso App') . ": <br><b>";
                                        foreach (explode(",", $propietario['Propietario']['email']) as $e) {
                                            echo h($e) . " - " . codigoAccesoApp(h($e)) . "<br>";
                                        }

                                        echo "</b>";
                                        echo "</li>";
                                    }
                                    ?>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($propietario['Propietario']['miembrodelconsejo'] ? '1' : '0') . '.png', array('title' => __('Es miembro del consejo'))), array('controller' => 'Propietarios', 'action' => 'invertir', 'miembrodelconsejo', h($propietario['Propietario']['id'])), ['class' => 'status', 'escape' => false]) . __('Miembro del Consejo') ?>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:230px">
                                <ul>
                                    <li>
                                        <a href="#" onclick='javascript:$("#pid").val("<?= $propietario['Propietario']['id'] ?>");$("#dfechascc").dialog("open")'>Cuenta corriente</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reparaciones/historial/<?= $propietario['Propietario']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Historial Reparaciones</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Propietarios/reportemultas/<?= $propietario['Propietario']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Historial Multas</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick='javascript:$("#propid").val("<?= $propietario['Propietario']['id'] ?>");$("#cid").val("<?= $propietario['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq(1);$("#rliqs").prop("action", "<?php echo $this->webroot; ?>Propietarios/informedeudapropietario")'>Informe de Deuda Ordinaria</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick='javascript:$("#propid").val("<?= $propietario['Propietario']['id'] ?>");$("#cid").val("<?= $propietario['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq(4);$("#rliqs").prop("action", "<?php echo $this->webroot; ?>Propietarios/informedeudapropietarioext")'>Informe de Deuda Extraordinaria</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick='javascript:$("#propid").val("<?= $propietario['Propietario']['id'] ?>");$("#cid").val("<?= $propietario['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq(5);$("#rliqs").prop("action", "<?php echo $this->webroot; ?>Propietarios/informedeudapropietariofondo")'>Informe de Deuda Fondo</a>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $propietario['Propietario']['id'])));
                        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'style' => 'cursor:pointer', 'data-pid' => $propietario['Propietario']['id'], 'onclick' => '$("#editp").dialog("open");$("#editp").html("<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'' . $this->webroot . 'img/loading.gif' . '\'/></div>");$("#editp").load("' . $this->webroot . 'propietarios/edit/' . $propietario['Propietario']['id'] . '");'));
                        if (!empty($propietario['Propietario']['email']) && $_SESSION['Auth']['User']['client_id'] != 22) {
                            echo $this->Html->link($this->Html->image('link.png', array('alt' => __('Abrir panel propietario'), 'title' => __('Abrir panel propietario'))), array('action' => 'link', $propietario['Propietario']['id']), array('escapeTitle' => false, 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer'), __('Se abrira una ventana nueva con el panel de control del propietario'));
                        }
                        if (in_array($_SESSION['Auth']['User']['username'], ['rcasco', 'mmazzei', 'ecano', 'mcorzo'])) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $propietario['Propietario']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', h($propietario['Propietario']['name'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="14"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php
    echo $this->element('pagination');
    ?>
</div>
<script>
    $.fn.editable.defaults.mode = 'inline';
    $(document).ready(function () {
        $('.code').editable({type: 'text', name: 'code', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.orden').editable({type: 'text', name: 'orden', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.name').editable({type: 'text', name: 'name', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.email').editable({type: 'textarea', name: 'email', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.postal_address').editable({type: 'text', name: 'postal_address', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.postal_city').editable({type: 'text', name: 'postal_city', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.telephone').editable({type: 'text', name: 'telephone', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.whatsapp').editable({type: 'text', name: 'whatsapp', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $('.unidad').editable({type: 'text', name: 'unidad', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Propietarios/editar', placement: 'right'});
        $("#filterConsorcio").select2({language: "es", placeholder: "<?= __("Seleccione consorcio...") ?>", allowClear: true});
    });
</script>
<script>
    $(function () {
        var dialog1 = $("#editp").dialog({
            autoOpen: false, height: "auto", width: "700", maxWidth: "700",
            position: {at: "top top"},
            closeOnEscape: false,
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

        $(window).on('resize', function () {
            $(".box-table-ax").css('width', $(".box-table-bx").width());
        });
        $(".box-table-ax").css('width', $(".box-table-bx").width());
    });
    function envia(id) {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
        $.ajax({
            async: true,
            type: "POST",
            url: "<?= $this->webroot ?>Propietarios/edit/" + id,
            data: $("#PropietarioEditForm").serialize()
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
                location.reload();
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
<?= "<div id='editp' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; ?>
<?php
echo $this->element('fechas', ['url' => ['controller' => 'Reports', 'action' => 'cuentacorrientepropietario'], 'model' => 'Propietario']);
echo $this->element('rangoliquidaciones', ['url' => ['controller' => 'Propietarios', 'action' => 'index'], 'model' => 'Propietario', 'infdeuda' => 1]);
