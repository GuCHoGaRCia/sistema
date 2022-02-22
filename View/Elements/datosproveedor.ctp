<?php
// se utiliza para mostrar la cabecera de los reportes con los datos del proveedor
?>
<td align="left" rowspan="2" valign="middle">
    <?= __("PROVEEDOR") ?><br/><br/>
    <p style="font-size:16px;font-weight:bold;margin:0;line-height:16px"><?= h($dato['name']) ?></p>
    <br/><?= __("Dirección") ?>: <?= !empty($dato['address']) ? h($dato['address']) : '--' ?>
    <br/><?= __("Ciudad") ?>: <?= !empty($dato['city']) ? h($dato['city']) : '--' ?>
    <br/><?= __("CUIT") ?>: <?= !empty($dato['cuit']) && $dato['cuit'] !== "00-00000000-0" ? h($dato['cuit']) : '--' ?>
    <br/><?= __("Mat.") ?>: <?= !empty($dato['matricula']) ? h($dato['matricula']) : '--' ?>
    <br/><?= __("Tel.") ?>: <?= !empty($dato['telephone']) ? h($dato['telephone']) : '--' ?>
    <br/><?= __("Email") ?>: <?= !empty($dato['email']) ? h($dato['email']) : '--' ?>
</td>

