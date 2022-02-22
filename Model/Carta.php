<?php

App::uses('AppModel', 'Model');

class Carta extends AppModel {

    public $validate = array(
            /* 'client_id' => array(
              'numeric' => array(
              'rule' => array('numeric'),
              'message' => 'Debe completar el dato',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ),
              'propietario_id' => array(
              'numeric' => array(
              'rule' => array('numeric'),
              'message' => 'Debe completar el dato',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ), *//*
              'numero' => array(
              'notBlank' => array(
              'rule' => array('notBlank'),
              'message' => 'Debe completar el dato',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ),
              'tipo' => array(
              'numeric' => array(
              'rule' => array('numeric'),
              'message' => 'Debe completar el dato',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ), */
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Cartastipo' => array(
            'className' => 'Cartastipo',
            'foreignKey' => 'cartastipo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    // clientes ceonline
    //'Carta' => array(
    //    (int) 6 => array(
    //        'oblea' => 'GR55555555',
    //        'codigo' => '006600020012'
    //    ),
    //)
    //  // clientes terceros
    //'terc' => '',
    //'Carta' => array(
    //    (int) 1 => array(
    //        'codigo' => 'LOC2',
    //        'tipo' => 'SU',
    //        'consorcio' => '76',
    //        'cliente' => '2',
    //        'oblea' => 'SU991229395'
    //    )
    //)
    public function procesa($r) {
        $data = $result = [];
        $cantcartas = count($r['Carta']);
        foreach ($r['Carta'] as $k => $v) {
            if (!isset(($r['terc']))) { // clientes CEONLINE
                $clientid = $this->Client->getClientId(substr($v['codigo'], 0, 4));
                $consorcioid = $this->Consorcio->getConsorcioId($clientid, substr($v['codigo'], 4, 4));
                $propietarioid = $this->Propietario->getPropietarioId($consorcioid, substr($v['codigo'], 8, 4));
            } else { // clientes TERCEROS
                $clientid = $v['cliente'];
                $consorcioid = $this->Consorcio->getConsorcioId($clientid, $this->Consorcio->getConsorcioCode($v['consorcio']));
                $propietarioid = /* $this->Propietario->getPropietarioId($consorcioid, $v['propietario']); */ 0; // le pongo propietario 0 y uso codigo para guardar el piso-depto
            }
            $data['Carta'][$k]['oblea'] = strtoupper(trim($v['oblea']));
            $data['Carta'][$k]['cartastipo_id'] = $this->Cartastipo->getCartastipoId(trim($v['oblea'], '0123456789')); // le saco a la oblea todos los caracteres num�ricos
            $data['Carta'][$k]['codigo'] = trim($v['codigo']);
            $data['Carta'][$k] += ['client_id' => $clientid, 'consorcio_id' => $consorcioid, 'propietario_id' => $propietarioid];
        }
        $cantbien = 0;

        foreach ($data['Carta'] as $k => $d) {
            if ($this->obleaEnUso($d['oblea'])) {
                $r = $this->Client->find('first', ['conditions' => ['Client.id' => $clientid], 'fields' => ['Client.id', 'Client.name']]);
                $s = $this->Client->Consorcio->find('first', ['conditions' => ['Consorcio.client_id' => $clientid, 'Consorcio.id' => $consorcioid], 'fields' => ['Consorcio.name', 'Consorcio.id']]);
                $result['e'][$k] = "La Oblea " . $d['oblea'] . " (" . h($r['Client']['name']) . " - " . h($s['Consorcio']['name']) . ") ya fue cargada anteriormente<br>";
                continue;
            } else {
                $cantbien += 1;
            }
            $a['Carta'] = $d;
            $this->create();

            $resul = $this->save($a, ['callbacks' => false]);
            if ($d['propietario_id'] != 0) { // solo para las cartas ceonline
                $cid = $resul['Carta']['id'];
                $gpid = $this->Propietario->GastosParticulare->crearGPCarta($d);

                // guardo el id del GP en la carta (por si despues elimino, que me elimine el GP tambien)
                $this->id = $cid;
                $this->saveField('gastosparticulare_id', $gpid);
            }
        }

        if ($cantbien == $cantcartas) {
            $result['e'] = "";
        }
        $result['s'] = $cantbien;
        return $result;
    }

    public function getdatos($codigo) {
        $resul = [];
        if (strlen($codigo) == 12) {// ej: 001000010005 (cliente 0010, consorcio 0001, prop 0005)
            list($cliente, $consorcio, $propietario) = [substr($codigo, 0, 4), substr($codigo, 4, 4), substr($codigo, 8, 4)];
            // obtengo el nombre del cliente
            $r = $this->Client->find('first', ['conditions' => ['Client.code' => $cliente], 'fields' => ['Client.id', 'Client.name']]);

            // obtengo el id y nombre del consorcio
            $s = $this->Client->Consorcio->find('first', ['conditions' => ['Consorcio.client_id' => @$r['Client']['id'], 'Consorcio.code' => $consorcio], 'fields' => ['Consorcio.name', 'Consorcio.id']]);

            // obtengo el nombre del propietario
            $t = $this->Client->Consorcio->Propietario->find('first', ['conditions' => ['Propietario.consorcio_id' => @$s['Consorcio']['id'], 'Propietario.code' => $propietario], 'fields' => 'Propietario.name']);
            //debug($this->getDataSource()->getLog(false, false));die;
            //$resul = 'Cliente: ' . @$r['Client']['name'] . ', Consorcio: ' . @$s['Consorcio']['name'] . ', Propietario: ' . @$t['Propietario']['name'];
            $resul = [@$r['Client']['name'], @$s['Consorcio']['name'], @$t['Propietario']['name']];
        }
        return json_encode($resul);
    }

    public function getBoletaImposicion($data) {
        $f = $this->fecha($data['Carta']['fecha']);
        $cartas = $this->find('all', ['conditions' => ['date(Carta.created)' => $f, 'Client.enabled' => 1],
            'order' => 'Cartastipo.nombre', 'fields' => ['Carta.*', 'Cartastipo.nombre', 'Cartastipo.id', 'Client.es_manekese'], 'recursive' => 0]);
        return $cartas;
    }

    /*
     * Contain !!!! genial
     */

    public function getEnviosDelDia($data, $client_id = null) {
        $f = $this->fecha($data['Carta']['fecha']);
        $cartas = $this->find('all', ['conditions' => ['date(Carta.created)' => $f, 'Client.enabled' => 1] + (!empty($client_id) ? ['Client.id' => $client_id] : []),
            'fields' => ['Client.name', 'Client.address', 'Client.cuit', 'Client.city', 'Client.code', 'Client.email',
                'Carta.*', 'Consorcio.client_id', 'Consorcio.id', 'Consorcio.name', 'Propietario.name', 'Propietario.unidad', 'Propietario.id', 'Propietario.code',
                'Cartastipo.abreviacion', 'Cartastipo.id'],
            'contain' => ['Client', 'Consorcio', 'Propietario', 'Cartastipo'],
            'order' => 'Propietario.name,Carta.codigo'
        ]);
        return $cartas;
    }

    public function getEnviosDelDiafacturadas($data, $client_id = null, $verfacturados = 0) {
        $f = $this->fecha($data['Carta']['fecha']);

        if ($verfacturados === 0) {                             // envios no facturados
            $conditions = ['date(Carta.created) <=' => $f];
        } else {                                                // envios facturados
            $conditions = ['date(Carta.created)' => $f];
        }

        $cartas = $this->find('all', ['conditions' => $conditions + ['Client.enabled' => 1, 'Carta.facturado' => $verfacturados] + (!empty($client_id) ? ['Client.id' => $client_id] : []),
            'fields' => ['Client.name', 'Carta.consorcio_id', 'Carta.facturado', 'Carta.created', 'Consorcio.id', 'Consorcio.name', 'Cartastipo.abreviacion',
                'Cartastipo.id', 'count(Cartastipo.id) as cant'],
            'contain' => ['Client', 'Consorcio', 'Cartastipo'],
            'group' => 'Consorcio.id, date(Carta.created),Cartastipo.id',
            'order' => 'Client.id'
        ]);
        return $cartas;
    }

    /*
     * verifico que la oblea no haya sido utilizada en otra carta (en TODOS los clientes)
     */

    public function obleaEnUso($oblea) {
        if (strtolower($oblea) === 's') {// la simple puedo cargarla muchas veces
            return false;
        }
        $resul = $this->find('first', ['conditions' => ['Carta.oblea' => strtoupper($oblea)], 'fields' => 'Carta.id']);
        return !empty($resul);
    }

    /*
     * Obtiene las cartas cargadas por CEONLINE para cargar sus respectivos gastos particulares
     */

    public function getCartas() {
        $client = (($_SESSION['Auth']['User']['is_admin'] == 0) ? ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']] : []);
        $options = ['conditions' => ['Carta.gastocargado' => 0, $client], 'recursive' => 0, 'fields' => ['Consorcio.name as c', 'Propietario.name as p', 'Propietario.id as pid', 'Cartastipo.nombre as t', 'Carta.codigo as co', 'Carta.created as f', 'Carta.id as cid']];
        return $this->find('all', $options);
    }

    public function guardafacturadas($data) {
        if (isset($data['consorcio']) && !empty($data['consorcio'])) {
            list($f, $check, $consorcio) = [$data['f'], $data['check'], $data['consorcio']];
            if ($this->updateAll(['Carta.user_id' => $_SESSION['Auth']['User']['id'], 'Carta.facturado' => ($check === 'true'), 'Carta.fechafacturacion' => 'now()'], ['date(Carta.created)' => $this->fecha($f), 'Carta.consorcio_id' => $consorcio])) {
                return ($check === 'true' ? '1' : '2');
            }
        }
        return '0';
    }

//array(
//    'carta' => array(
//        (int) 5 => array(
//            'oblea' => 'CU296469252',
//            'codigo' => '002503120897'
//        ),
//        (int) 4 => array(
//            'cliente' => '4',
//            'oblea' => 'CU296469195',
//            'codigo' => '5289'
//        ),
//        (int) 3 => array(
//            'oblea' => 'CU296469249',
//            'codigo' => '154879847514'
//        ),
//        (int) 2 => array(
//            'oblea' => 'CU296469204',
//            'codigo' => '002503120897'
//        ),
//        (int) 1 => array(
//            'oblea' => 'CU296469195',
//            'codigo' => '000400010001'
//        )
//    ),
//    'Carta' => array(
//        'client_id' => '',
//        'numero' => '',
//        'modified' => '2014-12-04 11:16:38',
//        'created' => '2014-12-04 11:16:38'
//    )
//)
    /*
     * Borro el Gasto Particular asociado (si existe) y si la Liquidacion asociada no se encuentra bloqueada
     */
    public function beforeDelete($cascade = true) {
        if ($this->field('gastosparticulare_id') != 0) {
            $gp = $this->Propietario->GastosParticulare->find('first', ['conditions' => ['GastosParticulare.id' => $this->field('gastosparticulare_id')], 'fields' => ['GastosParticulare.id']]);
            if (!empty($gp)) {
                $this->Propietario->GastosParticulare->id = $gp['GastosParticulare']['id'];
                $this->Propietario->GastosParticulare->Liquidation->id = $this->Propietario->GastosParticulare->field('liquidation_id');
                if ($this->Propietario->GastosParticulare->Liquidation->field('bloqueada') == 0) {
                    $this->Propietario->GastosParticulare->delete();
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    public function informarRobo($id) {
        $resul = $this->find('first', ['conditions' => ['Carta.id' => $id], 'contain' => ['Client', 'Propietario.name', 'Consorcio.name']]);
        if (empty($resul)) {
            return null;
        }
        //debug($resul);die;
        $listaemails = explode(', ', $resul['Client']['email']);
        if (count($listaemails) > 0) {
            foreach ($listaemails as $j) {
                if (filter_var($j, FILTER_VALIDATE_EMAIL)) { // verifico q sea un mail valido
                    $this->Client->Avisosqueue->create();
                    $this->Client->Avisosqueue->save(['client_id' => $resul['Client']['id'], 'emailfrom' => 'no-responder@ceonline.com.ar',
                        'razonsocial' => 'CEONLINE', 'asunto' => 'Informe Carta ' . h($resul['Carta']['oblea']) . ' Robada',
                        'altbody' => "Le informamos que la Carta " . h($resul['Carta']['oblea']) . " enviada el dia " . date("d/m/Y", strtotime($resul['Carta']['created'])) . " al Consorcio " . h($resul['Consorcio']['name']) . " y Propietario " . (!empty($resul['Propietario']['name']) ? h($resul['Propietario']['name']) : h($resul['Carta']['codigo'])) . " fue reportada como ROBADA por el Correo. Atte. CEONLINE",
                        'codigohtml' => "Le informamos que la Carta " . h($resul['Carta']['oblea']) . " enviada el dia " . date("d/m/Y", strtotime($resul['Carta']['created'])) . " al Consorcio " . h($resul['Consorcio']['name']) . " y Propietario " . (!empty($resul['Propietario']['name']) ? h($resul['Propietario']['name']) : h($resul['Carta']['codigo'])) . " fue reportada como ROBADA por el Correo. Atte. CEONLINE",
                        'mailto' => $j]);
                }
            }
        }
        $this->saveField('robada', 1);
        return ['cliente' => $resul['Client']['name'], 'carta' => $resul['Carta']['oblea']];
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                'Carta.codigo LIKE' => '%' . $data['buscar'] . '%',
                'Carta.oblea LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
