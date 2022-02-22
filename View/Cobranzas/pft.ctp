<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Agregar Interés por Pago Fuera de Término'); ?></h2>
        <?php
        echo $this->Form->create('Cobranza', ['class' => 'inline', 'id' => 'verform']);
        echo $this->JqueryValidation->input('consorcio_id', ['label' => '', 'empty' => '']);
        echo $this->JqueryValidation->input('tipos', ['label' => '', 'empty' => '']);
        echo $this->Form->end(array('label' => __('Ver'), 'id' => 'ver'));
        ?>
    </fieldset>
    <div id="contenido" style="width:auto;height:auto">
        <?php
        $hay = false;
        $todasVerde = true;
        if (isset($this->request->data['Cobranza']['tipos']) && isset($data['vencimiento'])) {
            if (!empty($data['cobranzas'])) {
                foreach ($data['cobranzas'] as $k => $v) {
                    if (strtotime($v['Cobranza']['fecha']) > strtotime($data['vencimiento'])) {
                        $hay = true;
                        break;
                    }
                }

                if ($hay) {
                    echo "Fecha l&iacute;mite: " . $this->Time->format(__('d/m/Y'), $data['vencimiento']);
                    echo " - Dias: 30 ";
                    echo " - Inter&eacute;s del Consorcio: " . $interes . "% - Porcentaje diario: " . round($interes / 30, 4);
                    echo $this->Form->create('Cobranza', array('class' => 'jquery-validation', 'url' => ['controller' => 'Cobranzas', 'action' => 'pft'], 'id' => 'formperiodo'));
                    echo "<input type='checkbox' onClick=\"for (c in document.getElementsByClassName('til')) document.getElementsByClassName('til').item(c).checked = this.checked\" style=\"cursor:pointer\" />&nbsp;Tildar todos - Destildar todos<br>";
                    echo $this->JqueryValidation->input('tipos', ['div' => false, 'type' => 'hidden', 'value' => $this->request->data['Cobranza']['tipos']]);
                    echo $this->JqueryValidation->input('consorcio_id', ['div' => false, 'type' => 'hidden', 'value' => $this->request->data['Cobranza']['consorcio_id']]);

                    foreach ($data['cobranzas'] as $k => $v) {
                        if (strtotime($v['Cobranza']['fecha']) > strtotime($data['vencimiento'])) {
                            // pagó fuera de termino
                            $diasretraso = abs(strtotime($data['vencimiento']) - strtotime($v['Cobranza']['fecha'])) / (60 * 60 * 24);
                            $opcion = false;
                            if (in_array($v['Cobranza']['id'], $data['pftcargados'])) {
                                $opcion = true;
                            }

                            echo "<div class='inline' " . ($opcion ? 'style="color:green;font-weight:bold"' : '') . ">";

                            echo "Importe abonado: " . $this->Functions->money($v['Cobranzatipoliquidacione']['amount']) . " - D&iacute;as de retraso: <b>" . $diasretraso . "</b>";
                            if (!$opcion) {
                                $todasVerde = false;
                                echo $this->JqueryValidation->input('c_' . $v['Cobranza']['propietario_id'] . "_" . $v['Cobranza']['id'], ['label' => false, 'div' => false, 'id' => 'c_' . $v['Cobranza']['propietario_id'], 'type' => 'checkbox', 'style' => 'width:20px;box-shadow:0px !important;', 'class' => 'til']);
                            }

                            $total = round($v['Cobranzatipoliquidacione']['amount'] * $diasretraso * round($interes / 30, 4) / 100, 2);
                            echo " - Total: <span class='negrita'>$total</span>";
                            echo " - " . $v['Propietario']['name'] . " - " . $v['Propietario']['unidad'] . " (" . $v['Propietario']['code'] . ")";
                            echo "</div>";
                        }
                    }
                    echo "<br>";
                    echo $this->Form->end(array('label' => __('Guardar'), 'id' => 'guardar'));
                }
            }

            if (!$hay) {
                ?>
                <div class="info">No se encuentran Cobranzas fuera de t&eacute;rmino para el Consorcio y Tipo de Liquidaci&oacute;n seleccionados</div>
                <?php
            }
        }
        ?>
    </div>
</div>
<script>
    var interes = <?= isset($interes) ? $interes : 0 ?>;
    var porcdiario = parseFloat(interes / 30).toFixed(4);
    var todasverdes = <?= $todasVerde ? 'true' : 'false' ?>;
    $(document).ready(function () {
        $("#CobranzaConsorcioId").select2({language: "es", placeholder: "<?= __("Seleccione consorcio...") ?>"});
        $("#CobranzaTipos").select2({language: "es", placeholder: "<?= __("Seleccione tipo...") ?>"});
        $(document).on('click', '#guardar', function () {
            var hay = false;
            $('input[id^="c_"]').each(function () {
                if ($(this).is(":checked")) {
                    hay = true;
                    return false;
                }
            });

            if (todasverdes) {
                alert("Todos los Pagos fuera de Término ya fueron cargados");
                return false;
            }
            if (!hay) {
                alert("Debe seleccionar al menos un Propietario");
                return false;
            }

            $('input[id^="c_"]').each(function () {
                if (!$(this).is(":checked")) {
                    this.remove();<?php /* no envio los vacios o cero */ ?>
                }
            });
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
            $("#verform").submit();
        });
    });

    function sett(id) {
        $("#c_" + id).val(porcdiario * parseFloat($("#d_c_" + id).val()) * parseFloat($("#m_c_" + id).val()) / 100);
    }
</script>
<style>
    .negrita{
        font-weight:bold;
    }
</style>