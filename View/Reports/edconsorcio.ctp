<?php

if (empty($data)) { 
    echo "No se encuentran datos<br>";
} else {   
    $cantTiposLiq = count($data); 
    $totalescobranzas = [];  
    foreach ($data as $ka => $ve) {
        $totalcobranzas = 0;
        foreach ($ve as $c => $j) {
            if(!empty($j)){
                $a = !empty($j['Resumene']['data']) ? $j['Resumene']['data'] : $j;
                $dataLiq = json_decode($a, true);
                foreach ($dataLiq['cobranzas'] as $v) {
                    $totalcobranzas += round($v['Cobranzatipoliquidacione']['amount'], 2);
                }                 
            }     
        }
        $totalescobranzas[$ka] = $totalcobranzas;
    }    
    ?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Estado de Disponibilidad - <?= h($consorcio['Consorcio']['name']) ?></title>
            <?= $this->Minify->script(['jq']); ?>
        <style type="text/css">
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 9px;
                text-align: left;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                background: none;
            }
            .box-table-a th,.box-table-b th{
                font-size: 8px;
                padding:2px;
                color: #000;
                text-align:center;
            }
            .box-table-a td{
                padding: 2px;
                background: none;
                border-left: 2px solid #aabcfe;
                border-top: 2px solid #9baff1 !important;
                color: #000; 
            }
            .box-table-b td{
                padding: 2px;
                background: none; 
                border-left: 2px solid #aabcfe;
                border-bottom: 1px solid #aabcfe;
                color: #000;
                line-height:10px;
                text-align:left;
            }
            .tdleft{
                padding: 2px;
                background: none; 
                border:none;
                color: #000;
            }
            .right{
                text-align:right;
                width:auto;
            }
            .chico{
                min-width:35px;
            }
            .pri{
                text-align:left;
                width:130px;
                border:none !important;
            }
            .totales{
                border: 2px solid #9baff1;
                font-weight: 700;
                font-size: 14px;
            }
            #print{
                position:absolute;
                right:0;
                cursor:pointer;
            }
        </style>
    </head>
    <body>
            <?php
            $client = $cliente['Client'];
            $consorcio = $consorcio['Consorcio'];
            $consorciointeres = $consorcio['interes'];
            $datoscliente = $this->element('datoscliente', ['dato' => $client]);
            $datosconsorcio = $this->element('datosconsorcio', ['dato' => $consorcio]);
            ?>
        <table style='font-size:11px;font-family:"Lucida Sans Unicode, Lucida Grande, Sans-Serif";width:750px;max-width:750px;border-bottom:0px' class="box-table-a" align="center">
            <tr>
                    <?= $datoscliente ?>
                    <?= $datosconsorcio ?>
            </tr>
        </table>
            <?php
            $formato = "style='font-size:11px; font-family:Verdana, Helvetica, sans-serif;width:750px;max-width:750px;'";
            $totalgeneral = 0;
            if (isset($data[0]) && !empty($data[0]) && !isset($data[5]) && !isset($data[9])) {// son todas ordinarias
                ?>
        <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
            <tr class="totales" style="font-size:15px">
            <tr>
                <td class="totales" style="width:750px;text-align:center;padding:10px" colspan="4">
                    <b>Estado Disponibilidad Per&iacute;odo: </b><?= h($periodoL1); ?> <b>&nbsp;A</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= h($periodoL2); ?> 
                </td>
            </tr>
            <tr>
                <td class="totales" style='text-align:left'>
                    <b>Concepto</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Debe</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Haber</b>
                </td>
                <td class="totales" style="width:100px;text-align:right">
                    <b>Saldo</b>
                </td>
            </tr>
        </tr>
                <?php
                $saldo = round($disponibilidad ?? 0, 2);
                echo "<tr><td style='text-align:left'><b>Saldo anterior</b></td><td></td><td></td>";
                echo "<td style='text-align:right'><b>" . $this->Functions->money($saldo) . "</b></td></tr>";
               
                $cobranzasOrdinaria = $totalescobranzas[0] ?? 0;
                $saldo += $cobranzasOrdinaria; 
                echo "<tr><td style='text-align:left'><b>Cobranzas</b></td>";
                echo "<td style='text-align:right'>" . $this->Functions->money($cobranzasOrdinaria) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
                                           
                // $gg son egresos
                if(isset($gg['0'])){
                    $saldo -= $gg['0'];  // de ordinaria
                    echo "<tr><td style='text-align:left'><b>Gastos</b></td>";
                    echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($gg['0']) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
                } 
                                          
                $saldo += array_sum($resumen['ingresosmanuales']);
                echo "<tr><td style='text-align:left'><b>Ingresos manuales</b></td>";
                echo "<td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['ingresosmanuales'])) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

                $saldo -= array_sum($resumen['egresosmanuales']);
                echo "<tr><td style='text-align:left'><b>Egresos manuales</b></td>";
                echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['egresosmanuales'])) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
                               
                echo "<tr><td style='width:300px;text-align:center' class='totales'><b>Total<b></td>";
                echo "<td class='totales'>&nbsp;</td><td class='totales'>&nbsp;</td><td class='totales' style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
                ?>
    </table>
    <br/>
            <?php
        }
        ?>
    <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr class="totales" style="font-size:15px">
        <tr>
            <td class="totales" style="width:750px;text-align:center;padding:10px" colspan="4">
                <b>Estado Disponibilidad PAGA Per&iacute;odo: </b><?= h($periodoL1); ?> <b>&nbsp;A</b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= h($periodoL2); ?> 
            </td>
        </tr>
        <tr>
            <td class="totales" style='text-align:left'>
                <b>Concepto</b>
            </td>
            <td class="totales" style="width:100px;text-align:right">
                <b>Debe</b>
            </td>
            <td class="totales" style="width:100px;text-align:right">
                <b>Haber</b>
            </td>
            <td class="totales" style="width:100px;text-align:right">
                <b>Saldo</b>
            </td>
        </tr>
    </tr>
        <?php
        $saldo = 0; 
        if($cantTiposLiq == 1 && (isset($disponibilidadpaga0) || isset($disponibilidadpaga5) || isset($disponibilidadpaga9))){
            echo "<tr><td style='text-align:left'><b>Saldo anterior</b></td><td></td><td></td>";
        }
        if(isset($disponibilidadpaga0)){        // dispobilidad paga de Ordinaria
            $saldoOrdinaria = round($disponibilidadpaga0, 2);
            $saldo += $saldoOrdinaria;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Saldo anterior Ordinaria</b></td><td></td><td></td>";
            }
            echo "<td style='text-align:right'><b>" . $this->Functions->money($saldoOrdinaria) . "</b></td></tr>";
        }
        if(isset($disponibilidadpaga5)){        // dispobilidad paga de Extraordinaria
            $saldoExtraordinaria = round($disponibilidadpaga5, 2);
            $saldo += $saldoExtraordinaria;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Saldo anterior Extraordinaria</b></td><td></td><td></td>";
            }
            echo "<td style='text-align:right'><b>" . $this->Functions->money($saldoExtraordinaria) . "</b></td></tr>";
        }
        if(isset($disponibilidadpaga9)){        // dispobilidad paga de Fondo
            $saldoFondo = round($disponibilidadpaga9, 2);
            $saldo += $saldoFondo;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Saldo anterior Fondo</b></td><td></td><td></td>";
            }
            echo "<td style='text-align:right'><b>" . $this->Functions->money($saldoFondo) . "</b></td></tr>";
        }
        if($cantTiposLiq != 1){     //si hay mas de un tipo de liq
            echo "<tr><td style='text-align:left'><b>Saldo anterior Total</b></td><td></td><td></td>";
            echo "<td style='text-align:right'><b>" . $this->Functions->money($saldo) . "</b></td></tr>";
        }
        
        $totalCobranzas = 0;
        if($cantTiposLiq == 1 && !empty($totalescobranzas)){
            echo "<tr><td style='text-align:left'><b>Cobranzas</b></td>";
        } 
        foreach($totalescobranzas as $k => $v){
            $totalCobranzas += $v;
            $saldo += $v;
            if($k == 0){
                if($cantTiposLiq != 1){
                    echo "<tr><td style='text-align:left'><b>Cobranzas Ordinaria</b></td>";
                }
                echo "<td style='text-align:right'>" . $this->Functions->money($totalescobranzas[0]) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
            }
            if($k == 5){
                if($cantTiposLiq != 1){
                    echo "<tr><td style='text-align:left'><b>Cobranzas Extraordinaria</b></td>";
                }
                echo "<td style='text-align:right'>" . $this->Functions->money($totalescobranzas[5]) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>"; 
            }
            if($k == 9){
                if($cantTiposLiq != 1){
                    echo "<tr><td style='text-align:left'><b>Cobranzas Fondo</b></td>";
                }
                echo "<td style='text-align:right'>" . $this->Functions->money($totalescobranzas[9]) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
            }      
        }
        if($cantTiposLiq != 1){     //si hay mas de un tipo de liq
            echo "<tr><td style='text-align:left'><b>Total Cobranzas</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($totalCobranzas) . "</td><td>&nbsp;</td><td style='text-align:right'></td></tr>";
        }    
        
        $totalGastosPagos = 0;
        if($cantTiposLiq == 1 && (isset($gastospagos0) || isset($gastospagos5) || isset($gastospagos9))){
            echo "<tr><td style='text-align:left'><b>Gastos pagos</b></td>";
        }
        if(isset($gastospagos0)){        // gastos pagos de Ordinaria
            $totalGastosPagos += $gastospagos0;
            $saldo -= $gastospagos0;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Gastos pagos Ordinaria</b></td>";
            }
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($gastospagos0) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        }
        if(isset($gastospagos5)){        // gastos pagos de Extraordinaria
            $totalGastosPagos += $gastospagos5;
            $saldo -= $gastospagos5;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Gastos pagos Extraordinaria</b></td>";
            }
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($gastospagos5) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";      
        }
        if(isset($gastospagos9)){        // gastos pagos de Fondo
            $totalGastosPagos += $gastospagos9;
            $saldo -= $gastospagos9;
            if($cantTiposLiq != 1){
                echo "<tr><td style='text-align:left'><b>Gastos pagos Fondo</b></td>";
            }
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($gastospagos9) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        }
        if($cantTiposLiq != 1){     //si hay mas de un tipo de liq
            echo "<tr><td style='text-align:left'><b>Total Gastos pagos</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($totalGastosPagos) . "</td><td style='text-align:right'></td></tr>";
        }
        if (isset($data[0]) && !empty($data[0])) {// solo para ordinarias
            $saldo += array_sum($resumen['ingresosmanuales']);
            echo "<tr><td style='text-align:left'><b>Ingresos manuales</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['ingresosmanuales'])) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= array_sum($resumen['egresosmanuales']);
            echo "<tr><td style='text-align:left'><b>Egresos manuales</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money(array_sum($resumen['egresosmanuales'])) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo += $resumen['ingresoscreditos'];
            echo "<tr><td style='text-align:left'><b>Cr&eacute;ditos</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($resumen['ingresoscreditos']) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= $resumen['egresosdebitos'];
            echo "<tr><td style='text-align:left'><b>D&eacute;bitos</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($resumen['egresosdebitos']) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo += $resumen['ingresostransferenciasinterbancos'];
            echo "<tr><td style='text-align:left'><b>Ingresos transferencias interbancarias</b></td>";
            echo "<td style='text-align:right'>" . $this->Functions->money($resumen['ingresostransferenciasinterbancos']) . "</td><td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";

            $saldo -= $resumen['egresostransferenciasinterbancos'];
            echo "<tr><td style='text-align:left'><b>Egresos transferencias interbancarias</b></td>";
            echo "<td>&nbsp;</td><td style='text-align:right'>" . $this->Functions->money($resumen['egresostransferenciasinterbancos']) . "</td><td style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        }

        echo "<tr><td style='width:300px;text-align:center' class='totales'><b>Total<b></td>";
        echo "<td class='totales'>&nbsp;</td><td class='totales'>&nbsp;</td><td class='totales' style='text-align:right'>" . $this->Functions->money($saldo) . "</td></tr>";
        ?>
</table>
    <?= "<div style='page-break-after:always'></div>" ?>
</body>
</html>
    <?php
}
?>
