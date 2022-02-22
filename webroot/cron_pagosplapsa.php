<?php

//Cron que descarga los pagos de la Plataforma de Pagos SA
$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");
require __DIR__ . '/../vendor/autoload.php';

use phpseclib3\Net\SFTP;

ini_set("display_errors", 1);
ini_set('max_execution_time', '10000');
$conf = new DATABASE_CONFIG();

try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'], $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$fecha = isset($_GET["fecha"]) ? date("Y-m-d", strtotime($_GET['fecha'])) : date("Y-m-d");

echo "Comienzo del proceso...<br>";
if (getPagosPlataformaPagos($fecha, $webroot)) {
    $ruta = $webroot . "/plapsa/";
    if (is_dir($ruta)) {
        if ($dh = opendir($ruta)) {
            while (($cliente = readdir($dh)) !== false) {
                if (!is_dir($ruta . $cliente)) {
                    mkdir($ruta . $cliente, 0755, true);
                }
                if (!in_array($cliente, ['.', '..'])) {
                    echo "Verificando los pagos del Cliente $cliente...<br>";
                    $file = $ruta . $cliente . "/" . preg_replace("/[^0-9]/", "", $cliente) . "_RD" . date("Ymd", strtotime($fecha)) . '.txt';
                    // si ya se bajó el archivo, no hago nada
                    if (file_exists($file)) {
                        $data = $db->query('SELECT count(*) from pagoselectronicos where fecha_proc="' . $fecha . '" and client_code=' . $cliente)->fetchColumn();
                        sleep(1); // 09/03/2020 agregué esto (fruta) y count(*) porq a veces el query se ve q devolvía vacío (habiendose ya cargado pagos) y se volvian a cargar. cobranzas automaticas duplicadas
                        // si no se procesó ningun pago de este cliente, lo hago
                        if ($data == 0) {
                            procesarArchivo($file, $db, $fecha);
                        } else {
                            echo "Los pagos del Cliente $cliente ya fueron procesados...<br>";
                        }
                    }
                }
            }
        }
    }
} else {
    echo "Ocurri&oacute; un error al descargar el archivo (si hay internet, entonces PLAPSA todavía no informó el archivo del día de hoy)<br>"; // error?
}
echo "</div>";

// obtengo los pagos de la Plataforma de Pagos S.A. para la fecha $fecha
function getPagosPlataformaPagos($fecha, $webroot) {
    try {
        $sftp = new SFTP('files.plataformadepagos.com.ar', '22');
        if (!$sftp->login(base64_decode('Q2VvT25saW5l'), base64_decode('T05jZW9MaW5lQEA='))) {
            exit('Login Failed');
        }
        $lista = $sftp->nlist();
        if (empty($lista)) {
            return false;
        }
        foreach ($lista as $v) {
            if (!in_array($v, ['.', '..'])) {
                // es un directorio, descargo el archivo del dia de la fecha, si existe?
                // obtengo el archivo del dia $fecha
                $cliente = preg_replace("/[^0-9]/", "", $v);
                $nombre = $cliente . "_RD" . date("Ymd", strtotime($fecha)) . '.txt';
                $nombre2 = $cliente . "_RD" . date("Ymd", strtotime($fecha)) . '.pdf';
                if (!is_dir($webroot . "/plapsa/$cliente")) {
                    mkdir($webroot . "/plapsa/$cliente", 0755, true);
                }
                if (file_exists("plapsa/$cliente/$nombre") && file_exists("plapsa/$cliente/$nombre2") && filesize("plapsa/$cliente/$nombre") > 0 && filesize("plapsa/$cliente/$nombre2") > 0) {// si ya existen, no los vuelvo a bajar
                    //lo comento temporalmente, porq en caso q la plataforma envie mal los archivos, y tengamos q borrarlos, nunca baja los nuevos correctos    
                    //una vez solucionado, habilitar de nuevo sino cada 15 minutos descarga todos los archivos al pedo
                    //NO TOCAR MAS: Si esta seteado $_GET["fecha"] es una llamada desde el sistema, descargo todo otra vez x las dudas. Caso contrario (cron), hago continue
                    if (!isset($_GET["fecha"])) {
                        continue;
                    }
                }
                $sftp->get($v . "/Recibe/" . $nombre, $webroot . "/plapsa/$cliente/$nombre");
                $sftp->get($v . "/Recibe/" . $nombre2, $webroot . "/plapsa/$cliente/$nombre2");
                if (file_exists($webroot . "/plapsa/$cliente/$nombre") && filesize($webroot . "/plapsa/$cliente/$nombre") == 0) {
                    unlink($webroot . "/plapsa/$cliente/$nombre");
                }
                if (file_exists($webroot . "/plapsa/$cliente/$nombre2") && filesize($webroot . "/plapsa/$cliente/$nombre2") == 0) {
                    unlink($webroot . "/plapsa/$cliente/$nombre2");
                }
            }
        }

        return true;
    } catch (Exception $ex) {
        echo "Ocurri&oacute; un error al descargar el archivo<br>";
        return false;
    }
}

