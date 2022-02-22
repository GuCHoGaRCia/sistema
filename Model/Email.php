<?php

App::uses('AppModel', 'Model');

class Email extends AppModel {

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
        'email' => [
            'maildir' => [
                'rule' => ['checkEmails'],
                'message' => 'El formato del email es incorrecto. Ej: juan@gmail.com. Si desea agregar mas de un email, separelos con coma y sin espacios. Ej: juan@gmail.com,pepe@hotmail.com',
            //'allowEmpty' => false,
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
        'html' => [
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
        ]
    ];

    /*
     * Si no es admin le establezco el client_id por el del cliente
     * Si es admin elije el cliente, por eso no se necesitaría
     */

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['Email']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        return true;
    }

    /*
     * Envia los emails en add y edit (esta comentado el if($created)
     */

    public function afterSave($created, $options = array()) {
        //if ($created) {
        $this->enviarEmail($this->data['Email']);
        //}
    }

    public function guardar() {
        $consorcios = $data['Email']['consorcio_id'];
        unset($data['Email']);
        foreach ($consorcios as $a => $b) {
            foreach ($data as $k => $v) {
                if (substr($k, strpos($k, '_') + 1, strrpos($k, '_') - 2) !== "$a") {// obtengo la XXX en 't_XXX_0' (el index del consorcio)
                    continue; // no es un propietario de este consorcio (busca en todo $data)
                }
                $email = $this->Client->Consorcio->Propietario->getPropietarioEmail($this->Client->Consorcio->Propietario->getPropietarioId($b, $v));
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
                    $html = utf8_encode();
                    $text = utf8_encode();
                    $this->enviaMail($datoscliente, '', $html, $text, $email);
                }
            }
        }
        return true;
    }

    public function enviaMail($datoscliente, $asunto, $html, $text, $email) {

        return true;
    }

    /*
     * envia un email clásico:
     * $data = ['unoomasemailsseparadosporcoma@gmail.com','asunto','html'];
     */

    public function enviarEmail($data) {
        $emails = explode(',', $data['email']);
        $to = [];
        foreach ($emails as $k => $v) {
            $to[] = $v;
        }
        $to[] = 'it@ceonline.com.ar'; // me lo envio a mi tambien
        $json_string = ['to' => $to];
        $params = [
            'api_user' => base64_decode('dGhlNGhvcnNlbWVu'), 'api_key' => base64_decode('MjNFNzEyZDgh'), 'to' => 'it@ceonline.com.ar', 'x-smtpapi' => json_encode($json_string),
            'from' => 'no-responder-este-mail@ceonline.com.ar', 'subject' => $data['asunto'], 'html' => utf8_encode($data['html']), 'text' => utf8_encode(strip_tags($data['html']))
        ];

        $session = curl_init(base64_decode('aHR0cHM6Ly9hcGkuc2VuZGdyaWQuY29tL2FwaS9tYWlsLnNlbmQuanNvbg=='));
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1_2');
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($session);
        curl_close($session);
    }


    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }

        return array(
            'OR' => array(
                'Email.email LIKE' => '%' . $data['buscar'] . '%',
                'Email.asunto LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
