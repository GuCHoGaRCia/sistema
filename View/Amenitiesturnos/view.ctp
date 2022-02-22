<?php
echo $this->Flash->render('otro');
echo $this->Flash->render();
?>
<h4>Turnos disponibles para Reserva<br><?= h($amenity['Consorcio']['name'] . " - " . $amenity['Amenity']['nombre']) ?></h4>
<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td class="esq_i"></td>
            <th><?php echo __('Dia') ?></th>
            <th><?php echo __('Inicio') ?></th>
            <th><?php echo __('Fin') ?></th>
            <th class='center'><?php echo __('Habilitado') ?></th>
            <th class="acciones" style="width:50px"><?php echo __('Acciones'); ?></th>
            <td class="esq_d"></td>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        $diassemana = [2 => 'Lunes', 3 => 'Martes', 4 => 'Miércoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sábado', 1 => 'Domingo'];
        foreach ($amenitiesturnos as $amenitiesturno):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?>>
                <td class="borde_tabla"></td>
                <td><span class="center" id="dia<?= $amenitiesturno['Amenitiesturno']['id'] ?>" data-value="<?php echo $amenitiesturno['Amenitiesturno']['diasemana'] ?>" data-pk="<?php echo $amenitiesturno['Amenitiesturno']['id'] ?>"><?php echo h($diassemana[$amenitiesturno['Amenitiesturno']['diasemana']]) ?></span>
                    <script>
                        $(function () {
                            $('#dia<?= $amenitiesturno['Amenitiesturno']['id'] ?>').editable({
                                placement: 'right', name: 'diasemana', type: 'select', url: '<?php echo $this->webroot; ?>amenitiesturnos/editar', success: function (n, r) {
                                    if (n) {
                                        return n
                                    }
                                },
                                source: [<?php
        foreach ($diassemana as $j => $l) {
            echo "{value: $j, text: '" . h($l) . "'},";
        }
        ?>]
                            });
                        });
                    </script>  
                </td>
                <td><span class="inicio" data-value="<?php echo h(date('H:i', strtotime($amenitiesturno['Amenitiesturno']['inicio']))) ?>" data-pk="<?php echo h($amenitiesturno['Amenitiesturno']['id']) ?>"><?php echo h(date('H:i', strtotime($amenitiesturno['Amenitiesturno']['inicio']))) ?></span>&nbsp;</td>
                <td><span class="fin" data-value="<?php echo h(date('H:i', strtotime($amenitiesturno['Amenitiesturno']['fin']))) ?>" data-pk="<?php echo h($amenitiesturno['Amenitiesturno']['id']) ?>"><?php echo h(date('H:i', strtotime($amenitiesturno['Amenitiesturno']['fin']))) ?></span>&nbsp;</td>
                <td class='center'><?php echo $this->Html->link($this->Html->image(h($amenitiesturno['Amenitiesturno']['habilitado'] ? '1' : '0') . '.png', ['title' => __('Habilitado')]), ['controller' => 'amenitiesturnos', 'action' => 'invertir', 'habilitado', h($amenitiesturno['Amenitiesturno']['id'])], ['class' => 'status', 'escape' => false]); ?></td>
                <td class="acciones" style="width:50px">
                    <?php
                    echo $this->Html->image('delete.png', ['title' => __('Eliminar'), 'style' => 'cursor:pointer', 'onclick' => 'del("/amenitiesturnos/delete",' . h($amenitiesturno['Amenitiesturno']['id']) . ');']);
                    ?>
                </td>
                <td class="borde_tabla"></td>
            </tr>
        <?php endforeach; ?>
    <script>
        $(document).ready(function () {
            $('.inicio').editable({type: 'text', name: 'inicio', success: function (n) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>amenitiesturnos/editar', placement: 'right'});
            $('.fin').editable({type: 'text', name: 'fin', success: function (n) {
                    if (n) {
                        return n
                    }
                }, url: '<?php echo $this->webroot; ?>amenitiesturnos/editar', placement: 'right'});
        });
    </script>
    <tr class="altrow">
        <td class="bottom_i"></td>
        <td colspan="5"></td>
        <td class="bottom_d"></td>
    </tr>
</table>
<h4>Agregar nuevo Turno</h4>
<div class="amenitiesturnos form">
    <?php echo $this->Form->create('Amenitiesturno', ['class' => 'jquery-validation']); ?>
    <fieldset>
        <?php
        echo "<div class='inline'>";
        echo $this->JqueryValidation->input('amenitie_id', ['label' => false, 'type' => 'hidden', 'value' => $amenity['Amenity']['id']]);
        echo $this->JqueryValidation->input('diasemana', ['label' => __('Día de la Semana'), 'options' => $diassemana]);
        echo $this->JqueryValidation->input('inicio', ['label' => __('Hora Inicio'), 'timeFormat' => '24', 'interval' => 30, 'style' => 'width:55px']);
        echo $this->JqueryValidation->input('fin', ['label' => __('Hora Fin'), 'timeFormat' => '24', 'interval' => 30, 'style' => 'width:55px']);
        echo $this->JqueryValidation->input('habilitado', ['label' => __('Habilitado'), 'checked' => 'checked']);
        echo "</div>";
        ?>
    </fieldset>
    <?php echo $this->Form->end(['id' => 'guardar', 'label' => __('Guardar')]); ?>
</div>
<script>
    $(document).ready(function () {
        $("#AmenitiesturnoDiasemana").select2({language: "es", width: 100});
    });
    var url = "<?= $this->webroot ?>Amenitiesturnos/view/";
    url += "<?= $amenity['Amenity']['id'] ?>";<?php /* lo dejo en 2 lineas porq sino la indentación queda para la mierda (WTF) */ ?>
    $("#AmenitiesturnoViewForm").on("submit", function (e) {
        e.preventDefault();
        $("#guardar").prop('disabled', true);
        $.ajax({
            type: "POST",
            url: url,
            data: $("#AmenitiesturnoViewForm").serialize()
        }).done(function (msg) {
            try {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                    $("#guardar").prop('disabled', false);
                } else {
                    dialogLoad("#cal", url);
                }
            } catch (err) {
                //
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
            } else {
                alert("No se pudo realizar la accion, intente nuevamente");
            }
        });
    });
    function del(ruta, id) {
        if (confirm("Desea eliminar el turno seleccionado?")) {
            $.ajax({
                type: "POST",
                url: "<?= $this->webroot ?>" + ruta + "/" + id
            }).done(function (msg) {
                dialogLoad("#cal", url);
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la accion, intente nuevamente");
                }
            });
        }
    }
</script>
<style>
    .editableform .form-control {
        width: 100px !important;
    }
</style>