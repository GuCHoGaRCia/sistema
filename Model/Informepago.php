<?php

App::uses('AppModel', 'Model');

class Informepago extends AppModel {

    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'fecha' => [
            'date' => [
                'rule' => ['date'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'importe' => [
            'decimal' => [
                'rule' => ['decimal'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'banco' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'observaciones' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'formasdepago_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Formasdepago' => [
            'className' => 'Formasdepago',
            'foreignKey' => 'formasdepago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Banco' => [
            'className' => 'Banco',
            'foreignKey' => 'banco_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasMany = [
        'Informepagosadjunto' => [
            'className' => 'Informepagosadjunto',
            'foreignKey' => 'informepago_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ]
    ];

    /*
     * Obtiene las consultas del propietario actual (puede tener consultas en varios clientes)
     */

    public function getInformePago($p, $cl = null) {
        $consor = $this->Propietario->getPropietarioConsorcio($p); //chequeo q el prop sea del cliente informado
        if (!$this->Propietario->Consorcio->getConsorcioClientId($consor)) {
            return false;
        } else {
            $resul = $this->find('all', ['conditions' => ['Informepago.client_id' => $cl, 'Informepago.propietario_id' => $p],
                'joins' => [/* ['table' => 'informepagosadjuntos', 'alias' => 'Informepagosadjunto', 'type' => 'left', 'conditions' => ['Informepagosadjunto.informepago_id=Informepago.id']], */
                    ['table' => 'bancos', 'alias' => 'Banco', 'type' => 'left', 'conditions' => ['Informepago.banco_id=Banco.id']],
                    ['table' => 'formasdepagos', 'alias' => 'Formasdepago', 'type' => 'left', 'conditions' => ['Informepago.client_id=Formasdepago.id']]],
                'contain' => ['Informepagosadjunto'],
                'fields' => ['distinct (Informepago.id)', "DATE_FORMAT(Informepago.fecha,'%d/%m/%Y') as f", 'Informepago.importe as i', 'Banco.name as b', 'Informepago.observaciones as o',
                    'Formasdepago.forma', 'Informepago.verificado as v', 'Informepago.rechazado as r', 'Informepago.motivorechazo as m'],
                'order' => 'Informepago.created']);
            return $resul;
        }
    }

    /*
     * $data: fecha, importe, etc.... $adjuntos: los comprobantes adjuntos
     * f: fecha, i:importe, b:banco, fp: formas de pago, o: observaciones, p: propietarioid, cl: client_id, adj: adjunto/s
     */

    public function setInformePago($data, $adjuntos = null) {// desde el panel del administrador, seteo $l para saber q es respuesta
        if (!isset($data['link']) || !isset($data['i']) || !isset($data['f']) || !isset($data['b']) || !isset($data['fp']) || !isset($data['o']) || !isset($data['cl']) || !isset($data['p'])) {
            return false;
        }
        $p = (int) filter_var($data['p'], FILTER_SANITIZE_NUMBER_INT);
        $emailLink = $this->Client->Aviso->_decryptUrl($data['link']);
        $emailsProp = explode(',', $this->Propietario->getPropietarioEmail($p));
        $existe = false;
        foreach (explode(',', $emailLink) as $e) {
            if (in_array($e, $emailsProp)) {// el email del Propietario cifrado en "link" no es del Propietario $data['p']
                $existe = true;
            }
        }

        if (!$existe) {
            return false;
        }
        $importe = (float) $data['i'];
        $fecha = substr($data['f'], 6, 4) . "-" . substr($data['f'], 3, 2) . "-" . substr($data['f'], 0, 2);
        $banco = filter_var($data['b'], FILTER_SANITIZE_STRING);
        $formadepago = (int) filter_var($data['fp'], FILTER_SANITIZE_NUMBER_INT); // 1, 2 o 3 (combo transferencia,deposito, cajeroautomatico
        $observaciones = filter_var($data['o'], FILTER_SANITIZE_STRING);
        $cl = (int) filter_var($data['cl'], FILTER_SANITIZE_NUMBER_INT);

        if (!is_float($importe) || !DateTime::createFromFormat('d/m/Y', $data['f']) || !is_int($formadepago) || !is_int($p) || !is_int($cl) || empty($banco)) {
            return false;
        }
        $consor = $this->Propietario->getPropietarioConsorcio($p); //chequeo q el prop sea del cliente informado

        if ($this->Propietario->Consorcio->getConsorcioClientId($consor) != $cl) {
            return false;
        }

        $this->create();
        $resul = $this->save(['client_id' => $cl, 'propietario_id' => $p, 'formasdepago_id' => $formadepago, 'importe' => $importe, 'fecha' => $fecha, 'banco_id' => $banco, 'observaciones' => $observaciones]);
        if ($resul) {
            // guardo los adjuntos asociados
            $this->_setArchivo($resul['Informepago']['id'], $adjuntos, $cl);
        } else {
            return false;
        }
        return true;
    }

    /*
     * En Cobranzas->Informe pagos propietarios, rechazo un pago informado debido al motivo 'm' y le aviso al Propietario
     */

    public function rechazar($id, $motivo = "") {
        $idinforme = $this->find('first', ['conditions', ['Informepago.id' => $id, 'Informepago.client_id' => $_SESSION['Auth']['User']['client_id']]]);
        if (!empty($idinforme)) {
            $this->id = $id;
            $this->save(['motivorechazo' => strip_tags($motivo), 'rechazado' => 1]);
            $email = $this->Propietario->getPropietarioEmail($this->field('propietario_id'));
            $listaemails = explode(',', $email);
            if (count($listaemails) > 0) {
                foreach ($listaemails as $j) {
                    if (filter_var($j, FILTER_VALIDATE_EMAIL)) { // verifico q sea un mail valido
                        $this->Client->Avisosqueue->create();
                        $this->Client->Avisosqueue->save(['client_id' => $_SESSION['Auth']['User']['client_id'], 'emailfrom' => explode(',', $_SESSION['Auth']['User']['Client']['email'])[0],
                            'razonsocial' => $_SESSION['Auth']['User']['Client']['name'], 'asunto' => 'Informe Pago Rechazado',
                            'altbody' => "Le informamos que el pago informado el dia " . date("d/m/Y", strtotime($this->field('created'))) . " por un importe igual a $" . CakeNumber::currency(h($this->field('importe')), null, ['negative' => '-', 'before' => false, 'fractionSymbol' => false]) . " fue rechazado debido al siguiente motivo: " . h($motivo),
                            'codigohtml' => "Le informamos que el pago informado el dia " . date("d/m/Y", strtotime($this->field('created'))) . " por un importe igual a $" . CakeNumber::currency(h($this->field('importe')), null, ['negative' => '-', 'before' => false, 'fractionSymbol' => false]) . " fue rechazado debido al siguiente motivo: " . h($motivo),
                            'mailto' => $j]);
                    }
                }
            }
            return true;
        }
        return false;
    }

    /*
     * En Cobranzas->Informe pagos propietarios, cancelo el rechazo de un pago informado
     */

    public function undorechazar($id) {
        $idinforme = $this->find('first', ['conditions', ['Informepago.id' => $id, 'Informepago.client_id' => $_SESSION['Auth']['User']['client_id']]]);
        if (!empty($idinforme)) {
            $this->id = $id;
            $this->save(['motivorechazo' => '', 'rechazado' => 0]);
            return true;
        }
        return false;
    }

    private function _setArchivo($id, $data, $cli = null) {
        if (!empty($data) && is_array($data) && count($data) > 0) {
            $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $cli . DS . 'consultas';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $archivos = $this->Client->Consorcio->Liquidation->Adjunto->transformar($data);
            foreach ($archivos['name'] as $k => $v) {
                $path_parts = pathinfo($v);
                $ext = $path_parts['extension'];
                if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png']) && $this->Client->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                    // si es una extension permitida, lo subo
                    $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($path_parts['filename'], 0, 20)) . "." . $ext);
                    if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {
                        // no lo pudo mover, sigo con el q sigue
                        continue;
                    }
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $this->Client->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                    }

                    $this->Informepagosadjunto->create();
                    $this->Informepagosadjunto->save(['informepago_id' => $id, 'ruta' => $fileName, 'url' => $this->Client->Aviso->_encryptURL($fileName)]);
                }
            }
        }

    }

    public function getArchivos($p, $cli = null) {
        return $this->find('all', ['conditions' => ['Consultaspropietariosadjunto.client_id' => $cli, 'Consultaspropietariosadjunto.propietario_id' => $p], 'fields' => ['Consultaspropietariosadjunto.ruta as r', "DATE_FORMAT(Consultaspropietariosadjunto.created,'%d/%m/%Y %T') as f", 'Consultaspropietariosadjunto.id as id'], 'order' => 'created desc']);
    }

}
