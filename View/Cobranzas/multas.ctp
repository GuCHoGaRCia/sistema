<div class="multas form">
    <fieldset>
        <h2><?php echo __('Multas'); ?></h2>
        <?php
        echo $this->Form->create('Cobranza', ['class' => 'inline', 'id' => 'verform']);
        echo $this->JqueryValidation->input('consorcio_id', ['label' => '', 'empty' => '']);
        echo $this->JqueryValidation->input('tipos', ['label' => '', 'empty' => '']);
        echo "<div class='inline'>" . $this->Form->end(['label' => __('ver'), 'id' => 'ver']) . "<img src='" . $this->webroot . "img/loading.gif' id='load' style='display:none'></div>";
        ?>

    </fieldset>
    <div id="contenido" style="width:auto;height:auto">
        <?php
        if (isset($todosmultados) && $todosmultados == true) {
            ?>
            <div class="warning">Todas las Multas ya fueron cargadas</div>
            <?php
        } else {
            if (isset($propietarios) && !empty($propietarios)) {
                $formato = "style='font-size: 11px; font-family: Verdana, Helvetica, sans-serif;width:750px'";
                $totalgeneral = 0;
                ?>
                <table valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
                    <thead>
                        <tr>
                            <td class="esq_i"></td>
                            <?php
                            echo "<input type='checkbox' onClick=\"for (c in document.getElementsByClassName('til')) document.getElementsByClassName('til').item(c).checked = this.checked\" style=\"cursor:pointer\" />&nbsp;Tildar todos - Destildar todos<br>";
                            ?>
                            <th class="totales" style="width:auto"><b></b></th>
                            <th class="totales" style="width:auto"><b>Unidad</b></th>
                            <th class="totales" style="width:85px; text-align:right"><b>Saldo deudor</b></th>
                            <th class="totales" style="width:85px; text-align:right"><b>Ultima expensa</b></th>
                            <th class="totales" style="width:85px; text-align:right"><b>Multa</b></th>
                            <td class="esq_d"></td>
                        </tr>
                    </thead>
                    <?php
                    echo $this->Form->create('Cobranza', array('class' => 'jquery-validation', 'url' => ['controller' => 'Cobranzas', 'action' => 'multas'], 'id' => 'formperiodo'));
                    echo $this->JqueryValidation->input("consorcio_id", ['label' => false, 'div' => false, 'type' => 'hidden', 'value' => $consorcio_id]);
                    echo $this->JqueryValidation->input("tipos", ['label' => false, 'div' => false, 'type' => 'hidden', 'value' => $idTipoLiquidacion]);

                    foreach ($propietarios as $k => $v) {

                        $opcion = false;
                        if (in_array($v['id'], $multascargadas)) {
                            $opcion = true;
                        }

                        if (!$opcion) {
                            //$todasVerde = false;

                            $multa = 0;

                            if ($saldosactualespropietarios[$k][$idTipoLiquidacion] < $saldosultimaexpensapropietarios[$k][$idTipoLiquidacion]) {

                                $multa = $saldosactualespropietarios[$k][$idTipoLiquidacion] * ($interesMultaConsorcio / 100);
                            } else {

                                $multa = $saldosultimaexpensapropietarios[$k][$idTipoLiquidacion] * ($interesMultaConsorcio / 100);
                            }
                            //echo "<div class='inline' " . ($opcion ? 'style="color:green;font-weight:bold"' : '') . ">";
                            //echo $this->JqueryValidation->input('p_' . $v['id'] /* . "_" . $v['Cobranza']['id'] */, ['label' => false, 'div' => false, 'id' => 'p_' . $v['id'], 'type' => 'checkbox', 'style' => 'width:20px;box-shadow:0px !important;', 'class' => 'til']);
                            //echo "</div>";

                            echo "<tr><td class='borde_tabla'></td>";

                            echo "<td>" . $this->JqueryValidation->input($v['id'], ['label' => false, 'div' => false, 'id' => 'p_' . $v['id'], 'type' => 'checkbox', 'style' => 'width:20px;box-shadow:0px !important;', 'class' => 'til']) . "</td>";

                            echo "<td style='text-align:left; border-bottom: 1px solid grey;'>" . h($v['name'] . ' - ' . $v['unidad'] . " (" . $v['code'] . ")") . "</td>";
                            echo "<td style='text-align:right; border-bottom: 1px solid grey;'>" . h($saldosactualespropietarios[$k][$idTipoLiquidacion]) . "</td>";
                            echo "<td style='text-align:right; border-bottom: 1px solid grey;'>" . h($saldosultimaexpensapropietarios[$k][$idTipoLiquidacion]) . "</td>";
                            echo "<td style='text-align:right; border-bottom: 1px solid grey; font-weight:bold;'>" . $this->Functions->money(round($multa, 2)) . "&nbsp;</td>";

                            echo "<td class='borde_tabla'></td></tr>";
                        }
                    }
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="5"></td>
                        <td class="bottom_d"></td>
                    </tr>
                </table>
                <br/>
                <?php
                echo "<div class='inline'>" . $this->Form->end(['label' => __('Multar'), 'id' => 'multar']) . "<img src='" . $this->webroot . "img/loading.gif' id='loadMultar' style='display:none'></div>";
            } else {
                if (!isset($propietarios)) {
                    ?>
                    <div class="info">Seleccione un Consorcio y tipo de Liquidaci&oacute;n...
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="warning">No se encuentran deudores para el Consorcio y Tipo de Liquidaci&oacute;n seleccionados</div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#CobranzaConsorcioId").select2({language: "es", placeholder: "<?= __("Seleccione consorcio...") ?>"});
        $("#CobranzaTipos").select2({language: "es", placeholder: "<?= __("Seleccione tipo...") ?>"});
        $(document).on('click', '#multar', function () {
            var hay = false;
            $('input[id^="p_"]').each(function () {
                if ($(this).is(":checked")) {
                    hay = true;
                    return false;
                }
            });
            if (!hay) {
                alert("Debe seleccionar al menos un Propietario");
                return false;
            }

            $('input[id^="p_"]').each(function () {
                if (!$(this).is(":checked")) {
                    this.remove();
<?php /* no envio los vacios o cero */
?>
                }
            });
            $("#loadMultar").show();
            $("#formperiodo").submit();
        });
        $(document).on('click', '#ver', function () {
            if ($("#CobranzaConsorcioId").val() === "") {
                alert("Debe seleccionar un Consorcio");
                return false;
            }
            if ($("#CobranzaTipos").val() === "") {
                alert("Debe seleccionar Tipo de Liquidación");
                return false;
            }
            $("#load").show();
            $("#verform").submit();
        });
    });
</script>