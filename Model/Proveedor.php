<?php

App::uses('AppModel', 'Model');

class Proveedor extends AppModel {

    public $virtualFields = ['name2' => 'CONCAT(Client.name," - ",Proveedor.name)', 'name3' => 'CONCAT(Proveedor.name,IF(Proveedor.nombrefantasia="","",concat(" (",Proveedor.nombrefantasia,")")))'];
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
        ),
        'address' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'matricula' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'cuit' => array(
            'unique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Ya existe un proveedor con ese CUIT',
                'allowEmpty' => true
            ),
            'escuit' => [
                'rule' => '/^[0-9]{2}-[0-9]{8}-[0-9]$/',
                'message' => 'El formato del CUIT es incorrecto. Ej: 20-30799986-3',
                'allowEmpty' => true,
            ],
            'validarCuit' => [
                'rule' => ['validarCuit'],
                'message' => "El CUIT no es correcto, verifique el mismo por favor",
                'allowEmpty' => true,
            ]
        ),
        'city' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'telephone' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'email' => array(
            'maildir' => array(
                'rule' => ['checkEmails'],
                'message' => 'El formato del email es incorrecto. Ej: juan@gmail.com. Si desea agregar mas de un email, separelos con coma y sin espacios. Ej: juan@gmail.com,pepe@hotmail.com',
                'allowEmpty' => true,
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
        )
    );
    public $hasMany = array(
        'Proveedorsfactura' => array(
            'className' => 'Proveedorsfactura',
            'foreignKey' => 'proveedor_id',
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
        'Proveedorspago' => array(
            'className' => 'Proveedorspago',
            'foreignKey' => 'proveedor_id',
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
        'Llavesmovimiento' => array(
            'className' => 'Llavesmovimiento',
            'foreignKey' => 'proveedor_id',
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
        return !empty($this->find('first', ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'Proveedor.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function getList($client = null) {
        $resul = $this->find('list', ['conditions' => ['Proveedor.client_id' => empty($client) ? $_SESSION['Auth']['User']['client_id'] : $client], 'order' => 'Proveedor.name', 'fields' => ['id', 'name3'], 'recursive' => 0]);
        return $resul;
    }

    public function getProveedorClientId($proveedor_id) {
        $r = $this->find('first', array('conditions' => array('Proveedor.id' => $proveedor_id), 'fields' => array('client_id')));
        return (empty($r) ? 0 : $r['Proveedor']['client_id']);
    }

    /*
     * Utilizada para obtener los Proveedores q coincidan con el texto ingresado en pago proveedores (nombre, cuit)
     */

    public function get($texto = null) {
        if (!empty($texto)) {
            $options = ['conditions' => ['Proveedor.client_id' => $_SESSION['Auth']['User']['client_id'], 'OR' => ['Proveedor.name like' => '%' . $texto . '%']],
                'fields' => ['Proveedor.id', 'Proveedor.name', 'Proveedor.cuit'],
                'order' => 'Proveedor.name',
                'limit' => 10];
            $resul = $this->find('all', $options);
            $cad = [];
            foreach ($resul as $k => $v) {// formateo el resultado para
                $cad[] = ['id' => $v['Proveedor']['id'], 'text' => $v['Proveedor']['name']];
            }
            return $cad;
        } else {
            return [];
        }
    }

    public function beforeSave($options = array()) {
        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['Proveedor']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        return true;
    }

    public function checkUnique($check) {
        if (isset($this->data['Proveedor']['cuit'])) {
            $resul = $this->find('count', array(
                'conditions' => array('Proveedor.cuit' => $this->data['Proveedor']['cuit'], 'Proveedor.client_id' => $_SESSION['Auth']['User']['client_id']),
                'recursive' => -1
            ));
            return ($resul == 0);
        }
        return true;
    }

    public function getMovimientosProveedor($id, $desde = null, $hasta = null, $incluye_pagas = false, $idconsorcio = null) {
        $desde = $this->fecha($desde);
        $hasta = $this->fecha($hasta);
        $cond = $incluye_pagas ? [] : ['Proveedorsfactura.saldo >' => 0];
        $cond += empty($desde) || $desde == '1' ? [] : ['Proveedorsfactura.fecha >=' => $desde];
        $cond += empty($hasta) || $hasta == '1' ? [] : ['Proveedorsfactura.fecha <=' => $hasta];
        $cond += !empty($idconsorcio) ? ['Consorcio.id' => $idconsorcio] : [];

        // busco los datos del proveedor, sus facturas, sus pagos y pagos a cuenta
        $proveedor = $this->find('first', ['conditions' => ['Proveedor.id' => $id], 'fields' => ['Proveedor.name', 'Proveedor.id', 'Proveedor.saldo']]);
        $facturas = $this->Proveedorsfactura->find('all', ['conditions' => ['Proveedorsfactura.proveedor_id' => $id, $cond],
            'fields' => ['Proveedorsfactura.id', 'Proveedorsfactura.concepto', 'Proveedorsfactura.fecha', 'Proveedorsfactura.created', 'Proveedorsfactura.numero', 'Proveedorsfactura.importe', 'Proveedorsfactura.saldo', 'Proveedorsfactura.tipo', 'Liquidation.periodo', 'Consorcio.name'],
            'joins' => [['table' => 'proveedors', 'alias' => 'Proveedor', 'type' => 'left', 'conditions' => ['Proveedorsfactura.proveedor_id=Proveedor.id']],
                ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.id=Proveedorsfactura.liquidation_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']]],
            'order' => 'Consorcio.name,Proveedorsfactura.fecha asc']);
        $movimientos = $movimientos2 = [];
        foreach ($facturas as $k => $v) {// facturas
            $movimientos[$k] = $v['Proveedorsfactura'];
            $movimientos[$k]['Consorcio'] = $v['Consorcio'];
            $movimientos[$k]['Liquidation'] = $v['Liquidation'];
            $movimientos[$k]['fecha'] .= " " . date("H:i:s", strtotime($v['Proveedorsfactura']['created'])); // para q la factura quede primero
        }

        $cond = empty($desde) || $desde == '1' ? [] : ['Proveedorspago.fecha >=' => $desde];
        $cond += empty($hasta) || $hasta == '1' ? [] : ['Proveedorspago.fecha <=' => $hasta];
        if (!empty($idconsorcio)) {
            $pagos = $this->Proveedorspago->getPagos($id, $idconsorcio, $desde, $hasta, 1, -1);
        } else {
            $pagos = $this->Proveedorspago->find('all', ['conditions' => ['Proveedorspago.proveedor_id' => $id, 'Proveedorspago.anulado' => 0, $cond],
                'order' => 'Proveedorspago.fecha asc', 'contain' => ['Proveedorspagosfactura']]);
        }

        if ($incluye_pagas) {
            foreach ($pagos as $k => $v) { // pagos
                $movimientos2[$k] = $v['Proveedorspago'];
                $movimientos2[$k]['fecha'] .= " " . date("H:i:s", strtotime($v['Proveedorspago']['created'])); // para q el pago quede despues de la factura
            }
        }
        $totales = $this->array_sort(array_merge($movimientos, $movimientos2), 'fecha', SORT_DESC);
        //$cuentasbancarias = $this->Client->Banco->Bancoscuenta->get(); //obtengo todas las cuentas bancarias del cliente (porq si no hay facturas, puedo hacer un pago a cuenta)
        return ['proveedor' => $proveedor, 'saldos' => $totales, /* 'cuentasbancarias' => $cuentasbancarias, */ 'pagos' => $pagos];
    }

    public function actualizaSaldosProveedor() {
        $proveedores = $this->find('list');
        foreach ($proveedores as $k => $v) {
            $facturas = $this->Proveedorsfactura->find('all', ['conditions' => ['Proveedorsfactura.proveedor_id' => $k], 'fields' => ['sum(Proveedorsfactura.importe) as total']]);
            $pagos = $this->Proveedorspago->find('all', ['conditions' => ['Proveedorspago.proveedor_id' => $k, 'Proveedorspago.anulado' => 0], 'fields' => ['sum(Proveedorspago.importe) as total']]);
            $this->id = $k;
            $this->saveField('saldo', $facturas[0][0]['total'] - $pagos[0][0]['total']);
        }
    }

    /*
     * Funcion que obtiene el saldo actual de un Proveedor
     */

    public function getSaldo($id) {
        $r = $this->find('first', array('conditions' => array('Proveedor.id' => $id), 'recursive' => -1, 'fields' => ['Proveedor.saldo']));
        return $r['Proveedor']['saldo'];
    }

    /*
     * Funcion que actualiza el saldo en pesos de un Proveedor
     */

    public function setSaldo($id, $importe) {
        $this->id = $id;
        $this->saveField('saldo', $this->field('saldo') + $importe);
    }

    /*
     * Funcion que verifica si el Proveedor tiene saldo suficiente para realizar el movimiento
     */

    public function hasSaldo($id, $importe) {
        $r = $this->find('first', ['conditions' => ['Proveedor.id' => $id], 'recursive' => -1, 'fields' => ['saldo']]);
        return (bool) (($r['Proveedor']['saldo'] - $importe) >= 0);
    }

    /*
     * Verifico q no haya movimientos del proveedor
     */

    public function beforeDelete($cascade = true) {
        $count = $this->Proveedorsfactura->find('count', array('conditions' => array('proveedor_id' => $this->id)));
        if ($count == 0) {
            $count = $this->Proveedorspago->find('count', array('conditions' => array('proveedor_id' => $this->id)));
            if ($count == 0) {
                return true;
            }
        }
        return false;
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
                $this->alias . '.city LIKE' => '%' . $data['buscar'] . '%',
                $this->alias . '.email LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
