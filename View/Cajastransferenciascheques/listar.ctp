<?php

//	(int) 0 => array(
//		'id' => '4',
//		'client_id' => '65',
//		'caja_id' => '314',
//		'proveedorspago_id' => '12',
//		'fecha_emision' => '2016-08-26',
//		'fecha_vencimiento' => '2016-08-26',
//		'concepto' => 'CM Consorcio 1 - Esteban Rossi (1ºA)',
//		'banconumero' => 'cheque 1',
//		'importe' => '1500.00',
//		'saldo' => '0.00',
//		'depositado' => '0',
//		'anulado' => false,
//		'created' => '2016-08-26 09:34:32',
//		'modified' => '2017-07-03 11:32:33',
//		'conceptoimporte' => 'CM Consorcio 1 - Esteban Rossi (1ºA) ($1500.00)'
//	),
$i = 0;
foreach ($lista as $k => $v) {
    echo "<li style='list-style-type:none'>" . $this->Html->link($this->Html->image('icon-info.png') . " " . $v['conceptoimporte'], ['controller' => 'Cheques', 'action' => 'view', $v['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
    echo " <b>Nº:</b> " . $v['banconumero'];
    echo " <b>Emisi&oacute;n:</b> " . $this->Time->format(__('d/m/Y'), $v['fecha_emision']);
    echo " <b>Vencimiento:</b> " . $this->Time->format(__('d/m/Y'), $v['fecha_vencimiento']);
    echo "</li>";
}

