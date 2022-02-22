<div class="clients form">
    <h2>Panel control
        <?php
        if ($_SESSION['Auth']['User']['id'] == -1000 || $_SESSION['Auth']['User']['id'] == -1009) {
            ?>
            - <a href="https://ceonline.com.ar/sistema/CeoReporteRealtime.html" rel='nofollow noopener noreferrer' target='_blank'>Log de accesos</a>
            <?php
        }
        ?>    
    </h2>
    <div class='info'>
        En esta secci&oacute;n se podr&aacute;n verificar la existencia de eventos recientes en el Sistema tales como nuevas consultas, envios nuevos a la Cola de Impresión, etc. 
        Esta pantalla se recarga automáticamente cada 30 segundos
    </div>
    <br />
    <div class='inline'>
        <div id='c' class='info' style='width:auto;cursor:pointer' onclick='window.location.href = "<?= $this->webroot ?>panel/Consultas"'>
            Consultas Clientes
        </div>
        <!--div id='ci' class='info' style='width:auto;cursor:pointer' onclick='window.location.href = "<?= $this->webroot ?>panel/Colaimpresiones"'>
            Cola impresiones
        </div-->
    </div>
    <br>

    <div class="principal">
        <div class="titulo">
            &Uacute;ltimos Liquidados - <b>Total unidades: <?= $cantidadunidades[0][0]['cant'] ?></b>
        </div>
        <div class="contenido">
            <table>
                <thead>
                <td class="esq_i"></td>
                <th>Cliente</th>
                <td class="esq_d"></td>
                </thead>
                <?php
                $i = 0;
                foreach ($ultimosliquidados as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . h($clients[$v['colaimpresiones']['Cliente']]) . "</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow" >
                    <td class="bottom_i"></td>
                    <td>&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="principal">
        <div class="titulo">
            Pagos Electr&oacute;nicos Plataformas
        </div>
        <div class="contenido">
            <table>
                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td colspan="3" style="text-align:center">PLAPSA</td>
                    <td class="borde_tabla"></td>
                </tr>
                <thead>
                <td class="esq_i"></td>
                <th>Fecha</th>
                <th style="text-align:center">Cantidad</th>
                <th style="text-align:right">Total</th>
                <td class="esq_d"></td>
                </thead>
                <?php
                $i = 0;
                foreach ($plapsa as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . $this->Time->format(__('d/m/Y'), $v[0]['Fecha']) . "</td><td style='text-align:center'>" . h($v[0]['Cantidad'] . " (" . $v[0]['cantcli'] . " clientes)") . "</td><td style='text-align:right'>" . $this->Functions->money($v[0]['Total']) . "</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow">
                    <td class="borde_tabla"></td>
                    <td colspan="3" style="text-align:center">ROELA</td>
                    <td class="borde_tabla"></td>
                </tr>
                <?php
                $i = 0;
                foreach ($roela as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . $this->Time->format(__('d/m/Y'), $v[0]['Fecha']) . "</td><td style='text-align:center'>" . h($v[0]['Cantidad'] . " (" . $v[0]['cantcli'] . " clientes)") . "</td><td style='text-align:right'>" . $this->Functions->money($v[0]['Total']) . "</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="3">&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="principal">
        <div class="titulo">
            &Uacute;ltimos usuarios conectados
        </div>
        <div class="contenido">
            <table>
                <thead>
                <td class="esq_i"></td>
                <th style='width:50%'>Fecha</th>
                <th>Usuario@Cliente</th>
                <td class="esq_d"></td>
                </thead>
                <?php
                $i = 0;
                foreach ($usuarios as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . $this->Time->timeAgoInWords($v['u']['Fecha']) . "</td><td>";
                    echo $this->Html->link(h($v['u']['Usuario'] . "@" . $v['c']['Cliente']), ['controller' => 'Audits', 'action' => 'index', 'buscar:' . h($v['u']['Usuario'] . "@" . $v['c']['Cliente'])], ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer', 'escape' => false]);
                    echo "</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="2">&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="principal">
        <div class="titulo">
            Proceso Saldos Caja Banco
        </div>
        <div class="contenido">
            <table>
                <thead>
                <td class="esq_i"></td>
                <th>Fecha</th>
                <th style="text-align:center">Consorcios</th>
                <th style="text-align:right">Tiempo</th>
                <td class="esq_d"></td>
                </thead>
                <?php
                $i = 0;
                foreach ($saldoscajabanco as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . $this->Time->format(__('d/m/Y'), $v[0]['Fecha']) . "</td><td style='text-align:center'>" . h($v[0]['Consorcios']) . "</td><td style='text-align:right'>" . h($v[0]['Segundosejecucion']) . " seg</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow">
                    <td class="bottom_i"></td>
                    <td colspan="3">&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="principal">
        <div class="titulo">
            Lista negra emails
        </div>
        <div class="contenido">
            <table>
                <thead>
                <td class="esq_i"></td>
                <th>Cliente</th>
                <th style='text-align:center'>Emails</th>
                <td class="esq_d"></td>
                </thead>
                <?php
                $i = 0;
                foreach ($blacklist as $k => $v) {
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    echo "<tr $class><td class='borde_tabla'></td><td>" . h($v['c']['Cliente']) . "</td><td style='text-align:center'>" . h($v[0]['Cantidad']) . "</td><td class='borde_tabla'></td></tr>";
                }
                ?>
                <tr class="altrow" >
                    <td class="bottom_i"></td>
                    <td colspan="2">&nbsp;</td>
                    <td class="bottom_d"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="principal">
        <div class="titulo">
            &Uacute;ltimas consultas
        </div>
        <div class="contenido" style="width:49%;display:inline-block !important">
            <table>
                <?php
                $i = 0;
                echo "<tr><td class='borde_tabla'><td>";
                foreach ($consultas as $k => $v) {
                    $i++;
                    echo h(substr($v['c']['name'], 0, 28)) . "<br>";
                    if ($i == 12) {
                        break;
                    }
                }
                echo "</td><td class='borde_tabla'></td></tr>";
                ?>
            </table>
        </div>
        <div class="contenido" style="width:49%;display:inline-block !important">
            <table>
                <?php
                $j = 0;
                echo "<tr><td class='borde_tabla'><td>";
                foreach ($consultas as $k => $v) {
                    $j++;
                    if ($j <= $i) {
                        continue;
                    }
                    echo h(substr($v['c']['name'], 0, 20)) . "<br>";
                    if ($j == 9) {
                        break;
                    }
                }
                echo "</td><td class='borde_tabla'></td></tr>";
                ?>
            </table>
        </div>
    </div>
    <audio id="a1" style="display:none;">
        <source src="<?= $this->webroot ?>img/msg.ogg" type="audio/ogg">
    </audio>
    <audio id="a2" style="display:none;">
        <source src="<?= $this->webroot ?>img/msg2.ogg" type="audio/ogg">
    </audio>
</div>
<script>
    $(document).ready(function () {
        v1();
    });
    setInterval(v1, 60000);
    function v1() {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultas/verificar", cache: false, data: {}
        }).done(function (msg) {
            if (msg !== "[]") {<?php /* Hay consultas */ ?>
                $("#c").attr("class", "warning");
                document.getElementById('a1').play();
            } else {
                $("#c").attr("class", "info");
            }
        }).always(function () {
            //v2();
        });
    }
    function v2() {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Colaimpresiones/verificar", cache: false, data: {}
        }).done(function (msg) {
            if (msg !== "[]") {<?php /* Hay cosas en la cola sin imprimir */ ?>
                $("#ci").attr("class", "warning");
                document.getElementById('a2').play();
            } else {
                $("#ci").attr("class", "info");
            }
        });
    }
</script>
<style>
    .principal{
        width:auto;
        min-width:30%;
        float:left;
        margin:10px;
        background-color:#fff;
        border:1px solid #00529B;
        border-radius:4px;
        box-shadow:10px 10px rgba(0,0,0,.05);
    }
    .titulo,.contenido{
        padding:5px;
        width:100%;
        border-radius:3px;
        border-bottom:1px solid #00529B;
        background-color:#BDE5F8;
    }
    .contenido{
        background-color:#fff;
        height:270px;
    }
    #centro{height:1000px}
    @media (max-width: 858px) {
        body{
            font-size: 11px;
        }
    }
    @media (max-width: 780px) {
        body{
            font-size: 11px;
        }
    }
    @media (max-width: 724px) {
        body{
            font-size: 10px;
        }
    }
    @media (max-width: 702px) {
        body{
            font-size: 10px;
        }
    }
    @media (max-width: 623px) {
        body{
            font-size: 9px;
        }
    }
    @media only screen and (max-width: 600px) {
        body{
            font-size: 8px;
        }
    }
</style>