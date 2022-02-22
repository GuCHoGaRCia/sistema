<div class="cajas index">
    <h2><?php echo __('Cajas'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('user_id', __('Usuario')); ?></th>
                <th style="text-align:right"><?php echo $this->Paginator->sort('saldo_pesos', __('Saldo Pesos')); ?></th>
                <th style="text-align:right;padding-right:20px"><?php echo $this->Paginator->sort('saldo_cheques', __('Saldo Cheques')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $saldopesos = 0.00;
            $saldocheque = 0.00;
            foreach ($cajas as $caja):
                //a los usuarios q no sean ricardo, esteban, marce, marcela no les muestro estos mismos usuarios 
                if (!in_array($_SESSION['Auth']['User']['username'], ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster']) && in_array($caja['User']['username'], ['ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster'])) {
                    continue;
                }
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                $saldopesos += $caja['Caja']['saldo_pesos'];
                $saldocheque += $caja['Caja']['saldo_cheques'];
                $saldopendiente = (isset($chequesconsaldo[$caja['Caja']['id']]) ? '<span class="error" style="padding:6.5px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(pendiente asignar: ' . $chequesconsaldo[$caja['Caja']['id']] . ')</span>' : '');
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($caja['Caja']['name']) ?>" data-pk="<?php echo h($caja['Caja']['id']) ?>"><?php echo h($caja['Caja']['name']) ?></span>&nbsp;</td>
                    <td><?php echo h($caja['User']['username'] . '@' . $_SESSION['Auth']['User']['Client']['identificador_cliente']) ?>&nbsp;</td>
                    <td style="text-align:right"><span class="saldo_pesos <?= $caja['Caja']['saldo_pesos'] >= 0 ? 'success-message' : 'error-message' ?>" data-value="<?php echo h($caja['Caja']['saldo_pesos']) ?>" data-pk="<?php echo h($caja['Caja']['id']) ?>"><?php echo $this->Functions->money($caja['Caja']['saldo_pesos']) ?></span>&nbsp;</td>
                    <td style="text-align:right;padding-right:20px"><span class="saldo_cheques <?= $caja['Caja']['saldo_cheques'] >= 0 ? 'success-message' : 'error-message' ?>" data-value="<?php echo h($caja['Caja']['saldo_cheques']) ?>" data-pk="<?php echo h($caja['Caja']['id']) ?>"><?php echo $saldopendiente . $this->Functions->money($caja['Caja']['saldo_cheques']) ?></span>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:230px;margin-left:-260px">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Cajas/view/<?= $caja['Caja']['id'] ?>">Movimientos de Caja</a>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        //echo $this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'), 'url' => array('action' => 'view', $caja['Caja']['id'])));
                        if (in_array($_SESSION['Auth']['User']['username'], ['rcasco', 'mmazzei', 'ecano', 'mcorzo', 'mcasalderrey'])) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $caja['Caja']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', h($caja['Caja']['name'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow" style="font-weight:bold;border-top:2px solid black">
                <td></td>
                <td>TOTALES</td>
                <td></td>
                <td style="text-align:right" class="<?= $saldopesos >= 0 ? 'success-message' : 'error-message' ?>"><?php echo $this->Functions->money((float) $saldopesos) ?>&nbsp;</td>
                <td style="text-align:right;padding-right:20px" class="<?= $saldocheque >= 0 ? 'success-message' : 'error-message' ?>"><?php echo $this->Functions->money((float) $saldocheque) ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
            </tr>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php //echo $this->element('pagination');  ?>
</div>
<script>
    $(document).ready(function () {
        $('.name').editable({type: 'text', name: 'name', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Cajas/editar', placement: 'right'});
    });
</script>
