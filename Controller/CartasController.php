<?php

App::uses('AppController', 'Controller');

class CartasController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('enviosadm');
        array_push($this->Security->unlockedActions, 'panel_add', 'panel_add2', 'panel_getdatos', 'panel_getConsorcios', 'panel_oEU', 'getCartas', 'panel_boleta', 'panel_envios', 'envios'); // permito blackhole x ajax
    }

    public function index() {
        $this->Carta->recursive = 0;
        $b = [];
        $conditions = ['Carta.client_id' => $_SESSION['Auth']['User']['client_id']];

        if (isset($this->request->data['Carta']['buscar']) && !empty($this->request->data['Carta']['buscar'])) {
            //if (in_array(strtolower($this->request->data['Carta']['buscar']), ['s', 'su', 'gr'])) {
//                $b = ['Cobranza.recibimosde like' => $this->request->data['Carta']['buscar'] . ' %'];
//            } else {
            $conditions += ['OR' => ['Propietario.name like' => '%' . $this->request->data['Carta']['buscar'] . '%',
                    'Carta.codigo like' => '%' . $this->request->data['Carta']['buscar'] . '%', 'Carta.oblea like' => '%' . $this->request->data['Carta']['buscar'] . '%']];
//            }
        }

        $d = isset($this->request->data['Carta']['desde']) ? $this->request->data['Carta']['desde'] : '';
        $h = isset($this->request->data['Carta']['hasta']) ? $this->request->data['Carta']['hasta'] : '';
        $c = isset($this->request->data['Carta']['consorcio']) && $this->request->data['Carta']['consorcio'] !== '' ? ['Consorcio.id' => $this->request->data['Carta']['consorcio']] : [];

        $conditions += !empty($d) ? ['date(Carta.created) >=' => $this->Carta->fecha($d)] : [];
        $conditions += !empty($h) ? ['date(Carta.created) <=' => $this->Carta->fecha($h)] : [];
        $conditions += $c;

        $this->Paginator->settings = array('conditions' => $conditions, 'order' => 'Carta.id desc',
            'fields' => array('Client.name', 'Client.id', 'Consorcio.name', 'Consorcio.id', 'Propietario.name', 'Propietario.id', 'Cartastipo.abreviacion', 'Cartastipo.id', 'Carta.oblea',
                'Carta.codigo', 'Carta.id', 'Carta.created', 'Carta.robada'),
            'order' => 'Carta.created desc,Consorcio.code');

        if (!isset($this->request->data['Carta']) || empty($this->request->data['Carta'])) {
            $this->Paginator->settings += ['limit' => 10];
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }

        $this->set('d', $d);
        $this->set('h', $h);
        $this->set('b', isset($this->request->data['Carta']['buscar']) ? $this->request->data['Carta']['buscar'] : '');
        $this->set('c', $c);
        $this->set('cartas', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Carta->Propietario->Consorcio->getConsorciosList());
    }

    public function panel_index() {
        $this->Carta->recursive = 0;
        $this->Paginator->settings = array('conditions' => array($this->Carta->parseCriteria($this->passedArgs)), 'order' => 'Carta.id desc',
            'fields' => array('Client.name', 'Client.id', 'Consorcio.name', 'Consorcio.id', 'Propietario.name', 'Propietario.id', 'Cartastipo.abreviacion', 'Cartastipo.id', 'Carta.oblea',
                'Carta.codigo', 'Carta.id', 'Carta.created', 'Carta.robada'),
            'order' => 'Carta.created desc,Consorcio.code');
        $this->Prg->commonProcess();
        $this->set('cartas', $this->paginar($this->Paginator));
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $resul = $this->Carta->procesa($this->request->data);
            if ($resul['e'] == "") {
                $this->Flash->success(__('Se guardaron exitosamente ' . $resul['s'] . ' cartas'));
                return $this->redirect(['action' => 'add']);
            } else {
                $errors = "";
                foreach ($resul['e'] as $v) {
                    $errors .= $v;
                }
                $this->Flash->success(__('Se guardaron exitosamente ' . $resul['s'] . ' cartas'));
                $this->Flash->error($errors);
            }
        }
    }

    public function panel_add2() {
        if ($this->request->is('post')) {
            $resul = $this->Carta->procesa($this->request->data);
            if ($resul['e'] == "") {
                $this->Flash->success(__('Se guardaron exitosamente ' . $resul['s'] . ' cartas'));
                return $this->redirect(['action' => 'add2']);
            } else {
                $errors = "";
                foreach ($resul['e'] as $v) {
                    $errors .= $v;
                }
                $this->Flash->success(__('Se guardaron exitosamente ' . $resul['s'] . ' cartas'));
                $this->Flash->error($errors);
            }
        }
        $clients = $this->Carta->Client->find('list', ['fields' => ['Client.id', 'Client.code']]);
        $clients2 = $this->Carta->Client->find('list', ['fields' => ['Client.id', 'Client.name']]);
        $this->set(compact('clients', 'clients2'));
    }

    public function panel_boleta() {
        if ($this->request->is('post')) {
            $this->set('cartas', $this->Carta->getBoletaImposicion($this->request->data));
            $this->set('tipos', $this->Carta->Cartastipo->find('list'));
            $this->set('fecha', $this->request->data['Carta']['fecha']);
            $this->layout = '';
            $this->render('panel_boletareporte');
        }
    }

    public function envios() {
        if ($this->request->is('post')) {
            $cartas = $this->Carta->getEnviosDelDia($this->request->data, $_SESSION['Auth']['User']['client_id']);
            if (empty($cartas)) {
                $this->Flash->info(__('No se enviaron cartas en el d&iacute;a seleccionado'));
                return $this->redirect(['action' => 'envios']);
            }
            $this->set('cartas', $cartas);
            $this->set('tipos', $this->Carta->Cartastipo->find('list'));
            $this->set('fecha', $this->request->data['Carta']['fecha']);
            $this->set('panel', false);
            $this->layout = '';
            $this->render('enviosreporte');
        }
    }

    public function enviosadm($link, $fecha) {
        $f = str_replace("-", "/", $fecha);
        $fecha = $this->Carta->fecha($f);
        $aux['Carta']['fecha'] = $fecha;

        if (!isset($link)) {
            die("El dato es inexistente");
        }
        $email = $this->Carta->Client->Consorcio->Propietario->Aviso->_decryptURL($link);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("El dato es inexistente");
        }
        $client_id = $this->Carta->Client->getClientIdFromMultipleEmails($email);

        if (empty($client_id)) {
            die("El dato es inexistente");
        }
        $cartas = $this->Carta->getEnviosDelDia($aux, $client_id);
        if (empty($cartas)) {
            die(__('No se enviaron cartas en el d&iacute;a seleccionado'));
        }
        $this->set('cartas', $cartas);
        $this->set('tipos', $this->Carta->Cartastipo->find('list'));
        $this->set('fecha', $fecha);
        $this->set('panel', true);
        $this->layout = '';
        $this->render('enviosreporte');
    }

    public function panel_envios() {
        if ($this->request->is('post')) {
            //debug($this->request->data);die;
            $cartas = $this->Carta->getEnviosDelDia($this->request->data, $this->request->data['Carta']['client_id'] == '0' ? null : $this->request->data['Carta']['client_id']);
            if (empty($cartas)) {
                $this->Flash->info(__('No se enviaron cartas en el d&iacute;a seleccionado'));
                return $this->redirect(['action' => 'envios']);
            }
            $this->set('cartas', $cartas);
            $this->set('tipos', $this->Carta->Cartastipo->find('list'));
            $this->set('fecha', $this->request->data['Carta']['fecha']);
            $this->set('panel', false);
            $this->layout = '';
            $this->render('enviosreporte');
        }
        $this->set('clients', $this->Carta->Client->find('list', ['conditions' => ['enabled' => 1]]));
    }

    public function panel_enviosfacturados() {
        if ($this->request->is('post')) {
            $cartas = $this->Carta->getEnviosDelDiafacturadas(['Carta' => ['fecha' => $this->request->data['f']]], $this->request->data['c'] == '0' ? null : $this->request->data['c'], 1);
            if (empty($cartas)) {
                die('1');
            }
            $this->set('cartas', $cartas);
            $this->set('tipos', $this->Carta->Cartastipo->find('list'));
            $this->set('fecha', $this->request->data['f']);
            $this->set('panel', true);
            $this->layout = '';
            $this->render('enviosfacturados');
        }
        $this->set('clients', $this->Carta->Client->find('list', ['conditions' => ['enabled' => 1]]));
    }

    public function panel_enviosnofacturados() {
        if ($this->request->is('post')) {
            $cartas = $this->Carta->getEnviosDelDiafacturadas(['Carta' => ['fecha' => $this->request->data['f']]], $this->request->data['c'] == '0' ? null : $this->request->data['c'], 0);
            if (empty($cartas)) {
                die('1');
            }
            $this->set('cartas', $cartas);
            $this->set('tipos', $this->Carta->Cartastipo->find('list'));
            $this->set('fecha', $this->request->data['f']);
            $this->set('panel', true);
            $this->layout = '';
            $this->render('enviosnofacturados');
        }
        $this->set('clients', $this->Carta->Client->find('list', ['conditions' => ['enabled' => 1]]));
    }

    public function panel_informarRobo($id = null) {
        $this->Carta->id = $id;
        if (!$this->Carta->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $carta = $this->Carta->informarRobo($id);
        if (empty($carta)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->success(utf8_decode('Se informó al Cliente ' . $carta['cliente'] . ' el robo de la Carta ' . $carta['carta']));
        return $this->redirect(['action' => 'index']);
    }

    /*
     * Obtengo los datos del Cliente, Consorcio y Propietario que corresponde al codigo de barras del resumen de cuenta
     */

    public function panel_gD() {
        if (!$this->request->is('ajax')) {
            die();
        }
        //$this->set('msg', $this->Carta->getDatos($this->request->data['c']));
        die($this->Carta->getDatos($this->request->data['c']));
        //$this->layout = '';
    }

    public function panel_gF() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die($this->Carta->guardafacturadas($this->request->data));
    }

    /*
     * verifico que la oblea no haya sido utilizada en otra carta (en TODOS los clientes)
     */

    /* public function panel_cO() {
      if (!$this->request->is('ajax')) {
      die();
      }
      $this->layout = '';
      die(json_encode($this->Carta->obleaEnUso($this->request->data['c'])));
      }
     */
    /*
     * Verifica si la oblea se encuentra en uso
     */

    public function panel_oEU() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Carta->obleaEnUso($this->request->data['o'])));
    }

    public function panel_getConsorcios() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Carta->Consorcio->getConsorciosList($this->request->data['cliente'])));
    }

    public function panel_delete($id = null) {
        $this->Carta->id = $id;
        if (!$this->Carta->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Carta->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado. La liquidaci&oacute;n asociada se encuentra Bloqueada'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function getCartas() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Carta->getCartas($this->request->data)));
    }

}
