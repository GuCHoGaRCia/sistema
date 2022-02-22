<?php

App::uses('AppModel', 'Model');

class Proveedorsfacturasadjunto extends AppModel {

    public $validate = [
        'proveedorsfactura_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'adjunto_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
    ];
    public $belongsTo = array(
        'Proveedorsfactura' => array(
            'className' => 'Proveedorsfactura',
            'foreignKey' => 'proveedorsfactura_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function guardar($data) {
        // guardo los adjuntos, si tiene
        $archivos = $this->Proveedorsfactura->Liquidation->Adjunto->transformar($data->params['form']);
        $titulos = $data->data['Adjunto'];
        $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ($archivos['name'] as $k => $v) {
            $ext = pathinfo($v, PATHINFO_EXTENSION);
            if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->Proveedorsfactura->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                // si es una extension permitida, lo subo!
                $fileName = basename(date("YmdHis") . rand(10000, 99999) . preg_replace("/[^a-zA-Z0-9]/", "", substr($titulos[$k]['titulo'], 0, 20)) . "." . $ext);
                if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {// no lo pudo mover, sigo con el q sigue                    
                    continue;
                }
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->Proveedorsfactura->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                }
                $this->create();
                if (!$this->save(['proveedorsfactura_id' => $data->data['Proveedorsfactura']['id'], 'titulo' => $titulos[$k]['titulo'], 'ruta' => $fileName,
                            'url' => $this->Proveedorsfactura->Liquidation->Consorcio->Client->Aviso->_encryptURL($fileName)])) {
                    return false;
                }
            }
        }
        return true;
    }

    public function delAdjunto($url) {
        $resul = $this->find('first', ['conditions' => ['Proveedorsfacturasadjunto.url' => $url]]);
        if (!empty($resul)) {
            $this->id = $resul['Proveedorsfacturasadjunto']['id'];
            $this->delete(); //el dato de la base de datos
        }
        return true;
    }

    public function beforeDelete($cascade = true) {
        if (is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . $this->field('ruta'))) {
            $file = new File(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . $this->field('ruta'));
            $file->delete();
        }
        return true;
    }

}
