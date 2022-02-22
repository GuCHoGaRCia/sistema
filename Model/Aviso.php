<?php

App::uses('AppModel', 'Model');

class Aviso extends AppModel {

    public $validate = [
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here!!',
            ],
        ],
    ];
    public $belongsTo = [
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

//    't_0_0' => '1',
//    't_0_1' => '2',
//    't_1_0' => '2',
//    't_1_1' => '6',
//    't_1_2' => '9',
//    ...
//    't_1_43' => '186',
//    't_1_44' => '187',
//    'Aviso' => array(
//        'consorcio_id' => array(
//            (int) 0 => '32',/////////// t_0_x son de este consorcio
//            (int) 1 => '34'///////////  t_1_x son de este consorcio
//        )
//    )
    /*
     * Guarda en la cola de avisos los mails seleccionados para enviar (pueden ser de varios consorcios simultaneamente)
     */
    public function guardar($data = null) {
        $consorcios = $data['Aviso']['consorcio_id'];
        unset($data['Aviso']);
        $cantenvios = $result = [];
        foreach ($consorcios as $a => $b) {//$b consorcio_id
            $cantenvios[$b] = 0;
            foreach ($data as $k => $v) {
                if (substr($k, strpos($k, '_') + 1, strrpos($k, '_') - 2) !== "$a") {// obtengo la XXX en 't_XXX_0' (el index del consorcio)
                    continue; // no es un propietario de este consorcio (busca en todo $data)
                }
                $propietario_id = $this->Propietario->getPropietarioId($b, $v);
                $email = $this->Propietario->getPropietarioEmail($propietario_id);
                $listaemails = explode(',', $email);
                $datoscliente = $this->Client->find('first', ['conditions' => ['Client.id' => $_SESSION['Auth']['User']['client_id']]]);
                foreach ($listaemails as $h => $j) {
                    $resul = $this->find('first', ['conditions' => ['Aviso.email' => $j, 'Aviso.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => 'Aviso.id']);
                    if (!empty($resul)) {
                        $this->save(['id' => $resul['Aviso']['id'], 'created' => date("Y-m-d H:i:s"), 'client_id' => $_SESSION['Auth']['User']['client_id']], false);
                    } else {
                        $this->create();
                        $this->save(['client_id' => $_SESSION['Auth']['User']['client_id'], 'email' => $j, 'eventos' => '', 'enviado' => date("Y-m-d H:i:s")], false);
                    }
                    if (!$this->Client->Avisosblacklist->isBlacklisted($j) /* && $this->Client->Avisosqueue->find('count', ['conditions' => ['Avisosqueue.mailto' => $j]]) == 0 */) {
                        $cantenvios[$b]++;
                        $result[] = ['j' => $j, 'd' => $datoscliente, 'w' => $this->Propietario->getPropietarioWhatsapp($propietario_id)];
                    }
                }
            }
        }
        $this->Client->Avisosenviado->sumaEnvios($cantenvios);
        return $result;
    }

    /*
     * Al bloquear de la cola de impresiones una liquidacion, se encolan los avisos para enviar
      (int) 0 => array(
      'Propietario' => array(
      'n' => 'CORIGLIANO SALVADOR',
      'c' => '5',
      'e' => 'saralegui.mercenick@gmail.com,lala@lalal.com',
      'u' => '1ºB',
      'id' => '254'
      )
      ),
      (int) 1 => array(
     */

    public function enviarLink($liquidation_id, $cliente = null) {
        $propietarios = $this->Propietario->getPropietariosLinkData($this->Propietario->Consorcio->Liquidation->getConsorcioId($liquidation_id));
        $datoscliente = $this->Client->find('first', ['conditions' => ['Client.id' => !empty($cliente) ? $cliente : $_SESSION['Auth']['User']['client_id']]]);
        foreach ($propietarios as $k => $v) {
            // si tiene mas de un mail, lo encolo igual
            $emails = array_unique(explode(",", $v['Propietario']['e']));
            foreach ($emails as $d) {
                if (filter_var($d, FILTER_VALIDATE_EMAIL)) { // verifico q sea un mail valido
                    $this->encolarAviso($d, $datoscliente, $v['Propietario']['w']);
                }
            }
        }
    }

    /*
     * Envio de mails genérico. Por defecto envia el link, en caso de necesitar mandar otro mail distinto, completar $asunto, $text y $html
     */

    public function enviarAviso($email, $asunto = null, $text = null, $html = null) {
        $link = $this->_encryptURL($email);

        $html2 = utf8_encode(!empty($html) ? $html : "Sr. Propietario:<br><br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Actualizamos la informaci&oacute;n disponible de sus Propiedades. Para visualizarla ingrese <b>"
                . "<a href='https://ceonline.com.ar/p/?avisos/view/$link' target='_blank'>AQUI</a></b>"
                . "<br><br>Atte. Su Administrador");
        $text2 = utf8_encode(!empty($text) ? $text : "Sr. Propietario: Actualizamos la informaci&oacute;n disponible de sus Propiedades. Para visualizarla ingrese aqui
                https://ceonline.com.ar/p/?avisos/view/$link . Atte. Su Administrador");

        $this->Client->Avisosqueue->addQueue('', !empty($asunto) ? utf8_encode($asunto) : 'Acceso a sus expensas Online', $html2, $text2, $email);
    }

    /*
     * Encola un aviso para enviar x mail si no se encuentra ni en la blacklist ni en la cola
     */

    public function encolarAviso($email, $datoscliente = null, $whatsapp = '') {
        if (!$this->Client->Avisosblacklist->isBlacklisted($email)) {
            $link = $this->_encryptURL($email);
            if (!empty($link)) {// x las dudas para no mandar cualquier boludez en el mail
                $url = /* Configure::read('debug') == 0 ? "181.231.115.147:3333" : */"ceonline.com.ar/p/?";
                $html = ("Sr. Propietario:<br><br>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;Actualizamos la informaci&oacute;n disponible de sus Propiedades. Para visualizarla ingrese <b>"
                        . "<a href='https://$url" . "avisos/view/$link' target='_blank'>AQUI</a></b>.<br><br>") . (!empty($datoscliente) ? ($datoscliente['Client']['name']) : 'Su Administrador de Consorcios');
                $text = ("Sr. Propietario: Actualizamos la informacion disponible de sus Propiedades. Para visualizarla ingrese en "
                        . "https://$url" . "avisos/view/$link ") . (!empty($datoscliente) ? ($datoscliente['Client']['name']) : 'Su Administrador de Consorcios');
                $emails = explode(",", $datoscliente['Client']['email']);
                $emailfrom = $emails[0];
                $this->Client->Avisosqueue->create(); // el client_id se guarda en tabla avisos ejecutar el cron.php!!
                $this->Client->Avisosqueue->save(['client_id' => empty($datoscliente) ? $_SESSION['Auth']['User']['client_id'] : $datoscliente['Client']['id'],
                    'emailfrom' => $emailfrom, 'razonsocial' => !empty($datoscliente) ? $datoscliente['Client']['name'] : 'CEONLINE',
                    'asunto' => 'Acceso a sus Expensas Online', 'altbody' => $text, 'codigohtml' => $html, 'mailto' => strtolower($email), 'whatsapp' => $whatsapp], false);
            }
        }
    }

    /*
     * Guarda los avisos para los mails enviados por CEONLINE a los clientes (Ej: Facturas electronicas)
     */

    public function guardaAvisoClientes($email) {
        // guardo el aviso para tener un registro de esos mails en /panel
        $aviso = $this->find('first', ['conditions' => ['Aviso.email' => $email], 'fields' => 'Aviso.id']);
        if (empty($aviso)) {
            $this->create();
            $this->save(['propietario_id' => 0, 'eventos' => '', 'client_id' => $_SESSION['Auth']['User']['client_id'], 'email' => $email, 'enviado' => date("Y-m-d H:i:s")]);
        } else {
            $this->save(['id' => $aviso['Aviso']['id'], 'propietario_id' => 0, 'eventos' => '', 'client_id' => $_SESSION['Auth']['User']['client_id'], 'email' => $email, 'enviado' => date("Y-m-d H:i:s")]);
        }
    }

    /*
     * Viene de AvisosController::view()
     * Funcion que verifica si existe el Propietario solicitado y de ser asi, retorna los datos del mismo 
     * Los reportes retornados son aquellos que se encuentren "Listos" e "Impresos / Online" en la cola de impresiones
     * (liquidaciones, resumenes de gastos, resumenes de cuenta, composiciones, notas, adjuntos, reparaciones)
     */

    public function getDatos($link) {
        $emails = explode(',', $this->_decryptURL($link));
        if (empty($emails)) {
            return false;
        }
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                return false;
            }
        }

        // busco todos los id del propietario Habilitado ONLINE y sus respectivos cliente/consorcios q coincidan con el email recibido
        //(propietario_id => consorcio_id)
        $idPropietariosyConsorcios = [];
        foreach ($emails as $e) {
            $x = $this->Propietario->getPropietarioIdFromEmail($e, 'all'); // obtengo los id de todos los propietarios con ese email. Si no existe, devuelve cero
            if (!empty($x)) {
                $idPropietariosyConsorcios += $x;
            }
        }
        if (empty($idPropietariosyConsorcios)) {
            return []; // el propietario no se encuentra online
        }
        $consultas = $bancos = $formasdepago = $amenities = [];
        foreach ($idPropietariosyConsorcios as $k => $v) {
            $cli = $this->Client->Consorcio->getConsorcioClientId($this->Propietario->getPropietarioConsorcio($k));
            $consultas[$k]['client'] = $cli;
            $formasdepago[$cli] = $this->Client->Formasdepago->find('list', ['conditions' => ['Formasdepago.client_id' => $cli]]);
            $bancos[$cli] = $this->Client->Banco->find('list', ['conditions' => ['Banco.client_id' => $cli]]);
            $consultas[$k]['c'] = json_encode($this->Client->Consultaspropietario->getConsultasPropietario($k, $cli));
            $consultasadjuntos = $this->Client->Consultaspropietariosadjunto->find('all', ['conditions' => ['Consultaspropietariosadjunto.client_id' => $cli, 'Consultaspropietariosadjunto.propietario_id' => $k], 'fields' => ['Consultaspropietariosadjunto.ruta as r', "DATE_FORMAT(Consultaspropietariosadjunto.created,'%d/%m/%Y %T') as f"], 'order' => 'Consultaspropietariosadjunto.created desc']);
            $consultas[$k]['a'] = json_encode($consultasadjuntos);
            $amenities[$k] = $this->Client->Amenity->getAll($v, $cli);
        }
        $reparaciones = $this->getReparaciones($idPropietariosyConsorcios);
        // retorno todos los propietarios y sus consorcios y liquidaciones, mas las reparaciones. Los re
        $cola = ClassRegistry::init('Colaimpresione');
        $liquidation = ClassRegistry::init('Liquidation');
        return ['reparaciones' => $reparaciones, 'datos' => $liquidation->getLastLiquidationsFromConsorcio($idPropietariosyConsorcios), 'encola' => $cola->getReportesenCola($idPropietariosyConsorcios),
            'online' => $idPropietariosyConsorcios, 'consultas' => $consultas, 'listaconsorcios' => $idPropietariosyConsorcios, 'bancos' => $bancos, 'formasdepago' => $formasdepago, 'amenities' => $amenities];
    }

    /*
     * Obtengo las reparaciones del propietario seleccionado (los id q tengan el mismo mail) y las reparaciones que sean de los consorcios asociados
     */

    public function getReparaciones($ids) {
        if (!empty($ids)) {
            foreach ($ids as $k => $v) {//$k=propietario, $v=consorcio
                $ks[] = $k;
                $vs[] = $v;
            }
            return $this->Propietario->getReparaciones(array_values(array_unique($vs)), array_values(array_unique($ks)));
        }
        return [];
    }

    public function eventos($data) {
        $r = json_decode($data, true);
        if (!empty($r) && json_last_error() === JSON_ERROR_NONE) {
            foreach ($r as $v) {
                if (isset($v['email']) && isset($v['event'])) {
                    $d = ['email' => $v['email']];
                    // el mail llegó, rebotó o hizo click el propietario
                    // si no encuentra el id de propietario, es un mail enviado por las consultas (por Mariana x ejemplo) o es un propietario que no está cargado todavia
                    // en el sistema. Asigno el client_id al nuestro
                    $resul = $this->find('all', ['conditions' => ['email' => $v['email']], 'fields' => 'Aviso.id']);
                    if (!empty($resul)) {
                        foreach ($resul as $s => $t) {
                            $d = ['id' => $t['Aviso']['id']];
                            $d += $this->procesarEventos($v);
                            $this->save($d);
                        }
                    } else {
                        $this->create();
                        $d += $this->procesarEventos($v);
                        $this->save($d, ['callbacks' => false]);
                    }

                    if ($v['event'] == 'bounce' || $v['event'] == 'dropped') {
                        $this->blacklist($v['email']);
                    }
                }
            }
        }
    }

    public function procesarEventos($v) {
        $d = [];
        if ($v['event'] == 'click') {
            $d += ['click' => date("Y-m-d H:i:s", $v['timestamp'] /* + 60 * 4 */)];
        }
        if ($v['event'] == 'delivered') {
            $d += ['recibido' => date("Y-m-d H:i:s", $v['timestamp'] /* + 60 * 4 */), 'cantrecibido' => 'cantrecibido+1']; // le sumo 4 minutos porq esta desfasado el horario de sendgrid con el nuestro
        }
        if ($v['event'] == 'bounce' || (isset($v['type']) && $v['type'] == 'bounce') || $v['event'] == 'dropped') {
            $d += ['eventos' => $v['reason'], 'rechazado' => date("Y-m-d H:i:s", $v['timestamp'] /* + 60 * 4 */)];
        }
        return $d;
    }

    public function blacklist($v) {
        // para los clientes q usan el sistema nuevo!
        $resp = $this->Client->Avisosblacklist->find('count', ['conditions' => ['Avisosblacklist.email' => $v]]);
        if ($resp != 0) {
            $this->query("update avisosblacklists set cantidad=cantidad+1,dsc='no existe' where email='" . $v . "' limit 1");
        } else {
            $this->query("insert into avisosblacklists (client_id,email,cantidad,created,modified,dsc) values (0,'" . $v . "',0,now(),now(),'no existe');"); // va 0 de client_id porq es un proceso q viene iniciado por Sendgrid
        }
    }

    /*
     * Obtiene los links de los propietarios de el/los consorcios seleccionados en avisos/add o comunicaciones/add
     */

    public function getPropietarios($consorcio_id) {
        $p = [];
        if (!empty($consorcio_id)) {
            foreach ($consorcio_id as $j => $l) {
                $prop = $this->Propietario->getPropietariosLinkData($l);
                $name = $this->Propietario->Consorcio->getConsorcioName($l);
                foreach ($prop as $k => $v) {
                    $emails = explode(",", $v['Propietario']['e']);
                    $p[$j][$k] = $v;
                    $p[$j][$k]['n'] = $name;
                    $p[$j][$k]['e'] = $v['Propietario']['e'];
                    $p[$j][$k]['cid'] = $l;
                    $p[$j][$k]['Propietario']['l'] = $this->_encryptURL($emails[0]);
                }
            }
        }
        return $p;
    }

    /**
     * Devuelve un id cifrado en formato url
     *
     * @param integer $textoplano
     * @return string $textocifrado
     * http://book.cakephp.org/2.0/en/core-utility-libraries/security.html#Security::rijndael
     */
    public function _encryptURL($textoplano) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $textocifrado = strtr(base64_encode($iv . openssl_encrypt($textoplano, 'AES-256-CBC', Configure::read('Security.key'), OPENSSL_RAW_DATA, $iv)), '+/=', '-_,');
        if (strlen($textocifrado) > 0) {
            return $textocifrado;
        }
        return null; // error
    }

    /**
     * Devuelve un texto a partir de una url cifrada
     *
     * @param string $textocifrado
     * @return string $textoplano
     * http://book.cakephp.org/2.0/en/core-utility-libraries/security.html#Security::rijndael
     */
    public function _decryptURL($textocifrado) {
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

    /**
     * Redirecciona desde "Miembros" a una url de acceso al panel del miembro
     *
     * @param CakeRequest $request
     * @return redirect
     */
    public function link($id = null) {
        $this->redirect(['controller' => 'reports', 'action' => 'index', $this->_encryptURL($id)]);
    }

    // funcion de busqueda
    public function filterName($data, $field = null) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Aviso.email like' => '%' . trim($data['buscar']) . '%',
            ]
        ];
    }

}
