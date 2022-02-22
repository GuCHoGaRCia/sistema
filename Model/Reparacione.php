<?php

App::uses('AppModel', 'Model');

class Reparacione extends AppModel {

    public $useTable = 'reparaciones';
    public $displayField = 'concepto';
    public $validate = array(
        'consorcio_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'propietario_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'fecha' => array(
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
        'reparacionesestado_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debe completar el dato',
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
        ),
        'Propietario' => array(
            'className' => 'Propietario',
            'foreignKey' => 'propietario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Reparacionesestado' => array(
            'className' => 'Reparacionesestado',
            'foreignKey' => 'reparacionesestado_id',
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
        )
    );
    public $hasMany = [
        'Reparacionesactualizacione' => [
            'className' => 'Reparacionesactualizacione',
            'foreignKey' => 'reparacione_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => 'created desc',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ],
        'Reparacionesadjunto' => [
            'className' => 'Reparacionesadjunto',
            'foreignKey' => 'reparacione_id',
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
        return !empty($this->find('first', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Reparacione.id' => $id),
                            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Reparacione.consorcio_id']]])));
    }

    public function beforeSave($options = []) {
        // es reparacion del edificio (cero)
        if (empty($this->data['Reparacione']['propietario_id'])) {
            $this->data['Reparacione']['propietario_id'] = 0;
        }
        if (isset($this->data['Reparacione']['observaciones'])) {
            $this->data['Reparacione']['observaciones'] = $this->cleanHTML($this->data['Reparacione']['observaciones']);
        }
        return true;
    }

//  params => array(
//      'plugin' => null,
//      'controller' => 'reparaciones',
//      'action' => 'add',
//      'named' => array(),
//      'pass' => array(),
//      'form' => array(
//          'archivostxt' => array(
//              'name' => array(
//                  (int) 0 => 'FIESTA A&E-9647.jpg'
//              ),
//              'type' => array(
//                  (int) 0 => 'image/jpeg'
//              ),
//              'tmp_name' => array(
//                  (int) 0 => 'C:\xampp\tmp\php4AF9.tmp'
//              ),
//              'error' => array(
//                  (int) 0 => (int) 0
//              ),
//              'size' => array(
//                  (int) 0 => (int) 7651818
//              )
//  data => array(
//      'Reparacione' => array(
//          'consorcio_id' => '65',
//          'propietario_id' => '',
//          'fecha' => '08/03/2018',
//          'concepto' => 'concept',
//          'reparacionesestado_id' => '1',
//          'observaciones' => '<p>sadfasdfsadf</p>
//'
//      ),
//      'Adjunto' => array(
//          (int) 0 => array(
//              'titulo' => 'fiesta'
//          )
//      )
//  )
    public function beforeGuardar($data) {
        $errores = "";
        $info = isset($data->data['Reparacionesactualizacione']) ? $data->data['Reparacionesactualizacione'] : $data->data['Reparacione'];
        $client_id = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $this->Consorcio->Client->Aviso->_decryptURL($data->data['Reparacionesactualizacione']['c']); // si no esta logueado, es un supervisor (le asigno su clientid)
        if (!$this->Consorcio->exists($info['consorcio_id'])) {
            $errores .= "El Consorcio es inexistente\n"; // no guarda nada, sale x error
        }
        // verifico llaves
        if (isset($data->data['Reparacionesllavesmovimiento']) && !empty($data->data['Reparacionesllavesmovimiento'])) {
            $resul = $this->Reparacionesactualizacione->Reparacionesactualizacionesllavesmovimiento->Llavesmovimiento->beforeGuardar($data->data['Reparacionesllavesmovimiento'], $client_id);
            if ($resul !== "") {
                $errores .= $resul; // no guarda nada, sale x error
            }
        }
        // verifico proveedores
        if (isset($info['proveedor_id']) && !empty($info['proveedor_id'])) {
            foreach ($info['proveedor_id'] as $v) {
                if ($this->Reparacionesactualizacione->Reparacionesactualizacionesproveedore->Proveedor->find('count', array('conditions' => array('Proveedor.client_id' => $client_id, 'Proveedor.id' => $v))) == 0) {
                    $errores .= "El Proveedor ingresado es inexistente\n"; // no guarda nada, sale x error
                }
            }
        }

        // verifico supervisores
        if (isset($info['reparacionessupervisore_id']) && !empty($info['reparacionessupervisore_id'])) {
            foreach ($info['reparacionessupervisore_id'] as $v) {
                if ($this->Reparacionesactualizacione->Reparacionesactualizacionessupervisore->Reparacionessupervisore->find('count', array('conditions' => array('Reparacionessupervisore.client_id' => $client_id, 'Reparacionessupervisore.id' => $v))) == 0) {
                    $errores .= "El Supervisor ingresado es inexistente\n"; // no guarda nada, sale x error
                }
            }
        }

        return $errores;
    }

    public function guardar($data) {
        $resul = $this->beforeGuardar($data);
        if ($resul !== "") {
            return ['e' => 1, 'd' => $resul]; // no guarda nada, sale x error
        }
        if (!isset($data['Reparacione']['id'])) {
            $this->create(); // es un add, no edit
        }
        // creo la reparacion
        $info = $data->data['Reparacione'];
        $d = ['consorcio_id' => $info['consorcio_id'], 'propietario_id' => $info['propietario_id'] != '' ? $info['propietario_id'] : 0,
            'fecha' => $this->fecha($info['fecha']), 'user_id' => $_SESSION['Auth']['User']['id'], 'concepto' => $info['concepto'], 'reparacionesestado_id' => $info['reparacionesestado_id'],
            'observaciones' => 'a'];
        $resul = $this->save($d);

        $id = isset($data->data['Reparacione']['id']) ? $data->data['Reparacione']['id'] : $resul['Reparacione']['id'];

        // creo la primer actualizacion
        $this->Reparacionesactualizacione->guardarActualizacion($data, $id); // cuando crea la reparacion, paso el ID. En actualizacion NO

        return true;
    }

    public function guardarOLD($data) {
        if (!isset($data['Reparacione']['id'])) {
            $this->create(); // es un add, no edit
        }

        $resul = $this->save($data->data['Reparacione']);
        $id = isset($data->data['Reparacione']['id']) ? $data->data['Reparacione']['id'] : $resul['Reparacione']['id'];
        if (isset($data->data['Adjunto'])) {
            $archivos = $data->params['form']['archivostxt'];
            $titulos = $data->data['Adjunto'];
            $dir = APP . WEBROOT_DIR . DS . 'files' . DS . $_SESSION['Auth']['User']['client_id'] . DS . 'rep';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            foreach ($archivos['name'] as $k => $v) {
                $ext = pathinfo($v, PATHINFO_EXTENSION);
                if ($archivos['error'][$k] == 0 && in_array($ext, ['doc', 'xls', 'pdf', 'xlsx', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar']) && $this->Consorcio->Liquidation->Adjunto->checkMimeType($archivos['tmp_name'][$k])) {
                    // si es una extension permitida, lo subo
                    $fileName = basename(date("YmdHis") . rand(10000, 50000) . preg_replace("/[^a-zA-Z0-9]/", "", substr($titulos[$k]['titulo'], 0, 20)) . "." . $ext);
                    if (!move_uploaded_file($archivos['tmp_name'][$k], $dir . DS . $fileName)) {
                        // no lo pudo mover, sigo con el q sigue
                        continue;
                    }
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $this->Consorcio->Liquidation->Adjunto->comprimirImagen($dir, $fileName);
                    }

                    $this->Reparacionesadjunto->create();
                    $this->Reparacionesadjunto->save(['reparacione_id' => $id, 'titulo' => $titulos[$k]['titulo'], 'ruta' => $fileName]);
                }
            }
        }
        return true;
    }

    /*
     * Obtiene las Reparaciones que fue asignado un Supervisor específico
     * Se accede desde el Panel Supervisor!!!
     */

    public function getReparacionesSupervisor($id, $estado = 0) {
        $reparaciones = $this->find('all', ['conditions' => ['Reparacionesactualizacionessupervisore.reparacionessupervisore_id' => $id] + ($estado == 0 ? [] : ['Reparacione.reparacionesestado_id' => $estado]),
            'joins' => [['table' => 'reparacionesactualizaciones', 'alias' => 'Reparacionesactualizacione', 'type' => 'left', 'conditions' => ['Reparacionesactualizacione.reparacione_id=Reparacione.id']],
                ['table' => 'reparacionesactualizacionessupervisores', 'alias' => 'Reparacionesactualizacionessupervisore', 'type' => 'left', 'conditions' => ['Reparacionesactualizacione.id=Reparacionesactualizacionessupervisore.reparacionesactualizacione_id']]],
            'fields' => ['DISTINCT Reparacione.id', 'Reparacione.*'], 'recursive' => 1,
            'contain' => ['Consorcio.name', 'Consorcio.code', 'Propietario.name', 'Propietario.unidad', 'Propietario.code', 'Reparacionesestado.nombre'],
            'order' => 'Reparacione.fecha desc'
        ]);
        return $reparaciones;
    }

    /*
     * No puedo eliminar una Reparacion con llaves que se encuentren entregadas
     */

    public function beforeDelete($cascade = true) {
        $count = $this->User->Client->Llave->find('count', ['conditions' => ['Llave.client_id' => $_SESSION['Auth']['User']['client_id'], 'Llave.llavesestado_id' => 2, 'Llave.reparacione_id' => $this->id]]);
        if ($count == 0) {
            return true;
        }
        return false;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'Reparacione.concepto LIKE' => '%' . $data['buscar'] . '%',
                'Reparacione.observaciones LIKE' => '%' . $data['buscar'] . '%',
                'Consorcio.name LIKE' => '%' . $data['buscar'] . '%',
                'Propietario.name LIKE' => '%' . $data['buscar'] . '%',
                'Propietario.unidad LIKE' => '%' . $data['buscar'] . '%',
                'Propietario.code' => $data['buscar'],
            ]
        ];
    }

}
