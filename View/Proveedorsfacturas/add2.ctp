<?php
if (empty($proveedors)) {
    echo "<div class='error'>" . __("Debe agregar Proveedores antes de agregar Facturas") . "</div>";
} else {
    ?>
    <div class="proveedorsfacturas form">
        <p class="error-message"><?= __("Facturas Proveedor asociadas") ?></p>
        <?php
        if (!empty($gg)) {
            foreach ($gg as $k => $v) {
                $leyenda = $this->Time->format(__('d/m/Y'), $v['Proveedorsfactura']['fecha']) . " - " . $v['Proveedor']['name'] . " - " . $v['Proveedorsfactura']['concepto'] . " - #" . $v['Proveedorsfactura']['numero'] . "  Importe: " . $v['Proveedorsfactura']['importe'];
                echo "<ul style='margin:0'>" . $this->Html->link(h($leyenda), ['controller' => 'Proveedorsfacturas', 'action' => 'view', $v['Proveedorsfactura']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'title' => 'Ver detalle factura', 'escape' => false]);
                if (isset($v['Proveedorsfacturasadjunto']) && !empty($v['Proveedorsfacturasadjunto'])) {
                    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Facturas Digitales</u>: </b>";
                    foreach ($v['Proveedorsfacturasadjunto'] as $r => $s) {
                        echo "<span id='adj" . $s['id'] . "'><li style='margin-left:50px'>" . $this->Html->link(h($s['titulo']), ['controller' => 'Proveedorsfacturas', 'action' => 'download', $s['url'], 1, 0, $_SESSION['Auth']['User']['client_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'title' => 'Ver adjunto', 'escape' => false]);
                        echo "&nbsp;" . $this->Html->image('drop.png', ['alt' => __('Eliminar adjunto'), 'title' => __('Eliminar adjunto'), 'onclick' => 'deladj(' . $s['id'] . ',"' . h($s['titulo']) . '","' . $s['url'] . '")', 'style' => 'cursor:pointer']);
                        echo "</li></span>";
                    }
                }
                echo "</ul>";
            }
        } else {
            echo "<p class='info'>" . __("No se encuentran Facturas proveedor asociadas") . "</p>";
        }
        ?>
        <?php echo $this->Form->create('Proveedorsfactura', ['class' => 'jquery-validation', 'id' => 'agregarfactura', 'type' => 'file', 'multiple' => 'multiple']); ?>
        <fieldset>
            <?php
            echo $this->Form->input('gastos_generale_id', ['label' => false, 'id' => 'ggid', 'type' => 'hidden', 'value' => $gastosgenerale_id]);
            echo $this->Form->input('proveedor_id', ['label' => __('Proveedor') . ' *', 'empty' => __('Seleccione Proveedor...'), 'style' => 'width:600px']);
            echo $this->Form->input('liquidation_id', ['label' => __('Liquidación') . ' *', 'id' => 'liquid', 'style' => 'width:600px', 'readonly' => 'readonly']);
            echo $this->Form->input('fecha', ['label' => __('Fecha') . ' *', 'type' => 'date', 'dateFormat' => 'DMY', 'style' => 'width:98px']);
            echo $this->Form->input('concepto', ['label' => __('Concepto')]);
            echo $this->Form->input('importe', ['label' => __('Importe') . ' *', 'min' => 0, 'step' => 0.01]);
            echo $this->Form->input('numero', ['label' => __('Número de factura') . ' *', 'type' => 'text']);
            ?>
            <div id="progressbar" style='display:none;width:220px;margin-top:5px'><span style="position:relative;float:left;font-size:12px;font-weight:bold;margin-top:7px">Comprimiendo imagenes... <span id="porc">0%</span></span></div>
            <?php
            echo "<div id='titulos'></div>";
            echo $this->JqueryValidation->input('Proveedorsfactura.files.', array(
                'label' => 'Facturas digitales',
                'div' => false,
                'id' => 'archivostxt',
                'name' => 'archivostxt[]',
                'data-required' => false,
                'type' => 'file',
                'multiple' => 'multiple',
                'onChange' => 'addTitulo();'
            ));
            ?>
        </fieldset>
        <?php echo $this->Form->end() ?>
    </div>
    <script>
        $(function () {
            $("#ProveedorsfacturaProveedorId").select2({language: "es"});
            $("#liqid").select2({language: "es"});
            $("#ProveedorsfacturaFechaDay").select2({language: "es"});
            $("#ProveedorsfacturaFechaMonth").select2({language: "es"});
            $("#ProveedorsfacturaFechaYear").select2({language: "es"});
        });
        function deladj(id, titulo, url) {
            if (confirm('<?= __("Desea eliminar el adjunto ") ?>"' + titulo + '" ?')) {
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedorsfacturas/delAdjunto", cache: false, data: {url: url}}).done(function (msg) {
                    if (msg === "true") {
                        $("#adj" + id).fadeOut(800, function () {
                            $(this).remove();
                        });
                    } else {
                        alert('<?= __("El dato no pudo ser eliminado") ?>');
                    }
                });
            }
        }
    </script>
    <?php
    echo $this->element('adjuntos');
}