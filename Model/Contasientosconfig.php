<?php

App::uses('AppModel', 'Model');

class Contasientosconfig extends AppModel {

    public $validate = [
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'config' => [
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
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Obtiene la configuracion de asientos del consorcio
     */

    public function getConfig($consorcio) {
        $c = $this->find('first', ['conditions' => ['Contasientosconfig.consorcio_id' => $consorcio], 'fields' => ['config']]);
        if (empty($c)) {
            return "";
        } else {
            return $c['Contasientosconfig']['config'];
        }
    }

    /*
     * Verifica si algun asiento del consorcio no tiene cuenta asociada.
     * Si se agregan rubros, cuentas o cuentas bancarias, tambien detecta si esta incompleta la configuracion
     */

    public function hasIncompleteConfig($consorcio) {
        $c = json_decode($this->getConfig($consorcio), true);
        if (empty($c)) {
            return true;
        }
        $rubros = $this->Consorcio->Rubro->getRubrosInfo($consorcio);
        if (count($rubros) > 0 && (!isset($c['liquidaciones']['rubros']) || count($rubros) !== count($c['liquidaciones']['rubros']))) {
            return true;
        }
        $cuentas = $this->Consorcio->Cuentasgastosparticulare->getCuentasInfo($consorcio);
        if (count($cuentas) > 0 && (!isset($c['liquidaciones']['cuentasgp']) || count($cuentas) !== count($c['liquidaciones']['cuentasgp']))) {
            return true;
        }
        $bancos = $this->Consorcio->Bancoscuenta->getCuentasBancarias($consorcio);
        if (count($bancos) > 0 && (!isset($c['bancos']['cierre']) || count($bancos) !== count($c['bancos']['cierre']))) {
            return true;
        }
        return $this->_hasEmptyValue($c);
    }

    private function _hasEmptyValue($c) {
        foreach ($c as $c1) {
            if (is_array($c1)) {
                $vacio = $this->_hasEmptyValue($c1);
            } else {
                $vacio = empty($c1);
            }
            if ($vacio) {
                return true;
            }
        }
        return false;
    }

    public function guardarConfiguracion($consorcio, $config) {
        $c = $this->find('first', ['conditions' => ['consorcio_id' => $consorcio], 'fields' => 'id']);
        $data = ['consorcio_id' => $consorcio, 'config' => json_encode($config)];
        if (empty($c)) {
            $this->create();
        } else {
            $data['id'] = $c['Contasientosconfig']['id'];
        }

        if ($this->save($data)) {
            return ['e' => 0];
        } else {
            return ['e' => 1, 'd' => "La configuración no pudo ser guardada"];
        }
    }

}
