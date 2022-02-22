<?php

App::uses('AppModel', 'Model');

class CoeficientesPropietario extends AppModel {

    public $validate = array(
        'coeficiente_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'propietario_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'value' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
            'range' => array(
                'rule' => array('range', -0.00001, 100.00001),
                'message' => 'Debe ser un numero decimal entre 0 y 100',
            ),
        )
    );
    public $belongsTo = array(
        'Coeficiente' => array(
            'className' => 'Coeficiente',
            'foreignKey' => 'coeficiente_id',
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

    /*
     * Verifico que no se esta creando un coeficiente para un propietario que ya exista
     */

    public function beforeSave($options = []) {

        if (isset($this->data['CoeficientesPropietario']['coeficiente_id']) && $this->find('count', array('conditions' => array('CoeficientesPropietario.coeficiente_id' => $this->data['CoeficientesPropietario']['coeficiente_id'], 'CoeficientesPropietario.propietario_id' => $this->data['CoeficientesPropietario']['propietario_id']), 'recursive' => -1)) != 0) {
            //SessionComponent::setFlash(__('Ya existe el Coeficiente para el Propietario que intenta agregar'), 'error', [], 'otro');
            return false;
        }
        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                'Coeficiente.name LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
