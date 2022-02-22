<?php
//echo $this->Minify->script(['jq']);
?>
<div class="proveedorsfacturas view">
    <h4><?php echo __('Detalle Factura Proveedor'); ?></h4>
    <fieldset>
        <b><?php echo __('Concepto'); ?>: </b>
        <?php echo h(__($proveedorsfactura['Proveedorsfactura']['concepto'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Fecha'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y'), $proveedorsfactura['Proveedorsfactura']['fecha']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Importe'); ?>: </b>
        <?php echo h(__($proveedorsfactura['Proveedorsfactura']['importe'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Número de factura'); ?>: </b>
        <?php echo h(__($proveedorsfactura['Proveedorsfactura']['numero'])); ?>
        &nbsp;
        <br>
    </fieldset>
</div>
<?php
if (isset($proveedorsfactura['Proveedorsfacturasadjunto']) && !empty($proveedorsfactura['Proveedorsfacturasadjunto'])) {
    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Facturas Digitales</u>: </b>";
    foreach ($proveedorsfactura['Proveedorsfacturasadjunto'] as $r => $s) {
        echo "<span id='adj" . $s['id'] . "'><li style='margin-left:50px'>" . $this->Html->link(h($s['titulo']), ['controller' => 'Proveedorsfacturas', 'action' => 'download', $s['url'], 1, 0, $_SESSION['Auth']['User']['client_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'title' => 'Ver adjunto', 'escape' => false]);
        echo "&nbsp;" . $this->Html->image('drop.png', ['alt' => __('Eliminar adjunto'), 'title' => __('Eliminar adjunto'), 'onclick' => 'deladj(' . $s['id'] . ',"' . h($s['titulo']) . '","' . $s['url'] . '")', 'style' => 'cursor:pointer']);
        echo "</li></span>";
    }
    ?>
    <script>
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
}
if (!empty($pagos)) {
    echo '<div class="recuadro"><h4 style="text-align:left">Pagos de la factura</h4>';
    foreach ($pagos as $k => $v) {
        ?>
        <fieldset>
            <?php
            echo $this->Time->format(__('d/m/Y'), $v['Proveedorspago']['fecha']) . " - " . $v['Proveedorspagosfactura']['importe'] . " ";
            echo $this->Html->link($this->Html->image('view.png', array('alt' => __('Ver'), 'title' => __('Ver'))), array('controller' => 'Proveedorspagos', 'action' => 'view', $v['Proveedorspago']['id']), ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
            ?>
            <br>
        </fieldset>
        <?php
    }
    echo "</div>";
}
?>
<?php
if (!empty($gastosGenerale)) {
    ?>
    <div class="recuadro">
        <h4>Gasto General Asociado</h4>
        <div class="gastosGenerales view">
            <fieldset>
                <b><?php echo __('Liquidación'); ?>: </b>
                <?php echo h($gastosGenerale['Liquidation']['name2']); ?>
                &nbsp;
                <br>
                <b><?php echo __('Descripción'); ?>: </b>
                <?php echo ($gastosGenerale['GastosGenerale']['description']); ?>
                &nbsp;
            </fieldset>
        </div>
    </div>
    <?php
} else {
    echo "<div class='info'>" . __('La Factura no posee Gasto General asociado') . "</div>";
}