<?php

App::uses('AppModel', 'Model');

class User extends AppModel {

    public $displayField = 'name';
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
        'username' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'isUnique' => array(
                'rule' => 'checkUnique',
                'message' => 'El nombre de usuario no se encuentra disponible, seleccione otro',
            ),
            'alphaNumeric' => array(
                'rule' => 'alphaNumeric',
                'required' => true,
                'message' => 'Solo letras y números'
            ),
        ),
        'password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Debe completar el dato',
            ),
            'largo' => array(
                'rule' => array('minLength', 8),
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra minúscula, mayúscula, número y caracter especial'
            ),
            'letras' => array(
                'rule' => '#[a-z]+#',
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra minúscula, mayúscula, número y caracter especial',
            ),
            'letras2' => array(
                'rule' => '#[A-Z]+#',
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra minúscula, mayúscula, número y caracter especial',
            ),
            'num' => array(
                'rule' => '#[0-9]+#',
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra minúscula, mayúscula, número y caracter especial',
            ),
            'especiales' => array(
                'rule' => '#[^0-9a-zA-Z]#',
                'message' => 'La contraseña debe tener al menos 8 caracteres, una letra minúscula, mayúscula, número y caracter especial',
            ),
            'usernameNotInPassword' => array(
                'rule' => ['usernameNotInPassword'],
                'message' => 'Su nombre no puede ser parte de la contraseña. Por favor, cree una contraseña nueva y segura',
            ),
        ),
        'is_admin' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'aceptaterminosycondiciones' => array(
            'boolean' => array(
                'rule' => array('boolean'),
                'message' => 'Debe completar el dato',
            ),
        ),
        'lastseen' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Debe completar el dato',
                'allowEmpty' => true
            ),
        ),
        'enabled' => array(
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
        )
    );
    public $hasMany = array(
        'Bancosdepositoscheque' => array(
            'className' => 'Bancosdepositoscheque',
            'foreignKey' => 'user_id',
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
        'Bancosdepositosefectivo' => array(
            'className' => 'Bancosdepositosefectivo',
            'foreignKey' => 'user_id',
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
        'Bancosextraccione' => array(
            'className' => 'Bancosextraccione',
            'foreignKey' => 'user_id',
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
        'Bancostransferencia' => array(
            'className' => 'Bancostransferencia',
            'foreignKey' => 'user_id',
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
        'Cajasegreso' => array(
            'className' => 'Cajasegreso',
            'foreignKey' => 'user_id',
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
        'Cajasingreso' => array(
            'className' => 'Cajasingreso',
            'foreignKey' => 'user_id',
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
        'Ajuste' => array(
            'className' => 'Ajuste',
            'foreignKey' => 'user_id',
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
        'Proveedorspago' => array(
            'className' => 'Proveedorspago',
            'foreignKey' => 'user_id',
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
        'Chequespropio' => array(
            'className' => 'Chequespropio',
            'foreignKey' => 'user_id',
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
    public $hasOne = array(
        'Caja' => array(
            'className' => 'Caja',
            'conditions' => '',
            'dependent' => false
        )
    );

    public function canEdit($id) {
        return !empty($this->find('first', ['conditions' => ['User.client_id' => $_SESSION['Auth']['User']['client_id'], 'User.id' => $id], 'fields' => [$this->alias . '.id']]));
    }

    public function getList($client = null) {
        $resul = $this->find('list', ['conditions' => ['User.client_id' => empty($client) ? $_SESSION['Auth']['User']['client_id'] : $client]]);
        return $resul;
    }

    /*
     * Si el Nombre de usuario ingresado es correcto, y la contraseña no coincide, incremento en 1 el campo cantidadlogueosincorrectos
     * si el campo cantidadlogueosincorrectos == 4, entonces deshabilito el usuario
     */

    public function logueoIncorrecto($login) {//esteban231312361_@fruttero3_1a
        if (preg_match('/^[A-Za-z0-9_-]*@[A-Za-z0-9_-]*$/', $login)) {
            $username = trim(substr($login, 0, strpos($login, '@')));
            $identificador = trim(substr($login, strpos($login, '@') + 1));
            $client_id = $this->Client->getClientIdFromIdentificador($identificador);
            if ($this->Client->isIdentificadorValido($identificador)) {
                $user_id = $this->isUserValido($username, $client_id);
                if ($user_id != 0) {
                    $this->id = $user_id;
                    if ($this->field('cantidadlogueosincorrectos') == 4) {// llegó a 4 intentos mal
                        $this->save(['id' => $user_id, 'cantidadlogueosincorrectos' => 0, 'enabled' => 0], ['callbacks' => false, 'validate' => false]); // pongo en cero la cantidad de incorrectos y deshabilito el usuario
                    } else {
                        $this->save(['id' => $user_id, 'cantidadlogueosincorrectos' => (int) $this->field('cantidadlogueosincorrectos') + 1], ['callbacks' => false, 'validate' => false]);
                    }
                }
            }
        }
    }

    /*
     * Chequea que el usuario se encuentre habilitado y pertenezca a un cliente habilitado
     */

    public function isUserValido($username, $client_id) {
        $options = array('conditions' => array('User.client_id' => $client_id, 'User.username' => $username, 'User.enabled' => 1, 'Client.enabled' => 1), 'recursive' => 0, 'fields' => 'User.id');
        $r = $this->find('first', $options);
        return (empty($r) ? 0 : $r['User']['id']);
    }

    public function beforeSave($options = []) {
        if (isset($this->data['User']['password']) && $this->data['User']['password'] != '') {
            $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
        } else {
            unset($this->data['User']['password']);
        }

        if ($_SESSION['Auth']['User']['is_admin'] == 0) {
            $this->data['User']['is_admin'] = 0;
            $this->data['User']['client_id'] = $_SESSION['Auth']['User']['client_id'];
        }

        if ($_SESSION['Auth']['User']['is_admin'] == 1 && isset($this->data['User']['perfil']) && $this->data['User']['perfil'] !== '0') {
            // si seleccionó un perfil, verifico que exista y le asigno al usuario el perfil correspondiente
            $perfiles = [];
            foreach ($this->query('select id,perfil from user_profiles') as $row) {
                $perfiles[$row['user_profiles']['id']] = $row['user_profiles']['perfil'];
            }
            if (in_array($this->data['User']['perfil'], array_keys($perfiles))) {
                $this->data['User']['perfil'] = $perfiles[$this->data['User']['perfil']];
            }
        }
        $this->data['User']['aceptaterminosycondiciones'] = 1;
        return true;
    }

    public function afterSave($created, $options = []) {
        if ($created) {
            $userid = $this->field('id');
            $name = $this->field('name');
            $clientid = $this->field('client_id');
            // creo la caja asociada al usuario (si es q no existe)
            if ($this->Client->Caja->find('count', ['conditions' => ['Caja.user_id' => $userid]]) == 0) {
                $this->Client->Caja->create();
                $this->Client->Caja->save(['client_id' => $clientid, 'user_id' => $userid, 'name' => _('Caja') . " " . $name, 'saldo_pesos' => 0, 'saldo_cheques' => 0], false);
            }
        }
    }

    /*
     * Valida que el nombre de usuario sea único para el cliente actual
     */

    public function checkUnique($check) {
        return ($this->find('count', ['conditions' => ['User.client_id' => ($_SESSION['Auth']['User']['is_admin'] ? $this->data['User']['client_id'] : $_SESSION['Auth']['User']['client_id']), 'User.username' => $check['username']]]) == 0);
    }

    /*
     * Valido que en la contraseña no ingresen el nombre o el username. Sean creativos, carajo!
     */

    public function usernameNotInPassword($check) {
        if (!isset($this->data['User']['name'])) {// está modificando la clave del usuario
            return true;
        }
        return (strpos(strtolower($this->data['User']['password']), strtolower($this->data['User']['name'])) === false && strpos(strtolower($this->data['User']['password']), strtolower($this->data['User']['username'])) === false);
    }

    public function beforeDelete($cascade = true) {
        // chequeo q el user a borrar no sea admin
        if ($this->field('is_admin') === true) { // no permito nunca borrar los admin (destildar el admin primero y probar de nuevo)
            return false;
        }

        // chequeo q el usuario a eliminar no sea ni ricardo, esteban, marcela, marce
        if (!in_array($_SESSION['Auth']['User']['name'], ['rcasco', 'ecano', 'mmazzei', 'mcorzo']) && in_array($this->field('username'), ['rcasco', 'ecano', 'mmazzei', 'mcorzo'])) { // no permito nunca borrar los admin (destildar el admin primero y probar de nuevo)
            //return false;
        }

        // verifico q la caja asociada no tenga movimientos. Si los tiene, no permito eliminar el usuario (por el historico de movimientos de la caja). Sugiero deshabilitar el mismo
        if ($this->Client->Caja->isInUse($this->Client->Caja->getCajaUsuario($this->field('id')))) {
            return false;
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
                'User.name LIKE' => '%' . $data['buscar'] . '%',
                'User.username LIKE' => '%' . $data['buscar'] . '%',
                'Client.name LIKE' => '%' . $data['buscar'] . '%',
                'Client.identificador_cliente LIKE' => '%' . $data['buscar'] . '%',
        ));
    }

}
