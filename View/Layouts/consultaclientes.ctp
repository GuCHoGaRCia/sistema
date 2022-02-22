<!-- Sistema Web Desarrollado por estebancano.com -->
<?php
require_once("menu.php");
$menu = new iH2HMenu;
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="theme-color" content="#08c">
        <?php
        echo $this->Minify->css(array('admin.css', 'menustyle', 'jquery-ui.min', 'select2.min'), ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
        echo $this->Minify->css(array('main', 'bootstrap.min.css', 'bootstrap-editable.css'), ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
        echo $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']);
        echo $this->Html->charset();
        echo $this->Minify->script(['jq', 'bs', 'jqui', 'jqval', 'jqvales', 'am', 'jqmeta'/* . $this->Session->read('Config.language') */, 'm', 'bsedit', 'admpanelclientes', 's2', 'i18n/es'], ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
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
                    <b><?php echo __('Bienvenid@, ') . ucwords(h($_SESSION['Auth']['User']['name'])) . "@" . $_SESSION['Auth']['User']['Client']['identificador_cliente']; ?></b>
                </div>
            </div>
            <div id="menu">
                <div class="menucont" style='padding-right:100px'>
                    <?php
                    $menu->setMainLink(__('Datos'));
                    $menu->setSubLink(__('Datos'), __('Control'), $this->webroot . 'panel/clients/control');
                    $menu->setSubLink(__('Datos'), __('Clientes'), $this->webroot . 'panel/clients');
                    $menu->setSubLink(__('Datos'), __('Consultas'), $this->webroot . 'panel/consultas');
                    $menu->setSubLink(__('Datos'), __('Avisos'), $this->webroot . 'panel/avisos');
                    $menu->setSubLink(__('Datos'), __('Enviar email'), $this->webroot . 'panel/emails');
                    $menu->setMainLink(__('Salir'), $this->webroot . 'panel/users/logout');
                    //Generate the layers
                    $menu->makeDivs();
                    //Generate the menu
                    $menu->makelinks();
                    ?>
                </div>
            </div>
            <?php
            echo $this->Flash->render('otro');
            echo $this->Flash->render();
            ?>
            <?php
            // muestro la ayuda
            if (!empty($help)) {
                ?>
                <div id="helpdiv">
                    <span class="tooltipx"><?php echo $this->Html->image('help.png', array('width' => '24px', 'height' => '24px', 'id' => 'helpimg')); ?>
                        <span id="helpcontent"><?php echo $help["Help"]["content"]; ?>
                            <span style="color:green;font-style:italic;"><?= __('&Uacute;ltima modificaci&oacute;n: ') . $this->Time->format(__('d/m/Y'), $help["Help"]["modified"]) ?></span>
                            <?php
                            if ($_SESSION['Auth']['User']['is_admin']) {
                                echo $this->Html->image('edit.png', array('width' => '16px', 'height' => '16px', 'alt' => __('Editar'), 'title' => __('Editar'), 'url' => array('controller' => 'Helps', 'action' => 'edit', $help["Help"]['id'])));
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
                if (editable.input.type == "text") {
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
</html>
<!-- Sistema Web Desarrollado por estebancano.com -->