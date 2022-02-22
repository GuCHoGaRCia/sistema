<div class="liquidations index">
    <h2><?php echo __('Liquidations'); ?></h2>
    <table cellpadding="0" cellspacing="0" style="margin-left: 0px">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th colspan="4">Res&uacute;men de cuenta</th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($propietarios as $k => $v){
			?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4"><?php echo $v["name"] . " (" . $v["unidad"] . ")" ?></td>
                    <td class="borde_tabla"></td>
                </tr>
				<?php
                $total = 0;
                //debug($saldosanteriores);die;
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>Saldo Anterior</td>
                    <?php
                    $capital = 0;
                    $interes = 0;
                    if (isset($saldosanteriores) && count($saldosanteriores) > 0) {
						$capital = $saldosanteriores[$v["id"]]['capital'];
						$interes = $saldosanteriores[$v["id"]]['interes'];
                    }
                    ?>
                    <td>Capital <?php echo $this->Functions->money($capital); ?> <br>Interes <?php echo $this->Functions->money($interes); ?></td>
                    <td><b>&nbsp;</b></td>
                    <td><b><?php echo $this->Functions->money($capital + $interes); ?></b></td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>Cobranzas</td>
                    <td colspan="3">&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
                //debug($cobranzas);die;
                $keys = $this->Functions->find($cobranzas, array('propietario_id' => $v["id"]), true);
                foreach ($keys as $k1) {
                    ?>
                    <tr>
                        <td class="borde_tabla"></td>
                        <td>&nbsp;</td>
                        <td>Fecha: <?php echo $cobranzas[$k1]['Cobranza']['fecha'] ?></td>
                        <td>&nbsp;</td>
                        <td><?php echo $this->Functions->money($cobranzas[$k1]['Cobranza']['amount']); ?>&nbsp;</td>
                        <td class="borde_tabla"></td>                    
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>Saldo remanente</td>
                    <td colspan="3">&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td>Capital: <?php echo $this->Functions->money($saldosremanentes[$v["id"]]['capital']) ?></td>
                    <td>Interes: <?php echo $this->Functions->money($saldosremanentes[$v["id"]]['interes']) ?></td>
                    <td><?php echo $this->Functions->money($saldosremanentes[$v["id"]]['capital'] + $saldosremanentes[$v["id"]]['interes']) ?></td>
                    <td class="borde_tabla"></td>                    
                </tr>
                <?php
                $coefpart = 0;
                if (isset($totales[$v["id"]]["tot"])) {
                    $coefpart = $totales[$v["id"]]["tot"];
                }
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td><b>Gastos particulares</b></td>
                    <td colspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
                if (isset($totales[$v["id"]]["detalle"])) {
                    foreach ($totales[$v["id"]]["detalle"] as $detalle) { // detalles de los gastos particulares de cada propietario
                        ?>
                        <tr>
                            <td class="borde_tabla"></td>
                            <td>&nbsp;</td>
                            <td><?php echo h($detalle['descripcion']) ?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><?php echo $this->Functions->money($detalle['total']); ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>                
                        <?php
                    }
                }

                if (isset($totales[$v["id"]]["coefpar"])) {
                    foreach ($totales[$v["id"]]["coefpar"] as $m) {
                        $coefpart+=$m["tot"]; // sumo el total de los gastos particulares q se prorratean a todos
                        foreach ($m['detalle'] as $detalle) { // detalles de los gastos particulares prorrateados a todos
                            ?>
                            <tr>
                                <td class="borde_tabla"></td>
                                <td>&nbsp;</td>
                                <td><?php echo h($detalle['descripcion']) ?>&nbsp;</td>
                                <td><?php echo "(" . $this->Functions->money($detalle['total']) . ") " . $m['val'] . "%" ?>&nbsp;</td>
                                <td><?php echo $this->Functions->money($detalle['monto']); ?>&nbsp;</td>
                                <td class="borde_tabla"></td>
                            </tr>                
                            <?php
                        }
                    }
                }
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>Total Gastos Particulares</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><b><?php echo $this->Functions->money($coefpart); ?>&nbsp;</b></td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
                $total += $coefpart;
                if (isset($totales[$v["id"]]["coefgen"])) {
                    foreach ($totales[$v["id"]]["coefgen"] as $l => $m):
                        ?>
                        <tr>
                            <td class="borde_tabla"></td>
                            <td>Gasto general</td>
                            <td><?php echo $descripcioncoeficientes[$l]; ?></td>
                            <td><?php echo h($m["val"]); ?> %</td>
                            <td><?php echo $this->Functions->money($m["tot"]); ?></td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                        $total += $m["tot"];
                    endforeach;
                }
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><b>Total expensa</b></td>
                    <td><b><?php echo $this->Functions->money($total); ?>&nbsp;</b></td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><b>Inter&eacute;s</b></td>
                    <td><b>
                            <?php
                            $intremanente = 0;
                            if ($saldosremanentes[$v["id"]]['capital'] > 0) {
                                $intremanente = $saldosremanentes[$v["id"]]['capital'] * 0.025;
                            }
                            echo $this->Functions->money($intremanente);
                            ?>
                            &nbsp;
                        </b></td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><b>Total a pagar</b></td>
                    <td><b><?php echo CakeNumber::currency($saldosremanentes[$v["id"]]['capital'] + $saldosremanentes[$v["id"]]['interes'] + $total + $intremanente, null, array('negative' => '-')); ?>&nbsp;</b></td>
                    <td class="borde_tabla"></td>
                </tr>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="4"><hr></td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
            } // end foreach
            if (count($propietarios) == 0) {
                ?>
                <tr>
                    <td class="borde_tabla"></td>
                    <td colspan="5">No se encuentran propietarios para el consorcio actual</td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>