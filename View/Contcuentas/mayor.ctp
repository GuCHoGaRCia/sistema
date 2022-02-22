<div>
    <h2><?php echo __('Ver Mayor') . " - " . h($consorcios[$consorcio]); ?></h2>
    <?php
    echo "<div class='inline'>";
    //echo $this->Form->create('Contcuenta', ['class' => 'jquery-validation', 'type' => 'post']);
    //echo $this->Form->input('id', ['label' => false, 'div' => false, 'options' => $cuentas, 'type' => 'select', 'selected' => $id ?? 0, 'empty' => '']) . "&nbsp;&nbsp;";
    //echo $this->Form->input('consorcio_id', ['label' => false, 'div' => false, 'type' => 'hidden', 'value' => $consorcio]) . "&nbsp;&nbsp;";
    //echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'id' => 'f1', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => $d, 'required' => 'required']);
    //echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'id' => 'f2', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => $h, 'required' => 'required']);
    //echo "&nbsp;&nbsp;" . $this->Form->end(['label' => 'Ver', 'id' => 'ver']);
    echo "</div>";
    if (isset($resul)) {
        $total = 0;
        ?>
        <div id='seccionaimprimir' style='width:100%'>
            <div class="titulo" style="font-size:14px;font-weight:bold;width:100%;margin:0;margin-top:3px;padding:5px;padding-bottom:0;border:2px dashed #000;white-space:nowrap;text-align:center">
                MAYOR CUENTA - <?= h($ejercicioinfo['nombre'] . " - " . ($consorcios[$consorcio] ?? '') . " - " . ($cuentas[$id] ?? '')) ?>
                <?= " - DEL " . $d . " AL " . $h ?>
            </div>
            <table cellpadding="0" cellspacing="0" style="width:60%;min-width:450px !important">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Fecha') ?></th>
                        <th><?php echo __('Número') ?></th>
                        <th><?php echo __('Descripción') ?></th>
                        <th style="text-align:right"><?php echo __('Debe') ?></th>
                        <th style="text-align:right"><?php echo __('Haber') ?></th>
                        <th style="text-align:right"><?php echo __('Saldo') ?></th>
                        <th class="acciones" style="width:100px"><?php echo __('Acciones') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = $saldo = $debe = $haber = 0;
                    foreach ($resul as $k => $v) {
                        //foreach ($cuentas as $k => $v) {
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr>
                            <td class="borde_tabla"></td>
                            <td style="width:80px"><?= $this->Time->format(__('d/m/Y'), $v['Contasiento']['fecha']) ?></td>
                            <td style="width:80px;text-align:center"><?= h($v['Contasiento']['numero']) ?></td>
                            <td><?= h($v['Contasiento']['descripcion']) ?></td>
                            <td style="width:120px;text-align:right"><?= $this->Functions->money($v['Contasiento']['debe']) ?></td>
                            <td style="width:120px;text-align:right"><?= $this->Functions->money($v['Contasiento']['haber']) ?></td>
                            <td style="width:120px;text-align:right">
                                <?php
                                $debe += $v['Contasiento']['debe'];
                                $haber += $v['Contasiento']['haber'];
                                $saldo += $v['Contasiento']['debe'] - $v['Contasiento']['haber'];
                                echo $this->Functions->money($saldo);
                                ?>
                            </td>
                            <td class="acciones" style="width:100px">
                                <?php
                                echo $this->Html->image('view.png', ['title' => __('Ver Detalle Asiento'), 'style' => 'cursor:pointer', 'onclick' => '$("#verasiento").dialog("open");$("#verasiento").html("<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'' . $this->webroot . 'img/loading.gif' . '\'/></div>");$("#verasiento").load("' . $this->webroot . 'contasientos/view/' . $v['Contasiento']['id'] . '");']);
                                if ($v['Contasiento']['manual']) {
                                    echo $this->Html->image('edit.png', ['title' => __('Editar'), 'style' => 'cursor:pointer', 'onclick' => '$("#verasiento").dialog("open");$("#verasiento").html("<div class=\'info\' style=\'width:200px;margin:0 auto\'>Cargando...<img src=\'' . $this->webroot . 'img/loading.gif' . '\'/></div>");$("#verasiento").load("' . $this->webroot . 'contasientos/edit/' . $v['Contasiento']['id'] . '");']);
                                    echo $this->Form->postLink($this->Html->image('delete.png', ['title' => __('Eliminar')]), ['controller' => 'contasientos', 'action' => 'delete', $v['Contasiento']['id']], ['escapeTitle' => false], __('Desea eliminar el Asiento # %s?', $v['Contasiento']['numero'] . " " . h($v['Contasiento']['descripcion'])));
                                }
                                ?>
                            </td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                        //}
                    }
                    ?>
                    <tr class="altrow">
                        <td class="bottom_i"></td>
                        <td colspan="7"></td>
                        <td class="bottom_d"></td>
                    </tr>
                    <tr class="altrow">
                        <td class="borde_tabla"></td>
                        <td colspan="3" style="text-align:right;font-weight:bold">Total</td>
                        <td style="text-align:right;border-top:2px solid black"><?= $this->Functions->money($debe) ?></td>
                        <td style="text-align:right;border-top:2px solid black"><?= $this->Functions->money($haber) ?></td>
                        <td style="text-align:right;border-top:2px solid black"><?= $this->Functions->money($debe-$haber) ?></td>
                        <td></td>
                        <td class="borde_tabla"></td>
                    </tr>
            </table>
        </div>
        <?php
    }
    ?>
</div>
<?= "<div id='verasiento' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; ?>
<script>
    $(function () {
        $("#ContcuentaId").select2({language: "es", placeholder: "Seleccione una Cuenta..."});
        $("#ContcuentaConsorcio").select2({language: "es", placeholder: "Seleccione un Consorcio..."});
        var dialog1 = $("#verasiento").dialog({
            autoOpen: false, height: "auto", width: "750", maxWidth: "750",
            position: {at: "top top"},
            closeOnEscape: true,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            close: function (event, ui) {
<?php /* Evita q al abrir varias veces el edit, el datepicker aparezca ya abierto (re molesto) */ ?>
                $(".dp").datepicker('destroy');
            },
            modal: true,
            buttons: {
                Cerrar: function () {
                    dialog1.dialog("close");
                }
            }
        });
    });
    $("#ver").on("click", function (e) {
        if ($("#ContcuentaConsorcio :selected").val() === "") {
            e.preventDefault();
            alert("Seleccione un Consorcio...");
            return false;
        }
        if ($("#ContcuentaId :selected").val() === "") {
            e.preventDefault();
            alert("Seleccione una Cuenta...");
            return false;
        }
        var f1 = $("#f1").val();
        var f2 = $("#f2").val();
        var x = new Date(f1.substr(6, 4), f1.substr(3, 2) - 1, f1.substr(0, 2), 0, 0, 0);
        var y = new Date(f2.substr(6, 4), f2.substr(3, 2) - 1, f2.substr(0, 2), 0, 0, 0);
        if (x > y) {
            alert('<?= __('La fecha Desde debe ser menor o igual a Hasta') ?>');
            return false;
        }
        $("#ver").prop('disabled', true);
        $("#ContcuentaMayorForm").submit();
    });
    $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
</script>
