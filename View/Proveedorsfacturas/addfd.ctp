
<div class="proveedorsfacturas form">
    <?php
    $cant = 0;
    $id = 0;
    if (!empty($adj)) {
        echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Facturas Digitales</u>: </b>";
        foreach ($adj as $r => $s) {
            $id = $s['Proveedorsfacturasadjunto']['proveedorsfactura_id'];
            echo "<span id='adj" . $s['Proveedorsfacturasadjunto']['id'] . "'><li style='margin-left:50px'>" . $this->Html->link(h($s['Proveedorsfacturasadjunto']['titulo']), ['controller' => 'Proveedorsfacturas', 'action' => 'download', $s['Proveedorsfacturasadjunto']['url'], 1, 0, $_SESSION['Auth']['User']['client_id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'title' => 'Ver adjunto', 'escape' => false]);
            echo "&nbsp;" . $this->Html->image('drop.png', ['alt' => __('Eliminar adjunto'), 'title' => __('Eliminar adjunto'), 'onclick' => 'deladj(' . $s['Proveedorsfacturasadjunto']['id'] . ',"' . h($s['Proveedorsfacturasadjunto']['titulo']) . '","' . $s['Proveedorsfacturasadjunto']['url'] . '")', 'style' => 'cursor:pointer']);
            echo "</li></span>";
            $cant++;
        }
    } else {
        echo "<p class='info'>" . __("No se encuentran Facturas digitales asociadas") . "</p>";
    }
    ?>
    <?php echo $this->Form->create('Proveedorsfactura', ['class' => 'jquery-validation', 'id' => 'agregaadjunto', 'type' => 'file', 'multiple' => 'multiple']); ?>
    <fieldset>
        <?php
        //echo $this->Form->input('proveedorsfactura_id', ['type' => 'hidden', 'value' => $id]);
        ?>
        <div id="progressbar" style='display:none;width:220px;margin-top:5px'><span style="position:relative;float:left;font-size:12px;font-weight:bold;margin-top:7px">Comprimiendo imagenes... <span id="porc">0%</span></span></div>
        <?php
        echo "<div id='titulos'></div>";
        echo $this->JqueryValidation->input('Proveedorsfactura.files.', array(
            'label' => 'Facturas digitales',
            'div' => false,
            'id' => 'archivostxt',
            'name' => 'archivostxt[]',
            'data-required' => true,
            'type' => 'file',
            'multiple' => 'multiple',
            'onChange' => 'addTitulo();'
        ));
        ?>
    </fieldset>
    <?php echo $this->Form->end() ?>
</div>
<?php
// si tiene adjuntos agrego la posibilidad de borrarlos
if ($cant > 0) {
    ?>
    <script>
        var cant =<?= $cant ?>;
        function deladj(id, titulo, url) {
            if (confirm('<?= __("Desea eliminar el adjunto ") ?>"' + titulo + '" ?')) {
                $.ajax({type: "POST", url: "<?= $this->webroot ?>Proveedorsfacturas/delAdjunto", cache: false, data: {url: url}}).done(function (msg) {
                    if (msg === "true") {
                        $("#addfd").html("<div class='info' style='width:200px;margin:0 auto'>Cargando...<img src='<?= $this->webroot ?>img/loading.gif' /></div>");
                        $("#addfd").load("<?= $this->webroot ?>Proveedorsfacturas/addfd/" + fid);
                        cant--;
                        if (cant == 0) {
                            $("#fimg<?= $id ?>").prop('src', '/sistema/img/factura.png');
                        }
                    } else {
                        alert('<?= __("El dato no pudo ser eliminado") ?>');
                    }
                });
            }
        }
    </script>
    <?php
}
?>

<?php
echo $this->element('adjuntos');
