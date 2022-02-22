<?php

App::uses('AppModel', 'Model');

class Cuentasgastosparticulare extends AppModel {

    public $validate = array(
        'consorcio_id' => array(
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
    );
    public $belongsTo = array(
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = array(
        'GastosParticulare' => array(
            'className' => 'GastosParticulare',
            'foreignKey' => 'cuentasgastosparticulare_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cuentasgastosparticulare.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function getCuentasInfo($consorcio_id) {
        return $this->find('list', ['conditions' => ['consorcio_id' => $consorcio_id]]);
    }

    // Obtiene el nombre de la cuenta de gasto particulares a partir del id de la misma
    
    public function getNombreCGP($cgp_id) {
        $this->id = $cgp_id;
        return $this->field('name');
    }
    
    /*
     * Para cada consorcio seleccionado crea las cuentas de gastos particulares
     */

    public function guardar($data) {
        $consorcios = $data['Cuentasgastosparticulare']['consorcio_id'];
        foreach ($consorcios as $k => $v) {
            $data['Cuentasgastosparticulare']['consorcio_id'] = $v;
            $this->create();
            $this->save($data);
        }
        return true;
    }

    /*
     * Verifico q no existan gastos asociados a la cuenta
     */

    public function beforeDelete($cascade = true) {
        $count = $this->GastosParticulare->find('count', ['conditions' => ['cuentasgastosparticulare_id' => $this->id]]);
        if ($count == 0) {
            return true;
        }
        //SessionComponent::setFlash(__('Existen gastos particulares asociados a la Cuenta, no se puede eliminar'), 'error', [], 'otro');
        return false;
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
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
