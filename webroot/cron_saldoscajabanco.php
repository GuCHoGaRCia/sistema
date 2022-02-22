<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');
ini_set("display_errors", 0);
ini_set('max_execution_time', '10000');
if ($_SERVER['DOCUMENT_ROOT'] !== "D:/Dropbox/dev") {
    $file = file_get_contents("https://ceonline.com.ar/sistema/panel/clients/saldosResumenCajaBanco");
} else {
    echo "es localhost! ejecutar desde el panel";
}


// Get cURL resource
//$curl = curl_init();
// Set some options - we are passing in a useragent too here
//curl_setopt_array($curl, array(
//    CURLOPT_RETURNTRANSFER => 1,
//    CURLOPT_URL => 'https://ceonline.com.ar/sistema/panel/clients/saldosResumenCajaBanco',
//));
// Send the request & save response to $resp
//$resp = curl_exec($curl);
// Close request to clear up some resources
//curl_close($curl);
