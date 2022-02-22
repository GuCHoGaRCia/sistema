<div class="cajas index" id="seccionaimprimir">
    <h2><?php
        echo __('Propietarios') . ' del Consorcio ' . h($c['Consorcio']['name']);
        ?></h2><br>
    <?php
    echo $this->Form->create('Consorcio', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->JqueryValidation->input('consorcio', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select', 'selected' => isset($c['Consorcio']['id']) ? $c['Consorcio']['id'] : 0]);
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;']);
    echo $this->Form->end(__('Ver'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Código') ?></th>
                <th><?php echo __('Orden') ?></th>
                <th><?php echo __('Nombre') ?></th>
                <th><?php echo __('Email') ?></th>
                <th><?php echo __('Dirección') ?></th>
                <th><?php echo __('Ciudad') ?></th>
                <th><?php echo __('Teléfono') ?></th>
                <th><?php echo __('Unidad') ?></th>
                <th class="center"><?php echo __('Superf.') ?></th>
                <th class="center"><?php echo __('Polígono') ?></th>
                <th class="center"><?php echo __('E. judicial') ?></th>
                <th class="center"><?php echo __('Exceptúa interés') ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($propietarios as $propietario):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?> style="border-top:1px solid gray">
                    <td class="borde_tabla"></td>
                    <td><?php echo h($propietario['Propietario']['code']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['orden']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['name']) ?></span>&nbsp;</td>
                    <td><?php echo str_replace(',', '<br>', $propietario['Propietario']['email']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['postal_address']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['postal_city']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['telephone']) ?></span>&nbsp;</td>
                    <td><?php echo h($propietario['Propietario']['unidad']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo h($propietario['Propietario']['superficie']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo h($propietario['Propietario']['poligono']) ?></span>&nbsp;</td>
                    <td class="center"><b><?php echo h($propietario['Propietario']['estado_judicial']) ?></b></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(h($propietario['Propietario']['exceptua_interes'] ? '1' : '0') . '.png', array('title' => __('Exceptúa interés'))) ?></td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="12"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        $("#ConsorcioConsorcio").select2({language: "es", placeholder: '<?= __("Seleccione consorcio...") ?>'});
    });
</script>