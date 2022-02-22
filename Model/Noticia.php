<?php

App::uses('AppModel', 'Model');

class Noticia extends AppModel {

    public $validate = array(
        'titulo' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            ),
        ),
        'noticia' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            ),
        ),
    );

    public function beforeSave($options = []) {
        if (isset($this->data['Noticia']['noticia'])) {
            $this->data['Noticia']['noticia'] = $this->cleanHTML($this->data['Noticia']['noticia']);
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
                'Noticia.titulo LIKE' => '%' . $data['buscar'] . '%',
                'Noticia.noticia LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
