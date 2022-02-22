<?php

App::uses('AppModel', 'Model');

class Consulta extends AppModel {

    public $useTable = 'consultas';
    public $validate = array(
            /* 'client_id' => array(
              'numeric' => array(
              'rule' => array('numeric'),
              //'message' => 'Your custom message here',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ),
              'mensaje' => array(
              'notBlank' => array(
              'rule' => array('notBlank'),
              //'message' => 'Your custom message here',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ),
              'es_respuesta' => array(
              'boolean' => array(
              'rule' => array('boolean'),
              //'message' => 'Your custom message here',
              //'allowEmpty' => false,
              //'required' => false,
              //'last' => false, // Stop validation after this rule
              //'on' => 'create', // Limit validation to 'create' or 'update' operations
              ),
              ), */
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
        )
    );

    /*
     * Obtiene las consultas del cliente actual
     */

    public function getConsultas($cli = null) {
        $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Client->getClientIdFromMultipleEmails($this->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : ($_SESSION['Auth']['User']['client_id'] ?? 0);
        if ($cliente == 0) {
            return []; //si no esta seteado $_SESSION['Auth']['User']['client_id'] tira error de 'User' index. Devuelvo vacio
        }
        $options = array('conditions' => array('Consulta.client_id' => $cliente), 'recursive' => 0, 'fields' => array('Consulta.mensaje as m', 'User.name as u', 'Consulta.es_respuesta as r', "DATE_FORMAT(Consulta.created,'%d/%m/%Y %T') as f"), 'order' => 'Consulta.created desc');
        return $this->find('all', $options);
    }

    public function setConsulta($c, $cli = null) {
        if (isset($_SESSION['Auth']['User']['id'])) {
            $cliente = !empty($cli) ? (is_numeric($cli) ? $cli : $this->Client->getClientIdFromMultipleEmails($this->Client->Consorcio->Propietario->Aviso->_decryptURL($cli))) : $_SESSION['Auth']['User']['client_id'];
            $this->create();
            $this->save(array('client_id' => $cliente, 'user_id' => $_SESSION['Auth']['User']['id'], 'mensaje' => filter_var($c, FILTER_SANITIZE_STRING), 'es_respuesta' => isset($_SESSION['Auth']['User']['is_admin']) ? $_SESSION['Auth']['User']['is_admin'] : 0,
                'seen' => 0));
            //return $this->find('all', array('conditions' => array('Consulta.client_id' => $cliente), 'recursive' => 0, 'fields' => array('Consulta.mensaje as m', 'User.name as u', 'Consulta.es_respuesta as r', "DATE_FORMAT(Consulta.created,'%d/%m/%Y %T') as f"), 'order' => 'Consulta.created desc'));
        } else {
            if (is_numeric($cli)) {   // no esta logueado con ceonlinemdp
                return 0;
            }
            $resul = $this->Client->Consorcio->Propietario->Aviso->_decryptURL($cli);

            if (!empty($resul) && filter_var($resul, FILTER_VALIDATE_EMAIL)) {
                $cliente = $this->Client->getClientIdFromMultipleEmails($resul);
                $this->create();
                $this->save(array('client_id' => $cliente, 'user_id' => '', 'mensaje' => filter_var($c, FILTER_SANITIZE_STRING), 'es_respuesta' => 0, 'seen' => 0));
            } else {
                return 0;
            }
        }
        return $this->find('all', array('conditions' => array('Consulta.client_id' => $cliente), 'recursive' => 0, 'fields' => array('Consulta.mensaje as m', 'User.name as u', 'Consulta.es_respuesta as r', "DATE_FORMAT(Consulta.created,'%d/%m/%Y %T') as f"), 'order' => 'Consulta.created desc'));
    }

    // $n = $this->query('select client_id as c from consultas c1 where c1.id = (select max(c2.id) from consultas c2 where c1.client_id=c2.client_id) and c1.es_respuesta=0 order by c1.id desc');
    public function verificar() {
        $n = $this->query('select client_id as c from consultas c1 where c1.id = (select max(c2.id) from consultas c2 where c1.client_id=c2.client_id) and c1.seen=0 and c1.es_respuesta=0 order by c1.id desc');
        return $n;
    }

    /*
     * Verifico si la ultima consulta del Cliente fue vista o no (por defecto las consultas del cliente son "vistas", la del Administrador no)
     */

    public function getUnseen() {
        $n = 0;
        if (isset($_SESSION['Auth']['User']['client_id'])) {
            $n = $this->query('select seen from consultas where client_id=' . $_SESSION['Auth']['User']['client_id'] . ' order by id desc limit 1');
        }
        return $n;
    }

    public function setUnseen($cli = null) {
        if (!empty($cli)) {// pongo "visto" la ultima consulta del CLIENTE (es_respuesta=0), desde el /panel de ceonline (para no tener que poner "de nada")
            $n = $this->query('update consultas set seen=1 where client_id=' . $cli . ' and es_respuesta=0 order by id desc limit 1');
            return;
        }
        if (isset($_SESSION['Auth']['User']['client_id'])) {
            $n = $this->query('update consultas set seen=1 where client_id=' . $_SESSION['Auth']['User']['client_id'] . ' and es_respuesta=1 order by id desc limit 1');
        }
        return;
    }

}
