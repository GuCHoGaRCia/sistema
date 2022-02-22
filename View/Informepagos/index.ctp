<div class="informepagos index">
    <h2><?php echo __('Informe de pagos Propietarios'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0;'>";
    echo $this->Form->create('Informepago', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'style' => 'width:auto', 'options' => [0 => __('Todos')] + $consorcios, 'type' => 'select', 'selected' => isset($this->request->data['Informepago']['consorcio']) ? $this->request->data['Informepago']['consorcio'] : 0]);
    echo $this->Form->input('formasdepago', ['label' => false, 'empty' => '', 'options' => [0 => __('Todas')] + $formasdepago, 'type' => 'select', 'selected' => isset($this->request->data['Informepago']['formasdepago']) ? $this->request->data['Informepago']['formasdepago'] : 0]);
    echo $this->Form->input('verificado', ['label' => __('Ver verificados?'), 'type' => 'checkbox', 'class' => 'cb', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->input('rechazado', ['label' => __('Ver rechazados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo "<a href='/sistema/informepagos/view' style='position:absolute;float:right;cursor:pointer;right:30px;top:82px'><img src='/sistema/img/print2.png' /></a>";
    echo $this->Form->end(__('Ver'));
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0" style="font-size:12px">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Consorcio'); ?></th>
                <th style='white-space:nowrap'><?php echo $this->Paginator->sort('propietario_id', __('Propietario - Unidad')); ?></th>
                <th><?php echo $this->Paginator->sort('formasdepago_id', __('Forma')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th><?php echo $this->Paginator->sort('banco_id', __('Banco')); ?></th>
                <th><?php echo $this->Paginator->sort('operacion', __('Nº Op.')); ?></th>
                <th><?php echo $this->Paginator->sort('observaciones', __('Observaciones')); ?></th>
                <th class="center"><?php echo __('Verificado'); ?></th>
                <th class="center"><?php echo __('Rechazado'); ?></th>
                <th><?php echo __('Comprobantes'); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($informepagos as $informepago):
//                $class = $informepago['Informepago']['verificado'] ? ' class="success-message"' : ($informepago['Informepago']['rechazado'] ? ' class="error-message"' : '');
//                if ($i++ % 2 == 0) {
//                    $class = $informepago['Informepago']['verificado'] ? ' class="altrow success-message"' : ($informepago['Informepago']['rechazado'] ? ' class="altrow error-message"' : ' class="altrow"');
//                }
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?> id="tr<?= $informepago['Informepago']['id'] ?>">
                    <td class="borde_tabla"></td>
                    <td><?php echo h($informepago['Consorcio']['name']) ?></td>
                    <td><?php echo h($informepago['Propietario']['name'] . " - " . $informepago['Propietario']['unidad'] . " (" . $informepago['Propietario']['code'] . ")"); ?></td>
                    <td><?php echo h($informepago['Formasdepago']['forma']); ?></td>
                    <td title='Creado el <?php echo $this->Time->format(__('d/m/Y H:i:s'), $informepago['Informepago']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $informepago['Informepago']['fecha']) ?>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['importe']) ?>&nbsp;</td>
                    <td><?php echo h($informepago['Banco']['name']) ?>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['operacion']) ?>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['observaciones']) ?>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($informepago['Informepago']['verificado'] ? '1' : '0') . '.png', array('title' => __('El pago fue verificado?'))), array('controller' => 'Informepagos', 'action' => 'invertir', 'verificado', h($informepago['Informepago']['id'])), ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="center"><?php echo $this->Html->image(h($informepago['Informepago']['rechazado'] ? '1' : '0') . '.png', ['id' => 'm' . $informepago['Informepago']['id'], 'style' => 'cursor:pointer', 'title' => __('El pago fue rechazado?'), 'onclick' => 'rechazar(' . $informepago['Informepago']['id'] . ')']); ?></td>
                    <td>
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes">
                                <ul>
                                    <?php
                                    if (count($informepago['Informepagosadjunto']) > 0) {
                                        foreach ($informepago['Informepagosadjunto'] as $k => $v) {
                                            ?>
                                            <li>
                                                <a target='_blank' rel='nofollow noopener noreferrer' href="<?php echo $this->webroot; ?>Informepagos/download/<?= $this->Functions->_encryptURL($v['ruta']) ?>/c/<?= $_SESSION['Auth']['User']['client_id'] ?>">Comprobante <?= $k + 1 ?></a>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo "Sin comprobante<br>";
                                    }
                                    echo "<span id='r" . $informepago['Informepago']['id'] . "'>" . ($informepago['Informepago']['rechazado'] ? "<br>Motivo rechazo: " . h($informepago['Informepago']['motivorechazo']) : '' ) . "</span>";
                                    ?>
                                </ul>
                            </span>
                        </span>
                    </td>
                    <td class="acciones" style="width:auto">
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="12"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(function () {
        $("#InformepagoConsorcio").select2({language: "es", placeholder: '<?= __('Consorcio...') ?>'});
        $("#InformepagoFormasdepago").select2({language: "es", width: 200, placeholder: '<?= __('Forma de pago...') ?>'});
    });
    function rechazar(id) {
        if ($("#m" + id).attr('src') === "/sistema/img/1.png") {
            $("#m" + id).attr('src', "/sistema/img/0.png");
            $("#tr" + id).removeClass('error-message');
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Informepagos/undorechazar", cache: false, data: {i: id}});
            $("#r" + id).html('');
            return false;
        }
        var x = prompt("<?= __('Ingrese el motivo por el que rechaza el pago') ?>");
        while (x !== null && x === "") {// acepta y esta vacio
            alert("Debe ingresar un motivo de rechazo");
            var x = prompt("<?= __('Ingrese el motivo por el que rechaza el pago') ?>");
        }
        if (!x) {// cancela
            return false;
        }
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Informepagos/rechazar", cache: false, data: {i: id, m: x}}).done(function (msg) {
            if (msg) {
                $("#m" + id).attr('src', '/sistema/img/1.png');
                $("#tr" + id).addClass('error-message');
                $("#r" + id).html('<br>Motivo rechazo: ' + hhh(x));
            } else {
                alert("<?= __('El informe de pago no pudo ser rechazado, intente nuevamente') ?>");
            }
        });
    }
    /*function validar(id) {
     if ($("#m" + id).attr('src') !== "/sistema/img/1.png") {//si no esta rechazado
     //            if ($("#v" + id).attr('src') !== "/sistema/img/0.png") {
     //                $("#tr" + id).removeClass('success-message');
     //                $("#v" + id).attr('src', '/sistema/img/0.png');
     //            } else {
     //                $("#tr" + id).removeClass('error-message');
     //                $("#tr" + id).addClass('success-message');
     //                $("#v" + id).attr('src', '/sistema/img/1.png');
     //            }
     var cual = ($("#v" + id).prop('src') === '1.png');
     $.ajax({type: "GET", url: "<?= $this->webroot ?>Informepagos/invertir/verificado/" + id + "?" + new Date().getTime()}
     ).done(function (msg) {
     if (msg === "1") {<?php /* todo ok, pongo verde o normal */ ?>
     if (cual) {<?php /* puso en rojo el pago */ ?>
     $("#v" + id).attr({src: "/sistema/img/0.png", title: "Habilitar"});
     $("#tr" + id).removeClass('error-message');
     $("#tr" + id).removeClass('success-message');
     } else {<?php /* puso en verde el pago */ ?>
     $("#tr" + id).removeClass('error-message');
     $("#tr" + id).addClass('success-message');
     $("#v" + id).attr({src: "/sistema/img/1.png", title: "Deshabilitar"});
     }
     }
     });
     } else {
     alert('<?= __('No se puede verificar un informe de pago rechazado previamente. Destilde la opción "Rechazado" e intente nuevamente') ?>');
     }
     }*/
</script>
<style>
    .checkbox{
        width:150px !important;
    }
</style>
