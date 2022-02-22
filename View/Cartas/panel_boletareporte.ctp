<?php
//(int) 0 => array(
//        (int) 0 => array(
//                'cantidad' => '8'
//        ),
//        'Cartastipo' => array(
//                'nombre' => '100202001 - Certificada plus H/150 grs.'
//        )
//),
//(int) 1 => array(
//        (int) 0 => array(
//                'cantidad' => '5'
//        ),
//        'Cartastipo' => array(
//                'nombre' => '100502006 - Carta Factura Dist. Local h/50 grs.'
//        )
//)
//array(
//	(int) 0 => array(
//		'100702006 - Registrada 50gr' => (int) 0,
//		'100202001 - Certificada plus H/150 grs.' => (int) 3,
//		'100602001 - Rápida plus h/150 grs.' => (int) 0,
//		'100502006 - Carta Factura Dist. Local h/50 grs.' => (int) 5,
//		'100302001 - Expreso plus h/150 grs.' => (int) 0
//	),
//	(int) 1 => array(
//		'100702006 - Registrada 50gr' => (int) 0,
//		'100202001 - Certificada plus H/150 grs.' => (int) 5,
//		'100602001 - Rápida plus h/150 grs.' => (int) 0,
//		'100502006 - Carta Factura Dist. Local h/50 grs.' => (int) 0,
//		'100302001 - Expreso plus h/150 grs.' => (int) 0
//	)
//)
$totales = [0 => []/*, 1 => []*/];
$empresas = [0 => ['nombre' => 'MANEKESE S.R.L.', 'cuenta' => '000017766', 'fact' => '17766FMDPL', 'cuit' => '30-71454867-7']/*,
    1 => ['nombre' => 'BUREAU S.A.', 'cuenta' => '000015982', 'fact' => '15982FMDPL', 'cuit' => '30-70904123-8']*/];
foreach ($tipos as $k => $v) {
    $totales[0][$v] = 0;
    //$totales[1][$v] = 0;
}

foreach ($cartas as $k => $v) {
    if (!isset($totales[!$v['Client']['es_manekese']][$v['Cartastipo']['nombre']])) {
        $totales[!$v['Client']['es_manekese']][$v['Cartastipo']['nombre']] = 0;
    }
    $totales[!$v['Client']['es_manekese']][$v['Cartastipo']['nombre']] += ($v['Carta']['oblea'] == 'S' && ctype_digit($v['Carta']['codigo']) && $v['Carta']['propietario_id'] == '0' ? $v['Carta']['codigo'] : 1);
}

