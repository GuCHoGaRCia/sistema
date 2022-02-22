<div class="llaves index">
    <h2><?php echo __('Llaves'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'print' => true, 'model' => 'Llave']); ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            LLAVES
        </div>    
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('numero', __('Número')); ?></th>
                    <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                    <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                    <th><?php echo $this->Paginator->sort('fecha', __('Fecha')); ?></th>
                    <th><?php echo $this->Paginator->sort('descripcion', __('Descripción')); ?></th>
                    <th><?php echo __('Estado') ?></th>
                    <th><?php echo __('Reparación') ?></th>
                    <th class="center"><?php echo $this->Paginator->sort('habilitada', __('Habilitada')); ?></th>
                    <th class="acciones" style="width:70px"><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ($llaves as $llave):
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><span class="numero" data-value="<?php echo h($llave['Llave']['numero']) ?>" data-pk="<?php echo h($llave['Llave']['id']) ?>"><?php echo h(str_pad($llave['Llave']['numero'], 4, "0", STR_PAD_LEFT)) ?></span>&nbsp;</td>
                        <td><?php echo h($llave['Consorcio']['name']) ?></td>
                        <td><?php echo h($llave['Propietario']['name']) ?></td>
                        <td><span class="fecha" data-value="<?php echo h($llave['Llave']['fecha']) ?>" data-pk="<?php echo h($llave['Llave']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $llave['Llave']['fecha']) ?></span>&nbsp;</td>
                        <td><span class="descripcion" data-value="<?php echo h($llave['Llave']['descripcion']) ?>" data-pk="<?php echo h($llave['Llave']['id']) ?>"><?php echo h($llave['Llave']['descripcion']) ?></span>&nbsp;</td>
                        <td><?php echo h($llavesestados[$llave['Llave']['llavesestado_id']]) ?></td>
                        <td><?php echo ($llave['Llave']['llavesestado_id'] == 1 || $llave['Llave']['reparacione_id'] === '0' ? '--' : $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc2').dialog('open');$('#rc2').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc2').load('" . $this->webroot . "Reparaciones/view/" . $llave['Llave']['reparacione_id'] . "');"])) ?></td>
                        <td class="center"><?php echo $this->Form->postLink($this->Html->image(h($llave['Llave']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Deshabilitar'))), array('action' => 'habilitarDeshabilitar', $llave['Llave']['id']), array('escapeTitle' => false), __('Desea habilitar / deshabilitar la llave # %s?', h($llave['Llave']['descripcion']))); ?></td>
                        <td class="acciones" style="width:auto">
                            <?php
                            echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').html('');$('#rc').dialog('open');$('#rc').load('" . $this->webroot . "Llaves/view/" . $llave['Llave']['id'] . "');"]);
                            echo $this->Html->image('new.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').html('');$('#rc').dialog('open');$('#rc').load('" . $this->webroot . "Llavesmovimientos/add/" . $llave['Llave']['id'] . "');"]);
                            if (!$llave['Llave']['habilitada']) {
                                //echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $llave['Llave']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', $llave['Llave']['descripcion']));
                            }
                            ?>
                        </td>
                        <td class="borde_tabla"></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="9"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.numero').editable({type: 'text', name: 'numero', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>llaves/editar', placement: 'right'});
        $('.fecha').editable({type: 'date', viewformat: 'dd/mm/yyyy', name: 'fecha', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>llaves/editar', placement: 'right'});
        $('.descripcion').editable({type: 'text', name: 'descripcion', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>llaves/editar', placement: 'left'});
    });
</script>
<script>
    $(function () {
        var dialog = $("#rc").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: true,
            modal: false,
        });
        var dialog2 = $("#rc2").dialog({
            autoOpen: false, height: "auto", width: "900", maxWidth: "900",
            position: {at: "center top"},
            closeOnEscape: true,
            modal: true,
            buttons: {
                Cerrar: function () {
                    $("#rc2").html('');
                    dialog2.dialog("close");
                }
            }
        });
    });
</script>
<?= "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para los movimientos de llaves  ?>
<?=
"<div id='rc2' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el detalle de la reparacion asociada  ?>