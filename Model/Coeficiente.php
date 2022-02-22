<?php

App::uses('AppModel', 'Model');

class Coeficiente extends AppModel {

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
        'enabled' => array(
            'boolean' => array(
                'rule' => array('boolean'),
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
        'GastosGeneraleDetalle' => array(
            'className' => 'GastosGeneraleDetalle',
            'foreignKey' => 'coeficiente_id',
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
        'GastosParticulare' => array(
            'className' => 'GastosParticulare',
            'foreignKey' => 'coeficiente_id',
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
        'Liquidationspresupuesto' => array(
            'className' => 'Liquidationspresupuesto',
            'foreignKey' => 'coeficiente_id',
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
    public $hasAndBelongsToMany = array(
        'Propietario' => array(
            'className' => 'Propietario',
            'joinTable' => 'coeficientes_propietarios',
            'foreignKey' => 'coeficiente_id',
            'associationForeignKey' => 'propietario_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Coeficiente.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function beforeSave($options = []) {
        if (isset($this->data['Coeficiente']['consorcio_id']) && count($this->Propietario->getPropietariosId($this->data['Coeficiente']['consorcio_id'])) == 0) {
            //SessionComponent::setFlash(__('No se han cargado propietarios en el consorcio. Agregue Propietarios (men&uacute; Datos) e intente nuevamente.'), 'error', array(), 'otro');
            return false;
        }
        return true;
    }

    public function getConsorcioId($coeficiente_id) {
        $this->id = $coeficiente_id;
        return $this->field('consorcio_id');
    }

    public function getList($consor) {
        return $this->find('list', array('conditions' => array('Consorcio.id' => $consor, 'Coeficiente.enabled' => 1), 'recursive' => 0));
    }

    /*
     * Creo el coeficiente para todos los propietarios del consorcio
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            $props = $this->Propietario->getPropietariosId($this->data['Coeficiente']['consorcio_id']);
            foreach ($props as $k => $v) {
                $this->CoeficientesPropietario->create();
                $d = array('coeficiente_id' => $this->data['Coeficiente']['id'], 'propietario_id' => $v['Propietario']['id'], 'value' => 0);
                $this->CoeficientesPropietario->save($d, array('callbacks' => false, 'validate' => false));
            }

            // agrego los presupuestos para el coeficiente y cada una de las liquidaciones iniciales
            $l = $this->Consorcio->Liquidation->getLiquidationsIniciales($this->data['Coeficiente']['consorcio_id']);
            foreach ($l as $v) {
                $this->Consorcio->Liquidation->Liquidationspresupuesto->create();
                $this->Consorcio->Liquidation->Liquidationspresupuesto->save(array('liquidation_id' => $v['Liquidation']['id'], 'coeficiente_id' => $this->data['Coeficiente']['id'], 'total' => 0), array('callbacks' => false, 'validate' => false));
            }
        }
    }

    /*
     * Verifico q no haya gastos creados utilizando este coeficiente. Si existen, no permito borrar
     */

    public function beforeDelete($cascade = true) {
        if ($this->GastosGeneraleDetalle->find('count', array('conditions' => array('coeficiente_id' => $this->id))) == 0) {
            if ($this->GastosParticulare->find('count', array('conditions' => array('coeficiente_id' => $this->id))) == 0) {
                return true;
            }
        }
        //SessionComponent::setFlash(__('Existen gastos relacionados, no se puede eliminar el coeficiente'), 'error', array(), 'otro');
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
