<div class="reportsclients index">
    <h2><?php echo __('Configurar reportes'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => false, 'pagenew' => false, 'model' => 'Reportsclient')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo __('Resumen de gastos') ?></th>
                <th><?php echo __('Composición de saldos') ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            /*
             * $reportsclients -> Reportsclient.id, Reportsclient.report_id
             */
            //debug($reportsclients);
            $r = array_shift($reportsclients);
            $i = 0;
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?>>
                <td class="borde_tabla"></td>
                <td><span class="nombre1" data-value="<?php echo h($r['Reportsclient']['report_id']) ?>" data-pk="<?php echo h($r['Reportsclient']['id']) ?>"><?php echo h(@$reports[$r['Reportsclient']['report_id']]) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.nombre1').editable({type: 'select', value: <?php echo h($r['Reportsclient']['report_id']) ?>, name: 'report_id',
                    source: [
<?php
foreach ($reports as $k => $v) {
    if (substr($v, 0, 17) == 'Resumen de gastos') {
        echo "{value: $k, text: '" . h($v) . "'},";
    }
}
?>
                    ],
                    success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Reportsclients/editar', placement: 'right'});
            });</script>
        <?php
        $r = array_shift($reportsclients);
        ?>
        <td><span class="nombre2" data-value="<?php echo h($r['Reportsclient']['report_id']) ?>" data-pk="<?php echo h($r['Reportsclient']['id']) ?>"><?php echo h(@$reports[$r['Reportsclient']['report_id']]) ?></span>&nbsp;</td>
        <script>$(document).ready(function () {
                $('.nombre2').editable({type: 'select', value: <?php echo h($r['Reportsclient']['report_id']) ?>, name: 'report_id',
                    source: [
<?php
foreach ($reports as $k => $v) {
    if (substr($v, 0, 22) == 'Composición de saldos') {
        echo "{value: $k, text: '" . h($v) . "'},";
    }
}
?>
                    ],
                    success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>Reportsclients/editar', placement: 'left'});
            });</script>
        <td class="borde_tabla"></td>
        </tr>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="2"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<style>
    .editableform .form-control{
        width:350px;
    }
</style>