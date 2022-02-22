<div class="comunicaciones index">
    <h2><?php echo __('Comunicaciones'); ?></h2>
    <?php
    echo $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'model' => 'Comunicacione']);
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Fecha')); ?></th>
                <th><?php echo $this->Paginator->sort('asunto', __('Asunto')); ?></th>
                <th style="text-align:center"><?php echo $this->Paginator->sort('enviada', __('Enviada')); ?></th>
                <th class="acciones" style="width:170px;text-align:center"><?php echo __('Acciones'); ?>&nbsp;<span class='iom' onclick="mdtoggle()"><?= $this->Html->image('sa.png', ['title' => 'Seleccionar múltiples registros', 'style' => 'width:20px;cursor:pointer']) ?></span></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($comunicaciones as $comunicacione):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($comunicacione['Consorcio']['name']) ?>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y'), $comunicacione['Comunicacione']['created']) ?>&nbsp;</td>
                    <td><?php echo h($comunicacione['Comunicacione']['asunto']) ?></td>
                    <td style="text-align:center">
                        <?php
                        if ($comunicacione['Comunicacione']['enviada']) {
                            echo $this->Html->image('1.png', array('title' => __('La comunicación fue enviada')));
                        } else {
                            echo $this->Html->image('0.png', array('title' => __('La comunicación NO fue enviada todavía')));
                        }
                        ?>
                    </td>
                    <td class="acciones">
                        <?php
                        echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['controller' => 'Comunicaciones', 'action' => 'view', $comunicacione['Comunicacione']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        if (!$comunicacione['Comunicacione']['enviada']) {
                            echo $this->Html->image('edit.png', ['alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['action' => 'edit', $comunicacione['Comunicacione']['id']]]);
                            echo $this->Form->postLink($this->Html->image('send.png', array('title' => __('Enviar comunicación'))), array('action' => 'enviar', $comunicacione['Comunicacione']['id'], $comunicacione['Consorcio']['id']), array('escape' => false), h(__('Desea enviar la Comunicación # %s?', h($comunicacione['Comunicacione']['asunto']))));
                        }
                        if (!$comunicacione['Comunicacione']['enviada']) {
                            echo $this->Form->input('', ['label' => false, 'type' => 'checkbox', 'div' => false, 'class' => 'til_' . $comunicacione['Comunicacione']['id'], 'style' => 'box-shadow:none;transform: scale(1.8);margin:6px;position:absolute']) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            echo $this->Form->postLink($this->Html->image('delete.png', ['alt' => __('Eliminar'), 'title' => __('Eliminar')]), ['action' => 'delete', $comunicacione['Comunicacione']['id']], ['escapeTitle' => false], __('Desea eliminar el dato # %s?', h($comunicacione['Comunicacione']['asunto'])));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="5"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php
    echo $this->element('pagination');
    ?>
    <script>
        $(document).ready(function () {
            $(".toolbar .botones .inline").append('<?= $this->Html->image('send.png', ['title' => 'Envío Múltiple', 'id' => 'multi', 'style' => 'display:none;cursor:pointer', 'onclick' => 'multi()']) ?>');
        });
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
            } else {
                $("#multi").hide();
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
                if (confirm('Desea enviar las Comunicaciones seleccionadas?')) {
                    $("#multi").prop('src', '<?= $this->webroot . 'img/loading.gif' ?>');
                    $("#wait").show();
                    $.ajax({
                        type: "POST",
                        url: "<?= $this->webroot . "Comunicaciones/multienvio" ?>",
                        data: {ids: ids}
                    }).done(function (msg) {
                        console.log(msg);
                        var obj = JSON.parse(msg);
                        if (obj.e === 1) {
                            alert(obj.d);
                        } else {
                            window.location.replace("<?= $this->webroot . "Comunicaciones/index" ?>");
                        }
                    }).fail(function (jqXHR, t) {
                        if (jqXHR.status === 403) {
                            alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                        } else {
                            alert("No se pudo realizar la accion, intente nuevamente");
                        }
                        $("#multi").prop('src', '<?= $this->webroot . 'img/send.png' ?>');
                        $("#wait").hide();
                    });
                }
            }
        }
    </script>
</div>