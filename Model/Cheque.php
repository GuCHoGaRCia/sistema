<?php

App::uses('AppModel', 'Model');

class Cheque extends AppModel {

    public $displayField = 'concepto';
    public $virtualFields = array('conceptoimporte' => 'CONCAT(Cheque.banconumero, " ($", Cheque.importe,")")');
    public $validate = array(
        'client_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'caja_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'fecha_emision' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Debe completar con una fecha correcta',
            ),
        ),
        'fecha_vencimiento' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Debe completar con una fecha correcta',
            ),
        ),
        'concepto' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'importe' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'Debe ser un número decimal',
            ),
            'total' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Debe ser un importe mayor a cero',
            ),
        ),
        'anulado' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
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
        'Caja' => array(
            'className' => 'Caja',
            'foreignKey' => 'caja_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = array(
        'Bancosdepositoscheque' => array(
            'className' => 'Bancosdepositoscheque',
            'foreignKey' => 'cheque_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Cobranzacheque' => array(
            'className' => 'Cobranzacheque',
            'foreignKey' => 'cheque_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        /* 'Pagosproveedorscheque' => array(
          'className' => 'Pagosproveedorscheque',
          'foreignKey' => 'cheque_id',
          'dependent' => true,
          'conditions' => '',
          'fields' => '',
          'order' => '',
          'limit' => '',
          'offset' => '',
          'exclusive' => '',
          'finderQuery' => '',
          'counterQuery' => ''
          ), */
        'Cajastransferenciascheque' => [
            'className' => 'Cajastransferenciascheque',
            'foreignKey' => 'cajasegreso_id',
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

    /*
     * Al agregar un cheque de terceros en cobranza manual, hace append del agregado a la lista de cheques disponibles (por eso selecciona solo el creado actual)
     */

    public function agregar($data) {
        $this->create();
        $resul = $this->save(['client_id' => $_SESSION['Auth']['User']['client_id'], 'caja_id' => $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']), 'fecha_emision' => date("Y-m-d", strtotime($data['fe'])), 'fecha_vencimiento' => date("Y-m-d", strtotime($data['fp'])), 'concepto' => $data['c'], 'banconumero' => $data['n'], 'importe' => $data['i'], 'saldo' => $data['i'], 'anulado' => 0, 'fisico' => $data['t']]);
        return $this->find('first', array('conditions' => array('Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.id' => $resul['Cheque']['id'], 'Cheque.caja_id' => $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id'])), 'recursive' => 0,
                    'fields' => array('Cheque.id', 'Cheque.concepto', 'Cheque.importe', 'Cheque.banconumero', 'Cheque.saldo', 'Cheque.fisico')));
        //return $this->getChequesPendientes();
    }

    public function beforeSave($options = []) {
        $this->data['Cheque']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        if (!isset($this->data['Cheque']['caja_id'])) {
            $this->data['Cheque']['caja_id'] = $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id']);
        }
        // controlo las fechas de emision y pago (emision <= pago)
        if (isset($this->data['Cheque']['fecha_emision']) && !$this->fechaEsMenorIgualQue($this->data['Cheque']['fecha_emision'], $this->data['Cheque']['fecha_vencimiento'])) {
            //SessionComponent::setFlash(__('La fecha de pago debe ser mayor o igual a la fecha de emisión'), 'error', [], 'otro');
            return false;
        }

        if (isset($this->data['Cheque']['importe']) && !isset($this->data['Cheque']['id']) && isset($this->data['Cheque']['saldo']) && $this->data['Cheque']['saldo'] != 0) {
            // esta creando el cheque, guardo el saldo
            $this->data['Cheque']['saldo'] = $this->data['Cheque']['importe'];
        }
        return true;
    }

    /*
     * Si es cheque de tercero, creo el ingreso en la caja (!=0) y actualizo el saldo
     */

    public function afterSave($created, $options = []) {
        if ($created) {
            // si no es un crédito entonces entra (es deposito de caja a banco)
            if (isset($this->data['Cheque']['caja_id']) && $this->data['Cheque']['caja_id'] != 0) {
                $this->Caja->setSaldo($this->data['Cheque']['caja_id'], $this->data['Cheque']['importe'], 'saldo_cheques');
            }
        }
    }

    /*
     * Devuelve los cheques pendientes (utilizados o no, no anulados, sin depositar y que tengan saldo pendiente)
     * Si $todos = false, devuelve los cheques en caja del usuario actual, sino los cheques de todas las cajas
     */

    public function getChequesPendientes($todos = false) {
        $conditions = ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.anulado' => 0, 'Cheque.depositado' => 0, 'saldo >' => 0];
        if (!$todos) {
            $conditions += ['Cheque.caja_id' => $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id'])];
        }
        return $this->find('all', array('conditions' => $conditions, 'recursive' => 0,
                    'fields' => array('Cheque.id', 'Cheque.conceptoimporte', 'Caja.name', 'Caja.id', 'Cheque.concepto', 'Cheque.importe', 'Cheque.saldo', 'Cheque.fisico')));
    }

    /*
     * Cheques q se utilizaron completos y q estan listos para depositar o pagar proveedor
     */

    public function getChequesListosParaEntregar($todos = false) {
        $conditions = ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.habilitado' => 1, 'Cheque.anulado' => 0, 'Cheque.depositado' => 0, 'Cheque.saldo' => 0, 'Cheque.proveedorspago_id' => 0];
        if (!$todos) {
            $conditions += ['Cheque.caja_id' => $this->Caja->getCajaUsuario($_SESSION['Auth']['User']['id'])];
        }
        return $this->find('all', ['conditions' => $conditions, 'recursive' => 0,
                    'joins' => [['table' => 'cobranzacheques', 'alias' => 'Cobranzacheque', 'type' => 'left', 'conditions' => ['Cheque.id=Cobranzacheque.cheque_id']],
                        ['table' => 'cobranzas', 'alias' => 'Cobranza', 'type' => 'left', 'conditions' => ['Cobranza.id=Cobranzacheque.cobranza_id']],
                        ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=Cobranza.propietario_id']],
                        ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]
                    ],
                    'order' => 'Propietario.consorcio_id',
                    'group' => 'Cheque.id',
                    'fields' => ['Cheque.id', 'Cheque.conceptoimporte', 'Caja.name', 'Caja.id', 'Cheque.concepto', 'Cheque.importe', 'Cheque.banconumero', 'Cheque.saldo', 'Cheque.fisico', 'Consorcio.name', 'Consorcio.id', 'Cobranza.id']]);
    }

    /*
     * Calcula el saldo de cheques pendientes de cada caja (todos=true), sino de la caja del usuario actual
     */

    public function getSaldoChequesPendientes($todos = false) {
        $cheques = $this->getChequesPendientes($todos);
        $saldos = [];
        foreach ($cheques as $k => $v) {
            //if ($v['Cheque']['importe'] - $v['Cheque']['saldo'] > 0 && $v['Cheque']['saldo'] > 0) {
            if ($v['Cheque']['saldo'] != 0) {
                if (isset($saldos[$v['Caja']['id']])) {
                    $saldos[$v['Caja']['id']] += $v['Cheque']['saldo'];
                } else {
                    $saldos[$v['Caja']['id']] = $v['Cheque']['saldo'];
                }
            }
        }
        return $saldos;
    }

    /*
     * Devuelve los movimientos del cheque actual
     */

    public function getMovimientosCheque($cheque_id) {
        $cch = $this->Client->Consorcio->Propietario->Cobranza->Cobranzacheque->getMovimientosCheque($cheque_id);
        $mch = [];
        if (!empty($cch)) {
            foreach ($cch as $k => $v) {
                $mch[$k] = $v['Cobranza'];
                $mch[$k]['Cheque'] = $v['Cheque'];
                $mch[$k]['Cobranzacheque'] = $v['Cobranzacheque'];
            }
        }
        $bd = $this->Bancosdepositoscheque->getMovimientosCheque($cheque_id);
        $ingresos = Hash::combine($this->Caja->Cajasingreso->find('all', ['conditions' => ['Cajastransferenciascheque.cheque_id' => $cheque_id],
                            'joins' => [['table' => 'cajastransferenciascheques', 'alias' => 'Cajastransferenciascheque', 'type' => 'left', 'conditions' => ['Cajasingreso.id=Cajastransferenciascheque.cajasingreso_id']]],
                            'order' => 'Cajasingreso.fecha2 desc']), '{n}.Cajasingreso.id', '{n}.Cajasingreso');
        $proveed = $this->find('first', ['conditions' => ['Cheque.id' => $cheque_id], 'fields' => ['Cheque.proveedorspago_id']]);
        $pp = [];
        if ($proveed['Cheque']['proveedorspago_id'] != 0) {
            // utilizado para un pago a proveedor, busco el detalle
            $pp = Hash::combine($this->Client->Proveedor->Proveedorspago->find('all', ['conditions' => ['Proveedorspago.id' => $proveed['Cheque']['proveedorspago_id']]]), '{n}.Proveedorspago.id', '{n}.Proveedorspago');
        }
        return Hash::sort(array_merge($mch, (!empty($bd) ? [$bd[0]['Bancosdepositoscheque']] : []), $pp, $ingresos), '{n}.fecha2', 'asc');
    }

    /*
     * Funcion que obtiene el saldo actual de un cheque
     */

    public function getSaldo($id) {
        $r = $this->find('first', array('conditions' => array('Cheque.id' => $id), 'recursive' => -1, 'fields' => array('saldo')));
        return (empty($r) ? 0 : $r['Cheque']['saldo']);
    }

    /*
     * Funcion que actualiza el saldo de un cheque
     */

    public function setSaldo($id, $importe) {
        $this->id = $id;
        $this->saveField('saldo', $this->field('saldo') + $importe, ['callbacks' => false]);
    }

    /*
     * Funcion que verifica si el cheque tiene saldo suficiente para realizar el movimiento
     */

    public function hasSaldo($id, $importe) {
        $r = $this->find('first', array('conditions' => array('Cheque.id' => $id), 'recursive' => -1, 'fields' => array('saldo')));
        return (bool) (isset($r['Cheque']['saldo']) && ($r['Cheque']['saldo'] - $importe) >= 0);
    }

    /*
     * Funcion que obtiene el importe actual de un cheque (para los pagos proveedor)
     */

    public function getImporte($id) {
        $r = $this->find('first', array('conditions' => array('Cheque.id' => $id), 'fields' => array('importe')));
        return (empty($r) ? 0 : $r['Cheque']['importe']);
    }

    /*
     * Funcion que obtiene el numero de un cheque (para los errores de pagos proveedor)
     */

    public function getNumero($id) {
        $r = $this->find('first', array('conditions' => array('Cheque.id' => $id), 'fields' => array('banconumero')));
        return (empty($r) ? 0 : $r['Cheque']['banconumero']);
    }

    /*
     * Funcion que obtiene el Consorcio asociado a un Cheque de Terceros. Se utiliza por ejemplo al depositar cheques de terceros
     */

    public function getConsorcioId($id) {
        $r = $this->find('first', ['conditions' => ['Cheque.client_id' => $_SESSION['Auth']['User']['client_id'], 'Cheque.id' => $id], 'recursive' => 0,
            'joins' => [['table' => 'cobranzacheques', 'alias' => 'Cobranzacheque', 'type' => 'left', 'conditions' => ['Cheque.id=Cobranzacheque.cheque_id']],
                ['table' => 'cobranzas', 'alias' => 'Cobranza', 'type' => 'left', 'conditions' => ['Cobranza.id=Cobranzacheque.cobranza_id']],
                ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Propietario.id=Cobranza.propietario_id']]
            ],
            'fields' => ['Propietario.consorcio_id']]);
        return (empty($r) ? 0 : $r['Propietario']['consorcio_id']);
    }

    /*
     * Funcion que verifica si el cheque está en uso (falta asignarle alguna cobranza) (saldo != 0) o proveedorspago_id !== 0
     */

    public function isInUse($id) {
        /* $r = $this->find('first', array('conditions' => array('Cheque.id' => $id), 'recursive' => -1, 'fields' => array('saldo', 'importe', 'proveedorspago_id')));
          return (bool) ($r['Cheque']['saldo'] !== '0.00' || $r['Cheque']['proveedorspago_id'] !== '0'); */
        $r = empty($this->getMovimientosCheque($id));
        return (bool) $r;
    }

    /*
     * Verifica si un cheque puede ser anulado (puede tener movimientos, pero estan anulados. En estos casos, el saldo sigue siendo igual al importe)
     */

    public function sePuedeAnular($id) {
        $this->id = $id;
        return (bool) ($this->field('anulado') == 0 && $this->field('proveedorspago_id') == 0 && $this->field('depositado') == 0 && $this->field('saldo') == $this->field('importe'));
    }

    /*
     * Verifica si un cheque esta anulado
     */

    public function isAnulado($id) {
        $this->id = $id;
        return (bool) ($this->field('anulado') == 1);
    }

    /*
     * verifica si un cheque fue depositado (el campo contiene cero, o el id de la cuenta bancaria donde fue depositado)
     */

    public function isDepositado($id) {
        $this->id = $id;
        return (bool) ($this->field('depositado') != 0);
    }

    /*
     * Funcion que verifica si el cheque está listo para entregar a proveedor
     */

    public function isListoParaEntregar($id) {
        $this->id = $id;
        return (bool) ($this->field('saldo') == 0 && $this->field('proveedorspago_id') == 0);
    }

    /*
     * Funcion que cambia la caja de un cheque
     */

    public function transferir($id, $caja_id) {
        $this->id = $id;
        $this->saveField('caja_id', $caja_id, ['callbacks' => false]);
    }

    /*
     * Funcion que deposita un cheque en una cuenta bancaria y actualizo su saldo
     */

    public function depositar($id, $bancoscuenta_id) {
        $this->id = $id;

        // deposito el cheque
        $this->saveField('depositado', $bancoscuenta_id, ['callbacks' => false]);

        // actualizo el saldo de la cuenta bancaria
        $this->Bancosdepositoscheque->Bancoscuenta->setSaldo($bancoscuenta_id, $this->field('importe'));
    }

    /*
     * Funcion que elimina un depósito de un cheque
     */

    public function undoDepositar($id, $consorcio_id, $fecha) {
        $this->id = $id;
        if ($this->field('anulado') == 1) {
            return true;
        }
        $data = ['bancoscuenta_id' => 0, 'consorcio_id' => $consorcio_id, 'caja_id' => $this->field('caja_id'), 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $fecha,
            'concepto' => '[ANULADO] DChT ' . $this->field('concepto'), 'importe' => 0, 'cheque' => $this->field('importe'), 'anulado' => 1];
        $this->Caja->Cajasingreso->crear($data, 'saldo_cheques');

        // creo el debito bancario de ese cheque y actualizo saldo del banco
        $data2 = ['consorcio_id' => $consorcio_id, 'bancoscuenta_id' => $this->field('depositado'), 'caja_id' => 0, 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $data['fecha'], 'concepto' => $data['concepto'], 'importe' => $data['cheque'], 'anulado' => 1];
        $this->Bancosdepositoscheque->Bancoscuenta->Bancosextraccione->crear($data2 + ['proveedorspago_id' => 0, 'conciliado' => 0]); //actualizo el saldo bancario ahi
        // undo del depósito del cheque
        $this->saveField('depositado', 0, ['callbacks' => false]);
    }

    /*
     * Funcion que anula un cheque verificando primero que no haya movimientos (saldo != importe)
     */

    public function undo($id) {
        if (!$this->sePuedeAnular($id)) {
            return false;
        }
        $r = $this->find('first', ['conditions' => ['Cheque.id' => $id], 'recursive' => -1]);
        $r = $r['Cheque'];

        // anulo el cheque
        $this->save(['id' => $id, 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['concepto']], ['callbacks' => false]);

        // actualizo el saldo de la caja, salvo los cheques propios
        if ($r['caja_id'] != 0) {
            $this->Caja->setSaldo($r['caja_id'], -$r['saldo'], 'saldo_cheques');
        }

        return true;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return array(
            'OR' => array(
                'Cheque.concepto LIKE' => '%' . $data['buscar'] . '%',
                'Caja.name LIKE' => '%' . $data['buscar'] . '%',
                'Cheque.banconumero LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
