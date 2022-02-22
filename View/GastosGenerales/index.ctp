<div class="gastosGenerales index">
    <h2><?php echo __('Gastos Generales'); ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => false, 'pagesearch' => true, 'pagenew' => false, 'filter' => ['enabled' => true, 'options' => $consorcios, 'field' => 'consorcio'], 'model' => 'GastosGenerale')); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('consorcio_id', __('Consorcio')); ?></th>
                <th><?php echo $this->Paginator->sort('liquidation_id', __('Liquidación')); ?></th>
                <th><?php echo $this->Paginator->sort('rubro_id', __('Rubro')); ?></th>
                <th><?php echo $this->Paginator->sort('coeficiente_id', __('Coeficiente')); ?></th>
                <th><?php echo $this->Paginator->sort('user_id', __('Creado por')); ?></th>
                <th><?php echo $this->Paginator->sort('created', __('Fecha creación')); ?></th>
                <th><?php echo $this->Paginator->sort('description', __('Descripción')); ?></th>
                <th style="text-align:right"><?php echo $this->Paginator->sort('amount', __('Monto')); ?></th>
                <th class="acciones" style="width:40px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($gastosGenerales as $gastosGenerale):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><?php echo h($gastosGenerale['Consorcio']['name']); ?></td>
                    <td><?php echo h($gastosGenerale['Liquidation']['periodo']); ?></td>
                    <td><?php echo h($gastosGenerale['Rubro']['name']); ?></td>
                    <td><?php echo h($gastosGenerale['Coeficiente']['name']); ?></td>
                    <td><?php echo h($gastosGenerale['User']['name']); ?></td>
                    <td><?php echo $this->Time->format(__('d/m/Y H:i:s'), $gastosGenerale['GastosGenerale']['created']); ?></td>
                    <td>
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:310px;line-height:13px">
                                <?= $gastosGenerale['GastosGenerale']['description'] ?>
                            </span>
                        </span> 
                    </td>
                    <td style="text-align:right"><?php echo $this->Functions->money($gastosGenerale['GastosGeneraleDetalle']['amount']) ?>&nbsp;</td>
                    <td class="acciones" style="width:40px">
                        <?php
                        echo $this->Html->link($this->Html->image('view.png', ['title' => __('Ver'), 'alt' => __('Ver')]), ['action' => 'view', $gastosGenerale['GastosGenerale']['id']], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="9"></td>
                <td class="bottom_d"></td>
            </tr>
    </table>
    <?php echo $this->element('pagination'); ?></div>