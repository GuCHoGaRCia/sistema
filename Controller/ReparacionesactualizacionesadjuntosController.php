<?php

App::uses('AppController', 'Controller');

class ReparacionesactualizacionesadjuntosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('download');
    }

    public function download($name = null, $clientid = null) {
        $this->layout = '';
        $this->autoRender = false;
        if (empty($name) || (!isset($_SESSION['Auth']['User']['client_id']) && empty($clientid))) {
            // si entra es q quiso descargar un adjunto con un link invalido
            die;
        }
        $name = $this->Reparacionesactualizacionesadjunto->Reparacionesactualizacione->Reparacione->Consorcio->Propietario->Aviso->_decryptURL($name);
        $client_id = isset($_SESSION['Auth']['User']['client_id']) ? $_SESSION['Auth']['User']['client_id'] : $this->Reparacionesactualizacionesadjunto->Reparacionesactualizacione->Reparacione->Consorcio->Propietario->Aviso->_decryptURL($clientid);
        $ruta = APP . WEBROOT_DIR . DS . 'files' . DS . $client_id . DS . 'rep' . DS;
        if (preg_match('/^([-\.\w]+)$/', $name) > 0 && is_file($ruta . basename($name))) {
            // nuevo en cake 2.3 http://book.cakephp.org/2.0/en/controllers/request-response.html#cake-response-file
            $this->response->file($ruta . basename($name), ['download' => true, 'name' => basename(substr($name, 19))]);
            return $this->response;
        } else {
            die(__('El archivo no pudo ser descargado'));
        }
    }

}
