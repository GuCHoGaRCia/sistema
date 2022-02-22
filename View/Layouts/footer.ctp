<?php

echo '<footer>&#169; <span style="cursor:pointer;text-decoration:underline" onClick="javascript:window.open(\'https://web.ceonline.com.ar/\');">CEONLINE</span> '.date("Y");

if (Configure::read('debug') > 0) {
    echo " - " . __("P&aacute;gina generada en ") . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000) . "ms.";
}

echo "</footer>";
