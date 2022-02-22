<?php
$url = $this->webroot;
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="author" content="CEONLINE" />
        <meta name="description" content="CEONLINE" />
        <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']) ?>
        <meta name="keywords" content="expensas, administracion de consorcios, procesamiento de expensas, facturas, web, sistemas, internet, expensas por internet, expensas on line, inmobiliaria, administracion"/>
        <title><?php echo h("CEONLINE - Panel de control"); ?></title>
        <?= $this->Html->script('ckeditor/ckeditor') ?>
    </head>
    <body>
        <style>
            #pri li ul li{
                display:inline;
                padding-right:20px;
            }
            #cont{
                width:auto;min-width:350px;max-width:590px;border:2px solid gray;padding:5px;
                border-radius:21px;
                -moz-border-radius:4px;
                -webkit-border-radius:4px;
                -moz-box-shadow:5px 5px 8px #CCC;
                -webkit-box-shadow:5px 5px 8px #CCC;
                box-shadow:5px 5px 8px #CCC;
            }
            .iom{
                cursor:pointer;
                font-size:13px;
                margin-right:15px;
            }
            .iom:hover{
                text-decoration:underline;
            }
            .ui-accordion-header{
                font-size:11px !important;
                height:25px;
            }
            .toolbar{
                margin-left:200px !important;
            }
        </style>
        <div id="fondogris">
            <h2><?php echo __('Mis Reparaciones'); ?></h2>
            <?php
            echo "<div style='margin-top:-10px;text-align:center'>";
            echo $this->Form->create('Reparacionessupervisore', ['class' => 'inline', 'id' => 'noimprimir']);
            echo $this->Form->input('estado', ['label' => false, 'empty' => '', 'options' => [0 => __('Todas')] + $estados, 'type' => 'select', 'selected' => $estado ?? 0]);
            echo $this->Form->end(['label' => __('Ver'), 'style' => 'width:50px']);
            echo "</div>";
            ?>
            <table cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="esq_i"></td>
                        <th><?php echo __('Consorcio') ?></th>
                        <th><?php echo __('Propietario') ?></th>
                        <th><?php echo __('Fecha') ?></th>
                        <th><?php echo __('Concepto') ?></th>
                        <th><?php echo __('Estado') ?></th>
                        <th class="acciones" style="width:100px"><?php echo __('Acciones') ?></th>
                        <td class="esq_d"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($reparaciones as $reparacione):
                        $vista = strtotime(date("Y-m-d", strtotime($reparacione['Reparacione']['created']))) <= strtotime('2018-04-12') ? 'view2' : 'view';
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr<?php echo $class; ?>>
                            <td class="borde_tabla"></td>
                            <td><?php echo h($reparacione['Consorcio']['name']) ?></td>
                            <td><?php echo empty($reparacione['Propietario']['name']) ? '--' : h($reparacione['Propietario']['name'] . " - " . $reparacione['Propietario']['unidad'] . " (" . $reparacione['Propietario']['code'] . ")") ?></td>
                            <td><span class="fecha" data-value="<?php echo h($vista == 'view' ? $reparacione['Reparacione']['fecha'] : $reparacione['Reparacione']['recordatorio']) ?>" data-pk="<?php echo h($reparacione['Reparacione']['id']) ?>"><?php echo h($this->Time->format(__('d/m/Y'), $reparacione['Reparacione']['fecha'])) ?></span>&nbsp;</td>
                            <td><span class="concepto" data-value="<?php echo h($reparacione['Reparacione']['concepto']) ?>" data-pk="<?php echo h($reparacione['Reparacione']['id']) ?>"><?php echo h($reparacione['Reparacione']['concepto']) ?></span>&nbsp;</td>
                            <?php
                            switch ($reparacione['Reparacionesestado']['nombre']) {
                                case "Finalizada":
                                    echo "<td style='color:green'>";
                                    break;
                                case "Pendiente":
                                    echo "<td style='color:red'>";
                                    break;
                                case "Suspendida":
                                    echo "<td style='color:red;text-decoration:line-through'>";
                                    break;
                                default:
                                    echo "<td style='color:orange'>";
                                    break;
                            }
                            ?>
                    <b><?php echo h($reparacione['Reparacionesestado']['nombre']); ?></b></td>
                    <td class="acciones" style="width:auto">
                        <?php
                        echo $this->Html->image('view.png', ['alt' => __('Ver'), 'title' => __('Ver'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $url . "img/loading.gif\"/></div>');$('#rc').load('" . $url . "Reparacionessupervisores/view2/$link/" . $reparacione['Reparacione']['id'] . "');"]);
                        echo $this->Html->image('edit.png', ['title' => __('Editar última actualización'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparacionesactualizaciones/edit/$link/" . $reparacione['Reparacione']['id'] . "');"]);
                        echo $this->Html->image('new.png', ['title' => __('Agregar'), 'style' => 'cursor:pointer', 'onclick' => "$('#rc').dialog('open');$('#rc').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\"" . $this->webroot . "img/loading.gif\"/></div>');$('#rc').load('" . $this->webroot . "Reparacionesactualizaciones/agregar/$link/" . $reparacione['Reparacione']['id'] . "');"]);
                        ?>
                    </td>
                    <td class="borde_tabla"></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="6"></td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
            <?php
            /* $rep = "";
              foreach ($reparaciones as $k => $v) {
              $rep .= "<h3 style='text-align:left;font-size:11px'>" . $this->Time->format(__('d/m/Y H:i:s'), $v['Reparacione']['created']) . " - " . $v['Consorcio']['name'] . " - " . (!empty($v['Propietario']['unidad']) ? $v['Propietario']['unidad'] . " - " : '') . $v['Reparacione']['concepto'] . " (" . $v['Reparacionesestado']['nombre'] . ")</h3>";
              //$rep .= "<div>" . $v['Reparacione']['observaciones'] . "<p style='color:green;font-style:italic;'>&Uacute;ltima modificaci&oacute;n: " . $this->Time->format(__('d/m/Y H:i:s'), $v['Reparacione']['modified']) . "</p></div>";
              }
              echo $rep;
              debug($reparaciones); */
            ?>
        </div>
        <script>
            $(function () {
                $(".accordion").accordion({
                    collapsible: true,
                    heightStyle: "content",
                    active: false
                });
                var dialog = $("#rc").dialog({
                    autoOpen: false, height: "600", width: "100%", maxWidth: "100%",
                    position: {at: "center top"},
                    closeOnEscape: true,
                    modal: true,
                });
                $("#ReparacionessupervisoreEstado").select2({language: "es", placeholder: "<?= __("Seleccione estado...") ?>", width: "200"});
            });
        </script>
        <?php
        echo "<div id='rc' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; // es el div para el RC  
        ?>
    </body>
</html>