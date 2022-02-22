<!-- Sistema Web Desarrollado por estebancano.com --> 
<?php
//require_once("menu.php");
//$menu = new iH2HMenu;
?>
<!DOCTYPE html>
<html lang="es-419">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="dns-prefetch" href="//googletagmanager.com"/>
        <link rel="preconnect" href="//googletagmanager.com"/>
        <meta name="theme-color" content="#08c">
        <?php
        echo $this->Minify->css(['admin', 'menustyle', 'jquery-ui.min', 'select2.min']);
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
                    <b><?php echo __('Bienvenid@, ') . ucwords(h($_SESSION['Auth']['User']['name'])) . "@" . h($_SESSION['Auth']['User']['Client']['identificador_cliente']); ?></b><br>
                    <u><span class='hand imgmove' onclick='window.open("https://g.page/r/CUiqDEzZ0fZKEAg/review", "_blank", "noopener noreferrer nofollow")' style='font-size:14px'>¡Puntu&aacute; nuestro servicio!</span></u>
                </div>
            </div>
            <nav>
                <div class="menucont">
                    <?php
                    // verifico si hay consultas sin visualizar. En ese caso, muestro la imagen al lado del menu Consultas
                    $tieneconsultas = false;
                    if (isset($seen[0]['consultas']['seen']) && $seen[0]['consultas']['seen'] === false) {
                        $style = "style='display:inline-block'";
                        $tieneconsultas = true;
                    } else {
                        $style = "style='display:none'";
                    }
                    ?>
                    <div id="DatosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('DatosDIV', '', 'show')" onMouseOut="MM_showHideLayers('DatosDIV', '', 'hide')">
                        <li><a href="/sistema/Noticias">Noticias</a></li>
                        </ul>
                    </div>
                    <div id="ReparacionesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ReparacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ReparacionesDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Reparaciones">Listar</a></li>
                            <li><a href="/sistema/Reparaciones/anuladas">Anuladas</a></li>
                            <li><a href="/sistema/Reparaciones/finalizadas">Finalizadas</a></li>
                            <li><a href="/sistema/Reparacionessupervisores">Supervisores</a></li>
                        </ul>
                    </div>
                    <div id="GestionesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('GestionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('GestionesDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Llaves">Llaves</a></li>
                            <?php
                            if ($_SESSION['Auth']['User']['Client']['amenities']) {
                                ?>
                                <li><a href="/sistema/Amenities">Amenities</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="links2" id="Reparaciones" onMouseOver="setLyr(this, 'ReparacionesDIV');MM_showHideLayers('ReparacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ReparacionesDIV', '', 'hide')"><a href="#">Reparaciones</a></div>
                    <div class="links2" id="Gestiones" onMouseOver="setLyr(this, 'GestionesDIV');MM_showHideLayers('GestionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('GestionesDIV', '', 'hide')"><a href="#">Gestiones</a></div>
                    <div class="links2" id="Salir"><a href="/sistema/Users/logout">Salir</a></div>
                </div>
            </nav>
            <?php
            echo $this->Flash->render('otro');
            echo $this->Flash->render();
            // muestro la ayuda
            if (!empty($help)) {
                ?>
                <div id="helpdiv">
                    <span class="tooltipx"><?php echo $this->Html->image('help.png', ['width' => '24px', 'height' => '24px', 'id' => 'helpimg']); ?>
                        <span id="helpcontent"><?php echo $help["Help"]["content"]; ?>
                            <span style="color:green;font-style:italic;"><?= __('&Uacute;ltima modificaci&oacute;n: ') . $this->Time->format(__('d/m/Y'), $help["Help"]["modified"]) ?></span>
                            <?php
                            if (isset($_SESSION['Auth']['User']['is_admin']) && $_SESSION['Auth']['User']['is_admin']) {
                                echo $this->Html->image('edit.png', ['width' => '16px', 'height' => '16px', 'alt' => __('Editar'), 'title' => __('Editar'), 'url' => ['controller' => 'panel/Helps', 'action' => 'edit', $help["Help"]['id']]]);
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
        <script>
            //$.fn.editable.defaults.mode = 'inline';
            $(document).ready(function () {
                $('.editable').on('shown', function (e, editable) {
                    editable.input.postrender = function () {
                        editable.input.$input.select();
                    };
                });
<?php
if ($tieneconsultas) {// si tiene consultas, reproduzco el sonido
    ?>
                    //document.getElementById('a1').play();
    <?php
}
?>
            });

<?php
//esto sirve para q me deje hacer focus en los inputs del ckeditor cuando el ckeditor lo abro en una ventana modal del jquery.dialog (sino no me deja, por ejemplo, crear tablas en el ckeditor y cambiar la cant de cols)
// se entendio?? jaja q quilombooo
?>
            $.widget("ui.dialog", $.ui.dialog, {
                _allowInteraction: function (event) {
                    return !!$(event.target).closest(".cke_dialog").length || this._super(event);
                }
            });
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function () {
                var cad = window.location.href;
                if (cad.indexOf("#") === -1) {
                    alert("Se recomienda no utilizar el boton 'Atrás' del navegador. Por favor, utilice el menu correspondiente")
                    window.history.pushState(null, "", window.location.href);
                }
            };
            $(document).tooltip({
                position: {
                    my: "center bottom-25",
                    at: "center top",
                },
                items: "input[title],p[title],span[title],td[title],img[title],a[title],li[title]"
            });
        </script>
        <audio id="a1" style="display:none;">
            <source src="<?= $this->webroot ?>img/msg.ogg" type="audio/ogg">
        </audio>
        <style>
            .ui-datepicker-year{
                color:#2e6e9e !important;<?php /* Es el color del Año en el Datepicker, sino aparece en blanco */ ?>
            }
        </style>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-84605171-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'UA-84605171-1');
        </script>
    </body>
</html>
<!-- Sistema Web Desarrollado por estebancano.com --> 
