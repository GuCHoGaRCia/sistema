<?php

App::uses('AppModel', 'Model');

class Adjunto extends AppModel {

    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'titulo' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'ruta' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'imprimir' => array(
            'notBlank' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'poneronline' => array(
            'notBlank' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $belongsTo = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Adjunto.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0,
                            'joins' => [['table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => ['c2.id=Liquidation.consorcio_id']]]]));
    }

    public function guardar($data) {
        if (!isset($data->params['form'])) {
            return false;
        }
        $archivos = $this->transformar($data->params['form']);
        $titulos = $data->data['Adjunto'];

        $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        foreach ($archivos['name'] as $k => $v) {
            $ext = pathinfo($v, PATHINFO_EXTENSION);
            if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->checkMimeType($archivos['tmp_name'][$k])) {
                // si es una extension permitida, lo subo!
                $fileName = basename(date("YmdHis") . rand(10000, 99999) . preg_replace("/[^a-zA-Z0-9]/", "", substr($titulos[$k]['titulo'], 0, 20)) . "." . $ext);
                if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {// no lo pudo mover, sigo con el q sigue                    
                    continue;
                }
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $this->comprimirImagen($dir, $fileName);
                }
                $this->create();
                $this->save(array('liquidation_id' => $data->data['Adjunto']['liquidation_id'], 'titulo' => $titulos[$k]['titulo'], 'ruta' => $fileName,
                    'url' => $this->Liquidation->Consorcio->Client->Aviso->_encryptURL($fileName), 'imprimir' => $data->data['Adjunto']['imprimir'], 'poneronline' => $data->data['Adjunto']['poneronline'], 'online' => $data->data['Adjunto']['poneronline']));
            }
        }
        return true;
    }

    /*
     * le pongo extension que corresponda al mime y limpio el nombre de los archivos enviados
     */

    public function transformar($data) {
        $archivostxt = ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []];
        if (!is_array($data)) {
            return $archivostxt;
        }
        $mimes = ['application/pdf' => '.pdf', // .pdf
            'application/msword' => '.doc', // .doc
            'image/jpeg' => '.jpg', //.jpeg .jpg o .jpe
            'image/jpg' => '.jpg', //.jpg 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx', // .docx
            'application/vnd.ms-excel' => '.xls', // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx', // .xlsx
            'image/png' => '.png', // png
            'application/zip' => '.zip', // zip
            'application/x-rar-compressed' => '.rar', // rar
                //'image/heif' => '.heif', //https://es.wikipedia.org/wiki/HEIF
                //'image/heic' => '.heic'
        ];
        foreach ($data as $k => $v) {
            $mime = $this->getMime($v['tmp_name']);
            $m = isset($mimes[$mime]) ? $mimes[$mime] : '.xxx';
            $filename = pathinfo($v['name'], PATHINFO_FILENAME);
            $archivostxt['name'][] = preg_replace("/[^a-zA-Z0-9]/", "", substr($filename, 0, 100)) . $m;
            $archivostxt['type'][] = $v['type'];
            $archivostxt['tmp_name'][] = $v['tmp_name'];
            $archivostxt['error'][] = $v['error'];
            $archivostxt['size'][] = $v['size'];
        }
        return $archivostxt;
    }

    /*
     * Chequea q el archivo adjunto se corresponda con alguno de los MIMETYPES permitidos 
     */

    public function checkMimeType($file) {
        $mimes = ['application/pdf', // .pdf
            'application/msword', // .doc
            'image/jpeg', //.jpeg o .jpe
            'image/jpg' => '.jpg', //.jpg 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-excel', // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'image/png', // png
            'application/zip', // zip
            'application/x-rar-compressed', // rar
                //'image/heif' => '.heif', //https://es.wikipedia.org/wiki/HEIF
                //'image/heic' => '.heic'
        ];

        $esvalido = (bool) in_array($this->getMime($file), $mimes);
        //if (!$esvalido) {
        // que hago? me envio un mail avisando
        //$this->Liquidation->Consorcio->Client->Email->enviarEmail(['email' => 'it@ceonline.com.ar', 'asunto' => 'MimeType dangerous?', 'html' => strip_tags('El mime enviado fue: ' . $mime . '\n' . print_r($this->data))]);
        //}
        return $esvalido;
    }

    /*
     * Obtiene el MimeType del archivo $file
     */

    public function getMime($file) {
        if (empty($file) || !is_string($file)) {
            return ''; // al chequear, '' no va a ser un mime válido
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $file);
    }

    public function comprimirImagen($updir, $name) {
        if (!is_file("$updir" . DS . "$name")) {
            return;
        }
        $arr_image_details = getimagesize("$updir" . DS . "$name"); // pass id to thumb name
        if ($arr_image_details[2] == 2) {
            $imgcreatefrom = "imagecreatefromjpeg";
        }
        if ($arr_image_details[2] == 3) {
            $imgcreatefrom = "imagecreatefrompng";
        }
        // los PNG (original y thumb) los paso a JPG para disminuir el tamaño del archivo
        // http://php.net/manual/en/function.imagejpeg.php
        // https://github.com/danielmiessler/SecLists/blob/master/Payloads/Images/lottapixel.jpg
        $image = @$imgcreatefrom("$updir" . DS . "$name"); // para evitar errores si suben lottapixel por ejemplo (imagen corrupta q intenta alocar muchos gigas de memoria y rompe todo)
        if (!$image) {
            return;
        }
        imagejpeg($image, "$updir" . DS . "$name", 75); // original optimizado
        imagedestroy($image);
    }

    /*
     * Borro el adjunto al borrar el registro, si existe
     */

    public function beforeDelete($cascade = true) {
        if (is_file(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . $this->field('ruta'))) {
            $file = new File(APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . $this->field('ruta'));
            $file->delete();
        }
        return true;
    }

    public function delAdjunto($url, $client_id = null) {
        $resul = $this->find('first', ['conditions' => ['Adjunto.url' => $url]]);
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
        return array(
            'OR' => array(
                'Adjunto.titulo LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%'
        ));
    }

}
