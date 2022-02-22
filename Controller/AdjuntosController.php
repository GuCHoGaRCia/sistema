<?php

App::uses('AppController', 'Controller');

class AdjuntosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('download', 'd', 'e');
        array_push($this->Security->unlockedActions, 'download', 'panel_download', 'd', 'panel_d', 'add'); // permito blackhole x ajax
    }

    public function index() {
        $this->Adjunto->recursive = 0;
        $conditions = ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Adjunto->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['liquidacion']) && $this->request->data['filter']['liquidacion'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['liquidacion'])) {
            $conditions += ['Liquidation.id' => $this->request->data['filter']['liquidacion']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions,
            'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['Liquidation.liquidations_type_id=LiquidationsType.id']]],
            'order' => 'Adjunto.created desc'
        ];

        if (!isset($this->request->data['filter']['liquidacion'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('adjuntos', $this->paginar($this->Paginator));
        $this->set('liquidations', $this->Adjunto->Liquidation->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Liquidation.inicial' => 0],
                    'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2'], 'order' => 'Consorcio.code']));
    }

    public function add() {
        if ($this->request->is('post')) {
            if ($this->Adjunto->guardar($this->request)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
        $liquidations = $this->Adjunto->Liquidation->find('list', array('conditions' => array('Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'],
                'Liquidation.inicial' => 0, 'Liquidation.bloqueada' => 0), 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']));
        $this->set(compact('liquidations'));
    }

    public function delete($id = null) {
        $this->Adjunto->id = $id;
        if (!$this->Adjunto->exists() || $this->Adjunto->find('count', array('conditions' => array('c2.client_id' => $_SESSION['Auth']['User']['client_id'], 'Adjunto.id' => $id), 'recursive' => 0, 'joins' => array(array('table' => 'consorcios', 'alias' => 'c2', 'type' => 'left', 'conditions' => array('c2.id=Liquidation.consorcio_id'))))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Adjunto->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado, intente nuevamente'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function download($n = null, $pid = null, $link = null, $cli = null) {
        $email = !empty($link) ? $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($link) : null;
        if (empty($pid)) {
            // si entra es q quiso descargar un adjunto con un link invalido
            die;
        }
        if (!empty($link)) {//!empty($link) es para q no este descargando desde Adjuntos/index logueado
            $emails = explode(',', $email);
            if (empty($emails)) {
                return false;
            }
            foreach ($emails as $e) {
                if (filter_var($e, FILTER_VALIDATE_EMAIL) === FALSE) {
                    die("El dato es inexistente");
                }
            }
        }

        $name = $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($n);
        if (preg_match('/^([-\.\w]+)$/', $name) > 0 && is_file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cli) . DS . basename($name))) {
            // nuevo en cake 2.3 http://book.cakephp.org/2.0/en/controllers/request-response.html#cake-response-file
            //if (!empty($n)) {
            // para q en auditoria quede en auditoría q se descargó el archivo. No lo hago al final, me audita el cambio de modified y no me sirve para un carajo
            //    $this->Adjunto->updateAll(['modified' => "'" . date("Y-m-d H:i:s") . "'"], ['url' => $n]); 
            //}
            $this->response->file(APP . WEBROOT_DIR . DS . 'files' . DS . basename($cli) . DS . basename($name), ['download' => true, 'name' => basename($name)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            if (empty($link)) {
                $this->redirect(['action' => 'index']);
            } else {
                $this->redirect(array('controller' => 'avisos', 'action' => 'view', $link));
            }
        }
    }

    public function panel_download($name = null, $cli = null) {
        $this->autoRender = false;
        $this->download($name, 1, 0, $cli);
    }

    /*
     * Descargar un archivo de un directorio especifico. Para mejorar la seguridad, cifro la ruta del archivo para q no se vea en la url o en el html
     * Verifico igual q no me manden ../ en el dir
     */

    public function d($name = null, $cli = null, $link = null) {
        $email = !empty($link) ? $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($link) : null;
        if (!empty($link) && filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            // si entra es q quiso descargar un adjunto con un link invalido
            die;
        }
        $ruta = $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($name);
        // la ruta permite barra (/) y espacio, sino seria '/^([-\.\w]+)$/'
        if (preg_match('/^([-\. \/\w]+)$/', $ruta) > 0 && strpos($ruta, '../') === false && is_file(getcwd() . '/files/' . basename($cli) . '/consultas/' . $ruta)) {
            $this->response->file(APP . WEBROOT_DIR . '/files/' . basename($cli) . '/consultas/' . $ruta, ['download' => true, 'name' => basename($ruta)]);
            return $this->response;
        } else {
            $this->Flash->error(__('El archivo no pudo ser descargado'));
            if (empty($link)) {
                $this->redirect(['action' => 'index']);
            } else {
                $this->redirect(array('controller' => 'avisos', 'action' => 'view', $link));
            }
        }
    }

    /*
     * Funcion para descargar un adjunto a traves del nombre, q incluye la ruta CIFRADA completa Ej: files/10/e/asdfas1246912hglk1jhg24.pdf (
     * se usa en Comunicaciones x ejemplo
     */

    public function e($name = null) {
        $ruta = $this->Adjunto->Liquidation->Consorcio->Propietario->Aviso->_decryptURL($name);
        if (empty($name) || !is_file(getcwd() . DS . $ruta)) {
            // si entra es q quiso descargar un adjunto con un link invalido
            die;
        }
        $this->response->file(getcwd() . DS . $ruta, ['download' => true, 'name' => basename($ruta)]);
        return $this->response;
    }

    public function panel_d($name = null, $link = null, $cli = null) {
        $this->d($name, $link, $cli);
    }

}
