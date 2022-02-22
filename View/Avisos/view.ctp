<style>
    #pri li ul li{
        display:inline;
    }
    .cont{
        width:auto;min-width:350px;max-width:600px;border:2px solid gray;padding:5px;margin-top:5px;
        border-radius:21px;
        -moz-border-radius:4px;
        -webkit-border-radius:4px;
        -moz-box-shadow:5px 5px 8px #CCC;
        -webkit-box-shadow:5px 5px 8px #CCC;
        box-shadow:5px 5px 8px #CCC;
        display:none;
    }
    .iom{
        cursor:pointer;
        font-size:13px;
        margin-right:15px;
    }
    .iom:hover{
        text-decoration:underline;
    }
    .ui-accordion-header{
        font-size:11px !important;
        height:25px;
    }
    #fondogris{
        margin:0px;
    }
    #centro{
        padding:0px;
        height:auto;
    }
    .nowrap{
        white-space:nowrap;
    }
    .noTitleStuff .ui-dialog-titlebar {display:none}<?php /* en amenities, no muestra la barra de titulo */ ?>
</style>
<div id="fondogris">
    <div id="loading" style="text-align:center"><?= $this->Html->image('loading.gif', ['width' => '48px', 'height' => '48px']) ?></div>
    <?php
    $url = $_SERVER["REMOTE_ADDR"] == '::1' || substr($_SERVER['REMOTE_ADDR'], 0, 10) == '192.168.0.' ? "/sistema/" : "https://ceonline.com.ar/p/?";
    foreach ($datos['datos'] as $k => $consorcio) {
        if (empty($consorcio)) {
            continue;
        }
        $data = json_decode($consorcio[0]['Resumene']['data'], true);
        if (isset($consorcio[0]['Client']['code']) && in_array($consorcio[0]['Client']['code'], ['1119'])) {
            // es un cliente externo, busco archivos asociados al propietario
            $cliente = $consorcio[0]['Consorcio']['client_id'];
            echo "<div class='cont'>";
            echo "<span class='iom' style='font-size:13px'><b>" . h($consorcio[0]['Client']['name'] . " - " . $consorcio[0]['Consorcio']['name']);
            echo " - " . h($data['prop'][$k]['unidad'] . " (" . $data['prop'][$k]['code'] . ")") . "</b></span>";

            $pattern = str_pad($consorcio[0]['Client']['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio[0]['Consorcio']['code'], 4, "0", STR_PAD_LEFT) . str_pad($data['prop'][$k]['code'], 4, "0", STR_PAD_LEFT) . ".*\.*";
            $archivos = getFiles(APP . WEBROOT_DIR . DS . 'files' . DS . $consorcio[0]['Client']['id'] . DS . 'consultas' . DS, basename($pattern), APP . WEBROOT_DIR . DS . 'files' . DS . $consorcio[0]['Client']['id'] . DS . 'consultas' . DS);
            if ($archivos !== []) {
                echo showFiles($archivos, $consorcio[0]['Client']['id'], $id, 0);
            } else {
                echo "<p class='info' style='width:400px'>No se encuentran archivos disponibles para esta unidad</p>";
            }
            if ($consorcio[0]['Client']['informepagospropietarios'] || $consorcio[0]['Client']['consultaspropietarios'] || $consorcio[0]['Client']['amenities']) {
                echo "<hr style='border:1px solid gray;margin:0 0 2px 0'>";
            }
            // agrego el informe de pagos para los propietarios
            $parametros = ['link' => $this->request->params['pass'][0], 'cl' => $cliente, 'pid' => $k];
            if ($consorcio[0]['Client']['informepagospropietarios']) {
                $titulo = "<span class='nowrap'><b>[ Informar un Pago ]</b></span>";
                echo "<span class='iom' onclick='$(\"#pago$k\").dialog(\"open\");$(\".ui-dialog-title\").html(\"Informar un Pago\");act$k();'>$titulo</span><div id='pago$k' style='display:none;margin:0 auto'>";
                echo $this->element('propietarioinformepago', $parametros + ['formasdepago' => $datos['formasdepago'][$cliente]]) . "</div>";
                scriptDialogInformePagoPropietarios($k);
            }
            // agrego el chat con el administrador de la liquidaciona actual (despues de la ultima liquidacion del cliente)
            if ($consorcio[0]['Client']['consultaspropietarios']) {
                $titulo = "<span class='nowrap'><b>[ Enviar mensaje ]</b></span>";
                echo "<span class='iom' onclick='$(\"#chat$k\").dialog(\"open\");$(\".ui-dialog-title\").html(\"Enviar mensaje\");$(\"#actualizar$k\").click();'>$titulo</span><div id='chat$k' style='display:none;margin:0 auto'>";
                echo $this->element('propietarioconsultas', $parametros + ['consultas' => $datos['consultas'][$k]['c'], 'consultasadjuntos' => $datos['consultas'][$k]['a']]) . "</div>";
                scriptDialogConsultasPropietarios($k);
            }
            // agrego la gestion de amenities
            if ($consorcio[0]['Client']['amenities'] && isset($datos['amenities']) && !empty($datos['amenities'])) {
                foreach ($datos['amenities'][$k] as $oo => $pp) {
                    echo "<span class='nowrap iom' onclick='getAmenity(\"" . h($this->request->params['pass'][0]) . "\",$k,\"" . $pp['Amenity']['id'] . "\")'><b>[ Reservar " . h($pp['Amenity']['nombre']) . " ]</b></span>";
                }
            }
            echo "</div>";
        } else {
            if (count($consorcio) == 0) {
                echo "<h3>-- No se encontraron liquidaciones asociadas --</h3>";
                break;
            }
            ?>
            <br>
            <?php
            echo "<div class='cont'>";
            echo "<span class='iom' style='font-size:15px'><b>" . h($consorcio[0]['Client']['name'] . " - " . $consorcio[0]['Consorcio']['name']);
            echo " - " . h($data['prop'][$k]['unidad'] . " (" . $data['prop'][$k]['code'] . ")") . "</b></span>";

            echo "<ul id='pri' class='accordion' style='padding-left:15px'>";
            foreach ($consorcio as $p => $liquidacion) {
                // implementacion columna online en cola impresiones
                // si no esta tildado ONLINE el resumen de cuentas es que no va online la liquidacion actual. Sigo con la siguiente. Como minimo siempre se tilda el resumen de cuentas (los otros son opcionales)
                // HAY casos q solo envian la composicion de saldos
                if (!isset($datos['encola'][$liquidacion['Consorcio']['id']]['resumenesdecuentas'][$liquidacion['Liquidation']['id']]) &&
                        !isset($datos['encola'][$liquidacion['Consorcio']['id']]['resumengastos'][$liquidacion['Liquidation']['id']]) &&
                        !isset($datos['encola'][$liquidacion['Consorcio']['id']]['composicionsaldos'][$liquidacion['Liquidation']['id']])) {
                    continue;
                }
                $enproceso = true;
                if (!in_array($consorcio[0]['Client']['code'], ['1119'])) { // no muestro esto a los clientes externos, ej: VF (no empecemos con los cableadossssssssss jaja)
                    echo "<li><b>" . h($liquidacion['Liquidation']['periodo']) . "</b><ul>";
                    echo "<li><b>" . __("REPORTES") . "</b><ul>";
                    $reportes = "";
                    if (isset($datos['encola'][$liquidacion['Consorcio']['id']]['resumenesdecuentas'][$liquidacion['Liquidation']['id']])) {//////////////////////////////////////$k=propid,$id=link,$client_id
                        $reportes .= "<li><a href='$url" . "Reports/resumencuenta/" . $liquidacion['Liquidation']['id'] . "/$k/$id/" . $liquidacion['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Resumen de cuenta</a></li> | ";
                        $enproceso = false;
                    }
                    if (isset($datos['encola'][$liquidacion['Consorcio']['id']]['resumengastos'][$liquidacion['Liquidation']['id']])) {
                        $reportes .= "<li><a href='$url" . "Reports/resumengastos/" . $liquidacion['Liquidation']['id'] . "/$k/$id/" . $liquidacion['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Resumen de gastos</a></li> | ";
                        $enproceso = false;
                    }

                    if (isset($datos['encola'][$liquidacion['Consorcio']['id']]['composicionsaldos'][$liquidacion['Liquidation']['id']])) {
                        $reportes .= "<li><a href='$url" . "Reports/composicionsaldos/" . $liquidacion['Liquidation']['id'] . "/$k/$id/" . $liquidacion['Client']['id'] . "' target='_blank' rel='nofollow noopener noreferrer'>Composici&oacute;n de saldos</a></li> | ";
                        $enproceso = false;
                    }
                    echo substr($reportes, 0, -2) . "</ul></li>";

                    // agrego los recibos de las cobranzas al panel
                    foreach ($datos['datos'][$k] as $abc => $abc2) {
                        if ($abc2['Liquidation']['id'] == $liquidacion['Liquidation']['id']) {
                            $data = json_decode($consorcio[$abc]['Resumene']['data'], true);
                            break;
                        }
                    }
                    $keys = $this->Functions->find($data['cobranzas'], ['propietario_id' => $k], true);
                    if (!empty($keys)) {
                        echo "<li><b>" . __("RECIBOS") . "</b><ul>";
                        $recibos = "";
                        foreach ($keys as $kkv) {
                            $recibos .= "<a href='$url" . "Cobranzas/ver/" . $this->Functions->_encryptURL($liquidacion['Liquidation']['id'] . "#$k#$id#" . $data['cobranzas'][$kkv]['Cobranza']['id']) . "' target='_blank' rel='nofollow noopener noreferrer'>" . h($this->Time->format(__('d/m/Y'), $data['cobranzas'][$kkv]['Cobranza']['fecha'])) . "</a> - ";
                        }
                        echo substr($recibos, 0, -2) . "</ul></li>";
                    }
                } else {
                    $enproceso = false; // es vf
                }

                if (!$enproceso && isset($liquidacion['Adjunto']) && count($liquidacion['Adjunto']) > 0) {
                    echo "<li><b>" . __("ADJUNTOS") . "</b><ul>";
                    $adj = "";
                    foreach ($liquidacion['Adjunto'] as $adjunto) {
                        if (!$adjunto['online']) { // muestro solo los adjuntos q tienen el tilde online
                            continue;
                        }
                        $adj .= $this->Html->link(h($adjunto['titulo']), array('controller' => 'Adjuntos', 'action' => 'download', $this->Functions->_encryptURL($adjunto['ruta']), $k, $id, $liquidacion['Consorcio']['client_id']), ['target' => '_blank', 'rel' => 'nofollow noopener noreferrer']) . " | ";
                    }
                    echo substr($adj, 0, -2) . "</ul></li>";
                }
                if ($enproceso) {
                    echo "<p class='info' style='width:500px'>La administraci&oacute;n se encuentra actualmente procesando esta liquidaci&oacute;n</p>";
                }
                echo "</ul></li>";
            }
            echo "</ul>";

            // muestro las reparaciones de este propietario (si tiene alguna)
            $rep = "";
            if ($consorcio[0]['Client']['reparacionpropietariosonline']) {
                if (!empty($datos['reparaciones'])) {
                    $rep = "";
                    foreach ($datos['reparaciones'] as $f => $v) {
                        if ($v['Propietario']['id'] == $k || (is_null($v['Propietario']['id']) && $v['Consorcio']['id'] == $consorcio[0]['Consorcio']['id'])) {// muestro las reparaciones del propietario actual
                            $rep .= "<h3 style='text-align:left;font-size:11px'>" . h($this->Time->format(__('d/m/Y'), $v['Reparacione']['created'])) . " - ";
                            $rep .= h((!empty($v['Propietario']['unidad']) ? $v['Propietario']['unidad'] . " - " : '') . (isset(end($v['Reparacionesactualizacione'])['concepto']) ? end($v['Reparacionesactualizacione'])['concepto'] : '') . " (" . $v['Reparacionesestado']['nombre']) . ")</h3>";
                            $rep .= "<div>";

                            foreach ($v['Reparacionesactualizacione'] as $ra) {
                                $rep .= $this->Time->format(__('d/m/Y'), $ra['fecha']) . " - " . h($ra['concepto']) . "<br>";
                            }
                            $rep .= "<p style='color:green;font-style:italic;'>&Uacute;ltima actualizaci&oacute;n: " . $this->Time->format(__('d/m/Y H:i:s'), $v['Reparacione']['modified']) . "</p></div>";
                        }
                    }
                    if (!empty($rep)) {
                        echo "<br><h1 style='font-size:15px;font-weight:bold'>Reparaciones ";
                        echo '<a id="ver_ocultar" href="#" style="font-size:14px;" onclick=\'$("#rep' . $k . '").slideToggle();return false\'>[ Ver ]</a>';
                        echo "</h1><ul class='accordion' id='rep$k' style='padding-left:15px;display:none'>$rep</ul>";
                    }
                }
            }

            if ($consorcio[0]['Client']['informepagospropietarios'] || $consorcio[0]['Client']['consultaspropietarios'] || $consorcio[0]['Client']['amenities']) {
                echo "<hr style='border:1px solid gray;margin:0 0 2px 0'>";
            }
            // agrego el informe de pagos para los propietarios
            $cliente = $consorcio[0]['Consorcio']['client_id'];
            $parametros = ['link' => $this->request->params['pass'][0], 'cl' => $cliente, 'consultas' => $datos['consultas'][$k]['c'], 'consultasadjuntos' => $datos['consultas'][$k]['a'], 'pid' => $k, 'formasdepago' => $datos['formasdepago'][$cliente]];
            if ($consorcio[0]['Client']['informepagospropietarios']) {
                $titulo = "<span class='nowrap'><b>[ Informar un Pago ]</b></span>";
                echo "<span class='iom' onclick='$(\"#pago$k\").dialog(\"open\");$(\".ui-dialog-title\").html(\"Informar un pago\");act$k();'>$titulo</span><div id='pago$k' style='display:none;margin:0 auto'>";
                echo $this->element('propietarioinformepago', $parametros) . "</div>";
                scriptDialogInformePagoPropietarios($k);
            }

            // agrego el chat con el administrador de la liquidaciona actual (despues de la ultima liquidacion del cliente)
            if ($consorcio[0]['Client']['consultaspropietarios']) {
                $titulo = "<span class='nowrap'><b>[ Enviar mensaje ]</b></span>";
                echo "<span class='iom' onclick='$(\"#chat$k\").dialog(\"open\");$(\".ui-dialog-title\").html(\"Enviar mensaje\");$(\"#actualizar$k\").click();'>$titulo</span><div id='chat$k' style='display:none;margin:0 auto'>";
                echo $this->element('propietarioconsultas', $parametros) . "</div>";
                scriptDialogConsultasPropietarios($k);
            }

            if ($consorcio[0]['Client']['amenities'] && isset($datos['amenities']) && !empty($datos['amenities'])) {
                foreach ($datos['amenities'][$k] as $oo => $pp) {
                    echo "<span class='nowrap iom' onclick='getAmenity(\"" . h($this->request->params['pass'][0]) . "\",$k,\"" . $pp['Amenity']['id'] . "\")'><b>[ Reservar " . h($pp['Amenity']['nombre']) . " ]</b></span>";
                }
            }
            echo "</div>"; //class cont de clientes normales
        }
        if (count($datos['datos']) == 0) {
            echo "<h3>-- No se encontraron liquidaciones asociadas --</h3>";
        }
    }
    ?>
</div>
<script>
    var propx = "";
    $("#accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        active: false
    });
    $(".accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        active: false
    });
    $(function () {
        $("#loading").hide();
        $(".cont").slideToggle('fast');
        $(".dp").datepicker({dateFormat: 'dd/mm/yy', maxDate: '0', changeYear: true, yearRange: '2016:+1'});
        var dialog3 = $("#amenities").dialog({
            autoOpen: false, height: "auto", width: "95%", maxWidth: "590",
            position: {at: "top top"},
            modal: true, buttons: {
                Cancelar: function () {
                    dialog3.dialog("close");
                }
            }
        });
    });
    function getAmenity(link, pid, id) {
        $("#amenities").dialog("open");
        $("#amenities").html("<div class='info' style='width:200px;margin:0 auto'>Cargando...<img src='/sistema/img/loading.gif'/></div>");
        $("#amenities").load("<?= $this->webroot ?>Amenities/propietarioreservaamenities/" + link + "/" + pid + "/" + id);
    }
</script>
<?= "<div id='amenities' style='display:none;margin:0 auto;background:#fff;z-index:1000000'></div>"; ?>
<?php
/*
 * Obtiene todos los archivos del directorio $path y sus subdirectorios que cumplan con el pattern $pattern
 */

function getFiles($path, $pattern, $root) {
    $resul = [];
    $dir = new Folder($path);
    $carpetas = $dir->read()[0];
    if ($carpetas !== []) {
        foreach ($carpetas as $c) {
            if (!isset($resul[$c])) {
                $resul[$c] = [];
            }
            $resul[$c] += getFiles($path . DS . $c, $pattern, $root);
        }
    }
    $c = basename($path); // el nombre del directorio actual
    $files = array_merge($dir->find($pattern), $dir->find(substr($pattern, 0, -9) . "0000.*\.*"), $dir->find(substr($pattern, 0, 4) . "00000000.*\.*"));
    if ($files !== []) {
        foreach ($files as $f) {
            $resul[] = substr($path, strlen(substr($root, strpos($path, $root)))) . "/" . $f;
        }
    }
    return $resul;
}

/*
 * Muestra los archivos encontrados con getFiles()
 */

function showFiles($dir, $pid, $l, $nivel) {
    $nivel++;
    if ($nivel == 1) {
        $arch = "<ul class='accordion' style='margin-left:25px'>";
    } else {
        $arch = "<ul>";
    }
    foreach ($dir as $k => $c) {
        if (!is_int($k)) {
            $arch .= "<b>$k</b>";
            $arch .= showFiles($dir[$k], $pid, $l, $nivel);
        } else {
            // es un archivo, lo muestro
            $url = _encryptURL(str_replace('\\', '/', $dir[$k]));
            if (!is_null($url)) {
                $arch .= "<a href='https://ceonline.com.ar/sistema/Adjuntos/d/" . urlencode($url) . "/$pid/$l' target='_blank' rel='nofollow noopener noreferrer'>" . h(ucwords(substr(basename($c), 12))) . "</a>" . "&nbsp;&nbsp;|&nbsp;&nbsp;";
            }
        }
    }
    $arch .= "</ul>";
    return $arch;
}

function _encryptURL($textoplano) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $textocifrado = strtr(base64_encode($iv . openssl_encrypt($textoplano, 'AES-256-CBC', Configure::read('Security.key'), OPENSSL_RAW_DATA, $iv)), '+/=', '-_,');
    if (strlen($textocifrado) > 0) {
        return $textocifrado;
    }
    return null; // error
}

function scriptDialogInformePagoPropietarios($k) {
    ?>
    <script>
        $(function () {
            var dialog2 = $("#pago<?= $k ?>").dialog({
                autoOpen: false, height: "auto", width: "95%", maxWidth: "590",
                position: {at: "center center"},
                modal: true, buttons: {
                    Cancelar: function () {
                        dialog2.dialog("close");
                    }
                }
            });
        });
    </script>
    <?php
}

function scriptDialogConsultasPropietarios($k) {
    ?>
    <script>
        $(function () {
            var dialog = $("#chat<?= $k ?>").dialog({
                autoOpen: false, height: "auto", width: "95%", maxWidth: "590",
                position: {at: "center center"},
                modal: true, buttons: {
                    Cancelar: function () {
                        dialog.dialog("close");
                    }
                }
            });
        });
    </script>
    <?php
}
