<?php
// es el view para enviar por mail los avisos de nueva reparacion asignada a supervisor
//debug($client);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es-419">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="format-detection" content="telephone=no">
        <title>Reparaciones Supervisor</title>
        <style>
            body {
                margin:0;
                padding:0;
                border-style:solid;
                border-width:thin;
                border-color:#dadce0;
                border-radius:8px;
                padding:40px 20px;
            }
            table {
                border-spacing:0;
            }
            table td {
                border-collapse:collapse;
            }
            .ExternalClass {
                width:100%;
            }
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height:100%;
            }
            .ReadMsgBody {
                width:100%;
                /*background-color:#ebebeb;*/
            }

            table {
                mso-table-lspace:0pt;
                mso-table-rspace:0pt;
            }

            img {
                -ms-interpolation-mode:bicubic;
            }

            .yshortcuts a {
                border-bottom:none !important;
            }

            @media screen and (max-width:599px) {
                .force-row,
                .container {
                    width:100% !important;
                    max-width:100% !important;
                }
            }
            @media screen and (max-width:400px) {
                .container-padding {
                    padding-left:12px !important;
                    padding-right:12px !important;
                }
            }
            .ios-footer a {
                color:#000000 !important;
                text-decoration:none;
            }
            .header,.subtitle,.footer-text {
                font-family:Arial,Sans-serif;
            }
            .header {
                font-size:24px;
                font-weight:bold;
                padding-bottom:12px;
                color:#DF4726;
            }

            .footer-text {
                padding:10px;
                font-size:12px;
                line-height:16px;
                color:#888 !important;
                background-color:#f6f6f6;
                background-size:10px;
            }
            .footer-text a {
                color:#888 !important;
                text-decoration:none !important;
            }
            .container {
                width:90%;
                max-width:90%;
            }

            .container-padding {
                padding-left:24px;
                padding-right:24px;
            }

            .content {
                padding-top:12px;
                padding-bottom:12px;
                background-color:#fff;
            }

            code {
                background-color:#eee;
                padding:0 4px;
                font-family:Menlo, Courier, monospace;
                font-size:12px;
            }
            hr {
                border:0;
                border-bottom:1px solid #ccc;
            }

            .hr {
                height:1px;
                border-bottom:1px solid #ccc;
            }

            .title {
                font-size:18px;
                font-weight:600;
            }
            .title2{
                text-align:center;
                margin-top:10px;
            }
            .title2 a{   
                line-height:16px;
                color:#fff !important;
                font-weight:400;
                text-decoration:none;
                font-size:16px;
                display:inline-block;
                padding:15px 30px;
                background-color:#4184f3;
                border-radius:5px;
                min-width:90px;
            }
            .subtitle {
                font-size:16px;
                font-weight:600;
                color:#2469A0;
            }
            .subtitle span {
                font-weight:400;
                color:#999;
            }

            .body-text {
                font-family:Arial,Sans-serif;
                font-size:14px;
                line-height:20px;
                text-align:left;
                color:#000;
            }
        </style>
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="">
            <tr>
                <td align="center" valign="top" bgcolor="" style="">
                    <table border="0" width="100%" cellpadding="0" cellspacing="0" class="container">
                        <tr>
                            <td class="container-padding header" align="left">
                                <?php
                                //<img src="data:image/png;base64,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" />
                                // dejar el ceonline.com.ar sino el redir de imagenes no funciona, y en el mail no muestra el logo!
                                ?>
                                <img alt="logo" width=100 height=100 src="https://ceonline.com.ar/sistema/files/<?= h($client['id']) ?>/<?= h($client['id']) ?>.jpg"><br>
                                <p style="display:inline;font-size:24px;color:#000"><?= h($client['name']) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="container-padding content" align="left">
                                <br>
                                <div class="title">
                                    <?= h("Estimado Supervisor:") ?>
                                </div>
                                <br>
                                <div class="body-text">
                                    Una nueva Reparaci&oacute;n le fue asignada. Para ingresar a su Panel de Reparaciones, presione el siguiente bot&oacute;n
                                </div>
                                <br>
                                <div class="title2">
                                    <?php
                                    echo "<a target='_blank' rel='nofollow noopener noreferrer' href='https://$url" . "Reparacionessupervisores/view/$link'>Visualizar Expensas</a>";
                                    ?>
                                </div>
                                <br>

                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <?= h($client['name']) ?>
                                <br>
                                <span class="ios-footer">
                                    <?= h($client['address'] . " - " . $client['city']) ?><br>
                                    <?= (isset($client['email']) ? '<a href="mailto:' . h($client['email']) . '?subject=\'Consulta email avisos\'">' . h($client['email']) . '</a>' : '' ) . h(!empty($client['telephone']) ? " - " . $client['telephone'] : '') ?>
                                </span>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr style="text-align:center"><td>
                                <?php
                                /* $arr = str_split(strtolower($email) . "!$*~|#aA<>3bñ " . strtoupper($email));
                                  $total = 139099;
                                  foreach ($arr as $v) {
                                  $total += (ord($v) * ord($v));
                                  }
                                  $codigo = str_pad(substr(($total * $total), -6, 6), 6, 0, STR_PAD_RIGHT);
                                  if (!empty($codigo)) {
                                  ?>
                                  <hr>
                                  Visualiz&aacute; tus Expensas en tu celular con nuestra App (disponible solo para Android) <br>
                                  Tu C&oacute;digo Personal es: <b><?= h($codigo) ?></b><br>
                                  ¿Necesit&aacute;s ayuda? Mir&aacute; el instructivo <a href="https://ceonline.com.ar/web/app.pdf">ac&aacute!</a>
                                  <a href='https://play.google.com/apps/testing/ar.com.ceonline.expensasonline'>
                                  <img alt='Disponible en Google Play' style="width:200px" src='https://play.google.com/intl/en_us/badges/images/generic/es-419_badge_web_generic.png'/>
                                  </a>
                                  <hr>
                                  <?php
                                  } */
                                ?>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="container-padding footer-text" align="center">
                                Mensaje generado y enviado a trav&eacute;s del Sistema CEONLINE de Liquidaci&oacute;n de Expensas
                                <br>
                                &#0169; <?= date("Y") . ' <a target="_blank" rel="nofollow noopener noreferrer" href="https://ceonline.com.ar/web/">CEONLINE</a> - <a href="mailto:info@ceonline.com.ar?subject=\'Consulta email avisos\'">info@ceonline.com.ar</a>' ?>&nbsp;
                                <br>
                                <span class="ios-footer">
                                    <?= '(7600) Mar del Plata' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>