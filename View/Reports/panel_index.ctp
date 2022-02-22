<div class="reports index">
    <h2><?php echo __('Reportes'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Report')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('funcion', __('Función')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('enabled', __('Habilitado')); ?></th>
                <th class="acciones center"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($reports as $report):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="name" data-value="<?php echo h($report['Report']['name']) ?>" data-pk="<?php echo h($report['Report']['id']) ?>"><?php echo h($report['Report']['name']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.name').editable({type: 'text', name: 'name', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>Reports/editar', placement: 'right'});
                });</script>
            <td><span class="funcion" data-value="<?php echo h($report['Report']['funcion']) ?>" data-pk="<?php echo h($report['Report']['id']) ?>"><?php echo h($report['Report']['funcion']) ?></span>&nbsp;</td>
            <script>$(document).ready(function () {
                    $('.funcion').editable({type: 'text', name: 'funcion', success: function (n) {
                            if (n) {
                                return n
                            }
                        }, url: '<?php echo $this->webroot; ?>Reports/editar', placement: 'right'});
                });</script>
            <td class="center"><?php echo $this->Html->link($this->Html->image(h($report['Report']['enabled'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Reports', 'action' => 'invertir', 'enabled', h($report['Report']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
            <td class="acciones center">
                <?php
                echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $report['Report']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', $report['Report']['id']));
                ?>
            </td>
            <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>