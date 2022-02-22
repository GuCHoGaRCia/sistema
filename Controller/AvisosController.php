<?php

header("Access-Control-Allow-Origin: https://ceonline.com.ar"); // para q desde el panel Propietario puedan ver archivos y consultas, sino no funciona ajax (porq estoy en ceonline.com.ar/p/?)
header('Access-Control-Allow-Methods: POST,GET');
header('Access-Control-Allow-Headers: x-requested-with');
ini_set('always_populate_raw_post_data', -1);
App::uses('AppController', 'Controller');
App::uses('FormasdepagoController', 'Controller');

class AvisosController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('view', 'external', 'response', 'eventos');
        array_push($this->Security->unlockedActions, 'getPropietarios', 'add', 'eventos'); // permito blackhole x ajax
        if ($this->request->is('get') && $this->request['action'] == 'view' && isset($this->request->pass[0])) {
            // acceso por link de propietario
            $this->email = $this->Aviso->_decryptURL($this->request->pass[0]);
        }
    }

    public function index() {
        //echo $this->Aviso->_encryptUrl('administracion');
        //echo $this->Aviso->_decryptUrl('E55LLiQZfJ5Fd6X4_u9WuLT-QBPLMqdVUdMWK5Gp47U,');
        if (isset($this->request->params['pass'][0])) {
            $this->Flash->success(__('Los avisos fueron puestos en cola para enviar.'));
            $this->redirect(['action' => 'index']);
        }
        $resul = $propietarios = [];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $p = $this->Aviso->Client->Consorcio->Propietario->find('all', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id'], 'Consorcio.id' => $this->request->data['filter']['consorcio'],
                    'Propietario.email !=' => ''], 'recursive' => 0, 'fields' => ['DISTINCT Propietario.id', 'Propietario.email', 'Propietario.unidad', 'Propietario.code'], 'group' => 'Propietario.email', 'order' => 'Propietario.orden,Propietario.code']);
            $emails = [];

            foreach ($p as $k => $v) {
                $e = explode(',', $v['Propietario']['email']);
                foreach ($e as $s) {
                    $emails[] = $s;
                    $propietarios[$s] = $s . " - " . $v['Propietario']['unidad'] . " (" . $v['Propietario']['code'] . ")";
                }
            }
            $resul = $this->Aviso->find('all', ['conditions' => [/* 'Aviso.client_id' => $_SESSION['Auth']['User']['client_id'], */ 'Aviso.email' => $emails], 'order' => 'Aviso.email']);
        }
        $this->Flash->info(__('Seleccione un Consorcio'));

        $this->set('avisos', $resul);
        $this->set('propietarios', $propietarios);
        $this->set('consorcios', $this->Aviso->Propietario->Consorcio->getConsorciosList());
    }

    public function panel_index() {
        $conditions = [$this->Aviso->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Aviso.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions, 'joins' => [['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Propietario.consorcio_id']]],
            'fields' => ['Consorcio.id', 'Consorcio.name', 'Aviso.email', 'Client.name', 'Propietario.id', 'Propietario.name', 'Aviso.id', 'Aviso.recibido', 'Aviso.click', 'Aviso.modified', 'Aviso.rechazado', 'Aviso.created', 'Aviso.eventos'],
            'order' => 'Aviso.modified desc', 'recursive' => 0];
        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 400, 'maxLimit' => 400];
        }
        $this->set('avisos', $this->paginar($this->Paginator));
        $this->set('cliente', $this->Aviso->Client->find('list'));
    }

    public function view($link = null) {
        $datos = $this->Aviso->getDatos($link);
        if (empty($datos) || $datos === []) {
            die(__('<p style="text-align:center;font-size:28px;color:#000">El dato es inexistente, por favor, verifique la informaci&oacute;n ingresada</p>'));
        }

        $this->layout = 'backendpropietarios';
        $this->set('datos', $datos);
        $this->set('id', $this->Aviso->_encryptURL($this->email));

        $this->autoRender = false;
        $view = new View($this, false);

        // si es un cliente externo (ej: Vasini Florio, hago render de 'external'
        // external busca para el cliente si en la carpeta de consultas hay carpetas (las interpreta como liquidaciones) y busca ahi dentro los
        // archivos adjuntos q pertenezcan al cliente usando el formato 000011112222..., donde 0000=client_code, 1111=consorcio_code, 2222=propietario_code
        // agregar a ['1119'] otros clientes q usen esto
        // IMPORTANTE: Es necesario q cada consorcio tenga 1 liquidacion prorrateada para poder tomar algunos datos del cliente y demas
        // q se llame "Archivos del Consorcio" y periodo --
        /* $d = array_values($datos['datos']);
          if (empty($d[0])) {// no tiene ni 1 liquidacion, es externo
          $render = 'external';
          } else {
          // tiene alguna liquidacion, verifico si es VF (82) o algun otro
          $code = array_shift($d)[0]['Client']['code'];
          $render = in_array($code, ['1119']) ? 'external' : 'view';
          $this->set('client_code', $code);
          } */
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        $html = $view->render('view');
        echo $html;
        //echo preg_replace(['/ {2,}/'], [' '], $html);
    }

    /*
     * Se utiliza para acceder a las expensas a traves de un email (desde vasiniflorio.com.ar o lmfruttero.com.ar)
     */

    public function external($email = null, $codigopersonal = null) {
        $email = base64_decode(str_rot13(trim($email)));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: https://ceonline.com.ar/link/?");
            die();
        }
        $cp = base64_decode(str_rot13(trim($codigopersonal)));
        $arr = str_split(strtolower($email) . "!$*~|#aA<>3bñ " . strtoupper($email));
        $total = 139099;
        foreach ($arr as $v) {
            $total += (ord($v) * ord($v));
        }
        if ($cp != str_pad(substr(($total * $total), -6, 6), 6, 0, STR_PAD_RIGHT)) {
            header("Location: https://ceonline.com.ar/link/?");
            die();
        }
        $this->email = $email;
        //$this->view($this->Aviso->_encryptURL($this->email));
        $this->redirect("https://ceonline.com.ar/p/?avisos/view/" . $this->Aviso->_encryptURL($this->email));
    }

    //array( 2 consorcios y 1 y 2 propietarios respectivamente
    //    't_0_0' => '1',
    //    't_0_1' => '2',
    //    't_1_0' => '1',
    //    'Aviso' => array(
    //        'consorcio_id' => array(
    //            (int) 0 => '66',
    //            (int) 1 => '65'
    //        )
    //    )
    //)
    public function add() {
        if ($this->request->is('post')) {
            if (!isset($this->request->data['Aviso']['consorcio_id'])) {
                $this->Flash->success(__('No seleccionó ningún Propietario.'));
                $this->index();
                $this->render('index');
            }
            $d = $this->Aviso->guardar($this->request->data);
            $enviados = [];
            foreach ($d as $k => $v) {
                if (!$this->Aviso->Client->Avisosblacklist->isBlacklisted(strtolower($v['j'])) && !in_array($v['j'], $enviados)) {
                    $l = $this->Aviso->_encryptURL($v['j']);
                    $u = "ceonline.com.ar/p/?";

                    $view = new View($this, false);
                    $view->layout = '';
                    $view->set('link', $l);
                    $view->set('url', $u);
                    $view->set('email', strtolower($v['j']));
                    $view->set('client', $v['d']['Client']);

                    $text = ("Sr. Propietario: \r\n\r\nActualizamos la información disponible de sus Propiedades. Para visualizarla ingrese en el siguiente link: "
                            . "https://$u" . "avisos/view/$l" . "\r\n\r\n") . (!empty($v['d']) ? h($v['d']['Client']['name']) : 'Su Administrador de Consorcios');

                    $html = $view->render('view2');

                    $emails = explode(",", $v['d']['Client']['email']);
                    $emailfrom = $emails[0];

                    $this->Aviso->Client->Avisosqueue->create(); // el client_id se guarda en tabla avisos ejecutar el cron.php!!
                    $this->Aviso->Client->Avisosqueue->save(['client_id' => empty($v['d']) ? $_SESSION['Auth']['User']['client_id'] : $v['d']['Client']['id'],
                        'emailfrom' => !empty($emailfrom) ? $emailfrom : 'noreply@ceonline.com.ar', 'razonsocial' => !empty($v['d']) ? h($v['d']['Client']['name']) : 'CEONLINE',
                        'asunto' => 'Acceso a sus Expensas Online', 'altbody' => $text, 'codigohtml' => $html, 'mailto' => strtolower($v['j']), 'whatsapp' => $v['w']], false);

                    $enviados[] = $v['j'];
                }
            }
            return $this->redirect(['action' => 'index', 'enviados']);
        }
        $consorcios = $this->Aviso->Propietario->Consorcio->getConsorciosList();
        $this->set(compact('consorcios'));
    }

    public function panel_delete($id = null) {
        $this->Aviso->id = $id;
        if (!$this->Aviso->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Aviso->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('El dato no pudo ser eliminado'));
        }
        return $this->redirect($this->referer());
    }

    public function getPropietarios() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Aviso->getPropietarios(@$this->data['con'])));
    }

    public function eventos() {
        if ($this->request->is('post')) {
            $post = file_get_contents("php://input");
            if (!empty($post)) {
                $this->Aviso->eventos($post);
                //chmod(dirname(__FILE__) . "/../webroot/__logs/eventossendgrid_" . date("Ym") . ".txt", 0644);
                //chown(dirname(__FILE__) . "/../webroot/__logs/eventossendgrid_" . date("Ym") . ".txt", "www-data");
                $fh = fopen(dirname(__FILE__) . "/../webroot/__logs/eventossendgrid_" . date("Ym") . ".txt", "a+");
                fwrite($fh, print_r($post, true));
                fclose($fh);
                exit(0);
            }
        }
    }

    public function view2() {
        $this->layout = '';
    }

}