foreach ($totales as $k => $v) {
    ?>
    <html lang="es-419">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
            <title>Boleta de Imposicion</title>
        </head>
        <style>
            table{
                font-size:11px;
            }
        </style>
        <body>
            <table border="0" width="100%">
                <tr>
                    <td width="25%">
                        <img src="<?= $this->webroot ?>img/correo.gif" v:shapes="_x0000_i1025" width="140" height="45"></td>
                    <td width="40%" valign="bottom">
                        <p align="center"><b><span>Solicitud de
                                    Retiro / Boleta de imposici&oacute;n<o:p>
                                        &nbsp;
                                </span></b></p>
                    </td>
                    <td width="35%" align="right" valign="top">
                        Fecha de confecci&oacute;n : <?php echo h($fecha) ?>
                    </td>
                </tr>
            </table>
            <table border="1" width="100%" bordercolor="#FFFFFF">
                <tr>
                    <td width="6%">&nbsp;</td>
                    <td width="4%" bordercolor="#000000">
                        <p align="center">X</td>
                    <td width="16%">
                        Retiro permanente</td>
                    <td width="4%" bordercolor="#000000">
                        <p align="center"> &nbsp;</td>
                    <td width="16%">
                        Retiro eventual</td>
                    <td width="4%" bordercolor="#000000">
                        <p align="center"> &nbsp;</td>
                    <td width="16%">
                        Ventanilla</td>
                    <td width="34%" bordercolor="#000000"> </td>
                </tr>
            </table>
            <table border="1" cellspacing="0" cellpadding="0" width="100%" bordercolor="#FFFFFF">
                <tr valign="center" style="height:30.6pt">
                    <td colspan= "2" valign="top" bordercolor="#808080" >
                        <p class="MsoNormal"><br><b>&nbsp;C.U.I.T. N&ordm;:</b>
                            <font size="5">
                            <?= h($empresas[$k]['cuit']) ?></font>
                    </td>
                </tr>
                <tr height="5">
                    <td height="5" colspan= "2"></td>
                </tr>

                <tr >
                    <td width="230" valign="top" bordercolor="#808080" >
                        &nbsp;Cuenta N&ordm;: <?= h($empresas[$k]['cuenta']) ?>    </td>
                    <td valign="top" bordercolor="#808080" >
                        &nbsp;Nombre: <?= h($empresas[$k]['nombre']) ?>
                    </td>
                </tr>
                <tr height="5">
                    <td height="5" colspan= "2"></td>
                </tr>

                <tr >
                    <td valign="top" bordercolor="#808080" >
                        &nbsp;Dest. Factura N&ordm;: <?= h($empresas[$k]['fact']) ?>    </td>
                    <td valign="top" bordercolor="#808080" >
                        &nbsp;Nombre:</td>
                </tr>
                <tr height="5">
                    <td height="5" colspan= "2"></td>
                </tr>

                <tr >
                    <td valign="top" bordercolor="#808080" >
                        &nbsp;Subsubcuenta N&ordm;:  </td>
                    <td valign="top" bordercolor="#808080" >
                        &nbsp;Nombre:      </td>
                </tr>
                <tr height="5">
                    <td height="5" colspan= "2"></td>
                </tr>

                <tr>
                    <td colspan= "2" valign="top" bgcolor="#DADADA" bordercolor="#808080"><b>&nbsp;N&ordm; orden de compra o contrato (*):</b>
                    </td>
                </tr>
            </table>
            Se&ntilde;ores: <BR> Correo Argentino S.A., junto con la presente enviamos las piezas que a continuaci&oacute;n se detallan:
            <table border="1" cellspacing="0" cellpadding="0" width="100%">
                <tr >
                    <td width="20%" valign="top" bgcolor="#DADADA">
                        <b>C&oacute;d. de Prod. (*)</b>      </td>
                    <td width="40%" align="center" valign="top" bgcolor="#DADADA">
                        <b>Descripci&oacute;n</b>            </td>
                    <td width="20%" valign="top" bgcolor="#DADADA">
                        <b>Cantidad entregada</b>     </td>     
                </tr>
                <?php
                $tot = 0;
                foreach ($v as $r => $s) {
                    if ($s > 0) {
                        $dsc = explode(" - ", $r);
                        echo "<tr><td>" . h($dsc[0]) . "</td>";
                        echo "<td>" . h($dsc[1]) . "</td>";
                        echo "<td>&nbsp;" . h($s) . "</td></tr>";
                        $tot += $s;
                    }
                }
                ?>

                <tr >
                    <td bgcolor="#DADADA">&nbsp;         </td>
                    <td align="right" bgcolor="#DADADA">
                        <b>
                            TOTAL DE PIEZAS&nbsp;</b>         </td>
                    <td bgcolor="#DADADA">&nbsp;<?php echo $tot ?> </td>
                </tr>
            </table>
            <BR>

            <table border="1" cellspacing="0" cellpadding="0" width="100%" bgcolor="#DADADA">
                <tr>
                    <td width="100%" colspan="8"><b>PARA
                            COMPLETAR EN CASO DE ENVIO DE COMPONENTES, ADICIONALES Y/O BULTOS (*):</b></td>
                </tr>
                <tr>
                    <td width="15%" align="center"><b>Componentes</b></td>
                    <td width="10%" align="center"><b>Total</b></td>
                    <td width="15%" align="center"><b>Componentes</b></td>
                    <td width="10%" align="center"><b>Total</b></td>
                    <td width="15%" align="center"><b>Componentes</b></td>
                    <td width="10%" align="center"><b>Total</b></td>
                    <td width="15%" align="center"><b>Tipo de bultos</b></td>
                    <td width="10%" align="center"><b>Total</b></td>
                </tr>
                <tr>
                    <td width="15%">Sobres</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">Folletos</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">Otros</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    <td width="10%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="15%">Hojas Innominadas</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">Stickers</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    <td width="10%">&nbsp;</td>
                </tr>
                <tr>
                    <td width="15%">Hojas  Nominadas</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">Resumen Cta</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">Diskette (c.regist)</td>
                    <td width="10%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    <td width="10%">&nbsp;</td>
                </tr>
            </table>

            OBS: Las cantidades y productos impuestos est&aacute;n sujetos a verificaci&oacute;n por parte
            de Correo Argentino S.A.<BR>

            <table border="1" cellspacing="0" cellpadding="0" width="100%">
                <tr style="height:50.0pt">
                    <td width="34%" valign="top" bgcolor="#DADADA" >Firma Transportista (*): <br><br><br>
                    </td>
                    <td width="33%" valign="top">Firma Admisi&oacute;n/Ventanilla:
                    </td>
                    <td width="33%" valign="top">Firma del Cliente:
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#DADADA">Aclaraci&oacute;n:
                    </td>
                    <td>Aclaraci&oacute;n:
                    </td>
                    <td>Aclaraci&oacute;n:
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#DADADA">DNI N&ordm;:
                    </td>
                    <td>DNI N&ordm;:
                    </td>
                    <td>DNI N&ordm;:
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#DADADA">Cargo:
                    </td>
                    <td>Cargo:
                    </td>
                    <td>Cargo:
                    </td>
                </tr>
            </table>
            <BR>

            <table border="1" cellspacing="0" cellpadding="0" width="100%" bgcolor="#DADADA">
                <tr style="height:15.0pt;mso-height-rule:exactly">
                    <td colspan="4" valign="top" align="center" height="19"><b>
                            PARA COMPLETAR EN CASO DE SOLICITUDES DE RETIROS (EXCLUSIVO CORREO ARGENTINO):</b>
                    </td>
                </tr>
                <tr>
                    <td width="33%" valign="top">Fecha retiro:&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;/
                    </td>
                    <td width="33%" colspan="2" valign="top" height="19">Franja horaria: DE:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        A:      </td>
                    <td width="34%" valign="top">Contacto:      </td>
                </tr>
                <tr>
                    <td colspan="4" valign="top">Domicilio Retiro:      </td>
                </tr>
                <tr>
                    <td colspan="4" valign="top">Apellido y Nombre del solicitante del cliente:</td>
                </tr>
                <tr>
                    <td colspan="2" width="43%" valign="top">Cargo y Sector:</td>
                    <td width="20%" valign="top">Telefono:</td>
                    <td valign="top">e-mail:      </td>
                </tr>
                <tr>
                    <td colspan="4" valign="top">Lugar de Entrega:&nbsp;&nbsp; OGC&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )&nbsp;&nbsp;&nbsp;
                        CPI&nbsp; (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp; 
                        CTP-BUE&nbsp; (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Otro (detallar):
                    </td>
                </tr>
                <tr>
                    <td colspan="3" valign="top" height="19" >
                        Factibilidad N&ordm;
                    </td>
                    <td rowspan="2" valign="top">Aprob&oacute; Jefe Unidad de Negocio:<BR>
                        Firma y Aclaraci&oacute;n<BR>
                        <BR>
                        <BR>
                        <BR>Tel&eacute;fono:      </td>
                </tr>
                <tr>
                    <td colspan="3" valign="top" >
                        NOTAS: 
                        <BR>
                        <BR>
                        <BR>
                        <BR>
                    </td>
                </tr>
            </table>
            <BR>
            <table border="1" cellspacing="0" cellpadding="0" width="100%" bgcolor="#DADADA">
                <tr>
                    <td colspan="2" valign="top">
                        Datos de Transporte:</td>
                    <td rowspan="4" valign="top">
                        Recibido por (firma, aclaraci&oacute;n y hora):</td>
                </tr>
                <tr>
                    <td valign="top" >
                        N&ordm; de Retiro:      </td>
                    <td valign="top" >
                        Circuito N&ordm;      </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" >
                        Nombre del transportista      </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" >
                        Legajo N&ordm;      </td>
                </tr>
                <tr>
                    <td colspan="3" valign="top" >
                        <b>COMPLETAR EN CASO DE RETIROS EVENTUALES O PERMANENTES</b>
                    </td>
                </tr>

            </table>
        </body>
    </html>
    <div style='page-break-after:always'></div>    
    <?php
}
