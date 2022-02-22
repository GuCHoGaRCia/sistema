<?php
// es el view para enviar por mail las Comunicaciones
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es-419">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="format-detection" content="telephone=no">
        <title>Comunicaci&oacute;n</title>
         <style>
            body {
                margin: 0;
                padding: 0;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
            }
            table {
                border-spacing: 0;
            }
            table td {
                border-collapse: collapse;
            }
            .ExternalClass {
                width: 100%;
            }
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }
            .ReadMsgBody {
                width: 100%;
                background-color: #ebebeb;
            }
            table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }
            img {
                -ms-interpolation-mode: bicubic;
            }
            .yshortcuts a {
                border-bottom: none !important;
            }
            @media screen and (max-width: 599px) {
                .force-row,
                .container {
                    width: 100% !important;
                    max-width: 100% !important;
                }
            }
            @media screen and (max-width: 400px) {
                .container-padding {
                    padding-left: 12px !important;
                    padding-right: 12px !important;
                }
            }
            .ios-footer a {
                color: #aaaaaa !important;
                text-decoration: underline;
            }
            .header,
            .title,
            .subtitle,
            .footer-text {
                font-family: Helvetica, Arial, sans-serif;
            }
            .header {
                font-size: 24px;
                font-weight: bold;
                padding-bottom: 12px;
                color: #DF4726;
            }
            .footer-text {
                font-size: 12px;
                line-height: 16px;
                color: #000;
            }
            .footer-text a {
                color: #000;
            }
            .container {
                width: 90%;
                max-width: 90%;
            }
            .container-padding {
                padding-left: 24px;
                padding-right: 24px;
            }
            .content {
                padding-top: 12px;
                padding-bottom: 12px;
                background-color: #fff;
            }
            code {
                background-color: #eee;
                padding: 0 4px;
                font-family: Menlo, Courier, monospace;
                font-size: 12px;
            }
            hr {
                border: 0;
                border-bottom: 1px solid #000;
            }
            .title {
                font-size: 18px;
                font-weight: 600;
                color: #374550;
            }
            .subtitle {
                font-size: 16px;
                font-weight: 600;
                color: #2469A0;
            }
            .subtitle span {
                font-weight: 400;
                color: #999;
            }
            .body-text {
                font-family: Helvetica, Arial, sans-serif;
                font-size: 14px;
                line-height: 20px;
                text-align: left;
                color: #333;
            }
        </style>
    </head>
    <body style="margin:0; padding:0;" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
        <table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="top">
                    <br>
                    <table border="0" width="100%" cellpadding="0" cellspacing="0" class="container">
                        <tr>
                            <td class="container-padding header" align="left">
                                <?php
                                //<img src="data:image/png;base64,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" />
                                // dejar el ceonline.com.ar sino el redir de imagenes no funciona, y en el mail no muestra el logo!
                                $ff = APP . WEBROOT_DIR . DS . 'files' . DS . h($client['id']) . DS . h($client['id']) . ".jpg";
                                if (!file_exists($ff)) {
                                    $logo = "https://ceonline.com.ar/sistema/img/0000.png";
                                } else {
                                    $logo = "https://ceonline.com.ar/sistema/files/" . h($client['id']) . "/" . h($client['id']) . ".jpg";
                                }
                                ?>
                                <img alt="logo" style="width:70px" src="<?= $logo ?>">
                                <p style="display:inline;font-family:Cooper;font-size:24px;color:#000"><?= h($client['name']) ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="container-padding content" align="left">
                                <br>
                                <div class="title">
                                    <?= h($data['Comunicacione']['asunto']) ?>
                                </div>
                                <br>
                                <div class="body-text">
                                    <?= $data['Comunicacione']['mensaje'] ?>
                                </div>
                                <br><hr>
                                <div class="title">
									<?php
									if (!empty($data['Comunicacionesadjunto'])) {
										echo "Adjuntos recibidos";
									}
									?>
                                </div>
                                <br>
								<?php
								if (count($data['Comunicacionesadjunto']) > 0) {
									foreach ($data['Comunicacionesadjunto'] as $k => $v) {
										$dir = APP . DS . WEBROOT_DIR . DS . $this->Functions->_decryptURL($v['url']);
										if (file_exists($dir)) {
											echo "<a target='_blank' rel='nofollow noopener noreferrer' href='" . Router::url('/', true) . 'Adjuntos/e/' . $v['url'] . "'>" . h($v['titulo']) . "</a><br>";
										}
									}
								}
								?>
                            </td>
                        </tr>
                        <tr>
                            <td class="container-padding footer-text" align="center">
                                <br><br>
                                &#0169; <?= date("Y") ?>&nbsp;
                                <?= h($client['name']) ?>
                                <br><br>
                                <span class="ios-footer">
                                    <?= h($client['address'] . " - " . $client['city']) ?><br>
                                    <?= h($client['email'] . " - " . $client['telephone']) ?><br>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>