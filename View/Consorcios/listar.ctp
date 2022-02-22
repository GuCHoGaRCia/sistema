<div class="consorcios index" id='seccionaimprimir'>
    <h3><?php echo __('Listado de Consorcios') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Código') ?></th>
                <th><?php echo __('Nombre') ?></th>
                <th><?php echo __('Propietarios') ?></th>
                <th><?php echo __('CUIT') ?></th>
                <th><?php echo __('Dirección') ?></th>
                <th><?php echo __('Ciudad') ?></th>
                <th><?php echo __('Interés') ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($consorcios as $consorcio):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($consorcio['Consorcio']['code']) ?>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['name']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietarios[$consorcio['Consorcio']['id']]) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['cuit']) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['address']) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['city']) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['interes']) ?></span>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="7"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>

