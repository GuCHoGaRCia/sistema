<?php

App::uses('AppController', 'Controller');

class PagoselectronicosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'getCobranzas', 'panel_getCobranzas'); // permito blackhole x ajax
    }

    public function panel_index() {
        $conditions = [$this->Pagoselectronico->parseCriteria($this->passedArgs), 'Pagoselectronico.plataforma' => 1]; //1 es PLAPSA
        if (isset($this->request->data['filter']['client']) && $this->request->data['filter']['client'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['client'])) {
            $conditions += ['Pagoselectronico.client_code' => $this->request->data['filter']['client']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions, 'fields' => ['Pagoselectronico.*'], 'order' => 'Pagoselectronico.fecha_proc desc,Pagoselectronico.client_code desc,Pagoselectronico.consorcio_code,Pagoselectronico.propietario_code',
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.code=Pagoselectronico.client_code']]],
            'group' => 'Pagoselectronico.id'
        ];

        if (!isset($this->request->data['filter']['client'])) {
            $this->Paginator->settings += ['limit' => 50];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('pagoselectronicos', $this->paginar($this->Paginator));
        $clients = $this->Pagoselectronico->Client->find('list', ['fields' => ['Client.code', 'Client.name'], 'order' => 'Client.name']);
        //$consorcios = $this->Pagoselectronico->Consorcio->find('list', ['fields' => ['Consorcio.code', 'Consorcio.name']]);
        //$propietarios = $this->Pagoselectronico->Propietario->find('list', ['fields' => ['Propietario.code', 'Propietario.name2'], 'recursive' => 0]);
        $this->set(compact('clients'/* , 'consorcios', 'propietarios' */));
    }

    public function panel_comisiones() {
        $client_code = null;
        $d = date('Y-m-01');
        $h = date('Y-m-d');
        if ($this->request->is('post')) {
            if (isset($this->request->data['Pagoselectronico']['client'])) {
                $client_code = $this->request->data['Pagoselectronico']['client'];
            }

            $d = $this->Pagoselectronico->fecha($this->request->data['Pagoselectronico']['desde']);
            $h = $this->Pagoselectronico->fecha($this->request->data['Pagoselectronico']['hasta']);
            if (!$this->Pagoselectronico->validateDate($d, 'Y-m-d') || !$this->Pagoselectronico->validateDate($h, 'Y-m-d') || !$this->Pagoselectronico->fechaEsMenorIgualQue($d, $h)) {
                $this->Flash->error(__('Las fechas son incorrectas'));
                return $this->redirect(['action' => 'comisiones']);
            }
        }
        $this->set('pagoselectronicos', $this->Pagoselectronico->getComisionesPLAPSA($client_code, $d, $h));
        $clients = $this->Pagoselectronico->Client->find('list', ['fields' => ['Client.code', 'Client.name'], 'order' => 'Client.name']);
        $this->set(compact('clients'));
    }

    public function panel_roela() {
        $conditions = [$this->Pagoselectronico->parseCriteria($this->passedArgs), 'Pagoselectronico.plataforma' => 3]; //3 es ROELA
        if (isset($this->request->data['filter']['client']) && $this->request->data['filter']['client'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['client'])) {
            $conditions += ['Pagoselectronico.client_code' => $this->request->data['filter']['client']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions, 'fields' => ['Pagoselectronico.*'], 'order' => 'Pagoselectronico.fecha_proc desc,Pagoselectronico.client_code desc,Pagoselectronico.consorcio_code,Pagoselectronico.propietario_code',
            'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.code=Pagoselectronico.client_code']]]
        ];

        if (!isset($this->request->data['filter']['client'])) {
            $this->Paginator->settings += ['limit' => 50];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('pagoselectronicos', $this->paginar($this->Paginator));
        $clients = $this->Pagoselectronico->Client->find('list', ['fields' => ['Client.code', 'Client.name'], 'order' => 'Client.name']);
        //$consorcios = $this->Pagoselectronico->Consorcio->find('list', ['fields' => ['Consorcio.code', 'Consorcio.name']]);
        //$propietarios = $this->Pagoselectronico->Propietario->find('list', ['fields' => ['Propietario.code', 'Propietario.name2'], 'recursive' => 0]);
        $this->set(compact('clients'/* , 'consorcios', 'propietarios' */));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            ini_set('max_execution_time', '240');
            $ch = curl_init();
            // set url 
            $fecha = DateTime::createFromFormat('d/m/Y', $this->request->data['Pagoselectronico']['fecha']);
            if (!$fecha) {
                $this->Flash->error(__('La fecha es incorrecta'));
                return $this->redirect(['action' => 'add']);
            }
            curl_setopt($ch, CURLOPT_URL, Router::url('/', true) . "cron_pagosplapsa.php?fecha=" . $fecha->format('Ymd'));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            // close curl resource to free up system resources 
            curl_close($ch);
            $this->set('data', $output);
        }
    }

    public function panel_borrarMultiple() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            die;
        }
        $ids = $this->request->data['ids'];
        $cont = 0;
        foreach ($ids as $v) {
            $this->Pagoselectronico->id = $v;
            if (!$this->Pagoselectronico->exists()) {
                $cont++;
                continue;
            }
            $this->request->allowMethod('post', 'delete');
            if (!$this->Pagoselectronico->delete()) {
                $cont++;
            }
        }
        $cantelementos = count($ids);
        $borrados = $cantelementos - $cont;
        if ($borrados > 0) {
            $this->Flash->success(__('Se borraron exitosamente ' . $borrados . ' Pagos electr&oacute;nicos'));
        }
        if ($cont > 0) {
            $this->Flash->error(__('No se borraron ' . $cont . ' Pagos electr&oacute;nicos'));
        }
        $this->layout = '';
        $this->autoRender = false;
    }

    public function panel_addroela() {
        if ($this->request->is('post')) {
            // create curl resource 
            $ch = curl_init();
            // set url 
            $fecha = DateTime::createFromFormat('d/m/Y', $this->request->data['Pagoselectronico']['fecha']);
            if (!$fecha) {
                $this->Flash->error(__('La fecha es incorrecta'));
                return $this->redirect(['action' => 'add']);
            }
            curl_setopt($ch, CURLOPT_URL, Router::url('/', true) . "cron_pagosroela.php?fecha=" . $fecha->format('Ymd'));

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // $output contains the output string 
            $output = curl_exec($ch);

            // close curl resource to free up system resources 
            curl_close($ch);
            $this->set('data', $output);
        }
    }

    public function panel_delete($id = null) {
        $this->Pagoselectronico->id = $id;
        if (!$this->Pagoselectronico->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Pagoselectronico->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

    public function getCobranzas() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Pagoselectronico->getCobranzas($this->request->data)));
    }

    public function panel_getCobranzas() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Pagoselectronico->getCobranzas($this->request->data, 1)));
    }

}
