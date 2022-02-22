<div class="cartas index">
    <h2><?php echo __('Cartas'); ?></h2>
    <?php
    echo "<div class='inline' style='margin:-5px 0 0 0'>";
    echo $this->Form->create('Carta', ['class' => 'inline', 'id' => 'noimprimir']);
    //echo $this->Form->input('consorcio', ['label' => false, 'empty' => '', 'options' => $consorcios, 'type' => 'select', 'selected' => isset($c) ? $c : 0]);
    echo $this->Form->input('desde', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Desde'), 'value' => !empty($d) ? $d : '']);
    echo $this->Form->input('hasta', ['label' => '', 'class' => 'dp', 'autocomplete' => 'off', 'style' => 'width:85px', 'placeholder' => __('Hasta'), 'value' => !empty($h) ? $h : '']);
    echo $this->Form->input('buscar', ['label' => '', 'style' => 'width:110px', 'placeholder' => __('Buscar'), 'value' => !empty($b) ? $b : '']);
    echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px;']);
    echo "<div style='position:absolute;top:110px;right:250px'>" . $this->element('toolbar', ['pagecount' => false, 'pagesearch' => true, 'pagenew' => true, 'print' => false, 'multidelete' => false, 'model' => 'Carta']) . "</div>";
    echo "</div>";
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('propietario_id', __('Propietario')); ?></th>
                <th><?php echo $this->Paginator->sort('cartastipo_id', __('Tipo')); ?></th>
                <th><?php echo $this->Paginator->sort('codigo', __('Código')); ?></th>
                <th><?php echo $this->Paginator->sort('oblea', __('Oblea')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Cargada')); ?></th>
                <th class="acciones" style="width:auto"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($cartas as $carta):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($carta['Client']['name'] . " - " . $carta['Consorcio']['name']) ?></td>
                    <td><?php echo h($carta['Propietario']['name']) ?></td>
                    <td><?php echo h($carta['Cartastipo']['abreviacion']) ?></td>
                    <td><span class="codigo"><?php echo h($carta['Carta']['codigo']) ?></span>&nbsp;</td>
                    <td><span class="oblea"><?php echo h($carta['Carta']['oblea']) ?></span>&nbsp;</td>
                    <td><?php echo $this->Time->format(__('d/m/Y H:i:s'), $carta['Carta']['created']) ?>&nbsp;</td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $carta['Carta']['id']), array('escapeTitle' => false), __('Desea eliminar el dato # %s?', h($carta['Carta']['oblea'])));
                        if (!$carta['Carta']['robada']) {
                            echo $this->Form->postLink($this->Html->image('police.png', array('alt' => __('Informar Robo'), 'title' => __('Informar Robo'))), array('action' => 'informarRobo', $carta['Carta']['id']), array('escapeTitle' => false), __('Desea informar el Robo de la carta # %s?', h($carta['Carta']['oblea'])));
                        } else {
                            echo "<span style='color:red;font-weight:bold'>[ROBADA]</span>";
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
        $(".dp").datepicker({changeYear: true, yearRange: '2016:+1'});
    });
</script>