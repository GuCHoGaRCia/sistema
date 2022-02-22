<?php

App::uses('AppModel', 'Model');

class Reparacionesactualizacionesadjunto extends AppModel {

    public $validate = [
        'reparacionesactualizacione_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'titulo' => [
            'notBlank' => [
                'rule' => ['notBlank'],
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
        'Reparacionesactualizacione' => [
            'className' => 'Reparacionesactualizacione',
            'foreignKey' => 'reparacionesactualizacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /* public function beforeDelete($cascade = true) {
      //borro el adjunto
      if (file_exists(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'rep' . DS . $this->field('ruta'))) {
      $file = new File(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'rep' . DS . $this->field('ruta'));
      $file->delete();
      }
      return true;
      } */

    /*
     * $userid se utiliza cuando un supervisor necesita actualizar/editar una reparacion desde su Panel de Supervisor (no está logueado). Si esta logueado, se usa $_SESSION
     */

    public function guardarAdjunto($id, $data, $client_id = null) {
        $archivos = $this->Reparacionesactualizacione->Reparacione->Consorcio->Liquidation->Adjunto->transformar($data->params['form']);
        //$archivos = $data->params['form']['archivostxt'];
        $titulos = $data->data['Adjunto'];
        $client_id = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $client_id;
        $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $client_id . DS . 'rep';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        foreach ($archivos['name'] as $k => $v) {
            $ext = strtolower(substr($v, strrpos($v, ".") + 1, strlen($v)));
            if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->Reparacionesactualizacione->Reparacione->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                // si es una extension permitida, lo subo
                $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($titulos[$k]['titulo'], 0, 20)) . "." . $ext);
                if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {
                    // no lo pudo mover, sigo con el q sigue
                    continue;
                }
                if (in_array($ext, array('jpg', 'jpeg', 'png'))) {
                    $this->Reparacionesactualizacione->Reparacione->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                }

                $this->create();
                $this->save(['reparacionesactualizacione_id' => $id, 'titulo' => $titulos[$k]['titulo'], 'ruta' => $fileName,
                    'url' => $this->Reparacionesactualizacione->Reparacione->Consorcio->Client->Aviso->_encryptURL($fileName)]);
            }
        }
    }

    /*
     * Borro adjunto de reparaciones y su archivo fisico
     */

    public function delAdjunto($id, $client_id = null) {
        $client_id = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $this->Reparacionesactualizacione->Reparacione->Consorcio->Client->Aviso->_decryptURL($client_id);
        $resul = $this->find('first', ['conditions' => ['Consorcio.client_id' => $client_id, 'Reparacionesactualizacionesadjunto.id' => $id],
            'joins' => [['table' => 'reparacionesactualizaciones', 'alias' => 'Reparacionesactualizacione', 'type' => 'left', 'conditions' => ['Reparacionesactualizacionesadjunto.reparacionesactualizacione_id=Reparacionesactualizacione.id']],
                ['table' => 'reparaciones', 'alias' => 'Reparacione', 'type' => 'left', 'conditions' => ['Reparacionesactualizacione.reparacione_id=Reparacione.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Reparacione.consorcio_id=Consorcio.id']]],
            'fields' => ['Reparacionesactualizacionesadjunto.id']]);
        if (!empty($resul)) {
            $this->id = $resul['Reparacionesactualizacionesadjunto']['id'];
            if (file_exists(APP . WEBROOT_DIR . DS . 'files' . DS . $client_id . DS . 'rep' . DS . $this->field('ruta'))) {// el archivo fisico
                $file = new File(APP . WEBROOT_DIR . DS . 'files' . DS . $client_id . DS . 'rep' . DS . $this->field('ruta'));
                $file->delete();
            }
            $this->delete(); //el dato de la base de datos
        }
        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Reparacionesactualizacionesadjunto.titulo LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
