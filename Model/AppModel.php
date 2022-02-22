<?php

App::uses('Model', 'Model');
App::uses('CakeNumber', 'Utility');

// inicializo las Plataformas de pago
App::uses('Plapsa', 'Model');
App::uses('Pagomiexpensa', 'Model');
App::uses('Roela', 'Model');

class AppModel extends Model {

    // para búsqueda
    public $actsAs = array('Search.Searchable', 'Containable', 'Auditable');
    public $filterArgs = array(array('name' => 'buscar', 'type' => 'query', 'method' => 'filterName'));
    public $recursive = -1;

    /*
     * Para verificar si las pk utilizadas en editar e invertir (ver AppController) pueden ser modificadas (si pertenecen al cliente actual)
     * Tambien se utiliza en muchos otros lugares (en la mayoria de las action de controllers, etc).
     */

    public function canEdit($id) {
        return true;
    }

    /*
     * Obtiene el usuario actual para Auditoria
     */

    public function currentUser() {
        if (isset($_SESSION['Auth']['User'])) {
            return ['id' => $_SESSION['Auth']['User']['id'], 'client_id' => $_SESSION['Auth']['User']['client_id'],
                'description' => $_SESSION['Auth']['User']['username'] . '@' . $_SESSION['Auth']['User']['Client']['identificador_cliente']];
        } else {
            return ['id' => 0, 'client_id' => 0, 'description' => filter_input(INPUT_ENV, 'REMOTE_ADDR', FILTER_VALIDATE_IP)];
        }
    }

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

    public function buscaLista($lista, $valor, $all = false) {
        $key = array_keys($valor);
        $value = array_values($valor);
        $resul = [];
        if (!empty($lista)) {
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

    public function buscaLista2($lista, $valor, $all = false) {
        $key = array_keys($valor);
        $value = array_values($valor);
        $resul = [];
        if (!empty($lista)) {
            foreach ($lista as $k => $v) {
                if ($v[$key[0]] == $value[0]) {
                    if ($all) {
                        $resul[] = $k;
                    } else {
                        return $k;
                    }
                }
            }
        }

        return $resul;
    }

    /*
     * Compara 2 fechas y devuelve true si $menor es menor igual que $mayor
     */

    public function fechaEsMenorIgualQue($menor, $mayor, $solomenor = false) {
        try {
            $date1 = new DateTime($menor);
            $date2 = new DateTime($mayor);
            return (bool) ($solomenor ? ($date1 < $date2) : ($date1 <= $date2) );
        } catch (Exception $ex) {
            return false;
        }
    }

    /*
     * Dada una fecha (formato d/m/Y o Y-m-d), la convierte a Y-m-d de ser necesario
     */

    public function fecha($fecha) {
        if (empty($fecha) || strpos($fecha, '-') !== false) {
            return $fecha;
        }
        try {
            $f = substr($fecha, 6, 4) . "-" . substr($fecha, 3, 2) . "-" . substr($fecha, 0, 2);
            $date1 = new DateTime($f);
            return $f;
        } catch (Exception $ex) {
            return null;
        }
    }

    /*
     * Ordena un array por un campo (ej: fecha)
     */

    public function array_sort($array, $on, $order = SORT_ASC) {
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

    /*
     * Valida si un CUIT tiene el formato requerido (xx-xxxxxxxx-x) y si es válido matemáticamente realizando el checksum
     */

    public function validarCuit($check) {
        reset($check); // por si el campo no se llama 'cuit' (ej: iibb)
        $cuit = $check[key($check)];
        $digits = [];
        if (strlen($cuit) != 13) {
            return false;
        }
        for ($i = 0; $i < strlen($cuit); $i++) {
            if ($i == 2 or $i == 11) {
                if ($cuit[$i] != '-') {
                    return false;
                }
            } else {
                if (!ctype_digit($cuit[$i])) {
                    return false;
                }

                if ($i < 12) {
                    $digits[] = $cuit[$i];
                }
            }
        }
        $acum = 0;
        foreach ([5, 4, 3, 2, 7, 6, 5, 4, 3, 2] as $i => $multiplicador) {
            $acum += $digits[$i] * $multiplicador;
        }
        $cmp = 11 - ($acum % 11);
        if ($cmp == 11) {
            $cmp = 0;
        } else if ($cmp == 10) {
            $cmp = 9;
        }
        return ($cuit[12] == $cmp);
    }

    /*
     * Verifico que el formato del campo email sea correcto.
     * Puede ser una direccion de email como mínimo o varias separadas por coma (,)
     */

    public function checkEmails($check) {
        if (!isset($this->data[$this->alias]['email'])) {
            return true;
        }
        $emails = explode(",", $this->data[$this->alias]['email']);
        $error = false;
        foreach ($emails as $k => $v) {
            if (!filter_var($v, FILTER_VALIDATE_EMAIL)) {
                $error = true;
                break;
            }
        }

        return (count($emails) != 0 && !$error);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function generateRandomString($length = 20) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 1, $length);
    }

    /*
     * Permito solamente estos tags y atributos (HTMLPurifier escapea el resto)
     */

    public function cleanHTML($data) {
        require_once __DIR__ . '/../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'h1,h2,h3,h4,h5,h6,br,b,i,u,strong,em,a[href],pre,img,tt,div,p,ol,ul,table[cellspacing],caption,thead,tbody,tfoot,dl,dt,dd,kbd,q,span,hr,li,tr,td,th,*[colspan|style|class|border],img[src],*[align]');
        $config->set('URI.AllowedSchemes', ['data' => true, 'http' => true, 'https' => true]); // para <img src="data:image/jpeg;base64,...">, ej: en noticias
        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($data);
    }

}
