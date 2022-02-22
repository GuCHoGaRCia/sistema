<?php

App::uses('AppModel', 'Model');

class Nota extends AppModel {

    public $useTable = 'notas';
    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
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
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Nota.id' => $id], 'fields' => [$this->alias . '.id'], 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]], 'recursive' => 0]));
    }

    public function crearNotas($liquidacionNueva, $liquidacionAnterior) {
        $notasAnteriores = $this->find('first', array('conditions' => array('Nota.liquidation_id' => $liquidacionAnterior)));
        $this->create();
        if (!empty($notasAnteriores)) {
            $this->save(array('liquidation_id' => $liquidacionNueva, 'resumencuenta' => $notasAnteriores['Nota']['resumencuenta'], 'resumengasto' => $notasAnteriores['Nota']['resumengasto'], 'resumengastotop' => $notasAnteriores['Nota']['resumengastotop'], 'composicion' => $notasAnteriores['Nota']['composicion']));
        } else {
            $this->save(array('liquidation_id' => $liquidacionNueva, 'resumencuenta' => '', 'resumengasto' => '', 'resumengastotop' => '', 'composicion' => ''));
        }
    }

    public function beforeSave($options = []) {
        $this->data['Nota']['resumencuenta'] = $this->cleanHTML($this->data['Nota']['resumencuenta']);
        $this->data['Nota']['resumengasto'] = $this->cleanHTML($this->data['Nota']['resumengasto']);
        $this->data['Nota']['resumengastotop'] = $this->cleanHTML($this->data['Nota']['resumengastotop']);
        $this->data['Nota']['composicion'] = $this->cleanHTML($this->data['Nota']['composicion']);
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
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
            //'Client.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
