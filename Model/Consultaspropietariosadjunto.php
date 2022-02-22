<?php

App::uses('AppModel', 'Model');

class Consultaspropietariosadjunto extends AppModel {

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
        'ruta' => [
            'notBlank' => [
                'rule' => ['notBlank'],
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
        ]
    ];

    public function setArchivo($data, $cliente = null, $p = 0) {
        if (!empty($p)) {
            // es un adjunto de propietario, busco su client_id
            $cliente = $this->Client->Consorcio->getConsorcioClientId($this->Propietario->getPropietarioConsorcio($p));
        }
        $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $archivos = $this->Client->Consorcio->Liquidation->Adjunto->transformar($data);
        foreach ($archivos['name'] as $k => $v) {
            $path_parts = pathinfo($v);
            $ext = $path_parts['extension'];
            if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png']) && $this->Client->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                // si es una extension permitida, lo subo
                $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($path_parts['filename'], 0, 20)) . "." . $ext); //20 chars + nombre
                if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {
                    // no lo pudo mover, sigo con el q sigue
                    continue;
                }
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->Client->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                }
                $this->create();
                $this->save(['client_id' => $cliente, 'propietario_id' => $p, 'ruta' => $fileName, 'url' => $this->Client->Aviso->_encryptURL($fileName)]);
            }
        }

        return $this->getArchivos($p, $cliente);
    }

    public function getArchivos($p, $cli = null) {
        $resul = $this->find('all', array('conditions' => array('Consultaspropietariosadjunto.client_id' => $cli,
                'Consultaspropietariosadjunto.propietario_id' => $p),
            'fields' => array('substring(Consultaspropietariosadjunto.ruta,20) as r', 'Consultaspropietariosadjunto.url as l', "DATE_FORMAT(Consultaspropietariosadjunto.created,'%d/%m/%Y %T') as f", 'Consultaspropietariosadjunto.id as id'), 'order' => 'created desc'));
        return $resul;
    }

    /*
     * Borro adjuntos de las consultas de los propietarios
     */

    public function delAdjunto($id, $cli) {
        $result = $this->find('first', array('conditions' => array('Consultaspropietariosadjunto.client_id' => $cli, 'Consultaspropietariosadjunto.id' => $id), 'fields' => 'Consultaspropietariosadjunto.id'));
        if (!empty($result)) {
            $this->id = $id;
            return $this->delete();
        }
        return false;
    }

    public function beforeDelete($cascade = true) {
        //borro el adjunto
        if (file_exists(APP . WEBROOT_DIR . DS . 'files' . DS . $this->field('client_id') . DS . 'consultas' . DS . $this->field('ruta'))) {
            $file = new File(APP . WEBROOT_DIR . DS . 'files' . DS . $this->field('client_id') . DS . 'consultas' . DS . $this->field('ruta'));
            $file->delete();
        }
        return true;
    }

}
