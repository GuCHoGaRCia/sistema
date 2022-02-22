<?php

App::uses('AppModel', 'Model');

class Chequespropio extends AppModel {

    public $virtualFields = ['tipo' => 7, 'fecha' => 'fecha_vencimiento']; //ifnull(Proveedorspago.fecha,fecha_vencimiento)
    public $validate = [
        'client_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe completar  el dato',
            ],
        ],
        'user_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe completar  el dato',
            ],
        ],
        'bancoscuenta_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe completar  el dato',
            //'required' => true,
            ],
        ],
        'fecha_emision' => [
            'date' => [
                'rule' => ['date'],
                'message' => 'Debe completar  el dato',
                'on' => 'create'
            ],
            'verificafecha' => array(
                'rule' => array('checkDates'),
                'message' => 'La fecha emision debe ser menor o igual a la fecha vecimiento',
                'on' => 'update'
            ),
        ],
        'fecha_vencimiento' => [
            'date' => [
                'rule' => ['date'],
                'message' => 'Debe completar  el dato',
                'on' => 'create'
            ],
            'verificafecha' => [
                'rule' => ['checkDates'],
                'message' => 'La fecha de emision debe ser menor o igual a la de vencimiento',
                'on' => 'update'
            ],
        ],
        'concepto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => 'Debe completar el dato',
            ],
        ],
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
        'anulado' => [
            'boolean' => [
                'rule' => ['boolean'],
                'message' => 'Debe completar  el dato',
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
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Bancoscuenta' => [
            'className' => 'Bancoscuenta',
            'foreignKey' => 'bancoscuenta_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ],
        'Proveedorspago' => [
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedorspago_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ]
    ];

    /* public $hasMany = array(
      'Chequespropiosadm' => array(
      'className' => 'Chequespropiosadm',
      'foreignKey' => 'chequespropio_id',
      'dependent' => true,
      'conditions' => '',
      'fields' => '',
      'order' => '',
      'limit' => '',
      'offset' => '',
      'exclusive' => '',
      'finderQuery' => '',
      'counterQuery' => ''
      )); */

    /*
     * Agrega un Cheque Propio desde Pago a Proveedores
     */

    public function agregar($data) {
        if (!isset($data['fe']) || !isset($data['fv']) || $data['fv'] < $data['fe']) {
            return ['r' => 0, 'e' => 'La fecha de vencimiento debe ser mayor o igual a la de emisión'];
        }
        if (!isset($data['i']) || $data['i'] <= 0) {
            return ['r' => 0, 'e' => 'El importe debe ser mayor a cero'];
        }
        if (!isset($data['c']) || $data['c'] === '' || empty($data['c'])) {
            return ['r' => 0, 'e' => 'Debe ingresar un concepto'];
        }
        $r = $this->Bancoscuenta->find('first', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.id' => $data['b']], 'recursive' => 0, 'fields' => 'Bancoscuenta.consorcio_id']);
        if (!isset($data['b']) || empty($r)) {
            return ['r' => 0, 'e' => 'La Cuenta Bancaria es inexistente'];
        }
        //obtengo las cuentas bancarias de ADM, si tiene
        //$badm = $this->Bancoscuenta->find('list', ['conditions' => ['Banco.client_id' => $_SESSION['Auth']['User']['client_id'], 'Bancoscuenta.consorcio_id' => 0], 'recursive' => 0, 'fields' => ['Bancoscuenta.id', 'Bancoscuenta.id']]);

        /* if (in_array($data['b'], $badm)) {// si es cheque de adm, chequeo q i = sumadeimportesdecadaconsor
          $sum = 0;
          if (!empty($data['d'])) {
          foreach ($data['d'] as $k => $v) {
          $sum += $v;
          }
          }
          $a = (float) $data['i'];
          $b = (float) $sum;
          if ("$a" != "$b") {// dejar la comparacion con STRING, es una chotada sino 904.53 != 904.53 ???!?!?!
          return ['r' => 0, 'e' => 'El importe total es distinto a la suma por Consorcio'];
          }
          } */
        $resul = $this->save(['fecha_emision' => $data['fe'], 'fecha_vencimiento' => $data['fv'], 'concepto' => $data['c'], 'importe' => $data['i'], 'bancoscuenta_id' => $data['b'], 'numero' => $data['n']]);
        if ($resul) {
            /* if (in_array($data['b'], $badm)) {// es un CHP de administracion, guardo el importe del ChP de cada consorcio
              $chpid = $this->getInsertId();
              foreach ($data['d'] as $k => $v) {
              die;
              $this->Chequespropiosadm->create();
              $this->Chequespropiosadm->save(['chequespropio_id' => $chpid, 'consorcio_id' => $k, 'proveedorspago_id' => 0, 'importe' => $v]);
              }
              } */
            return ['r' => 1, 'e' => $this->find('first', array('conditions' => array('Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropio.id' => $resul['Chequespropio']['id']), 'recursive' => 0,
                    //'joins' => [['table' => 'chequespropiosadms', 'alias' => 'Chequespropiosadm', 'type' => 'left', 'conditions' => ['Chequespropio.id=Chequespropiosadm.chequespropio_id']]],
                    'fields' => array('DISTINCT Chequespropio.id', 'Chequespropio.concepto', 'Chequespropio.numero', 'Chequespropio.importe', 'Bancoscuenta.name', 'Bancoscuenta.consorcio_id'),
                    'contain' => ['Bancoscuenta']))];
        } else {
            return ['r' => 0, 'e' => 'El Cheque Propio no pudo guardarse'];
        }
    }

    /*
     * Devuelve los cheques pendientes (no anulados, sin depositar y que tengan saldo pendiente)
     */

    public function getChequesPendientes() {
        return $this->find('all', array('conditions' => array('Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropio.anulado' => 0, 'Chequespropio.proveedorspago_id' => 0), 'recursive' => 0,
                    //'joins' => [['table' => 'chequespropiosadms', 'alias' => 'Chequespropiosadm', 'type' => 'left', 'conditions' => ['Chequespropio.id=Chequespropiosadm.chequespropio_id']]],
                    'fields' => array('DISTINCT Chequespropio.id', 'Chequespropio.numero', 'Chequespropio.concepto', 'Chequespropio.importe', 'Bancoscuenta.name', 'Bancoscuenta.consorcio_id'),
                    'contain' => ['Bancoscuenta']
                        )
        );
    }

    /*
     * Devuelve los cheques con vencimiento a futuro (mayor a la fecha $fechainicial). 
     * Se utiliza en resumen caja banco, abajo del todo. 
     * Si seleccionan Mayo 2021, te va a mostrar los cheques creados en mayo y vencimiento mayor a mayo
     */

    public function getChequesFuturos($consorcio, $fechainicial) {
        return $this->find('all', array('conditions' => array('Bancoscuenta.consorcio_id' => $consorcio, 'Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropio.anulado' => 0, 'Chequespropio.proveedorspago_id !=' => 0, 'Chequespropio.fecha_vencimiento >' => $this->fecha($fechainicial)), 'recursive' => 0,
                    //'joins' => [['table' => 'chequespropiosadms', 'alias' => 'Chequespropiosadm', 'type' => 'left', 'conditions' => ['Chequespropio.id=Chequespropiosadm.chequespropio_id']]],
                    'fields' => array('DISTINCT Chequespropio.id', 'Chequespropio.numero', 'Chequespropio.concepto', 'Chequespropio.importe', 'Chequespropio.fecha_emision', 'Chequespropio.fecha_vencimiento', 'Bancoscuenta.name', 'Bancoscuenta.consorcio_id'),
                    'contain' => ['Bancoscuenta']
                        )
        );
    }

    /*
     * Funcion que obtiene el importe de un cheque propio
     */

    public function getImporte($id) {
        $r = $this->find('first', array('conditions' => array('Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropio.id' => $id), 'fields' => array('importe')));
        return (empty($r) ? 0 : $r['Chequespropio']['importe']);
    }

    public function getNumero($id) {
        $r = $this->find('first', array('conditions' => array('Chequespropio.id' => $id), 'fields' => array('numero')));
        return (empty($r) ? 0 : $r['Chequespropio']['numero']);
    }

    /*
     * Verifico si el ChequePropio se puede usar: si esta anulado o ya fue utilizado en otro pago proveedor, no se puede usar
     */

    public function isInUse($id) {
        $this->id = $id;
        return (bool) (!$this->exists() || $this->field('anulado') == 1 || $this->field('proveedorspago_id') != 0 || $this->field('client_id') != $_SESSION['Auth']['User']['client_id']);
    }

    /*
     * Los cheques propios pertenecen a un cliente y usuario
     */

    public function beforeSave($options = array()) {
        $this->data['Chequespropio']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        $this->data['Chequespropio']['user_id'] = $_SESSION['Auth']['User']['id'];
        /* if (isset($this->data['Chequespropio']['importe']) && !isset($this->data['Chequespropio']['id'])) {
          //esta creando uno nuevo desde /add
          } */
        return true;
    }

    /*
     * Si la fecha de vencimiento es menor o igual a la fecha actual, modifico el saldo de la cuenta bancaria (se hace al modificar el cheque)
     */

    public function afterSave($created, $options = []) {
        if (!$created && isset($this->data['Chequespropio']['fecha_vencimiento']) && isset($this->data['Chequespropio']['importe']) && strtotime($this->data['Chequespropio']['fecha_vencimiento']) <= strtotime(date("Y-m-d"))) {
            $this->Bancoscuenta->setSaldo($this->data['Chequespropio']['bancoscuenta_id'], -$this->data['Chequespropio']['importe']);
        }
    }

    /*
     * Funcion que anula un cheque propio
     */

    public function undo($id) {
        $r = $this->find('first', array('conditions' => array('Chequespropio.id' => $id, 'Chequespropio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Chequespropio.anulado' => 0), 'fields' => ['Chequespropio.id', 'Chequespropio.concepto', 'Chequespropio.importe', 'Chequespropio.bancoscuenta_id', 'Chequespropio.fecha_vencimiento', 'Chequespropio.proveedorspago_id']));
        if (!empty($r)) {
            // anulo el cheque propio (no pongo return $this->save(...) porq no devuelve true or false! No se q onda..
            $this->save(['id' => $r['Chequespropio']['id'], 'anulado' => 1, 'concepto' => '[ANULADO] ' . $r['Chequespropio']['concepto']], ['callbacks' => false]);
            if ($r['Chequespropio']['proveedorspago_id'] != 0 && strtotime($r['Chequespropio']['fecha_vencimiento']) <= strtotime(date("Y-m-d"))) {
                $data2 = ['caja_id' => 0, 'bancoscuenta_id' => $r['Chequespropio']['bancoscuenta_id'], 'user_id' => $_SESSION['Auth']['User']['id'], 'cobranza_id' => null, 'fecha' => $r['Chequespropio']['fecha_vencimiento'], 'concepto' => '[ANULADO] ' . $r['Chequespropio']['concepto'], 'importe' => $r['Chequespropio']['importe'], 'es_transferencia' => 0, 'conciliado' => 0, 'anulado' => 1];
                $this->Bancoscuenta->Bancosdepositosefectivo->crear($data2);
            }
            // IMPORTANTE!!!!!!!!!
            // los ChP cuyo vencimiento es a futuro, crea los créditos en el cron_movimientosbancarios.php !!!!!!!!!
            return true;
        } else {
            return false;
        }
    }

    /*
     * Verifico que la fecha de emision sea menor o igual a la de vencimiento
     */

    /* public function checkDates($check) {
      if (!isset($this->data['Chequespropio']['fecha_emision']) || !isset($this->data['Chequespropio']['fecha_vencimiento'])) {
      return true;
      }
      return (date('Y-m-d', strtotime($this->data['Chequespropio']['fecha_emision'])) <= date('Y-m-d', strtotime($this->data['Chequespropio']['fecha_vencimiento'])));
      } */
    /*
     * Verifico que el vencimiento sea menor o igual al limite
     */

    public function checkDates($check) {
        if (isset($this->data['Chequespropio']['id'])) {
            if (isset($check['fecha_emision'])) {
                $fecha = $check['fecha_emision'];
                $esfecha_emision = true;
            } else {
                $fecha = $check['fecha_vencimiento'];
                $esfecha_emision = false;
            }
            $this->id = $this->data['Chequespropio']['id'];
            $fechaAComparar = $this->field(($esfecha_emision ? 'fecha_vencimiento' : 'fecha_emision'));
            if ($esfecha_emision) {
                // vencimiento <= limite
                return (date('Y-m-d', strtotime($fecha)) <= date('Y-m-d', strtotime($fechaAComparar)));
            } else {
                // limite >= vencimiento
                return (date('Y-m-d', strtotime($fecha)) >= date('Y-m-d', strtotime($fechaAComparar)));
            }
        }
        return true;
    }

}