function procesarArchivo($file, $db, $f) {
    if (filesize($file) > 0) {
        // abro el archivo
        // preparo la inserción
        $sql = "INSERT INTO pagoselectronicos ";
        $sql .= "(`client_code`, `consorcio_code`, `propietario_code`, `prefijo`, `fecha`, `fecha_proc`, `medio`, `importe`, `comision`, `cobranza_id`, `created`) VALUES (";
        $sql .= ":client_code,:consorcio_code,:propietario_code,:prefijo,:fecha,:fecha_proc,:medio,:importe,:comision,0,:created)";
        $q = $db->prepare($sql);

        $cant_pagos = 0;
        $handle = fopen($file, "r");
        $contents = fread($handle, filesize($file));
        $arch = explode("\r\n", $contents); // leo todo el archivo

        $sumatotal = 0;
        $sumacomision = 0;
        foreach ($arch as $txt_entry) {
            switch (substr($txt_entry, 0, 1)) {
                case 1: // header
                    $idplataformapago = substr($txt_entry, 1, 4);
                    //$fechaArchivo = substr($txt_entry, 5, 8);
                    //$adminInfo = getAdminInfo($idplataformapago, $db);
                    break;
                case 5: // detalle
                    $idconsorcio = substr($txt_entry, 1, 4);
                    $idunidad = substr($txt_entry, 6, 4); // xyyyy, obtiene yyyy
                    $prefijo = substr($txt_entry, 5, 1); // xyyyy, obtiene x
                    $fecha = substr($txt_entry, 10, 8);
                    $monto = ((int) substr($txt_entry, 18, 11)) / 100;
                    $comision = ((int) substr($txt_entry, 29, 11)) / 100;
                    $neto = ((int) substr($txt_entry, 40, 11)) / 100;
                    $medio = trim(substr($txt_entry, 51, 15));

                    // calculo los totales
                    $sumatotal += $monto;
                    $sumacomision += $comision;
                    // Obtengo fecha del proceso a partir de $f (si $f es un dia anterior, le agrego la hora
                    $fecha_proc = date("Y-m-d H:i:s", strtotime($f . " " . date("H:i:s", time())));

                    $q->bindParam(":client_code", /* $adminInfo[0] */ $idplataformapago);
                    $q->bindParam(":consorcio_code", $idconsorcio);
                    $q->bindParam(":propietario_code", $idunidad);
                    $q->bindParam(":prefijo", $prefijo);
                    $q->bindParam(":fecha", $fecha);
                    $q->bindParam(":fecha_proc", $fecha_proc);
                    $q->bindParam(":medio", $medio);
                    $q->bindParam(":importe", $monto);
                    $q->bindParam(":comision", $comision);
                    $q->bindParam(":created", $fecha_proc);

                    //Inserto el pago en pagoselectronicos
                    if (!$q->execute()) {
                        print_r($q->errorInfo());
                    }

                    // guardo la cantidad de pagos de este consorcio
                    $cant_pagos++;
                    break;
                case 9: // trail
                    // Guardo el reporte.
                    break;
            }
        }
        echo "Se procesaron $cant_pagos pagos<br>";
    }
}

function getAdminInfo($idplataformapago, $db) {
    foreach ($db->query("SELECT id,name FROM clients where code=$idplataformapago LIMIT 1") as $row) {
        return [isset($row['id']) ? $row['id'] : $idplataformapago, isset($row['name']) ? $row['name'] : 'sin cliente asociado'];
    }
    return [$idplataformapago, 'sin cliente asociado'];
}
