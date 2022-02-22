<?php

App::uses('AppModel', 'Model');

class Llamadosadjunto extends AppModel {

    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            ),
        ),
        'nombre' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            //'message' => 'Your custom message here',
            ),
        ),
        'ruta' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            //'message' => 'Your custom message here',
            ),
        ),
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function setArchivo($data, $cli = null) {
        ini_set('max_execution_time', '120');
        if (!isset($_SESSION['Auth']['User']['client_id']) && empty($cli)) {
            return [];
        }
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Client->getClientIdFromMultipleEmails($this->Client->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
        $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $cliente . DS . 'consultas';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        //array(
        //    'name' => array(
        //            (int) 0 => 'file-0.jpg',
        //            (int) 1 => 'file-1.jpg'
        //    ),
        //    'type' => array(
        //            (int) 0 => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        //            (int) 1 => 'image/jpeg'
        //    ),
        $archivos = $this->Client->Consorcio->Liquidation->Adjunto->transformar($data);
        foreach ($archivos['name'] as $k => $v) {
            $path_parts = pathinfo($v);
            $ext = $path_parts['extension'];
            if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->Client->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                // si es una extension permitida, lo subo
                $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($path_parts['filename'], 0, 20)) . "." . $ext); //20 chars + nombre
                if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {// no lo pudo mover, sigo con el q sigue
                    continue;
                }
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->Client->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                }
                $this->create();
                $this->save(['client_id' => $cliente, 'ruta' => $fileName, 'url' => $this->Client->Aviso->_encryptURL($fileName), 'es_respuesta' => isset($_SESSION['Auth']['User']['is_admin']) ? $_SESSION['Auth']['User']['is_admin'] : 0]);
            }
        }

        return $this->getArchivos($cliente);
    }

    public function getArchivos($cli = null) {
        $resul = [];
        if (isset($_SESSION['Auth']['User']['client_id']) || !empty($cli)) {
            $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Client->getClientIdFromMultipleEmails($this->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
            $resul = $this->find('all', array('conditions' => array('Llamadosadjunto.client_id' => $cliente), 'fields' => array('substring(Llamadosadjunto.ruta,20) r', 'Llamadosadjunto.url as l', "DATE_FORMAT(Llamadosadjunto.created,'%d/%m/%Y %T') as f", 'Llamadosadjunto.id as id', 'Llamadosadjunto.es_respuesta as res',), 'order' => 'created desc'));
            if (!empty($resul)) {
                foreach ($resul as $k => $v) {
                    if (empty($v['Llamadosadjunto']['l'])) {// si no lo tiene, lo cargo
                        $resul[$k]['Llamadosadjunto']['l'] = $this->Client->Consorcio->Propietario->Aviso->_encryptURL($v['Llamadosadjunto']['r']);
                    }
                }
            }
        }
        return $resul;
    }

    /*
     * Borro adjuntos de las consultas
     */

    public function delAdjunto($id, $cli) {
        if (!isset($_SESSION['Auth']['User']['client_id']) || empty($id)) {
            return false;
        }
        $result = $this->find('first', array('conditions' => array('Llamadosadjunto.client_id' => $cli, 'Llamadosadjunto.id' => $id), 'fields' => 'Llamadosadjunto.id'));
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
