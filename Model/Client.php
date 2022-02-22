<?php

App::uses('AppModel', 'Model');

class Client extends AppModel {

    public $validate = array(
        'code' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cuit' => [
            'escuit' => [
                'rule' => '/^[0-9]{2}-[0-9]{8}-[0-9]$/',
                'message' => 'El formato del CUIT es incorrecto. EJ: 20-30799986-3',
                'allowEmpty' => true,
            ],
            'validarCuit' => [
                'rule' => ['validarCuit'],
                'message' => "El CUIT no es correcto, verifique el mismo por favor",
            ]
        ],
        'address' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'city' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'telephone' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'identificador_cliente' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'unique' => array(
                'rule' => array('isUnique'),
                'message' => 'Ya existe un Cliente con este identificador, elija otro',
            ),
        ),
        'numeroregistro' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'web' => [
            'web' => [
                'rule' => 'url',
                'message' => 'Ingrese una web completa, ej: https://ceonline.com.ar',
                'allowEmpty' => true,
            ],
        ],
        'comision' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'imprime_talon_banco' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'talonbancoprefijo' => array(
            'numeric' => [
                'rule' => ['range', 1, 9999],
                'message' => 'El número debe ser mayor o igual a 1',
            ],
        ),
        'talonbancocodigo' => [
            'numeric' => [
                'rule' => ['range', 1, 9999],
                'message' => 'El número debe ser mayor o igual a 1',
            ],
        ],
        'talonbancocomision' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'talonbancominimo' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'email' => array(
            'email' => array(
                'rule' => ['checkEmails'],
                'message' => 'El formato del email es incorrecto. Ej: juan@gmail.com. Si desea agregar mas de un email, separelos con coma y sin espacios. Ej: juan@gmail.com,pepe@hotmail.com',
                'allowEmpty' => true
            ),
        ),
    );
    public $hasMany = array(
        'Colaimpresione' => array(
            'className' => 'Colaimpresione',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'LiquidationsType' => array(
            'className' => 'LiquidationsType',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Proveedor' => array(
            'className' => 'Proveedor',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Carta' => array(
            'className' => 'Carta',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Cartasprecio' => array(
            'className' => 'Cartasprecio',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Consulta' => array(
            'className' => 'Consulta',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Consultaspropietario' => array(
            'className' => 'Consultaspropietario',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Consultaspropietariosadjunto' => array(
            'className' => 'Consultaspropietariosadjunto',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Consultasadjunto' => array(
            'className' => 'Consultasadjunto',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Informepago' => array(
            'className' => 'Informepago',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Reportsclient' => array(
            'className' => 'Reportsclient',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Banco' => array(
            'className' => 'Banco',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Cheque' => array(
            'className' => 'Cheque',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Chequespropio' => array(
            'className' => 'Chequespropio',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Chequespropiosadm' => array(
            'className' => 'Chequespropiosadm',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Caja' => array(
            'className' => 'Caja',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Avisosqueue' => array(
            'className' => 'Avisosqueue',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Avisosblacklist' => array(
            'className' => 'Avisosblacklist',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Aviso' => array(
            'className' => 'Aviso',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Formasdepago' => array(
            'className' => 'Formasdepago',
            'foreignKey' => 'client_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Email' => array(
            'className' => 'Email',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Audit' => array(
            'className' => 'Audit',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Comunicacione' => array(
            'className' => 'Comunicacione',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Reparacionessupervisore' => array(
            'className' => 'Reparacionessupervisore',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Avisosenviado' => array(
            'className' => 'Avisosenviado',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Llave' => array(
            'className' => 'Llave',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Saldoscajabanco' => array(
            'className' => 'Saldoscajabanco',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Plataformasdepagosconfig' => array(
            'className' => 'Plataformasdepagosconfig',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Amenity' => array(
            'className' => 'Amenity',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Llamado' => array(
            'className' => 'Llamado',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Llamadosadjunto' => array(
            'className' => 'Llamadosadjunto',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Reparacionesestado' => array(
            'className' => 'Reparacionesestado',
            'foreignKey' => 'client_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function canEdit($id) {
        return (isset($_SESSION['Auth']['User']['client_id']) && $id == $_SESSION['Auth']['User']['client_id']);
    }

    /*
     * obtengo los tipos de liq del cliente, todas o las habilitadas solamente?
     */

    public function getClientLiquidationsTypes($client = null) {
        $options = array('conditions' => array('LiquidationsType.client_id' => empty($client) ? $_SESSION['Auth']['User']['client_id'] : $client, 'LiquidationsType.enabled' => 1));
        return $this->LiquidationsType->find('all', $options);
    }

    public function getClientName($id) {
        $this->id = $id;
        return $this->field('name');
    }

    public function getClientCode($id) {
        $this->id = $id;
        return $this->field('code');
    }

    public function getClientComision($id) {
        $this->id = $id;
        return $this->field('comision');
    }

    public function cargaGPdeCartas($id) {
        $this->id = $id;
        return $this->field('cargagpdecartas');
    }

    public function controlaNumFactura($id) {
        $this->id = $id;
        return $this->field('controla_numFactura');
    }

    public function usaPlapsa($id) {
        $this->id = $id;
        return $this->field('usa_plapsa');
    }

    public function getClientIdFromEmail($email) {
        $options = array('conditions' => array('Client.email' => $email), 'recursive' => -1, 'fields' => 'Client.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Client']['id']);
    }

    public function getClientIdFromMultipleEmails($email) {
        $options = array('conditions' => array('Client.email like' => $email . '%'), 'recursive' => -1, 'fields' => 'Client.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Client']['id']);
    }

    public function getClientId($code) {
        $options = array('conditions' => array('Client.code' => $code), 'recursive' => -1, 'fields' => 'Client.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Client']['id']);
    }

    /*
     * Chequea que el identificador pertenezca a un cliente habilitado
     */

    public function isIdentificadorValido($identificador) {
        $options = array('conditions' => array('Client.identificador_cliente' => $identificador, 'Client.enabled' => 1), 'fields' => 'Client.id');
        $r = $this->find('first', $options);
        return (bool) (!empty($r));
    }

    /*
     * Chequea si es un identificador válido perteneciente a un cliente habilitado y devuelve el id del cliente, sino cero
     */

    public function getClientIdFromIdentificador($identificador) {
        if ($this->isIdentificadorValido($identificador)) {
            $options = array('conditions' => array('Client.identificador_cliente' => $identificador, 'Client.enabled' => 1), 'fields' => 'Client.id');
            $r = $this->find('first', $options);
            return (empty($r) ? 0 : $r['Client']['id']);
        }
        return 0;
    }

    public function getCartaDeudores($id) {
        $this->id = $id;
        return $this->field('cartadeudores');
    }

    public function getRecordatorioPago($id) {
        $this->id = $id;
        return $this->field('recordatoriopago');
    }

    public function getCuerpoEmailAvisos($id) {
        $this->id = $id;
        return $this->field('cuerpoemailaviso');
    }

    public function beforeDelete($cascade = true) {
        if ($this->id !== '1') {
            return true;
        }
        return false;
    }

    public function guardar($data) {
        $cid = "";
        if (!isset($data->data['Client']['id'])) {
            $this->create();
        } else {
            $cid = $data->data['Client']['id'];
        }
        $resul = $this->save($data->data);
        if (!$resul) {
            return __("Ocurrió un error al guardar el Cliente");
        }
        return $this->__guardarLogoFirma($data->params, empty($cid) ? $this->id : $cid);
    }

    private function __guardarLogoFirma($data, $client_id) {
        // verifico si subió el logo o firma
        //array(
        //    'plugin' => null,
        //    'controller' => 'clients',
        //    'action' => 'panel_add',
        //    'named' => array(),
        //    'pass' => array(),
        //	'form' => array(
        //		'logoadm' => array(
        //			'name' => '6003.jpg',
        //			'type' => 'image/jpeg',
        //			'tmp_name' => 'C:\xampp\tmp\phpFFDE.tmp',
        //			'error' => (int) 0,
        //			'size' => (int) 58914
        //		),
        //		'firmaadm' => array(
        //			'name' => 'anulado.png',
        //			'type' => 'image/png',
        //			'tmp_name' => 'C:\xampp\tmp\phpFFDF.tmp',
        //			'error' => (int) 0,
        //			'size' => (int) 9492
        //		)
        //	),
        //    'prefix' => 'panel',
        //    'panel' => true
        //)
        $errores = "";
        if (isset($data['form']) && count($data['form']) > 0) {
            foreach ($data['form'] as $a => $v) {
                $ext = substr($v['name'], strrpos($v['name'], ".") + 1, strlen($v['name']));
                if ($v['error'] == 0 && in_array($ext, ['jpg', 'jpeg', 'png']) && $this->Consorcio->Liquidation->Adjunto->checkMimeType($v['tmp_name'])) {
                    // si es una extension permitida, lo subo
                    $fileName = ($a == 'logoadm' ? $client_id . '.jpg' : 'firma.jpg');
                    $dir = APP . DS . WEBROOT_DIR . DS . 'files' . DS . $client_id;
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    if (!move_uploaded_file($v['tmp_name'], $dir . DS . $fileName)) {
                        // no lo pudo mover, sigo con el q sigue
                        $errores .= "El archivo " . h($v['name']) . " no pudo guardarse<br>";
                        continue;
                    }
                }
            }
        }
        return $errores;
    }

    /*
     * Creo la configuracion de los reportes para el cliente actual. Tambi�n creo los tipos de liquidaci�n por defecto
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            if (isset($this->data['Client']['id'])) {
                $id = $this->data['Client']['id'];
                $this->Reportsclient->create();
                $this->Reportsclient->save(['client_id' => $id, 'report_id' => 0]);
                $this->Reportsclient->create();
                $this->Reportsclient->save(['client_id' => $id, 'report_id' => 0]);

                // creo los tipos de liquidacion por defecto
                $this->LiquidationsType->create();
                $this->LiquidationsType->save(['client_id' => $id, 'name' => 'Expensa ordinaria', 'prefijo' => 0, 'enabled' => 1]);
                $this->LiquidationsType->create();
                $this->LiquidationsType->save(['client_id' => $id, 'name' => 'Expensa extraordinaria', 'prefijo' => 5, 'enabled' => 1]);
                $this->LiquidationsType->create();
                $this->LiquidationsType->save(['client_id' => $id, 'name' => 'Fondo', 'prefijo' => 9, 'enabled' => 1]);

                //creo los usuarios x defecto (esteban, ricardo, marcela, marce, adrian) y las cajas (User->aftersave)
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Esteban', 'username' => 'ecano', 'password' => $this->Aviso->_decryptURL('sX77rd-kM4J8XVki_6YKCkAFD79b84-N_jZjhswG6NE,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 21/08/2019
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Ricardo', 'username' => 'rcasco', 'password' => $this->Aviso->_decryptURL('hBW9OUNILKfcooaYDg7sVAhNg8x7Zm66hg6hwWMfTaY,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 21/08/2019
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Marcela', 'username' => 'mmazzei', 'password' => $this->Aviso->_decryptURL('K24q-ZA2EzzUqiSiWyZZmTCbuYv0ePNmIGZt-gsWjSA,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 21/08/2019 (sin especiales)
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Marce', 'username' => 'mcorzo', 'password' => $this->Aviso->_decryptURL('LJY7fTAps2TkzbLspUvY78CK5rmpiKhyfKrbDWSkuU4,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]);
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Adrian', 'username' => 'akohan', 'password' => $this->Aviso->_decryptURL('xVPWWwPBW7J2Bj5_02xkri_QUYf4jKn_yvjCyiIwSb8,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // no modificó un pomo
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Mariana', 'username' => 'mpetrek', 'password' => $this->Aviso->_decryptURL('UKTKp7MwFkoAHL9iIHDyc_w2f8OEtve6WbCUexKzDuY,﻿'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 21/08/2019
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Laura', 'username' => 'mlmazzei', 'password' => $this->Aviso->_decryptURL('qE18yT-OT4aowRr5_WcKWxEv4llbTtrzc8yuTmjC2Mk,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 02/10/2019
                $this->User->create();
                $this->User->save(['client_id' => $id, 'name' => 'Georgina', 'username' => 'gcingolani', 'password' => $this->Aviso->_decryptURL('DEFHA7-5jfe2Pz2CqeGqbgpIfHZlKYzdCqCjkrtnIOU,'), 'aceptaterminosycondiciones' => 1, 'enabled' => 1], ['validate' => false]); // modificada 02/10/2019
                // creo las formas de pago por defecto
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Transferencia', 'destino' => 2, 'orden' => 1, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Cobranza Automática', 'destino' => 2, 'orden' => 2, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Depósito', 'destino' => 2, 'orden' => 3, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Interdepósito', 'destino' => 2, 'orden' => 4, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Tarjeta de Crédito', 'destino' => 2, 'orden' => 5, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Tarjeta de Débito', 'destino' => 2, 'orden' => 6, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Efectivo', 'destino' => 1, 'orden' => 0, 'habilitada' => 1]);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $id, 'forma' => 'Cheque de Terceros', 'destino' => 1, 'orden' => 0, 'habilitada' => 1]);

                // creo los estados de reparaciones
                $this->Reparacionesestado->create();
                $this->Reparacionesestado->save(['client_id' => $id, 'nombre' => 'Pendiente']);
                $this->Reparacionesestado->create();
                $this->Reparacionesestado->save(['client_id' => $id, 'nombre' => 'En curso']);
                $this->Reparacionesestado->create();
                $this->Reparacionesestado->save(['client_id' => $id, 'nombre' => 'Enviada al consejo']);
                $this->Reparacionesestado->create();
                $this->Reparacionesestado->save(['client_id' => $id, 'nombre' => 'Suspendida']);
                $this->Reparacionesestado->create();
                $this->Reparacionesestado->save(['client_id' => $id, 'nombre' => 'Finalizada']);

                $this->Plataformasdepagosconfig->create();
                $this->Plataformasdepagosconfig->save(['client_id' => $id, 'plataformasdepago_id' => 0, 'datointerno' => 0, 'minimo' => 0, 'comision' => 0, 'codigo' => 0], ['callbacks' => false]);

                $c = $this->getCartaDeudores(1);
                $this->id = $id;
                $this->saveField('cartadeudores', $c); //Guardo la carta deudores con el valor por defecto q tiene el cliente CEONLINE

                $c = $this->getRecordatorioPago(1);
                $this->id = $id;
                $this->saveField('recordatoriopago', $c); //Guardo el recordatorio de pago con el valor por defecto q tiene el cliente CEONLINE

                $c = $this->getCuerpoEmailAvisos(1);
                $this->id = $id;
                $this->saveField('cuerpoemailaviso', $c); //Guardo el texto para el email de aviso (valor por defecto q tiene el cliente ceonline)
            }
        }
    }

    /*
     * Si es un link de un Propietario, trae $client_id cifrado, sino obtengo los datos del cliente a partir de la liquidacion
     */

    public function getClientInfo($liquidation_id, $client_id = null) {
        if (!empty($client_id)) {
            $email = $this->Consorcio->Propietario->Aviso->_decryptURL($client_id);
            $emails = explode(',', $email);
            if (empty($emails)) {
                return false;
            }
            $or = [];
            foreach ($emails as $e) {
                if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                    die("El dato es inexistente");
                }
                $or[] = ['c2.email like' => '%' . $e . '%'];
            }
            $options = array('conditions' => array('OR' => $or),
                //'fields' => ['Client.id', 'Client.code', 'Client.name', 'Client.cuit', 'Client.address', 'Client.city', 'Client.telephone', 'Client.email', 'Client.usa_plapsa', 'Client.comision', 'Client.numeroregistro'],
                'joins' => array(array('table' => 'consorcios', 'alias' => 'c1', 'type' => 'left', 'conditions' => array('c1.client_id=Client.id')),
                    array('table' => 'propietarios', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c1.id=c2.consorcio_id'))));
        } else {
            $options = array('conditions' => array('Liquidation.id' => $liquidation_id),
                'joins' => array(array('table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => array('Consorcio.client_id=Client.id')),
                    array('table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => array('Liquidation.consorcio_id=Consorcio.id'))));
        }
        $r = $this->find('first', $options);
        return (empty($r) ? null : $r);
    }

    /*
     * Para todas las administraciones actualizo los saldos cierres. Se utiliza cuando se agrega algun campo en alguna tabla y hace falta volver
     * a prorratear la misma (pero sin la necesidad de desbloquear y volver a bloquear)
     * Actualizo los valores de Cobranzas, Ajustes, Gastos generales y Gastos particulares
     */

    public function actualizaSaldosCierres() {
        $clientes = $this->find('list', ['conditions' => ['Client.enabled' => true]]);
        $this->Consorcio->Liquidation->SaldosCierre->unbindModel(['hasMany' => ['Audit']]);
        foreach ($clientes as $k => $v) {
            $consorcios = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $k]]);
            foreach ($consorcios as $l => $w) {
                // obtengo las Liquidaciones bloqueadas del Consorcio
                $liquidations = $this->Consorcio->Liquidation->find('list', ['conditions' => ['Liquidation.consorcio_id' => $l, 'Liquidation.bloqueada' => 1, 'Liquidation.cerrada' => 1], 'order' => 'Liquidation.closed,Liquidation.id']);
                $propietarios = $this->Consorcio->Propietario->find('list', ['conditions' => ['Propietario.consorcio_id' => $l]]);
                foreach ($liquidations as $m => $x) {
                    set_time_limit(1000);
                    $totales = $this->Consorcio->Liquidation->totalesProrrateoPropietario($m);
                    $cobranzas = $this->Consorcio->Liquidation->LiquidationsType->Cobranza->getCobranzas($m);
                    $saldosanteriores = $this->Consorcio->Liquidation->SaldosCierre->getSaldo($m); // si no existe, busca el saldo inicial
                    //$consor = $this->Liquidation->getConsorcioId($m);
                    //$coeficientes = $this->Liquidation->Consorcio->Coeficiente->find('list', array('conditions' => array('Consorcio.id' => $consor), 'recursive' => 0));
                    //$prop = $this->Liquidation->Consorcio->Propietario->getPropietarios($consor);
                    $ajustes = $this->Consorcio->Liquidation->Consorcio->Propietario->Ajuste->getAjustes($m);
                    $remanentes = $this->Consorcio->Liquidation->getSaldosRemanentes($saldosanteriores, $cobranzas, $ajustes);
                    $saldocierre = $this->Consorcio->Liquidation->SaldosCierre->calculaSaldoCierre($totales, $m, $remanentes, $saldosanteriores, $cobranzas, $ajustes); // en $saldosanteriores tengo los redondeos del cierre anterior
                    $saldocierre = Hash::combine($saldocierre, '{n}.SaldosCierre.propietario_id', '{n}.SaldosCierre');
                    if (empty($saldocierre)) {
                        continue;
                    }

                    foreach ($propietarios as $n => $y) {
                        $find = $this->Consorcio->Liquidation->SaldosCierre->find('list', ['conditions' => ['SaldosCierre.propietario_id' => $n, 'SaldosCierre.liquidation_id' => $m]]);
                        if (!empty($find)) {
                            // encontró el saldocierre de ese Propietario y Liquidacion, actualizo los valores de Cobranzas, Ajustes, Gastos generales y Gastos particulares
                            $id = key($find); // ej: key([1235 => 'xxx']) es igual a 1235 (el id)
                            $this->Consorcio->Liquidation->SaldosCierre->id = $id;
                            if (!isset($remanentes[$n])) {
                                continue; // aic san angelo 2 tiene problemas en agosto wtf
                            }
                            $capant = $saldosanteriores[$n]['capital'];
                            $intant = $saldosanteriores[$n]['interes'];
                            $this->Consorcio->Liquidation->SaldosCierre->save(['cobranzas' => $remanentes[$n]['cobranzas'], 'ajustes' => $remanentes[$n]['ajustes'],
                                'gastosgenerales' => $saldocierre[$n]['gastosgenerales'], 'gastosparticulares' => $saldocierre[$n]['gastosparticulares'], 'interesactual' => $saldocierre[$n]['interesactual'],
                                'capant' => $capant, 'intant' => $intant, 'redant' => $capant + $intant - intval($capant + $intant)]);
                        }
                    }
                }
            }
        }
        $this->Consorcio->Liquidation->SaldosCierre->bindModel(['hasMany' => ['Audit']]);
    }

    /*
     * Actualiza el numero de recibo de la cobranza manual o automatica para todos los clientes
     */

    public function actualizaNumeroReciboCobranza() {
        $clientes = $this->find('list');
        foreach ($clientes as $k => $v) {
            $this->query("SET @rank:=0; update cobranzas c join users u on c.user_id=u.id set c.numero=@rank:=@rank+1 where u.client_id=$k");
        }
    }

    /*
     * Genera las formas de pago de los Propietarios (para el Panel de Propietario y que utilicen Informe de pagos) para cada cliente que no tenga actualmente
     */

    public function generarFormasdePago() {
        $clients = $this->find('list');
        $this->Formasdepago->unbindModel(['hasMany' => ['Audit']]);
        foreach ($clients as $k => $v) {
            $resul = $this->Formasdepago->find('first', ['conditions' => ['Formasdepago.client_id' => $k]]);
            if (empty($resul)) {
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $k, 'forma' => 'Transferencia']);
                $this->Formasdepago->create();
                $this->Formasdepago->save(['client_id' => $k, 'forma' => 'Depósito']);
            }
        }
    }

    /*
     * Cifra el nombre del archivo adjunto y lo guarda en 'url' para más seguridad
     */

    public function cifrarURLAdjuntos() {
        set_time_limit(1000);
        $this->Consorcio->Liquidation->Adjunto->unbindModel(['hasMany' => ['Audit']]);
        foreach ($this->Consorcio->Liquidation->Adjunto->find('all', ['conditions' => ['Adjunto.url' => ''], 'fields' => ['Adjunto.ruta', 'Adjunto.id']]) as $k => $v) {
            $this->Consorcio->Liquidation->Adjunto->id = $v['Adjunto']['id'];
            $this->Consorcio->Liquidation->Adjunto->saveField('url', $this->Aviso->_encryptURL($v['Adjunto']['ruta']));
        }
        $this->Consorcio->Liquidation->Adjunto->bindModel(['hasMany' => ['Audit']]);

        $this->Consultasadjunto->unbindModel(['hasMany' => ['Audit']]);
        foreach ($this->Consultasadjunto->find('all', ['conditions' => ['Consultasadjunto.url' => ''], 'fields' => ['Consultasadjunto.ruta', 'Consultasadjunto.id']]) as $k => $v) {
            $this->Consultasadjunto->id = $v['Consultasadjunto']['id'];
            $this->Consultasadjunto->saveField('url', $this->Aviso->_encryptURL($v['Consultasadjunto']['ruta']));
        }
        $this->Consultasadjunto->bindModel(['hasMany' => ['Audit']]);

        $this->Consultaspropietariosadjunto->unbindModel(['hasMany' => ['Audit']]);
        foreach ($this->Consultaspropietariosadjunto->find('all', ['conditions' => ['Consultaspropietariosadjunto.url' => ''], 'fields' => ['Consultaspropietariosadjunto.ruta', 'Consultaspropietariosadjunto.id']]) as $k => $v) {
            $this->Consultaspropietariosadjunto->id = $v['Consultaspropietariosadjunto']['id'];
            $this->Consultaspropietariosadjunto->saveField('url', $this->Aviso->_encryptURL($v['Consultaspropietariosadjunto']['ruta']));
        }
        $this->Consultaspropietariosadjunto->bindModel(['hasMany' => ['Audit']]);

        $this->Informepago->Informepagosadjunto->unbindModel(['hasMany' => ['Audit']]);
        foreach ($this->Informepago->Informepagosadjunto->find('all', ['conditions' => ['Informepagosadjunto.url' => ''], 'fields' => ['Informepagosadjunto.ruta', 'Informepagosadjunto.id']]) as $k => $v) {
            $this->Informepago->Informepagosadjunto->id = $v['Informepagosadjunto']['id'];
            $this->Informepago->Informepagosadjunto->saveField('url', $this->Aviso->_encryptURL($v['Informepagosadjunto']['ruta']));
        }
        $this->Informepago->Informepagosadjunto->bindModel(['hasMany' => ['Audit']]);
    }

    /*
     * Actualizo el estado de disponibilidad de cada liquidacion
     */

    public function actualizaEstadoDisponibilidad() {
        set_time_limit(500);
        $client_id = $_SESSION['Auth']['User']['client_id'];
        $clientes = $this->find('list', ['conditions' => ['Client.enabled' => true]]);
        $this->Consorcio->Liquidation->SaldosCierre->unbindModel(['hasMany' => ['Audit']]);
        $this->Consorcio->Liquidation->unbindModel(['hasMany' => ['Audit']]);
        foreach ($clientes as $k => $v) {
            if (!(in_array($k, [104]))) {//in_array($k, [15,2,40,105,38,47,21,39,44,14])->son los clientes mas "pesados" al 01/12/2018
                continue;
            }
            $_SESSION['Auth']['User']['client_id'] = $k;
            $consorcios = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $k]]);
            foreach ($consorcios as $l => $w) {
                /* if (!($l >= 303 && $l <= 303)) {
                  continue;
                  } */
                // obtengo las Liquidaciones bloqueadas del Consorcio
                $liquidations = $this->Consorcio->Liquidation->find('list', ['conditions' => ['Liquidation.consorcio_id' => $l, 'Liquidation.bloqueada' => 1, 'Liquidation.cerrada' => 1], 'order' => 'Liquidation.closed,Liquidation.id']);
                foreach ($liquidations as $m => $x) {
                    // actualizo el estado de disponibilidad (sin actualizar la fecha de modificacion). El parametro true indica q es este proceso el q se ejecuta (la fecha hasta es la del ultimo prorrateo)
                    set_time_limit(500);
                    $disponibilidad = $this->Consorcio->Liquidation->calculaDisponibilidad($m, true);
                    $this->Consorcio->Liquidation->save(['id' => $m, 'disponibilidad' => $disponibilidad['disponibilidad'], 'disponibilidadpaga' => $disponibilidad['disponibilidadpaga'], 'modified' => false], false);
                }
            }
        }
        $_SESSION['Auth']['User']['client_id'] = $client_id;
        $this->Consorcio->Liquidation->SaldosCierre->bindModel(['hasMany' => ['Audit']]);
        $this->Consorcio->Liquidation->bindModel(['hasMany' => ['Audit']]);
    }

    /*
     * Actualizo el saldo de todas las cuentas bancarias teniendo en cuenta el total guardado en Saldoscajabancos (sadobancoefectivo + saldobancocheque)
     * Incluye los movimientos desde el saldo caja banco utilizado hasta la fecha actual.
     * Por eso, se puede ejecutar en cualquier momento porque incluirá los movimientos del dia hasta la hora actual
     */

    public function actualizaSaldoCuentasBancarias() {
        set_time_limit(500);
        $client_id = $_SESSION['Auth']['User']['client_id'];
        $clientes = $this->find('list', ['conditions' => ['Client.enabled' => true]]);
        $this->Consorcio->Bancoscuenta->unbindModel(['hasMany' => ['Audit']]);
        foreach ($clientes as $k => $v) {
            /* if ((!in_array($k, [14]))) {//in_array($k, [15,2,40,105,38,47,21,39,44])->son los clientes mas "pesados" al 01/12/2018
              continue;
              } */
            $_SESSION['Auth']['User']['client_id'] = $k;
            $consorcios = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $k]]);
            $dia = date("Y-m-d", strtotime(date("Y-m-d") . " -1 day"));
            $siguiente = date("Y-m-d", strtotime($dia . " +1 day")) . " 00:00:00";
            foreach ($consorcios as $l => $w) {
                $saldo = $this->Saldoscajabanco->getSaldos($l, $dia);
                $cuentas = array_keys($this->Consorcio->Client->Banco->Bancoscuenta->getCuentasBancarias($l));
                if (!empty($cuentas) && !empty($saldo)) {
                    foreach ($cuentas as $b => $n) {
                        $resumen = $this->Consorcio->Client->Caja->getTotalesMovimientosResumen($l, $siguiente, date("Y-m-d H:i:s"), 1); //incluye los anulados!
                        $total = ($saldo['saldobancoefectivo'] - $resumen['egresosdebitos'] - $resumen['ingresosextracciones']);
                        $total += $saldo['saldobancocheque'] + $resumen['ingresostransferencias'] + $resumen['ingresostransferenciasinterbancos'] + $resumen['ingresoscreditos'] + $resumen['bancosdepositosefectivo'] + $resumen['bancosdepositoscheques'] - ($resumen['egresospagosproveedorchequepropio'] + $resumen['egresospagosproveedortransferencia'] + $resumen['egresostransferenciasinterbancos']);
                        $this->Consorcio->Bancoscuenta->save(['id' => $n, 'saldo' => $saldo['saldobancoefectivo'] + $saldo['saldobancocheque'], 'modified' => false], ['callbacks' => false]);
                    }
                }
            }
        }
        $_SESSION['Auth']['User']['client_id'] = $client_id;
        $this->Consorcio->Bancoscuenta->bindModel(['hasMany' => ['Audit']]);
    }

    /*
     * Actualizo el saldo de todas las cuentas bancarias teniendo en cuenta el total guardado en Saldoscajabancos (sadobancoefectivo + saldobancocheque)
     * Incluye los movimientos desde el saldo caja banco utilizado hasta la fecha actual.
     * Por eso, se puede ejecutar en cualquier momento porque incluirá los movimientos del dia hasta la hora actual
     */

    public function actualizaSaldoCajas() {
        set_time_limit(500);
        $client_id = $_SESSION['Auth']['User']['client_id'];
        $clientes = $this->find('list', ['conditions' => ['Client.enabled' => true]]);
        $this->Caja->unbindModel(['hasMany' => ['Audit']]);
        foreach ($clientes as $k => $v) {
            if (!in_array($k, [115])) {//in_array($k, [15,2,40,105,38,47,21,39,44])->son los clientes mas "pesados" al 01/12/2018
                continue;
            }
            $_SESSION['Auth']['User']['client_id'] = $k;
            $cajas = $this->Caja->find('list', ['conditions' => ['Caja.client_id' => $k]]);
            foreach ($cajas as $l => $w) {
                $i = $this->query("SELECT sum(ci.importe) as ipesos,sum(ci.cheque) as icheques FROM cajasingresos ci where ci.caja_id=$l");
                $e = $this->query("SELECT sum(ce.importe) as epesos,sum(ce.cheque) as echeques FROM cajasegresos ce where ce.caja_id=$l");
                $ipesos = $icheques = $epesos = $echeques = 0;
                $hay = false;
                if (!is_null($i[0][0]['ipesos'])) {
                    $ipesos += $i[0][0]['ipesos'];
                    $hay = true;
                }
                if (!is_null($i[0][0]['icheques'])) {
                    $icheques += $i[0][0]['icheques'];
                    $hay = true;
                }
                if (!is_null($e[0][0]['epesos'])) {
                    $epesos += $e[0][0]['epesos'];
                    $hay = true;
                }
                if (!is_null($e[0][0]['echeques'])) {
                    $echeques += $e[0][0]['echeques'];
                    $hay = true;
                }
                //debug($ipesos - $epesos);
                //debug($icheques - $echeques);
                if ($hay) {
                    $this->Caja->save(['id' => $l, 'saldo_pesos' => $ipesos - $epesos, 'saldo_cheques' => $icheques - $echeques], ['callbacks' => false]);
                }
            }
        }
        $_SESSION['Auth']['User']['client_id'] = $client_id;
        $this->Caja->bindModel(['hasMany' => ['Audit']]);
    }

    public function processCleanHTML() {
        /* $notas = $this->Consorcio->Liquidation->Nota->find('list');
          set_time_limit(5000);
          foreach ($notas as $k => $v) {
          $this->Consorcio->Liquidation->Nota->id = $k;
          $this->Consorcio->Liquidation->Nota->save(['id' => $k,
          'resumencuenta' => $this->cleanHTML($this->Consorcio->Liquidation->Nota->field('resumencuenta')),
          'resumengasto' => $this->cleanHTML($this->Consorcio->Liquidation->Nota->field('resumengasto')),
          'resumengastotop' => $this->cleanHTML($this->Consorcio->Liquidation->Nota->field('resumengastotop')),
          'composicion' => $this->cleanHTML($this->Consorcio->Liquidation->Nota->field('composicion')),
          'created' => $this->Consorcio->Liquidation->Nota->field('created'),
          'modified' => $this->Consorcio->Liquidation->Nota->field('modified')], ['callbacks' => false]);
          }
          set_time_limit(5000);
          $gg = $this->Consorcio->Liquidation->GastosGenerale->find('list');
          foreach ($gg as $k => $v) {
          $this->Consorcio->Liquidation->GastosGenerale->id = $k;
          $this->Consorcio->Liquidation->GastosGenerale->save(['id' => $k, 'description' => $this->cleanHTML($this->Consorcio->Liquidation->GastosGenerale->field('description')),
          'created' => $this->Consorcio->Liquidation->GastosGenerale->field('created'),
          'modified' => $this->Consorcio->Liquidation->GastosGenerale->field('modified')], ['callbacks' => false]);
          } */
        set_time_limit(5000);
        $gg = $this->Comunicacione->find('list');
        foreach ($gg as $k => $v) {
            $this->Comunicacione->id = $k;
            $this->Comunicacione->save(['id' => $k, 'mensaje' => $this->cleanHTML($this->Comunicacione->field('mensaje')),
                'created' => $this->Comunicacione->field('created'),
                'modified' => $this->Comunicacione->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = $this->Consorcio->Reparacione->find('list');
        foreach ($gg as $k => $v) {
            $this->Consorcio->Reparacione->id = $k;
            $this->Consorcio->Reparacione->save(['id' => $k, 'observaciones' => $this->cleanHTML($this->Consorcio->Reparacione->field('observaciones')),
                'created' => $this->Consorcio->Reparacione->field('created'),
                'modified' => $this->Consorcio->Reparacione->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = $this->Consorcio->Reparacione->Reparacionesactualizacione->find('list');
        foreach ($gg as $k => $v) {
            $this->Consorcio->Reparacione->Reparacionesactualizacione->id = $k;
            $this->Consorcio->Reparacione->Reparacionesactualizacione->save(['id' => $k, 'observaciones' => $this->cleanHTML($this->Consorcio->Reparacione->Reparacionesactualizacione->field('observaciones')),
                'created' => $this->Consorcio->Reparacione->Reparacionesactualizacione->field('created'),
                'modified' => $this->Consorcio->Reparacione->Reparacionesactualizacione->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = $this->Consorcio->find('list');
        foreach ($gg as $k => $v) {
            $this->Consorcio->id = $k;
            $this->Consorcio->save(['id' => $k, 'description' => $this->cleanHTML($this->Consorcio->field('description')),
                'created' => $this->Consorcio->field('created'),
                'modified' => $this->Consorcio->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = ClassRegistry::init('Noticia');
        $noticias = $gg->find('list');
        foreach ($noticias as $k => $v) {
            $gg->id = $k;
            $gg->save(['id' => $k, 'noticia' => $this->cleanHTML($gg->field('noticia')),
                'created' => $gg->field('created'),
                'modified' => $gg->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = $this->Amenity->find('list');
        foreach ($gg as $k => $v) {
            $this->Amenity->id = $k;
            $this->Amenity->save(['id' => $k, 'reglamento' => $this->cleanHTML($this->Amenity->field('reglamento')),
                'created' => $this->Amenity->field('created'),
                'modified' => $this->Amenity->field('modified')], ['callbacks' => false]);
        }
        set_time_limit(5000);
        $gg = $this->find('list');
        foreach ($gg as $k => $v) {
            $this->id = $k;
            $this->save(['id' => $k, 'description' => $this->cleanHTML($this->field('description')), 'cartadeudores' => $this->cleanHTML($this->field('cartadeudores')),
                'cuerpoemailaviso' => $this->cleanHTML($this->field('cuerpoemailaviso')),
                'created' => $this->field('created'),
                'modified' => $this->field('modified')], ['callbacks' => false]);
        }
    }

    /*
     * Se utiliza para calcular a una determinada fecha, los saldos de caja y banco x consorcio, para que el reporte Resumen caja banco
     * no necesite calcular los saldos desde el inicio de los tiempos, sino a partir del saldo guardado mas cercano, se pueda obtener la info mas rapido
     */

    public function saldosResumenCajaBanco() {
        //$start = microtime(true);
        $clientes = $this->find('list', ['conditions' => ['Client.enabled' => true]]);

        if (!isset($_SESSION['Auth']['User']['client_id'])) {// engendro para poder correr el proceso sin loguearte al sistema, ya q las funciones q se llaman en getTotalesMovimientosResumen() usan el client_id de $_SESSION
            unset($_SESSION['Auth']);
            $_SESSION['Auth']['User']['client_id'] = 0;
            $_SESSION['Auth']['User']['id'] = 0;
            $_SESSION['Auth']['User']['Client'] = [];
            $_SESSION['Auth']['Client']['id'] = 0;
            $_SESSION['Auth']['User']['username'] = '';
            $_SESSION['Auth']['User']['Client']['identificador_cliente'] = '';
        }
        $client_id = $_SESSION['Auth']['User']['client_id'];
        $this->Saldoscajabanco->unbindModel(['hasMany' => ['Audit']]);
        $last = $this->Saldoscajabanco->find('list', ['order' => 'fecha desc', 'fields' => ['id', 'fecha'], 'limit' => 1]);
        if (!empty($last)) {
            $fecha = date("Y-m-d", strtotime(reset($last) . " +1 day"));
        } else {
            $fecha = '2016-06-01'; //para prueba, despues hacerlo desde el 2016-06-01. El cliente 81 (amui) tiene el primer movimiento de caja/banco (el 2/6)
            // primer mov de farina, a partir del 2017-09-01
            // primer mov de rignola, a partir del 2017-12-15
            /* primer mov martinez, a partir del 2019-03-01
             * primer mov serena fazzi, a partir del 2019-03-01
             * 
             * 
             * volver a 2016
             * 
             * 
             * 
             * 
             */
        }
        if ($fecha == date("Y-m-d")) {
            die; // corri el proceso y esta tratando de generar los saldos para el dia actual, ya terminó todo entonces lo freno.
        }
        $guardoalgo = false;
        //while (strtotime($fecha) <= strtotime(date("2021-01-25"))) {//2019-06-12   2018-08-17
        $hasta = $fecha . " 23:59:59";
        foreach ($clientes as $k => $v) {
            //if (!in_array($k, [14])) {//14 farina, 38 vitale, 115 martinez, 104 rignola, 121 fazzi, 131 serena, 135 degano, 21 guillone
            //    continue;
            //}
            $consorcios = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $k]]);
            $_SESSION['Auth']['User']['client_id'] = $k;
            foreach ($consorcios as $l => $w) {
                //if (in_array($k, [21]) && !in_array($l, [868])) {//san francisco iv farina
                //    continue;
                //}
                set_time_limit(10000);
                $saldo = $this->Saldoscajabanco->getSaldos($l, date("Y-m-d", strtotime($fecha . " -1 day")));
                $total = [];
                $total['saldocajaefectivo'] = $saldo['saldocajaefectivo'];
                $total['saldocajacheque'] = $saldo['saldocajacheque'];
                $total['saldobancoefectivo'] = $saldo['saldobancoefectivo'];
                $total['saldobancocheque'] = $saldo['saldobancocheque'];

                $resumen = $this->Caja->getTotalesMovimientosResumen($l, $fecha . " 00:00:00", $hasta, 1); //incluye los anulados!
                foreach ($resumen as $tt => $tt1) {
                    if (!in_array($tt, ['saldocajaefectivo', 'saldocajacheque', 'saldobancoefectivo', 'saldobancocheque'])) {
                        $total[$tt] = $tt1;
                    }
                }

                $total['egresospagosacuenta'] = array_sum($resumen['egresospagosacuenta']);
                $total['ingresosmanuales'] = array_sum($resumen['ingresosmanuales']);
                $total['egresosmanuales'] = array_sum($resumen['egresosmanuales']);
                $total['saldocajaefectivo'] += $resumen['ingresosefectivo'] + $resumen['ingresosmanuales']['e'] + $resumen['ingresosextracciones'] - $resumen['egresospagosproveedorefectivo'] - $resumen['egresosmanuales']['e'] - $resumen['bancosdepositosefectivo'];
                //$total['saldocajaefectivo'] -= (isset($resumen['egresospagosacuenta']['e']) ? $resumen['egresospagosacuenta']['e'] : 0); //egresos pago a cuenta en efectivo
                $total['saldocajacheque'] += $resumen['ingresoscheque'] + $resumen['ingresosmanuales']['c'] - $resumen['egresosmanuales']['c'] - $resumen['egresospagosproveedorcheque'] - $resumen['bancosdepositoscheques'] /* - (isset($resumen['egresospagosacuenta']['c']) ? $resumen['egresospagosacuenta']['c'] : 0) */; // egresos pac cheque
                $total['saldobancoefectivo'] += -$resumen['egresosdebitos'] - $resumen['ingresosextracciones'] /* - (isset($resumen['egresospagosacuenta']['t']) ? $resumen['egresospagosacuenta']['t'] : 0) */;
                $total['saldobancocheque'] += $resumen['ingresostransferencias'] + $resumen['ingresostransferenciasinterbancos'] + $resumen['ingresoscreditos'] + $resumen['bancosdepositosefectivo'] + $resumen['bancosdepositoscheques'] - ($resumen['egresospagosproveedorchequepropio'] + $resumen['egresospagosproveedortransferencia'] + $resumen['egresostransferenciasinterbancos']);

                $this->Saldoscajabanco->deleteAll(['client_id' => $k, 'consorcio_id' => $l, 'fecha' => $hasta], false);
                if (!empty(array_filter($total))) {//el array filter devuelve [] si todos los elementos son cero
                    $this->Saldoscajabanco->create();
                    $this->Saldoscajabanco->save($total + ['client_id' => $k, 'consorcio_id' => $l, 'fecha' => $hasta]);
                    $guardoalgo = true;
                }
            }
        }
        if (!$guardoalgo) {
            $this->Saldoscajabanco->create(); //guardo algo vacio para q procese el proximo dia en la proxima ejecucion
            $this->Saldoscajabanco->save($total + ['client_id' => 0, 'consorcio_id' => 0, 'fecha' => $hasta]);
        }
        $fecha = date("Y-m-d", strtotime($fecha . " +1 day"));
        //}
        //echo "Tiempo: " . (microtime(true) - $start);
        $_SESSION['Auth']['User']['client_id'] = $client_id;
        $this->Saldoscajabanco->bindModel(['hasMany' => ['Audit']]);
    }

    public function actualizaImportesPagoProveedor() {
        $this->Proveedor->Proveedorspago->unbindModel(['hasMany' => ['Audit']]);
        $lista = $this->Proveedor->Proveedorspago->find('list', ['fields' => ['id', 'importe']]);
        foreach ($lista as $k => $v) {
            $facturas = $this->Proveedor->Proveedorspago->Proveedorspagosfactura->find('all', ['conditions' => ['Proveedorspagosfactura.proveedorspago_id' => $k],
                'fields' => ['Proveedorsfactura.*', 'Consorcio.name', 'Consorcio.id', 'Liquidation.periodo', 'Proveedorspagosfactura.importe', 'Bancoscuenta.id'],
                'group' => ['Proveedorsfactura.id'],
                'joins' => [['table' => 'proveedorsfacturas', 'alias' => 'Proveedorsfactura', 'type' => 'left', 'conditions' => ['Proveedorsfactura.id=Proveedorspagosfactura.proveedorsfactura_id']],
                    ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                    ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Liquidation.consorcio_id=Consorcio.id']],
                    ['table' => 'bancoscuentas', 'alias' => 'Bancoscuenta', 'type' => 'left', 'conditions' => ['Bancoscuenta.consorcio_id=Liquidation.consorcio_id']]]]);
            $total = 0;
            if (!empty($facturas)) {
                foreach ($facturas as $k1 => $v1) {
                    $total += $v1['Proveedorspagosfactura']['importe'];
                }
            }
            $options = ['conditions' => ['Proveedorspago.id' => $k], 'recursive' => 1, 'contain' => ['Proveedor', 'User', 'Chequespropio', 'Proveedorspagosacuenta', 'Proveedorspagoscheque'],
                'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Proveedor.client_id']]]
            ];
            $proveedorspago = $this->Proveedor->Proveedorspago->find('first', $options);
            if (isset($proveedorspago['Proveedorspagosacuenta'][0]['importe']) && !empty($proveedorspago['Proveedorspagosacuenta'][0]['importe'])) {
                $total += $proveedorspago['Proveedorspagosacuenta'][0]['importe'];
            }
            $pagosacuentaaplicados = $this->Proveedor->Proveedorspago->Proveedorspagosacuenta->find('all', ['conditions' => ['Proveedorspagosacuenta.proveedorspagoaplicado_id' => $k]]);
            if (!empty($pagosacuentaaplicados)) {
                foreach ($pagosacuentaaplicados as $k1 => $v1) {
                    $total -= $v1['Proveedorspagosacuenta']['importe'];
                }
            }
            $notasdecreditoaplicadas = $this->Proveedor->Proveedorspago->Proveedorspagosnc->find('all', ['conditions' => ['Proveedorspagosnc.proveedorspago_id' => $k]]);
            if (!empty($notasdecreditoaplicadas)) {
                foreach ($notasdecreditoaplicadas as $k1 => $v1) {
                    $total -= $v1['Proveedorspagosnc']['importe'];
                }
            }
            if ("$total" != "$v") {
                //echo "id=$k, importeactual=$v,nuevo=$total<br>";
                $this->Proveedor->Proveedorspago->id = $k;
                $this->Proveedor->Proveedorspago->saveField('importe', $total);
            }
        }
        $this->Proveedor->Proveedorspago->bindModel(['hasMany' => ['Audit']]);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
                'Client.cuit LIKE' => '%' . $data['buscar'] . '%',
                'Client.city LIKE' => '%' . $data['buscar'] . '%',
                'Client.address LIKE' => '%' . $data['buscar'] . '%',
                'Client.email LIKE' => '%' . $data['buscar'] . '%',
                'Client.identificador_cliente LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
