<?php

/*
 * Métodos para utilizar la Plataforma de Pagos (PLAPSA) en CakePHP 2.x
 */
App::uses('AppModel', 'Model');
require __DIR__ . '/../vendor/autoload.php';

use phpseclib3\Net\SFTP;

class Plapsa extends AppModel {

    private static $url = 'files.plataformadepagos.com.ar';
    private static $puerto = '22';

    public static function enviarSaldosPlataformaPagos($client_code, $file) {
        try {
            $sftp = new SFTP(self::$url, self::$puerto);
            if (!$sftp->login(base64_decode('Q2VvT25saW5l'), base64_decode('T05jZW9MaW5lQEA='))) {
                print_r($sftp->getSFTPErrors());
                exit('No se pudo conectar con PLAPSA');
            }
            if (filesize(getcwd() . "/temp/" . $file) > 0) {
                $sftp->setTimeout(0);
                if (!$sftp->is_dir("cli$client_code/Envia/")) {
                    return ['e' => 1, 'd' => 'No existe el directorio del Cliente $client_code. Reclamar a PLAPSA'];
                }
                $fileh = fopen(getcwd() . "/temp/" . $file, 'r');
                $theData = fread($fileh, filesize(getcwd() . "/temp/" . $file));
                $sftp->put("cli$client_code/Envia/$file", $theData);
                fclose($fileh);
            } else {
                return ['e' => 1, 'd' => 'El archivo a enviar se encuentra vacío'];
            }

            //comparo el tamaño del archivo local con el subido, si son iguales esta todo bien!
            if ((filesize(getcwd() . "/temp/" . $file) == $sftp->filesize("cli$client_code/Envia/$file"))) {
                @unlink(getcwd() . "/temp/" . $file); // lo borro
                return ['e' => 0];
            } else {
                return ['e' => 1, 'd' => 'El archivo enviado y el remoto no coinciden, intente nuevamente'];
            }
        } catch (Exception $ex) {
            //echo "Hubo un error al subir el archivo.";
            return ['e' => 1, 'd' => 'Ocurrió un error desconocido al enviar el archivo a PLAPSA'];
        }
    }

    public static function getArchivoInformeDeuda($client_code, $file) {
        try {
            $sftp = new SFTP(self::$url, self::$puerto);
            if (!$sftp->login(base64_decode('Q2VvT25saW5l'), base64_decode('T05jZW9MaW5lQEA='))) {
                print_r($sftp->getSFTPErrors());
                exit('No se pudo conectar con PLAPSA');
            }
            $sftp->setTimeout(0); //evitar el error PHP Notice: Expected SSH_FX_DATA or SSH_FXP_STATUS en SFTP.php (phpseclib). https://github.com/phpseclib/phpseclib/issues/999
            $archivo = $sftp->get("cli$client_code/Envia/$file");
            if (empty($archivo)) {
                $archivo = $sftp->get("cli$client_code/Procesados/$file");
            }
            return $archivo;
        } catch (Exception $ex) {
            return '';
        }
    }

