<div class="reparaciones index">
    <h2><?php echo __('Reparaciones Anuladas'); ?></h2>
    <?php
    echo $this->element('toolbar', ['pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => false, 'model' => 'Reparacione']);
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            REPARACIONES ANULADAS - 
            <?php
            echo h((isset($this->request->data['filter']['consorcio']) ? $consorcios[$this->request->data['filter']['consorcio']] : 'Todos los Consorcios'));
            echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'id' => 'print', 'style' => 'float:right;cursor:pointer;width:28px']);
            ?>
        </div>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                    <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                    <th class="center"><?php echo $this->Paginator->sort('fecha', __('Última actualización')); ?></th>
                    <th><?php echo $this->Paginator->sort('concepto', __('Concepto')); ?></th>
                    <th class="acciones" style="width:70px"><?php echo __('Acciones'); ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ($reparaciones as $reparacione):
                    $vista = strtotime(date("Y-m-d", strtotime($reparacione['Reparacione']['created']))) <= strtotime('2018-04-12') ? 'view2' : 'view';
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td class="borde_tabla"></td>
                        <td><?php echo h($reparacione['Consorcio']['name']) ?></td>
                        <td><?php echo empty($reparacione['Propietario']['name']) ? '--' : h($reparacione['Propietario']['name'] . " - " . $reparacione['Propietario']['unidad'] . " (" . $reparacione['Propietario']['code'] . ")") ?></td>
                        <td class="center"><?php echo h($this->Time->format(__('d/m/Y H:i:s'), $reparacione['Reparacione']['modified'])) ?>&nbsp;</td>
                        <td><?php echo h($reparacione['Reparacione']['concepto']) ?>&nbsp;</td>
                        
                <td class="acciones" style="width:auto">
                    <?php
                    echo $this->Html->image('view.png', ['title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparaciones/$vista/" . $reparacione['Reparacione']['id'] . "');"]);
                    echo $this->Form->postLink($this->Html->image('undo.png', array('title' => __('Restaurar'))), array('action' => 'undo', $reparacione['Reparacione']['id']), ['escapeTitle' => false], __('Desea restaurar la reparación # %s?', h($reparacione['Reparacione']['concepto'])));
                    ?>
                </td>
                <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
        </table>
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
<script>
    $(function () {
        var dialog = $("#rc").dialog({
            autoOpen: false, height: "600", width: "100%", maxWidth: "100%",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true
        });
    });
</script>
<style>
    a:link:after, a:visited:after {    
        content: "";    
        font-size: 90%;   
    }
    @media print {
        table{
            font-size:14px !important;
            font-weight:400 !important;
        }
        .titulo{display:inline-block !important;}
        .acciones {display:none;}
        table thead{line-height:10px}
    }
</style>
<?php
echo "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC  
