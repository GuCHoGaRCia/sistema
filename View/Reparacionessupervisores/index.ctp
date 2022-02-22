<div class="reparacionessupervisores index">
    <h2><?php echo __('Supervisores'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Reparacionessupervisore']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('nombre', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('direccion', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('telefono', __('Teléfono')); ?></th>
                <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                <th><?php echo $this->Paginator->sort('ultimoacceso', __('Último acceso')); ?></th>
                <th class='center'><?php echo $this->Paginator->sort('habilitado', __('Habilitado')); ?></th>
                <th class="acciones" style="width:70px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($reparacionessupervisores as $reparacionessupervisore):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="nombre" data-value="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['nombre']) ?>" data-pk="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['id']) ?>"><?php echo h($reparacionessupervisore['Reparacionessupervisore']['nombre']) ?></span>&nbsp;</td>
                    <td><span class="direccion" data-value="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['direccion']) ?>" data-pk="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['id']) ?>"><?php echo h($reparacionessupervisore['Reparacionessupervisore']['direccion']) ?></span>&nbsp;</td>
                    <td><span class="telefono" data-value="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['telefono']) ?>" data-pk="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['id']) ?>"><?php echo h($reparacionessupervisore['Reparacionessupervisore']['telefono']) ?></span>&nbsp;</td>
                    <td><span class="email" data-value="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['email']) ?>" data-pk="<?php echo h($reparacionessupervisore['Reparacionessupervisore']['id']) ?>"><?php echo h($reparacionessupervisore['Reparacionessupervisore']['email']) ?></span>&nbsp;</td>
                    <td><?= $reparacionessupervisore['Reparacionessupervisore']['ultimoacceso'] !== '0000-00-00 00:00:00' ? $this->Time->timeAgoInWords($reparacionessupervisore['Reparacionessupervisore']['ultimoacceso']) : '--' ?>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->image(h($reparacionessupervisore['Reparacionessupervisore']['habilitado'] ? '1' : '0') . '.png', ['onclick' => 'd(' . $reparacionessupervisore['Reparacionessupervisore']['id'] . ')', 'id' => 'tilde_' . $reparacionessupervisore['Reparacionessupervisore']['id'], 'style' => 'cursor:pointer', 'title' => __('Habilitar / Deshabilitar')]) ?></td>
                    <td class="acciones" style="width:70px">
                        <?php
                        if (!empty($reparacionessupervisore['Reparacionessupervisore']['email'])) {
                            echo $this->Html->link($this->Html->image('link.png', ['title' => __('Abrir panel Supervisor')]), array('action' => 'link', $reparacionessupervisore['Reparacionessupervisore']['id']), ['escapeTitle' => false, 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer'], __('Se abrira una ventana nueva con el panel de control del Supervisor'));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="7"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.nombre').editable({type: 'text', name: 'nombre', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Reparacionessupervisores/editar', placement: 'right'});
        $('.direccion').editable({type: 'text', name: 'direccion', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Reparacionessupervisores/editar', placement: 'right'});
        $('.telefono').editable({type: 'text', name: 'telefono', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Reparacionessupervisores/editar', placement: 'right'});
        $('.email').editable({type: 'text', name: 'email', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Reparacionessupervisores/editar', placement: 'right'});
        $('.habilitado').editable({type: 'text', name: 'habilitado', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Reparacionessupervisores/editar', placement: 'right'});
    });
</script>
<script>
    function d(id) {
        if ($("#tilde_" + id).prop("src").indexOf('0.png') === -1) {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>Reparacionessupervisores/deshabilitar", cache: false, data: {id: id}})
                    .done(function (msg) {
                        if (JSON.parse(msg) === 1) {
                            $("#tilde_" + id).attr("src", "<?= $this->webroot ?>img/0.png");
                        } else {
                            alert(JSON.parse(msg));
                        }
                    }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo agregar el cheque. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo agregar el cheque de terceros");
                }
            });
        } else {
            $.get("<?= $this->webroot ?>Reparacionessupervisores/invertir/habilitado/" + id + "?" + new Date().getTime());
            $("#tilde_" + id).attr("src", "<?= $this->webroot ?>img/1.png");
        }
    }
</script>