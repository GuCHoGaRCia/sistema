<!-- Sistema Web Desarrollado por estebancano.com -->
<?php
require_once("menu.php");
$menu = new iH2HMenu;
$cant = 0;
foreach (scandir(session_save_path()) as $k => $v) {
    if (substr($v, 0, 5) == 'sess_') {
        if (@filectime(session_save_path() . '/' . $v) > (time() - 60)) {
            $cant++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="theme-color" content="#08c">
        <?php
        echo $this->Minify->css(['admin.css', 'menustyle', 'jquery-ui.min', 'select2.min']);
        echo $this->Minify->css(['main', 'bootstrap.min.css', 'bootstrap-editable.css']);
        echo $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']);
        echo $this->Html->charset();
        echo $this->Minify->script(['jq', 'bs', 'jqui', 'jqval', 'jqvales', 'am', 'jqmeta', 'm', 'bsedit', 'admpanelclientes', 's2', 'i18n/es', 'datepicker-es']);
        ?>
        <meta name="author" content="CEONLINE" />
        <meta name="description" content="CEONLINE - Sistema Web de Liquidacion de Expensas para Administradores de Consorcios" />
        <meta name="keywords" content="sistema liquidacion de expensas,liquidar expensas, administracion de consorcios, procesamiento de expensas, facturas, web, sistemas, internet, expensas por internet, expensas on line, inmobiliaria, administracion"/>
        <title>CEONLINE - Panel de Gesti&oacute;n</title>
    </head>
    <body>
        <div id="container">
            <div id="banner">
                <div id="logo"></div>
                <div id="bienvenido">
                    <b><?php echo __('Bienvenid@, ') . ucwords(h($_SESSION['Auth']['User']['name'])) . "@" . h($_SESSION['Auth']['User']['Client']['identificador_cliente']); ?><br>
                        [~<?= $cant ?> ONLINE ]
                    </b>
                </div>
            </div>
            <nav>
                <div class="menucont">
                    <?php
                    /* $menu->setMainLink(__('Datos'));
                      $menu->setSubLink(__('Datos'), __('Clientes'), $this->webroot . 'panel/clients');
                      $menu->setSubLink(__('Datos'), __('Usuarios'), $this->webroot . 'panel/users');
                      $menu->setSubLink(__('Datos'), __('Noticias'), $this->webroot . 'panel/noticias');
                      $menu->setSubLink(__('Datos'), __('Reportes'), $this->webroot . 'panel/reports');
                      $menu->setSubLink(__('Datos'), __('Consorcios'), $this->webroot . 'panel/consorcios');
                      $menu->setSubLink(__('Datos'), __('Consultas'), $this->webroot . 'panel/consultas');
                      $menu->setSubLink(__('Datos'), __('Cola impresiones'), $this->webroot . 'panel/colaimpresiones');
                      $menu->setSubLink(__('Datos'), __('Enviar email'), $this->webroot . 'panel/emails');
                      $menu->setSubLink(__('Datos'), __('Ayudas'), $this->webroot . 'panel/helps');
                      $menu->setSubLink(__('Datos'), __('Cartas precios'), $this->webroot . 'panel/cartasprecios');
                      $menu->setSubLink(__('Datos'), __('Cartas tipos'), $this->webroot . 'panel/cartastipos');
                      $menu->setMainLink(__('Gastos'));
                      $menu->setSubLink(__('Gastos'), __('Cartas clientes CEONLINE'), $this->webroot . 'panel/cartas/');
                      $menu->setSubLink(__('Gastos'), __('Cartas clientes terceros'), $this->webroot . 'panel/cartas/add2');
                      $menu->setMainLink(__('Liquidaciones'));
                      $menu->setSubLink(__('Liquidaciones'), __('Liquidaciones'), $this->webroot . 'panel/liquidations');
                      $menu->setSubLink(__('Liquidaciones'), __('Avisos'), $this->webroot . 'panel/avisos');
                      $menu->setSubLink(__('Liquidaciones'), __('Blacklist'), $this->webroot . 'panel/avisosblacklists');
                      $menu->setMainLink(__('Procesos'));
                      $menu->setSubLink(__('Procesos'), __('PLAPSA Pagos'), $this->webroot . 'panel/pagoselectronicos');
                      $menu->setSubLink(__('Procesos'), __('Varios'), $this->webroot . 'panel/clients/procesos');
                      $menu->setMainLink(__('Salir'), $this->webroot . 'panel/users/logout');
                      //Generate the layers
                      //$menu->makeDivs();
                      //Generate the menu
                      //$menu->makelinks();
                      echo "<!--span>";
                      debug($menu->makeDivs() . $menu->makelinks());
                      echo "</span>--";
                      die; */
                    ?>
                    <div id="DatosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('DatosDIV', '', 'show')" onMouseOut="MM_showHideLayers('DatosDIV', '', 'hide')">
                        <ul><li><a href="/sistema/panel/clients/control">Control</a></li><li><a href="/sistema/panel/clients">Clientes</a></li>
                            <li><a href="/sistema/panel/users">Usuarios</a></li>
                            <li><a href="/sistema/panel/UserProfiles">Perfiles</a></li>
                            <li><a href="/sistema/panel/noticias">Noticias</a></li>
                            <li><a href="/sistema/panel/reports">Reportes</a></li><li><a href="/sistema/panel/consorcios">Consorcios</a></li>
                            <li><a href="/sistema/panel/consultas">Consultas</a></li>
                            <li><a href="/sistema/panel/llamados">Llamados</a></li>
                            <li><a href="/sistema/panel/colaimpresiones/impresiones">Cola Impresiones</a></li>
                            <li><a href="/sistema/panel/colaimpresiones/finalizadas">Cola Finalizadas</a></li>
                            <li><a href="/sistema/panel/emails">Enviar Email</a></li>
                            <li><a href="/sistema/panel/helps">Ayudas</a></li><li><a href="/sistema/panel/audits">Auditor&iacute;a</a></li>
                            <li><a href="/sistema/panel/cartasprecios">Cartas Precios</a></li>
                            <li><a href="/sistema/panel/cartastipos">Cartas Tipos</a></li>                 
                            <li><a href="/sistema/panel/Formasdepagos">Formas De Pago</a></li>
                            <li><a href="/sistema/panel/plataformasdepagos">Plataformas de Pago</a></li>
                        </ul>
                    </div>

                    <div id="GastosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('GastosDIV', '', 'show')" onMouseOut="MM_showHideLayers('GastosDIV', '', 'hide')">
                        <ul><li><a href="/sistema/panel/cartas/">Cartas Clientes CEONLINE</a></li><li><a href="/sistema/panel/cartas/add2">Cartas Clientes Terceros</a></li>
                            <li><a href="/sistema/panel/cartas/boleta">Boleta imposici&oacute;n</a></li>
                            <li><a href="/sistema/panel/cartas/envios">Env&iacute;os Postales del d&iacute;a</a></li></ul></div><div id="LiquidacionesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('LiquidacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('LiquidacionesDIV', '', 'hide')">
                        <ul><li><a href="/sistema/panel/liquidations">Liquidaciones</a></li>
                            <li><a href="/sistema/panel/avisos">Avisos</a></li>
                            <li><a href="/sistema/panel/avisosqueues">Cola Avisos</a></li>
                            <li><a href="/sistema/panel/avisosenviados">Avisos Enviados</a></li>
                            <li><a href="/sistema/panel/avisosblacklists">Blacklist</a></li>                    
                        </ul></div><div id="ProcesosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ProcesosDIV', '', 'show')" onMouseOut="MM_showHideLayers('ProcesosDIV', '', 'hide')">
                        <ul><li><a href="/sistema/panel/pagoselectronicos">PLAPSA Pagos</a></li>
                            <li><a href="/sistema/panel/pagoselectronicos/roela">ROELA Pagos</a></li>
                            <ul><li><a href="/sistema/panel/pagoselectronicos/comisiones">Comisiones</a></li>
                                <ul><li><a href="/sistema/panel/consorcios/listar">Facturaci&oacute;n</a></li>
                                    <li><a href="/sistema/panel/clients/procesos">Varios</a></li>                    
                                </ul></div><div class="links2" id="Datos" onMouseOver="setLyr(this, 'DatosDIV');MM_showHideLayers('DatosDIV', '', 'show')" onMouseOut="MM_showHideLayers('DatosDIV', '', 'hide')"><a href="#">Datos</a></div>
                                <div class="links2" id="Gastos" onMouseOver="setLyr(this, 'GastosDIV');MM_showHideLayers('GastosDIV', '', 'show')" onMouseOut="MM_showHideLayers('GastosDIV', '', 'hide')"><a href="#">Gastos</a></div>
                                <div class="links2" id="Liquidaciones" onMouseOver="setLyr(this, 'LiquidacionesDIV');MM_showHideLayers('LiquidacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('LiquidacionesDIV', '', 'hide')"><a href="#">Liquidaciones</a></div>
                                <div class="links2" id="Procesos" onMouseOver="setLyr(this, 'ProcesosDIV');MM_showHideLayers('ProcesosDIV', '', 'show')" onMouseOut="MM_showHideLayers('ProcesosDIV', '', 'hide')"><a href="#">Procesos</a></div>
                                <div class="links2" id="Salir"><a href="/sistema/panel/users/logout">Salir</a></div>
                                </div>
                                </nav>
                                <?php
                                echo $this->Flash->render('otro');
                                echo $this->Flash->render();
                                ?>
                                <?php
                                // muestro la ayuda
                                if (!empty($help)) {
                                    ?>
                                    <div id="helpdiv">
                                        <span class="tooltipx"><?php echo $this->Html->image('help.png', ['width' => '24px', 'height' => '24px', 'id' => 'helpimg']); ?>
                                            <span id="helpcontent"><?php echo $help["Help"]["content"]; ?>
                                                <span style="color:green;font-style:italic;"><?= __('&Uacute;ltima modificaci&oacute;n: ') . $this->Time->format(__('d/m/Y'), $help["Help"]["modified"]) ?></span>
                                                <?php
                                                if ($_SESSION['Auth']['User']['is_admin']) {
                                                    echo $this->Html->image('edit.png', ['width' => '16px', 'height' => '16px', 'alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['controller' => 'Helps', 'action' => 'edit', $help["Help"]['id']]]);
                                                }
                                                ?>
                                            </span>                    
                                        </span>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div id="centro">
                                    <?php
                                    echo $this->fetch('css');
                                    echo $this->fetch('content');
                                    ?>
                                </div>
                                </div>
                                <?php
                                include("footer.ctp");
                                ?>
                                <?php echo $this->element('sql_dump'); ?>
                                </body>
                                <script>
                                    $(document).ready(function () {
                                        $('.editable').on('shown', function (e, editable) {
                                            if (editable.input.type === "text") {
                                                editable.input.postrender = function () {
                                                    editable.input.$input.select();
                                                };
                                            }
                                        });
                                    });
                                    $(document).tooltip({
                                        position: {
                                            my: "center bottom-25",
                                            at: "center top",
                                        }
                                    });
                                </script>
                                <script async src="https://www.googletagmanager.com/gtag/js?id=UA-84621011-1"></script>
                                <script>
                                    window.dataLayer = window.dataLayer || [];
                                    function gtag() {
                                        dataLayer.push(arguments);
                                    }
                                    gtag('js', new Date());
                                    gtag('config', 'UA-84621011-1');
                                </script>
                                </html>
                                <!-- Sistema Web Desarrollado por estebancano.com -->