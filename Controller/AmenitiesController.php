<?php

App::uses('AppController', 'Controller');

class AmenitiesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('propietarioreservaamenities', 'reservar', 'cancelar');
    }

    public function index() {
        $conditions = ['Amenity.client_id' => $_SESSION['Auth']['User']['Client']['id'], $this->Amenity->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Amenity.consorcio_id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }
        $this->Paginator->settings = ['conditions' => $conditions, 'fields' => ['Consorcio.name', 'Amenity.*', 'Amenitiesconfig.*'], 'order' => 'Amenity.created desc',
            'contain' => ['Amenitiesconfig', 'Consorcio', 'Client']];

        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 50, 'maxLimit' => 50];
        }

        $this->set('amenities', $this->paginar($this->Paginator));
        $this->set('consorcios', $this->Amenity->Consorcio->getConsorciosList());
    }

    /*
     * Aca entra el administrador a gestionar las amenities
     */

    public function view($id = null, $año = null, $mes = null) {
        if (!$this->request->is('ajax')) {
            die;
        }
        $resul = "";
        if (!$this->Amenity->canEdit($id)) {
            $resul .= 'El dato es inexistente';
        }
        $año = $this->request->params['pass'][1] ?? date("Y");
        $mes = $this->request->params['pass'][2] ?? date("m");
        if (empty($mes) || !in_array($mes, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]) || empty($año) || !($año >= 2019 && ($año <= date("Y") + 1)) || ($año == 2019 && $mes <= 9)) {
            $resul .= 'No se encuentran turnos en el año y mes seleccionados';
        }
        $amenity = $this->Amenity->get($id);
        if (empty($amenity) || $amenity['Amenity']['habilitado'] == 0) {
            $resul .= 'La Amenity es inexistente o se encuentra deshabilitada';
        }
        if ($resul === "") {
            $this->set('amenity', $amenity);
            $config = $this->Amenity->Amenitiesconfig->get($id)['Amenitiesconfig'];
            $this->set('config', $config);
            $this->set('turnos', json_encode($this->Amenity->getTurnos($id)));
            $this->set('reservas', json_encode($this->Amenity->getReservas($id, $año, $mes)));
            $this->set('año', $año);
            $this->set('mes', $mes);
            $this->set('diaactual', date("Y-m-d"));
            $this->set('diafinal', date("Y-m-d", strtotime("+" . ($config['diashabilitadosreserva'] <= 0 ? 1000 : $config['diashabilitadosreserva']) . " days")));
        }
        $this->set('resul', $resul);
        $this->layout = '';
        $this->render('propietarioreservaamenities');
    }

    /*
     * Para que el Propietario gestione sus Amenities desde el Panel propietario
     */

    public function propietarioreservaamenities($link = null, $propietario_id = null, $amenitie_id = null, $año = null, $mes = null) {
        if (!$this->request->is('ajax') || empty($link) || empty($amenitie_id) || empty($propietario_id)) {
            die;
        }

        // valido q el email sea valido y el id sea numerico
        $email = $this->Amenity->Client->Aviso->_decryptURL($link);
        $emails = explode(',', $email);
        if (empty($emails)) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
            }
        }

        // valido q el amenity exista y que sea alguno de los habilitados para el consorcio del propietario
        // tambien valido que $propietario_id se encuentre en array_keys($idPropietariosyConsorcios)
        $idPropietariosyConsorcios = $this->Amenity->Consorcio->Propietario->getPropietarioIdFromEmail($email, 'all'); // obtengo los id de todos los propietarios con ese email
        if (empty($idPropietariosyConsorcios) || !in_array($propietario_id, array_keys($idPropietariosyConsorcios))) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }

        // valido mes y año
        $año = !empty($año) ? $año : date("Y");
        $mes = !empty($mes) ? $mes : date("m");
        if (empty($mes) || !in_array($mes, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]) || empty($año) || !($año >= 2019 && ($año <= date("Y") + 1)) || ($año == 2019 && $mes <= 9)) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">No se encuentran turnos en el año y mes seleccionados</p>'));
        }
        //debug($idPropietariosyConsorcios);
        //debug($propietario_id);
        // valido amenity existente y habilitada
        $client_id = $this->Amenity->Consorcio->getConsorcioClientId($idPropietariosyConsorcios[$propietario_id]);
        $amenity = $this->Amenity->get($amenitie_id, $client_id);
        if (empty($amenity) || $amenity['Amenity']['habilitado'] == 0) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">La Amenity es inexistente o se encuentra deshabilitada</p>'));
        }

        $this->set('amenity', $amenity);
        $config = $this->Amenity->Amenitiesconfig->get($amenitie_id)['Amenitiesconfig'];
        $this->set('config', $config);
        $this->set('turnos', json_encode($this->Amenity->getTurnos($amenitie_id, $client_id)));
        $this->set('reservas', json_encode($this->Amenity->getReservas($amenitie_id, $año, $mes, $client_id)));
        $this->set('año', $año);
        $this->set('mes', $mes);
        $this->set('diaactual', date("Y-m-d"));
        $this->set('diafinal', date("Y-m-d", strtotime("+" . ($config['diashabilitadosreserva'] <= 0 ? 1000 : $config['diashabilitadosreserva']) . " days")));
        $this->set('link', $link);
        $this->set('pid', $propietario_id); // para poder reservar y saber q propietario esta reservando
        $this->set('name', $this->Amenity->Consorcio->Propietario->getPropietarioName2($propietario_id)); // para poder reservar y saber q propietario esta reservando

        $this->layout = '';
    }

    /*
     * Realiza una reserva 
     *    "l"   => "qBtg0GFrRPwf2NCdTqLxdpZ7KDyzc0p5g_76tLNwdVRn7tYiAVIGF5lUsukDduA2",
     *    "pid" => "4926",
     *    "aid" => "1",
     *    "tid" => "43",
     *    "f"   => "8/10/2019",
     *    "li"  => "p"
     */

    public function reservar() {
        $link = $this->request->data['l'] ?? null;
        $propietario_id = $this->request->data['pid'] ?? null;
        $amenitie_id = $this->request->data['aid'] ?? null;
        $amenitiesturno_id = $this->request->data['tid'] ?? null;
        $fecha = $this->request->data['f'] ?? null;
        $limpieza = substr($this->request->data['li'] ?? '', 0, 1);
        if (!$this->request->is('ajax') || empty($link) || empty($propietario_id || empty($amenitie_id) || empty($amenitiesturno_id) || empty($fecha))) {
            header("HTTP/1.1 404 Not Found");
            die();
        }

        die(json_encode($this->Amenity->reservar($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha, $limpieza)));
    }

    /*
     * Cancela una reserva
     */

    public function cancelar() {
        $link = $this->request->data['l'] ?? null;
        $propietario_id = $this->request->data['pid'] ?? null;
        $amenitie_id = $this->request->data['aid'] ?? null;
        $amenitiesturno_id = $this->request->data['tid'] ?? null;
        $fecha = $this->request->data['f'] ?? null;
        if (!$this->request->is('ajax') || empty($link) || empty($propietario_id || empty($amenitie_id) || empty($amenitiesturno_id) || empty($fecha))) {
            header("HTTP/1.1 404 Not Found");
            die();
        }

        die(json_encode($this->Amenity->cancelar($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha)));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->Amenity->create();
            if ($this->Amenity->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $consorcios = $this->Amenity->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function edit($id = null) {
        if (!$this->Amenity->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->Amenity->save($this->request->data)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = ['conditions' => ['Amenity.' . $this->Amenity->primaryKey => $id]];
            $this->request->data = $this->Amenity->find('first', $options);
        }
        $consorcios = $this->Amenity->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function delete($id = null) {
        $this->Amenity->id = $id;
        if (!$this->Amenity->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Amenity->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect($this->referer());
    }

}
