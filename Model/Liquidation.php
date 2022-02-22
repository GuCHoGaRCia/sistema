<?php

App::uses('AppModel', 'Model');

class Liquidation extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(Consorcio.name, " - ", LiquidationsType.name," - ", Liquidation.periodo)'];
    public $displayField = 'periodo';
    public $validate = array(
        'consorcio_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'liquidations_type_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'periodo' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'establoqueada' => array(
                'rule' => array('establoqueada'),
                'message' => 'No se puede modificar una liquidacion bloqueada',
                'on' => 'update',
            ),
        ),
        'vencimiento' => array(
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
                'on' => 'create'
            ],
            'verificafecha' => array(
                'rule' => array('checkDates'),
                'message' => 'El vencimiento debe ser menor o igual al limite',
                'on' => 'update',
            ),
            'establoqueada' => array(
                'rule' => array('establoqueada'),
                'message' => 'No se puede modificar una liquidacion bloqueada',
                'on' => 'update',
            ),
        ),
        'limite' => array(
            'date' => [
                'rule' => ['date', 'dmy'],
                'message' => 'El formato debe ser dd/mm/yyyy',
                'on' => 'create'
            ],
            'verificafecha' => array(
                'rule' => array('checkDates'),
                'message' => 'El limite debe ser mayor o igual al vencimiento',
                'on' => 'update',
            ),
            'establoqueada' => array(
                'rule' => array('establoqueada'),
                'message' => 'No se puede modificar una liquidacion bloqueada',
                'on' => 'update',
            ),
        )
    );
    public $belongsTo = array(
        'Consorcio' => array(
            'className' => 'Consorcio',
            'foreignKey' => 'consorcio_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'LiquidationsType' => array(
            'className' => 'LiquidationsType',
            'foreignKey' => 'liquidations_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasOne = array(
        'Nota' => array(
            'className' => 'Nota',
            'foreignKey' => 'liquidation_id',
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
        'Resumene' => array(
            'className' => 'Resumene',
            'foreignKey' => 'liquidation_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
    public $hasMany = array(
        'GastosGenerale' => array(
            'className' => 'GastosGenerale',
            'foreignKey' => 'liquidation_id',
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
        'GastosParticulare' => array(
            'className' => 'GastosParticulare',
            'foreignKey' => 'liquidation_id',
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
            'foreignKey' => 'liquidation_id',
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
        'Liquidationspresupuesto' => array(
            'className' => 'Liquidationspresupuesto',
            'foreignKey' => 'liquidation_id',
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
        'Adjunto' => array(
            'className' => 'Adjunto',
            'foreignKey' => 'liquidation_id',
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
        'Proveedorsfactura' => array(
            'className' => 'Proveedorsfactura',
            'foreignKey' => 'liquidation_id',
            'dependent' => false, //no borro las facturas si borro la liq
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Colaimpresione' => array(
            'className' => 'Colaimpresione',
            'foreignKey' => 'liquidation_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    /*
     * Realiza los controles de los coeficientes (si suman 100% cada uno)
     * @returns "" | string Si devuelve "", esta todo bien, sino es un mensaje indicando el resultado del control
     */

    public function doControls($id) {
        $this->id = $id;
        if ($this->field('bloqueada') == 1) {
            return __("La liquidaci&oacute;n se encuentra bloqueada, no se puede volver a cerrar. En caso de necesitar hacerlo, comun&iacute;quese con nosotros.");
        }

        return $this->Consorcio->sumCoeficientes($this->Consorcio->Propietario->getPropietarios($this->field('consorcio_id')));
    }

    public function prorrateo($liquidation_id) {
        ini_set('max_execution_time', '120');
        $totales = $this->totalesProrrateoPropietario($liquidation_id);
        $cobranzas = $this->LiquidationsType->Cobranza->getCobranzas($liquidation_id);
        $saldosanteriores = $this->SaldosCierre->getSaldo($liquidation_id); // si no existe, busca el saldo inicial
        $consor = $this->getConsorcioId($liquidation_id);
        $coeficientes = $this->Consorcio->Coeficiente->getList($consor);
        $prop = $this->Consorcio->Propietario->getPropietarios($consor);
        $ajustes = $this->Consorcio->Propietario->Ajuste->getAjustes($liquidation_id);
        $remanentes = $this->getSaldosRemanentes($saldosanteriores, $cobranzas, $ajustes);
        $descripcioncoeficientes = Hash::combine($this->Consorcio->Coeficiente->findAllByConsorcioIdAndEnabled($consor, 1, ['id', 'name']), '{n}.Coeficiente.id', '{n}.Coeficiente.name');
        $saldocierre = $this->SaldosCierre->calculaSaldoCierre($totales, $liquidation_id, $remanentes, $saldosanteriores, $cobranzas, $ajustes); // en $saldosanteriores tengo los redondeos del cierre anterior
        $this->SaldosCierre->guardarTodos($saldocierre); // guardo los totales
        $this->id = $liquidation_id;
        $this->saveField('closed', date('Y-m-d H:i:s')); // actualizo la fecha del ultimo cierre de la liquidacion
        $this->saveField('cerrada', 1); // cierro la liquidacion 
        $x = ClassRegistry::init('Plataformasdepago');
        $plataforma = $x->getConfig($this->Consorcio->getConsorcioClientId($consor));
        $datos = ['client' => $this->Consorcio->Client->getClientInfo($liquidation_id), 'liquidation_id' => $liquidation_id, 'totales' => $totales, 'cobranzas' => $cobranzas, 'saldosanteriores' => $saldosanteriores,
            'coeficientes' => $coeficientes, 'prop' => $prop, 'remanentes' => $remanentes, 'ajustes' => $ajustes, 'descripcioncoeficientes' => $descripcioncoeficientes, 'rubrosinfo' => $this->Consorcio->Rubro->getRubrosInfo($consor),
            'gpinfo' => $this->Consorcio->Cuentasgastosparticulare->getCuentasInfo($consor), 'gastosinfo' => $this->GastosGenerale->listarGastosPorCoeficiente($liquidation_id, true), 'facturasdigitales' => $this->GastosGenerale->Proveedorsfactura->getFacturasDigitales($liquidation_id),
            'saldo' => Hash::combine($saldocierre, '{n}.SaldosCierre.propietario_id', '{n}.SaldosCierre'),
            'bancoscuentas' => $this->Consorcio->Bancoscuenta->getBancoNombre($consor),
            'consorcio' => $this->Consorcio->find('first', ['conditions' => ['Consorcio.id' => $consor], 'recursive' => 0])['Consorcio'],
            'plataforma' => $plataforma, 'plataformas' => $x->get()];
        $this->Resumene->guardaProrrateo($datos);
        $disponibilidad = $this->calculaDisponibilidad($liquidation_id);
        $saldocajabanco = $this->calculaSaldoCajaBanco($liquidation_id); // ver si sirve para algooooooooooooooooo!!!!!!!! creo q no 20190610

        $this->save(['id' => $liquidation_id, 'disponibilidad' => $disponibilidad['disponibilidad'], 'disponibilidadpaga' => $disponibilidad['disponibilidadpaga'],
            'saldocajaefectivo' => $saldocajabanco['saldocajaefectivo'], 'saldocajacheque' => $saldocajabanco['saldocajacheque'], 'saldobanco' => $saldocajabanco['saldobanco']]); // cierro la liquidacion (necesito los gastos para ver el total de gastos)
        // si quiero, despues de prorratear propongo la creación de la proxima liquidacion
        //$this->nuevaLiquidacion($liquidation_id, $consor, $this->field('liquidations_type_id'));
    }

    /*
     * Devuelve el consorcio_id a partir de la liquidacion
     * @param integer $liquidation_id Es el id interno de la liquidacion
     * @returns integer $consorcio_id Es el id interno del consorcio
     */

    public function getConsorcioId($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('consorcio_id');
    }

    public function getPeriodo($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('periodo');
    }

    /*
     * Devuelve el liquidations_type_id a partir de la liquidacion
     * @param integer $liquidation_id Es el id interno de la liquidacion
     * @returns integer liquidations_type_id Es el id interno del tipo de liquidacion
     */

    public function getLiquidationsTypeId($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('liquidations_type_id');
    }

    /*
     * Devuelve inicial a partir de la liquidacion
     */

    public function getLiquidationInicial($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('inicial');
    }

    /*
     * Devuelve created a partir de la liquidacion
     * @param integer $liquidation_id Es el id interno de la liquidacion
     * @returns date $created Es la fecha de creacion de la liquidacion
     */

    public function getLiquidationCreatedDate($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('created');
    }

    /*
     * Devuelve closed a partir de la liquidacion
     * @param integer $liquidation_id Es el id interno de la liquidacion
     * @returns date $closed Es la fecha de cierre de la liquidacion
     */

    public function getLiquidationClosedDate($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('closed');
    }

    /*
     * Devuelve el liquidation_id de la ultima liquidacion del mismo tipo a partir de la liquidacion actual
     * Si no existe una liquidacion anterior (es la primera del tipo), devuelve 0
     * @param integer $liquidation_id Es el id interno de la liquidacion
     * @returns integer $liquidation_id Es el id interno de la liquidacion anterior (o cero)
     */

    public function getLastLiquidation($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('liquidation_id');
    }

    /*
     * Obtiene la proxima liquidacion asociada a la actual (normalmente es una sola). 
     * Se usa para verificar si se puede desbloquear una liquidacion de la cola de impresiones (si no tiene cobranzas la siguiente)
     */

    public function getNextLiquidation($liquidation_id) {
        return $this->find('list', ['conditions' => ['Liquidation.liquidation_id' => $liquidation_id]]);
    }

    public function getColaData($liquidation_id) {
        return $this->Resumene->find('first', ['conditions' => ['Resumene.liquidation_id' => $liquidation_id], 'fields' => ['Resumene.data']])['Resumene']['data'];
    }

    /*
     * Dado un rango de fechas, devuelve los id de las liquidaciones bloqueadas en ese rango. 
     * 01/06/2021 Agrego 00:00:00 y 23:59:59 porq sino no me toma las del ultimo dia del mes
     * Se utiliza en la Generación de Asientos Automáticos
     */

    public function getLiquidacionesEnRangoDeFechas($consorcio_id, $inicio, $fin) {
        return $this->find('list', ['conditions' => ['consorcio_id' => $consorcio_id, /* 'bloqueada' => 1, */ 'closed >=' => $inicio . " 00:00:00", 'closed <=' => $fin . " 23:59:59"]]);
    }

    // Dado un rango de fechas, devuelve los id de las liquidaciones en ese rango que no son iniciales
    // Se utiliza para el reporte estado de dsiponibilidad del consorcio

    public function getGruposLiquidacionesPorTipoEnRangoDeFechas($consorcio_id, $desde, $hasta) {
        $condiciones = ['Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.inicial' => 0, 'Liquidation.closed >' => $desde, 'Liquidation.closed <' => $hasta];
        $options = ['conditions' => $condiciones, 'order' => ['Liquidation.bloqueada', 'Liquidation.cerrada', 'Liquidation.closed desc', 'Liquidation.id desc', 'Liquidation.modified desc'], 'fields' => ['Liquidation.id', 'Liquidation.liquidations_type_id']];
        $resul = $this->find('list', $options);
        return empty($resul) ? 0 : $resul;
    }

    // Devuelve una lista de liquidaciones del consorcio cerradas o abiertas (que se hayan prorrateado al menos una vez), y que no sean iniciales
    // Se utiliza para el rango de liquidaciones a elegir, para el reporte estado de disponibilidad del consorcio  

    public function getLiquidationsEdc($consorcio_id) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.inicial' => 0, 'Liquidation.closed !=' => 'null');
        $options = array('conditions' => $condiciones, 'fields' => array('Liquidation.id', 'Liquidation.periodo', 'Liquidation.bloqueada'), 'order' => array('Liquidation.bloqueada DESC', 'Liquidation.cerrada DESC', 'Liquidation.closed', 'Liquidation.id', 'Liquidation.modified'));

        $liquidations = [];
        $liqs = $this->find('list', $options);
        if (!empty($liqs)) {
            $i = 0;
            foreach ($liqs[1] as $k => $v) {
                $i++;
                $liquidations[$i]['liq_id'] = $k;
                $liquidations[$i]['periodo'] = $v;
            }
            if (isset($liqs[0]) && !empty($liqs[0])) {
                foreach ($liqs[0] as $k2 => $v2) {
                    $i++;
                    $liquidations[$i]['liq_id'] = $k2;
                    $liquidations[$i]['periodo'] = $v2;
                    $liquidations[$i]['bloqueada'] = 0;
                }
            }
            return $liquidations;
        } else {
            return 0;
        }
    }

    /*
     * Devuelve una lista de liquidaciones BLOQUEADAS O INICIALES del consorcio y tipo de liquidacion actual (esta creando una nueva, asi q la actual no la va a traer)
     * Si no existe una liquidacion anterior (es la primera del tipo), devuelve 0
     * @param integer $consorcio_id Es el id interno del consorcio
     * @param integer $liquidations_type_id Es el id interno del tipo de liquidacion
     * @returns list $lista Lista de id => liquidaciones
     */

    public function getLiquidations($consorcio_id = null, $liquidations_type_id = null, $abiertas = 1) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'OR' => ['Liquidation.bloqueada' => 1, 'Liquidation.inicial' => $abiertas]);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.created desc'));
        return $this->find('list', $options);
    }

    // Obtiene las liquidaciones de un consorcio segun el tipo de prefijo que estan habilitadas

    public function getLiquidationsSegunPrefijo($consorcio_id, $prefijo = 0) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidations_types.prefijo' => $prefijo, 'Liquidations_types.enabled' => 1);
        $options = array('conditions' => $condiciones,
            'fields' => ['Liquidation.id', 'Liquidation.name', 'Liquidation.periodo', 'Liquidations_types.client_id', 'Liquidations_types.name', 'Liquidation.vencimiento', 'Liquidation.inicial'],
            'joins' => [['table' => 'liquidations_types', 'alias' => 'Liquidations_types', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=Liquidations_types.id']]],
            'order' => array('Liquidation.created desc'));
        return Hash::combine($this->find('all', $options), '{n}.Liquidation.id', '{n}.Liquidation');
    }

    /*
     * Devuelve el ID de la liquidacion mas antigua que se encuentra ACTIVA (sin bloquear), puede pasar q hayan varias sin bloquear
     */

    public function getLiquidationActivaId($consorcio_id = null, $liquidations_type_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'Liquidation.bloqueada' => 0, 'Liquidation.inicial' => 0);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.created'), 'limit' => 1, 'fields' => 'Liquidation.id');
        $id = $this->find('first', $options);
        return empty($id) ? 0 : $id['Liquidation']['id'];
    }

    /*
     * verifico cual es la liquidacion siguiente a la ultima prorrateada (o es la inicial) para saber a cual ponerle el icono de "Prorratear". 
     * Solo se puede prorratear la liquidacion siguiente a la ultima prorrateada
     */

    public function getLiquidationsActivasIds() {
        $activas = [];
        $lt = $this->LiquidationsType->getLiquidationsTypes();
        $c = $this->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0]);
        foreach ($c as $k => $v) {
            foreach ($lt as $r => $s) {
                $act = $this->getLiquidationActivaId($k, $r);
                if ($act != 0) {
                    $activas[] = $act;
                }
            }
        }
        return $activas;
    }

    /*
     * Para mostrar la Cuenta Corriente del Propietario necesito LA ULTIMA SOLAMENTE (NO! Porq sino no muestra las extraordinarias y demás) liquidacion q esté sin bloquear y no sea iniciales (la liquidacion en proceso)
     * para poder tomar de esas las cobranzas y ajustes q no están en SaldosCierre (porq no se cerro nunca todavia)
     * ver SaldosCierre->getSaldosPropietario()
     */

    public function getLiquidacionesAbiertas($consorcio_id, $liquidations_type_id = null) {
        $cond = [];
        if (!is_null($liquidations_type_id)) {
            $cond = ['Liquidation.liquidations_type_id' => $liquidations_type_id];
        }
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.bloqueada' => 0, 'Liquidation.inicial' => 0) + $cond;
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.created asc')/* , 'limit' => 1 */);
        return $this->find('list', $options);
    }

    /*
     * si esta seteada $fecha, obtiene la liq q se encuentre bloqueada o inicial anterior a esa fecha
     */

    public function getUltimaLiquidacion($consorcio_id = null, $liquidations_type_id = null, $fecha = null) {
        $condiciones = ['Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'OR' => ['Liquidation.bloqueada' => 1, 'Liquidation.inicial' => 1]];
        if (!empty($fecha)) {
            $condiciones += ['Liquidation.closed <' => $fecha];
        }
        $options = ['conditions' => $condiciones, 'order' => ['Liquidation.created desc'], 'fields' => ['Liquidation.id']];
        $resul = $this->find('first', $options);
        return empty($resul) ? 0 : $resul['Liquidation']['id'];
    }

    /*
     * Funcion q valida si se puede modificar el saldo inicial (saldos_iniciale/index de un propietario (solamente se puede si no hay liquidaciones bloqueadas del consorcio y tipo de liq del propietario
     */

    public function hasLiquidationsBloqueadas($consorcio_id = null, $liquidations_type_id = null) {
        $resul = $this->find('first', ['conditions' => ['Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'Liquidation.bloqueada' => 1], 'fields' => ['Liquidation.id']]);
        return empty($resul);
    }

    /*
     * Obtiene la fecha de cierre de la ultima liquidacion del consorcio y tipo
     */

    public function getLastBloqueadaClosedDate($consorcio_id = null, $liquidations_type_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'OR' => ['Liquidation.bloqueada' => 1, 'Liquidation.inicial' => 1]);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.closed desc'), 'limit' => 1, 'fields' => ['Liquidation.closed']);
        $resul = $this->find('first', $options);
        if (empty($resul)) {
            return null;
        } else {
            return $resul['Liquidation']['closed'];
        }
    }

    /*
     * Obtiene el id de la ultima liquidacion bloqueada del consorcio
     */

    public function getLastBloqueadaId($consorcio_id = null, $liquidations_type_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'OR' => ['Liquidation.bloqueada' => 1, 'Liquidation.inicial' => 1]);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.closed desc'), 'limit' => 1, 'fields' => ['Liquidation.id']);
        $resul = $this->find('first', $options);
        if (empty($resul)) {
            return null;
        } else {
            return $resul['Liquidation']['id'];
        }
    }

    /*
     * Obtiene la fecha vencimiento de la liquidacion
     */

    public function getLastBloqueadaVencimiento($consorcio_id = null, $liquidations_type_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'OR' => ['Liquidation.bloqueada' => 1, 'Liquidation.inicial' => 1]);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.closed desc'), 'limit' => 1, 'fields' => ['Liquidation.vencimiento']);
        $resul = $this->find('first', $options);
        if (empty($resul)) {
            return null;
        } else {
            return $resul['Liquidation']['vencimiento'];
        }
    }

    /*
     * Para saber en el listado de cobranzas si muestro la flecha para borrar la cobranza o no, dependiendo si la cobranza pertenece a una liquidacion q ya fue bloqueada.
     * En caso de que la cobranza sea de ordinaria y extraordinaria, si una de las dos esta bloqueada, no permito borrar la cobranza (tendrán q hacer un ajuste o algo asi)
     */

    public function getFechaBloqueadaXTipoLiquidacion() {
        $consorcios = $this->Consorcio->getConsorciosList();
        $lt = $this->LiquidationsType->getLiquidationsTypes();
        $resul = [];
        foreach ($lt as $l => $m) {
            $resul[$l] = [];
            foreach ($consorcios as $k => $v) {
                $resul[$l][$k] = strtotime($this->getLastBloqueadaClosedDate($k, $l));
            }
        }
        return $resul;
    }

    /*
     * Devuelve los periodos de las liquidaciones posteriores a la actual (si hubiesen).
     * Se llama al proponer una liq nueva luego de prorratear una
     */

    public function getPeriodos($consorcio_id = null, $liquidations_type_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.liquidations_type_id' => $liquidations_type_id, 'Liquidation.inicial' => 0);
        $options = array('conditions' => $condiciones, 'order' => array('Liquidation.created desc'), 'fields' => ['Liquidation.id', 'Liquidation.periodo'], 'limit' => 3);
        return $this->find('list', $options);
    }

    public function getLastLiquidationsFromConsorcio($consorcios_id) {
        $resul = [];
        if (!empty($consorcios_id)) {
            foreach ($consorcios_id as $k => $v) {
                $condiciones = array('Liquidation.consorcio_id' => $v, 'Liquidation.inicial' => 0, 'Liquidation.cerrada' => 1);
                $options = array('conditions' => $condiciones,
                    'limit' => 7,
                    'fields' => ['Resumene.data', 'Consorcio.id', 'Consorcio.client_id', 'Consorcio.name', 'Consorcio.code', 'Liquidation.id', 'Liquidation.name', 'Liquidation.periodo', 'Liquidation.limite', 'Client.*'],
                    'contain' => ['Adjunto', 'Resumene', 'Consorcio'],
                    'joins' => [['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Client.id=Consorcio.client_id']]],
                    'order' => 'Liquidation.closed desc'); // se muestran 5 liquidaciones en el panel (la 1º es la mas nueva)
                $resul += [$k => $this->find('all', $options)]; // los agrupo por consorcio    
            }
        }
        return $resul;
    }

    public function getLiquidationsIniciales($consorcio_id = null) {
        $condiciones = array('Liquidation.consorcio_id' => $consorcio_id, 'Liquidation.inicial' => 1);
        $options = array('conditions' => $condiciones, 'fields' => ['Liquidation.id']);
        return $this->find('all', $options);
    }

    public function getConsorcioInfo($liquidation_id, $client_id = null) {
        $options = array('conditions' => array('Liquidation.id' => $liquidation_id, 'Consorcio.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id']),
            'fields' => ['Consorcio.*'/* , 'Consorcio.name', 'Consorcio.cuit', 'Consorcio.address', 'Consorcio.city', 'Consorcio.telephone', 'Consorcio.interes', 'Consorcio.imprime_cod_barras', 'Consorcio.2_cuotas', 'Consorcio.prorrateagastosgenerales', 'Consorcio.imprime_cpe' */],
            'contain' => 'Consorcio');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r);
    }

    /*
     * Para cada propietario devuelve la suma de los gastos generales prorrateados por su coeficiente (si prorratea Gastos Generales)
     * y la suma de los gastos particulares 
     * @param integer $liquidation_id es el id interno de la liquidacion
     * @returns array $totales Son los totales del prorrateo para cada propietario
     */

    public function totalesProrrateoPropietario($liquidation_id) {
        $consorcio_id = $this->getConsorcioId($liquidation_id);
        list($totales, $listaGastosPart, $listaIdPropiet) = [[], $this->GastosParticulare->listarGastos($liquidation_id), $this->Consorcio->Propietario->getPropietariosId($consorcio_id)];
        $totalGastosPorCoef = $this->GastosGenerale->sumarGastosPorCoeficiente($liquidation_id);
        if ($this->Consorcio->prorrateaGastosGenerales($consorcio_id)) {
            // el consorcio prorratea gastos generales
            foreach ($listaIdPropiet as $v) {
                $coefs = $this->Consorcio->Propietario->getCoeficientePropietario($v["Propietario"]["id"]);
                foreach ($totalGastosPorCoef as $sgg) {
                    // suma los gastos generales separados por coeficiente
                    $valorCoeficiente = $this->_buscarValorCoeficientePropietario($coefs, $sgg['GastosGeneraleDetalle']['coeficiente_id']);
                    $totales[$v["Propietario"]["id"]]["coefgen"][$sgg["GastosGeneraleDetalle"]["coeficiente_id"]]["val"] = $valorCoeficiente;
                    $totales[$v["Propietario"]["id"]]["coefgen"][$sgg["GastosGeneraleDetalle"]["coeficiente_id"]]["tot"] = round($sgg[0]["total"] * ($valorCoeficiente / 100), 2); // redondeo, porq sino da diferencias de centavos en el redondeo de la composicion y demas
                }
            }
        }

        // IMPORTANTE! Inicializo los coeficientes de Gastos Particulares
        foreach ($listaIdPropiet as $v) {
            $coefs = $this->Consorcio->Propietario->getCoeficientePropietario($v["Propietario"]["id"]);
            foreach ($totalGastosPorCoef as $sgg) {
                $valorCoeficiente = $this->_buscarValorCoeficientePropietario($coefs, $sgg['GastosGeneraleDetalle']['coeficiente_id']);
                $totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosGeneraleDetalle"]["coeficiente_id"]]["val"] = $valorCoeficiente;
            }
        }

        /*
          // obtengo solo los gastos particulares discriminados x cuenta (solo los q no son prorrateables) (para los q usen la composiciongastosparticulares) (no prorratean gastos generales)
          foreach ($listaGastosPart as $k => $sgg) {
          if (!empty($sgg["GastosParticulare"]["propietario_id"])) {
          if (!isset($totales[$sgg["GastosParticulare"]["propietario_id"]]["gp"][$sgg["GastosParticulare"]["cuentasgastosparticulare_id"]])) {
          $totales[$sgg["GastosParticulare"]["propietario_id"]]["gp"][$sgg["GastosParticulare"]["cuentasgastosparticulare_id"]] = 0;
          }
          $totales[$sgg["GastosParticulare"]["propietario_id"]]["gp"][$sgg["GastosParticulare"]["cuentasgastosparticulare_id"]] += (float) $sgg["GastosParticulare"]["amount"];
          }
          }

          // dejo solamente los gastos particulares q no son prorrateables en $gastosparticulares (los prorrateables ya se suman en _prorrateaGastoParticularAPropietarios()
          $gastosparticulares = [];
          foreach ($listaGastosPart as $k => $sgg) {
          if (!empty($sgg["GastosParticulare"]["propietario_id"])) {
          $gastosparticulares[$sgg["GastosParticulare"]["propietario_id"]] = $sgg["GastosParticulare"];
          }
          } */

        foreach ($listaGastosPart as $k => $sgg) {
            if (is_null($sgg["GastosParticulare"]["coeficiente_id"])) {
                // suma el gasto particular del propietario actual
                $totales[$sgg["GastosParticulare"]["propietario_id"]]["tot"] = (!isset($totales[$sgg["GastosParticulare"]["propietario_id"]]["tot"])) ? 0 : $totales[$sgg["GastosParticulare"]["propietario_id"]]["tot"];
                $totales[$sgg["GastosParticulare"]["propietario_id"]]["tot"] += $sgg["GastosParticulare"]["amount"];
                $totales[$sgg["GastosParticulare"]["propietario_id"]]["detalle"][] = ['total' => $sgg["GastosParticulare"]["amount"], 'descripcion' => $sgg["GastosParticulare"]["description"], 'cuenta' => $sgg["GastosParticulare"]['cuentasgastosparticulare_id']];
            } else {
                // suma el gasto particular prorrateado a todos los propietarios
                $totales = $this->_prorrateaGastoParticularAPropietarios($totales, $sgg, $listaIdPropiet);
            }
        }
        return $totales;
    }

    private function _buscarValorCoeficientePropietario($coefs, $coeficiente_id) {
        foreach ($coefs as $v) {
            if ($v["CoeficientesPropietario"]["coeficiente_id"] == $coeficiente_id) {
                return $v["CoeficientesPropietario"]["value"];
            }
        }
        return 0;
    }

    /*
     * Obtiene la disponibilidad final q fue guardada en la liquidacion (con los gastos generales sin pagar)
     */

    public function getDisponibilidad($anterior, $actual = null) {
        $this->id = $anterior;
        if (($this->field('inicial') || $anterior == 0) && !empty($actual)) {
            // saco el saldo de saldos_iniciales_consorcios
            return $this->Consorcio->SaldosInicialesConsorcio->getSaldo($this->getConsorcioId($actual), $this->getLiquidationsTypeId($actual));
        } else {
            return $this->field('disponibilidad');
        }
    }

    /*
     * Obtiene la disponibilidad final q fue guardada en la liquidacion, con Gastos PAGOS
     */

    public function getDisponibilidadPaga($anterior, $actual = null) {
        $this->id = $anterior;
        if (($this->field('inicial') || $anterior == 0) && !empty($actual)) {
            // saco el saldo de saldos_iniciales_consorcios
            //return $this->Consorcio->SaldosInicialesConsorcio->getSaldo($this->getConsorcioId($actual), $this->getLiquidationsTypeId($actual));
            return 0;
        } else {
            return $this->field('disponibilidadpaga');
        }
    }

    /*
     * Devuelve true si la liquidacion es Inicial
     */

    public function esInicial($liquidation_id) {
        $this->id = $liquidation_id;
        return $this->field('inicial');
    }

    /*
     * Calcula la disponibilidad a partir del saldo de la liq anterior mas las cobranzas menos los gastos PAGOS
     */

    public function calculaDisponibilidad($liquidation_id, $eselproceso = false) {
        $liqanterior = $this->getLastLiquidation($liquidation_id);
        $consorcio = $this->getConsorcioId($liquidation_id);
        $ltid = $this->getLiquidationsTypeId($liquidation_id);
        if ($this->esInicial($liqanterior)) {
            // busco el saldo inicial del consorcio
            $disponibilidadanterior = $this->LiquidationsType->SaldosInicialesConsorcio->getSaldo($consorcio, $ltid);
            $disponibilidadanteriorpaga = 0; //$disponibilidadanterior
        } else {
            $disponibilidadanterior = $this->getDisponibilidad($liqanterior);
            $disponibilidadanteriorpaga = $this->getDisponibilidadPaga($liqanterior);
        }

        $d = $this->getLiquidationClosedDate($liqanterior);
        if ($eselproceso) {//es el proceso Client::actualizaEstadoDisponibilidad()
            $h = $this->getLiquidationClosedDate($liquidation_id); // esta bien esta fecha, es la ultima vez q prorratearon!
        } else {
            $h = date("Y-m-d H:i:s"); //siempre es la fecha actual!!! sino, toma la del ultimo prorrateo de la actual y puede q sea de hace un tiempo y se hayan hecho movimientos entre medio de esa fecha y la actual
        }

        // para calcular la disponibilidad de la extraordinaria o fondo
        $prefijo = $this->LiquidationsType->getPrefijo(null, $ltid);
        //ingresosefectivocheque y egresospagosproveedor tienen el detalle desglosado ('e','c',etc)
        $gastospagos = $this->Consorcio->Client->Proveedor->Proveedorspago->getTotalPagosPorLiquidacion($consorcio, $d, $h, $prefijo);
        if ($prefijo == 0) {
            $gastosg = $this->GastosGenerale->calculaTotalesGastos($liquidation_id);
            $resumen = $this->Consorcio->Client->Caja->getTotalesMovimientosResumen($consorcio, $d, $h, 0); // no incluyo anulados!
            $ingresos = $this->Consorcio->Propietario->Cobranza->totalCobranzas($liquidation_id);
            /* $egresos = $resumen['egresospagosproveedorefectivo'] + $resumen['egresospagosproveedorcheque'] + $resumen['egresospagosproveedorchequepropio'] + $resumen['egresospagosproveedortransferencia'] +
              array_sum($resumen['egresospagosacuenta']) + $resumen['egresospagosproveedorefectivoadm'] + $resumen['egresospagosproveedortransferenciaadm'] + $resumen['egresospagosproveedorchequepropioadm']; */
            return ['disponibilidad' => $disponibilidadanterior + $ingresos - $gastosg + array_sum($resumen['ingresosmanuales']) - array_sum($resumen['egresosmanuales']),
                'disponibilidadpaga' => $disponibilidadanteriorpaga + $ingresos - $gastospagos + $resumen['ingresostransferenciasinterbancos'] - $resumen['egresostransferenciasinterbancos'] +
                array_sum($resumen['ingresosmanuales']) - array_sum($resumen['egresosmanuales']) + $resumen['ingresoscreditos'] - $resumen['egresosdebitos'] /* - array_sum($resumen['egresospagosacuenta']) */];
        } else {
            return['disponibilidad' => 0, 'disponibilidadpaga' => $disponibilidadanteriorpaga + $this->Consorcio->Propietario->Cobranza->totalCobranzas($liquidation_id) - $gastospagos];
        }
    }

    /*
     * Al prorratear, calcula los totales de movimientos de caja efectivo, caja cheque y banco
     */

    public function calculaSaldoCajaBanco($liquidation_id) {
        $this->id = $liquidation_id;
        $liqanterior = $this->field('liquidation_id');
        $consorcio = $this->field('consorcio_id');

        $h = $this->field('closed');

        $this->id = $liqanterior;
        $d = $this->field('closed');
        $saldoanteriorcajaefectivo = $this->field('saldocajaefectivo');
        $saldoanteriorcajacheque = $this->field('saldocajacheque');
        $saldoanteriorbanco = $this->field('saldobanco');

        if (is_null($h) || $h == "0000-00-00 00:00:00") {
            $h = date("Y-m-d H:i:s");
        }
        $saldocajaefectivo = $saldocajacheque = $saldobanco = 0;
        $movimientos = $this->Consorcio->Client->Caja->getMovimientosResumen($consorcio, $d, $h, 1);
        if (isset($movimientos['ingresos']['cobranzas']) && count($movimientos['ingresos']['cobranzas']) > 0) {
            foreach ($movimientos['ingresos']['cobranzas'] as $a => $b) {
                $saldocajaefectivo += (float) $b['importe'];
                $saldocajacheque += (float) $b['cheque'];
            }
        }
        if (!empty($movimientos['ingresos']['otros'])) {
            foreach ($movimientos['ingresos']['otros'] as $l) {
                $saldocajaefectivo += $l['Cajasingreso']['importe'];
                $saldocajacheque += $l['Cajasingreso']['cheque'];
            }
        }
        if (!empty($movimientos['ingresos']['extracciones'])) {
            foreach ($movimientos['ingresos']['extracciones'] as $l) {
                $saldocajaefectivo += $l['Cajasingreso']['importe'];
                $saldobanco -= $l['Cajasingreso']['importe']; // las extracciones salen del banco y van a la caja
            }
        }
        if (!empty($movimientos['egresos']['pagosproveedor']['efectivocheque'])) {
            foreach ($movimientos['egresos']['pagosproveedor']['efectivocheque'] as $l) {
                $saldocajaefectivo -= $l['Cajasegreso']['importe'];
                $saldocajacheque -= $l['Cajasegreso']['cheque'];
            }
        }
        if (!empty($movimientos['egresos']['pagosproveedor']['acuenta'])) {
            foreach ($movimientos['egresos']['pagosproveedor']['acuenta'] as $l) {
                $saldocajaefectivo -= $l['Cajasegreso']['importe'];
                $saldocajacheque -= $l['Cajasegreso']['cheque'];
            }
        }
        if (!empty($movimientos['egresos']['otros'])) {
            foreach ($movimientos['egresos']['otros'] as $l) {
                $saldocajaefectivo -= $l['Cajasegreso']['importe'];
            }
        }
        if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
            foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {
                $saldocajaefectivo -= $l['Bancosdepositosefectivo']['importe'];
            }
        }
        if (!empty($movimientos['egresos']['depositos']['cheque'])) {
            foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {
                $saldocajacheque -= $l['Bancosdepositoscheque']['importe'];
            }
        }
        if (!empty($movimientos['ingresos']['transferencias'])) {
            foreach ($movimientos['ingresos']['transferencias'] as $l) {
                $saldobanco += $l['Bancosdepositosefectivo']['importe'];
            }
        }
        if (!empty($movimientos['ingresostransferenciasinterbancos'])) {
            foreach ($movimientos['ingresostransferenciasinterbancos'] as $l) {//muestro el detalle
                $saldobanco += $l['Bancostransferencia']['importe'];
            }
        }
        if (!empty($movimientos['creditos'])) {
            foreach ($movimientos['creditos'] as $l) {//muestro el detalle
                $saldobanco += $l['Bancosdepositosefectivo']['importe'];
            }
        }
        if (!empty($movimientos['egresos']['depositos']['efectivo'])) {
            foreach ($movimientos['egresos']['depositos']['efectivo'] as $l) {//muestro el detalle
                $saldobanco += $l['Bancosdepositosefectivo']['importe'];
            }
        }
        if (!empty($movimientos['egresos']['depositos']['cheque'])) {
            foreach ($movimientos['egresos']['depositos']['cheque'] as $l) {//muestro el detalle
                $saldobanco += $l['Bancosdepositoscheque']['importe'];
            }
        }
        if (!empty($movimientos['debitos'])) {
            foreach ($movimientos['debitos'] as $l) {//muestro el detalle
                $saldobanco -= $l['Bancosextraccione']['importe'];
            }
        }
        if (!empty($movimientos['egresos']['pagosproveedor']['chequepropio'])) {
            foreach ($movimientos['egresos']['pagosproveedor']['chequepropio'] as $l) {
                $saldobanco -= $l['Chequespropio']['importe'] ?? 0;
            }
        }
        if (!empty($movimientos['egresos']['pagosproveedor']['transferencia'])) {
            foreach ($movimientos['egresos']['pagosproveedor']['transferencia'] as $l) {//muestro el detalle
                $saldobanco -= $l['Bancosextraccione']['importe'];
            }
        }
        if (!empty($movimientos['egresostransferenciasinterbancos'])) {
            foreach ($movimientos['egresostransferenciasinterbancos'] as $l) {//muestro el detalle
                $saldobanco -= $l['Bancostransferencia']['importe'];
            }
        }
        return ['saldocajaefectivo' => $saldoanteriorcajaefectivo + $saldocajaefectivo, 'saldocajacheque' => $saldoanteriorcajacheque + $saldocajacheque,
            'saldobanco' => $saldoanteriorbanco + $saldobanco];
    }

    private function _prorrateaGastoParticularAPropietarios($totales, $sgg, $listaIdPropiet) {
        foreach ($listaIdPropiet as $v) {
            $coefs = $this->Consorcio->Propietario->getCoeficientePropietario($v["Propietario"]["id"]);
            $valorCoeficiente = $this->_buscarValorCoeficientePropietario($coefs, $sgg["GastosParticulare"]["coeficiente_id"]);
            $totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosParticulare"]["coeficiente_id"]]["val"] = $valorCoeficiente;
            if (!isset($totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosParticulare"]["coeficiente_id"]]["tot"])) {
                $totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosParticulare"]["coeficiente_id"]]["tot"] = 0;
            }
            $totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosParticulare"]["coeficiente_id"]]["tot"] += round($sgg["GastosParticulare"]["amount"] * ($valorCoeficiente / 100), 2);
            $totales[$v["Propietario"]["id"]]["coefpar"][$sgg["GastosParticulare"]["coeficiente_id"]]['detalle'][] = ['total' => $sgg["GastosParticulare"]["amount"], 'cuenta' => $sgg["GastosParticulare"]['cuentasgastosparticulare_id'], 'descripcion' => $sgg["GastosParticulare"]["description"], 'monto' => $sgg["GastosParticulare"]["amount"] * ($valorCoeficiente / 100)];
        }
        return $totales;
    }

    /*
     * Devuelve los saldos remanentes de todos los propietarios de la liquidacion actual
     * @param array $saldosanteriores Son los saldos anteriores (de la liq anterior o el saldo inicial)
     * @param array $cobranzas son las cobranzas hechas entre el ultimo cierre y la fecha actual
     * @param array $ajustes son los ajustes realizados entre el ultimo cierre y la fecha actual
     * @returns array(capital,interes) de cada propietario
     * array(
     * 	(int) 0 => array(
     *      'SaldosIniciale' => array(
     * 		'liquidations_type_id' => '18',
     * 		'created' => '2015-05-04',
     * 		'propietario_id' => '8',
     * 		'capital' => '-156.36',
     * 		'interes' => '0.00'
     *      )
     * 	),
     * array(
     * 	(int) 0 => array(
     * 		'Cobranza' => array(
     * 			'propietario_id' => '17',
     *       		'amount' => '500.00'
     * 		)
     * 	),
     * 	(int) 1 => array(
     * 		'Cobranza' => array(
     * 			'propietario_id' => '8',
     * 			'amount' => '700.00'
     * 		)
     * 	),
     * )
     */

    public function getSaldosRemanentes($saldosanteriores, $cobranzas, $ajustes) {
        $remanente = [];
        foreach ($saldosanteriores as $v) {
            list($capital, $interes) = [$v['capital'], $v['interes']];
            // sumo las cobranzas
            $keyscobranza = $this->buscaLista($cobranzas, ['propietario_id' => $v['propietario_id']], true);
            $cob = $solocapital = 0;  // el total de cobranzas del propietario
            foreach ($keyscobranza as $c) {
                if ($cobranzas[$c]['Cobranzatipoliquidacione']['solocapital']) {
                    $solocapital += $cobranzas[$c]['Cobranzatipoliquidacione']['amount'];
                } else {
                    $cob += $cobranzas[$c]['Cobranzatipoliquidacione']['amount'];
                }
            }

            // sumo los ajustes
            $keysajustes = $this->buscaLista($ajustes, ['propietario_id' => $v['propietario_id']], true);
            $aj = $ajsolocapital = 0; // el total de ajustes del propietario
            foreach ($keysajustes as $d) {
                if ($ajustes[$d]['Ajustetipoliquidacione']['solocapital']) {
                    $ajsolocapital += $ajustes[$d]['Ajustetipoliquidacione']['amount'];
                } else {
                    $aj += $ajustes[$d]['Ajustetipoliquidacione']['amount'];
                }
            }

            $totalcobranza = $cob + $aj;

            if ($totalcobranza > 0) {
                $auxinteres = $interes;
                // si el interes quedó negativo, lo pongo en cero, sino hago $interes - $totalcobranza
                $interes = ($interes - $totalcobranza < 0) ? 0 : $interes - $totalcobranza;
                $totalcobranza -= $auxinteres;
                // si el totalcobranza es menor a cero no hago nada (porq se pagó parte del interes). Sino hago $capital - $totalcobranza
                // si $totalcobranza > $capital, es pago a cuenta
                $capital = ($totalcobranza > 0) ? $capital - $totalcobranza : $capital;
            }
            if ($solocapital > 0 || $ajsolocapital > 0) {
                // descuento al capital las cobranzas y los ajustes de solo capital
                $capital -= $solocapital + $ajsolocapital;
            }

            $remanente[$v['propietario_id']] = ['capital' => $capital, 'interes' => $interes, 'redondeo' => (isset($v['redondeo']) ? $v['redondeo'] : 0), 'cobranzas' => $cob + $solocapital, 'ajustes' => $aj + $ajsolocapital];
        }
        return $remanente;
    }

    /*
     * Devuelve la cantidad de gastos (generales y particulares) que existen para la liquidacion actual
     */

    public function getGastosCount($liquidation_id) {
        return $this->GastosGenerale->find('count', ['conditions' => ['GastosGenerale.liquidation_id' => $liquidation_id]]) + $this->GastosParticulare->find('count', ['conditions' => ['GastosParticulare.liquidation_id' => $liquidation_id]]);
    }

    /*
     * Verifico que la fecha de vencimiento sea menor o igual a la de limite
     * Si es por ajax, verifico contra la fecha ya almacenada
     */

    public function beforeSave($options = []) {
        if (isset($this->data['Liquidation']['vencimiento']) && isset($this->data['Liquidation']['limite'])) {
            $this->data['Liquidation']['vencimiento'] = $this->fecha($this->data['Liquidation']['vencimiento']);
            $this->data['Liquidation']['limite'] = $this->fecha($this->data['Liquidation']['limite']);
        }
        return true;
    }

    public function afterSave($created, $options = []) {
        if ($created && isset($this->data['Liquidation'])) {
            // creo las notas de la liquidacion nueva usando las de la vieja
            $id = $this->data['Liquidation']['id'];
            $this->Nota->crearNotas($id, $this->data['Liquidation']['liquidation_id']);
            // se crea el presupuesto de la liquidacion creada para cada coeficiente del consorcio (hace saveAll y el form de add ya tiene los campos para el presupuesto)
            // tomo los gastos heredables de la liquidacion anterior y los copio a la nueva
            $this->GastosGenerale->heredar($id, $this->data['Liquidation']['liquidation_id']);
            $this->GastosParticulare->heredar($id, $this->data['Liquidation']['liquidation_id']);
        }
    }

    /*
     * Verifico que el vencimiento sea menor o igual al limite
     */

    public function checkDates($check) {
        if (isset($this->data['Liquidation']['id'])) {
            if (isset($check['vencimiento'])) {
                $fecha = $check['vencimiento'];
                $esvencimiento = true;
            } else {
                $fecha = $check['limite'];
                $esvencimiento = false;
            }
            $this->id = $this->data['Liquidation']['id'];
            $fechaAComparar = $this->field(($esvencimiento ? 'limite' : 'vencimiento'));
            if ($esvencimiento) {
                // vencimiento <= limite
                return (date('Y-m-d', strtotime($fecha)) <= date('Y-m-d', strtotime($fechaAComparar)));
            } else {
                // limite >= vencimiento
                return (date('Y-m-d', strtotime($fecha)) >= date('Y-m-d', strtotime($fechaAComparar)));
            }
        }
        return true;
    }

    /*
     * Verifico q no esté bloqueada
     */

    public function establoqueada($check) {
        $this->id = $this->data['Liquidation']['id'];
        if ($this->field('bloqueada')) {
            return false;
        }
        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }

        return [
            'OR' => [
        'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
        'Liquidation.periodo LIKE' => '%' . $data['buscar'] . '%',
        'Liquidation.description LIKE' => '%' . $data['buscar'] . '%',
        'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
        'LiquidationsType.name LIKE' => '%' . $data['buscar'] . '%',
            ] + ($_SESSION['Auth']['User']['is_admin'] ? ['Client.name LIKE' => '%' . $data['buscar'] . '%'] : [])];
    }

}
