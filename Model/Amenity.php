<?php

App::uses('AppModel', 'Model');

class Amenity extends AppModel {

    public $displayField = 'nombre';
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'consorcio_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => 'Debe ingresar un nombre',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'reglamento' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                //'message' => 'Your custom message here',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'habilitado' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];
    public $belongsTo = [
        'Client' => [
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Consorcio' => [
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];
    public $hasOne = [
        'Amenitiesconfig' => [
            'className' => 'Amenitiesconfig',
            'foreignKey' => 'amenitie_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ]
    ];
    public $hasMany = [
        'Amenitiesreserva' => [
            'className' => 'Amenitiesreserva',
            'foreignKey' => 'amenitie_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Amenitiesturno' => [
            'className' => 'Amenitiesturno',
            'foreignKey' => 'amenitie_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ]
    ];

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Amenity.client_id' => $_SESSION['Auth']['User']['client_id'], 'Amenity.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    /*
     * Se llama desde panel propietario x ajax, al hacer click en "Reservar Quincho", por ej.
     * En AmenitiesController::propietarioreservaamenities() ya valido $client_id (a traves del link)
     */

    public function get($id, $client_id = null) {
        return $this->find('first', ['conditions' => ['Amenity.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'Amenity.id' => $id], 'contain' => ['Consorcio.name']]);
    }

    /*
     * Obtiene todas las amenities del consorcio (panel propietario, Aviso::getDatos()). No necesito client_id porq ya hago la validacion del cliente en Aviso::getDatos()
     */

    public function getAll($consorcio_id, $client_id = null) {
        return $this->find('all', ['conditions' => ['Amenity.consorcio_id' => $consorcio_id, 'Amenity.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'Amenity.habilitado' => 1]]);
    }

    /*
     * obtiene los turnos de un amenitie y los formatea para poder mostrarlos en el calendario de forma mas facil
     * formato: diasemana=>[inicio y fin,inicio y fin]
     * (int) 1 => array(
     *     (int) 0 => array(
     *         'i' => '07:00:00',
     *         'f' => '09:00:00'
     *     ),
     *     (int) 1 => array(
     *         'i' => '10:30:00',
     *         'f' => '10:30:00'
     *     )
     * ),
     * (int) 7 => array(
     *     (int) 0 => array(
     *         'i' => '12:30:00',
     *         'f' => '18:30:00'
     *    )
     *  )
     */

    public function getTurnos($id, $client_id = null) {
        $turnos = $this->find('all', ['conditions' => ['Amenity.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'Amenity.id' => $id, 'Amenitiesturno.habilitado' => 1],
            'fields' => ['Amenitiesturno.id', 'Amenitiesturno.diasemana', 'Amenitiesturno.inicio', 'Amenitiesturno.fin'], 'order' => 'Amenitiesturno.amenitie_id,Amenitiesturno.inicio',
            'joins' => [['table' => 'amenitiesturnos', 'alias' => 'Amenitiesturno', 'type' => 'left', 'conditions' => ['Amenitiesturno.amenitie_id=Amenity.id']]]]);
        if (empty($turnos)) {
            return $turnos;
        }
        $resul = [];
        foreach ($turnos as $v) {
            $d = $v['Amenitiesturno'];
            if (!isset($resul[$d['diasemana']])) {
                $resul[$d['diasemana']] = [];
            }
            $resul[$d['diasemana']][] = ['id' => $d['id'], 'i' => $d['inicio'], 'f' => $d['fin']];
        }
        return $resul;
    }

    /*
     * Obtiene todas las reservas de un amenitie para el año y mes dados
     * Si $client_id!=null, viene del panel del propietario, entonces obtengo solo las VIGENTES
     * Si $client_id==null, es el administrador (usa $_SESSION) y muestro todo el historial (canceladas y no canceladas)
     * (int) 0 => array(
     *    'Propietario' => array(
     *          'name' => 'rakuzanska kasimiera'
     *      ),
     *      'Amenitiesreserva' => array(
     *          'amenitiesturno_id' => '1'
     *      ),
     *      (int) 0 => array(
     *          'fecha' => '23'
     *      )
     *  )
     * queda
     * 	(int) 23 => array(
     * 	    (int) 0 => array(
     * 		'p' => 'rakuzanska kasimiera',
     * 		't' => '1'
     * 	    )
     *  )
     */

    public function getReservas($id, $año = null, $mes = null, $client_id = null) {
        $m = empty($mes) ? date("m") : $mes;
        $a = empty($año) ? date("Y") : $año;
        $reservas = $this->find('all', ['conditions' => ['Amenity.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'Amenity.id' => $id, 'YEAR(fecha)' => $a, 'MONTH(fecha)' => $m, 'Amenity.habilitado' => 1, 'Amenitiesturno.habilitado' => 1] +
            (empty($client_id) ? [] : ['Amenitiesreserva.cancelado' => false]),
            'fields' => ['Amenitiesreserva.amenitiesturno_id', 'Amenitiesreserva.fecha as fecha', 'Amenitiesreserva.fechacancelacion', 'Amenitiesreserva.created', 'Amenitiesreserva.cancelado', 'Amenitiesreserva.multado', 'Amenitiesreserva.propietario_id', 'Propietario.name', 'Propietario.code', 'Propietario.unidad'],
            'joins' => [['table' => 'amenitiesturnos', 'alias' => 'Amenitiesturno', 'type' => 'left', 'conditions' => ['Amenitiesturno.amenitie_id=Amenity.id']],
                ['table' => 'amenitiesreservas', 'alias' => 'Amenitiesreserva', 'type' => 'left', 'conditions' => ['Amenitiesreserva.amenitie_id=Amenity.id', 'Amenitiesreserva.amenitiesturno_id=Amenitiesturno.id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Amenitiesreserva.propietario_id=Propietario.id']]],
            'order' => 'Amenitiesreserva.created']);
        if (empty($reservas)) {
            return $reservas;
        }
        $resul = [];
        foreach ($reservas as $v) {
            $habilitarreservapanelpropietario = strtotime($v['Amenitiesreserva']['fecha']) >= strtotime(date("Y-m-d")) ? 1 : 0;
            $f = date("d", strtotime($v['Amenitiesreserva']['fecha']));
            if (!isset($resul[$f])) {
                $resul[$f] = [];
            }
            $resul[$f][] = ['p' => h($v['Propietario']['name'] . " - " . $v['Propietario']['unidad'] . " (" . $v['Propietario']['code'] . ")"), 'cr' => date("d/m/Y H:i:s", strtotime($v['Amenitiesreserva']['created'])),
                't' => $v['Amenitiesreserva']['amenitiesturno_id'], 'h' => $habilitarreservapanelpropietario, 'id' => $v['Amenitiesreserva']['propietario_id'],
                'c' => $v['Amenitiesreserva']['cancelado'], 'm' => $v['Amenitiesreserva']['multado'] ? 1 : 0, 'f' => date("d/m/Y H:i:s", strtotime($v['Amenitiesreserva']['fechacancelacion']))];
        }
        return $resul;
    }

    public function reservar($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha, $limpieza = '') {
        $valida = $this->_valida($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha);
        if (!empty($valida)) {
            return $valida;
        }

        $config = $this->Amenitiesconfig->get($amenitie_id)['Amenitiesconfig'];

        // según la configuracion "Seleccionar quien realiza limpieza", valido q sea 'p' o 'e'
        if ($config['seleccionarquienrealizalimpieza'] === '1' && (empty($limpieza) || !in_array($limpieza, ['p', 'e']))) {
            return ['e' => 1, 'd' => __('Debe seleccionar quien realiza la limpieza')];
        } else {
            $limpieza = ''; // no usa $config['seleccionarquienrealizalimpieza'] y en $limpieza vino algun caracter, lo borro
        }

        $f1 = explode("/", $fecha);
        $f = $f1[2] . "-" . str_pad($f1[1], 2, "0", STR_PAD_LEFT) . "-" . str_pad($f1[0], 2, "0", STR_PAD_LEFT);

        // si no permite reservas condicionales, no me importa el valor de $config['maxreservasporpropietario']
        if ($config['reservacondicional'] === false) {
            return $this->_reservaNoCondicional($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza);
        } else {// reservacondicional es true
            if ($config['maxreservasporpropietario'] > 0 && $this->Amenitiesreserva->getCantidadReservasPorPropietario($amenitie_id, $propietario_id, date("Y", strtotime($f))) >= $config['maxreservasporpropietario']) {
                // el propietario q reserva supera la cantidad anual, reservo condicional y agrego mensaje?
            } else {
                return $this->_reservaCondicional($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza, $config);
            }
        }

        // obtengo el estado del dia a reservar (si ya fue reservado y por quien, si tiene condicional, etc
        //        if (count($reservas) == 2) {//no se q pasó, pero no puede reservar (ya hay una reserva y otra condicional)-. NO!! Si esta cancelando, le muestra esto
        //            return ['e' => 1, 'd' => __('El Turno ya se encuentra reservado y un Propietario se encuentra en espera')];
        //        }
        /* $primerturno = false; // si hay 2 reservas, y cancelo la condicional, no me debe multar
          foreach ($reservas as $k => $v) {
          if (!$primerturno) {
          $primerturno = true;
          }
          // verifico si el turno es del propietario actual (en ese caso lo cancelo)
          if ($v['Amenitiesreserva']['propietario_id'] == $propietario_id && $v['Amenitiesreserva']['cancelado'] == 0) {
          // segun configuración "Días habilitados cancelación", si cancela en esos dias, aviso y hay multa
          $leyenda = "";
          $multa = 0;
          if ($config['diashabilitadoscancelacion'] > 0 && !$primerturno && strtotime(date("Y-m-d")) >= strtotime(date("Y-m-d", strtotime($v['Amenitiesreserva']['fecha'] . " -" . $config['diashabilitadoscancelacion'] . " days")))) {
          $leyenda = ". Debido a que la cancelación no fue realizada con " . $config['diashabilitadoscancelacion'] . " dias de anticipación como mínimo, se le aplicará una multa.";
          $multa = 1;
          }
          $this->Amenitiesreserva->cancelar($v['Amenitiesreserva']['id'], $multa);

          //aviso al condicional q tiene disponible el turno
          //TODO


          return ['e' => 0, 'd' => __('Su Turno fue cancelado correctamente') . $leyenda];
          }
          } */
//        if ($this->Amenitiesreserva->crear($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza)) {
//            return ['e' => 0, 'd' => __('El Turno fue reservado correctamente (si ya se encontraba reservado por otro Propietario, su reserva es condicional)')];
//        } else {
//            return ['e' => 1, 'd' => __('El Turno no pudo ser reservado, intente nuevamente')];
//        }
        // si no permito condicional, y ya esta reservado (count=1):
        //      si es del mismo propietario, la cancelo
        //      sino
        // hay reservas hechas, veo que hago
        // si hay hecha
    }

    /*
     * Realizo una reserva. La amenitie esta configurada para no permitir reservas condicionales. 
     * getReservas() deberia traer [], ya q no permite condicionales y desde el panel propietario deberia habilitar la reserva solo si no fue reservado el turno
     */

    private function _reservaNoCondicional($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza) {
        $reservas = $this->Amenitiesreserva->getReservasVigentes($amenitie_id, $amenitiesturno_id, $f);
        if (empty($reservas)) {// no deberia haber reservas
            return $this->Amenitiesreserva->crear($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza);
        } else {
            // reservó y ya existia un turno anterior? capaz al mismo tiempo lo hicieron, y le ganaron de mano
            return ['e' => 1, 'd' => __('El Turno no pudo ser reservado, ya existe una reserva previa')];
        }
    }

    private function _reservaCondicional($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza, $config) {
        $reservas = $this->Amenitiesreserva->getReservasVigentes($amenitie_id, $amenitiesturno_id, $f);
        // si no hay reservas, reservo y listo
        if (empty($reservas)) {
            return $this->Amenitiesreserva->crear($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza);
        }
        if (count($reservas) === 1) {// hay una reserva vigente previa
            // $config['maxreservasporpropietario'] > 0 y el propietario q reservó antes supera la cantidad maxima,
            if ($config['maxreservasporpropietario'] > 0 && $this->Amenitiesreserva->getCantidadReservasPorPropietario($amenitie_id, $propietario_id, date("Y", strtotime($f))) >= $config['maxreservasporpropietario']) {
                
            }

            $resul = $this->Amenitiesreserva->crear($amenitie_id, $amenitiesturno_id, $propietario_id, $f, $limpieza);
            if ($resul['e'] === 0) {
                return ['e' => 0, 'd' => __('Su reserva CONDICIONAL fue registrada correctamente (ya existía una reserva anterior, si el Propietario la cancela se le avisará por email y la suya quedará como efectiva)')];
            } else {
                return $resul; //hubo algun error en el guardado?
            }
        }

        return ['e' => 1, 'd' => __('El Turno no pudo ser reservado, ya existe una reserva efectiva y una condicional previa')];
    }

    public function cancelar($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha) {
        $valida = $this->_valida($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha);
        if (!empty($valida)) {
            return $valida;
        }
        $f1 = explode("/", $fecha);
        $f = $f1[2] . "-" . str_pad($f1[1], 2, "0", STR_PAD_LEFT) . "-" . str_pad($f1[0], 2, "0", STR_PAD_LEFT);
        $config = $this->Amenitiesconfig->get($amenitie_id)['Amenitiesconfig'];

        // obtengo las reservas
        $reservasVigentes = $this->Amenitiesreserva->getReservasVigentes($amenitie_id, $amenitiesturno_id, $f);
        if (empty($reservasVigentes)) {// no hay reservas para cancelar!
            return ['e' => 1, 'd' => __('No tiene reservas previas para cancelar')];
        }

        $reservaPropietario = $this->Amenitiesreserva->getReservaPropietario($amenitie_id, $amenitiesturno_id, $propietario_id, $f);
        $primera = reset($reservasVigentes)['Amenitiesreserva'];
        $leyenda = "";
        $multa = 0;
        if ($primera === reset($reservaPropietario)) {
            // la primera vigente es del propietario actual, entonces la cancelo y me fijo si hay condicional. Si hay, le aviso al propietario q quedó efectiva su condicionalidad
            if ($config['diashabilitadoscancelacion'] > 0 && strtotime(date("Y-m-d")) >= strtotime(date("Y-m-d", strtotime($primera['fecha'] . " -" . $config['diashabilitadoscancelacion'] . " days")))) {
                $leyenda = ". Debido a que la cancelación no fue realizada con " . $config['diashabilitadoscancelacion'] . " dias de anticipación como mínimo, se le aplicará una multa.";
                $multa = 1;
            }
            // la primera vigente es del propietario, la cancelo
            $this->Amenitiesreserva->cancelar($primera['id'], $multa);

            // si existe una segunda reserva (condicional), le aviso q quedó efectiva
            if (isset($reservasVigentes[1])) {
                $propietario_id = $reservasVigentes[1]['Amenitiesreserva']['propietario_id'];
                // obtengo el email del propietario y le aviso q su reserva quedó efectiva
                $cliente = $this->Consorcio->getConsorcioClienteInfo($this->Consorcio->Propietario->getPropietarioConsorcio($propietario_id));
                $amenity = $this->get($amenitie_id, $cliente['Client']['id']);
                $turno = $this->Amenitiesturno->get($reservasVigentes[1]['Amenitiesreserva']['amenitiesturno_id']);
                $nombre = $amenity['Amenity']['nombre'] . " - " . $amenity['Consorcio']['name'];
                $text = "Estimado Propietario: su Reserva del dia ";
                $text .= date("d/m/Y", strtotime($reservasVigentes[1]['Amenitiesreserva']['fecha']));
                $text .= " de " . date("H:i", strtotime($turno['inicio'])) . " a " . date("H:i", strtotime($turno['fin']));
                $text .= " (" . $nombre . ") ha quedado efectiva! Que tenga un buen evento!";

                $e = explode(',', $this->Consorcio->Propietario->getPropietarioEmail($propietario_id));
                foreach ($e as $s) {
                    $this->Client->Avisosqueue->create();
                    $this->Client->Avisosqueue->save(['client_id' => $cliente['Client']['id'],
                        'emailfrom' => !empty($cliente['Client']['email']) ? explode(',', $cliente['Client']['email'])[0] : 'noreply@ceonline.com.ar', 'razonsocial' => h($cliente['Client']['name']),
                        'asunto' => 'Estado Reserva ' . h($nombre), 'altbody' => h($text), 'codigohtml' => h($text), 'mailto' => strtolower($s)], false);
                }
            }
        } else {
            // la primer reserva vigente no era del propietario q está cancelando, cancelo la segunda entonces
            if (isset($reservasVigentes[1]['Amenitiesreserva']['id'])) {
                $this->Amenitiesreserva->cancelar($primera['id']);
            } else {
                return ['e' => 1, 'd' => __('No tiene reservas previas para cancelar')];
            }
        }

        return ['e' => 0, 'd' => __('Su Reserva fue cancelada correctamente') . h($leyenda)];
    }

    private function _valida($link, $propietario_id, $amenitie_id, $amenitiesturno_id, $fecha) {
        // valido q el email sea valido y el id sea numerico
        if (!is_numeric($amenitie_id) || !is_numeric($amenitiesturno_id)) {
            return ['e' => 1, 'd' => __('El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada')];
        }

        $email = $this->Client->Aviso->_decryptURL($link);
        $emails = explode(',', $email);
        if (empty($emails)) {
            return ['e' => 1, 'd' => __('El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada')];
        }
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                return ['e' => 1, 'd' => __('El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada')];
            }
        }

        // valido q el amenity exista y que sea alguno de los habilitados para el consorcio del propietario
        // tambien valido que $propietario_id se encuentre en array_keys($idPropietariosyConsorcios)
        $idPropietariosyConsorcios = [];
        foreach ($emails as $e) {
            $x = $this->Consorcio->Propietario->getPropietarioIdFromEmail($e, 'all'); // obtengo los id de todos los propietarios con ese email
            if (!empty($x)) {
                $idPropietariosyConsorcios += $x;
            }
        }

        if (empty($idPropietariosyConsorcios) || !in_array($propietario_id, array_keys($idPropietariosyConsorcios))) {
            return ['e' => 1, 'd' => __('El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada')];
        }

        // valido amenity existente y habilitada
        $client_id = $this->Consorcio->getConsorcioClientId($idPropietariosyConsorcios[$propietario_id]);
        $amenity = $this->get($amenitie_id, $client_id);
        if (empty($amenity) || $amenity['Amenity']['habilitado'] == 0) {
            return ['e' => 1, 'd' => __('La Amenity es inexistente o se encuentra deshabilitada')];
        }
        // verifico que el turno sea de la amenity 
        if (!$this->Amenitiesturno->elTurnoEsDeLaAmenity($amenitiesturno_id, $amenitie_id)) {
            return ['e' => 1, 'd' => __('El Turno es inexistente o se encuentra deshabilitado')];
        }

        // valido dia, mes y año
        $f1 = explode("/", $fecha);
        $f = $f1[2] . "-" . str_pad($f1[1], 2, "0", STR_PAD_LEFT) . "-" . str_pad($f1[0], 2, "0", STR_PAD_LEFT);
        if (count($f1) != 3 || !$this->validateDate($f, 'Y-m-d')) {
            return ['e' => 1, 'd' => __('El formato de la fecha es incorrecto')];
        }

        // según la configuracion "Días habilitados reserva", valido q sea mayor al dia actual y menor al dia actual+X (x=diashabilitadosreserva)
        $c = $this->Amenitiesconfig->get($amenitie_id);
        if (empty($c)) {
            return ['e' => 1, 'd' => __('La Amenity no posee configuración')];
        }
        $config = $c['Amenitiesconfig'];
        if (!($config['diashabilitadosreserva'] > 0 && strtotime($f) >= strtotime(date("Y-m-d")) && strtotime($f) <= strtotime(date("Y-m-d", strtotime("+" . $config['diashabilitadosreserva'] . " days"))))) {
            return ['e' => 1, 'd' => __('No se puede reservar en d&iacute;as no habilitados')];
        }
        return [];
    }

    public function beforeSave($options = []) {
        $this->data['Amenity']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        if (!isset($this->data['Amenity']['habilitado'])) {
            $this->data['Amenity']['habilitado'] = 0; // al crear, por defecto deshabilitada
        }
        return true;
    }

    public function afterSave($created, $options = []) {
        if ($created) {
            $this->Amenitiesconfig->create();
            $this->Amenitiesconfig->save(['amenitie_id' => $this->data['Amenity']['id'], 'maxreservasporpropietario' => 0, 'diashabilitadosreserva' => 90, 'reservacondicional' => 0, 'seleccionarquienrealizalimpieza' => 0, 'diashabilitadoscancelacion' => 2]);
        }
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Amenity.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
