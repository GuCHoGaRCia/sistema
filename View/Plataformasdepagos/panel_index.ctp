<div class="plataformasdepagos index">
    <h2><?php echo __('Plataformas de Pagos'); ?></h2>
    <?php echo $this->element('toolbar', ['pagecount' => true, 'pagesearch' => false, 'pagenew' => true, 'model' => 'Plataformasdepago']); ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="esq_i"></td>
                <th><?php echo $this->Paginator->sort('titulo', __('Titulo')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitada', __('Habilitada')); ?></th>
                <th class="acciones" style="width:100px"><?php echo __('Acciones'); ?></th>
                <td class="esq_d"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($plataformasdepagos as $plataformasdepago):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td class="borde_tabla"></td>
                    <td><span class="titulo" data-value="<?php echo h($plataformasdepago['Plataformasdepago']['titulo']) ?>" data-pk="<?php echo h($plataformasdepago['Plataformasdepago']['id']) ?>"><?php echo h($plataformasdepago['Plataformasdepago']['titulo']) ?></span>&nbsp;</td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($plataformasdepago['Plataformasdepago']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Habilitar / Deshabilitar'))), array('controller' => 'Plataformasdepagos', 'action' => 'invertir', 'habilitada', h($plataformasdepago['Plataformasdepago']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:130px">
                        <span class="contenedorreportes">
                            <?php
                            //echo $this->Html->link($this->Html->image(h($plataformasdepago['Plataformasdepago']['habilitada'] ? '1' : '0') . '.png', array('title' => __('Configurar Plataforma'))), array('controller' => 'Plataformasdepagosconfigs', 'action' => 'edit', h($plataformasdepago['Plataformasdepagosconfig']['plataforma'])), array('class' => 'status', 'escape' => false));
                            ?>
                            <!--span class="listareportes" style="width:400px;margin-left:-425px;font-weight:bold">
                                <ul>
                                    <li>
                                        Dato Interno: <span class="datointerno" data-value="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['datointerno']) ?>" data-pk="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['id']) ?>"><?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['datointerno']) ?></span>
                                    </li>
                                    <li>
                                        M&iacute;nimo: <span class="minimo" data-value="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['minimo']) ?>" data-pk="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['id']) ?>"><?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['minimo']) ?></span>
                                    </li>
                                    <li>
                                        Comisi&oacute;n: <span class="comision" data-value="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['comision']) ?>" data-pk="<?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['id']) ?>"><?php echo h($ppc[$plataformasdepago['Plataformasdepago']['id']]['comision']) ?></span>
                                    </li>
                                </ul>
                            </span-->
                        </span>  

                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
        <script>
            $.fn.editable.defaults.mode = 'inline';
            $(document).ready(function () {
                $('.titulo').editable({type: 'text', name: 'titulo', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>plataformasdepagos/editar', placement: 'right'});
                $('.habilitada').editable({type: 'text', name: 'habilitada', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>plataformasdepagos/editar', placement: 'right'});
                $('.datointerno').editable({type: 'text', name: 'datointerno',
                    validate: function (value) {
                        if ($.trim(value).length != 6) {
                            return 'Deben ser 6 caracteres';
                        }
                    }, success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>plataformasdepagosconfigs/editar', placement: 'right'});
                $('.minimo').editable({type: 'number', step: 0.01, name: 'minimo', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>plataformasdepagosconfigs/editar', placement: 'right'});
                $('.comision').editable({type: 'number', step: 0.01, name: 'comision', success: function (n) {
                        if (n) {
                            return n
                        }
                    }, url: '<?php echo $this->webroot; ?>plataformasdepagosconfigs/editar', placement: 'right'});
            });
        </script>
        <tr class="altrow">
            <td class="bottom_i"></td>
            <td colspan="3"></td>
            <td class="bottom_d"></td>
        </tr>
    </table>
    <?php echo $this->element('pagination'); ?>
</div>
<style>
    .editableform .form-control{
        width:150px !important;
        line-height:12px;
    }
</style>