<?php

App::uses('AppModel', 'Model');

class Help extends AppModel {

    public $validate = array(
        'controller' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'action' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'content' => array(
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

    public function beforeSave($options = []) {
        if (isset($this->data['Help']['controller']) && isset($this->data['Help']['action'])) {
            $conditions = ['Help.controller' => $this->data['Help']['controller'], 'Help.action' => $this->data['Help']['action'], 'Help.soloadmin' => $this->data['Help']['soloadmin']];
            if (isset($this->data['Help']['id'])) {
                $conditions+= ['Help.id !=' => $this->data['Help']['id']];
            }
            $resul = $this->find('count', array('conditions' => $conditions, 'recursive' => -1));
            if ($resul == 0) {
                return true;
            } else {
                //SessionComponent::setFlash(__('La ayuda para esa seccion ya existe, modifique la misma o cambie la actual'), 'error', [], 'otro');
                return false;
            }
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
                $this->alias . '.controller LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.action LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.content LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
