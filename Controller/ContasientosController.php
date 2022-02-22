<?php

App::uses('AppController', 'Controller');

class ContasientosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function add() {
        if ($this->request->is('post')) {
            $resul = $this->Contasiento->guardar($this->request->data);
            if (empty($resul)) {
                $this->Flash->success(__('El Asiento fue guardado correctamente'));
                return $this->redirect(['action' => 'add']);
            } else {
                $this->Flash->error($resul);
            }
        }
        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $contcuentas = $this->Contasiento->Contcuenta->get();
        $this->set(compact('consorcios', 'contcuentas'));
    }

    public function edit($id = null) {
        if ($this->request->is('get')) {// al clickear en editar, va x get
            if (!$this->Contasiento->canEdit($id)) {
                die('El dato es inexistente');
            }
        }

        if ($this->request->is('ajax') && $this->request->is('post')) {// al guardar en edicion, va por ajax/post
            if (!isset($this->request->data['Contasiento']['id'][0])) {
                die('El dato es inexistente');
            }
            if (!$this->Contasiento->canEdit($this->request->data['Contasiento']['id'][0])) {
                die('El dato es inexistente');
            }
            $resul = $this->Contasiento->guardar($this->request->data);
            if (empty($resul)) {
                die("");
            } else {
                die($resul);
            }
        } else {
            $this->request->data = $this->Contasiento->getAsientoInfo($id);
        }
        $this->layout = '';
        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $contcuentas = $this->Contasiento->Contcuenta->get();
        $this->set(compact('consorcios', 'contcuentas'));
    }

    public function config($id = null) {
        if ($this->request->is('post')) {
            if (isset($this->request->data['consorcio_id'])) {
                if (!$this->Contasiento->Consorcio->canEdit($this->request->data['consorcio_id'])) {
                    die(json_encode(['e' => 1, 'd' => 'El dato es inexistente']));
                }
                $this->autoRender = false;
                $view = new View($this, false);
                $view->set('consorcio_id', $this->request->data['consorcio_id']);
                $view->set('contcuentas', $this->Contasiento->Contcuenta->get());
                $view->set('rubros', $this->Contasiento->Consorcio->Rubro->getRubrosInfo($this->request->data['consorcio_id']));
                $view->set('cuentasgp', $this->Contasiento->Consorcio->Cuentasgastosparticulare->getCuentasInfo($this->request->data['consorcio_id']));
                $view->set('bancoscuentas', $this->Contasiento->Consorcio->Bancoscuenta->getCuentasBancarias($this->request->data['consorcio_id']));
                $view->set('config', $this->Contasiento->Consorcio->Contasientosconfig->getConfig($this->request->data['consorcio_id']));
                $view->layout = '';
                return json_encode(['e' => 0, 'd' => $view->render('configdetalle')]);
            }
            if (isset($this->request->data['Contasiento']['consorcio_id'])) {
                if (!$this->Contasiento->Consorcio->canEdit($this->request->data['Contasiento']['consorcio_id'])) {
                    die(json_encode(['e' => 1, 'd' => 'El dato es inexistente']));
                }
                $resul = $this->Contasiento->Consorcio->Contasientosconfig->guardarConfiguracion($this->request->data['Contasiento']['consorcio_id'], $this->request->data['Contasiento']);
                if ($resul['e'] == 1) {
                    die(json_encode($resul));
                } else {
                    $this->autoRender = false;
                    $this->Flash->success(__('La configuraci&oacute;n fue guardada correctamente'));
                    die(json_encode(['e' => 0]));
                }
            }
        }

        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function view($id = null) {
        if (!$this->Contasiento->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }

        $resul = $this->Contasiento->getAsientoInfo($id);
        if (empty($resul)) {
            $this->Flash->error(__('El dato es inexistente'));
        }
        $this->layout = '';
        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $contcuentas = $this->Contasiento->Contcuenta->get();
        $this->set('contasiento', $resul);
        $this->set(compact('consorcios', 'contcuentas'));
    }

    public function automaticos() {
        if ($this->request->is('post')) {
            if (!isset($this->request->data['Contasiento']['consorcio_id']) || !is_numeric($this->request->data['Contasiento']['consorcio_id'])) {
                $this->Flash->error(__('El dato es inexistente'));
                return $this->redirect(['action' => 'index']);
            }
            if ($this->request->data['Contasiento']['consorcio_id'] != 0 && !$this->Contasiento->Consorcio->canEdit($this->request->data['Contasiento']['consorcio_id'])) {
                $this->Flash->error(__('El dato es inexistente'));
                return $this->redirect(['action' => 'index']);
            }
            $resul = $this->Contasiento->generarAsientosAutomaticos($this->request->data['Contasiento']['consorcio_id']);
            $this->set('consorcio', $this->request->data['Contasiento']['consorcio_id']);
            if (empty($resul)) {
                $this->Flash->success(__('Los Asientos Autom&aacute;ticos fueron generados correctamente'));
            } else {
                $this->Flash->error($resul);
            }
        }
        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function balance() {
        if (isset($this->request->data['Contasiento']['consorcio_id'])) {
            $this->set('ejerciciosconsor', $this->Contasiento->Contejercicio->getEjercicios($this->request->data['Contasiento']['consorcio_id']));
        }
        if (isset($this->request->data['Contasiento']['mes'])) {
            $this->set('mes', $this->request->data['Contasiento']['mes']);
        }
        if ($this->request->is('post')) {
            if (!isset($this->request->data['Contasiento']['consorcio_id'])) {
                $this->Flash->error("El Consorcio es inexistente");
                return $this->redirect(['action' => 'balance']);
            }
            $this->set('meses', $this->Contasiento->Contejercicio->getMeses($this->request->data['Contasiento']['ejercicio']));
            $resul = $this->Contasiento->getAsientosConsorcio($this->request->data['Contasiento']['consorcio_id'], $this->request->data['Contasiento']['ejercicio'], $this->request->data['Contasiento']['mes']);
            if ($resul['e'] == 0) {
                $this->set('cuentas', $this->Contasiento->Contcuenta->getInfo());
                $this->set('asientos', $resul['d']);
                $this->set('arbol', $this->Contasiento->Contcuenta->Conttitulo->getArbol());
                $this->set('titulos', $this->Contasiento->Contcuenta->Conttitulo->get());
            } else {
                $this->Flash->error(h($resul['d']));
            }
        }

        $consorcios = $this->Contasiento->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        if (!$this->Contasiento->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Contasiento->borrar($id)) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
