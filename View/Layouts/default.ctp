<!-- Sistema Web Desarrollado por estebancano.com -->
<?php
require_once("menu.php");
$menu = new iH2HMenu;
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="dns-prefetch" href="//google.com"/>
        <link rel="preconnect" href="//google.com"/>
        <link rel="dns-prefetch" href="//googletagmanager.com"/>
        <link rel="preconnect" href="//googletagmanager.com"/>
		<meta name="theme-color" content="#08c">
        <?php
        echo "<script src='https://www.google.com/recaptcha/api.js?render=6LcAemkUAAAAANRp9g9UW-AyUafthNCPXpdENUHq&onload=onloadCallback&render=explicit'></script>";
        echo $this->Minify->css(['main', 'bootstrap.min.css']);
        echo $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']);
        echo $this->Html->charset();
        echo $this->Minify->script(['jq']);
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
            </div>
            <div id="menu">
                <div id="bienvenido">
                </div>
                <div class="menucont">
                </div>
            </div>
            <div id="centro">
                <?php
                echo $this->Flash->render('otro');
                echo $this->Flash->render();
                echo $this->fetch('css');
                echo $this->fetch('content');
                ?>
            </div>
        </div>
        <?php
        include("footer.ctp");
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-84597691-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'UA-84597691-1');
        </script>
    </body>
</html>
<!-- Sistema Web Desarrollado por estebancano.com -->