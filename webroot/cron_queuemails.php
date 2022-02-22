<?php

/* Se utiliza como CRON para enviar mails que se ponen en cola desde el CEO
 * y que deseen enviar el link de acceso a lasexpensas a cada propietario.
 * Se utiliza el servicio de SENDGRID para el envio de mails
 * https://github.com/sendgrid/sendgrid-php#alternative-install-package-from-zip
 * https://sendgrid.com/docs/for-developers/sending-email/v3-php-code-example/
 * https://github.com/sendgrid/sendgrid-php/blob/main/USE_CASES.md#send-an-email-to-a-single-recipient
 */

$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");
ini_set("display_errors", 1);
ini_set('max_execution_time', '20');
$conf = new DATABASE_CONFIG();
try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'], $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    die();
}

// es una lista de id internos para luego borrar los mails q fueron enviados
$listaid = [];
$limit = 30;
require __DIR__ . '/../vendor/autoload.php';

use SendGrid\Mail\Mail;

// envio 100 mails por minuto (cada registro puede tener más de un mail)
foreach ($db->query("SELECT id,client_id,altbody,codigohtml,mailto,razonsocial,emailfrom,asunto,whatsapp FROM avisosqueues order by id asc limit $limit") as $row) {
    $response = enviarAviso($db, $row['mailto'], $row['altbody'], $row['codigohtml'], $row['client_id'], $row['razonsocial'], $row['emailfrom'], $row['asunto'], $row['whatsapp']);
    if ($response === 202) {
        $listaid[] = $row['id'];
    }
}
//cierro CURL
//curl_close($session);
// si estuvo todo bien, borro los mails
if (count($listaid) > 0) {
    $db->query("DELETE FROM avisosqueues where id in (" . implode(",", $listaid) . ") or mailto='' limit $limit");
}

function enviarAviso($db, $email, $text, $html, $client_id = 0, $razonsocial = 'Administracion', $from = 'no-responder@ceonline.com.ar', $asunto = 'Acceso a sus expensas', $whatsapp = '') {
    $emails = explode(",", $email);
    $statuscode = 0;
    foreach ($emails as $k => $v) {
        if (filter_var($v, FILTER_VALIDATE_EMAIL)) {
            //$params = array(
            //    'api_user' => base64_decode('dGhlNGhvcnNlbWVu'), 'api_key' => base64_decode('MjNFNzEyZDgh'), 'to' => $v, //the4horsemen
            //    'fromname' => empty($razonsocial) ? 'Administracion' : cleanString($razonsocial),
            //    'from' => strpos($from, '@yahoo.') === false ? $from : 'no-responder@ceonline.com.ar',
            //    'replyto' => $from,
            //    'subject' => $asunto,
            //    'html' => (str_replace('181.231.115.147:3333/sistema/', 'ceonline.com.ar/p/?', $html)),
            //    'text' => (str_replace('181.231.115.147:3333/sistema/', 'ceonline.com.ar/p/?', $text)),
            //);
            ////print_r($params);
            //curl_setopt($session, CURLOPT_POSTFIELDS, $params);
            //$response = curl_exec($session);
            // https://github.com/sendgrid/sendgrid-php#alternative-install-package-from-zip
            // https://sendgrid.com/docs/for-developers/sending-email/v3-php-code-example/

            $email = new Mail();
            $email->setFrom(strpos($from, '@yahoo.') === false ? $from : 'no-responder@ceonline.com.ar', empty($razonsocial) ? 'Administracion' : cleanString($razonsocial));
            $email->setReplyTo(strpos($from, '@yahoo.') === false ? $from : 'no-responder@ceonline.com.ar', empty($razonsocial) ? 'Administracion' : cleanString($razonsocial));
            $email->setSubject($asunto);
            $email->addTo($v);
            $email->addContent("text/plain", $text);
            $email->addContent("text/html", $html);
            $sendgrid = new \SendGrid('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');
            try {
                $response = $sendgrid->send($email);
                //print $response->statusCode() . "\n";
                $statuscode = $response->statusCode();
                //print_r($response->headers());
                //print $response->body() . "\n";
            } catch (Exception $e) {
                //echo 'Caught exception: ', $e->getMessage(), "\n";
            }

            // guardo el mail en avisos con el client_id asociado, para luego con los reportes de sendgrid saber de quien es el mail
            //echo "clientid=$client_id $v";
            $existe = false;
            foreach ($db->query("SELECT id FROM avisos where email='$v' limit 1") as $mail2) {
                $existe = true;
                if (isset($mail2['id'])) {
                    //echo "2";
                    //echo "update avisos set client_id=" . $client_id . " where id=" . $mail2['id'] . " limit 1";
                    $db->query("update avisos set client_id=" . $client_id . " where id=" . $mail2['id'] . " limit 1");
                } else {
                    //echo "3";
                    // creo el registro
                    //echo "insert into avisos (client_id,propietario_id,email,eventos,created,modified) values (" . $client_id . ",0,'$v','',now(),now())";
                    $db->query("insert into avisos (client_id,propietario_id,email,eventos,created,modified) values (" . $client_id . ",0,'$v','',now(),now())");
                }
            }
            if (!$existe) {
                //echo "no existe, lo creo";
                $db->query("insert into avisos (client_id,propietario_id,email,eventos,created,modified) values (" . $client_id . ",0,'$v','',now(),now())");
            }

            // whatsapp
            if (!empty($whatsapp) && !empty($text)) {
                $telefonos = explode(",", $whatsapp);
                echo "Enviando whatsapps..";
                foreach ($telefonos as $t) {
                    if (ctype_digit($t)) {
                        $resul = sendWhatsapp($t, $text);
                        $date = date("Y-m-d H:i:s");
                        $sentencia = $db->prepare("insert into avisoswhatsapps (client_id,resul,numero,created) values (:client_id,:resul,:numero,:created)");
                        $sentencia->bindParam(':client_id', $client_id);
                        $sentencia->bindParam(':resul', $resul);
                        $sentencia->bindParam(':numero', $t);
                        $sentencia->bindParam(':created', $date);
                        $sentencia->execute();
                    }
                }
            }
        }
    }
    return $statuscode;
}

function cleanString($text) {
    $utf8 = [
        '/[áàâãªä]/u' => 'a', '/[ÁÀÂÃÄ]/u' => 'A', '/[ÍÌÎÏ]/u' => 'I', '/[íìîï]/u' => 'i', '/[éèêë]/u' => 'e',
        '/[ÉÈÊË]/u' => 'E', '/[óòôõºö]/u' => 'o', '/[ÓÒÔÕÖ]/u' => 'O', '/[úùûü]/u' => 'u', '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c', '/Ç/' => 'C', '/ñ/' => 'n', '/Ñ/' => 'N', '/–/' => '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u' => ' ', // Literally a single quote
        '/[“”«»„]/u' => ' ', // Double quote
        '/ /' => ' ', // nonbreaking space (equiv. to 0x160)
    ];
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}

function sendWhatsapp($numero, $texto) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://mywhatsapp.jca.ec:5433/chat/sendmessage/$numero?number=K1tRG6NrXELnOn8pcazK");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['message' => $texto]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    return curl_exec($ch);
}
