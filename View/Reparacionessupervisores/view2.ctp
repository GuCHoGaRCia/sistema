<?php
/*
 * Es la lupa del Panel de Supervisor para ver el detalle de la reparacion
 */
if (empty($reparaciones)) {
    echo "<div class=''>La reparaci&oacute;n es inexistente</div>";
} else {
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'style' => 'float:right;cursor:pointer', 'onclick' => 'window.open("' . $this->webroot . 'Reparacionessupervisores/view2/' . $this->request->params['pass'][0] . "/" . $this->request->params['pass'][1] . '");']);
    ?>
    <div class='seccionaimprimir' style='width:100%'>
        <h4 style="margin-top:0px;text-align:center">
            <?php
            echo h($this->Time->format(__('d/m/Y'), $reparaciones['Reparacione']['fecha']) . " - " . $reparaciones['Consorcio']['name']);
            echo!empty($reparaciones['Propietario']['name2']) ? h(" - " . $reparaciones['Propietario']['name2']) : '';
            $usuario = isset($users[$reparaciones['Reparacione']['user_id']]) ? $users[$reparaciones['Reparacione']['user_id']] : $reparacionessupervisores[$reparaciones['Reparacione']['user_id']]; //si no existe el usuario, es un supervisor!
            echo h(" - Creada por " . $users[$reparaciones['Reparacione']['user_id']]);
            ?>
        </h4>
        <h4 style='text-align:center'><?php echo h($reparaciones['Reparacione']['concepto']) ?>
            <?php
            $estado = isset($reparaciones['Reparacionesactualizacione']) && !empty($reparaciones['Reparacionesactualizacione']) ? reset($reparaciones['Reparacionesactualizacione'])['Reparacionesestado']['nombre'] : $reparaciones['Reparacionesestado']['nombre'];
            estado($estado); // muestro el estado
            ?>
        </h4>
        <hr>
        <?php
        if (isset($reparaciones['Reparacionesactualizacione']) && !empty($reparaciones['Reparacionesactualizacione'])) {
            foreach ($reparaciones['Reparacionesactualizacione'] as $k => $v) {

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
                            $cad2 .= "<u>Propietario</u> " . h($reparaciones['Consorcio']['name'] . " - " . $s['Llavesmovimiento']['Propietario']['name2']);
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
                        $cad .= $this->Html->link(h($s['titulo']), array('controller' => 'Reparacionesactualizacionesadjuntos', 'action' => 'download', $this->Functions->_encryptURL($s['ruta']), $this->Functions->_encryptURL($reparaciones['Consorcio']['client_id']))) . " <b>||</b> ";
                    }
                    echo $hay ? substr($cad, 0, -11) . "<br>" : '';
                }
                echo "<hr>";
            }
        }
        ?>        
    </div>
    <style>
        hr{border-top: 2px dashed #222;}
        @media print{
            #seccionaimprimir{display:none !important}
            .seccionaimprimir{margin-top:0px !important;font-size:20px !important}
            img{display:none}
        }
    </style>
    <?php
    if (!$this->request->is('ajax')) {// cuando quieren imprimir
        echo "<script>window.print();window.close();</script>";
    }
}

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
