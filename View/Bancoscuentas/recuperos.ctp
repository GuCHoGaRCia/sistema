<div class="proveedorspagos index">
    <h2><?php echo __('Recuperos Cuentas Administración'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0' id='noimprimir'>";
    echo $this->Form->create('Bancoscuenta', ['class' => 'inline']);
    echo $this->Form->input('bancoscuenta_id', ['label' => false, 'empty' => '', 'options' => $bancoscuentasadms, 'required' => 'required', 'type' => 'select', 'selected' => isset($b) ? $b : '']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d, 'required' => 'required']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h, 'required' => 'required']);
    echo $this->Form->input('incluirrecuperados', ['label' => __('Ver Recuperados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;margin-right:50px']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px', 'id' => 'ver']);
    echo "</div>";
    if ($this->request->is('post')) {
        //debug($ctaconsor);
        ?>
        <div id="seccionaimprimir" style='width:100%'>
            <div class="titulo" style="font-size:16px;font-weight:bold;display:inline-block;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                RECUPEROS <?= h(($this->request->data['Bancoscuenta']['incluirrecuperados'] == 0 ? ' PENDIENTES' : '') . (isset($bancoscuentas[$b]) ? " - " . $bancoscuentas[$b] : '')) ?>
                <?= " - Del " . $d . " al " . $h ?>
            </div>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="esq_i"></td>
                        <th><?php echo __('Consorcio') ?>&nbsp;<span style="cursor:pointer" onclick="toggle2()">[+/-]</span></th>
                        <th style='text-align:left'><?php echo __('Concepto') ?></th>
                        <th style="text-align:right"><?php echo __('Importe') ?></th>
                        <th class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $first = true; // la primera vez muestro el nombre del proveedor, sino no lo muestra
                    $e = $t = $c = 0;
                    $mostro = false;
                    $consorcios = [];
                    foreach ($ctaconsor as $k => $v) {
                        if ($v != 0) {
                            $consorcios[$v] = ['c' => 0, 'e' => 0, 't' => 0];
                        }
                    }
                    foreach ($movimientos['c'] as $p) {
                        foreach ($p['Chequespropiosadmsdetalle'] as $v) {
                            if ($v['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados']) {
                                /* if (!isset($consorcios[$ctaconsor[$v['bancoscuenta_id']]])) {
                                  $consorcios[$ctaconsor[$v['bancoscuenta_id']]] = [];
                                  }
                                  if (!isset($consorcios[$ctaconsor[$v['bancoscuenta_id']]]['c'])) {
                                  $consorcios[$ctaconsor[$v['bancoscuenta_id']]]['c'] = 0;
                                  } */
                                $consorcios[$ctaconsor[$v['bancoscuenta_id']]]['c'] += $v['importe'];
                                $c += $v['importe'];
                            }
                        }
                    }
                    foreach ($movimientos['e'] as $p) {
                        foreach ($p['Administracionefectivosdetalle'] as $v) {
                            if ($v['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados']) {
                                /* if (!isset($consorcios[$v['consorcio_id']])) {
                                  $consorcios[$v['consorcio_id']] = [];
                                  }
                                  if (!isset($consorcios[$v['consorcio_id']]['e'])) {
                                  $consorcios[$v['consorcio_id']]['e'] = 0;
                                  } */
                                $consorcios[$v['consorcio_id']]['e'] += $v['importe'];
                                $e += $v['importe'];
                            }
                        }
                    }
                    foreach ($movimientos['t'] as $p) {
                        foreach ($p['Administraciontransferenciasdetalle'] as $v) {
                            if ($v['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados']) {
                                /* if (!isset($consorcios[$ctaconsor[$v['bancoscuenta_id']]])) {
                                  $consorcios[$ctaconsor[$v['bancoscuenta_id']]] = [];
                                  }
                                  if (!isset($consorcios[$ctaconsor[$v['bancoscuenta_id']]]['t'])) {
                                  $consorcios[$ctaconsor[$v['bancoscuenta_id']]]['t'] = 0;
                                  } */
                                $consorcios[$ctaconsor[$v['bancoscuenta_id']]]['t'] += $v['importe'];
                                $t += $v['importe'];
                            }
                        }
                    }

                    foreach ($consorcios as $k => $v) {
                        if (array_sum($v) == 0) {
                            continue;
                        }
                        ?>
                        <tr style="border:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td>&nbsp;<b><?= h($consor[$k]) ?></b>&nbsp;<span style="cursor:pointer" onclick="toggle('.ef<?= $k ?>')">[+/-]</span></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                        if ($e > 0 && isset($v['e']) && $v['e'] > 0) {
                            ?>
                            <tr>
                                <td class="borde_tabla"></td>
                                <td>&nbsp;</td>
                                <td style='text-align:left'>&nbsp;<b><?php echo __("Efectivo") ?></b>&nbsp;</td>
                                <td style='text-align:right'><?= $this->Functions->money($v['e']) ?>&nbsp;</td>
                                <td class="borde_tabla"></td>
                            </tr>
                            <?php
                            if (!empty($movimientos['e'])) {
                                foreach ($movimientos['e'] as $l) {//muestro el detalle
                                    foreach ($l['Administracionefectivosdetalle'] as $v1) {
                                        if ($v1['consorcio_id'] == $k && $v1['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados'] && isset($consorcios[$v1['consorcio_id']]['e']) && $consorcios[$v1['consorcio_id']]['e'] > 0) {//el consorcio tiene efectivo>0
                                            echo "<tr style=\"display:none\" class=\"ef$k\">";
                                            echo '<td class="borde_tabla"></td>';
                                            echo '<td>&nbsp;</td>';
                                            echo "<td style='padding-left:50px'>";
                                            echo $this->Html->link($this->Html->image(h($v1['recuperado'] ? '1' : '0') . '.png', ['title' => __('Recuperado')]), ['controller' => 'Administracionefectivosdetalles', 'action' => 'invertir', 'recuperado', h($v1['id'])], ['class' => 'status', 'escape' => false]);
                                            echo h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['fecha']) . " - " . $consor[$v1['consorcio_id']] . " - " . $l['Proveedorspago']['concepto']) . "</td>";
                                            echo "<td style='text-align:right'>" . $this->Functions->money($v1['importe']) . "&nbsp;</td>";
                                            echo '<td class="borde_tabla"></td>';
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }
                        }
                        if ($t > 0 && isset($v['t']) && $v['t'] > 0) {
                            ?>
                            <tr>
                                <td class="borde_tabla"></td>
                                <td>&nbsp;</td>
                                <td style='text-align:left'>&nbsp;<b><?php echo __("Transferencia") ?></b>&nbsp;</td>
                                <td style='text-align:right'><?= $this->Functions->money($v['t']) ?>&nbsp;</td>
                                <td class="borde_tabla"></td>
                            </tr>
                            <?php
                            if (!empty($movimientos['t'])) {
                                foreach ($movimientos['t'] as $l) {//muestro el detalle
                                    foreach ($l['Administraciontransferenciasdetalle'] as $v1) {
                                        if ($ctaconsor[$v1['bancoscuenta_id']] == $k && $v1['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados'] && isset($consorcios[$ctaconsor[$v1['bancoscuenta_id']]]['t']) && $consorcios[$ctaconsor[$v1['bancoscuenta_id']]]['t'] > 0) {//el consorcio tiene transf>0
                                            echo "<tr style=\"display:none\" class=\"ef$k\">";
                                            echo '<td class="borde_tabla"></td>';
                                            echo '<td>&nbsp;</td>';
                                            echo "<td style='padding-left:50px'>";
                                            echo $this->Html->link($this->Html->image(h($v1['recuperado'] ? '1' : '0') . '.png', ['title' => __('Recuperado')]), ['controller' => 'Administraciontransferenciasdetalles', 'action' => 'invertir', 'recuperado', h($v1['id'])], ['class' => 'status', 'escape' => false]);
                                            echo h($this->Time->format(__('d/m/Y'), $l['Proveedorspago']['fecha']) . " - " . $consor[$ctaconsor[$v1['bancoscuenta_id']]] . " - " . $l['Proveedorspago']['concepto']) . "</td>";
                                            echo "<td style='text-align:right'>" . $this->Functions->money($v1['importe']) . "&nbsp;</td>";
                                            echo '<td class="borde_tabla"></td>';
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }
                        }
                        if ($c > 0 && isset($v['c']) && $v['c'] > 0) {
                            ?>
                            <tr>
                                <td class="borde_tabla"></td>
                                <td>&nbsp;</td>
                                <td style='text-align:left'>&nbsp;<b><?php echo __("Cheques Propios") ?></b>&nbsp;</td>
                                <td style='text-align:right'><?= $this->Functions->money($v['c']) ?>&nbsp;</td>
                                <td class="borde_tabla"></td>
                            </tr>
                            <?php
                            if (!empty($movimientos['c'])) {
                                foreach ($movimientos['c'] as $l) {//muestro el detalle
                                    foreach ($l['Chequespropiosadmsdetalle'] as $v1) {
                                        if ($ctaconsor[$v1['bancoscuenta_id']] == $k && $v1['recuperado'] == $this->request->data['Bancoscuenta']['incluirrecuperados'] && isset($consorcios[$ctaconsor[$v1['bancoscuenta_id']]]['c']) && $consorcios[$ctaconsor[$v1['bancoscuenta_id']]]['c'] > 0) {//el consorcio tiene chp>0
                                            echo "<tr style=\"display:none\" class=\"ef$k\">";
                                            echo '<td class="borde_tabla"></td>';
                                            echo '<td>&nbsp;</td>';
                                            echo "<td style='padding-left:50px'>";
                                            echo $this->Html->link($this->Html->image(h($v1['recuperado'] ? '1' : '0') . '.png', ['title' => __('Recuperado')]), ['controller' => 'Chequespropiosadmsdetalles', 'action' => 'invertir', 'recuperado', h($v1['id'])], ['class' => 'status', 'escape' => false]);
                                            echo h($this->Time->format(__('d/m/Y'), $l['Chequespropiosadm']['fecha']) . " - " . $consor[$ctaconsor[$v1['bancoscuenta_id']]] . " - " . $l['Chequespropiosadm']['concepto'] . " #" . $l['Chequespropiosadm']['numero']) . "</td>";
                                            echo "<td style='text-align:right'>" . $this->Functions->money($v1['importe']) . "&nbsp;</td>";
                                            echo '<td class="borde_tabla"></td>';
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }
                        }
                        $subtotal = $v['e'] + $v['c'] + $v['t'];
                        ?>
                        <tr>
                            <td class="borde_tabla"></td>
                            <td>&nbsp;</td>
                            <td style="border-top:1px solid black;font-weight:bold;text-align:right">SUBTOTAL</td>
                            <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="borde_tabla"></td>
                        <td colspan='3'>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;</td>
                        <td style="font-weight:bold;text-align:right">TOTAL EFECTIVO</td>
                        <td style="font-weight:bold;text-align:right"><?= $this->Functions->money($e) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;</td>
                        <td style="font-weight:bold;text-align:right">TOTAL TRANSFERENCIA</td>
                        <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($t) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td>&nbsp;</td>
                        <td style="font-weight:bold;text-align:right">TOTAL CHEQUE</td>
                        <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($c) ?>&nbsp;</td>
                        <td class="borde_tabla"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td>&nbsp;</td>
                        <td style="font-weight:bold;text-align:right">TOTAL</td>
                        <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($e + $t + $c) ?>&nbsp;</td>
                        <td class="bottom_d"></td>
                    </tr>
            </table>
        </div>
    </div>
    <style>
        .checkbox{
            width:170px !important;
        }
        .busc{
            margin:-5px -300px !important;
        }
        .busc input[type="text"]{
            width:200px !important;
        }
        #busqform{
            margin-left:-50px;
        }
        a:link:after, a:visited:after {    
            content: "";    
            font-size: 90%;   
        }
        .titulo{
            display:block;
        }
        @media print {
            table{
                font-size:12px !important;
                font-weight:400 !important;
            }
            .acciones {display:none;}
            table thead{line-height:10px}
        }
    </style>
    <script>
        function toggle(detalle) {
            $(detalle).fadeToggle("fast");
        }
        var v = false;
        function toggle2() {
            if (v) {
                $('[class^="ef"]').hide("fast");
                v = false;
            } else {
                $('[class^="ef"]').show("fast");
                v = true;
            }
        }
    </script>
    <?php
} else {
    ?>
    <div class='info'>Seleccione una Cuenta Bancaria de Administraci&oacute;n y fechas desde y hasta</div>
    <?php
}
?>
<script>
    $(function () {
        $("#BancoscuentaBancoscuentaId").select2({language: "es", placeholder: "<?= __('Seleccione Cuenta de Administración...') ?>", width: "300", allowClear: true});
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    });

    $('#ver').on('click', function (e) {
        e.preventDefault();
        if ($("#BancoscuentaBancoscuentaId").val() === "") {
            alert('<?= __('Debe seleccionar una Cuenta Bancaria') ?>');
            return false;
        }
        var f1 = $("#BancoscuentaDesde").val();
        var f2 = $("#BancoscuentaHasta").val();
        if (f1 === "" || f2 === "") {
            alert('<?= __('Seleccione fecha Desde y Hasta') ?>');
            return false;
        }
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            alert('<?= __('La fecha Desde debe ser menor o igual a Hasta') ?>');
            return false;
        }
        $("form").submit();
    });
</script>