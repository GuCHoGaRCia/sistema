<div class="liquidations index">
    <h2><?php echo __('Liquidaciones Abiertas / Cerradas'); ?></h2>
    <?php
    echo $this->element('toolbar', array('pagecount' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagesearch' => true, 'pagenew' => true, 'export' => true, 'model' => 'Liquidation'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidations_type_id', __('Tipo')); ?></th>
                <th><?php echo $this->Paginator->sort('periodo', __('Período')); ?></th>
                <th><?php echo $this->Paginator->sort('vencimiento', __('Vencimiento')); ?></th>
                <th><?php echo $this->Paginator->sort('limite', __('Límite')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('cerrada', __('Prorrateada')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('bloqueada', __('Bloqueada')); ?></th>
                <th class="acciones" style="width:85px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($liquidations as $liquidation):
                $class = $liquidation['Liquidation']['bloqueada'] ? ' class="success-message"' : ' class="error-message"';
                if ($i++ % 2 == 0) {
                    $class = $liquidation['Liquidation']['bloqueada'] ? ' class="altrow success-message"' : ' class="altrow error-message"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($liquidation['Consorcio']['name']) ?></td>
                    <td><?php echo h($liquidation['LiquidationsType']['name']); ?></td>
                    <?php
                    if ($liquidation['Liquidation']['bloqueada'] == 0) {
                        ?>
                        <td><span class="periodo" data-value= "<?php echo h($liquidation['Liquidation']['periodo']) ?>" data-pk = "<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo h($liquidation['Liquidation']['periodo']) ?></span>&nbsp;</td>
                        <td><span class="vencimiento" data-value="<?php echo h($liquidation['Liquidation']['vencimiento']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['vencimiento']) ?></span>&nbsp;</td>
                        <td><span class="limite" data-value="<?php echo h($liquidation['Liquidation']['limite']) ?>" data-pk="<?php echo h($liquidation['Liquidation']['id']) ?>"><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['limite']) ?></span>&nbsp;</td>
                        <?php
                    } else {
                        ?>
                        <td><?php echo h($liquidation['Liquidation']['periodo']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['vencimiento']) ?>&nbsp;</td>
                        <td><?php echo $this->Time->format(__('d/m/Y'), $liquidation['Liquidation']['limite']) ?>&nbsp;</td>
                        <?php
                    }
                    ?> 
                    <td class="center">
                        <?php
                        echo $this->Html->image(($liquidation['Liquidation']['cerrada'] ? '1' : '0') . '.png', array('title' => __('La liquidación fue prorrateada?')));
                        //echo "<span style='font-size:10px'>" . $this->Time->format(__('d/m/Y H:i:s'), $liquidation['Liquidation']['closed']) . "</span>";
                        ?>
                    </td>
                    <td class="center" <?= !empty($liquidation['Liquidation']['closed']) ? 'title="Último Prorrateo: ' . $this->Time->format(__('d/m/Y H:i:s'), $liquidation['Liquidation']['closed']) . '"' : '' ?>>
                        <?php
                        echo $this->Html->image(h($liquidation['Liquidation']['bloqueada'] ? '1' : '0') . '.png', array('title' => __('La liquidación se encuentra bloqueda?')));
                        if ($liquidation['Liquidation']['bloqueada']) {
                            echo "<span style='font-size:10px'>" . $this->Time->format(__('d/m/Y H:i:s'), $liquidation['Liquidation']['modified']) . "</span>";
                        }
                        ?>
                    </td>
                    <td class="acciones" style="width:100px">
                        <?php
                        if ($liquidation['Liquidation']['cerrada'] == 1) {
                            ?>
                            <span class="contenedorreportes">
                                <?php
                                echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                                ?>
                                <span class="listareportes" style="width:220px">
                                    <ul>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/resumenesdecuentas/<?= $liquidation['Liquidation']['id'] . "/" . $client_id ?>" target="_blank" rel="nofollow noopener noreferrer">Resumen cuenta</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/resumengastos/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Resumen gastos</a>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->Html->image('link.png', array('title' => __('Finalizar Liquidación'), 'onclick' => 'finalizar(' . $liquidation['Liquidation']['id'] . ')'), [], ['escapeTitle' => false]) ?>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/composicionsaldos/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Compos. saldos</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/gastosparticularesporcuenta/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Gastos particulares</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/planillapagos/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla de pagos</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/cuentacorrienteliquidacion/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Cuenta corriente</a>
                                        </li>
                                        <?php
                                        // solo muestro el estado de disponibilidad de la ORDINARIA. 2018/12/10 al final, muestro de todas, para llevar control separado de ord y extra
                                        //if ($liquidation['LiquidationsType']['prefijo'] == 0) {
                                        ?>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/edliquidacion/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Estado disponibilidad</a>
                                        </li>
                                        <?php
                                        //}
                                        ?>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/resumenperiodo/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Resumen Per&iacute;odo</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/cobranzasrecibidas/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Cobranzas recibidas</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/propietariosdeudores/<?= $liquidation['Liquidation']['id'] . "/" . $liquidation['Consorcio']['id'] ?>" target="_blank">Propietarios deudores</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/propietariosacreedores/<?= $liquidation['Liquidation']['id'] . "/" . $liquidation['Consorcio']['id'] ?>" target="_blank">Propietarios acreedores</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Reports/recibosliquidacion/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Recibos</a> - 
                                            <a href="<?php echo $this->webroot; ?>Reports/recibosliquidacion/<?= $liquidation['Liquidation']['id'] . "/" . $_SESSION['Auth']['User']['client_id'] ?>/1" target="_blank" rel="nofollow noopener noreferrer">Recibos2</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo $this->webroot; ?>Liquidations/verPagos/<?= $liquidation['Liquidation']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Listar Pagos Proveedor</a>
                                        </li>
                                    </ul>
                                </span>
                            </span>  
                            <?php
                        }

                        // verifico cual es la liquidacion siguiente a la ultima prorrateada (o es la inicial) para saber a cual ponerle el icono de "Prorratear". 
                        // Solo se puede prorratear la liquidacion siguiente a la ultima prorrateada
                        if ($liquidation['Liquidation']['bloqueada'] == 0 && in_array($liquidation['Liquidation']['id'], $activas)) {
                            echo $this->Html->image('liquidation.png', array('alt' => __('Prorratear liquidacion'), 'title' => __('Prorratear liquidacion'), 'style' => 'cursor:pointer', 'onclick' => 'p(this,' . $liquidation['Liquidation']['id'] . ')')); //, array('action' => 'controlesCierres', $liquidation['Liquidation']['id']), array('escape' => false,));
                            echo $this->Form->input('', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $liquidation['Liquidation']['id'], 'style' => 'box-shadow:none;transform: scale(1.8);margin:6px;position:absolute']);
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="8"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php
    echo $this->element('pagination');
    ?>
</div>
<div class="parent" style='display:none' id='wait'><div class='child'><img src="<?= $this->webroot; ?>img/loading.gif" /> Espere por favor ...</div></div>
<style>
    .parent{
        background-color: rgba(0,0,0,0.5);
        position:fixed;
        width:100%;
        height:100%;
        top:0px;
        left:0px;
        z-index:100111110;
    }
    .child{
        background-color: rgba(240,240,240,0);
        color:white;
        font-size:25px;
        font-weight:bold;
        position: absolute;
        width: 100%;
        height: auto;
        top: 50%;
        left: 50%;
        margin-left: -100px; /* margin is -0.5 * dimension */
    }
</style>
<script>
    $(document).ready(function () {
        $(".toolbar .botones .inline").append('<?= $this->Html->image('liquidation.png', ['title' => 'Multiprorrateo', 'id' => 'multi', 'style' => 'display:none;cursor:pointer', 'onclick' => 'multi()']) ?>');
        $(".toolbar .botones .inline").prepend('<?= $this->Html->image('link.png', ['title' => 'Finalizar liquidaciones', 'id' => 'finalizar', 'style' => 'display:none;cursor:pointer', 'onclick' => 'finalizar()']) ?>');
        $('.periodo').editable({type: 'text', name: 'periodo', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Liquidations/editar', placement: 'right'});
        $('.vencimiento').editable({type: 'date', name: 'vencimiento', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Liquidations/editar', placement: 'right'});
        $('.limite').editable({type: 'date', name: 'limite', viewformat: 'dd/mm/yyyy', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Liquidations/editar', placement: 'left'});
    });
    function p(a, id) {<?php /* Para Prorratear liquidaciones */ ?>
        if (confirm("Desea prorratear la liquidación?")) {
            $(a).attr("src", "<?= $this->webroot; ?>img/loading.gif");
            location.href = '<?= $this->webroot ?>Liquidations/controlesCierres/' + id;
        }
    }
</script>
<script>
    var cont = 0;
    function mdtoggle() {
        var tildar = true;
        var todostildados = true;
        var todosdestildados = true;
        $("input[class^='til_']").each(function () {
            if (tildar) {
                if (!$(this).is(':checked')) {
                    todostildados = false;
                    return false;
                }
            } else {
                if ($(this).is(':checked')) {
                    todosdestildados = false;
                    return false;
                }
            }
        });

        tildar = (tildar && todostildados) || (!tildar && todosdestildados) ? !tildar : tildar;
        $("input[class^='til_']").each(function () {

<?php /* Si la accion es tildar y estan todos tildados ó si la accion es destildar y estan todos destildados realizo la accion inversa (!tildar) sino queda la opcion tildar y tendrian que hacer 2 clicks */ ?>
            if (tildar) {
                if (!$(this).is(':checked')) {
                    cont++;
                    $(this).prop('checked', true);
                }
            } else {
                if ($(this).is(':checked')) {
                    cont--;
                    $(this).prop('checked', false);
                }
            }
        });
        hs();
        tildar = !tildar;
    }
    $(document).on('click', "input[class^='til_']", function () {
        if ($(this).is(':checked')) {
            cont++;
        } else {
            cont--;
        }
        hs();
    });

    function hs() {
        if (cont > 1) {
            $("#multi").show();
            $("#finalizar").show();
        } else {
            $("#multi").hide();
            $("#finalizar").hide();
        }
    }
    function multi() {
        var ids = [];
        $("input[class^='til_']").each(function () {
            if ($(this).is(':checked')) {
                var strid = $(this).prop('class');
                var id = strid.replace('til_', '');
                ids.push(id);
            }
        });
        if (ids.length > 0) {
            if (confirm('Desea Prorratear las liquidaciones? El proceso puede demorar unos minutos..')) {
                $("#multi").prop('src', '<?= $this->webroot . 'img/loading.gif' ?>');
                $("#wait").show();
                $.ajax({
                    type: "POST",
                    url: "<?= $this->webroot . "Liquidations/multiprorrateo" ?>",
                    data: {ids: ids}
                }).done(function (msg) {
                    window.location.replace("<?= $this->webroot . "Liquidations/index" ?>");
                }).fail(function (jqXHR, t) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion, intente nuevamente");
                    }
                    $("#multi").prop('src', '<?= $this->webroot . 'img/prorrateo.png' ?>');
                    $("#wait").hide();
                });
                $("#multi").hide();
                $("#finalizar").hide();
                cont = 0;
            }
        }
    }
    function finalizar(id) {<?php /* id viene seteado cuando finalizan liquidaciones individualmente */ ?>
        var ids = [];
        if (typeof id !== "undefined") {
            ids.push(id);
        } else {
            $("input[class^='til_']").each(function () {
                if ($(this).is(':checked')) {
                    var strid = $(this).prop('class');
                    var id = strid.replace('til_', '');
                    ids.push(id);
                }
            });
        }
        if (ids.length > 0) {
            if (confirm('Desea finalizar las Liquidaciones seleccionadas?')) {
                $("#finalizar").prop('src', '<?= $this->webroot . 'img/loading.gif' ?>');
                $("#wait").show();
                $.ajax({
                    type: "POST",
                    url: "<?= $this->webroot . "Colaimpresiones/finalizar" ?>",
                    data: {ids: ids}
                }).done(function (msg) {
                    var obj = JSON.parse(msg);
                    if (obj.e === 1) {
                        $("#wait").hide();
                        alert(obj.d);
                    } else {
                        location.reload();
                    }
                }).fail(function (jqXHR, t) {
                    if (jqXHR.status === 403) {
                        alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                    } else {
                        alert("No se pudo realizar la accion, intente nuevamente");
                    }
                    $("#finalizar").prop('src', '<?= $this->webroot . 'img/link.png' ?>');
                    $("#wait").hide();
                });
                $("#multi").hide();
                $("#finalizar").hide();
                cont = 0;
            }
        }
        $("input[class^='til_']").each(function () {
            $(this).prop('checked', false);
        });
    }
</script>