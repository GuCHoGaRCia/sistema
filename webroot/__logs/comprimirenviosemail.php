<?php

/*
 * Cron para comprimir reportes de envios x email de Sendgrid
 */
date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set("display_errors", 1);
set_time_limit(10000);
$archivomesanterior = "eventossendgrid_" . date("Ym", strtotime(date("Y-m-d") . " -1 month")) . ".txt";
if (file_exists("$archivomesanterior")) {
    $resul = shell_exec("sudo 7z a $archivomesanterior.7z $archivomesanterior && sudo rm $archivomesanterior");
}

//$resul = shell_exec("7za a -t7z -mx9 eventossendgrid_201811.txt.7z eventossendgrid_201811.txt");//no anda

