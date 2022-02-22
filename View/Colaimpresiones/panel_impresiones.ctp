<div class="colaimpresiones index">
    <h2><?php echo __('Cola de impresiones'); ?>&nbsp;<span style='background:red;color:white' id="pendientes"></span></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $clientes, 'field' => 'cliente', 'panel' => true], 'pagesearch' => true, 'pagenew' => false, 'model' => 'Colaimpresione'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('id', __('#')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Recibido')); ?></th>
                <th><?php echo $this->Paginator->sort('bloqueado', __('Bloqueado')); ?></th>
                <th class="center">Impresi&oacute;n</th>
                <th class="center"><?php echo $this->Paginator->sort('linkenviado', __('Aviso')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('saldoenviado', __('Plataforma')); ?></th>
                <th class="acciones" style="width:100px;margin-left:10px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($colaimpresiones as $colaimpresione):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($colaimpresione['Colaimpresione']['id']) ?>&nbsp;</td>
                    <td style="text-align:left;font-size:12px"><?php echo h($colaimpresione['Client']['name']) . " - <span style='color:blue'>" . h($colaimpresione['Consorcio']['name'] . " - " . $colaimpresione['LiquidationsType']['name'] . " - " . $colaimpresione['Liquidation']['periodo']) . "</span>" ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y H:i:s'), $colaimpresione['Colaimpresione']['created']) ?>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($colaimpresione['Colaimpresione']['bloqueado'] ? '1' : '0') . '.png', array('title' => __('Bloqueado'))), array('controller' => 'Colaimpresiones', 'action' => 'invertir', 'bloqueado', h($colaimpresione['Colaimpresione']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td style="text-align:right">
                        <?php
                        $imprime = false;
                        foreach ($colaimpresione['Colaimpresionesdetalle'] as $v) {// para no mostrar <td>impreso</td>
                            if ($v['imprimir']) {
                                $imprime = true;
                                break;
                            }
                        }
                        echo "<table>";
                        if ($imprime) {
                            echo "<tr><td></td><td>Impreso</td></tr>";
                        }
                        foreach ($colaimpresione['Colaimpresionesdetalle'] as $v) {
                            if (!$v['imprimir']) { // si no imprime no muestro nada
                                continue;
                            }
                            echo "<tr>";
                            echo "<td style='text-align:left'>" . $this->Html->link(h($v['reporte']), array('controller' => 'Colaimpresiones', 'panel' => true, 'action' => 'view', $colaimpresione['Colaimpresione']['id'], h($v['reporte'])), ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) . "</td>";
                            echo "<td>" . $this->Html->link($this->Html->image(h($v['impreso'] ? '1' : '0') . '.png', array('title' => __('Impreso'))), array('controller' => 'Colaimpresionesdetalles', 'action' => 'invertir', 'impreso', h($v['id'])), array('class' => 'status', 'escape' => false)) . "</td>";
                            echo "</tr>";
                        }

                        if (!empty($colaimpresione['Liquidation']['Adjunto'])) {
                            $tituloimpreso = false;
                            foreach ($colaimpresione['Liquidation']['Adjunto'] as $a) {
                                if (!$a['imprimir']) {
                                    continue;
                                } else if (!$tituloimpreso) {
                                    echo "<tr><td>Adjunto</td></td>";
                                    $tituloimpreso = true;
                                }
                                echo "<tr style='text-align:left'><td>" . $this->Html->link(h(mb_substr($a['titulo'], 0, 20)), array('controller' => 'Adjuntos', 'panel' => true, 'action' => 'download', $a['url'], $colaimpresione['Colaimpresione']['client_id']), ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) . "</td>";
                                echo "<td>" . $this->Html->link($this->Html->image(h($a['impreso'] ? '1' : '0') . '.png', array('title' => __('Impreso'))), array('controller' => 'Adjuntos', 'action' => 'invertir', 'impreso', h($a['id'])), array('class' => 'status', 'escape' => false)) . "</td>";
                                echo "</tr>";
                            }
                        }
                        echo "</table>";
                        ?>
                    </td>
                    <td class="center">
                        <?php
                        echo "<span id='fecha" . $colaimpresione['Colaimpresione']['id'] . "'>";
                        if (!empty($colaimpresione['Colaimpresione']['linkenviado'])) {
                            echo $this->Time->format(__('d/m/Y H:i:s'), $colaimpresione['Colaimpresione']['linkenviado']);
                        }
                        echo "</span>";
                        //echo $this->Html->image('enviar.gif', array('title' => __('Enviar Aviso'), 'id' => 'imgl' . $colaimpresione['Colaimpresione']['id'], 'onclick' => 'enviarlink(' . $colaimpresione['Colaimpresione']['id'] . ')')); //, array('panel' => true, 'controller' => 'Colaimpresiones', 'action' => 'enviarlink', h($colaimpresione['Colaimpresione']['id'])), array('escape' => false), h(__('Desea Enviar link?'))) 
                        ?>
                    </td>
                    <td class="center"><?php
                        if (isset($plataformas[$colaimpresione['Colaimpresione']['client_id']]) && $plataformas[$colaimpresione['Colaimpresione']['client_id']]['plataformasdepago_id'] != 0) {
                            if (empty($colaimpresione['Colaimpresione']['saldoenviado'])) {
                                echo '';
                            }
                            echo "<span title='Enviado " . substr_count($colaimpresione['Colaimpresione']['archivo'], '#') . "'>" . $this->Html->link($this->Time->format(__('d/m/Y H:i:s'), $colaimpresione['Colaimpresione']['saldoenviado']), array('action' => 'getFile', $colaimpresione['Colaimpresione']['client_id'], $colaimpresione['Colaimpresione']['id']), ['target' => '_blank', 'escape' => false]) . "</span>";
                            //echo "<br>";
                            //if ($colaimpresione['Consorcio']['imprime_cod_barras']) {
                            //    echo $this->Html->image('saldo.gif', array('title' => __('Enviar link'), 'id' => 'imgs' . $colaimpresione['Colaimpresione']['id'], 'onclick' => 'enviarsaldo(' . $colaimpresione['Colaimpresione']['id'] . ')'));
                            //}
                        }
                        ?>
                    </td>
                    <td class="acciones" style="width:100px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/planillapagos/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla de pagos</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/edliquidacion/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Estado disponibilidad</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/cobranzasrecibidas/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Cobranzas recibidas</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/cuentacorrienteliquidacion/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Cuenta corriente</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/resumenperiodo/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Resumen Per&iacute;odo</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/planillaparticulares/<?= $colaimpresione['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla GP</a> - 
                                        <a href="<?php echo $this->webroot; ?>Reports/gastosparticularesporcuenta/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla GP2</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/recibosliquidacion/<?= $colaimpresione['Colaimpresione']['liquidation_id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Recibos</a> - 
                                        <a href="<?php echo $this->webroot; ?>Reports/recibosliquidacion/<?= $colaimpresione['Colaimpresione']['liquidation_id'] . "/" . $colaimpresione['Colaimpresione']['client_id'] ?>/1" target="_blank" rel="nofollow noopener noreferrer">Recibos2</a>
                                    </li>
                                </ul>
                            </span>
                        </span>  
                        <?php
                        //echo $this->Html->link($this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'))), array('action' => 'view', $colaimpresione['Colaimpresione']['id']), ['target' => '_blank', 'escape' => false]);
                        if (!$colaimpresione['Colaimpresione']['bloqueado']) {
                            //echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $colaimpresione['Colaimpresione']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $colaimpresione['Colaimpresione']['id']));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="8"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.copias').editable({type: 'number', name: 'copias', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Colaimpresiones/editar', placement: 'right'});
        $("#ColaimpresioneClientId").select2({language: "es", placeholder: '<?= __('Seleccione un cliente...') ?>', width: 600});
        $("#ColaimpresionePanelIndexForm").change(function () {
            $("#ColaimpresionePanelIndexForm").submit();
        });
    });
    setInterval(v2, 60000);
    function v2() {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Colaimpresiones/verificar", cache: false, data: {}
        }).done(function (msg) {
            if (msg !== "[]") {<?php /* Hay cosas en la cola sin imprimir */ ?>
                $("#pendientes").html("Existen liquidaciones pendientes de imprimir");
            } else {
                $("#pendientes").html("");
            }
        });
    }
    function enviarlink(n) {
        $("#imgl" + n).prop('src', '<?= $this->webroot ?>img/loading.gif');
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Colaimpresiones/enviarlink",
            cache: false,
            data: {id: n}
        }).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                } else {
                    $('#fecha' + n).html('<span style="color:green;font-weight:bold">' + obj.d + "</span><br>");
                }
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        }).always(function () {
            $("#imgl" + n).prop('src', '<?= $this->webroot ?>img/enviar.gif');
        });
    }
    function enviarsaldo(n) {
        $("#imgs" + n).prop('src', '<?= $this->webroot ?>img/loading.gif');
        $.ajax({type: "POST", url: "<?= $this->webroot ?>panel/Colaimpresiones/enviarsaldo",
            cache: false,
            data: {id: n}
        }).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                } else {
                    $('#fecha' + n).html('<span style="color:green;font-weight:bold">' + obj.f + "</span><br>");
                }
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        }).always(function () {
            $("#imgs" + n).prop('src', '<?= $this->webroot ?>img/enviar.gif');
        });
    }
</script>
<style>
    td{
        text-align:center;
    }
</style>
