<div class="bancoscuentas index">
    <h2><?php echo __('Cuentas Bancarias'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Bancoscuenta')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('banco_id', __('Banco')); ?></th>
                <th><?php echo $this->Paginator->sort('cuenta', __('Cuenta')); ?></th>
                <th><?php echo $this->Paginator->sort('cbu', __('CBU')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('comision_fija_interdeposito', __('Com. Fija')); ?></th>
                <th><?php echo $this->Paginator->sort('comision_variable', __('% Com. Variable')); ?></th>
                <th><?php echo __('CGP Comisión'); ?></th>
                <th><?php echo __('CA Defecto'); ?></th>
                <th><?php echo __('Habilitada'); ?></th>
                <th style="text-align:right;padding-right:20px"><?php echo $this->Paginator->sort('saldo', __('Saldo actual')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $saldo = 0;
            foreach ($bancoscuentas as $bancoscuenta):
                $saldo += $bancoscuenta['Bancoscuenta']['saldo'];
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h(is_null($bancoscuenta['Consorcio']['name']) ? __('00 - Administración - ' . $bancoscuenta['Bancoscuenta']['cuenta']) : $bancoscuenta['Consorcio']['name']); ?></td>
                    <td><?php echo h($bancoscuenta['Banco']['name']); ?></td>
                    <td><span class="cuenta" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['cuenta']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['cuenta']) ?></span>&nbsp;</td>
                    <td><span class="cbu" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['cbu']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['cbu']) ?></span>&nbsp;</td>
                    <td><span class="name" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['name']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['name']) ?></span>&nbsp;</td>
                    <td><span class="comision_fija_interdeposito" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['comision_fija_interdeposito']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['comision_fija_interdeposito']) ?></span>&nbsp;</td>
                    <td><span class="comision_variable" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['comision_variable']) ?>" data-pk="<?php echo h($bancoscuenta['Bancoscuenta']['id']) ?>"><?php echo h($bancoscuenta['Bancoscuenta']['comision_variable']) ?></span>&nbsp;</td>
                    <td>
                        <?php
                        if (!empty($gp[$bancoscuenta['Bancoscuenta']['consorcio_id']])) {
                            ?>
                            <span id="cgp_comision<?= $bancoscuenta['Bancoscuenta']['id'] ?>" data-value="<?php echo h($bancoscuenta['Bancoscuenta']['cgp_comision']) ?>" data-pk="<?php echo $bancoscuenta['Bancoscuenta']['id'] ?>"><?php echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]) ?></span>
                            <script>
                                $(function () {
                                    $('#cgp_comision<?= $bancoscuenta['Bancoscuenta']['id'] ?>').editable({
                                        value: 'CGP', name: 'cgp_comision', type: 'select', url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'left', success: function (n, r) {
                                            if (n) {
                                                return n
                                            }
                                        },
                                        source: [<?php
                    echo "{value: null, text: '- Sin seleccionar -'},";
                    foreach ($gp[$bancoscuenta['Bancoscuenta']['consorcio_id']] as $j => $l) {
                        echo "{value: $j, text: '" . h($l) . "'},";
                    }
                    ?>]
                                    });
                                });
                            </script>                                        
                            <?php
                        } else {
                            //echo "No existen cuentas de GP en el Consorcio asociado";
                        }
                        ?>                        
                    </td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($bancoscuenta['Bancoscuenta']['defectocobranzaautomatica'] ? '1' : '0') . '.png', array('title' => __('Cuenta Defecto Cobranza Automática'))), array('controller' => 'Bancoscuentas', 'action' => 'cambiaCADefecto', h($bancoscuenta['Bancoscuenta']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($bancoscuenta['Bancoscuenta']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Habilitar'))), array('controller' => 'Bancoscuentas', 'action' => 'invertir', 'habilitada', h($bancoscuenta['Bancoscuenta']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td style="text-align:right;padding-right:20px"><span class="saldo <?= $bancoscuenta['Bancoscuenta']['saldo'] >= 0 ? 'success-message' : 'error-message' ?>"><?php echo $this->Functions->money($bancoscuenta['Bancoscuenta']['saldo']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Bancoscuentas/view/<?= $bancoscuenta['Bancoscuenta']['id'] ?>">Movimientos de cuenta</a>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $bancoscuenta['Bancoscuenta']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', h($bancoscuenta['Bancoscuenta']['name'])));
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow" style="font-weight:bold;border-top:2px solid black">
                <td></td>
                <td>TOTALES</td>
                <td colspan="9"></td>
                <td style="text-align:right;padding-right:20px" class="<?= $saldo >= 0 ? 'success-message' : 'error-message' ?>"><?php echo $this->Functions->money($saldo) ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="12"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('.cbu').editable({type: 'text', name: 'cbu', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'right'});
        $('.cuenta').editable({type: 'text', name: 'cuenta', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'right'});
        $('.name').editable({type: 'text', name: 'name', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'right'});
        $('.comision_fija_interdeposito').editable({type: 'text', name: 'comision_fija_interdeposito', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'left'});
        $('.comision_variable').editable({type: 'text', name: 'comision_variable', success: function (n, r) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Bancoscuentas/editar', placement: 'left'});
    });
</script>