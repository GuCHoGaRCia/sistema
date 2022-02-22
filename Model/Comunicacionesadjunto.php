<?php

App::uses('AppModel', 'Model');

class Comunicacionesadjunto extends AppModel {

    public $validate = array(
        'comunicacione_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'titulo' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'ruta' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
    );
    public $belongsTo = array(
        'Comunicacione' => array(
            'className' => 'Comunicacione',
            'foreignKey' => 'comunicacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function beforeDelete($cascade = true) {
        // verifico si tiene adjuntos
        $este = $this->find('all', ['conditions' => ['Comunicacionesadjunto.comunicacione_id' => $this->field('comunicacione_id')], 'fields' => ['Comunicacionesadjunto.id', 'Comunicacionesadjunto.ruta']]);
        if (!empty($este)) {// la comunicacion tiene al menos un adjunto
            $otro = $this->find('list', ['conditions' => ['Comunicacionesadjunto.comunicacione_id !=' => $this->field('comunicacione_id'), 'Comunicacionesadjunto.ruta' => $este[0]['Comunicacionesadjunto']['ruta']]]);
            if (empty($otro)) { // no existe otra comunicacion con estos adjuntos
                $borrar = $this->find('all', ['conditions' => ['Comunicacionesadjunto.comunicacione_id' => $this->field('comunicacione_id')], 'fields' => ['Comunicacionesadjunto.id', 'Comunicacionesadjunto.ruta']]);
                debug($borrar);
                foreach ($borrar as $r) {
                    $arch = APP . WEBROOT_DIR . DS . $r['Comunicacionesadjunto']['ruta'];
                    if (file_exists($arch)) {
                        $file = new File($arch);
                        $file->delete();
                    }
                }
            }
        }
        return true;
    }

}
