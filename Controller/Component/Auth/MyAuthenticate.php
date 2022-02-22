<?php

App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class MyAuthenticate extends BaseAuthenticate {
    /*
     * Extiendo la autenticacion para poder loguear iguales usuarios de distintos clientes
     * Ej: usuario juan (cliente ceonline), el usuario es juan@ceonline
     * Obtengo "ceonline" y me fijo si existe un usuario juan para el identificador_cliente "ceonline"
     */

    public function authenticate(CakeRequest $request, CakeResponse $response) {
        $pos = strpos($request['data']['User']['username'], '@');
        if ($pos === false) {
            // ni siquiera puso @ en el usuario
            return false;
        }
        // busco si existe el usuario correspondiente al cliente ingresado
        $identificador = substr($request['data']['User']['username'], $pos + 1, strlen($request['data']['User']['username']));
        $user = substr($request['data']['User']['username'], 0, $pos);
        $result = ClassRegistry::init('User')->find('count', array(
            'conditions' => ['Client.identificador_cliente' => $identificador,
                'Client.enabled' => 1,
                'User.username' => $user,
                'User.enabled' => 1],
            'recursive' => 0
        ));
        if ($result === 0) {
            // no existe este usuario para este cliente o esta inhabilitado
            return false;
        }

        return $this->_findUser(['User.username' => $user, 'Client.identificador_cliente' => $identificador], $request->data['User']['password']);
    }

}
