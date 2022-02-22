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
     * 			'propietario_id' => '17',
     *                  'amount' => '500.00'
     *          )
     *      ),
     *      (int) 1 => array(
     * 		'Cobranza' => array(
     * 			'propietario_id' => '8',
     * 			'amount' => '700.00'
     * 		)
     * 	),
     * @param array $lista
     * @param array $valor
     * @return array/int $resul/$k
     */

    public function find($lista, $valor, $all = false) {
        $key = array_keys($valor);
        $value = array_values($valor);
        $resul = array();

        foreach ($lista as $k => $v) {
            $indice = array_keys($v);
            if ($v[$indice[0]][$key[0]] == $value[0]) {
                if ($all) {
                    $resul[] = $k;
                } else {
                    return $k;
                }
            }
        }
        return $resul;
    }

    public function money($valor) {
        return CakeNumber::currency(h($valor), null, array('negative' => '-'));
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
}
