<?php

App::uses('AppModel', 'Model');

class SaldosIniciale extends AppModel {

    public $validate = array(
        'liquidations_type_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'propietario_id' => array(
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Ya existe el saldo inicial para el tipo de liquidacion y propietario',
            ),
        ),
        'capital' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
            'checkBloqueada' => array(
                'rule' => array('checkBloqueada'),
                'message' => 'Existe al menos una liquidacion bloqueada, no se puede modificar el saldo inicial',
                'on' => 'update'
            ),
        ),
        'interes' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
            'range' => array(
                'rule' => array('range', -0.000000000000001, 999999),
                'message' => 'Debe ser un numero decimal mayor o igual a cero',
            ),
            'checkBloqueada' => array(
                'rule' => array('checkBloqueada'),
                'message' => 'Existe al menos una liquidacion bloqueada, no se puede modificar el saldo inicial',
                'on' => 'update'
            ),
        )
    );
    public $belongsTo = array(
        'LiquidationsType' => array(
            'className' => 'LiquidationsType',
            'foreignKey' => 'liquidations_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'SaldosIniciale.id' => $id], 'fields' => [$this->alias . '.id'],
                            'joins' => [['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=SaldosIniciale.propietario_id']],
                                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]]]));
    }

    /*
     * Funcion que devuelve el saldo inicial de los propietarios de un tipo de liquidación (el tipo de la liquidación actual)
     * Si esta seteado $propietario_id, devuelve el saldo inicial del propietario.
     * Si no existe el saldo, devuelve [], sino devuelve array(liquidations_type_id, propietario_id, capital, interes)
     */

    public function getSaldo($liquidation_id = null, $propietario_id = null, $tipo = 'all') {
        if (!is_null($liquidation_id)) {
            $liquidation_type = $this->LiquidationsType->Liquidation->getLiquidationsTypeId($liquidation_id);
            $consorcio_id = $this->LiquidationsType->Liquidation->getConsorcioId($liquidation_id);
            $condiciones['SaldosIniciale.liquidations_type_id'] = $liquidation_type;
            $condiciones['Propietario.consorcio_id'] = $consorcio_id;
        }

        //$tipo = "all";
        if (!is_null($propietario_id)) {
            $condiciones['SaldosIniciale.propietario_id'] = $propietario_id;
            //$tipo = "first"; // lo comente porq sino obtiene los saldos de un solo tipo de liquidacion
        }
        /*
         * Agrego modified as created: si creamos los saldos iniciales en febrero, y en diciembre arranca a liquidar, en la cuenta corriente del propietario
         * no va a mostrar el saldo inicial (se muestran los ultimos 6 meses), entonces modifico manualmente la fecha de creacion del saldo incial
         * x la fecha de modificacion
         */
        $options = ['conditions' => $condiciones, 'recursive' => 0,
            'fields' => ['SaldosIniciale.liquidations_type_id', 'SaldosIniciale.propietario_id', 'SaldosIniciale.modified as created', 'SaldosIniciale.capital', 'SaldosIniciale.interes']];
        if (!is_null($propietario_id)) {
            return $this->find($tipo, $options);
        } else {
            return Hash::combine($this->find($tipo, $options), '{n}.SaldosIniciale.propietario_id', '{n}.SaldosIniciale');
        }
    }

    /*
     * Al visualizar los Saldos Iniciales de un consorcio, verifico q estén creados todos los saldos iniciales para todos los propietarios del consorcio.
     * Si no esta creado, lo inicializo en cero
     */

    public function verificaSaldos($consorcio_id) {
        $propietarios = $this->Propietario->find('list', ['conditions' => ['Propietario.consorcio_id' => $consorcio_id]]);
        if (!empty($propietarios)) {
            foreach ($propietarios as $k => $v) {
                $this->Propietario->creaSaldosIniciales($k);
            }
        }
    }

    public function checkUnique($check) {
        if (isset($this->data['SaldosIniciale']['liquidations_type_id'])) {
            $resul = $this->find('count', array(
                'conditions' => array('SaldosIniciale.propietario_id' => $check['propietario_id'], 'SaldosIniciale.liquidations_type_id' => $this->data['SaldosIniciale']['liquidations_type_id']),
                'recursive' => -1
            ));
            if ($resul == 0) {
                return true;
            } else {
                //SessionComponent::setFlash(__('Ya existe el saldo inicial para el tipo de liquidacion y propietario'), 'error', [], 'otro');
                return false;
            }
        }
        return true;
    }

    /*
     * Funcion q verifica si existe alguna liquidacion bloqueada del tipo de liquidación actual. 
     * Si es asi, no dejo modificar el saldo inicial (ya que es al pedo, porq al cierre se toman los saldos 
     * de la ultima liquidacion cerrada). Esto me permite tener un listado de los saldos al cierre (incluyendo el saldo inicial real)
     */

    public function checkBloqueada($check) {
        $consorcio_id = $this->Propietario->find('first', ['conditions' => ['Propietario.id' => $this->field('propietario_id')], 'fields' => ['Propietario.consorcio_id']])['Propietario']['consorcio_id'];
        return $this->LiquidationsType->Liquidation->hasLiquidationsBloqueadas($consorcio_id, $this->field('liquidations_type_id'));
    }

// funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                'LiquidationsType.name LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
