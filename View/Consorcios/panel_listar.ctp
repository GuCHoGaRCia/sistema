<div class="consorcios index" id='seccionaimprimir'>
    <h2><?php echo __('Listado de Consorcios') ?></h2>
    <span class="inline noimprimir">
        <?php echo $this->element('toolbar', ['filter' => ['enabled' => true, 'options' => $clientes, 'field' => 'clientes', 'panel' => true], 'model' => 'Consorcio']); ?>
        <span style="font-size:16px;font-weight:bold">Total Propietarios: <span id="cantidadPropietarios"></span></span>
    </span>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Código') ?></th>
                <th style="width:200px"><?php echo __('Nombre') ?></th>
                <th style="width:100px"><?php echo __('Propietarios') ?></th>
                <th style="width:150px"><?php echo __('CUIT') ?></th>
                <th><?php echo __('Dirección') ?></th>
                <th><?php echo __('Ciudad') ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $totalpropietarios = 0;
            foreach ($lista as $consorcio):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                $totalpropietarios += $propietarios[$consorcio['Consorcio']['id']];
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($consorcio['Consorcio']['code']) ?>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['name']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietarios[$consorcio['Consorcio']['id']]) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['cuit']) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['address']) ?></span>&nbsp;</td>
                    <td><?php echo h($consorcio['Consorcio']['city']) ?></span>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="6"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $("#cantidadPropietarios").html("<?= $totalpropietarios ?>");
</script>