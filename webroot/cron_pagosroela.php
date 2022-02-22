<?php

//Cron que descarga los pagos de ROELA (20190513 hay pagos)
$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");

ini_set("display_errors", 1);
ini_set('max_execution_time', '10000');
$conf = new DATABASE_CONFIG();
$txt = "Inicio carga pagos ROELA - " . date("d/m/Y H:i:s") . "\n";
try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'], $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$txt .= "Conectado a la base de datos... \n";

$fecha = isset($_GET["fecha"]) ? date("Y-m-d", strtotime($_GET['fecha'])) : date("Y-m-d");

$xmlObtenerRendicion = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <ObtenerRendicion xmlns="http://www.bancoroela.com.ar/">
      <fecha_desde>' . $fecha . '</fecha_desde>
      <fecha_hasta>' . $fecha . '</fecha_hasta>
      <cuit_administrador></cuit_administrador>
      <nro_empresa></nro_empresa>
    </ObtenerRendicion>
  </soap12:Body>
</soap12:Envelope>';
//      <fecha_desde>' . $fecha . '</fecha_desde>
//      <fecha_hasta>' . $fecha . '</fecha_hasta>
$txt .= "Conectando al Webservice de Roela... \n";
$txt .= "Obteniendo Pagos... \n";

$curl = curl_init('https://www.bancoroela.com.ar/SiroWebService/IntegracionWebService.asmx');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlObtenerRendicion);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Authorization: Basic MzA3MTQ1NDg2Nzc6VG9jYWJhZWxwaWFub2NvbW91bmFuaW1hbDA1MDc',
    'SOAPAction: "http://www.bancoroela.com.ar/ObtenerRendicion"',
    'User-Agent: CEONLINE',
    'Connection: Keep-Alive',
    'Content-Type: application/soap+xml; charset=utf-8'
));
$resul = curl_exec($curl);
$txt .= "Pagos descargados... \n";
$cad = strip_tags($resul);
//debug(strip_tags($resul)); // jajajaja puto xml
//die;

if ($cad != "") {
    $arch = explode("\r\n", $cad); // leo todo el archivo
    if (count($arch) > 0 && $arch[0] != "") {// la ultima linea esta vacia
        $txt .= "Procesando " . (count($arch) - 1) . " pagos... \n";
        $sql = "INSERT INTO pagoselectronicos ";
        $sql .= "(`client_code`, `consorcio_code`, `propietario_code`, `prefijo`, `fecha`, `fecha_proc`, `medio`, `importe`, `comision`, `plataforma`, `cobranza_id`, `created`) VALUES (";
        $sql .= ":client_code,:consorcio_code,:propietario_code,:prefijo,:fecha,:fecha_proc,:medio,:importe,:comision,3,0,:created)";
        $q = $db->prepare($sql);
        $procesados = 0;
        foreach ($arch as $linea) {
            if ($linea == "") {
                continue; // la ultima linea esta vacia
            }
            $numeroconvenio = substr($linea, 84, 10);
            // obtengo cliente y consorcio con este numero de convenio
            $clienteconsorcio = $db->query("SELECT c.code as clientcode,co.code as consorciocode FROM clients c join consorcios co on c.id=co.client_id join plataformasdepagosconfigsdetalles p on co.id=p.consorcio_id where p.valor='$numeroconvenio' LIMIT 1")->fetch();
            if (empty($clienteconsorcio)) {
                continue; // el numero convenio no existe en la base de datos (consorcio sin configurar)
            }

            $clientcode = $clienteconsorcio['clientcode'];
            $consorciocode = $clienteconsorcio['consorciocode'];
            $fechapago = substr($linea, 0, 4) . "-" . substr($linea, 4, 2) . "-" . substr($linea, 6, 2);
            $importe = ((int) substr($linea, 24, 7)) / 100;
            // cambio la fecha de deposito a 3 dias hábiles ANTERIORES a la fecha de proceso
            // Porque en cobranzas automáticas le va a volver a sumar 3 dias habiles
            // si es lunes, le sumo 3 (da jueves) y le resto $cant[jueves]=5 para q me de la fecha de deposito informada por roela
            // D  = dia 0 = sumar 3 dias (miercoles)
            // L  = dia 1 = sumar 3 dias (jueves)
            // M  = dia 2 = sumar 3 dias (viernes)
            // Mi = dia 3 = sumar 5 dias (lunes)
            // J  = dia 4 = sumar 5 dias (martes)
            // V  = dia 5 = sumar 5 dias (miercoles)
            // S  = dia 6 = sumar 4 dias (miercoles)
            $cant = [3, 3, 3, 5, 5, 5, 4];
            $fechaacreditacion = substr($linea, 8, 4) . "-" . substr($linea, 12, 2) . "-" . substr($linea, 14, 2);
            $ff = date("Y-m-d", strtotime($fechaacreditacion . " +" . $cant[date("w", strtotime($fechaacreditacion))] . " days"));
            $fechaacreditacion = date("Y-m-d", strtotime($fechaacreditacion . " -" . $cant[date("w", strtotime($ff))] . " days"));
            //$datoscargados = $db->query("SELECT id FROM pagoselectronicos where client_code=$clientcode and consorcio_code=$consorciocode and "
            //                . "fecha='$fechapago' and fecha_proc='$fechaacreditacion' and importe=$importe LIMIT 1")->fetch();
            //if (!empty($datoscargados)) {
            //    continue; // ya fueron cargados los pagos de este dia
            //}
            $propietariocode = substr($linea, 36, 3);
            $prefijo = substr($linea, 44, 1);
            $medio = trim(substr($linea, 116, 3));
            $comision = 0;
            $created = date("Y-m-d H:i:s");

            $q->bindParam(":client_code", $clientcode);
            $q->bindParam(":consorcio_code", $consorciocode);
            $q->bindParam(":propietario_code", $propietariocode);
            $q->bindParam(":prefijo", $prefijo);
            $q->bindParam(":fecha", $fechapago);
            $q->bindParam(":fecha_proc", $fechaacreditacion);
            $q->bindParam(":medio", $medio);
            $q->bindParam(":importe", $importe);
            $q->bindParam(":comision", $comision);
            $q->bindParam(":created", $created);

            //Inserto el pago en pagoselectronicos
            if (!$q->execute()) {
                //debug($q->errorInfo());
            } else {
                $procesados++;
            }
        }
        $txt .= "Se insertaron $procesados Pagos... \n";
    }
} else {
    $txt .= "Sin Pagos para el dia de la fecha (vacío)... \n";
}
echo $procesados;
$txt .= "Fin del proceso... \n\n";
$txt .= file_get_contents($webroot . '/__logs/cron_pagosroela.txt');
file_put_contents($webroot . '/__logs/cron_pagosroela.txt', $txt);

function debug($data, $die = 0) {
    var_dump($data);
}
