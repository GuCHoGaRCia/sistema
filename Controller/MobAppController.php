<?php

header("Access-Control-Allow-Origin: https://ceonline.com.ar"); // para q desde el panel Propietario puedan ver archivos y consultas, sino no funciona ajax (porq estoy en ceonline.com.ar/p/?)
header('Access-Control-Allow-Methods: POST,GET');
header('Access-Control-Allow-Headers: x-requested-with');
App::uses('AppController', 'Controller');

class MobAppController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'login2', 'panel', 'scp', 'gcp');
        if ($this->request->is('options')) {
            exit(0);
        }

        // si no esta seteado ar.com.ceonline.expensasonline salgo
        if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strpos(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'ar.com.ceonline.expensasceo') !== false)) {
            exit(0);
        }
        $this->layout = '';
        $this->autoRender = false;
        //array_push($this->Security->unlockedActions, 'scp'); // permito blackhole x ajax
        //array_push($this->Security->unlockedActions, 'login'); // permito blackhole x ajax
        /* if ($this->request->is('get') && $this->request['action'] == 'view' && isset($this->request->pass[0])) {
          // acceso por link de propietario
          $this->email = $this->Aviso->_decryptURL($this->request->pass[0]);
          } */
    }

    /*
     * Se utiliza en los casos que el usuario desea recordar sus datos y ya se logueo anteriormente
     */

    public function login2() {
        if (isset($this->request->data['t'])) {
            $email = $this->_checkToken($this->request->data['t']);
            if ($email != '') {
                $lista = $this->_getPropietariosConEmail($email);
                if (empty($lista)) {
                    return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']); //email incorrecto
                }

                if (!isset($this->request->data['f'])) {
                    return json_encode(['e' => 0, 'd' => $this->panel($email, $this->_obtieneCodigoEmail($email))]);
                }
                switch ($this->request->data['f']) {
                    case 'c':
                        return json_encode(['e' => 0, 'd' => $this->chat($email, $this->_obtieneCodigoEmail($email))]);
                    case 'c2':
                        if (!isset($this->request->data['p'])) {
                            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']); // propiet inexistente
                        }
                        $p = $this->_decryptUrl($this->request->data['p']);
                        if (empty($p) || !filter_var($p, FILTER_VALIDATE_INT)) {
                            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']); //propiet inexistente
                        }
                        return json_encode(['e' => 0, 'd' => $this->chat2($email, $this->_obtieneCodigoEmail($email), $p)]);
                    default:
                        return json_encode(['e' => 0, 'd' => $this->panel($email, $this->_obtieneCodigoEmail($email))]);
                }
            }
        }
        return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']);
    }

    public function login() {
        if (!isset($this->request->data['e']) || empty($this->request->data['e']) || !isset($this->request->data['c']) || empty($this->request->data['c'])) {
            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']);
        }
        $e = $this->request->data['e'];
        $c = $this->request->data['c'];
        $r = (isset($this->request->data['r']) && $this->request->data['r'] == 1 ? 1 : 0);
        if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']); //El email es incorrecto
        }
        $lista = $this->_getPropietariosConEmail($e);
        if (empty($lista)) {
            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos']); //El email es incorrecto
        }
        $cod = $this->_obtieneCodigoEmail($e);
        if ($c == $cod) {
            return json_encode(['e' => 0, 'd' => $this->panel($e, $c)] + ($r == 1 ? ['t' => $this->_encryptURL($e . "##" . $c)] : []));
        } else {
            return json_encode(['e' => 1, 'd' => 'Usuario o código incorrectos'/* , 'c' => $cod */]); // para ver cual es el codigo de un email, se ve desde la consola
        }
    }

    /*
     * Funcion que muestra el panel principal con las Propiedades de los Propietarios
     */

    public function panel($e, $c) {
        $t = $this->_encryptURL($e . "##" . $c);
        $view = new View($this, false);
        $view->set('t', $t);
        $view->set('id', $this->_encryptURL($e));
        $view->set('d', $this->_getData($this->_getPropietariosConEmail($e)));
        return $view->render('panel');
    }

    /*
     * Funcion que muestra el listado de Propiedades habilitadas para Consultar
     */

    public function chat($e, $c) {
        $t = $this->_encryptURL($e . "##" . $c);
        $view = new View($this, false);
        $view->set('t', $t);
        $view->set('id', $this->_encryptURL($e));
        $view->set('d', $this->_getData($this->_getPropietariosConEmailChat($e)));
        return $view->render('chat');
    }

    /*
     * Funcion que muestra las consultas del Propietario con una Administracion y propietario _decryptUrl($p) particular
     */

    public function chat2($e, $c, $p) {
        $t = $this->_encryptURL($e . "##" . $c);
        $view = new View($this, false);
        $view->set('t', $t);
        $view->set('id', $this->_encryptURL($e));
        $prop = $this->_getPropietariosConEmailChat($e);
        if (!isset($prop[$p])) {
            $view->set('d', []); //propietario inexistente
        } else {
            $view->set('d', $this->_getData([$p => $prop[$p]])); // obtengo _getData solamente del Propietario seleccionado
            $view->set('consultas', json_encode($this->_getConsultasPropietario($p))); // obtengo _getData solamente del Propietario seleccionado
            $view->set('p', $this->_encryptURL($p)); // obtengo _getData solamente del Propietario seleccionado
        }
        return $view->render('chat2');
    }

    /*
     * Guarda la consulta del propietario
     */

    public function scp() {
        if (isset($this->request->data['t']) && isset($this->request->data['m']) && !empty($this->request->data['m']) && isset($this->request->data['p'])) {
            $email = $this->_checkToken($this->request->data['t']);
            if ($email != '') {
                $prop = $this->_getPropietariosConEmail($email);
                $p = $this->_decryptUrl($this->request->data['p']);
                if (!empty($p) && filter_var($p, FILTER_VALIDATE_INT) && isset($prop[$p])) { // propietario_id cifrado es un entero
                    $cid = $prop[$p];
                    $c = ClassRegistry::init('Consorcio');
                    $cli = $c->getConsorcioClientId($cid);
                    if ($this->_guardarConsultaPropietario($cli, $p, $this->request->data['m'])) {
                        return json_encode(['e' => 0, 'd' => $this->_getConsultasPropietario($p)]);
                    }
                }
            }
        }
        return json_encode(['e' => 1, 'd' => 'El mensaje no pudo ser enviado, intente nuevamente']);
    }

    /*
     * Obtiene las consultas de un Propietario particular
     */

    public function gcp() {
        if (isset($this->request->data['t']) && isset($this->request->data['p'])) {
            $email = $this->_checkToken($this->request->data['t']);
            if ($email != '') {//email valido
                $p = $this->_decryptUrl($this->request->data['p']);
                if (!empty($p) && filter_var($p, FILTER_VALIDATE_INT)) { // propietario_id cifrado es un entero
                    //$prop = $this->_getPropietariosConEmailChat($email);
                    //if (isset($prop[$p])) {
                    //    $cid = $prop[$p];
                    return json_encode(['e' => 0, 'd' => $this->_getConsultasPropietario($p)]);
                    //}
                }
            }
        }
        return json_encode(['e' => 1, 'd' => 'No se pudo obtener el chat']);
    }

    /*
     * Valida que el token $t no sea vacio, que se componga de 2 parametros y q sea el email y el codigo correcto del mismo 
     */

    private function _checkToken($t) {
        $a = $this->_decryptURL($t);
        if (empty($a)) {
            return ''; // el token es incorrecto
        }
        $t1 = explode("##", $a);
        if (empty($t1) || count($t1) != 2) {
            return ''; // el token es incorrecto
        }
        if (!filter_var($t1[0], FILTER_VALIDATE_EMAIL) || $this->_obtieneCodigoEmail($t1[0]) != $t1[1]) {
            return ''; // el token es incorrecto
        }
        return $t1[0];
    }

    /*
     * Obtiene las ultimas liquidaciones, los reportes en cola de los Propietarios 
     */

    private function _getData($propietariosId) {
        $l = ClassRegistry::init('Liquidation');
        $liquidaciones = $l->getLastLiquidationsFromConsorcio($propietariosId);
        $cola = ClassRegistry::init('Colaimpresione');
        $reportesencola = $cola->getReportesenCola($propietariosId);
        return ['l' => $liquidaciones, 'c' => $reportesencola, 'h' => $this->_encryptProp($propietariosId)]; //h son los encryptUrl de los propietario_id
    }

    /*
     *  obtiene los Propietarios asociados al email de Clientes habilitados q utilicen la App
     */

    private function _getPropietariosConEmail($e) {
        $prop = ClassRegistry::init('Propietario');
        return $prop->find('list', ['conditions' => ['Propietario.email like' => '%' . $e . '%', 'Propietario.sistema_online' => 1, 'Client.enabled' => true],
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                        ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]],
                    'fields' => ['Propietario.id', 'Consorcio.id']
        ]);
    }

    /*
     *  obtiene los Propietarios asociados al email de Clientes habilitados q utilicen la App y tengan habilitadas las Consultas Propietario
     */

    private function _getPropietariosConEmailChat($e) {
        $prop = ClassRegistry::init('Propietario');
        return $prop->find('list', ['conditions' => ['Propietario.email like' => '%' . $e . '%', 'Propietario.sistema_online' => 1, 'Client.enabled' => true, 'Client.consultaspropietarios' => true],
                    'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                        ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]],
                    'fields' => ['Propietario.id', 'Consorcio.id']
        ]);
    }

    /*
     * El codigo de email se utiliza para el login en la App. El usuario debe ingresar su email y el codigo recibido en los avisos
     */

    private function _obtieneCodigoEmail($e) {
        $arr = str_split(strtolower($e) . "!$*~|#aA<>3bñ " . strtoupper($e));
        $total = 139099;
        foreach ($arr as $v) {
            $total += (ord($v) * ord($v));
        }
        return str_pad(substr(($total * $total), -6, 6), 6, 0, STR_PAD_RIGHT);
    }

    /*
     * Cifra los propietario_id para más seguridad al obtener las consultas de uno en particular 
     */

    private function _encryptProp($prop) {
        $resul = [];
        foreach ($prop as $k => $v) {//'Propietario.id', 'Consorcio.id'
            $resul[$k] = $this->_encryptUrl($k);
        }
        return $resul;
    }

    /*
     * Cifra una cadena
     */

    private function _encryptUrl($s) {
        $a = ClassRegistry::init('Aviso');
        return $a->_encryptUrl($s);
    }

    /*
     * Descifra una cadena
     */

    private function _decryptUrl($s) {
        try {
            $a = ClassRegistry::init('Aviso');
            return $a->_decryptUrl($s);
        } catch (Exception $ex) {
            return null;
        }
    }

    private function _getConsultasPropietario($p) {
        $a = ClassRegistry::init('Consultaspropietario');
        $options = ['conditions' => ['Consultaspropietario.propietario_id' => $p],
            'fields' => ['Consultaspropietario.mensaje as m', 'Consultaspropietario.es_respuesta as r', "DATE_FORMAT(Consultaspropietario.created,'%d/%m/%Y %T') as f"], 'order' => 'created desc'];
        return $a->find('all', $options);
    }

    private function _guardarConsultaPropietario($cli, $p, $m) {// desde el panel del administrador, seteo $l para saber q es respuesta
        $a = ClassRegistry::init('Consultaspropietario');
        $a->create();
        return $a->save(['client_id' => $cli, 'propietario_id' => $p, 'mensaje' => filter_var($m, FILTER_SANITIZE_STRING), 'es_respuesta' => 0, 'seen' => 0]);
    }

}
