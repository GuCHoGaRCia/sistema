<div>
    <h2><?php echo __('Ver Balance'); ?></h2>
    <?php
    $meses = $this->request->data['Contasiento']['meses'] ?? $meses ?? [];
    $selectconsor = isset($this->request->data['Contasiento']['consorcio_id']) ? $this->request->data['Contasiento']['consorcio_id'] : 0;
    $ejercicios = [];
    if (isset($ejerciciosconsor) && !empty($ejerciciosconsor)) {
        foreach ($ejerciciosconsor as $k => $v) {
            $ejercicios[$k] = $v;
        }
    }
    $selectejercicio = isset($this->request->data['Contasiento']['ejercicio']) ? $this->request->data['Contasiento']['ejercicio'] : 0;
    $selectmes = isset($this->request->data['Contasiento']['mes']) ? $this->request->data['Contasiento']['mes'] : 0;
    echo "<div class='inline'>";
    echo $this->Form->create('Contasiento', ['class' => 'inline']);
    echo $this->Form->input('consorcio_id', ['label' => false, 'options' => $consorcios, 'type' => 'select', 'selected' => $selectconsor, 'empty' => '']);
    echo "<div id='ejercicio'" . (empty($selectejercicio) ? " style='display:none'" : "") . ">" . $this->Form->input('ejercicio', ['label' => false, 'div' => false, 'options' => $ejercicios, 'type' => 'select', 'selected' => $selectejercicio, 'empty' => '', 'style' => 'width:150px']) . "</div>";
    echo "<div id='mes' " . (empty($selectmes) ? " style='display:none'" : "") . ">" . $this->Form->input('mes', ['label' => false, 'div' => false, 'options' => $meses, 'type' => 'select', 'selected' => $selectmes, 'empty' => '', 'style' => 'width:150px']) . "</div>";
    echo "<div id='ocultar' " . (empty($selectmes) ? " style='display:none'" : "") . ">" . $this->Form->input('ocultar', ['label' => __('Ocultar en cero'), 'type' => 'checkbox', 'style' => 'margin-top:14px;transform: scale(1.3);border:1px solid grey']) . "</div>";
    echo $this->Form->end(['label' => 'Ver', 'id' => 'ver']);
    echo "</div>";
    if (isset($asientos)) {
        $total = 0;
        ?>
        <div id='seccionaimprimir' style='width:100%'>
            <div class="titulo" style="font-size:14px;font-weight:bold;width:100%;margin:0;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                BALANCE - <?= h($selectconsor != 0 ? $consorcios[$selectconsor] . " - " . $ejerciciosconsor[$selectejercicio] : '') ?>
            </div>
            <table cellpadding="0" cellspacing="0" style="width:60%;min-width:450px !important">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Título') ?></th>
                        <th style="text-align:right"><?php echo __('Saldo') ?></th>
                        <th class="acciones" style="width:70px"><?php echo __('Acciones') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($titulos as $k => $v) {
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr class="altrow">
                            <td class="borde_tabla"></td>
                            <td><?= str_repeat("&nbsp;", $arbol[$k]) . h($v) ?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="acciones" style="width:70px"></td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                        $cuentasdeltitulo = $this->Functions->find2($cuentas, ['conttitulo_id' => $k], true);

                        foreach ($cuentasdeltitulo as $a => $b) {
                            if ($this->request->data['Contasiento']['ocultar'] === '1' && $asientos[$cuentas[$b]['id']] == 0) {
                                continue;
                            }
                            $total += $asientos[$cuentas[$b]['id']];
                            ?>
                            <tr>
                                <td class="borde_tabla"></td>
                                <td><?= str_repeat("&nbsp;", $arbol[$k] + 5) . h($cuentas[$b]['name2']) ?> &nbsp;</td>
                                <td style="text-align:right"><?= $this->Functions->money($asientos[$cuentas[$b]['id']]) ?> &nbsp;</td>
                                <td class="acciones" style="width:70px">
                                    <?php
                                    echo $this->Form->postLink($this->Html->image('view.png', ['title' => __('Ver Mayor')]), ['action' => 'mayor', 'controller' => 'contcuentas', $cuentas[$b]['id'], $selectconsor, $selectejercicio, date("Y-m", strtotime($selectmes))], ['escapeTitle' => false, 'target' => '_blank']);
                                    ?>
                                </td>
                                <td class="borde_tabla"></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="3"></td>
                        <td class="bottom_d"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td style="text-align:right;font-weight:bold">Total</td>
                        <td style="text-align:right;border-top:2px solid black"><?= $this->Functions->money($total) ?>&nbsp;&nbsp;</td>
                        <td></td>
                        <td class="borde_tabla"></td>
                    </tr>
            </table>
        </div>
        <?php
    }
    ?>
</div>
<script>
    $(function () {
        $("#ContasientoConsorcioId").select2({language: "es", placeholder: "Seleccione Consorcio..."});
        $("#ContasientoEjercicio").select2({language: "es", placeholder: "Seleccione Ejercicio..."});
        $("#ContasientoMes").select2({language: "es", placeholder: "Seleccione Mes..."});
    });

    $("#ContasientoConsorcioId").on("change", function (e) {
        $("#ejercicio").hide('fast');
        $("#mes").hide('fast');
        $("#ocultar").hide('fast');
        $("#ContasientoEjercicio option").remove();
        $("#ContasientoMes option").remove();
        getEjercicios($("#ContasientoConsorcioId :selected").val());
        $("#ejercicio").show('fast');
    });

    $("#ContasientoEjercicio").on("change", function (e) {
        getMeses($("#ContasientoEjercicio :selected").val());
        $("#mes").show('fast');
        $("#ocultar").show('fast');
    });

    $("#ver").on("click", function (e) {
        if ($("#ContasientoConsorcioId :selected").val() === "") {
            e.preventDefault();
            alert("Seleccione un Consorcio...");
            return false;
        }
        $("#ver").prop('disabled', true);
        $("#ContasientoBalanceForm").submit();
    });

    function getEjercicios(id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Contejercicios/getEjercicios", cache: false, data: {id: id}}).done(function (msg) {
            if (msg) {
                var obj = jsonParseOrdered(msg);
                $("#ContasientoEjercicio option").remove();
                $.each(obj, function (j, val) {
                    $("#ContasientoEjercicio").append($("<option></option>").attr("value", hhh(val["k"])).text(hhh(val["v"])));
                });
                $('#ContasientoEjercicio').select2().trigger('change');
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener la información. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener la información, intente nuevamente");
            }
        });
    }

    function getMeses(id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Contejercicios/getMeses", cache: false, data: {id: id}}).done(function (msg) {
            if (msg) {
                var obj = jsonParseOrdered(msg);
                $("#ContasientoMes option").remove();
                $("#ContasientoMes").append($("<option></option>").attr("value", '').text('Seleccione Mes...'));
                $.each(obj, function (j, val) {
                    $("#ContasientoMes").append($("<option></option>").attr("value", hhh(val["k"])).text(hhh(val["v"])));
                });
                $('#ContasientoMes').val("<?= date("Y-m-01") ?>");<?php /* Si el mes actual esta en la lista de meses, lo preselecciono */ ?>
                $('#ContasientoMes').select2().trigger('change');
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener la información. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener la información, intente nuevamente");
            }
        });
    }
</script>