<?php

App::uses('AppModel', 'Model');

class Comunicacione extends AppModel {

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
        'asunto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'mensaje' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
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
        ]
    ];
    public $hasMany = [
        'Comunicacionesdetalle' => [
            'className' => 'Comunicacionesdetalle',
            'foreignKey' => 'comunicacione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Comunicacionesadjunto' => [
            'className' => 'Comunicacionesadjunto',
            'foreignKey' => 'comunicacione_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', array('conditions' => array('Comunicacione.client_id' => $_SESSION['Auth']['User']['client_id'], 'Comunicacione.id' => $id), 'fields' => [$this->alias . '.id'])));
    }

    public function guardar($data) {
        $listaconsorcios = [];
        if (!isset($data->data['prop']) || empty($data->data['prop'])) {
            return 'Debe seleccionar Propietarios';
        }
        foreach ($data->data['prop'] as $k => $v) {
            $consor = substr($k, strpos($k, '_') + 1, strrpos($k, '_') - 2);
            if (!$this->Client->Consorcio->canEdit($consor)) {
                return 'El dato es inexistente';
            }
            $listaconsorcios[] = $consor;
        }
        $files = []; //los nombres de los archivos guardados internamente
        if (isset($data->params['form'])) {
            $archivos = $this->Client->Consorcio->Liquidation->Adjunto->transformar($data->params['form']);
            $titulos = $data->data['Adjunto'] ?? [];
            // guardo los archivos adjuntos
            $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'e';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            foreach ($archivos['name'] as $k => $v) {
                $ext = pathinfo($v, PATHINFO_EXTENSION);
                if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->Client->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                    // si es una extension permitida, lo subo
                    $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($titulos[$k]['titulo'] ?? '', 0, 20)) . "." . $ext);
                    $files[$k] = $fileName;
                    if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {
                        // no lo pudo mover, sigo con el q sigue
                        continue;
                    }
                    chmod($dir . DS . $fileName, 0775);
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $this->Client->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                    }
                }
            }
        }

        foreach (array_unique($listaconsorcios) as $xx) {// para cada consorcio guardo la comunicacion con su adjunto
            $this->create();
            $resul = $this->save(['client_id' => $_SESSION['Auth']['User']['client_id'], 'asunto' => $data->data['Comunicacione']['asunto'], 'mensaje' => $data->data['Comunicacione']['mensaje'], 'html' => '', 'enviada' => 0]);
            $id = $resul['Comunicacione']['id'];
            if (count($files) > 0 && count($titulos) > 0) {
                foreach ($titulos as $k => $v) {
                    $this->Comunicacionesadjunto->create();
                    $this->Comunicacionesadjunto->save(['comunicacione_id' => $id, 'titulo' => $v['titulo'], 'ruta' => 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'e' . DS . $files[$k], 'url' => $this->Client->Aviso->_encryptURL('files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'e' . DS . $files[$k])]);
                }
            }

            foreach ($data->data['prop'] as $k => $v) {
                $consor = substr($k, strpos($k, '_') + 1, strrpos($k, '_') - 2);
                if ($consor === $xx) {
                    $pid = $this->Client->Consorcio->Propietario->getPropietarioId($consor, $v);
                    $this->Comunicacionesdetalle->create();
                    $this->Comunicacionesdetalle->save(['comunicacione_id' => $id, 'consorcio_id' => $consor, 'propietario_id' => $pid]);
                }
            }
        }
        return '';
    }

    public function beforeSave($options = []) {
        if (isset($this->data['Comunicacione']['mensaje'])) {
            $this->data['Comunicacione']['mensaje'] = $this->cleanHTML($this->data['Comunicacione']['mensaje']);
        }
        return true;
    }

    /*
     * Agrego a la cola de envios el newsletter seleccionado (para todos los consorcios y sus propietarios asociados)
     */

    public function encolar($id, $html) {
        $datos = $this->find('first', ['conditions' => ['Comunicacione.id' => $id], 'contain' => ['Comunicacionesdetalle'],
            'joins' => [['table' => 'comunicacionesdetalles', 'alias' => 'Comunicacionesdetalle', 'type' => 'left', 'conditions' => ['Comunicacionesdetalle.comunicacione_id=Comunicacione.id']]]]);

        if (empty($datos['Comunicacionesdetalle'])) {
            return false;
        }
        // guardo el html para los q no leen html en su mail
        $file = $this->generateRandomString(50);
        $fh = fopen(APP . WEBROOT_DIR . DS . 'emails' . DS . $file . ".html", "w");
        fwrite($fh, $html);
        fclose($fh);

        $this->save(['id' => $id, 'html' => $file . ".html"]); // para q al borrar la comunicación se borre el html tambien
        foreach ($datos['Comunicacionesdetalle'] as $k => $v) {
            $email = $this->Client->Consorcio->Propietario->getPropietarioEmail($v['propietario_id']);
            $consorcio = $this->Client->Consorcio->getConsorcioName($v['consorcio_id']);
            $listaemails = explode(',', $email);
            if (count($listaemails) > 0) {
                foreach ($listaemails as $j) {
                    if (filter_var($j, FILTER_VALIDATE_EMAIL)) { // verifico q sea un mail valido
                        $this->Client->Avisosqueue->create();
                        $this->Client->Avisosqueue->save(['client_id' => $_SESSION['Auth']['User']['client_id'], 'emailfrom' => empty($_SESSION['Auth']['User']['Client']['email']) ? 'no-responder@ceonline.com.ar' : explode(',', $_SESSION['Auth']['User']['Client']['email'])[0],
                            'razonsocial' => $_SESSION['Auth']['User']['Client']['name'], 'asunto' => $consorcio . " - " . $datos['Comunicacione']['asunto'],
                            'altbody' => "Si no puede ver este mensaje, copie y pegue en su navegador esta direccion: https://ceonline.com.ar/p/?emails/$file.html",
                            'codigohtml' => $html, 'mailto' => $j]);
                    }
                }
            }
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
                'Comunicacione.client_id LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
