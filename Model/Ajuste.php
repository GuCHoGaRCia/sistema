<?php

App::uses('AppModel', 'Model');

//prueba
class Ajuste extends AppModel {

    public $validate = [
        'propietario_id' => [
            'numeric' => [
                'rule' => ['numeric'],
                'message' => 'Debe completar el dato',
            ],
        ],
        'user_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'fecha' => [
            'date' => [
                'rule' => ['date'],
            ],
        ],
        'concepto' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'importe' => [
            'decimal' => [
                'rule' => ['decimal'],
                'message' => 'Debe ser un numero decimal',
            ],
            'range' => [
                'rule' => ['range', 0, 999999],
                'message' => 'Debe ser un numero decimal mayor a cero',
            ],
        ]
    ];
    public $belongsTo = [
        'Propietario' => [
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
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
        ]
    ];
    public $hasMany = [
        'Ajustetipoliquidacione' => [
            'className' => 'Ajustetipoliquidacione',
            'foreignKey' => 'ajuste_id',
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
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'Ajuste.id' => $id], 'fields' => [$this->alias . '.id'], 'recursive' => 0]));
    }

    public function procesaAjusteManual($data) {
        $this->create();
        $resul = $this->save(['propietario_id' => $data['Ajuste']['propietario_id'], 'fecha' => $data['Ajuste']['fecha'], 'concepto' => $data['Ajuste']['concepto'], 'importe' => $data['Ajuste']['importe'], 'user_id' => $_SESSION['Auth']['User']['id']]);
        $id = $resul['Ajuste']['id'] ?? 0;
        $total = 0;

        // para cada tipo de liq guardo cuanto se cobra
        foreach ($data['Ajuste'] as $k => $v) {
            if (substr($k, 0, 3) == 'lt_' && $v > 0) {
                $ltid = (int) substr($k, 3);
                $check = (int) $data['Ajuste']['chk_' . $ltid];
                if ($ltid > 0) {
                    // el tipo de liquidacion actual tiene importe > 0
                    $total += $v;
                    $this->Ajustetipoliquidacione->create();
                    $this->Ajustetipoliquidacione->save(['ajuste_id' => $id, 'liquidations_type_id' => substr($k, 3), 'amount' => $v, 'solocapital' => $check]);
                }
            }
        }
        // el ajuste no va ni al banco ni a la caja, no hago nada mas
        return true;
    }

    public function procesaAjustePeriodo($data) {
        $consorcio_id = $data['Ajuste']['consorcio_id'];
        unset($data['Ajuste']['consorcio_id']);
        $this->Propietario->Consorcio->id = $consorcio_id;
        $consorcio = $this->Propietario->Consorcio->field('name');
        $datos = []; // voy guardando el detalle del ajuste de cada propietario
        foreach ($data['Ajuste'] as $k => $v) {// para cada ajuste, ejecuto procesaAjusteManual()
            $d = explode("_", $k);
            if ($d[0] !== "c") {
                continue; // sigo cuando son fechas
            }
            $this->Propietario->id = $d[1];
            $concepto = 'AP ' . $consorcio . " - " . $this->Propietario->field('name') . " (" . $this->Propietario->field('unidad') . ")";
            if (!isset($datos[$d[1]])) {
                $datos[$d[1]] = [];
            }
            $f = [];
            if (isset($data['Ajuste']["f_" . $d[1] . "_" . $d[2]])) {
                $f = explode('/', $data['Ajuste']["f_" . $d[1] . "_" . $d[2]]);
            }
            $fecha = !empty($f) && count($f) === 3 && checkdate($f[1], $f[0], $f[2]) ? $f[2] . "-" . $f[1] . "-" . $f[0] : date("Y-m-d");
            $datos[$d[1]] += ['Ajuste' => ['propietario_id' => $d[1], 'user_id' => $_SESSION['Auth']['User']['id'], 'fecha' => $fecha, 'concepto' => $concepto, 'importe' => 0, 'anulado' => 0]];
            // tengo q ver dentro de todos los ajustes enviados cuales son del mismo propietario, y crear los  'lt_XX' => '574' para cada uno
            foreach ($data['Ajuste'] as $l => $m) {
                if ($l === 'c_' . $d[1] . '_' . $d[2] && $m > 0) {// es un ajuste del propietario actual, agrego los detalles abonados en cada liquidation_type
                    $datos[$d[1]]['Ajuste'] += ['chk_' . $d[2] => 0]; // el check de solocapital = 0
                    $datos[$d[1]]['Ajuste'] += ['lt_' . $d[2] => $m]; // el monto en el tipo de liquidacion $d[2]
                    $datos[$d[1]]['Ajuste']['importe'] += $m; // sumo el ajuste del mismo propiet en cada liquidation type
                }
            }
        }
        // proceso todas los ajustes generadas
        foreach ($datos as $k => $v) {
            $this->procesaAjusteManual($v);
        }
        return true;
    }

    /*
     * Funcion que anula un ajuste
     * No se tiene que realizar ninguna validación antes de anular el movimiento
     */

    public function undo($id) {
        // anulo el ajuste
        $this->id = $id;
        return $this->saveField('anulado', 1);
    }

    /*
     * Funcion q devuelve los ajustes de una liquidación o de un propietario particular
     */

    public function getAjustes($liquidation_id, $propietario_id = null) {
        $liquidation_type = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getLiquidationsTypeId($liquidation_id);
        // obtengo los ajustes actuales que no estén anulados (realizados entre la fecha del ultimo cierre y la actual
        $anterior = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getLastLiquidation($liquidation_id);
        $closed = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getLiquidationClosedDate($anterior);
        $condiciones = ['Ajustetipoliquidacione.liquidations_type_id' => $liquidation_type, /* 'Liquidation.bloqueada' => 1, */ 'Ajuste.created >=' => $closed, 'Ajuste.created <' => date('Y-m-d H:i:s'), 'Ajuste.anulado' => 0];
        if (!is_null($propietario_id)) {
            $condiciones['Ajuste.propietario_id'] = $propietario_id;
        } else {
            $consorcio_id = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getConsorcioId($liquidation_id);
            $props = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->Consorcio->Propietario->getPropietarios($consorcio_id, ['id']);
            $condiciones['Ajuste.propietario_id'] = array_keys($props);
        }
        $options = ['conditions' => $condiciones, 'recursive' => 0/* , 'order' => array('Liquidation.created DESC') */,
            'fields' => ['Ajuste.propietario_id', 'Ajustetipoliquidacione.amount', 'Ajustetipoliquidacione.liquidations_type_id', 'Ajustetipoliquidacione.solocapital', 'Ajuste.fecha'],
            'joins' => [['table' => 'ajustetipoliquidaciones', 'alias' => 'Ajustetipoliquidacione', 'type' => 'left', 'conditions' => ['Ajustetipoliquidacione.ajuste_id=Ajuste.id']]]
        ];
        $resul = $this->find('all', $options);
        return $resul;
    }

    /*
     * Funcion que devuelve el total de los ajustes de una liquidación o de un propietario particular
     */

    public function getTotalAjustesPorTipodeLiquidacion($liquidation_id, $propietario_id = null) {
        $total = [];
        $liquidation_types = $this->Ajustetipoliquidacione->LiquidationsType->getLiquidationsTypes();
        foreach ($liquidation_types as $k => $v) {
            $total[$k] = 0;
        }
        $ajustes = $this->getAjustes($liquidation_id, $propietario_id);
        foreach ($ajustes as $k => $v) {
            $total[$v['Ajustetipoliquidacione']['liquidations_type_id']] += $v['Ajustetipoliquidacione']['amount'];
        }
        return $total;
    }

    /*
     * Obtengo los ajustes del consorcio para todos los tipos de liquidaciones para liquidaciones q no hayan sido cerradas
     */

    public function getAjustesPeriodo($consorcio_id) {
        $liquidation_types = $this->Ajustetipoliquidacione->LiquidationsType->getLiquidationsTypes();
        // obtengo los ajustes
        $resultado = [];
        foreach ($liquidation_types as $k => $v) {
            $anterior = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getUltimaLiquidacion($consorcio_id, $k);
            $closed = $this->Ajustetipoliquidacione->LiquidationsType->Liquidation->getLiquidationClosedDate($anterior);
            $condiciones = ['Ajustetipoliquidacione.liquidations_type_id' => $k, 'Liquidation.consorcio_id' => $consorcio_id, 'Propietario.consorcio_id' => $consorcio_id, 'Ajuste.created >=' => $closed, 'Ajuste.created <' => date('Y-m-d H:i:s'), 'Ajuste.anulado' => 0];
            $options = ['conditions' => $condiciones, 'recursive' => -1,
                'fields' => ['Ajuste.propietario_id', 'Ajuste.id', 'Ajustetipoliquidacione.amount', 'Ajustetipoliquidacione.solocapital', 'Ajuste.fecha'],
                'joins' => [['table' => 'ajustetipoliquidaciones', 'alias' => 'Ajustetipoliquidacione', 'type' => 'left', 'conditions' => ['Ajustetipoliquidacione.ajuste_id=Ajuste.id']],
                    ['table' => 'liquidations', 'alias' => 'Liquidation', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=Ajustetipoliquidacione.liquidations_type_id']],
                    ['table' => 'propietarios', 'alias' => 'Propietario', 'type' => 'left', 'conditions' => ['Ajuste.propietario_id=Propietario.id']]],
                'group' => 'Ajuste.id'// si no pongo esto, me cuatriplica los ajustes
            ];
            $resul = $this->find('all', $options);
            /* if (count($resul) == 0 && !$this->Cobranzatipoliquidacione->LiquidationsType->Liquidation->hasLiquidationsBloqueadas($consorcio_id, $k)) {
              // no encontró ninguna liquidacion anterior, busco los ajustes de la liquidacion INICIAL
              $condiciones['Liquidation.liquidations_type_id'] = $k;
              $resultado[$k] = $this->getAjustesIniciales($condiciones);
              } else { */
            $resultado[$k] = $resul;
            // }
        }

        // no puedo hacer Hash::combine porq puede haber varias cobranzas de un mismo propietario_id, entonces me deja un solo key
        return $resultado;
    }

    /*
     * En la carga de ajustes, se utiliza para mostrar la cuenta corriente propietario (se llama desde saldosCierres::getAjustesPropietario()
     */

    public function getAjustesPropietario($propietario_id = null) {
        $options = array('conditions' => ['Ajuste.propietario_id' => $propietario_id, 'Ajuste.anulado' => 0],
            'fields' => array('Ajuste.fecha', 'Ajuste.concepto', 'Ajustetipoliquidacione.liquidations_type_id', 'Ajustetipoliquidacione.amount'),
            'order' => 'Ajuste.fecha',
            'joins' => [['table' => 'ajustetipoliquidaciones', 'alias' => 'Ajustetipoliquidacione', 'type' => 'right', 'conditions' => ['Ajuste.id=Ajustetipoliquidacione.ajuste_id']]]);
        return $this->find('all', $options);
    }

}
