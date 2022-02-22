<div class="consorcios index">
    <h2><?php echo __('Consorcios') . " <a href='" . $this->webroot . "Consorcios/listar' target='_blank' rel='nofollow noopener noreferrer'>Listar</a>" ?></h2>
    <?php echo $this->element('toolbar', array('pagecount' => true, 'pagesearch' => true, 'pagenew' => true, 'model' => 'Consorcio')); ?>
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
                <th class="center"><?php echo $this->Paginator->sort('imprime_cod_barras', __('ICB')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprime_cpe', __('ICPE')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('prorrateagastosgenerales', __('PGG')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('imprimeimportebanco', __('IIDB')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('2_cuotas', __('Dos cuotas')); ?></th>
                <th class="center"><?php echo $this->Paginator->sort('habilitado', __('Habilitado')); ?></th>
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
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['imprimeimportebanco'] ? '1' : '0') . '.png', array('title' => __('Imprime Importe Depósito Bancario Resumen Cuenta'))), array('controller' => 'Consorcios', 'action' => 'invertir', 'imprimeimportebanco', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['2_cuotas'] ? '1' : '0') . '.png', array('title' => __('Usa dos cuotas / No usa dos cuotas'))), array('controller' => 'Consorcios', 'action' => 'invertir', '2_cuotas', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="center"><?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['habilitado'] ? '1' : '0') . '.png', array('title' => __('Habilitado / Deshabilitado'))), array('controller' => 'Consorcios', 'action' => 'invertir', 'habilitado', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)); ?></td>
                    <td class="acciones" style="width:120px">
                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('config.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:350px;margin-left:-380px;font-weight:bold">
                                <ul>
                                    <?php
                                    if ($consorcio['Client']['cargagpdecartas']) {
                                        ?>
                                        <li>
                                            <?php
                                            echo $this->Html->image('1.png') . __('Cuenta GP Cartas') . ':';
                                            if (!empty($gp[$consorcio['Consorcio']['id']])) {
                                                ?>
                                                <span id="cgp<?= $consorcio['Consorcio']['id'] ?>" data-value="<?php echo h($consorcio['Consorcio']['cuentagastosparticularesdefecto']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>">
                                                    <?php echo isset($gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagastosparticularesdefecto']]) ? $gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagastosparticularesdefecto']] : '' ?>
                                                </span>
                                                <script>
                                                    $(function () {
                                                        $('#cgp<?= $consorcio['Consorcio']['id'] ?>').editable({
                                                            mode: 'inline',
                                                            showbuttons: false,
                                                            value: 'CGP', name: 'cuentagastosparticularesdefecto', type: 'select', url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left', success: function (n, r) {
                                                                if (n) {
                                                                    return n
                                                                }
                                                            },
                                                            source: [<?php
										echo "{value: null, text: '- Sin seleccionar -'},";
                                        foreach ($gp[$consorcio['Consorcio']['id']] as $j => $l) {
                                            echo "{value: $j, text: '" . h($l) . "'},";
                                        }
                                        ?>]
                                                        });
                                                    });
                                                </script>                                        
                                                <?php
                                            } else {
                                                //echo "No existen cuentas de GP en el Consorcio asociado";
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li>
                                        <?php
                                        echo $this->Html->image('1.png', ['title' => 'Establece la Cuenta de Gastos Particulares en la cual crear el Gasto particular con el importe de Comisión cobrado al Propietario en la última expensa']) . __('Cuenta Comisión Plataforma') . ':';
                                        if (!empty($gp[$consorcio['Consorcio']['id']])) {
                                            ?>
                                            <span id="cgpcp<?= $consorcio['Consorcio']['id'] ?>" data-value="<?php echo h($consorcio['Consorcio']['cuentagpcomisionplataforma']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>">
                                                <?php echo isset($gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpcomisionplataforma']]) ? $gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpcomisionplataforma']] : '' ?>
                                            </span>
                                            <script>
                                                $(function () {
                                                    $('#cgpcp<?= $consorcio['Consorcio']['id'] ?>').editable({
                                                        mode: 'inline',
                                                        showbuttons: false,
                                                        value: 'cgpcp', name: 'cuentagpcomisionplataforma', type: 'select', url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left', success: function (n, r) {
                                                            if (n) {
                                                                return n
                                                            }
                                                        },
                                                        source: [<?php
										echo "{value: null, text: '- Sin seleccionar -'},";
                                        foreach ($gp[$consorcio['Consorcio']['id']] as $j => $l) {
                                            echo "{value: $j, text: '" . h($l) . "'},";
                                        }
                                        ?>]
                                                    });
                                                });
                                            </script>                                        
                                            <?php
                                        } else {
                                            echo "No existen cuentas de GP en el Consorcio asociado";
                                        }
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo $this->Html->image('1.png', ['title' => 'Establece la Cuenta de Gastos Particulares en la cual crear el Gasto particular con el importe del pago fuera de término']) . __('Cuenta GP PFT') . ':';
                                        if (!empty($gp[$consorcio['Consorcio']['id']])) {
                                            ?>
                                            <span id="cgppft<?= $consorcio['Consorcio']['id'] ?>" data-value="<?php echo h($consorcio['Consorcio']['cuentagppft']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>">
                                                <?php echo isset($gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagppft']]) ? $gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagppft']] : '' ?>
                                            </span>
                                            <script>
                                                $(function () {
                                                    $('#cgppft<?= $consorcio['Consorcio']['id'] ?>').editable({
                                                        mode: 'inline',
                                                        showbuttons: false,
                                                        value: 'CGPPFT', name: 'cuentagppft', type: 'select', url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left', success: function (n, r) {
                                                            if (n) {
                                                                return n
                                                            }
                                                        },
                                                        source: [<?php
										echo "{value: null, text: '- Sin seleccionar -'},";
                                        foreach ($gp[$consorcio['Consorcio']['id']] as $j => $l) {
                                            echo "{value: $j, text: '" . h($l) . "'},";
                                        }
                                        ?>]
                                                    });
                                                });
                                            </script>                                        
                                            <?php
                                        } else {
                                            echo "No existen cuentas de GP en el Consorcio asociado";
                                        }
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo $this->Html->image('1.png', ['title' => 'Establece la Cuenta de Gastos Particulares en la cual crear el Gasto particular con el importe de la multa']) . __('Cuenta GP Multa') . ':';
                                        if (!empty($gp[$consorcio['Consorcio']['id']])) {
                                            ?>
                                            <span id="cgpmulta<?= $consorcio['Consorcio']['id'] ?>" data-value="<?php echo h($consorcio['Consorcio']['cuentagpmulta']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>">
                                                <?php echo isset($gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpmulta']]) ? $gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpmulta']] : '' ?>
                                            </span>
                                            <?= __("Intéres: ") ?><span class="interesmulta" data-value="<?php echo h($consorcio['Consorcio']['interesmulta']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['interesmulta']) ?></span>
                                            <script>
                                                $(function () {
                                                    $('#cgpmulta<?= $consorcio['Consorcio']['id'] ?>').editable({
                                                        mode: 'inline',
                                                        showbuttons: false,
                                                        value: 'CGPMULTA', name: 'cuentagpmulta', type: 'select', url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left', success: function (n, r) {
                                                            if (n) {
                                                                return n
                                                            }
                                                        },
                                                        source: [<?php
										echo "{value: null, text: '- Sin seleccionar -'},";
                                    foreach ($gp[$consorcio['Consorcio']['id']] as $j => $l) {
                                        echo "{value: $j, text: '" . h($l) . "'},";
                                    }
                                    ?>]
                                                    });
                                                });
                                            </script>                                        
                                            <?php
                                        } else {
                                            echo "No existen cuentas de GP en el Consorcio asociado";
                                        }
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                        echo $this->Html->image('1.png', ['title' => 'Establece la Cuenta de Gastos Particulares en la cual crear el Gasto particular con el importe de la multa más de un período']) . __('Cuenta GP Multa más de un período') . ':';
                                        if (!empty($gp[$consorcio['Consorcio']['id']])) {
                                            ?>
                                            <span id="cgpmultacapital<?= $consorcio['Consorcio']['id'] ?>" data-value="<?php echo h($consorcio['Consorcio']['cuentagpmultacapital']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>">
                                                <?php echo isset($gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpmultacapital']]) ? $gp[$consorcio['Consorcio']['id']][$consorcio['Consorcio']['cuentagpmultacapital']] : '' ?>
                                            </span>
                                            <?= __("Intéres: ") ?><span class="interesmultacapital" data-value="<?php echo h($consorcio['Consorcio']['interesmultacapital']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['interesmultacapital']) ?></span>
                                            <script>
                                                $(function () {
                                                    $('#cgpmultacapital<?= $consorcio['Consorcio']['id'] ?>').editable({
                                                        mode: 'inline',
                                                        showbuttons: false,
                                                        value: 'CGPMULTACAPITAL', name: 'cuentagpmultacapital', type: 'select', url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left', success: function (n, r) {
                                                            if (n) {
                                                                return n
                                                            }
                                                        },
                                                        source: [<?php
										echo "{value: null, text: '- Sin seleccionar -'},";
                                    foreach ($gp[$consorcio['Consorcio']['id']] as $j => $l) {
                                        echo "{value: $j, text: '" . h($l) . "'},";
                                    }
                                    ?>]
                                                    });
                                                });
                                            </script>                                        
                                            <?php
                                        } else {
                                            echo "No existen cuentas de GP en el Consorcio asociado";
                                        }
                                        ?>
                                    </li>
                                    <li>
                                        <?php echo $this->Html->link($this->Html->image(h($consorcio['Consorcio']['imprime_talon_banco'] ? '1' : '0') . '.png', array('title' => __('Imprimir talón banco'))), array('controller' => 'consorcios', 'action' => 'invertir', 'imprime_talon_banco', h($consorcio['Consorcio']['id'])), array('class' => 'status', 'escape' => false)) . __('ITB Pfjo: '); ?>
                                        <span class="talonbancoprefijo" data-value="<?php echo h($consorcio['Consorcio']['talonbancoprefijo']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['talonbancoprefijo']) ?></span>
                                        <?= __("Cod: ") ?><span class="talonbancocodigo" data-value="<?php echo h($consorcio['Consorcio']['talonbancocodigo']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['talonbancocodigo']) ?></span>
                                        <?= __("Com: ") ?><span class="talonbancocomision" data-value="<?php echo h($consorcio['Consorcio']['talonbancocomision']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['talonbancocomision']) ?></span>
                                        <?= __("Min: ") ?><span class="talonbancominimo" data-value="<?php echo h($consorcio['Consorcio']['talonbancominimo']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['talonbancominimo']) ?></span>
                                        <?= __("Nom: ") ?><span class="talonbanconombre" data-value="<?php echo h($consorcio['Consorcio']['talonbanconombre']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['talonbanconombre']) ?></span>
                                    </li>                               
                                    <li>
                                        <?php echo $this->Html->image('1.png', ['title' => 'Establece el valor a partir del cual se muestran los propietarios en el reporte de deudores en liquidaciones']) . __('Valor "desde" listado deudores') . ':'; ?>
                                        <span class="valordesdereportepropdeudor" data-value="<?php echo h($consorcio['Consorcio']['valordesdereportepropdeudor']) ?>" data-pk="<?php echo $consorcio['Consorcio']['id'] ?>"><?php echo h($consorcio['Consorcio']['valordesdereportepropdeudor']) ?></span>
                                    </li>                                                                              
                                </ul>
                            </span>
                        </span>

                        <span class="contenedorreportes">
                            <?php
                            echo $this->Html->image('report.png', array('alt' => __('Reportes'), 'title' => __('Reportes'), 'id' => 'reportesimg'), [], ['escapeTitle' => false]);
                            ?>
                            <span class="listareportes" style="width:220px">
                                <ul>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/propietariosdatos/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Listado Propietarios</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/coeficientespropietarios/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Coeficientes Propietarios</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Consorcios/cartadeudores/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Carta deudores</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Cartadeudores/index/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Carta deudores hist&oacute;rico</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Consorcios/recordatoriopago/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Recordatorio pago</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Consorcios/etiquetas/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Etiquetas</a>
                                        <!--a href="<?php echo $this->webroot; ?>Consorcios/etiquetas/<?= $consorcio['Consorcio']['id'] ?>/1" target="_blank" rel="nofollow noopener noreferrer" title='Imprime etiquetas de Propietarios que Imprimen el Resumen de Cuenta'>ConRC</a-->
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/planillafirmas/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla de Firmas</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->webroot; ?>Reports/planillaparticulares/<?= $consorcio['Consorcio']['id'] ?>" target="_blank" rel="nofollow noopener noreferrer">Planilla Particulares</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick='javascript:$("#cid").val("<?= $consorcio['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq(2);$("#rliqs").prop("action", "<?php echo $this->webroot; ?>Reports/analiticogastos")'>Anal&iacute;tico de Gastos</a>
                                    </li>
                                    <li>          
                                        <a href="#" onclick='javascript:$("#edc").attr("value", "1");$("#cid").val("<?= $consorcio['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq(3);$("#rliqs").prop("action", "<?php echo $this->webroot; ?>Reports/edconsorcio")'>Estado Disponibilidad General</a>
                                    </li>
                                    <li>
                                        <a onclick='javascript:$("#cid2").val("<?= $consorcio['Consorcio']['id'] ?>");$("#rg3369").dialog("open");' href="#">RG 3369 AFIP</a>
                                    </li>
                                    <?php /*
                                      <li>
                                      <a href="#" onclick='javascript:$("#cid").val("<?= $consorcio['Consorcio']['id'] ?>");$("#rangoliq").dialog("open");getLiq()'>Anal&iacute;tico de Cobranzas</a>
                                      </li>
                                     */ ?>
                                </ul>
                            </span>
                        </span> 
                        <?php
                        echo $this->Html->image('edit.png', array('alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('action' => 'edit', $consorcio['Consorcio']['id'])));
                        ?>

                    </td>
                    <td class="borde_tabla"></td>
                </tr>
            <?php endforeach; ?>
            <tr class="altrow">
                <td class="bottom_i"></td>
                <td colspan="13"></td>
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
        $('.interesmulta').editable({type: 'text', name: 'interesmulta', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.interesmultacapital').editable({type: 'text', name: 'interesmultacapital', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'right'});
        $('.talonbancocodigo').editable({type: 'text', name: 'talonbancocodigo', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $('.talonbancoprefijo').editable({type: 'text', name: 'talonbancoprefijo', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $('.talonbancocomision').editable({type: 'text', name: 'talonbancocomision', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $('.talonbanconombre').editable({type: 'text', name: 'talonbanconombre', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $('.talonbancominimo').editable({type: 'text', name: 'talonbancominimo', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $('.valordesdereportepropdeudor').editable({type: 'text', name: 'valordesdereportepropdeudor', success: function (n) {
                if (n) {
                    return n
                }
            },
            url: '<?php echo $this->webroot; ?>Consorcios/editar', placement: 'left'});
        $("#liquidaciones").select2({language: "es", allowClear: true, width: 350});
        var dialogp = $("#rg3369").dialog({
            autoOpen: false,
            height: "350",
            width: "450",
            maxWidth: "450px",
            modal: true,
            title: 'Seleccionar datos',
            position: {my: "center", at: "center", of: window},
            buttons: {
                "Ver reporte": function () {
                    var l1 = parseFloat($("#l1").val());
                    if (l1 === 0) {
                        alert('<?= __('Debe seleccionar Liquidación') ?>');
                        return false;
                    }
                    var superficie = parseFloat($("#superficie").val());
                    if (isNaN(superficie) || superficie <= 0) {
                        alert('<?= __('La superficie debe ser numérica y mayor a cero') ?>');
                        return false;
                    }
                    var monto = parseFloat($("#superficie").val());
                    if (isNaN(monto) || monto <= 0) {
                        alert('<?= __('El monto debe ser numérico y mayor a cero') ?>');
                        return false;
                    }
                    $("#rgafip").submit();
                },
                Cancelar: function () {
                    $("#rgafip")[0].reset();
                    dialogp.dialog("close");
                }
            },
            close: function () {
                if (typeof ($("#rgafip")[0]) !== "undefined") {
                    $("#rgafip")[0].reset();
                }
            },
            open: function () {
                $.ajax({type: "POST", url: "/sistema/Liquidations/getLiquidaciones", cache: false, data: {c: $("#cid2").val(), propid: 0, origenllamada: 3}}).done(function (msg) {
                    if (msg) {
                        var obj = JSON.parse(msg);
                        if (jQuery.isEmptyObject(obj)) {
                            alert("No existen liquidaciones");
                            $("#rgafip").dialog("close");
                        }
                        $("#liquidaciones option").remove();
                        $("#liquidaciones").append($("<option></option>").attr("value", 0).text("Seleccione Liquidación..."));
                        $.each(obj, function (j, val) {
                            if (typeof val['bloqueada'] !== 'undefined' && val['bloqueada'] === 0) {
                                $("#liquidaciones").append($("<option></option>").attr("value", val['liq_id']).text(val['periodo'] + ' ' + '(Liquidación Abierta!)'));
                            } else {
                                $("#liquidaciones").append($("<option></option>").attr("value", val['liq_id']).text(val['periodo']));
                            }
                        });
                    } else {
                        alert("No se pudieron obtener las liquidaciones");
                    }
                });
                event.preventDefault();
            }
        });
    });
</script>
<div id="rg3369" style="display:none">
    <div class="form">
        <?php echo $this->Form->create('Consorcio', ['class' => 'jquery-validation', 'id' => 'rgafip', 'target' => '_blank', 'url' => ['controller' => 'Consorcios', 'action' => 'rg3369afip']]); ?>
        <p class="error-message" style="font-size:11px">* Campos obligatorios</p>
        <?php
        echo $this->Form->input('consorcio', ['type' => 'hidden', 'id' => 'cid2']);
        echo $this->Form->input('liquidacion', ['id' => 'liquidaciones', 'type' => 'select', 'label' => __('Liquidación') . ' *', 'style' => 'width:300px']);
        echo $this->Form->input('superficie', ['id' => 'superficie', 'type' => 'number', 'label' => __('Superficie mínima') . ' *', 'value' => 100]);
        echo $this->Form->input('monto', ['id' => 'monto', 'type' => 'number', 'label' => __('Monto mínimo') . ' *', 'value' => 2000]);
        ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<?php
echo $this->element('rangoliquidaciones', ['url' => ['controller' => 'Consorcios', 'action' => 'index'], 'model' => 'Consorcio']);
