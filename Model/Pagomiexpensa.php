<?php

/*
 * Métodos para utilizar Pago mi Expensa en CakePHP 2.x
 */
App::uses('AppModel', 'Model');

class Pagomiexpensa extends AppModel {

    private static $url = 'ftpserver.pagomiexpensa.com.ar';
    private static $puerto = '22';

    public static function enviarSaldosPlataformaPagos($client_code, $file) {
        return true;
    }

    public static function generarArchivoInformeDeuda($client_code, $consorcio_code, $liquidacion, $data, $ltprefijo, $usa2cuotas, $comisionPlataformaPago = 3.1, $minimo = 18.15, $identificadorcuentaROELA = '0000000000') {
        $fechaArchivo = date("YmdHis");
        //Registro Cabecera
        //#1 Código de registro Numérico 1
        //#2 Identificación consorcio Carácter 3
        //#3 Identificación administrador Numérico 4

        $archivo = "0" . str_pad($consorcio, 3, "0", STR_PAD_LEFT) . str_pad($cliente, 4, "0", STR_PAD_LEFT) . PHP_EOL;

        //Registro Detalle
        //#1 Código de registro Numérico 1
        //#2 Identificación consorcio Numérico 3
        //#3 Identificación unidad funcional Numérico 3
        //#4 Concepto Carácter 40
        //#5 Fecha 1° vencimiento Numérico 6
        //#6 Importe 1° vencimiento Numérico 10
        //#7 Fecha 2° vencimiento Numérico 6
        //#8 Importe 2° vencimiento Numérico 10

        $total1venc = $total2venc = $cantUnidadesInformadas = 0;
        foreach ($data['prop'] as $p) {
            $totalexpensa = floor($data['saldo'][$p['id']]['capital'] + $data['saldo'][$p['id']]['interes']);
            $archivo .= "1" . str_pad($consorcio, 3, "0", STR_PAD_LEFT) . str_pad($p["code"], 3, "0", STR_PAD_LEFT);
            $archivo .= substr(str_pad(self::hyphenize($liquidacion['periodo']), 40, " ", STR_PAD_RIGHT), 0, 40);
            $archivo .= date("ymd", strtotime($liquidacion['vencimiento']));
            $archivo .= str_pad($totalexpensa * 100, 10, "0", STR_PAD_LEFT);
            $archivo .= date("ymd", strtotime($liquidacion['limite']));
            $archivo .= str_pad($totalexpensa * 100, 10, "0", STR_PAD_LEFT);
            $archivo .= PHP_EOL;
            $total1venc += $totalexpensa;
            $total2venc += $totalexpensa;
            $cantUnidadesInformadas++;
        }

        // registro control
        //#1 Código de registro Numérico 1
        //#2 Cantidad de casos Numérico 4
        //#3 Total importe 1° vencimiento Numérico 12
        //#4 Total importe 2° vencimiento Numérico 12
        $archivo .= "2" . str_pad($cantUnidadesInformadas, 4, "0", STR_PAD_LEFT);
        $archivo .= str_pad(round($total1venc, 2) * 100, 12, "0", STR_PAD_LEFT);
        $archivo .= str_pad(round($total2venc, 2) * 100, 12, "0", STR_PAD_LEFT);
        // guardo el archivo
        $nombreArchivo = "LQ" . str_pad($cliente, 4, "0", STR_PAD_LEFT) . $fechaArchivo . ".TXT";
        $hh = fopen(APP . DS . WEBROOT_DIR . DS . "temp/" . $nombreArchivo, "w+");
        fwrite($hh, $archivo);
        fclose($hh);
        return $nombreArchivo;
    }

    public static function generaClavePagoElectronico($codconsorcio, $codunidad, $prefijounidad = '', $codcliente = '', $identificadorcuentaROELA = '0000000000') {
        //BANELCO y LINK.
        //#1 Identificación administrador - Numérico 4
        //#2 Identificación consorcio - Numérico 3
        //#3 Identificación unidad funcional - Numérico 3
        $clave = "";
        if ($cliente != "0") {
            $clave = str_pad($cliente, 4, "0", STR_PAD_LEFT) . str_pad($consorcio, 3, "0", STR_PAD_LEFT) . str_pad($unidad, 3, "0", STR_PAD_LEFT);
        }
        return $clave;
    }

    public static function generaCodigoBarras($prefijo, $cliente, $consorcio, $prefijolt, $unidad, $vto1, $vto2, $total1, $total2, $datointerno = '111111') {
        //#1 Código de empresa Numérico 5 #1 Código de empresa Código de identificación de Pago Mi Expensa ante los entes recaudadores. Se informará siempre 16870.
        //#2 Identificación administrador Numérico 4 Código de identificación asignado por Pago Mi Expensa al administrador.
        //#3 Identificación consorcio Numérico 3
        //#4 Identificación unidad funcional Numérico 3
        //#5 Fecha 1° vencimiento Numérico 6
        //#6 Importe 1° vencimiento Numérico 8
        //#7 Fecha 2° vencimiento Numérico 6
        //#8 Importe 2° vencimiento Numérico 8
        //#9 Dígito verificador Numérico 1
        $codigobarras = 16870 . str_pad($cliente, 4, "0", STR_PAD_LEFT);
        $codigobarras .= str_pad($consorcio, 3, "0", STR_PAD_LEFT) . str_pad($unidad, 3, "0", STR_PAD_LEFT);
        $codigobarras .= date("ymd", strtotime($vto1));
        $codigobarras .= str_pad(round($total1 * 100), 8, "0", STR_PAD_LEFT);
        $codigobarras .= date("ymd", strtotime($vto2));
        $codigobarras .= str_pad(round($total2 * 100), 8, "0", STR_PAD_LEFT);
        return ($codigobarras . self::obtieneDigitoVerificador($codigobarras));
    }

    public static function obtieneDigitoVerificador($codigobarras, $hasta = '') {
        //El algoritmo consiste en multiplicar los dígitos correspondientes al código de barras de izquierda a
        //derecha por 3 las posiciones impares y por 1 las posiciones pares.
        //Se suman todos los resultados de los productos parciales y se toma el resto de la división por 10, dicho
        //resto se le resta al número 10, el digito verificador es el resultado de dicha resta si es menor a 10, caso
        //contrario es cero.
        //EJ:
        //CB = 731690180001031230003806000008
        //7*3=21, 3*1=3, 1*3=3, 6*1=6, 9*3=27, 0*1=0, 1*3=3, 8*1=8, 0*3=0, 0*1=0, 0*3=0, 1*1=1, 0*3=0, 3*1=3, 1*3=3, 2*1=2, 3*3=9, 0*1=0, 
        //0*3=0, 0*1=0, 3*3=9, 8*1=8, 0*3=0, 6*1=6, 0*3=0, 0*1=0, 0*3=0, 0*1=0, 0*3=0
        //Suma = 112,
        //Resto de la división por 10 = 2
        //Digito verificador → 10-2 = 8
        $arre = str_split($codigobarras);
        $pos = 1;
        $suma = 0;
        foreach ($arre as $v) {
            $suma += ($pos % 2 == 0 ? $v : $v * 3);
            $pos++;
        }
        $dv = (10 - ($suma % 10));
        if ($dv == 10) {
            $dv = 0;
        }

        return $dv;
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
