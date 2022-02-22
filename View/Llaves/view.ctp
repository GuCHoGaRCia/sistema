<?php
if (empty($llave)) {
    echo "<div class='info'>No se encuentran movimientos para la Llave seleccionada</div>";
} else {
    echo $this->Html->image('print2.png', ['alt' => __('Imprimir'), 'title' => __('Imprimir'), 'class' => 'imgmove', 'style' => 'float:right;cursor:pointer', 'onclick' => 'window.open("' . $this->webroot . 'Llaves/view/' . $this->request->params['pass'][0] . '");']);
    ?>
    <div class="seccionaimprimir" style="width:100%;height:100%;">
        <h3>Movimientos Llave <?= isset($llave[0]['Llave']) ? h("#" . $llave[0]['Llave']['numero'] . " " . $llave[0]['Llave']['descripcion'] . (isset($users[$llave[0]['Llave']['user_id']]) ? " - Creada por: " . $users[$llave[0]['Llave']['user_id']] : '')) : '' ?></h3>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="esq_i"></td>
                    <th style="width:70px"><?php echo __('Fecha') ?></th>
                    <th style="width:100px"><?php echo __('Usuario') ?></th>
                    <th style="width:80px"><?php echo __('Estado') ?></th>
                    <th><?php echo __('Origen/Destino') ?></th>
                    <th><?php echo __('Título') ?></th>
                    <td class="esq_d"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                if (!empty($llave)) {
                    //debug($llave);
                    foreach ($llave as $row) {
                        $class = null;
                        if ($i++ % 2 == 0) {
                            $class = ' class="altrow"';
                        }
                        ?>
                        <tr<?php echo $class; ?> style="border-top:1px solid gray">
                            <td class="borde_tabla"></td>
                            <td><?php echo $this->Time->format(__('d/m/Y'), $row['Llavesmovimiento']['fecha']) ?>&nbsp;</td>
                            <td><?= h($users[$row['Llave']['user_id']]) ?>&nbsp;</td>
                            <td><?= h($llavesestados[$row['Llavesestado']['id']]) ?>&nbsp;</td>
                            <td><?php
                                if (isset($row['Proveedor']['name'])) {
                                    echo h("Proveedor " . $row['Proveedor']['name']);
                                } elseif (isset($row['Reparacionessupervisore']['nombre'])) {
                                    echo h("Supervisor " . $row['Reparacionessupervisore']['nombre']);
                                } else {
                                    echo h("Consorcio " /* . $row['Consorcio']['name'] */);
                                    if (isset($row['Propietario']['name'])) {
                                        echo h("Propietario " . $row['Propietario']['name']);
                                    }
                                }
                                ?>&nbsp;
                            </td>
                            <td><?= h($row['Llavesmovimiento']['titulo']) ?>&nbsp;</td>
                            <td class="borde_tabla"></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="5"></td>
                    <td class="bottom_d"></td>
                </tr>
        </table>
    </div>
    <?php
    if (!$this->request->is('ajax')) {// cuando quieren imprimir
        echo $this->Minify->css(['main']);
        echo "<script>window.print();window.close();</script>";
        ?>
        <style>
            @media print{
                #seccionaimprimir{display:none !important}
                .seccionaimprimir{margin-top:0px !important;font-size:12px !important}
                img{display:none}
                table{color:inherit;}
            }
        </style>
        <?php
    }
}

