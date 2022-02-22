<?php

App::uses('AppController', 'Controller');

class CajastransferenciaschequesController extends AppController {

    public function listar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        if ($this->request->params['pass'][0][0] == 'i') {
            $f = 'cajasingreso_id';
        } else {
            $f = 'cajasegreso_id';
        }
        $this->layout = '';
        $this->set('lista', $this->Cajastransferenciascheque->listar($f, $this->request->params['pass'][1]));
    }

}
