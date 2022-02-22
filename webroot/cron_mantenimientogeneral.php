<?php

/*
 * Cron utilizado para tareas de mantenimiento en horarios q no se utilice el sistema
 */
$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");
ini_set("display_errors", 1);
ini_set('max_execution_time', '10000');
$conf = new DATABASE_CONFIG();
$resul = "Inicio del mantenimiento del Sistema CEO - " . date("d/m/Y H:i:s") . "\n";
try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'], $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    $resul .= "Error!: " . $e->getMessage() . "<br/>";
    die();
}
$resul .= "Conectado a la base de datos\n";

// borro pagos electronicos anteriores a 2 meses de la fecha actual
$query = $db->prepare('delete from pagoselectronicos where date(fecha_proc)<="' . date("Y-m-d", strtotime("-3 months")) . '"');
$query->execute();
$resul .= "Se eliminaron " . $query->rowCount() . " Pagos electronicos\n";

// borro html de emails enviados en comunicaciones
$dir = $webroot . "/emails";
$resul .= "Se eliminaron " . borrarRecursivo($dir, 60) . " html de emails de la carpeta /emails/\n";

// borro archivos de pagos PLAPSA de las carpetas /plapsa/xxxx/
$dir = $webroot . "/plapsa";
$resul .= "Se eliminaron " . borrarRecursivo($dir, 60) . " archivos de pagos PLAPSA de las carpetas /plapsa/xxxx/\n";

// borro archivos de vasini florio de más de 180 dias (6 meses). Estos archivos los generan con el sistema Quality y se importan en CEONLINE
$dir = $webroot . "/files/82/consultas";
$resul .= "Se eliminaron " . borrarRecursivo($dir, 180) . " archivos de Vasini Florio de las carpetas /files/82/consultas/\n";

// borro consultas de las administraciones anteriores a 4 meses de la fecha actual
// a partir del 20/09/2019 se guardan todas las consultas para siempre
//$query = $db->prepare('delete from consultas where date(created)<="' . date("Y-m-d", strtotime("-4 months")) . '"');
//$query->execute();
//$resul .= "Se eliminaron " . $query->rowCount() . " Consultas de las administraciones\n";
// borro adjuntos de las liquidaciones de más de 8 meses
$cant = 0;
foreach ($db->query('SELECT a.id,a.ruta,c.client_id FROM adjuntos a join liquidations l on a.liquidation_id=l.id join consorcios c on c.id=l.consorcio_id where date(a.created)<="' . date("Y-m-d", strtotime("-8 months")) . '"') as $row) {
    if (is_file($webroot . '/files/' . $row['client_id'] . '/' . basename($row['ruta']))) {
        unlink($webroot . '/files/' . $row['client_id'] . '/' . basename($row['ruta']));
    }
    $db->query('delete from adjuntos where id=' . $row['id'] . ' limit 1');
    $cant++;
}
$resul .= "Se eliminaron " . $cant . " adjuntos de las Liquidaciones (y sus archivos fisicos asociados)\n";

