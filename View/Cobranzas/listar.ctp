<?php

if (empty($cobranzas)) {
    die("No se encuentran cobranzas para la liquidación en Proceso y el Propietario seleccionados");
}
echo "<h3>Detalle cobranzas recibidas</h3>";
foreach ($cobranzas as $k => $v) {
    echo "Fecha: " . $this->Time->format('d/m/Y', $v['Cobranza']['fecha']) . " - Importe: ";
    echo $this->Functions->money($v['Cobranzatipoliquidacione']['amount']) . "&nbsp;&nbsp;" . $this->Html->link($this->Html->image('icon-info.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $v['Cobranza']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]) . "<br>";
}