<?php

App::uses('AppModel', 'Model');

class Bancoscuenta extends AppModel {

    public $displayField = 'name';
    public $virtualFields = ['name2' => 'ifnull(CONCAT(Consorcio.name," - ",Bancoscuenta.name),CONCAT("00 - Administración - ",Bancoscuenta.name))'];
    public $validate = array(
        'banco_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'consorcio_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cuenta' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cbu' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'comision_fija_interdeposito' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un numero decimal mayor o igual a cero',
            ),
            'range' => array(
                'rule' => array('range', -0.00001, 99999),
                'message' => 'Debe ser un numero decimal mayor o igual a cero',
            ),
            'checkCGP' => array(
                'rule' => array('checkCGP'),
                'message' => 'Debe configurar una Cuenta de Gastos Particulares primero',
                'on' => 'update',
            ),
        ),
        'comision_variable' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un porcentual entre 0 y 100',
            ),
            'range' => array(
                'rule' => array('range', -0.00001, 100.00001),
                'message' => 'Debe ser un porcentual entre 0 y 100',
            ),
            'checkCGP' => array(
                'rule' => array('checkCGP'),
                'message' => 'Debe configurar una Cuenta de Gastos Particulares primero',
                'on' => 'update',
            ),
        ),
        'defectocobranzaautomatica' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'checkDefaultCA' => array(
                'rule' => array('checkDefaultCA'),
                'message' => 'Ya existe una Cuenta Bancaria configurada por defecto para recibir las Cobranzas Automáticas',
                'on' => 'create',
            ),
        ),
    );
    public $belongsTo = array(
        'Banco' => array(
            'className' => 'Banco',
            'foreignKey' => 'banco_id',
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
        )
    );
    public $hasMany = array(
        'Bancosdepositoscheque' => array(
            'className' => 'Bancosdepositoscheque',
            'foreignKey' => 'bancoscuenta_id',
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
        'Bancosdepositosefectivo' => array(
            'className' => 'Bancosdepositosefectivo',
            'foreignKey' => 'bancoscuenta_id',
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
            'foreignKey' => 'bancoscuenta_id',
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
        'BancostransferenciaOrigen' => array(
            'className' => 'Bancostransferencia',
            'foreignKey' => 'bancoscuenta_id',
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
        'BancostransferenciaDestino' => array(
            'className' => 'Bancostransferencia',
            'foreignKey' => 'destino_id',
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
        'Chequespropio' => array(
            'className' => 'Chequespropio',
            'foreignKey' => 'bancoscuenta_id',
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
        'Chequespropiosadmsdetalle' => array(
            'className' => 'Chequespropiosadmsdetalle',
            'foreignKey' => 'bancoscuenta_id',
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
        'Chequespropiosadm' => array(
            'className' => 'Chequespropiosadm',
            'foreignKey' => 'bancoscuenta_id',
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
        'Administracionefectivo' => [
            'className' => 'Administracionefectivo',
            'foreignKey' => 'bancoscuenta_id',
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
        'Administraciontransferencia' => [
            'className' => 'Administraciontransferencia',
            'foreignKey' => 'bancoscuenta_id',
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
        'Administraciontransferenciasdetalle' => [
            'className' => 'Administraciontransferenciasdetalle',
            'foreignKey' => 'bancoscuenta_id',
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

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $id], 'recursive' => 0, 'fields' => [$this->alias . '.id']]));
    }

    /*
     * Verifico q no haya movimientos en la cuenta antes de borrarla
     */

    public function beforeDelete($cascade = true) {
        $count = $this->Bancosdepositosefectivo->find('count', array(
                    'conditions' => array('bancoscuenta_id' => $this->id)
                )) + $this->Bancosextraccione->find('count', array(
                    'conditions' => array('bancoscuenta_id' => $this->id)
                )) + $this->BancostransferenciaOrigen->find('count', array(
                    'conditions' => array('bancoscuenta_id' => $this->id)
                )) + $this->BancostransferenciaDestino->find('count', array(
                    'conditions' => array('destino_id' => $this->id)
        ));
        if ($count == 0) {
            return true;
        }
        return false;
    }

    /*
     * Función que obtiene las cuentas bancarias del cliente. Dejar el CONTAIN porque name2 lo necesita
     */

    public function get() {
        $r = $this->find('list', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], ['OR' => ['Consorcio.habilitado' => 1, 'Bancoscuenta.consorcio_id' => 0]]], 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.name2'], 'contain' => ['Banco', 'Consorcio'], 'order' => 'Consorcio.code']);
        return $r;
    }

    public function getCuentasAdm() {
        $r = $this->find('list', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => 0], 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.name2'], 'contain' => ['Banco', 'Consorcio'], 'order' => 'Bancoscuenta.name2']);
        return $r;
    }

    /*
     * Para saber de q consorcio es una cta bancaria
     */

    public function getCtaConsor() {
        $r = $this->find('list', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id']], 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.consorcio_id'], 'contain' => ['Banco', 'Consorcio'], 'order' => 'Consorcio.code']);
        return $r;
    }

    /*
     * Función que obtiene la cuenta bancaria del consorcio (la primera) y habilitada
     */

    public function getCuentaBancaria($consorcio) {
        $r = $this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancoscuenta.habilitada' => 1], 'recursive' => 0, 'fields' => ['id']]);
        return (empty($r) ? 0 : $r['Bancoscuenta']['id']);
    }

    /*
     * Obtiene la cuenta bancaria por defecto que recibe Cobranzas automaticas. Si no hay ninguna configurada, obtiene la primera
     */

    public function getDefaultCA($consorcio) {
        $resul = $this->find('first', ['conditions' => ['Bancoscuenta.consorcio_id' => $consorcio, 'Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.defectocobranzaautomatica' => 1, 'Bancoscuenta.habilitada' => 1],
            'recursive' => 0, 'fields' => ['Bancoscuenta.id']]);
        if (!empty($resul)) {
            return $resul['Bancoscuenta']['id'];
        } else {
            return $this->getCuentaBancaria($consorcio);
        }
    }

    /*
     * obtiene los ID y name2 de todas las cuentas bancarias asociadas al consorcio
     * 21/11/2019 Esteban - Modifique la funcio para q devuelva id => name2, y cuando se utiliza hago array_keys() para tener solo los id (no habia una funcion q devuelva la lista de cuentas de un consorcio)
     * Si agrego consorcio.habilitado=1, se rompe toda la parte de pago a proveedores (el +), porque las cuentas bancarias de adm tienen asociado consorcio 0
     */

    public function getCuentasBancarias($consorcio, $client_id = null) {
        $r = $this->find('list', ['conditions' => ['Banco.client_id' => !empty($client_id) ? $client_id : $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio, 'Bancoscuenta.habilitada' => 1], 'recursive' => 0, 'fields' => ['id', 'name2']]);
        return $r;
    }

    /*
     * A partir de una cuenta bancaria, puedo saber los consorcios asociados a ella. Se utiliza en la Vista del pago a proveedor, cuando pagan a cuenta (solo se la cuenta bancaria)
     */

    public function getCuentasBancariasPorConsorcio() {
        $cuentas = [];
        $r = $this->find('all', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0, 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.consorcio_id']]);
        foreach ($r as $v) {
            if (!isset($cuentas[$v['Bancoscuenta']['consorcio_id']])) {
                $cuentas[$v['Bancoscuenta']['consorcio_id']] = [];
            }
            $cuentas[$v['Bancoscuenta']['consorcio_id']][] = $v['Bancoscuenta']['id'];
        }
        return $cuentas;
    }

    public function getConsorcio($bancoscuenta_id) {
        $r = $this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $bancoscuenta_id], 'recursive' => 0, 'fields' => ['consorcio_id']]);
        return (empty($r) ? 0 : $r['Bancoscuenta']['consorcio_id']);
    }

    /*
     * Función que obtiene la cuenta bancaria del consorcio (la primera)
     */

    public function getBancoNombre($consorcio) {
        $r = $this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => $consorcio], 'recursive' => 0, 'fields' => ['Banco.name']]);
        return (empty($r) ? 0 : $r['Banco']['name']);
    }

    /*
     * Función que obtiene la Comision Fija Interdeposito de la cuenta bancaria
     */

    public function getComisionFijaInterdeposito($id) {
        $r = $this->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $id], 'recursive' => 0, 'fields' => ['comision_fija_interdeposito']]);
        return (empty($r) ? 0 : $r['Bancoscuenta']['comision_fija_interdeposito']);
    }

    /*
     * Función que obtiene la comision variable de la cuenta bancaria
     */

    public function getComisionVariable($id) {
        $r = $this->find('first', ['conditions' => ['Bancoscuenta.id' => $id], 'fields' => ['comision_variable']]);
        return (empty($r) ? 0 : $r['Bancoscuenta']['comision_variable']);
    }

    /*
     * Función que obtiene la Cuenta de Gastos Particulares (si esta configurada) por defecto para cargar las Comisiones Cobranzas
     */

    public function getCGPComisionCobranza($id) {
        $r = $this->find('first', ['conditions' => ['Bancoscuenta.id' => $id], 'fields' => ['cgp_comision']]);
        return (empty($r) ? 0 : $r['Bancoscuenta']['cgp_comision']);
    }

    /*
     * Funcion que obtiene el saldo actual de un banco
     */

    public function getSaldo($id) {
        $r = $this->find('first', ['conditions' => ['Bancoscuenta.id' => $id], 'fields' => ['saldo']]);
        return $r['Bancoscuenta']['saldo'];
    }

    /*
     * Funcion que actualiza el saldo de un banco
     */

    public function setSaldo($id, $importe) {
        $this->id = $id;
        $this->saveField('saldo', $this->field('saldo') + $importe);
    }

    /*
     * Funcion que verifica si la cuenta bancaria tiene saldo suficiente para realizar el movimiento
     * Cambios: Se pueden tener saldos negativos en la cuenta bancaria, devuelvo siempre true (tiene saldo)
     */

    public function hasSaldo($id, $importe) {
        /* $r = $this->find('first', ['conditions' => ['Bancoscuenta.id' => $id], 'recursive' => -1, 'fields' => ['saldo']]);
          return (bool) (($r['Bancoscuenta']['saldo'] - $importe) >= 0); */
        return true;
    }

    /*
     * Devuelve los movimientos (ingresos, egresos, transferencias, etc) del banco $bancoscuenta_id ordenados por fecha descendente
     */

    public function getMovimientos($bancoscuenta_id, $desde = null, $hasta = null, $incluye_anulados = false, $incluye_conciliados = true) {
        $h = date("d/m/Y");
        /* if (!(empty($desde) || $desde == '1')) {
          $d = substr($desde, 6, 4) . "-" . substr($desde, 3, 2) . "-" . substr($desde, 0, 2);
          } */
        $d = '2016-01-01';
        if (!(empty($hasta) || $hasta == '1')) {
            $h = substr($hasta, 6, 4) . "-" . substr($hasta, 3, 2) . "-" . substr($hasta, 0, 2);
        }
        $cond = $incluye_anulados ? [] : ['anulado' => 0];
        $cond += empty($d) || $d == '1' ? ['fecha >=' => date("Y-m-d", strtotime("-2 months"))] : ['fecha >=' => $d, 'fecha <=' => $h];
        $chequespropios = Hash::combine($this->Chequespropio->find('all', ['conditions' => ['Chequespropio.proveedorspago_id !=' => 0, 'Chequespropio.bancoscuenta_id' => $bancoscuenta_id, $incluye_anulados ? [] : ['Proveedorspago.anulado' => 0],
                                empty($d) || $d == '1' ? ['Chequespropio.fecha_vencimiento >=' => date("Y-m-d", strtotime("-2 months"))] : ['Chequespropio.fecha_vencimiento >=' => $d, 'Chequespropio.fecha_vencimiento <=' => $h]],
                            'order' => 'Chequespropio.fecha_vencimiento desc,Chequespropio.id', 'joins' => [['table' => 'proveedorspagos', 'alias' => 'Proveedorspago', 'type' => 'left', 'conditions' => ['Chequespropio.proveedorspago_id=Proveedorspago.id']]]]), '{n}.Chequespropio.id', '{n}.Chequespropio');
        // como hago join, tengo q aclarar de q modelo sino me tira q "anulado" es ambiguous
        $cond2 = $incluye_anulados ? [] : ['Cheque.anulado' => 0, 'Bancosdepositoscheque.anulado' => 0];
        $cond2 += empty($d) || $d == '1' ? ['fecha >=' => date("Y-m-d", strtotime("-2 months"))] : ['fecha >=' => $d, 'fecha <=' => $h];
        $bancosdepositoscheques = Hash::combine($this->Bancosdepositoscheque->find('all', ['conditions' => ['Bancosdepositoscheque.bancoscuenta_id' => $bancoscuenta_id, $cond2], 'recursive' => 0, 'contain' => ['Cheque'], 'order' => 'Bancosdepositoscheque.fecha desc,Bancosdepositoscheque.id']), '{n}.Bancosdepositoscheque.id', '{n}.Bancosdepositoscheque');
        $bancosdepositosefectivos = Hash::combine($this->Bancosdepositosefectivo->find('all', ['conditions' => ['Bancosdepositosefectivo.bancoscuenta_id' => $bancoscuenta_id, $cond] + ($incluye_conciliados ? [] : ['Bancosdepositosefectivo.conciliado' => 0]), 'order' => 'Bancosdepositosefectivo.fecha desc,Bancosdepositosefectivo.id']), '{n}.Bancosdepositosefectivo.id', '{n}.Bancosdepositosefectivo');
        $bancosextracciones = $chequespropiosadm = [];

        // si es la cuenta de administracion, obtengo los cheques propios de adm, sino las extracciones
        if (!in_array($bancoscuenta_id, array_keys($this->getCuentasAdm()))) {
            $bancosextracciones = Hash::combine($this->Bancosextraccione->find('all', ['conditions' => ['Bancosextraccione.bancoscuenta_id' => $bancoscuenta_id, $cond], 'order' => 'Bancosextraccione.fecha desc,Bancosextraccione.id']), '{n}.Bancosextraccione.id', '{n}.Bancosextraccione');
        } else {
            $chequespropiosadm = Hash::combine($this->Chequespropiosadm->find('all', ['conditions' => ['Chequespropiosadm.bancoscuenta_id' => $bancoscuenta_id, 'Chequespropiosadmsdetalle.proveedorspago_id !=' => 0, $incluye_anulados ? [] : ['anulado' => 0], empty($d) || $d == '1' ? ['fecha_vencimiento >=' => date("Y-m-d", strtotime("-2 months"))] : ['fecha_vencimiento >=' => $d, 'fecha_vencimiento <=' => $h]],
                                'order' => 'Chequespropiosadm.fecha_vencimiento desc,Chequespropiosadm.id', 'joins' => [['table' => 'chequespropiosadmsdetalles', 'alias' => 'Chequespropiosadmsdetalle', 'type' => 'left', 'conditions' => ['Chequespropiosadm.id=Chequespropiosadmsdetalle.chequespropiosadm_id']]]]), '{n}.Chequespropiosadm.id', '{n}.Chequespropiosadm');
        }

        $bancostransferenciasOrigen = Hash::combine($this->BancostransferenciaOrigen->find('all', ['conditions' => ['BancostransferenciaOrigen.bancoscuenta_id' => $bancoscuenta_id, $cond], 'order' => 'BancostransferenciaOrigen.fecha desc,BancostransferenciaOrigen.id']), '{n}.BancostransferenciaOrigen.id', '{n}.BancostransferenciaOrigen');
        $bancostransferenciasDestino = Hash::combine($this->BancostransferenciaDestino->find('all', ['conditions' => ['BancostransferenciaDestino.destino_id' => $bancoscuenta_id, $cond], 'order' => 'BancostransferenciaDestino.fecha desc,BancostransferenciaDestino.id']), '{n}.BancostransferenciaDestino.id', '{n}.BancostransferenciaDestino');
        return Hash::sort(array_merge($bancosdepositoscheques, $bancosdepositosefectivos, $bancosextracciones, $bancostransferenciasOrigen, $bancostransferenciasDestino, $chequespropios, $chequespropiosadm), '{n}.fecha', 'desc');
    }

    /*
     * Obtiene los movimientos de una cuenta bancaria de ADMINISTRACION entre fechas
     */

    public function getMovimientosAdministracion($cuentaadm, $desde = null, $hasta = null, $recuperados = 0) {
        $chequespropiosadm = $this->Chequespropiosadm->getPagosChequespropiosadm($cuentaadm, $desde, $hasta, null, 0, $recuperados); // el cero es de anulados
        $efectivo = $this->Administracionefectivo->getPagosEfectivoAdm($cuentaadm, $desde, $hasta, null, 0, $recuperados);
        $transferencia = $this->Administraciontransferencia->getPagosTransferenciaAdm($cuentaadm, $desde, $hasta, null, 0, $recuperados);
        return ['c' => $chequespropiosadm, 'e' => $efectivo, 't' => $transferencia];
    }

    /*
     * Modifica Cuenta bancaria por defecto para recibir CA (puede existir una sola)
     */

    public function cambiaCADefecto($data) {
        if (!$this->canEdit($data[0])) {
            die("El dato es inexistente");
        }
        $this->id = $data[0];
        $actual = $this->field('defectocobranzaautomatica');

        if ($this->field('defectocobranzaautomatica') === false) {
            $r = $this->find('first', ['conditions' => ['Bancoscuenta.consorcio_id' => $this->getConsorcio($data[0]), 'Bancoscuenta.id !=' => $data[0],
                    'Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.defectocobranzaautomatica' => 1],
                'recursive' => 0, 'fields' => ['Bancoscuenta.id']]);
            if (!empty($r)) {
                die("Ya existe una Cuenta Bancaria por defecto para recibir Cobranzas Automáticas");
            }
        }
        $this->saveField('defectocobranzaautomatica', !$actual);

        return 1;
    }

    /*
     * Verifico si ya existe una Cuenta Bancaria del consorcio que tenga tildado defectocobranzaautomatica (por defecto puede haber una sola
     */

    public function checkDefaultCA($check) {
        $resul = [];
        if ($check['defectocobranzaautomatica'] == 1) {
            $resul = $this->find('first', ['conditions' => ['Bancoscuenta.consorcio_id' => $this->data['Bancoscuenta']['consorcio_id'],
                    'Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.defectocobranzaautomatica' => 1],
                'recursive' => 0, 'fields' => ['Bancoscuenta.id']]);
        }
        return empty($resul);
    }

    /*
     * Verifico antes de actualizar los valores de comision que haya configurado una Cuenta de gastos particulares antes
     */

    public function checkCGP($check) {
        $this->id = $this->data['Bancoscuenta']['id'];
        return ($this->field('cgp_comision') != 0);
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                $this->alias . '.cbu LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.name LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
