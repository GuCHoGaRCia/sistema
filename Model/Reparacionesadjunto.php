<?php

App::uses('AppModel', 'Model');

class Reparacionesadjunto extends AppModel {

    public $displayField = 'titulo';
    public $belongsTo = [
        'Reparacione' => [
            'className' => 'Reparacione',
            'foreignKey' => 'reparacione_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /*
     * Borro el adjunto al borrar el registro
     */

    public function beforeDelete($cascade = true) {
        if (is_file(APP . DS . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'rep' . DS . $this->field('ruta'))) {
            $file = new File(APP . DS . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'rep' . DS . $this->field('ruta'));
            $file->delete();
        }
        return true;
    }

    /*
     * Borro desde reparacione/edit/ una imagen de reparacion
     */

    public function delImagen($id) {
        $resul = $this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacionesadjunto.id' => $id],
            'joins' => [['table' => 'reparaciones', 'alias' => 'Reparacione', 'type' => 'left', 'conditions' => ['Reparacionesadjunto.reparacione_id=Reparacione.id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Reparacione.consorcio_id=Consorcio.id']]]]);
        if (!empty($resul)) {
            $this->id = $resul['Reparacionesadjunto']['id'];
            $this->delete(); // se ejecuta el beforeDelete()
        }
        return true;
    }

}
