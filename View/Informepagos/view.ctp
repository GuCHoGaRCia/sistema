<div class="cajas index" id="seccionaimprimir">
    <h2><?php
        echo __('Informe de pagos Verificados') . (isset($desde) ? ' desde el ' . $this->Time->format(__('d/m/Y'), $desde) . ' al ' . $this->Time->format(__('d/m/Y'), $hasta) : '');
        ?>
    </h2>
    <?php
    echo "<div id='noimprimir'>";
    echo '<div class="info">Seleccione la fecha para ver los Pagos informados</div>';
    echo $this->Form->create('Informepago', ['class' => 'inline']);
    echo $this->JqueryValidation->input('desde', array('label' => __('Desde') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => !empty($desde) ? $desde : date("01/m/Y")));
    echo $this->JqueryValidation->input('hasta', array('label' => __('Hasta') . ' *', 'type' => 'text', 'class' => 'dp', 'style' => 'width:85px', 'value' => !empty($hasta) ? $hasta : date("d/m/Y")));
    echo $this->Form->input('verificado', ['label' => __('Incluir NO verificados?'), 'type' => 'checkbox', 'class' => 'cb', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']);
    echo isset($desde) ? $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'onclick' => 'imprimir()', 'style' => 'float:right;cursor:pointer']) : '';
    echo $this->Form->end(__('Ver'));
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0" style='font-size:12px'>
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Consorcio') ?></th>
                <th><?php echo __('Propietario - Unidad') ?></th>
                <th><?php echo __('Forma') ?></th>
                <th><?php echo __('Fecha') ?></th>
                <th><?php echo __('Importe') ?></th>
                <th><?php echo __('Banco') ?></th>
                <th style='width:50px'><?php echo __('Nº Op.') ?></th>
                <th><?php echo __('Observaciones') ?></th>
                <th class="center"><?php echo __('Verificado'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($informepagos as $informepago):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?> style="border-top:1px solid gray">
                    <td class="borde_tabla"></td>
                    <td style='white-space:nowrap'><?php echo $informepago['Consorcio']['name'] ?></td>
                    <td style='white-space:nowrap'><?php echo h($informepago['Propietario']['name'] . " (" . $informepago['Propietario']['unidad'] . ")") ?></span>&nbsp;</td>
                    <td><?php echo h($informepago['Formasdepago']['forma']) ?></span>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $informepago['Informepago']['fecha']) ?></span>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['importe']) ?></span>&nbsp;</td>
                    <td><?php echo h($informepago['Banco']['name']) ?></span>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['operacion']) ?></span>&nbsp;</td>
                    <td><?php echo h($informepago['Informepago']['observaciones']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(h($informepago['Informepago']['verificado'] ? '1' : '0') . '.png', ['id' => 'v' . $informepago['Informepago']['id'], 'title' => __('El pago fue verificado?')]); ?></td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="9"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
</div>
<script>
    $(document).ready(function () {
        $("#ConsorcioConsorcio").select2({language: "es", placeholder: '<?= __("Seleccione consorcio...") ?>'});
        $(".dp").datepicker({maxDate: '0', changeYear: true, yearRange: '2016:+1'});
        document.title = '<?= __('Informe de pagos Verificados') . (isset($desde) ? ' desde el ' . $this->Time->format(__('d/m/Y'), $desde) . ' al ' . $this->Time->format(__('d/m/Y'), $hasta) : '') ?>';

    });
    function imprimir() {
        var html = $("#noimprimir").html();
        $("#noimprimir").html('<br><br><br><br>');
        window.print();
        $("#noimprimir").html(html);
    }

</script>
<style>
    .checkbox{
        width:170px !important;
    }
</style>