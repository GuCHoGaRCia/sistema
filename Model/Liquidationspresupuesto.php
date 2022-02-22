<?php

App::uses('AppModel', 'Model');

class Liquidationspresupuesto extends AppModel {

    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            ),
        ),
        'coeficiente_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            ),
        ),
        'total' => array(
            'decimal' => array(
                'rule' => array('decimal'),
            //'message' => 'Your custom message here',
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
        ),
        'Coeficiente' => array(
            'className' => 'Coeficiente',
            'foreignKey' => 'coeficiente_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /*
     * Devuelve el presupuesto para la liquidacion y el coeficiente seleccionados. Por defecto 0
     */

    public function getPresupuesto($liquidation_id, $coeficiente_id) {
        $options = ['conditions' => ['Liquidationspresupuesto.liquidation_id' => $liquidation_id, 'Liquidationspresupuesto.coeficiente_id' => $coeficiente_id],
            'recursive' => -1, 'fields' => ['Liquidationspresupuesto.total']];
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Liquidationspresupuesto']['total']);
    }

    /*
     * Luego de crear una liquidacion, agrego los presupuestos para cada coeficiente de la misma (en el View/add de liquidaciones ya esta
     * el campo Presupuesto para cada coeficiente)
     */

    public function addPresupuesto($liquidation_id, $presupuestos) {
        foreach ($presupuestos as $k => $v) {
            $this->create();
            $this->save(['liquidation_id' => $liquidation_id, 'coeficiente_id' => $v['Liquidationspresupuesto']['coeficiente_id'], 'total' => $v['Liquidationspresupuesto']['total']]);
        }
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
                'c2.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
