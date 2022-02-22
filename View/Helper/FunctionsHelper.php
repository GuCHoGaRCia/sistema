<?php

App::uses('AppHelper', 'View/Helper');

class FunctionsHelper extends AppHelper {
    /*
     * Busca un valor en una lista y devuelve el index, por ejemplo:
     * $this->Functions->find($cobranzas, array('propietario_id' => 8));
     * Este ejemplo busca 'propietario_id' => 8
     * Devolver�a: 1 (index).
     * Si se incluye el parametro "all", devuelve un array con los indices donde fue encontrado $valor
     *  array(
     *      (int) 0 => array(
     *          'Cobranza' => array(
     * 				'propietario_id' => '17',
     *                  'amount' => '500.00'
     *          	)
     *      ),
     *      (int) 1 => array(
	 * 			'Cobranza' => array(
	 * 				'propietario_id' => '8',
	 * 				'amount' => '700.00'
	 * 			)
	 * 		),
     * @param array $lista
     * @param array $valor
     * @return array/int $resul/$k
     */

    public function find($lista, $valor, $all = false) {
        $key = array_keys($valor);
        $value = array_values($valor);
        $resul = [];

        foreach ($lista as $k => $v) {
            $indice = array_keys($v);
            if (isset($v[$indice[0]][$key[0]]) && $v[$indice[0]][$key[0]] == $value[0]) {
                if ($all) {
                    $resul[] = $k;
                } else {
                    return $k;
                }
            }
        }
        return $resul;
    }

    /* Se diferencia de la otra porq no esta el model en cada array
     * Busca un valor en una lista y devuelve el index, por ejemplo:
     * $this->Functions->find($cobranzas, array('propietario_id' => 8));
     * Este ejemplo busca 'propietario_id' => 8
     * Devolver�a: 1 (index).
     * Si se incluye el parametro "all", devuelve un array con los indices donde fue encontrado $valor
     *  array(
     *      (int) 0 => array(
     * 		'propietario_id' => '17',
     *          'amount' => '500.00'
     *      ),
     *      (int) 1 => array(
     *          'propietario_id' => '8',
     * 		'amount' => '700.00'
     * 	),
     * @param array $lista
     * @param array $valor
     * @return array/int $resul/$k
     */

    public function find2($lista, $valor, $all = false) {
        $key = array_keys($valor);
        $value = array_values($valor);
        $resul = [];

        foreach ($lista as $k => $v) {
            //$indice = array_keys($v);
            if ($v[$key[0]] == $value[0]) {
                if ($all) {
                    $resul[] = $k;
                } else {
                    return $k;
                }
            }
        }
        return $resul;
    }

    /*
     * fractionSymbol: si no pongo en false, me pone 0.25 como 25c
     */

    public function money($valor) {
        return CakeNumber::currency(h($valor), null, ['negative' => '-', 'before' => false, 'thousands' => '', 'decimals' => ',', 'fractionSymbol' => false]);
    }

    /**
     * Devuelve un hash a partir de un id
     *
     * @param integer $id
     * @return string $url
     * http://book.cakephp.org/2.0/en/core-utility-libraries/security.html#Security::rijndael
     */
    /* public function rijndael($text) {
      $cryptKey = substr(Configure::read('Security.key'), 0, 32);
      $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
      return $iv . '$$' . mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $cryptKey, $text, MCRYPT_MODE_CBC, $iv);
      } */

