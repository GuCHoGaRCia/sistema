<?php

App::uses('AppModel', 'Model');

class GastosParticulare extends AppModel {

    public $validate = array(
        'liquidation_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'cuentasgastosparticulare_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'date' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Debe completar con una fecha correcta',
            ),
        ),
        'amount' => array(
            'decimal' => array(
                'rule' => array('decimal'),
                'message' => 'El campo debe ser decimal',
            ),
        ),
        'description' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el campo',
            ),
        )
    );
    public $belongsTo = array(
        'Liquidation' => array(
            'className' => 'Liquidation',
            'foreignKey' => 'liquidation_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Cuentasgastosparticulare' => array(
            'className' => 'Cuentasgastosparticulare',
            'foreignKey' => 'cuentasgastosparticulare_id',
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
        'Coeficiente' => array(
            'className' => 'Coeficiente',
            'foreignKey' => 'coeficiente_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasMany = [
        'GastosParticularesComision' => [
            'className' => 'GastosParticularesComision',
            'foreignKey' => 'gastos_particulare_id',
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
        'GastosParticularesPft' => [
            'className' => 'GastosParticularesPft',
            'foreignKey' => 'gastos_particulare_id',
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
        'GastosParticularesMulta' => [
            'className' => 'GastosParticularesMulta',
            'foreignKey' => 'gastos_particulare_id',
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

    /*
     * Devuelve un array con la suma de los totales y coeficiente por propietario
     */

    public function listarGastos($liquidation_id) {
        $options = array('conditions' => array(
                'GastosParticulare.liquidation_id' => $liquidation_id),
            'recursive' => -1,
            'fields' => array('GastosParticulare.coeficiente_id', 'GastosParticulare.amount', 'GastosParticulare.propietario_id', 'GastosParticulare.description', 'GastosParticulare.cuentasgastosparticulare_id'));
        $lista = $this->find('all', $options);

        return $lista;
    }

    /*
     * Obtiene el total de gastos de una liquidacion especifica
     */

    public function calculaTotalesGastos($liquidation_id) {
        $total = 0;
        $data = json_decode($this->Liquidation->Resumene->getLiquidationData($liquidation_id)['Resumene']['data'], true);
        //        if (isset($data['totales'])) {
        //            foreach ($data['totales'] as $l => $m) {
        //                $total+= isset($m['tot']) ? $m['tot'] : 0;
        //                if (isset($m['coefpar'])) { // tiene gastos particulares prorrateados
        //                    foreach ($m['coefpar'] as $k => $v) {
        //                        $total+=$v['tot'];
        //                    }
        //                }
        //            }
        //        }
        // gastos particulares
        $total = 0;
        if (isset($data['totales'])) {
            foreach ($data['totales'] as $v1) {
                if (isset($v1['detalle'])) {
                    foreach ($v1['detalle'] as $mm => $nn) {
                        if (isset($nn['cuenta'])) {
                            $total += $nn['total'];
                        }
                    }
                }
            }
        }
        if (isset($v1['coefpar'])) { // tiene gastos particulares prorrateados
            foreach ($v1['coefpar'] as $cab => $det) {
                foreach ($det['detalle'] as $mm => $nn) {
                    if (isset($nn['cuenta'])) {
                        $total += $nn['total'];
                    }
                }
            }
        }

        return $total;
    }

    /*
     * Guarda los gastos particulares
     * 'gp' => array(
      //	'_Token' => array(
      //		'key' => '24bbb2cca29be6064e0f1c32b96d82be72eed7e9',
      //		'fields' => 'e2c088182ec6a3c103708ebd096ad78791196d25%3A',
      //		'unlocked' => ''
      //	),
      //	'GastosParticulare' => array(
      //		'liquidation_id' => '214',
      //		'cuentasgastosparticulare_id' => '24',
      //		'date' => array(
      //			'day' => '04',
      //			'month' => '01',
      //			'year' => '2015'
      //		),
      //		'description' => 'fr',
      //		'amount' => '222',
      //		'coeficiente_id' => '',
      //		'propietario_id' => '',
      //		'gp' => array(
      //                            propietario_id#cuentaGP#fecha#importe#desc
      //			(int) 0 => '52#13#2013-06-26#111#gp1',
      //			(int) 1 => '53#13#2013-06-26#111#gp1',
      //			(int) 2 => '54#24#2015-01-04#222#fr',
      //			(int) 3 => '55#24#2015-01-04#222#fr',
      //			(int) 4 => '56#24#2015-01-04#222#fr'
      //		)
     */

    public function guardar($data) {
        if (!empty($data['GastosParticulare']['gp'])) {
            $gp = $data['GastosParticulare']['gp'];
            foreach ($gp as $k => $v) {
                $d = explode('#', $v);
                $x = ['liquidation_id' => $data['GastosParticulare']['liquidation_id'], 'propietario_id' => $d[0], 'cuentasgastosparticulare_id' => $d[1], 'date' => $d[2], 'amount' => $d[3], 'description' => $d[4], 'heredable' => $data['GastosParticulare']['heredable']];
                $this->crear($x);
            }
            return true;
        }
        // si es gasto particular prorrateable
        if (!empty($data['GastosParticulare']['coeficiente_id'])) {
            $gp = $data['GastosParticulare'];
            $x = ['liquidation_id' => $gp['liquidation_id'], 'cuentasgastosparticulare_id' => $gp['cuentasgastosparticulare_id'], 'coeficiente_id' => $gp['coeficiente_id'], 'date' => $gp['date'], 'amount' => $gp['amount'], 'description' => $gp['description'], 'heredable' => $data['GastosParticulare']['heredable']];
            $this->crear($x);
            return true;
        }
        return false;
    }

    /*
     * Crea un Gasto Particular. Si cobranza_id!== null, entonces creo la comision por cobranza asociada
     */

    public function crear($data, $cobranza_id = null, $cobranzapft = null, $propietariomulta = null, $multacapital = null) {
        $this->create();
        $resul = $this->save($data);
        $id = $resul['GastosParticulare']['id'] ?? 0;
        if (!empty($cobranza_id)) {
            $this->GastosParticularesComision->create();
            $this->GastosParticularesComision->save(['gastos_particulare_id' => $id, 'cobranza_id' => $cobranza_id]);
        }
        if (!empty($cobranzapft)) {
            $this->GastosParticularesPft->create();
            $this->GastosParticularesPft->save(['gastos_particulare_id' => $id, 'cobranza_id' => $cobranzapft]);
        }
        if (!empty($propietariomulta)) {
            $this->GastosParticularesMulta->create();
            $this->GastosParticularesMulta->save(['liquidation_id' => $data['liquidation_id'], 'gastos_particulare_id' => $id, 'propietario_id' => $propietariomulta, 'multasobrecapital' => empty($multacapital) ? 0 : 1]);
        }
    }

    /*
     * Creo el gasto particular (si el cliente lo desea) de las Cartas cargadas en @ceonline.
     * Las cargo en la liquidacion activa (sino queda en cero) para el tipo de liquidacion ordinaria (cero), en el cliente, consorcio y propiet seleccionados
     */

    public function crearGPCarta($data) {
        if ($this->Liquidation->Consorcio->Client->cargaGPdeCartas($data['client_id'])) {
            $this->create();
            $resul = $this->save(['client_id' => $data['client_id'], 'heredable' => 0, 'date' => date("Y-m-d"), 'coeficiente_id' => null, 'propietario_id' => $data['propietario_id'],
                'liquidation_id' => $this->Liquidation->getLiquidationActivaId($data['consorcio_id'], $this->Liquidation->LiquidationsType->getLiquidationsTypeIdFromPrefijo(0, $data['client_id'])),
                'description' => 'Envio Postal ' . $data['oblea'], 'amount' => $this->Liquidation->Consorcio->Client->Cartasprecio->getPrecio($data['client_id'], $data['cartastipo_id']),
                'cuentasgastosparticulare_id' => $this->Liquidation->Consorcio->getCGPDefecto($data['consorcio_id'])
            ]);
            return isset($resul['GastosParticulare']['id']) ? $resul['GastosParticulare']['id'] : 0;
        }
        return 0;
    }

    /*
     * Si selecciona coeficiente y propietario a la vez o ninguno de los dos, devuelvo error
     */

    public function beforeSave($options = []) {
        /* if (isset($this->data['GastosParticulare']['coeficiente_id']) && ($this->data['GastosParticulare']['coeficiente_id'] != "" && $this->data['GastosParticulare']['propietario_id'] != "" || $this->data['GastosParticulare']['coeficiente_id'] == "" && $this->data['GastosParticulare']['propietario_id'] == "")) {
          SessionComponent::setFlash(__('Seleccione propietario (si es gasto particular) o coeficiente (si es gasto particular prorrateado), no ambos simultaneamente'), 'error', array(), 'otro');
          return false;
          } */

        return true;
    }

    public function beforeDelete($cascade = true) {
        $this->GastosParticularesPft->deleteAll(['gastos_particulare_id' => $this->id], false);
        return true;
    }

    /*
     * Se utiliza para borrar los gastos particulares asociados a una cobranza q fue x transf o interdep y generó un gasto particular
     */

    public function borrar($cobranza_id) {
        $gp = $this->GastosParticularesComision->find('first', ['conditions' => ['GastosParticularesComision.cobranza_id' => $cobranza_id]]);
        if (!empty($gp)) {
            $this->id = $gp['GastosParticularesComision']['gastos_particulare_id'];
            $this->delete();
        }
    }

    /*
     * Guardo en la liquidacion q estoy creando ($actual) los gastos particulares heredados de la anterior ($anterior)
     */

    public function heredar($actual, $anterior) {
        $options = ['conditions' => ['GastosParticulare.liquidation_id' => $anterior, 'GastosParticulare.heredable' => true],
            'fields' => ['GastosParticulare.*']];
        $resul = $this->find('all', $options);
        foreach ($resul as $k => $v) {
            $det = $v;
            $det['GastosParticulare']['liquidation_id'] = $actual;
            $det['GastosParticulare']['date'] = date("Y-m-d", strtotime($det['GastosParticulare']['date'] . " +1 month")); // les pongo a todos 1 mes mas de la fecha q tenian, es solo interna para el admin (no se muestra en ningun lado mas)
            unset($det['GastosParticulare']['id']);
            $this->create();
            $this->save($det, false);
        }
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
                'Propietario.unidad LIKE' => '%' . $data['buscar'] . '%',
                'Liquidation.name LIKE' => '%' . $data['buscar'] . '%',
                'Cuentasgastosparticulare.name LIKE' => '%' . $data['buscar'] . '%',
                'GastosParticulare.description LIKE' => '%' . $data['buscar'] . '%',
                'GastosParticulare.amount' => $data['buscar'],
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%'
        ));
    }

}
