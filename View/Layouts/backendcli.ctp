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
                    /* $menu->setMainLink(__('Datos'));
                      $menu->setSubLink(__('Datos'), __('Mis datos'), $this->webroot . 'clients');
                      $menu->setSubLink(__('Datos'), __('Noticias'), $this->webroot . 'noticias');
                      $menu->setSubLink(__('Datos'), __('Usuarios'), $this->webroot . 'users');
                      $menu->setSubLink(__('Datos'), __('Configurar reportes'), $this->webroot . 'reportsclients');
                      $menu->setSubLink(__('Datos'), __('Consorcios'), $this->webroot . 'consorcios');
                      $menu->setSubLink(__('Datos'), __('Propietarios'), $this->webroot . 'propietarios');
                      $menu->setSubLink(__('Datos'), __('Coeficientes'), $this->webroot . 'coeficientes');
                      $menu->setSubLink(__('Datos'), __('Coeficientes propietarios'), $this->webroot . 'coeficientes_propietarios');
                      $menu->setSubLink(__('Datos'), __('Consultas CEONLINE'), $this->webroot . 'consultas');
                      $menu->setSubLink(__('Datos'), __('Consultas propietarios'), $this->webroot . 'consultaspropietarios');
                      $menu->setSubLink(__('Datos'), __('Cola impresiones'), $this->webroot . 'colaimpresiones');
                      $menu->setSubLink(__('Datos'), __('Enviar email'), $this->webroot . 'emails');
                      $menu->setSubLink(__('Datos'), __('Cartas precios'), $this->webroot . 'cartasprecios');
                      $menu->setMainLink(__('Liquidaciones'));
                      $menu->setSubLink(__('Liquidaciones'), __('Tipos de liquidaciones'), $this->webroot . 'liquidations_types');
                      $menu->setSubLink(__('Liquidaciones'), __('Saldos Iniciales Propietarios'), $this->webroot . 'saldos_iniciales');
                      $menu->setSubLink(__('Liquidaciones'), __('Saldos Iniciales Consorcios'), $this->webroot . 'saldos_iniciales_consorcios');
                      $menu->setSubLink(__('Liquidaciones'), __('Liquidaciones'), $this->webroot . 'liquidations');
                      $menu->setSubLink(__('Liquidaciones'), __('Presupuestos'), $this->webroot . 'liquidationspresupuestos');
                      $menu->setSubLink(__('Liquidaciones'), __('Saldos Cierres'), $this->webroot . 'saldos_cierres');
                      $menu->setSubLink(__('Liquidaciones'), __('Notas'), $this->webroot . 'notas');
                      $menu->setSubLink(__('Liquidaciones'), __('Adjuntos'), $this->webroot . 'adjuntos');
                      $menu->setSubLink(__('Liquidaciones'), __('Avisos'), $this->webroot . 'avisos');
                      $menu->setSubLink(__('Liquidaciones'), __('Lista negra'), $this->webroot . 'avisosblacklists');
                      $menu->setMainLink(__('Gastos'));
                      $menu->setSubLink(__('Gastos'), __('Rubros Gastos Generales'), $this->webroot . 'rubros');
                      $menu->setSubLink(__('Gastos'), __('Cuentas Gastos Particulares'), $this->webroot . 'cuentasgastosparticulares');
                      $menu->setSubLink(__('Gastos'), __('Distribuciones'), $this->webroot . 'gastos_distribuciones');
                      $menu->setSubLink(__('Gastos'), __('Gastos Generales'), $this->webroot . 'gastos_generales/add');
                      $menu->setSubLink(__('Gastos'), __('Listar Gastos Generales'), $this->webroot . 'gastos_generales/index');
                      $menu->setSubLink(__('Gastos'), __('Gastos Particulares'), $this->webroot . 'gastos_particulares');
                      //$menu->setSubLink(__('Gastos'), __('Gastos por cartas'), $this->webroot . 'gastos_particulares/gastoporcarta');
                      $menu->setMainLink(__('Cobranzas'));
                      $menu->setSubLink(__('Cobranzas'), __('Listar'), $this->webroot . 'cobranzas');
                      $menu->setSubLink(__('Cobranzas'), __('Autom&aacute;ticas'), $this->webroot . 'cobranzas/add');
                      $menu->setSubLink(__('Cobranzas'), __('Manuales'), $this->webroot . 'cobranzas/add2');
                      $menu->setSubLink(__('Cobranzas'), __('Per&iacute;odo'), $this->webroot . 'cobranzas/periodo');
                      $menu->setSubLink(__('Cobranzas'), __('Ajustes'), $this->webroot . 'ajustes');
                      $menu->setSubLink(__('Cobranzas'), __('Pago Fuera T&eacute;rmino'), $this->webroot . 'pft');
                      $menu->setSubLink(__('Cobranzas'), __('Formas de pago'), $this->webroot . 'formasdepagos');
                      $menu->setSubLink(__('Cobranzas'), __('Informe pagos Propietarios'), $this->webroot . 'informepagos');
                      $menu->setMainLink(__('Bancos y cajas'));
                      $menu->setSubLink(__('Bancos y cajas'), __('Bancos'), $this->webroot . 'bancos');
                      $menu->setSubLink(__('Bancos y cajas'), __('Cajas'), $this->webroot . 'cajas');
                      $menu->setSubLink(__('Bancos y cajas'), __('Cuentas bancarias'), $this->webroot . 'bancoscuentas');
                      $menu->setSubLink(__('Bancos y cajas'), __('Dep&oacute;sitos en efectivo'), $this->webroot . 'bancosdepositosefectivos');
                      $menu->setSubLink(__('Bancos y cajas'), __('Dep&oacute;sitos de cheques'), $this->webroot . 'bancosdepositoscheques');
                      $menu->setSubLink(__('Bancos y cajas'), __('Extracciones a caja'), $this->webroot . 'bancosextracciones');
                      $menu->setSubLink(__('Bancos y cajas'), __('Transferencias entre bancos'), $this->webroot . 'bancostransferencias');
                      $menu->setSubLink(__('Bancos y cajas'), __('Cr&eacute;ditos bancarios'), $this->webroot . 'bancosdepositosefectivos/add2');
                      $menu->setSubLink(__('Bancos y cajas'), __('D&eacute;bitos bancarios'), $this->webroot . 'bancosextracciones/index2');
                      $menu->setSubLink(__('Bancos y cajas'), __('Ingresos a caja'), $this->webroot . 'cajasingresos/');
                      $menu->setSubLink(__('Bancos y cajas'), __('Egresos de caja'), $this->webroot . 'cajasegresos/');
                      $menu->setSubLink(__('Bancos y cajas'), __('Transferencias entre cajas'), $this->webroot . 'cajas/transferencias');
                      $menu->setSubLink(__('Bancos y cajas'), __('Recuperos'), $this->webroot . 'Bancoscuentas/recuperos');
                      $menu->setSubLink(__('Bancos y cajas'), __('Resumen Caja Banco'), $this->webroot . 'consorcios/resumen');
                      $menu->setMainLink(__('Cheques'));
                      $menu->setSubLink(__('Cheques'), __('Propios'), $this->webroot . 'chequespropios');
                      $menu->setSubLink(__('Cheques'), __('De Administración'), $this->webroot . 'chequespropiosadms');
                      $menu->setSubLink(__('Cheques'), __('De terceros'), $this->webroot . 'cheques');
                      $menu->setSubLink(__('Cheques'), __('Depositados'), $this->webroot . 'cheques/depositados');
                      $menu->setSubLink(__('Cheques'), __('Entregados'), $this->webroot . 'cheques/entregados');
                      $menu->setMainLink(__('Proveedores'));
                      $menu->setSubLink(__('Proveedores'), __('Proveedores'), $this->webroot . 'proveedors');
                      $menu->setSubLink(__('Proveedores'), __('Facturas'), $this->webroot . 'proveedorsfacturas');
                      $menu->setSubLink(__('Proveedores'), __('Pagos'), $this->webroot . 'proveedorspagos');
                      $menu->setMainLink(__('Reparaciones'));
                      $menu->setSubLink(__('Reparaciones'), __('Listar'), $this->webroot . 'reparaciones');
                      $menu->setSubLink(__('Reparaciones'), __('Supervisores'), $this->webroot . 'reparacionessupervisores');
                      $menu->setMainLink(__('Llaves'));
                      $menu->setSubLink(__('Llaves'), __('Listar'), $this->webroot . 'llaves');
                      //$menu->setSubLink(__('Llaves'), __('Estados'), $this->webroot . 'llavesestados');
                      $menu->setMainLink(__('Salir'), $this->webroot . 'users/logout');
                      //Generate the layers
                      //$menu->makeDivs();
                      //Generate the menu
                      //$menu->makelinks();
                      echo "<!--span>";
                      debug($menu->makeDivs() . $menu->makelinks());
                      echo "</span>--";
                      die; */
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
                        <ul>
                            <li><a href="/sistema/Clients">Mis Datos</a></li>
                            <li><a href="/sistema/Consorcios">Consorcios</a></li>
                            <li><a href="/sistema/Propietarios">&nbsp;&nbsp;&nbsp;&nbsp; Propietarios</a></li>
                            <li><a href="/sistema/Coeficientes">&nbsp;&nbsp;&nbsp;&nbsp; Coeficientes</a></li>
                            <li><a href="/sistema/Coeficientes_propietarios">&nbsp;&nbsp;&nbsp;&nbsp; Coeficientes Propietarios</a></li>
                            <li><a href="/sistema/Consorciosconfigurations">&nbsp;&nbsp;&nbsp;&nbsp; Configurar Consorcios</a></li>
                            <li><a href="/sistema/Reportsclients">&nbsp;&nbsp;&nbsp;&nbsp; Configurar Reportes</a></li>
                            <li><a href="/sistema/Comunicaciones">Comunicaciones</a></li>
                            <li><a href="/sistema/Consultas">Consultas CEONLINE&nbsp;<img src="/sistema/img/warn.png" class="seen" <?= $style ?> /></a></li>
                            <li><a href="/sistema/Consultaspropietarios">Consultas Propietarios</a></li>
                            <li><a href="/sistema/Users">Usuarios</a></li>
                            <li><a href="/sistema/Noticias">Noticias</a></li>
                            <!-- <li><a href="/sistema/Cartasprecios">Cartas Precios</a></li> -->                    
                        </ul>
                    </div>
                    <div id="LiquidacionesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('LiquidacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('LiquidacionesDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Liquidations">Abiertas / Cerradas</a></li>
                            <li><a href="/sistema/Colaimpresiones">Cola de reportes</a></li>
                            <li><a href="/sistema/Notas">Notas</a></li>
                            <li><a href="/sistema/Adjuntos">Adjuntos</a></li>
                            <li><a href="/sistema/Avisos">Avisos</a></li>
                            <li><a href="/sistema/Avisos">&nbsp;&nbsp;&nbsp;&nbsp;Env&iacute;os Email</a></li>
                            <li><a href="/sistema/Avisoswhatsapps">&nbsp;&nbsp;&nbsp;&nbsp;Historial WhatsApp</a></li>
                            <li><a href="/sistema/Avisosblacklists">&nbsp;&nbsp;&nbsp;&nbsp;Lista negra</a></li>
                            <li><a href="/sistema/Liquidations_types">Tipos De Liquidaciones</a></li>
                            <li><a href="/sistema/Saldos_iniciales">Saldos Iniciales Propietarios</a></li>
                            <li><a href="/sistema/Saldos_iniciales_consorcios">Saldos Iniciales Consorcios</a></li>
                            <li><a href="/sistema/Liquidationspresupuestos">Presupuestos</a></li>
                        </ul>
                    </div>
                    <div id="GastosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('GastosDIV', '', 'show')" onMouseOut="MM_showHideLayers('GastosDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Gastos_generales/add">Gastos Generales</a></li>
                            <li><a href="/sistema/Gastos_generales/">&nbsp;&nbsp;&nbsp;&nbsp; Listar Gastos Generales</a></li>
                            <li><a href="/sistema/Rubros">&nbsp;&nbsp;&nbsp;&nbsp; Rubros Gastos Generales</a></li>
                            <li><a href="/sistema/Gastos_distribuciones">&nbsp;&nbsp;&nbsp;&nbsp; Distribuciones</a></li>
                            <li><a href="/sistema/Gastos_particulares">Gastos Particulares</a></li>
                            <li><a href="/sistema/Cuentasgastosparticulares">&nbsp;&nbsp;&nbsp;&nbsp; Cuentas Gastos Particulares</a></li>
                            <li><a href="/sistema/Cobranzas/multas">Multas</a></li>
                            <li><a href="/sistema/Cobranzas/multassobrecapital">Multas m&aacute;s de un per&iacute;odo</a></li>
                            <!-- <li><a href="/sistema/Cartas/envios">Env&iacute;os Postales del d&iacute;a</a></li>
                            <li><a href="/sistema/Cartas">Cartas Enviadas</a></li> --> 
                        </ul>
                    </div>
                    <div id="CobranzasDIV" class="linkDIV" onMouseOver="MM_showHideLayers('CobranzasDIV', '', 'show')" onMouseOut="MM_showHideLayers('CobranzasDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Cobranzas">Listar</a></li>
                            <li><a href="/sistema/Cobranzas/add">Autom&aacute;ticas</a></li>
                            <li><a href="/sistema/Cobranzas/add2">Manuales</a></li>
                            <li><a href="/sistema/Cobranzas/periodo">Per&iacute;odo</a></li>
                            <li><a href="/sistema/Ajustes">Ajustes</a></li>
                            <li><a href="/sistema/Ajustes/periodo">&nbsp;&nbsp;&nbsp;&nbsp; Ajustes Per&iacute;odo</a></li>
                            <li><a href="/sistema/Cobranzas/pft">Pago Fuera T&eacute;rmino</a></li>
                            <li><a href="/sistema/Informepagos">Informe Pagos Propietarios</a></li>  
                            <li><a href="/sistema/Formasdepagos">Formas De Pago</a></li>
                        </ul>
                    </div>
                    <div id="BancosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('BancosDIV', '', 'show')" onMouseOut="MM_showHideLayers('BancosDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Bancosdepositosefectivos">Dep&oacute;sitos En Efectivo</a></li>
                            <li><a href="/sistema/Bancosdepositoscheques">Dep&oacute;sitos De Cheques</a></li>
                            <li><a href="/sistema/Bancosextracciones">Extracciones</a></li>
                            <li><a href="/sistema/Bancostransferencias">Transferencias Entre Bancos</a></li>
                            <li><a href="/sistema/Bancosdepositosefectivos/index2">Cr&eacute;ditos</a></li>
                            <li><a href="/sistema/Bancosextracciones/index2">D&eacute;bitos</a></li>
                            <li><a href="/sistema/Bancoscuentas/recuperos">Recuperos</a></li>
                            <li><a href="/sistema/Consorcios/resumen">Resumen Caja Banco</a></li>
                            <li><a href="/sistema/Bancos">Bancos</a></li>
                            <li><a href="/sistema/Bancoscuentas">Cuentas</a></li>
                        </ul>
                    </div>
                    <div id="CajasDIV" class="linkDIV" onMouseOver="MM_showHideLayers('CajasDIV', '', 'show')" onMouseOut="MM_showHideLayers('CajasDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Cajasingresos/">Ingresos</a></li>
                            <li><a href="/sistema/Cajasegresos/">Egresos</a></li>
                            <li><a href="/sistema/Cajas/transferencias">Transferencias Entre Cajas</a></li>
                            <li><a href="/sistema/Cajas">Cajas</a></li>
                        </ul>
                    </div>
                    <div id="ChequesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ChequesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ChequesDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Cheques">De Terceros</a></li>
                            <li><a href="/sistema/Chequespropios">Propios</a></li>
                            <li><a href="/sistema/Chequespropiosadms">De Administraci&oacute;n</a></li>
                            <li><a href="/sistema/Cheques/depositados">Depositados</a></li>
                            <li><a href="/sistema/Cheques/entregados">Entregados</a></li>      
                        </ul>
                    </div>
                    <div id="ProveedoresDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ProveedoresDIV', '', 'show')" onMouseOut="MM_showHideLayers('ProveedoresDIV', '', 'hide')">
                        <ul>
                            <li><a href="<?= $this->webroot ?>proveedorsfacturas">Facturas</a></li>
                            <li><a href="/sistema/Proveedorspagos">Pagos</a></li>
                            <li><a href="<?= $this->webroot ?>proveedorspagosacuentas">Pagos a cuenta</a></li> 
                            <li><a href="/sistema/Proveedors">Proveedores</a></li>
                        </ul>
                    </div>
                    <div id="ReparacionesDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ReparacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ReparacionesDIV', '', 'hide')">
                        <ul>
                            <li><a href="/sistema/Reparaciones">Listar</a></li>
                            <li><a href="/sistema/Reparaciones/anuladas">Anuladas</a></li>
                            <!--li><a href="/sistema/Reparaciones/finalizadas">Finalizadas</a></li-->
                            <li><a href="/sistema/Reparacionessupervisores">Supervisores</a></li>
                            <li><a href="/sistema/Reparacionesestados">Estados</a></li>
                        </ul>
                    </div>
                    <?php
                    if ($_SESSION['Auth']['User']['client_id'] == 65) {
                        ?>
                        <div id="SueldosDIV" class="linkDIV" onMouseOver="MM_showHideLayers('SueldosDIV', '', 'show')" onMouseOut="MM_showHideLayers('SueldosDIV', '', 'hide')">
                            <ul>
                                <!--  <li><a href="/sistema/Sueldos">Listar</a></li>
                                  <li><a href="/sistema/Datos">Datos</a></li>  --> 
                                <li><a href="/sistema/Sueldosempresas">Empresas</a></li>
                                <li><a href="/sistema/Sueldosareas">Areas</a></li>
                                <li><a href="/sistema/Sueldossubareas">Subareas</a></li>
                                <li><a href="/sistema/Sueldoslegajos">Legajos</a></li>
                                <li><a href="/sistema/Sueldosafips">Afip</a></li>
                                <li><a href="/sistema/Sueldosobrassociales">Obras Sociales</a></li>                            
                                <li><a href="/sistema/Sueldossindicatos">Sindicatos</a></li>
                                <li><a href="/sistema/Sueldosccts">Covenios</a></li>
                                <li><a href="/sistema/Sueldosescalassalariales">Escalas Salariales</a></li>
                                <li><a href="/sistema/Sueldoscategorias">Categor&iacute;as</a></li>
                                <li><a href="/sistema/Sueldoscargos">Cargos</a></li>
                                <li><a href="/sistema/Sueldosconceptos">Conceptos</a></li>
                                <li><a href="/sistema/Sueldosformulas">Formulas</a></li>
                                <li><a href="/sistema/Sueldosliquidations">Liquidaciones</a></li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
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
                    <div class="links2" id="Datos" onMouseOver="setLyr(this, 'DatosDIV');MM_showHideLayers('DatosDIV', '', 'show')" onMouseOut="MM_showHideLayers('DatosDIV', '', 'hide')"><a href="#">Datos&nbsp;<img src="<?= $this->webroot ?>img/warn.png" class="seen" <?= $style ?> /></a></div>
                    <div class="links2" id="Liquidaciones" onMouseOver="setLyr(this, 'LiquidacionesDIV');MM_showHideLayers('LiquidacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('LiquidacionesDIV', '', 'hide')"><a href="#">Liquidaciones</a></div>
                    <div class="links2" id="Gastos" onMouseOver="setLyr(this, 'GastosDIV');MM_showHideLayers('GastosDIV', '', 'show')" onMouseOut="MM_showHideLayers('GastosDIV', '', 'hide')"><a href="#">Gastos</a></div>
                    <div class="links2" id="Cobranzas" onMouseOver="setLyr(this, 'CobranzasDIV');MM_showHideLayers('CobranzasDIV', '', 'show')" onMouseOut="MM_showHideLayers('CobranzasDIV', '', 'hide')"><a href="#">Cobranzas</a></div>
                    <div class="links2" id="Bancos" onMouseOver="setLyr(this, 'BancosDIV');MM_showHideLayers('BancosDIV', '', 'show')" onMouseOut="MM_showHideLayers('BancosDIV', '', 'hide')"><a href="#">Bancos</a></div>
                    <div class="links2" id="Cajas" onMouseOver="setLyr(this, 'CajasDIV');MM_showHideLayers('CajasDIV', '', 'show')" onMouseOut="MM_showHideLayers('CajasDIV', '', 'hide')"><a href="#">Cajas</a></div>
                    <div class="links2" id="Cheques" onMouseOver="setLyr(this, 'ChequesDIV');MM_showHideLayers('ChequesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ChequesDIV', '', 'hide')"><a href="#">Cheques</a></div>
                    <div class="links2" id="Proveedores" onMouseOver="setLyr(this, 'ProveedoresDIV');MM_showHideLayers('ProveedoresDIV', '', 'show')" onMouseOut="MM_showHideLayers('ProveedoresDIV', '', 'hide')"><a href="#">Proveedores</a></div>
                    <div class="links2" id="Reparaciones" onMouseOver="setLyr(this, 'ReparacionesDIV');MM_showHideLayers('ReparacionesDIV', '', 'show')" onMouseOut="MM_showHideLayers('ReparacionesDIV', '', 'hide')"><a href="#">Reparaciones</a></div>
                    <?php
                    //65=prueba, 85=demo, 82=vf, 105=bedoya
                    if ($_SESSION['Auth']['User']['client_id'] == 65) {
                        ?>
                        <div class="links2" id="Sueldos" onMouseOver="setLyr(this, 'SueldosDIV');MM_showHideLayers('SueldosDIV', '', 'show')" onMouseOut="MM_showHideLayers('SueldosDIV', '', 'hide')"><a href="#">Sueldos</a></div>
                        <?php
                    }
                    if ($_SESSION['Auth']['User']['client_id'] == 82 || $_SESSION['Auth']['User']['client_id'] == 85 || $_SESSION['Auth']['User']['client_id'] == 65 || $_SESSION['Auth']['User']['client_id'] == 116) {
                        ?>
                        <div id="ContabilidadDIV" class="linkDIV" onMouseOver="MM_showHideLayers('ContabilidadDIV', '', 'show')" onMouseOut="MM_showHideLayers('ContabilidadDIV', '', 'hide')">
                            <ul>
                                <li><a href="/sistema/contasientos/config">Configuraci&oacute;n</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/contejercicios">Ejercicios</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/conttitulos">T&iacute;tulos</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/contcuentas">Cuentas</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/contasientos/add">Asientos</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/contasientos/automaticos">Generar asientos</a></li>
                            </ul>
                            <ul>
                                <li><a href="/sistema/contasientos/balance">Balance</a></li>
                            </ul>
                            <!--ul>
                                <li><a href="/sistema/contcuentas/mayor">Mayor</a></li>
                            </ul--> 
                        </div>
                        <div class="links2" id="Contabilidad" onMouseOver="setLyr(this, 'ContabilidadDIV');MM_showHideLayers('ContabilidadDIV', '', 'show')" onMouseOut="MM_showHideLayers('ContabilidadDIV', '', 'hide')"><a href="#">Contabilidad</a></div>
                        <?php
                    }
                    ?>
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
