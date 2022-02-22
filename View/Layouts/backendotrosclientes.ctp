<!-- Sistema Web Desarrollado por estebancano.com -->
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <base href="https://ceonline.com.ar/sistema">
        <meta name="theme-color" content="#08c">
        <?php
        echo $this->Minify->css(['admin.css', 'menustyle', 'jquery-ui.min'], ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
        echo $this->Minify->css(['main', 'bootstrap.min.css'], ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
        echo $this->Html->meta('icon');
        echo $this->Html->charset();
        echo $this->Minify->script(['jq', 'bs', 'jqui', 'jqval', 'jqvales', 'am', 'jqmeta', 'm', 'bsedit', 'admpanelclientes', 's2', 'i18n/es', 'datepicker-es.js'], ['fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar']);
        ?>
        <meta name="author" content="CEONLINE" />
        <meta name="description" content="CEONLINE - Sistema Web de Liquidacion de Expensas para Administradores de Consorcios" />
        <meta name="keywords" content="sistema liquidacion de expensas,liquidar expensas, administracion de consorcios, procesamiento de expensas, facturas, web, sistemas, internet, expensas por internet, expensas on line, inmobiliaria, administracion"/>
        <title>CEONLINE - Panel de Gesti&oacute;n</title>
    </head>
    <body>
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-85179216-1', 'auto');
            ga('send', 'pageview');
            $(document).tooltip({
                position: {
                    my: "center bottom-25",
                    at: "center top",
                }
            });
        </script>
        <div id="container">
            <div id="banner">
                <div id="logo"></div>
                <div id="bienvenido">
                    <b><?php //echo __('Bienvenid@, ') . ucwords(h($_SESSION['Auth']['User']['name']));        ?></b>
                </div>
            </div>
            <?php
            echo $this->Flash->render('otro');
            echo $this->Flash->render();
            ?>
            <div id="centro">
                <?php
                echo $this->fetch('css');
                echo $this->fetch('content');
                ?>
            </div>
            <?php
            // muestro la ayuda
            if (!empty($help)) {
                ?>
                <span class="tooltipx" style="top:58px"><img src="<?= $this->Html->assetUrl('help.png', array('width' => '24px', 'height' => '24px', 'id' => 'helpimg', 'fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar/sistema/img/')) ?>" />
                    <span id="helpcontent"><?php echo $help["Help"]["content"]; ?>
                        <span style="color:green;font-style:italic;"><?= __('&Uacute;ltima modificaci&oacute;n: ') . $this->Time->format(__('d/m/Y'), $help["Help"]["modified"]) ?></span>
                        <?php
                        if (!empty($_SESSION['Auth']['User']['is_admin'])) {
                            echo $this->Html->image('edit.png', ['width' => '16px', 'height' => '16px', 'alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['controller' => 'panel/Helps', 'action' => 'edit', $help["Help"]['id']], 'fullBase' => true, 'pathPrefix' => 'https://ceonline.com.ar/sistema/img/']);
                        }
                        ?>
                    </span>                    
                </span>
                <?php
            }
            ?>
        </div>
        <?php
        include("footer.ctp");
        ?>
        <?php echo $this->element('sql_dump'); ?>
    </body>
</html>
<!-- Sistema Web Desarrollado por estebancano.com -->