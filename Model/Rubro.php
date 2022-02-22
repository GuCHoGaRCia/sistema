<?php

App::uses('AppModel', 'Model');

class Rubro extends AppModel {

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
        'GastosGenerale' => array(
            'className' => 'GastosGenerale',
            'foreignKey' => 'rubro_id',
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
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Rubro.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function getRubrosInfo($consorcio_id) {
        return $this->find('list', ['conditions' => ['Rubro.consorcio_id' => $consorcio_id, 'Rubro.habilitado' => 1], 'order' => 'orden asc,id']);
    }

    public function getConsorcioId($rubro_id) {
        $this->id = $rubro_id;
        return $this->field('consorcio_id');
    }

    /*
     * Para cada consorcio seleccionado crea los rubros (muchos consorcios y nombres de rubros a la vez)
     */

    public function guardar($data) {
        if (!isset($data['Rubro']['consorcio_id'])) {
            return false;
        }
        $listaConsorcios = array_keys($this->Consorcio->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']))));
        foreach ($data['Rubro']['consorcio_id'] as $k => $v) {
            if (!in_array($v, $listaConsorcios)) {
                return false;
            }
        }
        foreach ($data['Rubro']['consorcio_id'] as $k => $v) {
            $data['Rubro']['consorcio_id'] = $v;
            foreach ($data['Rubro'] as $r => $s) {
                if (substr($r, 0, 4) == 'name') {
                    $this->create();
                    $this->save(['consorcio_id' => $v, 'habilitado' => 1, 'name' => $s]);
                }
            }
        }
        return true;
    }

    /*
     * Verifico q no existan gastos asociados al rubro
     */

    public function beforeDelete($cascade = true) {
        $count = $this->GastosGenerale->find('count', ['conditions' => ['rubro_id' => $this->id]]);
        if ($count == 0) {
            return true;
        }
        //SessionComponent::setFlash(__('Existen gastos asociados al Rubro, no se puede eliminar'), 'error', [], 'otro');
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
