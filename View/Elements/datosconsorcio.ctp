<?php
// se utiliza para mostrar la cabecera de los reportes con los datos del CONSORCIO
?>
<td align="left" valign="top" style="border-top:2px solid #000;border-bottom:0px">
    <?= __("CONSORCIO") ?><br/><br/>
    <p style="font-size:16px;font-weight:bold;margin:0;line-height:16px"><?= h($dato['name']) ?></p>
    <br/><?= __("Dirección") ?>: <?= !empty($dato['address']) ? h($dato['address']) : '--' ?>
    <br/><?= __("Ciudad") ?>: <?= !empty($dato['city']) ? h($dato['city']) : '--' ?>
    <br/><?= __("CUIT") ?>: <?= !empty($dato['cuit']) && $dato['cuit'] !== "00-00000000-0" ? h($dato['cuit']) : '--' ?>
</td>

