<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="author" content="CEONLINE" />
        <meta name="description" content="CEONLINE" />
        <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']) ?>
        <meta name="keywords" content="expensas, administracion de consorcios, procesamiento de expensas, facturas, web, sistemas, internet, expensas por internet, expensas on line, inmobiliaria, administracion"/>
        <title><?php echo h("CEONLINE - Panel de control"); ?></title>
    </head>
    <body>
        <?php
//debug($this->request);
        if (empty($reparaciones)) {
            echo "<div class=''>La reparaci&oacute;n es inexistente</div>";
        } else {
            echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'style' => 'float:right;cursor:pointer', 'onclick' => 'window.print()']);
            foreach ($reparaciones as $rep) {
                ?>
                <div class='seccionaimprimir' style='width:100%'>
                    <h4 style="margin-top:0px;text-align:center">
                        <?php
                        echo h($this->Time->format(__('d/m/Y'), $rep['Reparacione']['fecha']) . " - " . $rep['Consorcio']['name']);
                        echo!empty($rep['Propietario']['name2']) ? h(" - " . $rep['Propietario']['name2']) : '';
                        $usuario = isset($users[$rep['Reparacione']['user_id']]) ? $users[$rep['Reparacione']['user_id']] : $reparacionessupervisores[$rep['Reparacione']['user_id']]; //si no existe el usuario, es un supervisor!
                        echo h(" - Creada por " . $users[$rep['Reparacione']['user_id']]);
                        ?>
                    </h4>
                    <h4 style='text-align:center'><?php echo h($rep['Reparacione']['concepto']) ?>
                        <?php
                        $estado = isset($rep['Reparacionesactualizacione']) && !empty($rep['Reparacionesactualizacione']) ? reset($rep['Reparacionesactualizacione'])['Reparacionesestado']['nombre'] : $rep['Reparacionesestado']['nombre'];
                        estado($estado); // muestro el estado
                        ?>
                    </h4>
                    <hr>
                    <?php
                    //debug($reparacionessupervisores);
                    if (isset($rep['Reparacionesactualizacione']) && !empty($rep['Reparacionesactualizacione'])) {
                        foreach ($rep['Reparacionesactualizacione'] as $k => $v) {
                            $usuario = isset($users[$v['user_id']]) ? $users[$v['user_id']] : $reparacionessupervisores[$v['user_id']]; //si no existe el usuario, es un supervisor!
                            echo h($this->Time->format(__('d/m/Y'), $v['fecha']) . " (" . $usuario . ") - " . $v['concepto']);
                            echo estado($v['Reparacionesestado']['nombre']);
                            if ($v['created'] != $v['modified']) {
                                echo " - <span style='color:grey;font-style:italic;font-size:12px'>Modificado: " . $this->Time->format(__('d/m/Y H:i:s'), $v['modified']) . "</span>";
                            }
                            echo "<br>";
                            echo $v['observaciones'];
                            if (isset($v['Reparacionesactualizacionesproveedore']) && !empty($v['Reparacionesactualizacionesproveedore'])) {
                                $cad = "<u>Proveedores asignados:</u> ";
                                $hay = false;
                                foreach ($v['Reparacionesactualizacionesproveedore'] as $r => $s) {
                                    if (!$s['finalizado']) {
                                        $hay = true;
                                        $cad .= h($proveedors[$s['proveedor_id']]) . " <b>||</b> ";
                                    }
                                }
                                echo $hay ? substr($cad, 0, -11) . "<br>" : '';

                                $cad = "<u>Proveedores finalizados:</u> ";
                                $hay = false;
                                foreach ($v['Reparacionesactualizacionesproveedore'] as $r => $s) {
                                    if ($s['finalizado']) {
                                        $hay = true;
                                        $cad .= h($proveedors[$s['proveedor_id']]) . " <b>||</b> ";
                                    }
                                }
                                echo $hay ? substr($cad, 0, -11) . "<br>" : '';
                            }
                            if (isset($v['Reparacionesactualizacionessupervisore']) && !empty($v['Reparacionesactualizacionessupervisore'])) {
                                $cad = "<u>Supervisores asignados:</u> ";
                                $hay = false;
                                foreach ($v['Reparacionesactualizacionessupervisore'] as $r => $s) {
                                    if (!$s['finalizado']) {
                                        $hay = true;
                                        $cad .= h($reparacionessupervisores[$s['reparacionessupervisore_id']]) . " <b>||</b> ";
                                    }
                                }
                                echo $hay ? substr($cad, 0, -11) . "<br>" : '';

                                $cad = "<u>Supervisores finalizados:</u> ";
                                $hay = false;
                                foreach ($v['Reparacionesactualizacionessupervisore'] as $r => $s) {
                                    if ($s['finalizado']) {
                                        $hay = true;
                                        $cad .= h($reparacionessupervisores[$s['reparacionessupervisore_id']]) . " <b>||</b> ";
                                    }
                                }
                                echo $hay ? substr($cad, 0, -11) . "<br>" : '';
                            }

                            // llaves
                            //debug($v['Reparacionesactualizacionesllavesmovimiento']);
                            if (isset($v['Reparacionesactualizacionesllavesmovimiento']) && !empty($v['Reparacionesactualizacionesllavesmovimiento'])) {
                                $cad = "<u>Llaves:</u> ";
                                $hay = false;
                                $cad2 = "<div style='padding-left:40px'>";
                                foreach ($v['Reparacionesactualizacionesllavesmovimiento'] as $r => $s) {
                                    $hay = true;
                                    $cad2 .= h($s['Llavesmovimiento']['titulo'] . " - " . $s['Llavesmovimiento']['Llave']['name2']) . " al ";
                                    if ($s['Llavesmovimiento']['proveedor_id'] != 0) {
                                        $cad2 .= "<u>Proveedor</u> " . h($s['Llavesmovimiento']['Proveedor']['name']);
                                    } else if ($s['Llavesmovimiento']['reparacionessupervisore_id'] != 0) {
                                        $cad2 .= "<u>Supervisor</u> " . h($s['Llavesmovimiento']['Reparacionessupervisore']['nombre']);
                                    } else {
                                        $cad2 .= "<u>Propietario</u> " . h($rep['Consorcio']['name'] . " - " . $s['Llavesmovimiento']['Propietario']['name2']);
                                    }
                                    $cad2 .= "<br>";
                                }
                                $cad2 .= "</div>";
                                echo $hay ? $cad . $cad2 : '';
                            }

                            //adjuntos
                            if (isset($v['Reparacionesactualizacionesadjunto']) && !empty($v['Reparacionesactualizacionesadjunto'])) {
                                $cad = "<u>Adjuntos:</u> ";
                                $hay = false;
                                foreach ($v['Reparacionesactualizacionesadjunto'] as $r => $s) {
                                    $hay = true;
                                    $cad .= $this->Html->link(h($s['titulo']), array('controller' => 'Reparacionesactualizacionesadjuntos', 'action' => 'download', $this->Functions->_encryptURL($s['ruta']))) . " <b>||</b> ";
                                }
                                echo $hay ? substr($cad, 0, -11) . "<br>" : '';
                            }
                            echo "<hr>";
                        }
                    }
                    ?>        
                </div>
                <?php
            }
            ?>
            <style>
                hr{border-top: 2px dashed #222;}
                @media print{
                    #seccionaimprimir{display:none !important}
                    .seccionaimprimir{margin-top:0px !important;font-size:14px !important}
                    img{display:none}
                }
            </style>
            <?php
            if (!$this->request->is('ajax')) {// cuando quieren imprimir
                //echo "<script>window.print();window.close();</script>";
            }
        }
        ?>
    </body>
</html>
<?php

function estado($cad) {
    echo " - Estado: <p style='font-weight:bold;display:inline-block;margin:0;color:";
    switch ($cad) {
        case "En curso":
            echo "orange'";
            break;
        case "Finalizada":
            echo "green'";
            break;
        case "Pendiente":
            echo "red'";
            break;
        case "Suspendida":
            echo "red;text-decoration:line-through;'";
            break;
        case "Anulada":
            echo "red;text-decoration:line-through;'";
            break;
        default:
            echo "orange'";
            break;
    }

    echo ">" . h($cad) . "</p>";
}
