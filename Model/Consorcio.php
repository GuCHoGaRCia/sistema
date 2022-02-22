<?php

App::uses('AppModel', 'Model');

class Consorcio extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(Client.name, " - ", Consorcio.name)'];
    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'code' => array(
            'numeric' => array(
                'rule' => array('naturalNumber', false),
                'message' => 'El campo debe ser mayor a cero',
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'El campo debe ser unico para el cliente actual',
                'on' => 'create',
            ),
            'unosolo' => array(
                'rule' => array('checkUnique2'),
                'message' => 'El campo debe ser unico para el cliente actual',
                'on' => 'update',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cuit' => array(
            'escuit' => [
                'rule' => '/^[0-9]{2}-[0-9]{8}-[0-9]$/',
                'message' => 'El formato del CUIT es incorrecto. EJ: 20-30799986-3',
                'allowEmpty' => true,
            ],
            'validarCuit' => [
                'rule' => ['validarCuit'],
                'message' => "El CUIT no es correcto, verifique el mismo por favor",
            ]
        ),
        'address' => array(
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
        'telephone' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'interes' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal',
            ),
        ),
        'valordesdereportepropdeudor' => array(
            'decimal' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Debe ser un valor mayor a cero',
            ),
        )
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasOne = array(
        'Bancoscuenta' => array(
            'className' => 'Bancoscuenta',
            'foreignKey' => 'consorcio_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = array(
        'Coeficiente' => array(
            'className' => 'Coeficiente',
            'foreignKey' => 'consorcio_id',
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
        'Cuentasgastosparticulare' => array(
            'className' => 'Cuentasgastosparticulare',
            'foreignKey' => 'consorcio_id',
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
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'consorcio_id',
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
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'consorcio_id',
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
        'Rubro' => array(
            'className' => 'Rubro',
            'foreignKey' => 'consorcio_id',
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
            'foreignKey' => 'consorcio_id',
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
            'foreignKey' => 'consorcio_id',
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
        'GastosDistribucione' => array(
            'className' => 'GastosDistribucione',
            'foreignKey' => 'consorcio_id',
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
        'SaldosInicialesConsorcio' => array(
            'className' => 'SaldosInicialesConsorcio',
            'foreignKey' => 'consorcio_id',
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
        'Comunicacionesdetalle' => array(
            'className' => 'Comunicacionesdetalle',
            'foreignKey' => 'consorcio_id',
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
        'Cajasingreso' => array(
            'className' => 'Cajasingreso',
            'foreignKey' => 'consorcio_id',
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
        'Cajasegreso' => array(
            'className' => 'Cajasegreso',
            'foreignKey' => 'consorcio_id',
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
        'Bancosextraccione' => array(
            'className' => 'Bancosextraccione',
            'foreignKey' => 'consorcio_id',
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
        'Administracionefectivosdetalle' => array(
            'className' => 'Administracionefectivosdetalle',
            'foreignKey' => 'consorcio_id',
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
        'Amenity' => array(
            'className' => 'Amenity',
            'foreignKey' => 'consorcio_id',
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
        'Contasientosconfig' => array(
            'className' => 'Contasientosconfig',
            'foreignKey' => 'consorcio_id',
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
        'Consorciosconfiguration' => array(
            'className' => 'Consorciosconfiguration',
            'foreignKey' => 'consorcio_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function sumCoeficientes($propietarios) {
        list($coefs, $total) = [$this->getCoeficientes(), []];
        foreach ($propietarios as $k => $prop) {
            foreach ($coefs as $v => $c) {
                $val = $this->Propietario->getCoeficientePropietario($k, $v);
                // le pongo @ sino no funciona el redir en desarrollo
                @$total[$v] += $val["CoeficientesPropietario"]["value"];
            }
        }

        $info = "";
        return $info;
    }

    /*
     * Obtiene los id de los coeficientes habilitados del consorcio
     */

    public function getCoeficientes() {
        $options = array('conditions' => array('Coeficiente.consorcio_id' => $this->id, 'Coeficiente.enabled' => 1), 'fields' => array('Coeficiente.id'));
        return Hash::combine($this->Coeficiente->find('all', $options), '{n}.Coeficiente.id', '{n}.Coeficiente');
    }

    /*
     * Obtiene los id de los coeficientes habilitados del consorcio
     */

    public function listarCoeficientes($consorcio_id) {
        $options = array('conditions' => array('Coeficiente.consorcio_id' => $consorcio_id, 'Coeficiente.enabled' => 1), 'fields' => array('Coeficiente.id', 'Coeficiente.name'));
        return $this->Coeficiente->find('list', $options);
    }

    /*
     * Obtiene los consorcios del cliente actual
     */

    public function getConsorciosList($client = null) {
        $options = ['conditions' => ['Consorcio.client_id' => !empty($client) ? $client : $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1], 'fields' => ['Consorcio.id', 'Consorcio.name'], 'order' => 'Consorcio.code'];
        return $this->find('list', $options);
    }

    /*
     * Obtengo el id del consorcio a partir del código
     */

    public function getConsorcioId($clientid, $code) {
        $options = array('conditions' => array('Consorcio.code' => $code, 'Consorcio.client_id' => $clientid), 'fields' => 'Consorcio.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['Consorcio']['id']);
    }

    /*
     * Obtiene la cuenta de Gastos Particulares utilizada por defecto para la carga automatica de Envios Postales al Propietario
     */

    public function getCGPDefecto($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('cuentagastosparticularesdefecto');
    }

    public function isHabilitado($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('habilitado');
    }

    /*
     * Obtiene la cuenta de Gastos Particulares utilizada por defecto para las Multas
     */

    public function getCGPDefectoMulta($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('cuentagpmulta');
    }

    public function getCGPDefectoMultaCapital($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('cuentagpmultacapital');
    }

    public function getInteresMulta($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('interesmulta');
    }

    public function getInteresMultaCapital($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('interesmultacapital');
    }

    /*
     * Obtiene la cuenta de Gastos Particulares utilizada por defecto para los Pagos Fuera de Término
     */

    public function getCGPDefectoPFT($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('cuentagppft');
    }

    public function getConsorcioCode($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('code');
    }

    public function getConsorcioName($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('name');
    }

    public function is2Cuotas($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('2_cuotas');
    }

    /*
     * Obtengo el id del consorcio a partir del c�digo
     */

    public function getInteres($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('interes');
    }

    public function getConsorcioClientId($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('client_id');
    }

    public function getValorDesdeReportePropDeudor($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('valordesdereportepropdeudor');
    }

    /*
     * Devuelve la info del cliente de un consorcio
     */

    public function getConsorcioClienteInfo($consorcio_id) {
        return $this->Client->find('first', ['conditions' => ['Client.id' => $this->getConsorcioClientId($consorcio_id)]]);
    }

    /*
     * Verifica si el Consorcio prorratea los Gastos Generales en la liquidacion. Ej: en 13 de julio se prorratean montos fijos pero se quiere generar el resumen de gastos informativamente
     */

    public function prorrateaGastosGenerales($consorcio_id) {
        $this->id = $consorcio_id;
        return $this->field('prorrateagastosgenerales');
    }

    /*
     * Valida que el c�digo de consorcio sea �nico para el cliente actual
     */

    public function checkUnique($check) {
        $client_id = (($_SESSION['Auth']['User']['is_admin'] == 0) ? $_SESSION['Auth']['User']['client_id'] : $this->find('first', ['conditions' => ['Consorcio.id' => $this->data['Consorcio']['id']], 'fields' => 'Consorcio.client_id'])['Consorcio']['client_id']);
        $resul = $this->find('count', array(
            'conditions' => array('Consorcio.code' => $check['code'], 'Consorcio.client_id' => $client_id)
        ));
        return ($resul == 0);
    }

    public function checkUnique2($check) {
        $client_id = (($_SESSION['Auth']['User']['is_admin'] == 0) ? $_SESSION['Auth']['User']['client_id'] : $this->find('first', ['conditions' => ['Consorcio.id' => $this->data['Consorcio']['id']], 'fields' => 'Consorcio.client_id'])['Consorcio']['client_id']);
        $resul = $this->find('count', array(
            'conditions' => array('Consorcio.code' => $check['code'], 'Consorcio.client_id' => $client_id, 'Consorcio.id !=' => $this->data['Consorcio']['id']),
        ));
        return ($resul == 0);
    }

    /*
     * Si no es admin le establezco el client_id por el del cliente
     * Si es admin elije el cliente, por eso no se necesitar�a
     */

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['Consorcio']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }
        if (isset($this->data['Consorcio']['description'])) {
            $this->data['Consorcio']['description'] = $this->cleanHTML($this->data['Consorcio']['description']);
        }

        return true;
    }

    /*
     * Crea una liquidacion (oculta) para todos los tipos de liq del consorcio (para poder cargar cobranzas del saldo inicial).
     * Si un nuevo cliente quiere cargar una cobranza sin haber creado/cerrado ninguna liquidacion (y sin tocar el saldo inicial), 
     * puede cargar las cobranzas en la liquidacion 'Saldo inicial <Consorcio> <TipoLiquidacion>', de esta forma se toma en cuenta
     * el saldo inicial y las cobranzas para el calculo del saldo_cierre de la primer liquidacion creada por el cliente
     * El campo "inicial" se utiliza para filtrar las liquidaciones en el index (se muestran todas las del cliente menos las iniciales), pero
     * estas se muestran en "Agregar cobranzas"
     * Creo tambien los saldos iniciales del consorcio
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            $tipos = $this->Client->LiquidationsType->getLiquidationsTypes($_SESSION['Auth']['User']['client_id'], true);
            foreach ($tipos as $k => $v) {
                $this->Liquidation->create();
                $d = array('liquidations_type_id' => $k, 'consorcio_id' => $this->data['Consorcio']['id'], 'name' => 'Saldo inicial ' . $this->data['Consorcio']['name'] . " (" . $v . ")",
                    'periodo' => 'Saldo inicial ' . $this->data['Consorcio']['name'] . " (" . $v . ")", 'description' => 'SI', 'inicial' => 1, 'vencimiento' => date('Y-m-d'), 'limite' => date('Y-m-d'), 'closed' => date('Y-m-d H:i:s'));
                $this->Liquidation->save($d, array('callbacks' => false, 'validate' => false));

                //$consorcios = $this->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']]]);
                //foreach ($consorcios as $s => $t) {
                $this->SaldosInicialesConsorcio->create();
                $this->SaldosInicialesConsorcio->save(['consorcio_id' => $this->data['Consorcio']['id'], 'liquidations_type_id' => $k, 'saldo' => 0]);
                //}
                // creo la configuracion x defecto del consorcio
                $this->Consorciosconfiguration->create();
                $this->Consorciosconfiguration->save(['consorcio_id' => $this->data['Consorcio']['id'], 'liquidations_type_id' => $k, 'enviaraviso' => 0, 'reportarsaldo' => 0, 'onlinerc' => 0, 'onlinerg' => 0, 'onlinecs' => 0, 'imprimerc' => 0, 'imprimerg' => 0, 'imprimecs' => 0]);
            }
        }
    }

    /*
     * Si se agregan consorcios automaticamente, usar esta funcion para crear las liquidaciones iniciales asociadas y los presupuestos
     */

    public function creaLiquidacionesIniciales($consorcio) {
        ini_set('max_execution_time', '10000');
        $client_id = $this->getConsorcioClientId($consorcio['Consorcio']['id']);
        $tipos = $this->Client->LiquidationsType->getLiquidationsTypes($client_id, true);
        //$this->Propietario->SaldosIniciale->verificaSaldos($consorcio['Consorcio']['id']); // creo los saldos iniciales si no existen
        foreach ($tipos as $k => $v) {
            // verifico si ya fueron creadas, en caso contrario las creo
            $resul = $this->Liquidation->find('first', ['conditions' => ['liquidations_type_id' => $k, 'consorcio_id' => $consorcio['Consorcio']['id']/* , 'inicial' => 1 */], 'fields' => 'Liquidation.id']);
            if (empty($resul)) {
                $this->Liquidation->create();
                $d = array('liquidations_type_id' => $k, 'consorcio_id' => $consorcio['Consorcio']['id'], 'name' => 'Saldo inicial ' . $consorcio['Consorcio']['name'] . " (" . $v . ")",
                    'periodo' => 'Saldo inicial ' . $consorcio['Consorcio']['name'] . " (" . $v . ")", 'description' => 'SI', 'inicial' => 1, 'vencimiento' => date('Y-m-d'), 'limite' => date('Y-m-d'), 'closed' => date('Y-m-d H:i:s'));
                $this->Liquidation->save($d, array('callbacks' => false, 'validate' => false));
            }
        }
        // agrego los presupuestos para el coeficiente y cada una de las liquidaciones iniciales
        $coeficientes = $this->Coeficiente->find('all', array('conditions' => array('Coeficiente.consorcio_id' => $consorcio['Consorcio']['id']), 'fields' => ['Coeficiente.id']));
        foreach ($coeficientes as $h => $g) {
            $l = $this->Liquidation->getLiquidationsIniciales($consorcio['Consorcio']['id']);
            foreach ($l as $v) {
                $resul = $this->Liquidation->Liquidationspresupuesto->find('first', ['conditions' => ['liquidation_id' => $v['Liquidation']['id'], 'coeficiente_id' => $g['Coeficiente']['id']], 'fields' => 'Liquidationspresupuesto.id']);
                if (empty($resul)) {
                    $this->Liquidation->Liquidationspresupuesto->create();
                    $this->Liquidation->Liquidationspresupuesto->save(array('liquidation_id' => $v['Liquidation']['id'], 'coeficiente_id' => $g['Coeficiente']['id'], 'total' => 0), array('callbacks' => false, 'validate' => false));
                }
            }
        }

        // agrego los saldos iniciales de cada propietario del consorcio
        $propietarios = $this->Propietario->getPropietariosId($consorcio['Consorcio']['id']);
        foreach ($propietarios as $v) {
            $this->Propietario->creaSaldosIniciales($v['Propietario']['id'], $client_id);
        }
    }

    public function rg3369afip($data) {
        $propietarios = $this->Propietario->getPropietarios($data['consorcio'], ['name', 'code', 'unidad', 'orden', 'superficie', 'postal_address', 'postal_city', 'cuit']);
        $prop = [];
        foreach ($propietarios as $v) {
            // para los q superen la superficie, obtengo el total de expensa (Gastos Generales + Particulares)
            $monto = $this->Liquidation->SaldosCierre->getSaldo($data['liquidacion'], $v['id'], true);
            if (!isset($prop[$v['name']])) {
                $prop[$v['name']] = $v;
            }
            $prop[$v['name']]['name'] .= " - " . $v['unidad'] . " (" . $v['code'] . ")";
            if (!isset($prop[$v['name']]['monto'])) {
                $prop[$v['name']]['monto'] = 0;
            }
            if (!isset($prop[$v['name']]['sup'])) {
                $prop[$v['name']]['sup'] = 0;
            }
            $prop[$v['name']]['monto'] += ($monto[$v['id']]['gastosgenerales'] ?? 0) + ($monto[$v['id']]['gastosparticulares'] ?? 0);
            $prop[$v['name']]['sup'] += is_numeric($v['superficie']) ? $v['superficie'] : 0;
        }
        return ['cliente' => $_SESSION['Auth']['User']['Client'], 'consorcio' => $this->find('first', ['conditions' => ['Consorcio.id' => $data['consorcio']], 'recursive' => 0])['Consorcio'],
            'propietarios' => $prop, 'superficie' => $data['superficie'], 'monto' => $data['monto'], 'periodo' => $this->Liquidation->getPeriodo($data['liquidacion'])];
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                $this->alias . '.name LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.cuit LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