// borro adjuntos de las consultas de administraciones anteriores a 4 meses de la fecha actual
$cant = 0;
foreach ($db->query('select id,client_id,ruta from consultasadjuntos where date(created)<="' . date("Y-m-d", strtotime("-4 months")) . '"') as $row) {
    if (is_file($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']))) {
        unlink($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']));
        $cant += borrarRecursivo($webroot . '/files/' . $row['client_id'] . '/consultas/', 240);
    }
    $db->query('delete from consultasadjuntos where id=' . $row['id'] . ' limit 1');
    $cant++;
}
$resul .= "Se eliminaron " . $cant . " adjuntos de las Consultas (y sus archivos fisicos asociados) de las administraciones\n";

// borro consultas de los Propietarios anteriores a 4 meses de la fecha actual
//$query = $db->prepare('delete from consultaspropietarios where date(created)<="' . date("Y-m-d", strtotime("-4 months")) . '"');
//$query->execute();
//$resul .= "Se eliminaron " . $query->rowCount() . " Consultas de los propietarios\n";
// borro adjuntos de las consultas de Propietarios anteriores a 4 meses de la fecha actual
$cant = 0;
foreach ($db->query('select id,client_id,ruta from consultaspropietariosadjuntos where date(created)<="' . date("Y-m-d", strtotime("-4 months")) . '"') as $row) {
    if (is_file($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']))) {
        unlink($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']));
    }
    $db->query('delete from consultaspropietariosadjuntos where id=' . $row['id'] . ' limit 1');
    $cant++;
}
$resul .= "Se eliminaron " . $cant . " adjuntos de las Consultas (y sus archivos fisicos asociados) de los Propietarios\n";

// borro adjuntos de los informes de pagos de los Propietarios anteriores a 3 meses de la fecha actual
$cant = 0;
foreach ($db->query('select ip.id,i.client_id,ip.ruta from informepagosadjuntos ip join informepagos i on i.id=ip.informepago_id where date(ip.created)<="' . date("Y-m-d", strtotime("-3 months")) . '"') as $row) {
    if (is_file($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']))) {
        unlink($webroot . '/files/' . $row['client_id'] . '/consultas/' . basename($row['ruta']));
    }
    $db->query('delete from informepagosadjuntos where id=' . $row['id'] . ' limit 1');
    $cant++;
}
$resul .= "Se eliminaron " . $cant . " adjuntos de los Informes de pagos (y sus archivos fisicos asociados) de los Propietarios\n";

// borro facturas digitales de mas de 8 meses de antiguedad
$cant = 0;
foreach ($db->query('select pfa.id,p.client_id,pfa.ruta from proveedorsfacturasadjuntos pfa join proveedorsfacturas pf on pfa.proveedorsfactura_id=pf.id join proveedors p on pf.proveedor_id=p.id where date(pfa.created)<="' . date("Y-m-d", strtotime("-8 months")) . '"') as $row) {
    if (is_file($webroot . '/files/' . $row['client_id'] . '/' . basename($row['ruta']))) {
        unlink($webroot . '/files/' . $row['client_id'] . '/' . basename($row['ruta']));
    }
    $db->query('delete from proveedorsfacturasadjuntos where id=' . $row['id'] . ' limit 1');
    $cant++;
}
$resul .= "Se eliminaron " . $cant . " facturas digitales (y sus archivos fisicos asociados) de las administraciones\n";

// borro auditoria de más de 3 meses
$query = $db->prepare('delete a,ad from audits a join audit_deltas ad on a.id=ad.audit_id where date(a.created)<="' . date("Y-m-d", strtotime("-3 months")) . '"');
$query->execute();
$resul .= "Se eliminaron " . $query->rowCount() . " registros de auditoría\n";

// borro avisos de mas de 3 meses sin modificarse
$query = $db->prepare('delete from avisos where date(modified)<="' . date("Y-m-d", strtotime("-3 months")) . '"');
$query->execute();
$resul .= "Se eliminaron " . $query->rowCount() . " avisos de mas de 3 meses sin modificarse\n";

// borro avisosblacklists de mas de 3 meses sin modificarse
$query = $db->prepare('delete from avisosblacklists where date(modified)<="' . date("Y-m-d", strtotime("-3 months")) . '"');
$query->execute();
$resul .= "Se eliminaron " . $query->rowCount() . " avisosblacklists de mas de 3 meses sin modificarse\n";

// deshabilito usuarios (los nuestros no) cuyo ultimo logueo fue hace más de 4 meses. O si lastseen es null y created < 4 meses
$query = $db->prepare('update users set enabled=0 where (lastseen<="' . date("Y-m-d 00:00:00", strtotime("-4 months")) . '" or (created<="' . date("Y-m-d 00:00:00", strtotime("-4 months")) . '" and lastseen is null)) ' . " and username not in ('ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster')");
$query->execute();
$resul .= "Se deshabilitaron " . $query->rowCount() . " usuarios de mas de 4 meses sin loguearse\n";

// borro los html 

// el primero del mes comprimo los txt de los envios de sendgrid
if (date("d") == 1) {
    $archivomesanterior = "eventossendgrid_" . date("Ym", strtotime(date("Y-m-d") . " -1 month")) . ".txt";
    if (file_exists("$webroot/__logs/$archivomesanterior")) {
        $x = shell_exec("sudo 7z a $webroot/__logs/$archivomesanterior.7z $webroot/__logs/$archivomesanterior && sudo rm $webroot/__logs/$archivomesanterior");
        $resul .= "Se comprimió el archivo $archivomesanterior de envio de logs de sendgrid.";
    }
}

// guardo el log de las tareas realizadas
$resul .= "*******************\n";

echo $resul;

$resul .= file_get_contents($webroot . '/__logs/cron_mantenimientogenerallog.txt');
file_put_contents($webroot . '/__logs/cron_mantenimientogenerallog.txt', $resul);

function borrarRecursivo($dir, $dias = 15) {
    $cant = 0;
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == ".." || filetype($dir . "/" . $object) != "dir") {
                continue;
            }
            $archivos = scandir($dir . "/" . $object);
            foreach ($archivos as $arch) {
                if ($arch == "." || $arch == ".." || filetype(realpath($arch)) == "dir") {
                    continue;
                }
                if (filemtime($dir . "/" . $object . "/" . $arch) <= time() - $dias * 24 * 60 * 60) { // 15 dias
                    if (!is_dir($dir . "/" . $object . "/" . $arch)) {
                        unlink($dir . "/" . $object . "/" . $arch);
                        $cant++;
                    }
                }
            }
            reset($archivos);
        }
        reset($objects);
    }
    return $cant;
}
