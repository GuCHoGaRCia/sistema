<?php

/*
 * Métodos para utilizar la Plataforma ROELA en CakePHP 2.x
 * URL: https://www.bancoroela.com.ar/SiroWebService/IntegracionWebService.asmx (SOAP 1.2)
 */
App::uses('AppModel', 'Model');

class Roela extends AppModel {

    public static function enviarSaldosPlataformaPagos($client_code, $file) {
        try {
            if (file_exists(APP . DS . WEBROOT_DIR . DS . "temp/" . $file)) {
                $contenido = file_get_contents(APP . DS . WEBROOT_DIR . DS . "temp/" . $file);
                unlink(APP . DS . WEBROOT_DIR . DS . "temp/" . $file);
                if ($contenido !== "") {
                    $xmlProcesarBasePagos = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <ProcesarBasePagos xmlns="http://www.bancoroela.com.ar/">
      <base_pagos>' . $contenido . '</base_pagos>
      <confirmar_automaticamente>true</confirmar_automaticamente>
    </ProcesarBasePagos>
  </soap12:Body>
</soap12:Envelope>';
                    $curl = curl_init('https://www.bancoroela.com.ar/SiroWebService/IntegracionWebService.asmx');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlProcesarBasePagos);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Authorization: Basic MzA3MTQ1NDg2Nzc6VG9jYWJhZWxwaWFub2NvbW91bmFuaW1hbDA1MDc',
                        'User-Agent: CEONLINE',
                        'Connection: Keep-Alive',
                        'Content-Type: application/soap+xml; charset=utf-8'
                    ));
                    $resul = curl_exec($curl);
                    curl_close($curl);
                    return strip_tags($resul); // al quitarle los tags, si esta todo bien, me devuelve un numero de transaccion
                }
            }
        } catch (Exception $ex) {
            return false;
        }
        return false;
    }

    public static function getArchivoInformeDeuda($client_code, $file) {
        try {
            if (is_numeric($file)) {// es un numero de transaccion de roela, obtenido al enviar los saldos a PMC y Link
                $xmlProcesarBasePagos = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <ConsultarBasePagos xmlns="http://www.bancoroela.com.ar/">
      <nro_transaccion>' . $file . '</nro_transaccion>
      <obtener_informacion_base>true</obtener_informacion_base>
    </ConsultarBasePagos>
  </soap12:Body>
</soap12:Envelope>';
                $curl = curl_init('https://www.bancoroela.com.ar/SiroWebService/IntegracionWebService.asmx');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlProcesarBasePagos);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Authorization: Basic MzA3MTQ1NDg2Nzc6VG9jYWJhZWxwaWFub2NvbW91bmFuaW1hbDA1MDc',
                    'User-Agent: CEONLINE',
                    'Connection: Keep-Alive',
                    'Content-Type: application/soap+xml; charset=utf-8'
                ));
                $resul = curl_exec($curl);
                curl_close($curl);
                return $resul;
            }
        } catch (Exception $ex) {
            return false;
        }
        return false;
    }

    public static function generarArchivoInformeDeuda($client_code, $consorcio_code, $liquidacion, $data, $ltprefijo, $usa2cuotas, $comisionPlataformaPago = 3.1, $minimo = 18.15, $identificadorcuentaROELA = []) {
        if (strlen($identificadorcuentaROELA) != 10) {
            die("El Convenio de Roela $identificadorcuentaROELA no posee 10 números");
        }
        // 131 caracteres
        // Campo                     Tipo de Dato    Longitud    Posición    Descripción
        // Identificador de registro Alfanumérico    13          1 – 13      Fijo: HRFACTURACION
        // Código del Ente           Alfanumérico    3           14 – 16     Completar con espacios.
        // Fecha de proceso          Numérico        6           17 – 22     Año Año Mes Mes Día Día. Ej: el 07/04/2014, poner 140407
        // Lote                      Numérico        5           23 – 27     00001
        // Filler                    Alfanumérico    104         28 – 131    Completar con espacios. 

        $archivo = "HRFACTURACION" . "   " . date("ymd") . "00001" . str_pad("", 104, " ", STR_PAD_LEFT)/* . PHP_EOL */;

        // 131 caracteres
        // Campo                       Tipo de Dato    Longitud    Posición    Descripción
        // Identificador de Deuda      Numérico        5           1 – 5       Utilizar los 5 dígitos de la siguiente forma: El primer dígito para identificar elconcepto a facturar (si no se va autilizar, completar con cero). Si seutiliza el código de barras SIRO, enesta posición debe informarse el“Identificador de Concepto” de esediseño de registro. Los cuatro dígitos restantes paraidentificar el mes y el año defacturación con el formato MMAA.Por ejemplo si el Concepto 1 es “deuda” y el 2es “intereses”, si se quiere informar la deudade agosto de 2014 este campo se completacon: 10814; y si se quiere informar el interésde ese mismo período, se completa con20814En caso que el ente trabaja con un soloconcepto y no necesita discriminarlos,informará: 00814
        // Identificador de concepto   Numérico        3           6 – 8       FIJO: 001 
        // Identificador usuario o CPE Numérico        19          9 – 27      Los 19 dígitos se forman de la siguiente manera: Los primeros nueve dígitos son para identificar a los titulares de las obligaciones. Si utiliza el código de barras de SIRO, se competa la primera posición con cero y las 8 restantes con el “identificador de usuario” del diseño de registro de código de barra SIRO (salvo que se utilice el identificador de concepto como un campo adicional para ganar un dígito mas en el identificador de usuario). No puede utilizarse aquí un número de comprobante. Las últimas 10 posiciones son el “identificador de cuenta” otorgado por banco roela (presente en el diseño de registro del Código de Barras SIRO)
        // Fecha 1º vencimiento        Numérico        6           28 – 33     Año Año Mes Mes Día Día. Ej: el 07/04/2014, poner 140407.
        // Importe 1º vencimiento      Numérico        12 (10 y 2) 34 – 45     Importe de primer vencimiento.
        // Fecha 2º vencimiento        Numérico        6           46 – 51     Año Año Mes Mes Día Día. Si se usa, debe ser mayor al campo “Fecha 1º vencimiento”. Si no usa segundo vencimiento, completar con 0 (ceros)
        // Importe 2º vencimiento      Numérico        12 (10 y 2) 52 – 63     Si se usa, no puede ser menor al campo “Importe 1º vencimiento“. Si no usa segundo vencimiento, completar con 0 (ceros)
        // Fecha 3º vencimiento        Numérico        6           64 – 69     Año Año Mes Mes Día Día. Si se usa, debe ser mayor al campo “Fecha 2º vencimiento”. Si no usa tercer vencimiento, completar con 0(ceros).
        // Importe 3º vencimiento      Numérico        12 (10 y 2) 70 – 81     Si se usa, no puede ser menor al campo “Importe 2º vencimiento“. Si no usa tercer vencimiento, completar con 0 (ceros)
        // Mensaje en ticket           Alfanumérico    50          82 – 131    Datos a informar en el ticket de pago. Utilizarde la siguiente manera: Utilizar las primeras 15 posicionespara informar el Ente al cual se estáefectuando el pago, abreviando elnombre de modo que el pagadorpueda identificar inequívocamenteel Ente. Utilizar las 25 posiciones restantespara información secundaria, comoel concepto que se esta pagando y /o el período. Las últimas 10 posicionescompletarlas con espaciosCompletar solo con letras mayúsculas (sinacentos, ni “ñ”) y números. No admitecaracteres especiales (puntos, comas,paréntesis, etc.)
        $datoscliente = $data['client']['Client'];
        $total1venc = $total2venc = $cantUnidadesInformadas = 0;
        foreach ($data['prop'] as $p) {
            $totalexpensa = floor($data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes']);
            if ($totalexpensa > 0) {
                if ($usa2cuotas) {
                    $comisionPrimerVenc = round($totalexpensa / 2, 2);
                    //$comisionSegundoVenc = round($totalexpensa / 2, 2);
                    $archivo .= date("0md"); // para q me tome las dos cuotas, el id de deuda debe ser distinto, sino tira error!
                    $archivo .= "001" . $ltprefijo . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // Identificador usuario o CPE primera parte
                    $archivo .= str_pad($identificadorcuentaROELA, 10, "0", STR_PAD_LEFT); // Identificador usuario o CPE segunda parte
                    $archivo .= date("ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionPrimerVenc * 100, 12, "0", STR_PAD_LEFT); // 1º venc y total
                    //$archivo .= date("ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionSegundoVenc * 100, 12, "0", STR_PAD_LEFT); // 2º venc y total
                    $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 2º venc y total, no se usa
                    $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 3º venc y total, no se usa
                    $archivo .= substr(str_pad(strtoupper(self::hyphenize($datoscliente['name'])), 15, " ", STR_PAD_RIGHT), 0, 15); // datos del cliente
                    $archivo .= substr(str_pad("CUOTA 1 " . strtoupper(self::hyphenize($liquidacion['periodo'])), 25, " ", STR_PAD_RIGHT), 0, 25); // periodo a liquidar
                    $archivo .= str_pad("", 10, " ", STR_PAD_RIGHT); // 10 espacios al final
                    //$archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    //$total2venc += $comisionSegundoVenc;
                    $cantUnidadesInformadas++;

                    $comisionPrimerVenc = round($totalexpensa / 2, 2);
                    $comisionSegundoVenc = round($totalexpensa / 2, 2);
                    $archivo .= date("0md");
                    $archivo .= "001" . $ltprefijo . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // Identificador usuario o CPE primera parte
                    $archivo .= str_pad($identificadorcuentaROELA, 10, "0", STR_PAD_LEFT); // Identificador usuario o CPE segunda parte
                    $archivo .= date("ymd", strtotime($liquidacion['limite'])) . str_pad($comisionPrimerVenc * 100, 12, "0", STR_PAD_LEFT); // 1º venc y total
                    //$archivo .= date("ymd", strtotime($liquidacion['limite'])) . str_pad($comisionSegundoVenc * 100, 12, "0", STR_PAD_LEFT); // 2º venc y total
                    $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 2º venc y total, no se usa
                    $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 3º venc y total, no se usa
                    $archivo .= substr(str_pad(strtoupper(self::hyphenize($datoscliente['name'])), 15, " ", STR_PAD_RIGHT), 0, 15); // datos del cliente
                    $archivo .= substr(str_pad("CUOTA 2 " . strtoupper(self::hyphenize($liquidacion['periodo'])), 25, " ", STR_PAD_RIGHT), 0, 25); // periodo a liquidar
                    $archivo .= str_pad("", 10, " ", STR_PAD_RIGHT); // 10 espacios al final
                    //$archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    //$total2venc += $comisionSegundoVenc;
                    $cantUnidadesInformadas++;
                } else {
                    $comisionPrimerVenc = round($totalexpensa, 2);
                    $comisionSegundoVenc = round($totalexpensa, 2);
                    $archivo .= date("0md");
                    $archivo .= "001" . $ltprefijo . str_pad($consorcio_code, 4, "0", STR_PAD_LEFT) . str_pad($p["code"], 4, "0", STR_PAD_LEFT); // Identificador usuario o CPE primera parte
                    $archivo .= str_pad($identificadorcuentaROELA, 10, "0", STR_PAD_LEFT); // Identificador usuario o CPE segunda parte
                    $archivo .= date("ymd", strtotime($liquidacion['vencimiento'])) . str_pad($comisionPrimerVenc * 100, 12, "0", STR_PAD_LEFT); // 1º venc y total
                    if ($liquidacion['vencimiento'] == $liquidacion['limite']) {
                        $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 2º venc y total, no se usa    
                    } else {
                        $archivo .= date("ymd", strtotime($liquidacion['limite'])) . str_pad($comisionSegundoVenc * 100, 12, "0", STR_PAD_LEFT); // 2º venc y total
                        $total2venc += $comisionSegundoVenc;
                    }
                    $archivo .= str_pad("", 18, "0", STR_PAD_LEFT); // 3º venc y total, no se usa
                    $archivo .= substr(str_pad(strtoupper(self::hyphenize($datoscliente['name'])), 15, " ", STR_PAD_RIGHT), 0, 15); // datos del cliente
                    $archivo .= substr(str_pad(strtoupper(self::hyphenize($liquidacion['periodo'])), 25, " ", STR_PAD_RIGHT), 0, 25); // periodo a liquidar
                    $archivo .= str_pad("", 10, " ", STR_PAD_RIGHT); // 10 espacios al final
                    //$archivo .= PHP_EOL;
                    $total1venc += $comisionPrimerVenc;
                    $cantUnidadesInformadas++;
                }
            }
        }

        // 131 caracteres
        // Campo                        Tipo de Dato    Longitud    Posición    Descripción
        // Identificador de registro    Alfanumérico    13          1-13        Fijo: TRFACTURACION
        // Cantidad de registros        Numérico        8           14-21       Cantidad de registros incluyendo cabecera y pie
        // Total 1º vencimiento         Numérico        18 (16 y 2) 22-39       Suma de los importes informados en el primer vencimiento 
        // Total 2º vencimiento         Numérico        18 (16 y 2) 40-57       Suma de los importes informados en el segundo vencimiento 
        // Total 3º vencimiento         Numérico        18 (16 y 2) 58-75       Suma de los importes informados en el tercer vencimiento
        // Filler                       Alfanumérico    56          76-131      Completar con espacios
        $archivo .= "TRFACTURACION" . str_pad($cantUnidadesInformadas + 2, 8, "0", STR_PAD_LEFT);
        $archivo .= str_pad($total1venc * 100, 18, "0", STR_PAD_LEFT) . str_pad($total2venc * 100, 18, "0", STR_PAD_LEFT) . str_pad(0, 18, "0", STR_PAD_LEFT);
        $archivo .= str_pad("", 56, " ", STR_PAD_LEFT);

        // guardo el archivo
        $nombreArchivo = str_replace("-", "", $datoscliente['cuit']) . "." . date("Ymd");
        $hh = fopen(APP . DS . WEBROOT_DIR . DS . "temp/" . $nombreArchivo, "w+");
        fwrite($hh, $archivo);
        fclose($hh);
        
        /* parche temporal hasta q roela se decida a corregir sus quilombos */
        /*header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=" . h($nombreArchivo) . ".txt");
        echo $archivo;
        die;*/
        
        return $nombreArchivo;
    }

    public static function generaClavePagoElectronico($codconsorcio, $codunidad, $prefijounidad = '', $codcliente = '', $identificadorcuentaROELA = '0000000000') {
        // Identificador usuario o CPE Numérico        19          9 – 27      Los 19 dígitos se forman de la siguiente manera: Los primeros nueve dígitos son para identificar a los 
        // titulares de las obligaciones. Si utiliza el código de barras de SIRO, se competa la primera posición con cero y las 8 restantes con el “identificador de usuario” del diseño 
        // de registro de código de barra SIRO (salvo que se utilice el identificador de concepto como un campo adicional para ganar un dígito mas en el identificador de usuario). 
        // No puede utilizarse aquí un número de comprobante. Las últimas 10 posiciones son el “identificador de cuenta” otorgado por banco roela (presente en el diseño de registro del Código de Barras SIRO)
        $clave = $prefijounidad . str_pad($codconsorcio, 4, "0", STR_PAD_LEFT) . str_pad($codunidad, 4, "0", STR_PAD_LEFT) . $identificadorcuentaROELA;
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

    //    Campo                       Tipo de Dato            Longitud        Observaciones
    //    Empresa deServicio          Numérico                4               0447 (Otorgado por SIRO)
    //    Identificador de concepto   Numérico                1               Dígito a su disposición y puede referirse a distintos conceptos que factura el ente. Ej: 1: Alquileres, 2: expensas, 3: cuota 4: seguro de vida, 5: cliente VIP, etc.
    //    Identificador de usuario    Numérico                8               Código para identificar a los titulares de las obligaciones. Ej.: DNI, Legajo, Unidad Funcional, consorcio y PH, etc.
    //    Fecha 1er Vto               Numérico                6               Formato AAMMDD. Ej: Si la fecha de vencimiento es el 15 de abril de 2013, debe ponerse 130415
    //    Importe 1er Vto             Numérico                7 (5 y 2)       Monto a cobrar en el primer vencimiento. Ej: Si el monto a cobrar es de $1.490,80, debe ponerse 0149080
    //    Días hasta 2do Vto          Numérico                2               Cantidad de días corridos entre el 1er y 2do Vencimiento. Si Si no usa segundo vencimiento, completar con 0 (ceros).
    //    Importe 2do Vto             Numérico                7 (5 y 2)       Monto a cobrar en el segundo vencimiento. Si no usa segundo vencimiento, completar con 0 (ceros). Ver ejemplo del campo Importe 1er Vto.
    //    Días hasta 3er Vto          Numérico                2               Cantidad de días corridos entre el 2do y 3er Vencimiento. Si no usa tercer vencimiento, completar con 0 (ceros)
    //    Importe 3er Vto             Numérico                7 (5 y 2)       Monto a cobrar en el tercer vencimiento. Si no usa tercer vencimiento, completar con 0 (ceros). Ver ejemplo del campo Importe 1er Vto.
    //    Identificador de Cuenta     Numérico                10              Otorgado por BANCO ROELA.
    //    Dígito Verificador 1        Numérico                1               Ver ANEXO al final del documento.
    //    Dígito Verificador 2        Numérico                1               Ver ANEXO al final del documento. 

    public static function generaCodigoBarras($prefijo, $cod_cliente, $cod_consor, $ltprefijo, $cod_unidad, $vto1, $vto2, $monto1, $monto2 = 0, $identificadorcuentaROELA = '0000000000') {
        // EJ ROELA 04440000132671207300093700060098385000000000515000934600
        $cant = (new DateTime($vto1))->diff(new DateTime($vto2)); // dias hasta el prox vencimiento (dejar aca porq despues vto1 y vto2 cambian
        $codbarras = "0447" . $ltprefijo . str_pad($cod_consor, 4, "0", STR_PAD_LEFT) . str_pad($cod_unidad, 4, "0", STR_PAD_LEFT); // 0447.LTID.CONSOR.UNIDAD
        $codbarras .= date("ymd", strtotime($vto1)) . str_pad(round($monto1 * 100), 7, "0", STR_PAD_LEFT); // fecha monto
        if ($cant->format('%a') == 0) {// si el vencimiento es igual al limite, el 2º vencimiento debe generarse en cero
            $codbarras .= "000000000"; // dias hasta 2º vencimiento y monto 2º vencimiento (no tiene, completo con 00 y 0000000)
        } else {
            $codbarras .= str_pad($cant->format('%a'), 2, "0", STR_PAD_LEFT);
            $codbarras .= str_pad(round(($monto2 == 0 ? $monto1 : $monto2) * 100), 7, "0", STR_PAD_LEFT); // Si monto2 es cero, entonces uso monto1
        }
        $codbarras .= "000000000"; // dias hasta 3º vencimiento y monto 3º vencimiento (no tiene, completo con 00 y 0000000)
        $codbarras .= $identificadorcuentaROELA;
        $codbarras .= self::obtieneDigitoVerificador($codbarras, 54);
        $codbarras .= self::obtieneDigitoVerificador($codbarras, 55);
        return $codbarras;
    }

    // despues del 9, saco \. - en el regexp (no permite ni punto, coma, parentesis, etc en el concepto (solo num y letras sin acentos)
    public static function hyphenize($string) {
        $dict = ["I'm" => "I am", "thier" => "their"];
        return preg_replace(
                ['#[^A-Za-z0-9 ]+#'], [' '],
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

    /*
      // Para probar Roela. Lista convenios o Lista administradores
      $xml = '<?xml version="1.0" encoding="utf-8"?>
      <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
      <soap12:Body>
      <ConsultarConvenios xmlns="http://www.bancoroela.com.ar/">
      </ConsultarConvenios>
      </soap12:Body>
      </soap12:Envelope>';
      $xml2 = '<?xml version="1.0" encoding="utf-8"?>
      <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
      <soap12:Body>
      <ConsultarAdministradores xmlns="http://www.bancoroela.com.ar/" />
      </soap12:Body>
      </soap12:Envelope>';
      $curl = curl_init('https://www.bancoroela.com.ar/SiroWebService/IntegracionWebService.asmx');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Authorization: Basic MzA3MTQ1NDg2Nzc6VG9jYWJhZWxwaWFub2NvbW91bmFuaW1hbDA1MDc',
      'User-Agent: CEONLINE',
      'Connection: Keep-Alive',
      'Content-Type: application/soap+xml; charset=utf-8'
      ));
      $resul = curl_exec($curl);
      echo "<pre>";
      var_dump($resul);
      curl_close($curl);
     */
}
