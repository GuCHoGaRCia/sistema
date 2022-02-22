<?php
/* if (isset($this->request->params['pass'][0])) {
  echo "<div class='success'>El pago fue guardado correctamente</div>";
  } */
?>
<div class="proveedorspagos index">
    <h2><?php echo __('Pagos a proveedores'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0' id='noimprimir'>";
    echo $this->Form->create('Proveedorspago', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('proveedor_id', ['label' => false, 'empty' => '', 'options' => $proveedores, 'required' => false, 'type' => 'select', 'selected' => isset($px) ? $px : '']);
    echo $this->Form->input('consorcio_id', ['label' => false, 'empty' => '', 'options' => $consorcios, 'required' => false, 'type' => 'select', 'selected' => isset($c) ? $c : '']);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d, 'required' => 'required']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h, 'required' => 'required']);
    echo $this->Form->input('incluiranulados', ['label' => __('Ver anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo "<div style='position:absolute;top:108px;left:90%'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => false, 'pagenew' => true, 'print' => true, 'model' => 'Proveedorspago']) . "</div></div>";
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            PAGOS PROVEEDOR - <?= h((isset($proveedores[$px]) ? $proveedores[$px] : 'Todos los Proveedores') . " - " . (isset($consorcios[$c]) ? $consorcios[$c] : 'Todos los Consorcios')) ?>
            <?= " - Del " . $d . " al " . $h ?>
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="esq_i"></td>
                    <th style="width:40px;text-align:center"><?php echo __('#') ?></th>
                    <th>&nbsp;<?php echo __('Proveedor') ?></th>
                    <th><?php echo __('Consorcio') ?></th>
                    <th style="width:80px"><?php echo __('Fecha') ?></th>
                    <th><?php echo __('Concepto') ?></th>
                    <th style="text-align:right;width:100px"><?php echo __('Importe') ?></th>
                    <th class="acciones" style="width:50px"><?php echo __('Acciones'); ?></th>
                    <th class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $espost = ($this->request->is('post') && (!empty($px) || !empty($c)));
                $first = true; // la primera vez muestro el nombre del proveedor, sino no lo muestra
                $importe = $total = $subtotal = $consor = $proveedor = 0;
                $mostro = false;

                $busc = $proveedorspagos;
                foreach ($proveedorspagos as $k => $v) {
                    if (in_array($v['Proveedorspago']['id'], $pac)) {
                        if (isset($v['Cajasegreso']['consorcio_id']) && isset($v['Bancosextraccione']['consorcio_id']) && isset($v['Chequespropio']['bancoscuenta_id'])) {
                            if (is_null($v['Cajasegreso']['consorcio_id']) && is_null($v['Bancosextraccione']['consorcio_id']) && is_null($v['Chequespropio']['bancoscuenta_id'])) {
                                unset($busc[$k]);
                            }
                        }
                    }
                }
                //debug($busc);
                foreach ($busc as $p) {
                    $ccc = 0;
                    if (isset($p[0]['consorcio'])) {
                        $ccc = $p[0]['consorcio'];
                    } else if (isset($p['Proveedorspagosfactura'][0]['Proveedorsfactura']['Liquidation']['consorcio_id'])) {
                        $ccc = $p['Proveedorspagosfactura'][0]['Proveedorsfactura']['Liquidation']['consorcio_id'];
                    } else if (isset($p['Cajasegreso'][0]['consorcio_id'])) {
                        $ccc = $p['Cajasegreso'][0]['consorcio_id'];
                    } else if (isset($p['Bancosextraccione'][0]['consorcio_id'])) {
                        $ccc = $p['Bancosextraccione'][0]['consorcio_id'];
                    } else if (isset($p['Chequespropio'][0]['bancoscuenta_id'])) {
                        $ccc = $p['Chequespropio'][0]['bancoscuenta_id'];
                    } else if (isset($p['Proveedorspagosacuenta'][0]['consorcio_id'])) {
                        $ccc = $p['Proveedorspagosacuenta'][0]['consorcio_id'];
                    }
                    if (!in_array($ccc, array_keys($consorcios))) {// oculto pagos a proveedor de consorcios deshabilitados
                        continue;
                    }

                    $class = $p['Proveedorspago']['anulado'] ? ' class="error-message"' : ' class="success-message"';
                    $total += $p['Proveedorspago']['importe'];
                    if ($espost) {
                        if (empty($c)) {// cuando no selecciona consorcio
                            if ($consor == 0) {
                                $consor = $p[0]['consorcio'];
                            } else if ($consor != $p[0]['consorcio']) {
                                $consor = $p[0]['consorcio'];
                                ?>
                                <tr class="altrow">
                                    <td class="borde_tabla"></td>
                                    <td colspan="4">&nbsp;</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <tr>
                                    <td class="borde_tabla"></td>
                                    <td colspan="7">&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <?php
                                $mostro = true;
                                $subtotal = 0;
                            }
                        }
                        if (empty($px)) {// cuando no selecciona proveedor
                            if ($proveedor == 0) {
                                $proveedor = $p['Proveedor']['id'];
                            } else if ($proveedor != $p['Proveedor']['id']) {
                                $proveedor = $p['Proveedor']['id'];
                                ?>
                                <tr class="altrow">
                                    <td class="borde_tabla"></td>
                                    <td colspan="4" style="border:none">&nbsp;</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <tr>
                                    <td class="borde_tabla"></td>
                                    <td colspan="7">&nbsp;</td>
                                    <td class="borde_tabla"></td>
                                </tr>
                                <?php
                                $mostro = true;
                                $subtotal = 0;
                            }
                        }
                    }

                    $subtotal += $p['Proveedorspago']['anulado'] ? 0 : $p['Proveedorspago']['importe'];
                    $importe += $p['Proveedorspago']['anulado'] ? 0 : $p['Proveedorspago']['importe'];
                    ?>
                    <tr<?php echo $class; ?> style="border-bottom:1px dotted gray">
                        <td class="borde_tabla"></td>
                        <td><?php echo $this->Html->link($p['Proveedorspago']['numero'], ['action' => 'view2', $p['Proveedorspago']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false, 'style' => 'text-decoration:none']); ?></td>
                        <td>&nbsp;<?php echo h(($mostro || $first || !$espost ? h($p['Proveedor']['name'] . (empty($c) && !empty($px) ? ' - ' . (isset($bancoscuentas[$p['Chequespropio']['bancoscuenta_id']]) ? $bancoscuentas[$p['Chequespropio']['bancoscuenta_id']] : $consorcios[$consor]) : '')) : '')) ?></td>
                        <td><?php
                            if (isset($p['Proveedorspagosfactura'][0]['Proveedorsfactura']['Liquidation']['consorcio_id'])) {
                                echo " (" . h($consorcios[$p['Proveedorspagosfactura'][0]['Proveedorsfactura']['Liquidation']['consorcio_id']]) . ")";
                            } else if (isset($p['Cajasegreso'][0]['consorcio_id'])) {
                                echo " (PAC " . h($consorcios[$p['Cajasegreso'][0]['consorcio_id']]) . ")";
                            } else if (isset($p['Bancosextraccione'][0]['consorcio_id'])) {
                                echo " (PAC " . h($consorcios[$p['Bancosextraccione'][0]['consorcio_id']]) . ")";
                            } else if (isset($p['Chequespropio'][0]['bancoscuenta_id'])) {
                                echo " (PAC " . h($bancoscuentas[$p['Chequespropio'][0]['bancoscuenta_id']]) . ")";
                            } else if (isset($p['Proveedorspagosacuenta'][0]['consorcio_id'])) {
                                echo " (PAC " . h($consorcios[$p['Proveedorspagosacuenta'][0]['consorcio_id']]) . ")";
                            }
                            ?>
                        </td>
                        <td style="width:80px" title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $p['Proveedorspago']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $p['Proveedorspago']['fecha']) ?>&nbsp;</td>
                        <td><?php echo h($p['Proveedorspago']['concepto']) ?>&nbsp;</td>
                        <td style="text-align:right;width:100px;<?= $p['Proveedorspago']['anulado'] ? 'text-decoration:line-through' : '' ?>"><?php echo $this->Functions->money($p['Proveedorspago']['importe']) ?>&nbsp;</td>
                        <td class="acciones" style="width:auto">
                            <?php
                            echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $p['Proveedorspago']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                            if (!$p['Proveedorspago']['anulado'] && $p['Proveedorspago']['user_id'] == $_SESSION['Auth']['User']['id']) {// si no esta anulada y la hizo el usuario actual
                                echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $p['Proveedorspago']['id']), array('escapeTitle' => false), __('Se anularán los Pagos a Proveedor con número # %s?', h($p['Proveedorspago']['numero'])));
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
                    <td colspan="4" style="border:none">&nbsp;</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:center">SUBTOTAL</td>
                    <td style="border-top:1px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($subtotal) ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="4">&nbsp;</td>
                    <td style="font-weight:bold;text-align:right">TOTAL</td>
                    <td style="border-top:2px solid black;font-weight:bold;text-align:right"><?= $this->Functions->money($importe) ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
    </div>
    <?php
    if (!$this->request->is('post')) {
        echo $this->element('pagination');
    }
    ?>
</div>
<script>
    $(function () {
        $("#ProveedorspagoProveedorId").select2({language: "es", placeholder: "<?= __('Seleccione proveedor...') ?>", width: "300", allowClear: true});
        $("#ProveedorspagoConsorcioId").select2({language: "es", placeholder: "<?= __('Seleccione consorcio...') ?>", width: "300", allowClear: true});
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    });
</script>
<style>
    .checkbox{
        width:150px !important;
    }
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