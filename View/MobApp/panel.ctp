<style>   
    .fondogris{
        margin:0px;
        text-align:center;
    }
</style>
<?php
echo '<div class="fondogris">';
if (count($d['l']) == 0) {
    echo "<center><div class='info' style='margin-top:5px;width:99%'>No se encontraron Propiedades asociadas</div></center>";
} else {
    ?>
    <style>
        #pri li{
            list-style-type:none;
            text-align:left;
        }
        #pri li ul li{
            padding-left:15px;
            font-size:20px;
        }
        .cont{
            width:99%;border:1px solid gray;padding:5px;margin:5px auto;
            border-radius:21px;
            -moz-border-radius:4px;
            -webkit-border-radius:4px;
            -moz-box-shadow:5px 5px 8px #CCC;
            -webkit-box-shadow:5px 5px 8px #CCC;
            box-shadow:5px 5px 8px #CCC;
            font-weight:bold !important;
        }
        .iom{
            cursor:pointer;
            font-size:22px;
            margin-right:15px;
        }
        .iom:hover,a:hover{
            text-decoration:underline !important;
        }
        .periodo{
            font-size:22px;
        }
        #centro{
            padding:0px;
        }
        .more{cursor:pointer;font-size:18px}
        .hide{display:hidden}
        hr{margin:3px !important}
    </style>
    <?php
    $url = $_SERVER["REMOTE_ADDR"] == '::1' ? "localhost/sistema/" : "ceonline.com.ar/p/?";
    $i = 0;
    $salida = "";
    foreach ($d['l'] as $k => $consorcio) {
        if (count($consorcio) == 0) {
            echo "<h3>-- No se encontraron liquidaciones asociadas --</h3>";
            break;
        }
        $data = json_decode($consorcio[0]['Resumene']['data'], true);
        $salida .= "<div class='cont'>"; //style='background-color:#" . ($i++ % 2 == 0 ? 'fbf7fd' : 'f6fdfd') . "'
        $salida .= "<span class='iom'><b>" . h($consorcio[0]['Client']['name']) . "<br>" . h($consorcio[0]['Consorcio']['name']);
        $salida .= " - " . h($data['prop'][$k]['unidad']) . "</b></span>";
        $total = $saldoanterior = 0;

        //hago el ciclo porq si esta en proceso, tengo q tomar los saldos de esa liquidacion y restarle cobranzas y ajustes
        // si no hay liq en proceso, tomo el saldo actual y resto redondeo
        foreach ($consorcio as $p => $ll) {
            $enproceso = true;
            if (isset($d['c'][$ll['Consorcio']['id']]['resumenesdecuentas'][$ll['Liquidation']['id']])) {//////////////////////////////////////$k=propid,$id=link,$client_id
                $enproceso = false;
            }
            if (isset($d['c'][$ll['Consorcio']['id']]['resumengastos'][$ll['Liquidation']['id']])) {
                $enproceso = false;
            }
            if (isset($d['c'][$ll['Consorcio']['id']]['composicionsaldos'][$ll['Liquidation']['id']])) {
                $enproceso = false;
            }
            $saldo = $data['saldo'][$k];
            if (!$enproceso) {
                //$redondeoactual = $saldo['capital'] + $saldo['interes'] - intval($saldo['capital'] + $saldo['interes']);
                $cobranzas = $saldo['cobranzas'] + $saldo['ajustes'];
                if ($saldoanterior == 0) {//no tiene liq en proceso
                    $redondeoactual = $saldo['capital'] + $saldo['interes'] - intval($saldo['capital'] + $saldo['interes']);
                    $total = $saldo['capital'] + $saldo['interes'] - $redondeoactual;
                } else {
                    $total = $saldoanterior - $cobranzas;
                }
                break; // corto el ciclo, ya encontré la primera q no está en proceso y tomo saldos de esa
            } else {
                $saldoanterior = $saldo['capant'] + $saldo['intant'] - $saldo['redant'];
            }
        }
        //$saldo = $data['saldo'][$k];      
        // plataforma
        //$data['plataforma']['plataformasdepago_id'], si es 1 es plapsa (muestro [ PAGAR ]
        //debug($consorcio[0]['Client']);
        $usaplapsa = 1;
        if (isset($data['plataforma']['plataformasdepago_id']) && $data['plataforma']['plataformasdepago_id'] == 1) {
            $usaplapsa = 1;
        }
        //$redondeoactual = $saldo['capital'] + $saldo['interes'] - intval($saldo['capital'] + $saldo['interes']);
        //$total = $saldo['capital'] + $saldo['interes'] - $redondeoactual;
        //. ($total > 0 && $usaplapsa == 1 ? ' <a href="https://www.google.com" target="_blank" rel="nofollow noopener noreferrer" >[ PAGAR ]</a>' : '') .
        $salida .= "<h4 class='" . ($total > 0 ? 'warning' : 'success') . "'>Saldo actual: $ <span>" . $this->Functions->money($total) . "</span>" . "</h4>";
        $salida .= "<ul id='pri'>";
        foreach ($consorcio as $p => $ll) {
            $enproceso = true;
            $reportes = "<li><span onclick='$(\"#" . "l_" . $ll['Liquidation']['id'] . "_" . $k . "\").slideToggle();'><span class='periodo'>" . h($ll['Liquidation']['periodo']) . "</span>&nbsp;<span class='more'>[ +/- ]</span></span><ul class='rep' style='display:none' id='l_" . $ll['Liquidation']['id'] . "_" . $k . "'>";
            if (isset($d['c'][$ll['Consorcio']['id']]['resumenesdecuentas'][$ll['Liquidation']['id']])) {//////////////////////////////////////$k=propid,$id=link,$client_id
                $reportes .= "<li><a href='https://$url" . "Reports/resumencuenta/" . $ll['Liquidation']['id'] . "/$k/$id/" . $ll['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Resumen de cuenta</a></li>";
                $enproceso = false;
            }
            if (isset($d['c'][$ll['Consorcio']['id']]['resumengastos'][$ll['Liquidation']['id']])) {
                $reportes .= "<li><a href='https://$url" . "Reports/resumengastos/" . $ll['Liquidation']['id'] . "/$k/$id/" . $ll['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Resumen de gastos</a></li>";
                $enproceso = false;
            }

            if (isset($d['c'][$ll['Consorcio']['id']]['composicionsaldos'][$ll['Liquidation']['id']])) {
                $reportes .= "<li><a href='https://$url" . "Reports/composicionsaldos/" . $ll['Liquidation']['id'] . "/$k/$id/" . $ll['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Composici&oacute;n de saldos</a></li>";
                $enproceso = false;
            }

            // agrego los recibos de las cobranzas al panel
            foreach ($consorcio as $abc => $abc2) {
                if ($abc2['Liquidation']['id'] == $ll['Liquidation']['id']) {
                    $data = json_decode($consorcio[$abc]['Resumene']['data'], true);
                    break;
                }
            }
            $keys = $this->Functions->find($data['cobranzas'], ['propietario_id' => $k], true);
            if (!empty($keys)) {
                $reportes .= "<li><b>" . __("RECIBOS") . "</b><ul>";
                $recibos = "";
                foreach ($keys as $kkv) {
                    $recibos .= "<a href='https://$url" . "Cobranzas/ver/" . $this->Functions->_encryptURL($ll['Liquidation']['id'] . "#$k#$id#" . $data['cobranzas'][$kkv]['Cobranza']['id']) . "' target='_blank' rel='nofollow noopener noreferrer'>" . h($this->Time->format(__('d/m/Y'), $data['cobranzas'][$kkv]['Cobranza']['fecha'])) . "</a> - ";
                }
                $reportes .= substr($recibos, 0, -2) . "</ul></li>";
            }

            if (!$enproceso && isset($ll['Adjunto']) && count($ll['Adjunto']) > 0) {
                $reportes .= "<br><li><b>" . h("Adjuntos") . "</b><ul>";
                $adj = "";
                foreach ($ll['Adjunto'] as $adjunto) {
                    //$adj .= $this->Html->link(h($adjunto['titulo']), array('controller' => 'Adjuntos', 'action' => 'download', $this->Functions->_encryptURL($adjunto['ruta']), $k, $id, $ll['Consorcio']['client_id']), ['target' => '_blank']) . "<>";
                    $adj .= "<a href='https://ceonline.com.ar/sistema/Adjuntos/download/" . $this->Functions->_encryptURL($adjunto['ruta']) . "/$k/$id/" . $ll['Consorcio']['client_id'] . "' target='_blank' rel='nofollow noopener noreferrer'>" . h($adjunto['titulo']) . "</a><br>";
                }
                $reportes .= $adj . "</ul></li>";
            }
            if ($enproceso) {
                //echo "<p class='info'>Liquidaci&oacute;n actualmente en proceso...</p>";
                $reportes = "";
            }
            if ($reportes != "") {
                $salida .= $reportes . "</ul><hr></li>";
            }
        }
        $salida .= "</ul>";
        $salida .= "</div>";
    }
    echo $salida;
}
echo "</div>";
