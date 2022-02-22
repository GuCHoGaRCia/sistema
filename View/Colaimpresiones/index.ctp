<div class="colaimpresiones index">
    <h2><?php echo __('Cola de reportes'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'multidelete' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Colaimpresione'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('bloqueado', __('Bloqueado')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('linkenviado', __('Envío Aviso')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('linkenviado', __('Envío Plataforma')); ?></th>
                <th class="acciones center"><?php echo __('Acciones'); ?></th>
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
                $fueimpresa = false;
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($l[$colaimpresione['Colaimpresione']['liquidation_id']]) ?>&nbsp;</td>
                    <td class="center">
                        <?php
                        $puededesbloquear = true;
                        foreach ($colaimpresione['Colaimpresionesdetalle'] as $v) {
                            if ($v['imprimir']) {
                                $puededesbloquear = false;
                            }
                        }
                        if (!$puededesbloquear) {
                            echo $this->Html->image(($colaimpresione['Colaimpresione']['bloqueado'] ? '1' : '0') . '.png');
                        } else {
                            echo $this->Html->link($this->Html->image(($colaimpresione['Colaimpresione']['bloqueado'] ? '1' : '0') . '.png', ['title' => 'Bloquear/Desbloquear']), ['controller' => 'Colaimpresiones', 'action' => 'invertir', 'bloqueado', $colaimpresione['Colaimpresione']['id']], ['class' => 'status', 'escape' => false]);
                        }
                        ?>&nbsp;</td>
                    <td class="center">
                        <?php
                        if ($colaimpresione['Consorciosconfiguration']['enviaraviso']) {
                            echo "<span id='fecha" . $colaimpresione['Colaimpresione']['id'] . "'>";
                            if (!empty($colaimpresione['Colaimpresione']['linkenviado'])) {
                                echo $this->Time->format(__('d/m/Y H:i:s'), $colaimpresione['Colaimpresione']['linkenviado']);
                            }
                            echo "</span>";
                            echo "<br>";
                            echo $this->Html->image('enviar.gif', array('title' => __('Enviar link'), 'id' => 'imgl' . $colaimpresione['Colaimpresione']['id'], 'onclick' => 'enviarlink(' . $colaimpresione['Colaimpresione']['id'] . ')'));  
                        }
                        ?>
                    </td>
                    <td class="center"><?php
                        if ($colaimpresione['Consorciosconfiguration']['reportarsaldo'] && isset($plataformas[$colaimpresione['Colaimpresione']['client_id']]) && $plataformas[$colaimpresione['Colaimpresione']['client_id']]['plataformasdepago_id'] != 0) {
                            if (empty($colaimpresione['Colaimpresione']['saldoenviado'])) {
                                echo '';
                            }
                            echo "<span id='fechasaldo" . $colaimpresione['Colaimpresione']['id'] . "' title='Enviado " . substr_count($colaimpresione['Colaimpresione']['archivo'], '#') . "'>" . $this->Time->format(__('d/m/Y H:i:s'), $colaimpresione['Colaimpresione']['saldoenviado']) . "</span>";
                            echo "<br>";
                            if ($colaimpresione['Consorcio']['imprime_cod_barras']) {
                                echo $this->Html->image('saldo.gif', array('title' => __('Reportar saldo'), 'id' => 'imgs' . $colaimpresione['Colaimpresione']['id'], 'onclick' => 'enviarsaldo(' . $colaimpresione['Colaimpresione']['id'] . ')'));
                            }
                        }
                        ?>
                    </td>

                    <td class="acciones center">
                        <span class="contenedorreportes" style="">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:400px;margin-left:-425px">
                                <?php
                                echo "<table><tr><td>Reporte</td><td>Imprimir</td><td>Ver Online</td><td>Impreso</td><td><b>Online</b></td></tr>";
                                foreach ($colaimpresione['Colaimpresionesdetalle'] as $v) {
                                    if (!$v['imprimir'] && !$v['poneronline']) {
                                        continue;
                                    }
                                    echo "<tr>";
                                    echo "<td style='text-align:left'>" . $this->Html->link(h($v['reporte']), ['controller' => 'Reports', 'action' => h($v['reporte']), $colaimpresione['Colaimpresione']['liquidation_id'], $colaimpresione['Colaimpresione']['client_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) . "</td>";
                                    echo "<td>" . ($v['imprimir'] ? $this->Html->image(($v['imprimir'] ? '1' : '0') . '.png') : '') . "</td>";
                                    echo "<td>" . ($v['poneronline'] ? $this->Html->image(($v['poneronline'] ? '1' : '0') . '.png') : '') . "</td>";
                                    echo "<td>" . ($v['imprimir'] ? $this->Html->image(($v['impreso'] ? '1' : '0') . '.png') : '') . "</td>";
                                    echo "<td>" . ($v['poneronline'] ? $this->Html->link($this->Html->image(($v['online'] ? '1' : '0') . '.png', ['title' => 'Online']), ['controller' => 'Colaimpresionesdetalles', 'action' => 'invertir', 'online', $v['id']], ['class' => 'status', 'escape' => false]) : '') . "</td>";
                                    echo "</tr>";

                                    if ($v['impreso'] == 1) {
                                        $fueimpresa = true;
                                    }
                                }
                                if (!empty($colaimpresione['Liquidation']['Adjunto'])) {
                                    echo "<tr><td>Adjuntos</td><td colspan='4'></td>";
                                    foreach ($colaimpresione['Liquidation']['Adjunto'] as $a) {
                                        echo "<tr><td>" . h(mb_substr($a['titulo'], 0, 20)) . "</td>";
                                        echo "<td>" . ($a['imprimir'] ? $this->Html->image(($a['imprimir'] ? '1' : '0') . '.png') : '') . "</td>";
                                        echo "<td>" . ($a['poneronline'] ? $this->Html->image(($a['poneronline'] ? '1' : '0') . '.png') : '') . "</td>";
                                        echo "<td>" . ($a['imprimir'] ? $this->Html->image(($a['impreso'] ? '1' : '0') . '.png') : '') . "</td>";
                                        echo "<td>" . ($a['poneronline'] ? $this->Html->link($this->Html->image(($a['online'] ? '1' : '0') . '.png', ['title' => 'Online']), ['controller' => 'Adjuntos', 'action' => 'invertir', 'online', $a['id']], ['class' => 'status', 'escape' => false]) : '') . "</td>";
                                        echo "<td colspan='2'></td></tr>";
                                    }
                                }
                                echo "</table>";
                                ?>
                            </span>
                        </span>
                        <?php
                        if (!$colaimpresione['Colaimpresione']['bloqueado'] && !$fueimpresa) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $colaimpresione['Colaimpresione']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $colaimpresione['Colaimpresione']['id']));
                            echo $this->Form->input('borrado', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $colaimpresione['Colaimpresione']['id'], 'style' => 'box-shadow:none;transform: scale(2);margin:8px;position:absolute']);
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.observaciones').editable({type: 'text', name: 'observaciones', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Colaimpresiones/editar', placement: 'right'});
    });
    function enviarlink(n) {
        $("#imgl" + n).prop('src', '<?= $this->webroot ?>img/loading.gif');
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Colaimpresiones/enviarlink",
            cache: false,
            data: {id: n}
        }).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                } else {
                    $('#fecha' + n).html('<span style="color:green;font-weight:bold">' + obj.d + "</span>");
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
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Colaimpresiones/enviarsaldo",
            cache: false,
            data: {id: n}
        }).done(function (msg) {
            if (msg) {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                } else {
                    $('#fechasaldo' + n).html('<span style="color:green;font-weight:bold">' + obj.d + "</span>");
                }
            }
        }).fail(function (jqXHR) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        }).always(function () {
            $("#imgs" + n).prop('src', '<?= $this->webroot ?>img/saldo.gif');
        });
    }
</script>