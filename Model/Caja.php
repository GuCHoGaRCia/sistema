<?php

App::uses('AppModel', 'Model');

class Caja extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(Client.name, " - ", Caja.name)'];
    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Ya existe una caja con este nombre',
            ),
        ),
    );
    public $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'client_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
    public $hasMany = array(
        'Bancosdepositosefectivo' => array(
            'className' => 'Bancosdepositosefectivo',
            'foreignKey' => 'caja_id',
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
            'foreignKey' => 'caja_id',
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
            'foreignKey' => 'caja_id',
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
            'foreignKey' => 'caja_id',
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
        'Cheque' => array(
            'className' => 'Cheque',
            'foreignKey' => 'caja_id',
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
        return !empty($this->find('first', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function beforeSave($options = array()) {
        if (isset($this->data['Caja']['user_id'])) {
            if ($this->find('count', array('conditions' => array('Caja.user_id' => $this->data['Caja']['user_id'], 'Caja.client_id' => $_SESSION['Auth']['User']['client_id']))) != 0) {
                return false;
            }
        }
        $this->User->id = !isset($this->data['Caja']['user_id']) ? $_SESSION['Auth']['User']['id'] : $this->data['Caja']['user_id'];
        $this->data['Caja']['client_id'] = $this->User->field('client_id'); // es el cliente del usuario relacionado. Cuando se crea el cliente, crea los usuarios y las cajas, y le asignaba mal el client_id
        return true;
    }

    /*
     * Guardo la transferencia
     * 
     * array(
     * 'Caja' => array(
     *      'caja_id' => '1',
     *      'destinos' => '4',
     *      'importe' => '3000'
     * ),
     * 'Cheque' => array(
     *      (int) 15 => array(
     * 		'cheque_id' => '15'
     *      ),
     *      (int) 17 => array(
     *          'cheque_id' => '17'
     *      )
     * )
     * 
     */

    public function saveTransferencia($data = null) {
        $r = $data['Caja'];
        $resul = $this->_beforeTransferencia($r);
        if (!empty($resul)) {
            return $resul;
        }
        $egresoasociado = $ingresoasociado = $importecheques = 0;
        $origen = $this->getCajaUsuario($_SESSION['Auth']['User']['id']);
        $data1 = array('bancoscuenta_id' => 0, 'caja_id' => $origen, 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => date('Y-m-d'), 'concepto' => 'TR ' . $this->findById($origen, 'name')['Caja']['name'] . __(" a ") . $this->findById($r['destinos'], 'name')['Caja']['name'], 'importe' => $r['importe'], 'estransferencia' => 1);
        $data2 = array('bancoscuenta_id' => 0, 'movimientoasociado' => $egresoasociado, 'caja_id' => $r['destinos'], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => date('Y-m-d'), 'concepto' => 'TR ' . $this->findById($origen, 'name')['Caja']['name'] . __(" a ") . $this->findById($r['destinos'], 'name')['Caja']['name'], 'importe' => $r['importe'], 'estransferencia' => 1);
        if ($r['importe'] > 0) {
            // hago un egreso de la caja. Se actualiza el saldo ahi mismo
            $egresoasociado = $this->Cajasegreso->crear($data1);

            // hago un ingreso a la caja. Se actualiza el saldo ahi mismo
            $ingresoasociado = $this->Cajasingreso->crear($data2);
        }

        // cambio la caja de todos los cheques
        $idcheques = [];
        if (!empty($data['Cheque'])) {
            foreach ($data['Cheque'] as $k => $v) {
                if ($v['cheque_id'] != 0) {
                    $this->Cheque->id = $v['cheque_id'];
                    $importecheques += $this->Cheque->field('importe');
                    $this->Cheque->transferir($v['cheque_id'], $r['destinos']);
                    $idcheques[] = $v['cheque_id'];
                }
            }
        }
        if ($importecheques > 0) { // si hubo cheques involucrados
            if ($egresoasociado != 0) {
                $data1['id'] = $egresoasociado;
            } else {
                $this->Cajasegreso->create();
            }
            if ($ingresoasociado != 0) {
                $data2['id'] = $ingresoasociado;
            } else {
                $this->Cajasingreso->create();
            }

            // guardo ambos y les asigno el movimiento asociado
            $resul = $this->Cajasegreso->save($data1 + ['cheque' => $importecheques], ['callbacks' => false, 'validate' => false]);
            $egresoasociado = $resul['Cajasegreso']['id'] ?? 0;
            $resul = $this->Cajasingreso->save($data2 + ['cheque' => $importecheques, 'movimientoasociado' => $egresoasociado], ['callbacks' => false, 'validate' => false]);
            $ingresoasociado = $resul['Cajasingreso']['id'] ?? 0;
            $this->Cajasegreso->id = $egresoasociado;
            $this->Cajasegreso->saveField('movimientoasociado', $ingresoasociado);

            if (!empty($idcheques)) {
                foreach ($idcheques as $v) {
                    // guardo el detalle de los cheques involucrados en la transferencia
                    $this->Cajasegreso->Cajastransferenciascheque->create();
                    $this->Cajasegreso->Cajastransferenciascheque->save(['cajasingreso_id' => $ingresoasociado, 'cajasegreso_id' => $egresoasociado, 'cheque_id' => $v]);
                }
            }

            // actualizo el saldo cheque de las cajas
            $this->setSaldo($origen, -$importecheques, 'saldo_cheques');
            $this->setSaldo($r['destinos'], $importecheques, 'saldo_cheques');
        }

        return "";
    }

    private function _beforeTransferencia($r) {
        // controlo q el importe sea positivo
        if (!is_numeric($r['importe']) || $r['importe'] < 0) {
            return __('Debe ser un importe mayor o igual a cero');
        }

        // existe la caja del usuario?
        $caja = $this->getCajaUsuario($_SESSION['Auth']['User']['id']);
        if ($caja == -1) {
            return __('La Caja origen es inexistente');
        }

        // controlo q la caja tenga saldo suficiente
        //        if ($r['importe'] > 0 && !$this->hasSaldo($caja, $r['importe'])) {
        //            return __('La caja no posee saldo suficiente');
        //        }
        // chequeo q los cheques efectivamente no est�n en uso
        if (!empty($data['Cheque'])) {
            foreach ($data['Cheque'] as $k => $v) {
                if ($v['cheque_id'] != 0 && $this->Cheque->isInUse($v['cheque_id'])) {
                    return __('Uno de los cheques a transferir se encuentra en uso');
                }
            }
        }
        return "";
    }

    /*
     * Verifico q no haya movimientos en la caja antes de borrarla
     */

    public function beforeDelete($cascade = true) {
        return !$this->isInUse($this->id);
    }

    /*
     * Me fijo si hay ingresos o egresos, y despues depositos o extracciones
     */

    public function isInUse($caja_id) {
        if ($caja_id == -1) {// si no existe la caja..
            return false;
        }
        $count = $this->Cajasegreso->find('count', array(
                    'conditions' => array('caja_id' => $caja_id)
                )) + $this->Cajasingreso->find('count', array(
                    'conditions' => array('caja_id' => $caja_id)
        ));
        if ($count == 0) {
            $count = $this->Bancosdepositosefectivo->find('count', array(
                        'conditions' => array('caja_id' => $caja_id)
                    )) + $this->Bancosextraccione->find('count', array(
                        'conditions' => array('caja_id' => $caja_id)
            ));
            if ($count == 0) {
                return false;
            }
        }
        return true;
    }

    /*
     * Funcion que obtiene las cajas del cliente actual
     */

    public function getCajas() {
        return $this->find('list', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id']]]);
    }

    /*
     * Obtiene la Caja de la administracion, si existe (sin usuario asociado (user_id=0))
     */

    function getCajaAdm() {
        $r = $this->find('first', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.user_id' => 0], 'fields' => ['id']]);
        return (empty($r) ? 0 : $r['Caja']['id']);
    }

    /*
     * Funcion que obtiene el saldo actual de una caja
     */

    public function getSaldo($id, $field = 'saldo_pesos') {
        $r = $this->find('first', array('conditions' => array('Caja.id' => $id), 'fields' => [$field]));
        return $r['Caja'][$field];
    }

    /*
     * Funcion que actualiza el saldo de un caja, por defecto el saldo en pesos. Si es cheque, $field = 'saldo_cheques'
     */

    public function setSaldo($id, $importe, $field = 'saldo_pesos') {
        $this->id = $id;
        $this->saveField($field, $this->field($field) + $importe);
    }

    /*
     * Funcion que verifica si la caja tiene saldo suficiente para realizar el movimiento
     * 18/06/2018 - La caja permite saldo negativo de ahora en más (por pago a proveedor)
     */

    public function hasSaldo($id, $importe, $field = 'saldo_pesos') {
//        $r = $this->find('first', ['conditions' => ['Caja.id' => $id], 'recursive' => -1, 'fields' => [$field]]);
//        return (bool) (((float) $r['Caja'][$field] - (float) $importe) >= 0);
        return true;
    }

    /*
     * Funcion que obtiene el saldo actual de una caja
     */

    public function getSaldoTotal($liquidations_type_id = null) {
        $total = 0;
        if (!empty($liquidations_type_id)) {
            
        } else {
            foreach ($this->getCajas() as $k => $v) {
                $total += $this->getSaldo($k) + $this->getSaldo($k, 'saldo_cheques');
            }
        }

        return $total;
    }

    /*
     * Devuelve el id de la caja asociada al usuario. Si no existe, es -1
     */

    public function getCajaUsuario($user) {
        $resul = $this->find('first', ['conditions' => ['Caja.user_id' => $user], 'fields' => ['id']]);
        if (empty($resul)) {
            return -1; // no va a existir caja_id (en ingresos, egresos o movimientos bancarios) a esa caja
        } else {
            return $resul['Caja']['id'];
        }
    }

    /*
     * Devuelve los movimientos (ingresos, egresos, transferencias, etc) de la caja $caja_id ordenados por fecha descendente
     */

    public function getMovimientos($caja_id, $desde = null, $hasta = null, $incluye_anulados = false) {
        $cond = $incluye_anulados ? [] : ['anulado' => 0];
        $cond += empty($desde) || $desde == '1' ? ['date(created) >=' => date("Y-m-01")] : ['date(created) >=' => $this->fecha($desde)];
        $cond += empty($hasta) || $hasta == '1' ? ['date(created) <=' => date("Y-m-d")] : ['date(created) <=' => $this->fecha($hasta)];
        $cajasingresos = Hash::combine($this->Cajasingreso->find('all', ['conditions' => ['Cajasingreso.caja_id' => $caja_id, $cond], 'order' => 'Cajasingreso.fecha,Cajasingreso.created,Cajasingreso.id',
                            'joins' => [['table' => 'cobranzacheques', 'alias' => 'Cobranzacheque', 'type' => 'left', 'conditions' => ['Cajasingreso.cobranza_id=Cobranzacheque.cobranza_id']]]]), '{n}.Cajasingreso.id', '{n}.Cajasingreso');
        $detalleingresos = $this->getDetallesIngresos($cajasingresos);
        $cajasegresos = Hash::combine($this->Cajasegreso->find('all', ['conditions' => ['Cajasegreso.caja_id' => $caja_id, $cond], 'order' => 'Cajasegreso.fecha,Cajasegreso.created,Cajasegreso.id']), '{n}.Cajasegreso.id', '{n}.Cajasegreso');
        // si es un Egreso de caja, puede ser pago proveedor (cheque o efectivo) o deposito (cheque o efectivo).
        $cond['Cheque.anulado'] = $incluye_anulados ? [] : ['Cheque.anulado' => 0];
        $cond['Bancosdepositoscheque.anulado'] = $incluye_anulados ? [] : ['anulado' => 0];
        unset($cond['anulado']);
        return Hash::sort(array_merge($cajasingresos, $cajasegresos), '{n}.fecha2', 'asc') + ['di' => $detalleingresos];
    }

    /*
     * Resumen Caja Banco (INCLUYO ANULADOS)
     * Devuelve un resumen de Caja y Banco por consorcio y forma de pago de los movimientos (ingresos, egresos, transferencias, etc) del $consorcio 
     * ordenados por fecha descendente
     * Dejo Hasta=fecha actual porq del banco y caja tengo el saldo al dia de hoy, y para saber el saldo en dias anteriores debo
     * ir restando a partir de ese saldo los movimientos que existan.
     * Si la fecha "hasta" es menor a la actual, busco hasta la actual, hago la cuenta y muestro hasta "hasta"
     */

    public function getMovimientosResumen($consorcio, $desde = null, $hasta = null) {
        $d = (empty($desde) || $desde == '1' ? date("Y-m-01 00:00:00") : $this->fecha($desde) . " 00:00:00");
        $h = empty($hasta) || $hasta == '1' ? date("Y-m-d H:i:s") : $this->fecha($hasta) . " 23:59:59";
        $ingresos = $this->Cajasingreso->getTotalesEfectivoCheque($consorcio, $d, $h, 1);
        $transferencias = $this->User->Bancosdepositosefectivo->getTransferencias($consorcio, $d, $h, 1);
        $ingresostransferenciasinterbancos = $this->User->Bancostransferencia->getIngresosTransferenciasInterbancos($consorcio, $d, $h, 1);
        $egresostransferenciasinterbancos = $this->User->Bancostransferencia->getEgresosTransferenciasInterbancos($consorcio, $d, $h, 1);
        $egresos = $this->Cajasegreso->getTotalesEfectivoCheque($consorcio, $d, $h, 1);
        //debug($egresos);
        //$bancosdepositoscheques = $this->Cajasegreso->getTotalBancosChequesDepositos($consorcio, $d, $h, 1);
        //$bancosdepositosefectivo = $this->Cajasegreso->getTotalEgresosBancosDepositos($consorcio, $h, $h, 1); // deposito efectivo
        $chequesafuturo = $this->User->Chequespropio->getChequesFuturos($consorcio,$hasta);
        $creditos = $this->User->Bancosdepositosefectivo->getCreditos($consorcio, $d, $h, 1);
        $debitos = $this->User->Bancosextraccione->getDebitos($consorcio, $d, $h, 1);
        return Hash::merge($ingresos, $egresos, ['creditos' => $creditos], ['debitos' => $debitos], ['ingresostransferenciasinterbancos' => $ingresostransferenciasinterbancos],
                        ['egresostransferenciasinterbancos' => $egresostransferenciasinterbancos], ['transferencias' => $transferencias], ['chequesafuturo' => $chequesafuturo]/* , ['bancosdepositosefectivo' => $bancosdepositosefectivo] */);
    }

    /*
     * Se utiliza para el Estado disponibilidad y para Calcular la Disponibilidad al prorratear las liquidaciones
     * En ED $incluiranulados es cero
     * Tambien para el Cron diario cron_saldoscajabanco.php
     */

    public function getTotalesMovimientosResumen($consorcio, $desde = null, $hasta = null, $incluiranulados = 0) {
        $ingresosefectivocheque = $this->Cajasingreso->getTotalIngresosEfectivoCheque($consorcio, $desde, $hasta, $incluiranulados);
        $ingresostransferencias = $this->User->Bancosdepositosefectivo->getTotalTransferencias($consorcio, $desde, $hasta, $incluiranulados);
        $ingresosextracciones = $this->Cajasingreso->getTotalExtracciones($consorcio, $desde, $hasta, $incluiranulados);
        $ingresosmanuales = $this->Cajasingreso->getTotalIngresosManuales($consorcio, $desde, $hasta, $incluiranulados);
        $bancosdepositosefectivo = $this->Cajasegreso->getTotalEgresosBancosDepositos($consorcio, $desde, $hasta, $incluiranulados);
        $bancosdepositoscheques = $this->Cajasegreso->getTotalBancosChequesDepositos($consorcio, $desde, $hasta, $incluiranulados);
        $egresosmanuales = $this->Cajasegreso->getTotalEgresosManuales($consorcio, $desde, $hasta, $incluiranulados);
        $egresospagosproveedor = $this->User->Proveedorspago->getTotalPagosProveedor($consorcio, $desde, $hasta, $incluiranulados);
        $ingresostransferenciasinterbancos = $this->User->Bancostransferencia->getTotalIngresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados);
        $egresostransferenciasinterbancos = $this->User->Bancostransferencia->getTotalEgresosTransferenciasInterbancos($consorcio, $desde, $hasta, $incluiranulados);
        $egresospagosacuenta = $this->User->Proveedorspago->getTotalPagosACuenta($consorcio, $desde, $hasta, $incluiranulados);
        $ingresoscreditos = $this->User->Bancosdepositosefectivo->getTotalCreditos($consorcio, $desde, $hasta, $incluiranulados, $incluiranulados);
        $egresosdebitos = $this->User->Bancosextraccione->getTotalDebitos($consorcio, $desde, $hasta, $incluiranulados, $incluiranulados);
        return ['ingresosefectivo' => $ingresosefectivocheque['e'], 'ingresoscheque' => $ingresosefectivocheque['c'], 'ingresostransferencias' => $ingresostransferencias, 'ingresosextracciones' => $ingresosextracciones,
            'ingresosmanuales' => $ingresosmanuales, 'ingresostransferenciasinterbancos' => $ingresostransferenciasinterbancos, 'ingresoscreditos' => $ingresoscreditos,
            'bancosdepositosefectivo' => $bancosdepositosefectivo, 'bancosdepositoscheques' => $bancosdepositoscheques, 'egresosmanuales' => $egresosmanuales,
            'egresospagosproveedorefectivo' => $egresospagosproveedor['e'], 'egresospagosproveedorefectivoadm' => $egresospagosproveedor['eadm'], 'egresospagosproveedorcheque' => $egresospagosproveedor['ch'], 'egresospagosproveedorchequepropio' => $egresospagosproveedor['chp'],
            'egresospagosproveedorchequepropioadm' => $egresospagosproveedor['chpadm'], 'egresospagosproveedortransferencia' => $egresospagosproveedor['t'], 'egresospagosproveedortransferenciaadm' => $egresospagosproveedor['tadm'], 'egresostransferenciasinterbancos' => $egresostransferenciasinterbancos,
            'egresospagosacuenta' => $egresospagosacuenta, 'egresosdebitos' => $egresosdebitos];
    }

    public function getDetallesIngresos($cajasingresos) {
        $detalle = [];
        $cheques = [];
        foreach ($cajasingresos as $v) {
            $find = $this->Client->Cheque->Cobranzacheque->find('all', ['conditions' => ['Cobranzacheque.cobranza_id' => $v['cobranza_id']], 'fields' => ['Cobranzacheque.amount', 'Cobranzacheque.cheque_id']]);
            if (!empty($find)) {
                foreach ($find as $k => $l) {
                    if (!isset($detalle[$v['cobranza_id']])) {
                        $detalle[$v['cobranza_id']] = 0;
                    }
                    if (!isset($detalle[$v['cobranza_id']])) {
                        $cheques[$v['cobranza_id']] = [];
                    }
                    $detalle[$v['cobranza_id']] += $l['Cobranzacheque']['amount'];
                    $cheques[$v['cobranza_id']][] = $l['Cobranzacheque']['cheque_id'];
                }
            }
        }
        return ['detalle' => $detalle, 'cheques' => $cheques];
    }

    /*
     * Valida que el nombre de la caja sea único para el cliente actual
     */

    public function checkUnique($check) {
        $resul = $this->find('count', array(
            'conditions' => array('Caja.name' => $check['name'], 'Caja.client_id' => $_SESSION['Auth']['User']['client_id']),
            'recursive' => -1
        ));
        return ($resul == 0);
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
        ));
    }

}
