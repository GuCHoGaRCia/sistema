<div class="bancosdepositosefectivos index">
    <h2><?php echo __('Créditos bancarios') ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0;margin-bottom:-20px'>";
    echo $this->Form->create('Bancosdepositosefectivo', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('bancoscuenta', ['label' => false, 'options' => [0 => __('Todas')] + $bancoscuenta, 'type' => 'select', 'selected' => isset($this->request->data['Bancosdepositosefectivo']['bancoscuenta']) ? $this->request->data['Bancosdepositosefectivo']['bancoscuenta'] : 0]);
    echo $this->Form->input('incluye_anulados', ['label' => __('Incluir anulados?'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
    echo $this->Html->image('new.png', ['alt' => __('Agregar'), 'title' => __('Agregar'), 'id' => 'imagen', 'url' => ['action' => 'add2'], 'style' => 'margin-top:-80px;left:85%']);
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('bancoscuenta_id', __('Cuenta bancaria')); ?></th>
                <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                <th><?php echo $this->Paginator->sort('importe', __('Importe')); ?></th>
                <th class="center"><?php echo __('Conciliado') ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($bancosdepositosefectivos as $bancosdepositosefectivo):
                $class = $bancosdepositosefectivo['Bancosdepositosefectivo']['anulado'] ? ' class="error-message tachado"' : null;
                if ($i++ % 2 == 0) {
                    $class = $bancosdepositosefectivo['Bancosdepositosefectivo']['anulado'] ? ' class="altrow error-message tachado"' : ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo $this->Html->link($bancosdepositosefectivo['Bancoscuenta']['name'], array('controller' => 'Bancoscuentas', 'action' => 'view', $bancosdepositosefectivo['Bancoscuenta']['id'])); ?></td>
                    <td title='Creado el <?= $this->Time->format(__('d/m/Y H:i:s'), $bancosdepositosefectivo['Bancosdepositosefectivo']['created']) ?>'><?php echo $this->Time->format(__('d/m/Y'), $bancosdepositosefectivo['Bancosdepositosefectivo']['fecha']) ?>&nbsp;</td>
                    <td><?php echo h($bancosdepositosefectivo['Bancosdepositosefectivo']['concepto']) ?>&nbsp;</td>
                    <td><?php echo h($bancosdepositosefectivo['Bancosdepositosefectivo']['importe']) ?>&nbsp;</td>
                    <td class="center"><?php echo $bancosdepositosefectivo['Bancosdepositosefectivo']['anulado'] ? '' : $this->Html->link($this->Html->image(h($bancosdepositosefectivo['Bancosdepositosefectivo']['conciliado'] ? '1' : '0') . '.png', ['title' => __('Conciliar')]), ['controller' => 'Bancosdepositosefectivos', 'action' => 'invertir', 'conciliado', h($bancosdepositosefectivo['Bancosdepositosefectivo']['id'])], ['class' => 'status', 'escape' => false]); ?></td>
                    <td class="acciones" style="width:100px">
                        <?php
                        if (empty($bancosdepositosefectivo['Bancosdepositosefectivo']['cobranza_id']) && $bancosdepositosefectivo['Bancosdepositosefectivo']['user_id'] == $_SESSION['Auth']['User']['id'] && !$bancosdepositosefectivo['Bancosdepositosefectivo']['conciliado'] && !$bancosdepositosefectivo['Bancosdepositosefectivo']['anulado']) {
                            echo $this->Form->postLink($this->Html->image('undo.png', array('alt' => __('Anular'), 'title' => __('Anular'))), array('action' => 'delete', $bancosdepositosefectivo['Bancosdepositosefectivo']['id']), array('escapeTitle' => false), __('Desea anular el movimiento # %s?', h($bancosdepositosefectivo['Bancosdepositosefectivo']['concepto'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="6"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $("#BancosdepositosefectivoBancoscuenta").select2({language: "es"});
        $("#imagen").removeClass('imgmove');
    });
</script>