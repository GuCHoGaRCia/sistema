<?php

App::uses('AppModel', 'Model');

class Report extends AppModel {

    public $validate = array(
        'nombre' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            //'message' => 'Your custom message here',
            ),
        ),
        'funcion' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            //'message' => 'Your custom message here',
            ),
        ),
        'enabled' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            //'message' => 'Your custom message here',
            ),
        ),
    );
    public $hasMany = array(
        'Reportsclient' => array(
            'className' => 'Reportsclient',
            'foreignKey' => 'report_id',
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
        'Colaimpresione' => array(
            'className' => 'Colaimpresione',
            'foreignKey' => 'report_id',
            'dependent' => false,
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

    /*
     * Chequea que el cliente al q se busca acceder a un reporte, sea el correspondiente a la liquidacion asociada
     */

    public function checkClient($liquidation_id, $client_id) {
        if (empty($client_id) || empty($liquidation_id)) {
            return false;
        }
        $consorcio_id = $this->Colaimpresione->Client->Consorcio->Liquidation->getConsorcioId($liquidation_id);
        $id = $this->Colaimpresione->Client->Consorcio->getConsorcioClientId($consorcio_id);
        return (bool) ($client_id === $id);
    }

    public function checkClient2($consorcio_id, $client_id) {
        if (empty($client_id) || empty($consorcio_id)) {
            return false;
        }
        $id = $this->Colaimpresione->Client->Consorcio->getConsorcioClientId($consorcio_id);
        return (bool) ($client_id === $id);
    }

    /*
     * Verifica q el propietario asociado al email en el link, pertenezca al consorcio asociado a liquidation_id
     * verifico que el propietario_id del link sea el asociado al mail
     * Se utiliza en el Panel propietario
     */

    public function checkClient3($liquidation_id, $propietario_id, $link) {
        $consorcio_id = $this->Colaimpresione->Client->Consorcio->Liquidation->getConsorcioId($liquidation_id);
        $email = $this->Colaimpresione->Client->Aviso->_decryptURL($link);

        $emails = explode(',', $email);
        if (empty($emails)) {
            return false;
        }
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                return false;
            }
        }

        $idPropietariosyConsorcios = [];
        foreach ($emails as $e) {
            $idp = $this->Colaimpresione->Client->Consorcio->Propietario->getPropietarioIdFromEmail($e, 'all');
            $idPropietariosyConsorcios += !empty($idp) ? $idp : []; // obtengo los id de todos los propietarios con ese email [propid,consorcio_id]. Si no existe devuelve cero
        }
        if (empty($idPropietariosyConsorcios) || empty($consorcio_id)) {
            return []; // el propietario no se encuentra online
        }
        //$idPropietariosyConsorcios
        //  array(
        //	(int) 2555 => '65'
        //  )
        //$consorcio_id 65
        return (bool) (in_array($consorcio_id, array_values($idPropietariosyConsorcios)) && in_array($propietario_id, array_keys($idPropietariosyConsorcios)));
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                $this->alias . '.name LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.funcion LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
