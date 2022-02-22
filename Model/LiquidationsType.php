<?php

App::uses('AppModel', 'Model');

class LiquidationsType extends AppModel {

    public $validate = array(
        'client_id' => array(
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
        'prefijo' => array(
            'numeric' => array(
                'rule' => array('naturalNumber', true),
                'message' => 'El dato debe ser mayor o igual a cero',
            ),
            'range' => array(
                'rule' => array('range', -1, 10),
                'message' => 'Debe ser un numero entre 1 y 9999',
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Ya existe un Tipo de Liquidacion con ese Prefijo',
            //'on' => 'create',
            ),
        /* 'unosolo' => array(
          'rule' => array('checkUnique2'),
          'message' => 'El campo debe ser unico para el cliente actual',
          'on' => 'update',
          ), */
        ),
        'saldoinicial' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'El campo debe ser decimal',
            ),
        ),
        'enabled' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
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
    public $hasMany = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidations_type_id',
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
        'SaldosIniciale' => array(
            'className' => 'SaldosIniciale',
            'foreignKey' => 'liquidations_type_id',
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
        'Cobranza' => array(
            'className' => 'Cobranza',
            'foreignKey' => 'liquidations_type_id',
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
        'SaldosInicialesConsorcio' => array(
            'className' => 'SaldosInicialesConsorcio',
            'foreignKey' => 'liquidations_type_id',
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
        'Consorciosconfiguration' => array(
            'className' => 'Consorciosconfiguration',
            'foreignKey' => 'liquidations_type_id',
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

    /*
     * Devuelvo un listado de los tipos de liquidaciones del cliente actual
     */

    public function getLiquidationsTypes($client = null, $all = false) {
        return $this->find('list', ['conditions' => ['LiquidationsType.client_id' => !empty($client) ? $client : $_SESSION['Auth']['User']['client_id']] + (!$all ? ['LiquidationsType.enabled' => 1] : [])]);
    }

    public function getLiquidationsTypesPrefijos($client = null) {
        return $this->find('list', ['conditions' => ['LiquidationsType.client_id' => $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.enabled' => 1], 'fields' => ['LiquidationsType.id', 'LiquidationsType.prefijo']]);
    }

    public function getLiquidationsTypesName($LiquidationTypeId) {
        return $this->find('first', ['conditions' => ['LiquidationsType.id' => $LiquidationTypeId, 'LiquidationsType.enabled' => 1], 'fields' => ['LiquidationsType.name']]);
    }

    /*
     * Devuelvo el prefijo de la liquidacion actual
     */

    public function getPrefijo($client, $lt) {
        $resul = $this->find('first', ['conditions' => ['LiquidationsType.client_id' => !empty($client) ? $client : $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.enabled' => 1, 'LiquidationsType.id' => $lt], 'fields' => ['LiquidationsType.prefijo']]);
        return empty($resul) ? 0 : $resul['LiquidationsType']['prefijo'];
    }

    /*
     * Devuelvo el id del tipo de liquidacion a partir del prefijo de la misma (se utiliza en Pagoselectronico::getCobranzas())
     */

    public function getLiquidationsTypeIdFromPrefijo($prefijo = null, $client_id = null) {
        $resul = $this->find('first', ['conditions' => ['LiquidationsType.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'LiquidationsType.enabled' => 1, 'LiquidationsType.prefijo' => $prefijo], 'fields' => ['LiquidationsType.id']]);
        return empty($resul) ? 0 : $resul['LiquidationsType']['id'];
    }

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['LiquidationsType']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }
        return true;
    }

    /*
     * Solo pueden borrarse los tipos de liquidacion si no tienen saldos iniciales asociados
     */

    public function beforeDelete($cascade = true) {
        $count = $this->SaldosIniciale->find("count", array("conditions" => array("liquidations_type_id" => $this->id)));
        if ($count == 0) {
            return true;
        } else {
            SessionComponent::setFlash(__('Existen saldos iniciales asociados al tipo de liquidación, no se puede eliminar'), 'error', array(), 'otro');
            return false;
        }
    }

    public function afterSave($created, $options = []) {
        if ($created) {
            $consorcios = $this->Liquidation->Consorcio->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']],
                'fields' => ['id', 'name']]);
            foreach ($consorcios as $k => $v) {
                $existe = $this->Liquidation->find('first', ['conditions' => ['Liquidation.inicial' => 1, 'Liquidation.consorcio_id' => $v['Consorcio']['id'], 'Liquidation.liquidations_type_id' => $this->data['LiquidationsType']['id']], 'fields' => ['id']]);
                if (empty($existe)) {// no existe la liq inicial de este tipo de liquidacion, la creo
                    $this->Liquidation->create();
                    $d = array('liquidations_type_id' => $this->data['LiquidationsType']['id'], 'consorcio_id' => $v['Consorcio']['id'], 'name' => 'Saldo inicial ' . $v['Consorcio']['name'] . " (" . $this->data['LiquidationsType']['name'] . ")",
                        'periodo' => 'Saldo inicial ' . $v['Consorcio']['name'] . " (" . $this->data['LiquidationsType']['name'] . ")", 'description' => 'SI', 'inicial' => 1, 'vencimiento' => date('Y-m-d'), 'limite' => date('Y-m-d'), 'closed' => date('Y-m-d H:i:s'));
                    $this->Liquidation->save($d, array('callbacks' => false));
                }

                // agrego consorciosconfigurations
                $this->Liquidation->Consorcio->Consorciosconfiguration->create();
                $this->Liquidation->Consorcio->Consorciosconfiguration->save(['consorcio_id' => $v['Consorcio']['id'], 'liquidations_type_id' => $this->data['LiquidationsType']['id'],
                    'enviaraviso' => 0, 'reportarsaldo' => 0, 'onlinerc' => 0, 'onlinerg' => 0, 'onlinecs' => 0, 'imprimerc' => 0, 'imprimerg' => 0, 'imprimecs' => 0]);
            }
        }
    }

    /*
     * Valida que el prefijo sea unico para el cliente actual
     */

    public function checkUnique($check) {
        $client_id = (($_SESSION['Auth']['User']['is_admin'] == 0) ? $_SESSION['Auth']['User']['client_id'] : 0);
        $resul = $this->find('count', array(
            'conditions' => array('LiquidationsType.prefijo' => $check['prefijo'], 'LiquidationsType.client_id' => $client_id, 'LiquidationsType.id !=' => $this->id),
        ));
        return ($resul == 0);
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
        ));
    }

}
