<?php

App::uses('AppModel', 'Model');

class Propietario extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(Propietario.name, " - ", Propietario.unidad, " (",Propietario.code,")")'];
    public $validate = array(
        'consorcio_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'code' => array(
            'numeric' => array(
                'rule' => array('naturalNumber', false),
                'message' => 'El numero debe ser mayor a cero',
            ),
            'range' => array(
                'rule' => array('range', 0, 10000),
                'message' => 'Debe ser un numero entre 1 y 9999',
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'El campo debe ser unico para el consorcio actual',
                'on' => 'create',
            ),
            'unosolo' => array(
                'rule' => array('checkUnique2'),
                'message' => 'El campo debe ser unico para el consorcio actual',
                'on' => 'update',
            ),
        ),
        'orden' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'El numero debe ser mayor o igual a cero',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'address' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'postal_address' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'city' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'postal_city' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'unidad' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'reemplazar' => array(
                'rule' => ['reemplazar'],
            ),
        ),
        'imprime_resumen_cuenta' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'sistema_online' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'enabled' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'email' => array(
            'maildir' => array(
                'rule' => ['checkEmails'],
                'message' => 'El formato del email es incorrecto. Ej: juan@gmail.com. Si desea agregar mas de un email, separelos con coma y sin espacios. Ej: juan@gmail.com,pepe@hotmail.com',
                'allowEmpty' => true,
            ),
        ),
        'cuit' => [
            'escuit' => [
                'rule' => '/^[0-9]{2}-[0-9]{8}-[0-9]$/',
                'message' => 'El formato del CUIT es incorrecto. EJ: 20-30799986-3',
                'allowEmpty' => true,
            ],
            'validarCuit' => [
                'rule' => ['validarCuit'],
                'message' => "El CUIT no es correcto, verifique el mismo por favor",
            ]
        ],
        'whatsapp' => array(
            'validarWhatsapp' => array(
                'rule' => ['validarWhatsapp'],
                'message' => "El formato es incorrecto: ingrese solo numeros sin espacio y anteceda el codigo de pais (549 para Argentina). Si es mas de uno, separe con coma. Ej: 5492234111111,549117257482",
                'allowEmpty' => true
            ),
        ),
    );
    public $belongsTo = array(
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasOne = array(
        'Aviso' => array(
            'className' => 'Aviso',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = array(
        'GastosParticulare' => array(
            'className' => 'GastosParticulare',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'SaldosCierre' => array(
            'className' => 'SaldosCierre',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'SaldosIniciale' => array(
            'className' => 'SaldosIniciale',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Cobranza' => array(
            'className' => 'Cobranza',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Ajuste' => array(
            'className' => 'Ajuste',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Reparacione' => array(
            'className' => 'Reparacione',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Carta' => array(
            'className' => 'Carta',
            'foreignKey' => 'propietario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Pagoselectronico' => array(
            'className' => 'Pagoselectronico',
            'foreignKey' => 'propietario_code', /* dejar para q me deje hacer los finds a traves de Propietario->Pagoselectronico->find(... */
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'GastosParticularesMulta' => [
            'className' => 'GastosParticularesMulta',
            'foreignKey' => 'propietario_id',
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
        'Cartadeudore' => [
            'className' => 'Cartadeudore',
            'foreignKey' => 'propietario_id',
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
    );
    public $hasAndBelongsToMany = array(
        'Coeficiente' => array(
            'className' => 'Coeficiente',
            'joinTable' => 'coeficientes_propietarios',
            'foreignKey' => 'propietario_id',
            'associationForeignKey' => 'coeficiente_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Propietario.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function getPropietarioEmail($propietario_id) {
        $this->id = $propietario_id;
        return $this->field('email');
    }

    public function getPropietarioWhatsapp($propietario_id) {
        $this->id = $propietario_id;
        return $this->field('whatsapp');
    }

    public function getPropietarioConsorcio($propietario_id) {
        $this->id = $propietario_id;
        return $this->field('consorcio_id');
    }

    public function getPropietarioName2($propietario_id) {
        $this->id = $propietario_id;
        return h($this->field('name') . " - " . $this->field('unidad') . " (" . $this->field('code') . ")");
    }

    /*
     * Obtengo el listado de propietarios ordenados por Orden y Codigo (se usa al cerrar la liquidación). Se almacena en la "foto"
     * $fields lo uso si quiero obtener solo algunos campos $fields = ['fields' => ['Propietario.id','Propietario.nombre','Propietario.unidad']]
      Si se pasa el propietarioId obtengo solo ese propietario
     */

    public function getPropietarios($consorcio_id, $fields = [], $propietario_id = null) {
        if (!empty($propietario_id)) {
            $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id, 'Propietario.id' => $propietario_id), 'order' => 'Propietario.orden,Propietario.code') + $fields;
        } else {
            $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id), 'order' => 'Propietario.orden,Propietario.code') + $fields;
        }
        return Hash::combine($this->find('all', $options), '{n}.Propietario.id', '{n}.Propietario');
    }

    // Obtengo el listado de propietarios que no se les exceptua el interes

    public function getPropietariosNoExceptuanInteres($consorcio_id, $fields = []) {
        $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id, 'Propietario.exceptua_interes' => 0), 'order' => 'Propietario.orden,Propietario.code') + $fields;
        return Hash::combine($this->find('all', $options), '{n}.Propietario.id', '{n}.Propietario');
    }

    public function getList($consorcio_id) {
        $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id, 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']),
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']]],
            'order' => 'Propietario.orden,Propietario.code', 'fields' => ['Propietario.id', 'Propietario.name2']);
        return $this->find('list', $options);
    }

    public function getCount($consorcio_id) {
        $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id));
        return $this->find('count', $options);
    }

    /*
     * Utilizada para obtener los propietarios q coincidan con el texto ingresado en consultas propietarios (email, nombre del propietario, unidad)
     */

    public function get($texto = null) {
        if (!empty($texto)) {

            $c = explode(" ", $texto);
            $res = "";
            foreach ($c as $v) {
                $res .= $v . "|";
            }
            $options = ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'OR' => ['Consorcio.name REGEXP' => substr($res, 0, -1), 'Propietario.unidad like' => $texto . '%', 'Propietario.code' => $texto, 'Propietario.name REGEXP' => substr($res, 0, -1)]],
                'recursive' => 1,
                'contain' => ['Consorcio'],
                'fields' => ['Propietario.id', 'Propietario.name', 'Propietario.email', 'Propietario.unidad', 'Propietario.code', 'Consorcio.name'],
                'order' => 'Consorcio.code,Propietario.orden,Propietario.name',
                'limit' => 200];
            $resul = $this->find('all', $options);
            $cad = [];
            foreach ($resul as $k => $v) {// formateo el resultado para
                $cad[] = ['id' => $v['Propietario']['id'], 'text' => $v['Consorcio']['name'] . ' - ' . $v['Propietario']['name'] . ' - ' . $v['Propietario']['unidad'] . ' (' . $v['Propietario']['code'] . ')'];
            }
            return $cad;
        } else {
            return [];
        }
    }

    /*
     * Utilizada al enviar los avisos con link a los Propietarios. No chequeo $_SESSION['Auth']['User']['client_id'] porq lo usa tambien el Admin en Aviso->enviarLink()
     */

    public function getPropietariosLinkData($consorcio_id) {
        $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id, /* 'Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], */ 'Propietario.email !=' => '', 'Propietario.sistema_online' => 1), 'recursive' => 0,
            'fields' => array('Propietario.name as n', 'Propietario.code as c', 'Propietario.email as e', 'Propietario.unidad as u', 'Propietario.miembrodelconsejo as m', 'Propietario.whatsapp as w'), 'order' => 'Propietario.orden');
        return $this->find('all', $options);
    }

    public function getSaldoActualLiquidacion($propietario_id, $liquidations_type_id) {
        $saldoActual = 0;
        $consorcio_id = $this->getPropietarioConsorcio($propietario_id);
        $liquidacionActivaId = $this->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $liquidations_type_id);
        $saldosLiqAnterioraActiva = $this->SaldosCierre->getSaldo($liquidacionActivaId, $propietario_id);

        if (!empty($saldosLiqAnterioraActiva)) {
            $ajustesLiqActiva = $this->Ajuste->getTotalAjustesPorTipodeLiquidacion($liquidacionActivaId, $propietario_id);
            $cobranzasLiqActiva = $this->Cobranza->getTotalCobranzasPorTipodeLiquidacion($liquidacionActivaId, $propietario_id);
            $saldoActual = intval(intval($saldosLiqAnterioraActiva[$propietario_id]['capital'] + $saldosLiqAnterioraActiva[$propietario_id]['interes']) - $ajustesLiqActiva[$liquidations_type_id] - $cobranzasLiqActiva[$liquidations_type_id]);
        }

        return round($saldoActual, 2);
    }

    public function getSaldoActualPorTipoDeLiquidacion($propietario_id) {
        $saldosActuales = [];
        $consorcio_id = $this->getPropietarioConsorcio($propietario_id);

        $tiposliquidacion = $this->Consorcio->Client->LiquidationsType->getLiquidationsTypes($this->Consorcio->getConsorcioClientId($consorcio_id));

        foreach ($tiposliquidacion as $k => $v) {
            $saldosActuales[$k] = 0;
            $liquidacionActivaId = $this->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $k);

            $saldosLiqAnterioraActiva = $this->SaldosCierre->getSaldo($liquidacionActivaId, $propietario_id);

            if (!empty($saldosLiqAnterioraActiva)) {

                $ajustesLiqActiva = $this->Ajuste->getTotalAjustesPorTipodeLiquidacion($liquidacionActivaId, $propietario_id);
                $cobranzasLiqActiva = $this->Cobranza->getTotalCobranzasPorTipodeLiquidacion($liquidacionActivaId, $propietario_id);

                $saldosActuales[$k] = intval(intval($saldosLiqAnterioraActiva[$propietario_id]['capital'] + $saldosLiqAnterioraActiva[$propietario_id]['interes']) - $ajustesLiqActiva[$k] - $cobranzasLiqActiva[$k]);
            }
        }

        return $saldosActuales;
    }

    // Obtiene los saldos de un propietario difereciando capital de interes, y si es la liquidacion activa tambien devuelve ajustes y cobranzas

    public function getSaldoDeLiquidacionCapitalInteres($propietario_id, $liquidationTypeId, $liquidation_id) {
        $saldos = ['capital' => 0, 'interes' => 0];
        $consorcio_id = $this->getPropietarioConsorcio($propietario_id);
        $liquidacionActivaId = $this->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $liquidationTypeId);

        if ($liquidacionActivaId == $liquidation_id) {
            $saldos['ajustes'] = $this->Ajuste->getAjustes($liquidation_id, $propietario_id);
            $saldos['cobranzas'] = $this->Cobranza->totalCobranzas($liquidation_id, $propietario_id);
        } else {
            $saldosLiq = $this->SaldosCierre->getSaldo($liquidation_id, $propietario_id, true);

            $saldos['capital'] = $saldosLiq[$propietario_id]['capital'];
            $saldos['interes'] = $saldosLiq[$propietario_id]['interes'];
        }
        return $saldos;
    }

    // Obtiene la data de la liquidacion donde para los propietarios tengo el saldo remanente, el cual necesito para el reporte propietarios deudores.
    // si se trata de la liquidacion activa obtengo ajustes y cobranzas.

    public function getDataResumeneDeLiquidacion($consorcio_id, $liquidationTypeId, $liquidation_id) {
        $propietarios = $this->getPropietarios($consorcio_id, ['fields' => ['Propietario.id']]);
        $liquidacionActivaId = $this->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $liquidationTypeId);
        $saldos = [];
        if (($liquidacionActivaId == $liquidation_id) || $liquidacionActivaId == 0) {  // $liquidacionActivaId == 0 entonces es liquidacion inicial
            $data = $this->Consorcio->Liquidation->Resumene->getLiquidationData($liquidation_id);

            foreach ($propietarios as $k => $v) {
                $saldos[$k]['ajustes'] = $this->Ajuste->getAjustes($liquidation_id, $k);        // si es inicial pasó el cero
                $saldos[$k]['cobranzas'] = $this->Cobranza->totalCobranzas($liquidation_id, $k);
            }

            $retorno['data'] = $data;
            $retorno['saldos'] = $saldos;
            return $retorno;
        } else {
            $data = $this->Consorcio->Liquidation->Resumene->getLiquidationData($liquidation_id);

            return $data;
        }
    }

    public function getSaldoUltimaExpensa($propietario_id) {
        $saldosUltimaExpensa = [];
        $consorcio_id = $this->getPropietarioConsorcio($propietario_id);

        $tiposliquidacion = $this->Consorcio->Client->LiquidationsType->getLiquidationsTypes($this->Consorcio->getConsorcioClientId($consorcio_id));

        foreach ($tiposliquidacion as $k => $v) {
            $saldosUltimaExpensa[$k] = 0;
            $liquidacionActivaId = $this->Consorcio->Liquidation->getLiquidationActivaId($consorcio_id, $k);
            $saldosLiqAnterioraActiva = $this->SaldosCierre->getSaldo($liquidacionActivaId, $propietario_id);
            if (!empty($saldosLiqAnterioraActiva)) {
                $saldosUltimaExpensa[$k] = $saldosLiqAnterioraActiva[$propietario_id]['gastosgenerales'] ?? 0 + $saldosLiqAnterioraActiva[$propietario_id]['gastosparticulares'] ?? 0;
            }
        }
        return $saldosUltimaExpensa;
    }

    /*
     * Al recibir los reportes de los avisos enviados se ejecuta ('first'), y tambien cuando ingresa un propietario con su link ('all')
     * Puede tener mas de un mail de distintos clientes, asi que busco por "like"
     */

    public function getPropietarioIdFromEmail($email, $which = 'first') {
        if (empty($email)) {
            return 0;
        }
        $options = array('conditions' => array('Propietario.email like' => '%' . $email . '%', 'Propietario.sistema_online' => 1, 'Client.enabled' => 1, 'Consorcio.habilitado' => 1), 'recursive' => -1, 'fields' => ['Propietario.id, Propietario.consorcio_id'],
            'order' => 'Consorcio.client_id,Consorcio.id,Propietario.orden',
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Propietario.consorcio_id=Consorcio.id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]]);
        $r = $this->find($which, $options);
        return (empty($r) ? 0 : (($which == 'first') ? $r['Propietario']['id'] : Hash::combine($r, '{n}.Propietario.id', '{n}.Propietario.consorcio_id')));
    }

    public function getPropietariosId($consorcio_id) {
        $options = ['conditions' => ['Propietario.consorcio_id' => $consorcio_id], 'recursive' => -1, 'fields' => 'Propietario.id'];
        return $this->find('all', $options);
    }

    public function getPropietariosInfo($consorcio_id) {
        $options = array('conditions' => array('Propietario.consorcio_id' => $consorcio_id),
            'recursive' => -1,
            'fields' => ['Propietario.id', 'Propietario.imprime_resumen_cuenta']);
        return Hash::combine($this->find('all', $options), '{n}.Propietario.id', '{n}.Propietario.imprime_resumen_cuenta');
    }

    /*
     * Obtengo las reparaciones de los propietarios seleccionados y las de sus correspondientes consorcios
     */

    public function getReparaciones($consorcios_id, $propietarios_id) {
        $options = ['conditions' => ['OR' => [['Reparacione.consorcio_id' => $consorcios_id, 'Reparacione.propietario_id' => 0], ['Reparacione.propietario_id' => $propietarios_id]]],
            'contain' => ['Propietario', 'Consorcio', 'Reparacionesestado', 'Reparacionesactualizacione.concepto', 'Reparacionesactualizacione.fecha'],
            'fields' => ['Propietario.unidad', 'Consorcio.name', 'Reparacione.concepto', 'Reparacione.created', 'Reparacione.modified', /* 'Reparacione.propietario_id', 'Reparacione.created', 'Reparacione.modified', 'Reparacione.observaciones', */ 'Reparacionesestado.nombre'],
            'joins' => [['table' => 'reparacionesactualizaciones', 'alias' => 'Reparacionesactualizacione', 'type' => 'left', 'conditions' => ['Reparacionesactualizacione.reparacione_id=Reparacione.id']]],
            'order' => 'Reparacione.reparacionesestado_id,Reparacione.created desc,Reparacionesactualizacione.created desc',
            'group' => 'Reparacione.id'];
        return $this->Reparacione->find('all', $options);
    }

    /*
     * Obtengo el id del propietario a partir del código
     */

    public function getPropietarioId($consorcioid, $code) {
        $options = array('conditions' => array('Propietario.code' => $code, 'Propietario.consorcio_id' => $consorcioid), 'recursive' => -1, 'fields' => 'Propietario.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Propietario']['id']);
    }

    public function exceptuaInteres($propId) {
        $options = array('conditions' => array('Propietario.id' => $propId),
            'recursive' => -1,
            'fields' => 'Propietario.exceptua_interes');
        $resul = $this->find('first', $options);
        return ($resul['Propietario']['exceptua_interes'] == 1);
    }

    /*
     * Para el reporte "coeficientes propietarios" (menu consorcios)
     * devuelve la lista de propietarios y los coeficientes de cada uno
     */

    public function listarCoeficientes($consorcio_id) {
        $prop = $this->getPropietarios($consorcio_id);
        $resul = [];
        foreach ($prop as $k => $v) {
            $resul[$k] = Hash::combine($this->getCoeficientePropietario($k), '{n}.CoeficientesPropietario.coeficiente_id', '{n}.CoeficientesPropietario');
        }
        return ['prop' => $prop, 'coef' => $resul];
    }

    public function getCoeficientePropietario($propId, $coefId = null) {
        if (is_null($coefId)) {
            $options = array('conditions' => array('CoeficientesPropietario.propietario_id' => $propId), 'recursive' => -1, 'fields' => array('CoeficientesPropietario.coeficiente_id', 'CoeficientesPropietario.value'));
            return $this->CoeficientesPropietario->find('all', $options);
        } else {
            $options = array('conditions' => array('CoeficientesPropietario.propietario_id' => $propId, 'CoeficientesPropietario.coeficiente_id' => $coefId), 'recursive' => -1, 'fields' => array('CoeficientesPropietario.value'));
            return $this->CoeficientesPropietario->find('first', $options);
        }
    }

    /*
     * A partir del Codigo de barras que identifica al propietario (cliente-consorcio-unidad) (xxxxyyyyzzzz)
     * busca el id del mismo a partir de su "code"
     */

    /* public function buscaCodigoBarras($codigo) {
      $cliente = (int) substr($codigo, 0, 4);
      $consorcio = (int) substr($codigo, 4, 4);
      $propietario = (int) substr($codigo, 8, 4);
      if (strlen($codigo) != 12 || !is_int($cliente) || !is_int($consorcio) || !is_int($propietario)) {
      return [];
      }
      $options = ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Propietario.code' => $propietario, 'Consorcio.code' => $consorcio],
      'recursive' => 0, 'fields' => ['Propietario.id']];
      $resul = $this->find('first', $options);
      if (!empty($resul)) {
      return $resul['Propietario']['id'];
      } else {
      return [];
      }
      } */

    public function beforeSave($options = array()) {
        if (count($this->Consorcio->Client->LiquidationsType->getLiquidationsTypes()) == 0) {
            //SessionComponent::setFlash(__('No se han cargado los Tipos de liquidaciones. Agregue Tipos de liquidación e intente nuevamente.'), 'error', array(), 'otro');
            return false;
        }
        if (isset($this->data['Propietario']['email'])) {
            $this->data['Propietario']['email'] = strtolower($this->data['Propietario']['email']);
        }
        if (isset($this->data['Propietario']['observations'])) {
            $this->data['Propietario']['observations'] = $this->cleanHTML($this->data['Propietario']['observations']);
        }
        return true;
    }

    public function afterSave($created, $options = []) {
        if ($created) {
            // creo los saldos iniciales del propietario para todos los tipos de liquidaciones del consorcio
            $this->creaSaldosIniciales($this->data['Propietario']['id']);

            // verifico si existen Coeficientes para el consorcio actual. En ese caso, creó los coeficientes primero y luego agrego + propietarios
            // tengo q generar los coeficientes para el propietario actual
            $coefs = $this->Consorcio->Coeficiente->find('list', ['conditions' => ['Coeficiente.consorcio_id' => $this->data['Propietario']['consorcio_id']]]);
            if (!empty($coefs)) {
                foreach ($coefs as $k => $v) {
                    $this->Consorcio->Coeficiente->CoeficientesPropietario->create();
                    $this->Consorcio->Coeficiente->CoeficientesPropietario->save(['coeficiente_id' => $k, 'propietario_id' => $this->data['Propietario']['id'], 'value' => 0], ['callbacks' => false, 'validate' => false]);
                }
            }
        }
    }

    /*
     * Si se agregan propietarios automaticamente, se usa esta funcion para crear los saldos iniciales de los consorcios
     */

    public function creaSaldosIniciales($propietario, $client_id = null) {
        // creo los saldos iniciales del propietario para todos los tipos de liquidaciones del consorcio
        $tipos = $this->Consorcio->Client->LiquidationsType->getLiquidationsTypes($client_id);
        if (count($tipos) > 0) {
            foreach ($tipos as $l => $w) {
                $saldo = $this->SaldosIniciale->find('first', ['conditions' => ['liquidations_type_id' => $l, 'propietario_id' => $propietario], 'fields' => 'SaldosIniciale.id']);
                if (empty($saldo)) {
                    $this->SaldosIniciale->create();
                    $d = ['liquidations_type_id' => $l, 'propietario_id' => $propietario, 'capital' => 0, 'interes' => 0];
                    $this->SaldosIniciale->save($d);
                }
            }
        }
    }

    public function checkUnique($check) {
        if (!isset($this->data['Propietario']['consorcio_id'])) {
            // es un edit
            $options = array('conditions' => array('Propietario.id' => $this->data['Propietario']['id']),
                'recursive' => -1,
                'fields' => 'Propietario.consorcio_id');
            $resul = $this->find('first', $options);
            if (!empty($resul)) {
                $consorcio_id = $resul['Propietario']['consorcio_id'];
            } else {
                return false; // no existe el propietario seleccionado?
            }
        } else {
            $consorcio_id = $this->data['Propietario']['consorcio_id']; // es un add
        }

        $resul = $this->find('count', array(
            'conditions' => array('Propietario.code' => $check['code'], 'Propietario.consorcio_id' => $consorcio_id),
            'recursive' => -1
        ));
        return ($resul == 0);
    }

    public function checkUnique2($check) {
        if (!isset($this->data['Propietario']['consorcio_id'])) {
            // es un edit
            $options = array('conditions' => array('Propietario.id' => $this->data['Propietario']['id']),
                'recursive' => -1,
                'fields' => 'Propietario.consorcio_id');
            $resul = $this->find('first', $options);
            if (!empty($resul)) {
                $consorcio_id = $resul['Propietario']['consorcio_id'];
            } else {
                return false; // no existe el propietario seleccionado?
            }
        } else {
            $consorcio_id = $this->data['Propietario']['consorcio_id']; // es un add
        }
        $resul = $this->find('count', array(
            'conditions' => array('Propietario.code' => $check['code'], 'Propietario.consorcio_id' => $consorcio_id, 'Propietario.id !=' => $this->data['Propietario']['id']),
            'recursive' => -1
        ));
        return ($resul == 0);
    }

    public function validarWhatsapp($check) {
        if (empty($this->data[$this->alias]['whatsapp'])) {
            return true;
        }
        $numeros = explode(",", $this->data[$this->alias]['whatsapp']);
        $error = false;
        foreach ($numeros as $k => $v) {
            if (!(ctype_digit($v) && $v > 10000000 && $v < 10000000000000000)) {
                $error = true;
                break;
            }
        }

        return (count($numeros) != 0 && !$error);
    }

    /*
     * En la unidad reemplazo el caracter ° por º (no son iguales!), sino cuando busco 12ºJ no me aparece en la busqueda
     */

    public function reemplazar($check) {
        $this->data['Propietario']['unidad'] = str_replace('°', 'º', $this->data['Propietario']['unidad']);
        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        $buscar = trim(preg_replace('!\s+!', ' ', $data['buscar']));
        $or = ['Propietario.unidad LIKE' => '%' . $buscar . '%', 'Propietario.email LIKE' => '%' . $buscar . '%', 'Propietario.whatsapp LIKE' => '%' . $buscar . '%', 'Consorcio.name LIKE' => '%' . $buscar . '%'];

        $cad = explode(" ", $buscar);
        $res = "";
        foreach ($cad as $v) {
            $res .= preg_replace("/[^A-Za-z0-9 @.]/", '', $v) . "|";
        }
        return ['OR' => $or + (!empty($res) ? ['Propietario.name REGEXP' => substr($res, 0, -1)] : [])];
    }

}
