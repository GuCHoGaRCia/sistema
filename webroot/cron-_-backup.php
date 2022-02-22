<?php

/*
 * Cron para backup de base de datos de CEONLINE
 */
set_time_limit(10000);
$time_start = microtime(true);
$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");
ini_set("display_errors", 1);
$conf = new DATABASE_CONFIG();
$resul = "";
try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'] . ';charset=utf8mb4', $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    die("ef");
}

// borro backups viejos

$dir = $webroot . "/backups!!!!";
$resul .= "\nSe eliminaron " . borrarRecursivo($dir, 4) . " backups viejos\n";
//$n = date("YmdHis");
$database = $conf->default['database'];
$user = $conf->default['login'];
$pass = $conf->default['password'];
$ruta = $webroot . '/backups!!!!/' . date("YmdH") . "/";
$file = "$ruta";
if (!is_dir($ruta)) {
    mkdir($ruta, 0775, true);
}
if (!is_dir($ruta . "struct")) {
    mkdir($ruta . "struct", 0775, true);
}
$statement = $db->prepare("SHOW TABLES");
$statement->execute();
$tables = $statement->fetchAll(PDO::FETCH_NUM);

// dump de la estructura solamente
$archivo = $file . "struct/estructura.sql.gz"; //solo la estructura (--no-data)
$resul .= shell_exec("mysqldump --compress --compact --no-data --add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false -u $user -p$pass $database | pigz -6 > $archivo");

//// hago el dump de cada tabla y comprimo todos los archivos //D:/Xampp/mysql/bin/mysqldump sin data
foreach ($tables as $table) {
    $tabla = $table[0];
    if ($tabla == "audits" || $tabla == "audit_deltas") {// como mierda la divido a esta?
        $archivo = $file . $tabla . "1.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='created>date(date_add(now(), interval -20 day))' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "2.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='created between date(date_add(now(), interval -45 day)) and date(date_add(now(), interval -20 day))' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "3.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='created between date(date_add(now(), interval -70 day)) and date(date_add(now(), interval -46 day))' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "4.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='created<date(date_add(now(), interval -71 day))' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
    } else if ($tabla == "resumenes") {
        $archivo = $file . $tabla . "1.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='id between 0 and 6000' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "2.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='id between 6001 and 10000' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "3.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='id between 10001 and 16000' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
        $archivo = $file . $tabla . "4.sql.gz";
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false --where='id>16000' -u $user -p$pass $database $tabla | pigz -6 > $archivo");
    } else {
        $archivo = $file . $tabla . ".sql.gz"; //solo la data (--no-create-info)
        $resul .= shell_exec("mysqldump --compress --no-create-info --compact --skip-add-drop-table --skip-add-locks --skip-disable-keys --default-character-set=utf8mb4 --single-transaction --quick --lock-tables=false -u $user -p$pass $database $tabla | pigz -6 > $archivo");
    }
    set_time_limit(10000);
}

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
$resul .= 'Tiempo total: ' . $execution_time . ' segundos';

echo $resul;
$resul .= file_get_contents($webroot . '/__logs/cron-_-backup.txt');
file_put_contents($webroot . '/__logs/cron-_-backup.txt', $resul);

function borrarRecursivo($dir, $dias = 15) {
    $cant = 0;
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if (filetype($dir . "/" . $object) == "dir" && filemtime($dir . "/" . $object) <= time() - $dias * 24 * 60 * 60) {
                shell_exec("sudo rm -R " . $dir . "/" . $object);
                $cant++;
            }
        }
        reset($objects);
    }
    return $cant;
}
