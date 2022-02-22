<!DOCTYPE html>
<html lang="es-419">
    <head>
        <title>Cup&oacute;n de pago</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <?= $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']); ?>
        <style type="text/css">
            @font-face {
                font-family: '2of5';
                src: url('/sistema/fonts/2of5.woff');
            }
            .barcode1{
                font-family: '2of5';
                font-size:30.6pt;
            }
            .box-table-a,.box-table-b{
                font-family: "Lucida Sans Unicode, Lucida Grande, Sans-Serif";
                font-size: 11px;
                text-align: center;
                border-collapse: collapse;
                border: 2px solid #9baff1;
                line-height:9px;
            }
            .box-table-a th,.box-table-b th{
                font-size: 11px;
                font-weight: normal;
                padding: 8px;
                background: none;
                border-right: 2px solid #9baff1;
                border-left: 2px solid #9baff1;
                color: #039;
            }
            .box-table-a td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #aabcfe;
                color: #000;
            }
            .box-table-b td{
                padding: 4px;
                background: none; 
                border-left: 2px solid #aabcfe;
                border-bottom: 1px solid #aabcfe;
                color: #000;
            }
            .tdleft{
                padding: 4px;
                background: none; 
                border:none;
                color: #000;
            }
            .right{
                text-align:right;
                min-width:100px;
            }
            .pri{
                text-align:left !important;
                width:130px;
                border:none !important;
            }
            .totales{
                border: 2px solid #9baff1;
                font-weight: 700;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <?php
//$client
//    'code' => '6003',
//    'name' => 'Admin CASCO',
//    'cuit' => '20-31224654-7',
//    'address' => 'Buenos Aires 2850',
//    'city' => 'Mar del Plata',
//    'telephone' => '45874587',
//    'email' => 'ricardo@ceonline.com.ar',
//    'usa_plapsa' => true,
//    'comision' => 2.8
//    
        $propietario = filter_var($propietario_id, FILTER_VALIDATE_INT);
        if (!is_int($propietario)) {
            die("Propietario invalido");
        }
        $data['plataforma'] = $plataforma;
        $data['plataformas'] = $plataformas;
        $client = $cliente['Client'];
        /*
         * Para multiplataforma, si tiene configurada alguna, la seteo en usa_plapsa y en $client el detalle, asi no tengo q hacer tanto cambio
         * en $plataforma tengo la plataforma del cliente (reportescomponent)
         * en $plataformas tengo las plataformas disponibles (reportescomponent)
         * en $data['plataforma']['Plataformasdepagosconfig'], la info de la plataforma para las liquidaciones prorrateadas luego de la implementacion de la multiplataforma
         */
        $client['usa_plapsa'] = isset($data['plataforma']['Plataformasdepagosconfig']['plataformasdepago_id']) ? $data['plataforma']['Plataformasdepagosconfig']['plataformasdepago_id'] : $client['usa_plapsa']; // si es un resumen viejo, veo el usa_plapsa q habia antes
        if ($client['usa_plapsa']) {
            //debug($plataforma);
            $datosplataforma = $data['plataforma']['Plataformasdepagosconfig'] ?? $data['plataforma'] ?? $plataforma['Plataformasdepagosconfig'];
            //debug($datosplataforma);
            if (!isset($data['plataforma'])) {// resumenes viejos, para q muestre el codigo de barras y la comision cobrada
                $data['plataforma'] = $plataforma;
                $data['plataformas'] = $plataformas;
                $client['minimo'] = 25;
                $client['comision'] = 3.1;
            } else {
                $client['minimo'] = $datosplataforma['minimo'];
                $client['comision'] = $datosplataforma['comision'];
            }
            $idplataforma = $datosplataforma['plataformasdepago_id'];
            if ($idplataforma == 3) {//es roela
                $pos = $this->Functions->find2($data['plataforma']['Plataformasdepagosconfigsdetalle'], ['consorcio_id' => $consorcio['Consorcio']['id']]);
                if ($pos == []) {
                    die("Convenio Roela no encontrado");
                }
                $client['datointerno'] = $data['plataforma']['Plataformasdepagosconfigsdetalle'][$pos]['valor'];
            } else {
                $client['datointerno'] = $datosplataforma['datointerno'];
            }
            $client['codigo'] = $datosplataforma['codigo'];
            $client['plataforma'] = $data['plataformas'][$idplataforma]['modelo'];
            $client['titulo'] = $data['plataformas'][$idplataforma]['titulo'];
        } else {
            $client['plataforma'] = 'Plapsa';
        }
        /*         * ********************************************************************************************************* */

        $fecha = substr($fecha, 6, 4) . "-" . substr($fecha, 3, 2) . "-" . substr($fecha, 0, 2);
        anverso($propinfo['Propietario'], $client, $consorcio['Consorcio']);
        reverso($propinfo['Propietario'], $concepto, $importe, $client, $consorcio['Consorcio'], $fecha, $prefijos[$lt]);

        /* if ($client['usa_plapsa'] && $consorcio['imprime_cpe']) { // muestro la clave de pago electrónico (CPE) si el Cliente utiliza PLAPSA y el Consorcio tiene tildada la opcion "Imprime CPE"
          $codpagelect = generaClavePagoElectronico($consorcio['code'], $p['code'], $prefijo, $client['code']);
          if (strlen($codpagelect) == 14) {
          echo "<center><br><span class='box-table-a' style='padding:5px;margin-top:10px;font-size:11px;font-weight:bold;color:#666699;font-family:Verdana,Helvetica,sans-serif;'>";
          echo "SU CLAVE DE PAGO ELECTR&Oacute;NICO ES: $codpagelect (buscar como: Plataforma de Pagos)</span>";
          }
          } */

        @separacion(55);
        ?>
        <div style='page-break-after:always'></div>

    </body>
</html>
<?php

function anverso($propietario, $client, $consorcio) {
    $formato = "style='font-size:10px;font-family:Verdana,Helvetica,sans-serif;'";
    ?>
    <table width="750" <?= $formato ?> class="box-table-a" align="center">
        <tr>
            <td width="130" height="120" rowspan="3" align="center">
                <img alt="logo" width=100 height=100 src="/sistema/<?= file_exists("files/" . $client['id'] . "/" . $client['id'] . ".jpg") ? "files/" . $client['id'] . "/" . $client['id'] . ".jpg" : "img/0000.png" ?>">
            </td>
            <td width="360" rowspan="3" align="left" valign="middle">
                ADMINISTRACION<br/><br/>
                <font size=4><?= h($client['name']) ?></font><br/>
                <br/><?= h($client['address']) ?>
                <br/><?= h($client['city']) ?>
                <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                <br/>Tel.: <?= h(!empty($client['telephone']) ? $client['telephone'] : '--') ?>
                <br/>Email: <?= h(!empty($client['email']) ? $client['email'] : '--') ?>
            </td>
            <td align="left" valign="middle"><b>Consorcio:</b>
                <?= h($consorcio['name']) ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
                <b>CUIT: </b><?= h(!empty($consorcio['cuit']) && $consorcio['cuit'] !== "00-00000000-0" ? $consorcio['cuit'] : '--') ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
                <b>Domicilio: </b><?= h($consorcio['address']); ?>
            </td>
        </tr>
        <tr>
            <td align="left" rowspan="3" colspan="2" valign="top" cellspacing="0">
                <table border="0" valign="top" cellspacing="0" width="500" <?= $formato ?>>
                    <tr>
                        <td align="left" style="border:0px;padding-left:120px;">
                            <span style="width:60px;float:left;margin:20px 0px 0px -100px">PROPIETARIO
                            </span>
                            <font style="font-size:14px;font-weight:700;"><?= h($propietario['name']) ?></font><BR>
                            <?= $propietario['postal_address'] ?><br>
                            <?= $propietario['postal_city'] ?><br>
                            <br><span class="barcode1"><?= convertir_barcode(trim(str_pad($client['code'], 4, "0", STR_PAD_LEFT) . str_pad($consorcio['code'], 4, "0", STR_PAD_LEFT) . str_pad($propietario['code'], 4, "0", STR_PAD_LEFT))) ?></span>
                            <br><?= wordwrap(h($consorcio['name'] . " - " . $propietario['unidad']), 31, "<br />"); ?>
                            <span style="width:140px;float:right;margin-top:-20px;">
                                <?= h($client['name']) ?>
                                <br><?= h($client['address']) ?>
                                <br><?= h($client['city']) ?>
                                <br/>CUIT: <?= h(!empty($client['cuit']) && $client['cuit'] !== "00-00000000-0" ? $client['cuit'] : '--') ?>
                                <br/>Mat.: <?= h(!empty($client['numeroregistro']) ? $client['numeroregistro'] : '--') ?>
                            </span>        
                        </td>
                    </tr>
                </table>
            </td>
            <td align=left valign=bottom><b>Localidad:</b>
                <?= h($consorcio['city']) ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="bottom"><b>Unidad:</b>
                <?= h($propietario['unidad'] . " - Código: " . $propietario['code']) ?>
            </td>
        </tr>
        <tr>
            <td align="left" valign="middle">
            </td>
        </tr>
    </table>
    <?php
}

function reverso($p, $concepto, $importe, $client, $consorcio, $fecha, $prefijo) {
    $formato = "style='font-size: 10px; font-family: Verdana, Helvetica, sans-serif;border-top:0px'";
    $total = 0;
    ?>
    <table width=750 valign=top cellspacing=0 <?= $formato ?> class="box-table-b" align="center">
        <tr>
            <td class="center" colspan="6" style="font-size:14px;font-weight:bold"><br><br>
                <?= h("Reimpresión de cupón: " . $concepto) ?>
                <br><br>
            </td>
        </tr>
        <tr class="totales">
            <td style="text-align:left;"><b>Total</b></td>
            <td colspan="2" style='text-align:center'><?= $importe > 0 ? "IMPORTE DEPOSITO BANCARIO: " . money($importe + (($p['code'] / 100) - intval(($p['code'] / 100)))) : '' ?></td>
            <td colspan=3 class="right">
                <?php
                echo money($importe);
                ?>
            </td>
        </tr>
    </table>
    <br/>

    <?php
    if (intval($importe) > 0 && $consorcio['imprime_cod_barras']) {
        if ($consorcio['2_cuotas']) {
            talondepago($client, $consorcio['code'], $prefijo, $p['code'], (intval($importe) / 2), $fecha, $fecha);
            talondepago($client, $consorcio['code'], $prefijo, $p['code'], (intval($importe) / 2), $fecha, $fecha);
        } else {
            talondepago($client, $consorcio['code'], $prefijo, $p['code'], intval($importe), $fecha, $fecha);
        }
    } else {
        ?>
        <div style="clear:both;height:120px;"></div>
        <?php
    }
}

function talondepago($client, $consorcio_code, $prefijo, $propietario_code, $totalexpensa, $vencimiento, $limite) {
    ?>
    <table border=0 style='font-size:11px;font-family:Verdana,Helvetica,sans-serif;' align="center">
        <tr>
            <?php
            $c = $totalexpensa * ($client['comision'] / 100);
            if ($client['usa_plapsa']) {
                // 12/02/2020 Si la comision esta configurada cero, no pongo el minimo (asi los q no quieren q salga con comision, sale bien. Y de paso, cuando se reporta a PLAPSA sale sin comision
                if ($client['comision'] > 0 && $client['minimo'] > 0 && $c < $client['minimo']) {// PLAPSA (11/04/2017, modificado desde Gili Meno, Indonesia jaja) es el minimo de comision
                    $c = $client['minimo'];
                }
            } else {
                $c = $totalexpensa * (3.6 / 100); // latuf dijo 3.6 el 07/08/2019
            }

            // se muestran solo los cod de barras con total > 0 y menores a 100000/1.031~96993.21 (comision 3.1). PLAPSA NO SOPORTA IMPORTES > a 100mil
            if (($totalexpensa + $c) < 100000 / (1 + ($client['comision'] / 100))) {
                if ($client['usa_plapsa']) {
                    $codbarras = $client['plataforma']::generaCodigoBarras("2634", $client['codigo'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c, $client['datointerno']);
                } else {
                    $codbarras = Plapsa::generaCodigoBarras("305", $client['code'], $consorcio_code, $prefijo, $propietario_code, $vencimiento, $limite, $totalexpensa + $c, $totalexpensa + $c);
                }
                ?>
                <td align=center style="white-space:nowrap">
                    <span class="barcode1"><?= convertir_barcode($codbarras) ?></span>
                    <br>
                    <?php
                    // verifico la longitud del codigo de barras, tiene q ser 42/44/56!!!!!!
                    if (strlen($codbarras) == 42 || strlen($codbarras) == 44 || strlen($codbarras) == 56) {
                        echo $codbarras;
                    } else {
                        die("EL CODIGO DE BARRAS ES INCORRECTO, NO POSEE 42/44/56 CARACTERES!!!!");
                    }
                    ?><br><?= "<b>Fecha l&iacute;mite de pago: </b>" . date("d/m/Y", strtotime($limite)) ?> - <b>Validez: </b><?= date("d/m/Y", strtotime($vencimiento)); ?>
                </td>
                <td align=left width=auto>
                    <table style="border:0px; font-size: 11px; font-family: Verdana, Helvetica, sans-serif;margin:0px;" cellspacing="0" width="160">
                        <tr>							
                            <td align=left style="border:0px">
                                <b>TOTAL</b>
                            </td>
                            <td align=right style="border:0px" >
                                <?= money($totalexpensa) ?>
                            </td>
                        </tr>
                        <?php
                        if ($c > 0) {
                            ?>
                            <tr>
                                <td style="border:0px" align="left">
                                    <b>COMISION</b>
                                </td>                                <td style="border:0px" align="right">
                                    <?php
                                    echo money($c); //comision PLAPSA 
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td style="border:0px" align="right">
                                <hr align="right" style="visibility:hidden">
                                <b>TOTAL</b>
                            </td>
                            <td style="border:0px" align="right">
                                <hr align="right">
                                <?= money($totalexpensa + $c); //comision PLAPSA        ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php
            }
            ?>
        </tr>
    </table>
    <?php
}

function separacion($px) {
    ?>
    <div id="separacion" style="height:<?= $px ?>px;clear:both;">
    </div>
    <?php
}

function money($valor) {
    return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
}

function convertir_barcode($cadena) {
    if (empty($cadena)) {
        return "";
    }
    $resultado = "<img src='/sistema/img/bar/init.GIF' />";
    while (strlen($cadena) > 1) {
        $num = str_pad(substr($cadena, 0, 2), 2, "0", STR_PAD_LEFT);
        if (!is_numeric($num)) {
            die("error en el codigo de barras");
        }

        if (in_array($num, ['94', '95', '96', '97', '98', '99'])) {
            $resultado .= "<img src='/sistema/img/bar/$num.GIF' />";
        } else {
            $resultado .= h(chr($num + 33)); // uso h() porq si genera el caracter <, el navegador manda fruta
        }
        $cadena = substr($cadena, 2);
    }
    return $resultado . "<img src='/sistema/img/bar/fin.GIF' />";
}
