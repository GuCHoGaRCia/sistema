<div class="consorcios index">
    <h2><?php echo __('Consorcios'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => false, 'model' => 'Consorcio')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('code', __('Código')); ?></th>
                <th><?php echo $this->Paginator->sort('name', __('Nombre')); ?></th>
                <th><?php echo $this->Paginator->sort('cuit', __('CUIT')); ?></th>
                <th><?php echo $this->Paginator->sort('address', __('Dirección')); ?></th>
                <th><?php echo $this->Paginator->sort('city', __('Ciudad')); ?></th>
                <th><?php echo $this->Paginator->sort('interes', __('Interés')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprime_cod_barras', __('Imprime código de barras')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprime_cpe', __('Imprime CPE')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('prorrateagastosgenerales', __('Prorratea Gastos Generales')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('2_cuotas', __('Dos cuotas')); ?></th>
                <th class="acciones" style="width:140px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($consorcios as $consorcio):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="code" data-value="<?php echo h($consorcio['Consorcio']['code']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['code']) ?></span>&nbsp;</td>
                    <td><span class="name" data-value="<?php echo h($consorcio['Consorcio']['name']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['name']) ?></span>&nbsp;</td>
                    <td><span class="cuit" data-value="<?php echo h($consorcio['Consorcio']['cuit']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['cuit']) ?></span>&nbsp;</td>
                    <td><span class="address" data-value="<?php echo h($consorcio['Consorcio']['address']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['address']) ?></span>&nbsp;</td>
                    <td><span class="city" data-value="<?php echo h($consorcio['Consorcio']['city']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['city']) ?></span>&nbsp;</td>
                    <td><span class="interes" data-value="<?php echo h($consorcio['Consorcio']['interes']) ?>" data-pk="<?php echo h($consorcio['Consorcio']['id']) ?>"><?php echo h($consorcio['Consorcio']['interes']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['imprime_cod_barras'] ? '1' : '0') . '.png', array('title' => __('Imprime / No imprime'))), array('controller' => 'Consorcios', 'action' => 'invertir', 'imprime_cod_barras', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['imprime_cpe'] ? '1' : '0') . '.png', array('title' => __('Imprime CPE'))), array('controller' => 'Consorcios', 'action' => 'invertir', 'imprime_cpe', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['prorrateagastosgenerales'] ? '1' : '0') . '.png', array('title' => __('Prorratea Gastos Generales'))), array('controller' => 'Consorcios', 'action' => 'invertir', 'prorrateagastosgenerales', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['2_cuotas'] ? '1' : '0') . '.png', array('title' => __('Usa dos cuotas / No usa dos cuotas'))), array('controller' => 'Consorcios', 'action' => 'invertir', '2_cuotas', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:80px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/edconsorcio/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Estado disponibilidad</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Consorcios/view/<?= $consorcio['Consorcio']['id'] ?>">Listado Propietarios</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Consorcios/cartadeudores/<?= $consorcio['Consorcio']['id'] ?>">Carta deudores</a>
                                    </li>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        if (in_array($_SESSION['Auth']['User']['username'], ['rcasco', 'mmazzei', 'ecano'])) {
                            echo $this->Form->postLink($this->Html->image('delete.png', array('alt' => __('Eliminar'), 'title' => __('Eliminar'))), array('action' => 'delete', $consorcio['Consorcio']['id']), array('escapeTitle' => false), __('Eliminar el dato # %s?', $consorcio['Consorcio']['name']));
                        }
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="11"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<script>
    $(document).ready(function () {
        $('.code').editable({type: 'text', name: 'code', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.name').editable({type: 'text', name: 'name', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.cuit').editable({type: 'text', name: 'cuit', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.address').editable({type: 'text', name: 'address', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.city').editable({type: 'text', name: 'city', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.interes').editable({type: 'text', name: 'interes', success: function (n) {
                if (n) {
                    return n
                }
            }, url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
    });
</script>