    public static function generarArchivoInformeDeuda($client_code, $consorcio_code, $liquidacion, $data, $ltprefijo, $usa2cuotas, $comisionPlataformaPago = 3.1, $minimo = 36.30, $identificadorcuentaROELA = '0000000000') {
        $fechaArchivo = date("YmdHis");

        /* num (se completan con cero a izquierda), char (se completan con espacios a derecha)
         * ---------------- HEADER ---------------- 19 CARACTERES
         * campo            tipo    long    desc
         * id registro      num     1       cod ident registro (al ser header, es 1)
         * cod de empresa   num     4       asignado por Plataforma de Pagos S.A. (cod_cliente de adm)
         * fecha archivo    num     14      fecha generacion archivo AAAAMMDDHHMMSS
         */

        $archivo = "1" . str_pad($client_code, 4, "0", STR_PAD_LEFT) . $fechaArchivo . PHP_EOL;

        /*
         * ---------------- DETALLE ---------------- 223 CARACTERES
         * campo            tipo    long    desc
         * id registro      num     1       cod ident registro (al ser detalle, es 5)
         * cod consor       num     4       codigo de consorcio (cod_pago_elect de consorcios)
         * cod unidad       num     5       codigo de unidad funcional
         * periodo          char    20      periodo o concepto liquidado (completar con espacios a derecha)
         * propietario      char    40      nombre propietario
         * ubicacion        char    15      ubicacion de unidad funcional
         * mail             char    40      mail del propietario
         * fecha 1º venc    num     8       fecha formato AAAAMMDD
         * importe 1º venc  num     11      importe 1º venc. 9 enteros 2 decimales sin punto ni coma
         * fecha 2º venc    num     8       fecha formato AAAAMMDD
         * importe 2º venc  num     11      importe 2º venc. 9 enteros 2 decimales sin punto ni coma
         * clave pago elect num     14      clave de Pago Electrónico segun diseño
         * cod barras       num     56      codigo de barras segun diseño
         */
        $informamailpropietarios = false;
        if (isset($data['plataforma']['Plataformasdepagosconfig']['informamailpropietarios']) && $data['plataforma']['Plataformasdepagosconfig']['informamailpropietarios'] == 1) {
            $informamailpropietarios = true;
        }
        $total1venc = $total2venc = $cantUnidadesInformadas = 0;
        foreach ($data['prop'] as $p) {
            $totalexpensa = floor($data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes']);
            $email = "";
            if ($informamailpropietarios && !empty($p['email'])) {// informo el email si el administrador firmó convenio con PLAPSA autorizando el envio
                $e = explode(',', $p['email']);
                $email = strlen($e[0]) > 40 ? '' : $e[0]; // hasta 40 caracteres, sino no informo
            }
            // se informan solo los saldos > 0 y menores a 100000/1.031~96993.21 (comision 3.1). PLAPSA NO SOPORTA IMPORTES > a 100mil
            if ($totalexpensa > 0 && $totalexpensa < 100000 / (1 + ($comisionPlataformaPago / 100))) {
                if ($usa2cuotas) {
                    $comisionexpensa = round((($totalexpensa / 2)) * ($comisionPlataformaPago / 100), 2);
                    $comisionPrimerVenc = round($comisionexpensa < $minimo ? $totalexpensa / 2 + $minimo : $totalexpensa / 2 + $comisionexpensa, 2);
                    $comisionSegundoVenc = round($comisionexpensa < $minimo ? $totalexpensa / 2 + $minimo : $totalexpensa / 2 + $comisionexpensa, 2);
                    $archivo .= "5" . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . $ltprefijo . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // 5 (detalle), cod_pago_elect y id unidad (columna codigo)
                    $archivo .= substr(str_pad("C2 " . self::hyphenize($liquidacion['periodo']), 20, " ", STR_PAD_RIGHT), 0, 20); // le agrego el concepto (periodo)
                    $archivo .= str_pad(substr(self::hyphenize($p['name']), 0, 39), 40, " ", STR_PAD_RIGHT); // le agrego el nombre del propietario
                    $archivo .= str_pad(self::hyphenize(substr($p['unidad'], 0, 15)), 15, " ", STR_PAD_RIGHT); // le agrego la ubicacion de la unidad, 1ºA, LOCAL 1, etc
                    $archivo .= str_pad($email, 40, " ", STR_PAD_RIGHT);
                    $archivo .= date("Ymd", strtotime($liquidacion['limite'])) . str_pad($comisionPrimerVenc * 100, 11, "0", STR_PAD_LEFT); // 1º venc y total
                    $archivo .= date("Ymd", strtotime($liquidacion['limite'])) . str_pad($comisionSegundoVenc * 100, 11, "0", STR_PAD_LEFT); // 2º venc y total
                    $archivo .= self::generaClavePagoElectronico($consorcio_code, $p["code"], $ltprefijo, $client_code); // le agrego la clave de pago electrónico
                    $archivo .= self::generaCodigoBarrasV2("2634", $client_code, $consorcio_code, $ltprefijo, $p["code"], $liquidacion['limite'], $liquidacion['limite'], $comisionPrimerVenc, $comisionSegundoVenc);
                    $archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    $total2venc += $comisionSegundoVenc;
                    $cantUnidadesInformadas++;

                    $comisionexpensa = round((($totalexpensa / 2)) * ($comisionPlataformaPago / 100), 2);
                    $comisionPrimerVenc = round($comisionexpensa < $minimo ? $totalexpensa / 2 + $minimo : $totalexpensa / 2 + $comisionexpensa, 2);
                    $comisionSegundoVenc = round($comisionexpensa < $minimo ? $totalexpensa / 2 + $minimo : $totalexpensa / 2 + $comisionexpensa, 2);
                    $archivo .= "5" . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . $ltprefijo . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // 5 (detalle), cod_pago_elect y id unidad (columna codigo)
                    $archivo .= substr(str_pad("C1 " . self::hyphenize($liquidacion['periodo']), 20, " ", STR_PAD_RIGHT), 0, 20); // le agrego el concepto (periodo)
                    $archivo .= str_pad(substr(self::hyphenize($p['name']), 0, 39), 40, " ", STR_PAD_RIGHT); // le agrego el nombre del propietario
                    $archivo .= str_pad(self::hyphenize(substr($p['unidad'], 0, 15)), 15, " ", STR_PAD_RIGHT); // le agrego la ubicacion de la unidad, 1ºA, LOCAL 1, etc
                    $archivo .= str_pad($email, 40, " ", STR_PAD_RIGHT);
                    $archivo .= date("Ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionPrimerVenc * 100, 11, "0", STR_PAD_LEFT); // 1º venc y total
                    $archivo .= date("Ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionSegundoVenc * 100, 11, "0", STR_PAD_LEFT); // 2º venc y total
                    $archivo .= self::generaClavePagoElectronico($consorcio_code, $p["code"], $ltprefijo, $client_code);
                    $archivo .= self::generaCodigoBarrasV2("2634", $client_code, $consorcio_code, $ltprefijo, $p["code"], $liquidacion['vencimiento'], $liquidacion['vencimiento'], $comisionPrimerVenc, $comisionSegundoVenc);
                    $archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    $total2venc += $comisionSegundoVenc;
                    $cantUnidadesInformadas++;
                } else {
                    $comisionexpensa = round($totalexpensa * ($comisionPlataformaPago / 100), 2);
                    $comisionPrimerVenc = round($comisionexpensa < $minimo ? $totalexpensa + $minimo : $totalexpensa + $comisionexpensa, 2);
                    $comisionSegundoVenc = round($comisionexpensa < $minimo ? $totalexpensa + $minimo : $totalexpensa + $comisionexpensa, 2);
                    $archivo .= "5" . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . $ltprefijo . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // 5 (detalle), cod_pago_elect y id unidad (columna codigo)
                    $archivo .= substr(str_pad(self::hyphenize($liquidacion['periodo']), 20, " ", STR_PAD_RIGHT), 0, 20); // le agrego el concepto (periodo)
                    $archivo .= str_pad(substr(self::hyphenize($p['name']), 0, 39), 40, " ", STR_PAD_RIGHT); // le agrego el nombre del propietario
                    $archivo .= str_pad(self::hyphenize(substr($p["unidad"], 0, 15)), 15, " ", STR_PAD_RIGHT); // le agrego la ubicacion de la unidad, 1ºA, LOCAL 1, etc
                    $archivo .= str_pad($email, 40, " ", STR_PAD_RIGHT);
                    $archivo .= date("Ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionPrimerVenc * 100, 11, "0", STR_PAD_LEFT); // 1º venc y total
                    $archivo .= date("Ymd", strtotime($liquidacion['limite'])) . str_pad($comisionSegundoVenc * 100, 11, "0", STR_PAD_LEFT); // 2º venc y total
                    $archivo .= self::generaClavePagoElectronico($consorcio_code, $p["code"], $ltprefijo, $client_code); // le agrego la clave de pago electrónico
                    $archivo .= self::generaCodigoBarrasV2("2634", $client_code, $consorcio_code, $ltprefijo, $p["code"], $liquidacion['vencimiento'], $liquidacion['limite'], $comisionPrimerVenc, $comisionSegundoVenc);
                    $archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    $total2venc += $comisionSegundoVenc;
                    $cantUnidadesInformadas++;
                }
            }
        }

        /*
         * ---------------- TRAILER ---------------- 47 CARACTERES
         * campo            tipo    long    desc
         * id registro      num     1       cod ident registro (al ser trailer, es 9)
         * cod de empresa   num     4       asignado por Plataforma de Pagos S.A. (cod_cliente de adm)
         * fecha archivo    num     14      fecha generacion archivo AAAAMMDDHHMMSS
         * cant registros   num     6       cant de detalles
         * totales 1º venc  num     11      totales importes 1º vencimiento 9 enteros 2 decimales sin punto ni coma
         * totales 2º venc  num     11      totales importes 1º vencimiento 9 enteros 2 decimales sin punto ni coma
         */

        $archivo .= "9" . str_pad($client_code, 4, "0", STR_PAD_LEFT) . $fechaArchivo . str_pad($cantUnidadesInformadas, 6, "0", STR_PAD_LEFT);
        $archivo .= str_pad(round($total1venc, 2) * 100, 11, "0", STR_PAD_LEFT) . str_pad(round($total2venc, 2) * 100, 11, "0", STR_PAD_LEFT); // totales 1º y 2º venc
        // guardo el archivo
        $nombreArchivo = "DI_" . str_pad($client_code, 4, "0", STR_PAD_LEFT) . "_" . $fechaArchivo . ".txt";
        $hh = fopen(APP . DS . WEBROOT_DIR . DS . "temp/" . $nombreArchivo, "w+");
        fwrite($hh, $archivo);
        fclose($hh);
        return $nombreArchivo;
    }

    public static function generaClavePagoElectronico($codconsorcio, $codunidad, $prefijounidad = '', $codcliente = '', $identificadorcuentaROELA = '0000000000') {
        //codigo cliente (4 caracteres), codigo de consorcio (4 caracteres), codigo unidad (5 caracteres), dígito verificador
        $clave = "";
        if ($codcliente != "0") {
            $clave = str_pad($codcliente, 4, "0", STR_PAD_LEFT) . str_pad($codconsorcio, 4, "0", STR_PAD_LEFT) . $prefijounidad . str_pad($codunidad, 4, "0", STR_PAD_LEFT);
            $clave .= self::obtieneDigitoVerificador($clave, 13);
        }
        return $clave;
    }

    public static function obtieneDigitoVerificador($codbarras, $hasta) {
        $secuencia = '13579357935793579357935793579357935793579357935793579357935793579357935793579357935793579';
        $checksum = $cont = 0;
        while ($cont < $hasta) {
            $checksum += substr($secuencia, $cont, 1) * substr($codbarras, $cont, 1);
            $cont += 1;
        }
        return (floor($checksum / 2) % 10); // dígito $hasta+1
    }

    public static function generaCodigoBarras($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2, $datointerno = '111111') {
        if ($prefijo == "2634") { // es un codigo de barras de PLAPSA (56 caracteres)
            return self::generaCodigoBarrasV2($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2, $datointerno);
        }
        $banc_barcode = $prefijo . str_pad($cod_cliente, 4, "0", STR_PAD_LEFT);
        $banc_barcode .= str_pad($cod_consor, 4, "0", STR_PAD_LEFT) . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT);
        $vto1 = strtotime($vto1);
        $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto1), date("d", $vto1), date("Y", $vto1)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto1)) + 1;
        $fecha_juliana = substr(date("Y", $vto1), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
        $banc_barcode .= str_pad(round($banco_total * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;
        $vto2 = strtotime($vto2);
        $cant_dias = cal_to_jd(CAL_GREGORIAN, date("m", $vto2), date("d", $vto2), date("Y", $vto2)) - cal_to_jd(CAL_GREGORIAN, 1, 1, date("Y", $vto2)) + 1;
        $fecha_juliana = substr(date("Y", $vto2), 2, 2) . str_pad($cant_dias, 3, "0", STR_PAD_LEFT);
        $banc_barcode .= str_pad(round($banco_total2 * 100), 8, "0", STR_PAD_LEFT) . $fecha_juliana;

        return ($banc_barcode . self::obtieneDigitoVerificador($banc_barcode, 41));
    }

    public static function generaCodigoBarrasV2($prefijo, $cod_cliente, $cod_consor, $ltprefijo, $cod_unidad, $vto1, $vto2, $monto1, $monto2 = 0, $datointerno = '111111') {
        $cant = (new DateTime($vto1))->diff(new DateTime($vto2)); // dias hasta el prox vencimiento (dejar aca porq despues vto1 y vto2 cambian
        $codbarras = $prefijo . str_pad($cod_consor, 4, "0", STR_PAD_LEFT) . $ltprefijo . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT); // 2634 consor unidad
        $codbarras .= date("ymd", strtotime($vto1)) . str_pad(round($monto1 * 100), 7, "0", STR_PAD_LEFT); // fecha monto
        $codbarras .= str_pad($cant->format('%a'), 2, "0", STR_PAD_LEFT);
        $codbarras .= str_pad(round(($monto2 == 0 ? $monto1 : $monto2) * 100), 7, "0", STR_PAD_LEFT); // Si monto2 es cero, entonces uso monto1
        $codbarras .= "000000000"; // dias hasta 3º vencimiento y monto 3º vencimiento (no tiene, completo con 00 y 0000000)
        $codbarras .= $datointerno . str_pad($cod_cliente, 4, "0", STR_PAD_LEFT); // "dato interno". 4to es rapipago (111011)
        $codbarras .= self::obtieneDigitoVerificador($codbarras, 54);
        $codbarras .= self::obtieneDigitoVerificador($codbarras, 55);
        return $codbarras;
    }

    public static function hyphenize($string) {
        $dict = ["I'm" => "I am", "thier" => "their"];
        return preg_replace(
                ['#[^A-Za-z0-9\. -]+#'], [' '],
                // the full cleanString() can be download from http://www.unexpectedit.com/php/php-clean-string-of-utf8-chars-convert-to-similar-ascii-char
                self::cleanString(
                        str_replace(// preg_replace to support more complicated replacements
                                array_keys($dict), array_values($dict), urldecode($string)
                        )
                )
        );
    }

    public static function cleanString($text) {
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

}
