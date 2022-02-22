<?php
// se utiliza para mostrar la cabecera de los reportes con los datos del CLIENTE
?>
<td align="center">
    <?php
    if (file_exists("files/" . $dato['id'] . "/" . $dato['id'] . ".jpg")) {
        $foto = "files/" . $dato['id'] . "/" . $dato['id'] . ".jpg";
    } else {
        $foto = "img/0000.png";
    }
    echo '<img title="Administracion" style="width:100px;height:100px" src="data:image/png;base64,' . base64_encode(file_get_contents($foto)) . '">';
    ?>
</td>
<td align="left" valign="middle">
    <?= __("ADMINISTRACION") ?><br/><br/>
    <p style="font-size:16px;font-weight:bold;margin:0;line-height:16px"><?= h($dato['name']) ?></p>
    <br/><?= __("Direcci&oacute;n") ?>: <?= h($dato['address']) ?>
    <br/><?= __("Ciudad") ?>: <?= h($dato['city']) ?>
    <?php
    echo (!empty($dato['cuit']) && $dato['cuit'] !== "00-00000000-0" ? "<br/>CUIT: " . h($dato['cuit']) : '');
    echo!empty($dato['numeroregistro']) ? "<br/>Mat.: " . h($dato['numeroregistro']) : '';
    echo!empty($dato['telephone']) ? "<br/>Tel.: " . h($dato['telephone']) : '';
    echo!empty($dato['email']) ? "<br/>Email: " . h($dato['email']) : '';
    echo isset($dato['web']) && !empty($dato['web']) ? "<br/>Web: " . h($dato['web']) : '';
    echo isset($dato['whatsapp']) && !empty($dato['whatsapp']) ? "<br/><img src='{$this->webroot}img/wp.png' style='padding:2px;padding-top:7px;float:left'><br>" . h($dato['whatsapp']) : '';
    ?>
</td>

