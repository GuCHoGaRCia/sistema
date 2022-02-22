<div class="reparaciones index">
    <h2><?php echo __('Reparaciones Finalizadas'); ?></h2>
    <?php
    echo "<div class='inline noimprimir' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Reparacione', ['class' => 'inline', 'id' => 'noimprimir']);
    echo $this->Form->input('consorcio_id', array('label' => false, 'empty' => '', 'required' => false));
    echo $this->Form->input('propietario_id', array('label' => false, 'empty' => '', 'required' => false));
    echo $this->Form->input('buscar', ['label' => false, 'style' => 'width:100px', 'placeholder' => __('Buscar'), 'value' => !empty($b) ? $b : '']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;margin-left:-5px']);
    echo "<div style='position:absolute;top:110px;width:200px;right:150px'>" . $this->element('toolbar', ['pagecount' => false, 'filter' => false, 'pagesearch' => false, 'print' => true, 'pagenew' => true, 'model' => 'Reparacione']) . "</div>";
    echo "</div>";
    ?>
    <div id="seccionaimprimir" style='width:100%'>
        <div class="titulo" style="font-size:16px;font-weight:bold;display:none;width:100%;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
            REPARACIONES FINALIZADAS - 
            <?php
            echo h((isset($this->request->data['filter']['consorcio']) ? $consorcios[$this->request->data['filter']['consorcio']] : 'Todos los Consorcios'));
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
                    <th><?php echo $this->Paginator->sort('reparacionesestado_id', __('Estado')); ?></th>
                    <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
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
                        <td style='color:green'><b><?php echo h($reparacione['Reparacionesestado']['nombre']); ?></b></td>
                        <td class="acciones" style="width:auto">
                            <?php
                            echo $this->Html->image('view.png', ['title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparaciones/$vista/" . $reparacione['Reparacione']['id'] . "');"]);
                            echo $this->Html->image('edit.png', ['title' => __('Editar última actualización'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparacionesactualizaciones/edit/" . $reparacione['Reparacione']['id'] . "');"]);
                            echo $this->Html->image('new.png', ['title' => __('Agregar'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparacionesactualizaciones/agregar/" . $reparacione['Reparacione']['id'] . "');"]);
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
</div>
<script>
    $(function () {
        var dialog = $("#rc").dialog({
            autoOpen: false, height: "600", width: "100%", maxWidth: "100%",
            position: {at: "center top"},
            closeOnEscape: false,
            modal: true
        });
        $("#ReparacioneConsorcioId").select2({language: "es", allowClear: true, placeholder: 'Seleccione Consorcio...'});
        $("#ReparacionePropietarioId").select2({language: "es", allowClear: true, placeholder: 'Seleccione Propietario...'});
<?php
// si filtra x consorcio y pone ver, al volver, el listado de propietarios esta vacio, entonces lo lleno con el consorcio seleccionado
if (isset($this->request->data['Reparacione']['consorcio_id']) && !empty($this->request->data['Reparacione']['consorcio_id'])) {
    echo 'getData(' . $this->request->data['Reparacione']['consorcio_id'] . ');';
}
?>
    });
    $("#ReparacioneConsorcioId").change(function () {
        $("#ReparacionePropietarioId option").remove();
        $("#ReparacionePropietarioId").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
        if ($("#ReparacioneConsorcioId").val() !== "") {
            getData($("#ReparacioneConsorcioId").val());
        }
    });
    function getData(e) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = jsonParseOrdered(msg);
                $("#ReparacionePropietarioId option").remove();
                $("#ReparacionePropietarioId").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#ReparacionePropietarioId").append($("<option></option>").attr("value", val["k"]).text(val["v"]));
                });
<?php
// si filtra x consorcio y pone ver, al volver, el listado de propietarios esta vacio, entonces lo lleno con el consorcio seleccionado
// selecciono el propietario q envió anteriormente
if (!empty($this->request->data['Reparacione']['propietario_id'])) {
    echo "$('#ReparacionePropietarioId').val('" . $this->request->data['Reparacione']['propietario_id'] . "');";
}
?>
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        });
    }
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
echo $this->Html->script('ckeditor/ckeditor');