    function _encryptURL($textoplano) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $textocifrado = strtr(base64_encode($iv . openssl_encrypt($textoplano, 'AES-256-CBC', Configure::read('Security.key'), OPENSSL_RAW_DATA, $iv)), '+/=', '-_,');
        if (strlen($textocifrado) > 0) {
            return $textocifrado;
        }
        return null; // error
    }

    function _decryptURL($textocifrado) {
        $texto = base64_decode(strtr($textocifrado, '-_,', '+/='));
        if (!empty($texto)) {
            $ivSize = openssl_cipher_iv_length('AES-256-CBC');
            $textoplano = @openssl_decrypt(mb_substr($texto, $ivSize, null, '8bit'), 'AES-256-CBC', Configure::read('Security.key'), OPENSSL_RAW_DATA, mb_substr($texto, 0, $ivSize, '8bit'));
            if (!empty($textoplano)) {
                return $textoplano;
            }
            if (!function_exists('mcrypt_get_iv_size')) {// para evitar el warning Use of undefined constant MCRYPT_RIJNDAEL_256 - assumed 'MCRYPT_RIJNDAEL_256' (this will throw an Error in a future version of PHP) [APP\cakecore\lib\Cake\Utility\Security.php, line 266]
                return null;
            }
            $textoplano = Security::rijndael($texto, Configure::read('Security.key'), 'decrypt');
            if (!empty($textoplano)) {
                return $textoplano;
            }
        }

        return null; // error
    }

    function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    function convertir_barcode($cadena) {
        if (empty($cadena)) {
            return "";
        }
        $resultado = "<img src=\"/sistema/img/bar/init.GIF\">";
        while (strlen($cadena) > 1) {
            $resultado .= "<img src=\"/sistema/img/bar/" . substr($cadena, 0, 2) . ".GIF\">";
            $cadena = substr($cadena, 2);
        }
        $resultado .= "<img src=\"/sistema/img/bar/fin.GIF\">";
        return $resultado;
    }

    /*
     * Convertir numeros a letras
     */

    /* public static function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false) {
      $converted = '';
      $decimales = '';
      $negativo = (bool) ($number < 0);
      if ( ($number > 999999999)) { //($number < 0) ||
      return '';
      }
      $div_decimales = explode('.', $number);
      if (count($div_decimales) > 1) {
      $number = $div_decimales[0];
      $decNumberStr = (string) $div_decimales[1];
      if (strlen($decNumberStr) == 1) {
      $decNumberStr .= "0";
      }
      //if (strlen($decNumberStr) == 2) {
      $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
      $decCientos = substr($decNumberStrFill, 6);
      $decimales = convertGroup($decCientos);
      //}
      } else if (count($div_decimales) == 1 && $forzarCentimos) {
      $decimales = 'CERO ';
      }
      $numberStr = (string) $number;
      $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
      $millones = substr($numberStrFill, 0, 3);
      $miles = substr($numberStrFill, 3, 3);
      $cientos = substr($numberStrFill, 6);
      if (intval($millones) > 0) {
      if ($millones == '001') {
      $converted .= 'UN MILLON ';
      } else if (intval($millones) > 0) {
      $converted .= sprintf('%sMILLONES ', convertGroup($millones));
      }
      }
      if (intval($miles) > 0) {
      if ($miles == '001') {
      $converted .= 'MIL ';
      } else if (intval($miles) > 0) {
      $converted .= sprintf('%sMIL ', convertGroup($miles));
      }
      }
      if (intval($cientos) > 0) {
      if ($cientos == '001') {
      $converted .= 'UN ';
      } else if (intval($cientos) > 0) {
      $converted .= sprintf('%s ', convertGroup($cientos));
      }
      }
      if (empty($decimales)) {
      $valor_convertido = $converted . strtoupper($moneda);
      } else {
      $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
      }
      return $negativo ? 'MENOS ' . $valor_convertido : $valor_convertido;
      }

      private static function convertGroup($n) {
      $UNIDADES = ['', 'UN ', 'DOS ', 'TRES ', 'CUATRO ', 'CINCO ', 'SEIS ', 'SIETE ', 'OCHO ', 'NUEVE ', 'DIEZ ', 'ONCE ', 'DOCE ', 'TRECE ', 'CATORCE ', 'QUINCE ', 'DIECISEIS ', 'DIECISIETE ', 'DIECIOCHO ', 'DIECINUEVE ', 'VEINTE '];
      $DECENAS = ['VENTI', 'TREINTA ', 'CUARENTA ', 'CINCUENTA ', 'SESENTA ', 'SETENTA ', 'OCHENTA ', 'NOVENTA ', 'CIEN '];
      $CENTENAS = ['CIENTO ', 'DOSCIENTOS ', 'TRESCIENTOS ', 'CUATROCIENTOS ', 'QUINIENTOS ', 'SEISCIENTOS ', 'SETECIENTOS ', 'OCHOCIENTOS ', 'NOVECIENTOS '];

      $output = '';
      if ($n == '100') {
      $output = "CIEN ";
      } else if ($n[0] !== '0') {
      $output = $CENTENAS[$n[0] - 1];
      }
      $k = intval(substr($n, 1));
      if ($k <= 20) {
      $output .= $UNIDADES[$k];
      } else {
      if (($k > 30) && ($n[2] !== '0')) {
      $output .= sprintf('%sY %s', $DECENAS[intval($n[1]) - 2], $UNIDADES[intval($n[2])]);
      } else {
      $output .= sprintf('%s%s', $DECENAS[intval($n[1]) - 2], $UNIDADES[intval($n[2])]);
      }
      }
      return $output;
      } */
}
