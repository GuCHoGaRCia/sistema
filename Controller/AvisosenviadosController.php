<?php

App::uses('AppController', 'Controller');

class AvisosenviadosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function panel_index() {
        $conditions = [];
        $c = isset($this->request->data['Avisosenviado']['client_id']) ? $this->request->data['Avisosenviado']['client_id'] : '';
        $a = isset($this->request->data['Avisosenviado']['ano']) ? $this->request->data['Avisosenviado']['ano'] : '';
        $m = isset($this->request->data['Avisosenviado']['mes']) ? $this->request->data['Avisosenviado']['mes'] : '';
        $anos = [date("Y") - 1, date("Y"), date("Y") + 1];
        if (!empty($c)) {
            $conditions += ['Avisosenviado.client_id' => $c];
        }
        if ((!empty($m) && $m >= 0) || (empty($m) && $m == '0')) {
            $conditions += ['Avisosenviado.month' => $m + 1];
        }
        if (!empty($a) || $a == '0') {
            $conditions += ['Avisosenviado.year' => $anos[$a]];
        }
        $this->set('avisosenviados', $this->Avisosenviado->find('all', ['conditions' => $conditions, 'order' => 'Avisosenviado.year desc,Avisosenviado.month desc,Consorcio.code', 'recursive' => 0]));
        $this->set('clients', $this->Avisosenviado->Client->find('list', ['conditions' => ['enabled' => 1]]));
        $this->set('c', $c);
        $this->set('m', $m);
        $this->set('a', $a);
    }

}
