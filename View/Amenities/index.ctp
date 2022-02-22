<div class="amenities index">
    <h2><?php echo __('Amenities'); ?></h2>
    <?php echo $this->element('toolbar', ['filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Amenity']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitado', __('Habilitada')); ?></th>
                <th class="acciones" style="width:120px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            //debug($amenities);
            foreach ($amenities as $amenity):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($amenity['Consorcio']['name']) ?></td>
                    <td><span class="nombre" data-value="<?php echo h($amenity['Amenity']['nombre']) ?>" data-pk="<?php echo h($amenity['Amenity']['id']) ?>"><?php echo h($amenity['Amenity']['nombre']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($amenity['Amenity']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Amenitie Habilitada'))), array('controller' => 'Amenities', 'action' => 'invertir', 'habilitado', h($amenity['Amenity']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:120px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('config.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:360px;margin-left:-385px;font-weight:bold">
                                <ul>
                                    <li title='Cantidad máxima de reservas anuales por Propietario (solo si "Reserva condicional" se encuentra habilitado)'>
                                        Max Reservas por Propietario (cero=sin l&iacute;mite):
                                        <span id="max<?= $amenity['Amenitiesconfig']['id'] ?>" data-value="<?php echo h($amenity['Amenitiesconfig']['maxreservasporpropietario']) ?>" data-pk="<?php echo $amenity['Amenitiesconfig']['id'] ?>"><?php echo h($amenity['Amenitiesconfig']['maxreservasporpropietario']) ?></span>
                                    </li>
                                    <script>
                                        $(function () {
                                            $('#max<?= $amenity['Amenitiesconfig']['id'] ?>').editable({
                                                value: 'CGP', name: 'maxreservasporpropietario', type: 'text',
                                                url: '<?php echo $this->webroot; ?>Amenitiesconfigs/editar', placement: 'left',
                                                success: function (n, r) {
                                                    if (n) {
                                                        return n
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                    <li title='Cantidad de dias a partir del dia actual en los cuales permitir reservas'>
                                        D&iacute;as habilitados reserva (cero=sin l&iacute;mite):
                                        <span id="dhr<?= $amenity['Amenitiesconfig']['id'] ?>" data-value="<?php echo h($amenity['Amenitiesconfig']['diashabilitadosreserva']) ?>" data-pk="<?php echo $amenity['Amenitiesconfig']['id'] ?>"><?php echo h($amenity['Amenitiesconfig']['diashabilitadosreserva']) ?></span>
                                    </li>
                                    <script>
                                        $(function () {
                                            $('#dhr<?= $amenity['Amenitiesconfig']['id'] ?>').editable({
                                                value: 'CGP', name: 'diashabilitadosreserva', type: 'text',
                                                url: '<?php echo $this->webroot; ?>Amenitiesconfigs/editar', placement: 'left',
                                                success: function (n, r) {
                                                    if (n) {
                                                        return n
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                    <li title='Cantidad de dias anteriores al dia resevado en los que, si se cancela la reserva, se multa'>
                                        D&iacute;as habilitados cancelación (cero=sin l&iacute;mite):
                                        <span id="dhc<?= $amenity['Amenitiesconfig']['id'] ?>" data-value="<?php echo h($amenity['Amenitiesconfig']['diashabilitadoscancelacion']) ?>" data-pk="<?php echo $amenity['Amenitiesconfig']['id'] ?>"><?php echo h($amenity['Amenitiesconfig']['diashabilitadoscancelacion']) ?></span>
                                    </li>
                                    <script>
                                        $(function () {
                                            $('#dhc<?= $amenity['Amenitiesconfig']['id'] ?>').editable({
                                                value: 'CGP', name: 'diashabilitadoscancelacion', type: 'text',
                                                url: '<?php echo $this->webroot; ?>Amenitiesconfigs/editar', placement: 'left',
                                                success: function (n, r) {
                                                    if (n) {
                                                        return n
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                    <li>
                                        Reserva condicional: <?php echo $this->Html->link($this->Html->image(h($amenity['Amenitiesconfig']['reservacondicional'] ? '1' : '0') . '.png', ['title' => __('Permite reservar condicionalmente (se permite reservar condicionalmente a la espera de la cancelación de otro turno reservado con anterioridad por otro Propietario)')]), ['controller' => 'amenitiesconfigs', 'action' => 'invertir', 'reservacondicional', h($amenity['Amenitiesconfig']['id'])], ['class' => 'status', 'escape' => false]); ?>
                                    </li>
                                    <li>
                                        Seleccionar quien realiza limpieza: <?php echo $this->Html->link($this->Html->image(h($amenity['Amenitiesconfig']['seleccionarquienrealizalimpieza'] ? '1' : '0') . '.png', ['title' => __('Permite que en la reserva se seleccione quien realiza la limpieza del Amenitie (Propietario o Encargado)')]), ['controller' => 'amenitiesconfigs', 'action' => 'invertir', 'seleccionarquienrealizalimpieza', h($amenity['Amenitiesconfig']['id'])], ['class' => 'status', 'escape' => false]); ?>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        echo $this->Html->image('cal.png', ['title' => __('Gestionar Turnos'), 'class' => 'hand', 'onclick' => "$('#cal').dialog('open');$('#cal').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');dialogLoad('#cal','" . $this->webroot . "amenitiesturnos/view/" . $amenity['Amenity']['id'] . "');"]);
                        echo $this->Html->image('reserva.png', ['title' => __('Ver Reservas'), 'class' => 'hand', 'onclick' => "$('#cal').dialog('open');$('#cal').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');dialogLoad('#cal','" . $this->webroot . "amenities/view/" . $amenity['Amenity']['id'] . "');"]);
                        echo $this->Html->image('edit.png', ['title' => __('Editar'), 'url' => ['action' => 'edit', $amenity['Amenity']['id']]]);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $(function () {
                $('.nombre').editable({type: 'text', name: 'nombre', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>amenities/editar', placement: 'right'});
                var dialog = $("#cal").dialog({
                    autoOpen: false, height: "auto", width: "90%", maxWidth: "90%",
                    position: {at: "center top"},
                    closeOnEscape: true,
                    modal: true,
                });
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="4"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<style>
    .editableform .form-control{
        width:100px !important;
    }
</style>
<?php
echo "<div id='cal' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>";
