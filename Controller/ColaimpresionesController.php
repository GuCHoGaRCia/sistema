<?php

App::uses('AppController', 'Controller');

class ColaimpresionesController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        array_push($this->Security->unlockedActions, 'addCola', 'panel_addCola', 'borrarMultiple', 'finalizar', 'panel_verificar', 'panel_enviarlink'); // permito blackhole x ajax
    }

    public function index() {
        $conditions = ['Colaimpresione.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Colaimpresione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions,
            'fields' => ['Consorcio.imprime_cod_barras', 'Colaimpresione.id', 'Colaimpresione.archivo', 'Consorciosconfiguration.*', 'Colaimpresione.client_id', 'Colaimpresione.liquidation_id', 'Colaimpresione.linkenviado', 'Colaimpresione.saldoenviado', 'Colaimpresione.bloqueado', 'Colaimpresione.created'],
            'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']],
                ['table' => 'adjuntos', 'alias' => 'Adjunto', 'type' => 'left', 'conditions' => ['Liquidation.id=Adjunto.liquidation_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'clients', 'alias' => 'Client', 'type' => 'left', 'conditions' => ['Consorcio.client_id=Client.id']],
                ['table' => 'consorciosconfigurations', 'alias' => 'Consorciosconfiguration', 'type' => 'left', 'conditions' => ['Consorcio.id=Consorciosconfiguration.consorcio_id']]],
            'contain' => ['Colaimpresionesdetalle', 'Liquidation.Adjunto'], 'group' => ['Colaimpresione.liquidation_id'],
            'order' => 'Colaimpresione.id desc'];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 10];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 100, 'maxLimit' => 100];
        }
        //debug($this->paginar($this->Paginator));
        $this->set('colaimpresiones', $this->paginar($this->Paginator));
        $l = $this->Colaimpresione->Client->Consorcio->Liquidation->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'Liquidation.cerrada' => 1, 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']]);
        $this->set('l', $l);
        $this->set('consorcios', $this->Colaimpresione->Client->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0]));
        $x = ClassRegistry::init('Plataformasdepagosconfig');
        $plataformas = $x->getList();
        $this->set('plataformas', $plataformas);
    }

    public function panel_impresiones() {
        $conditions = [$this->Colaimpresione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Colaimpresione.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions + ['Colaimpresionesdetalle.imprimir' => 1], //solo muestro los q se imprimen
            'fields' => ['Consorcio.name', 'Consorcio.id', 'Colaimpresione.archivo', 'Consorcio.imprime_cod_barras', 'LiquidationsType.name', 'Client.id', 'Client.name', 'Colaimpresione.id', 'Colaimpresione.bloqueado', 'Colaimpresione.linkenviado', 'Colaimpresione.liquidation_id', 'Colaimpresione.client_id', 'Colaimpresione.saldoenviado', 'Colaimpresione.created'],
            'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['LiquidationsType.id=Liquidation.liquidations_type_id']]],
            'contain' => ['Client', 'Colaimpresionesdetalle', 'Liquidation.Adjunto', 'Liquidation.periodo'],
            'order' => 'Colaimpresione.created desc',
            'group' => 'Colaimpresione.id'];
        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 100, 'maxLimit' => 100];
        }
        //$this->set('l', $this->Colaimpresione->Client->Consorcio->Liquidation->find('list', ['conditions' => ['Liquidation.inicial' => 0], 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']]));
        $this->set('colaimpresiones', $this->paginar($this->Paginator));
        $this->set('clientes', $this->Colaimpresione->Client->find('list', ['conditions' => ['Client.enabled' => 1], 'order' => 'Client.name']));
        $x = ClassRegistry::init('Plataformasdepagosconfig');
        $plataformas = $x->getList();
        $this->set('plataformas', $plataformas);
    }

    public function panel_finalizadas() {
        $conditions = [$this->Colaimpresione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['cliente'])) {
            $conditions += ['Colaimpresione.client_id' => $this->request->data['filter']['cliente']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions,
            'fields' => ['Consorcio.name', 'Consorcio.id', 'Consorcio.imprime_cod_barras', 'LiquidationsType.name', 'Client.id', 'Client.name', 'Colaimpresione.id', 'Colaimpresione.bloqueado', 'Colaimpresione.linkenviado', 'Colaimpresione.archivo', 'Colaimpresione.liquidation_id', 'Colaimpresione.client_id', 'Colaimpresione.saldoenviado', 'Colaimpresione.created'],
            'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']],
                ['table' => 'consorcios', 'alias' => 'Consorcio', 'type' => 'left', 'conditions' => ['Consorcio.id=Liquidation.consorcio_id']],
                ['table' => 'liquidations_types', 'alias' => 'LiquidationsType', 'type' => 'left', 'conditions' => ['LiquidationsType.id=Liquidation.liquidations_type_id']]],
            'contain' => ['Client', 'Colaimpresionesdetalle', 'Liquidation.Adjunto', 'Liquidation.periodo'],
            'order' => 'Colaimpresione.created desc',
            'group' => 'Colaimpresione.id'];
        if (!isset($this->request->data['filter']['cliente'])) {
            $this->Paginator->settings += ['limit' => 20];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 100, 'maxLimit' => 100];
        }
        //$this->set('l', $this->Colaimpresione->Client->Consorcio->Liquidation->find('list', ['conditions' => ['Liquidation.inicial' => 0], 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']]));
        $this->set('colaimpresiones', $this->paginar($this->Paginator));
        $this->set('clientes', $this->Colaimpresione->Client->find('list', ['conditions' => ['Client.enabled' => 1], 'order' => 'Client.name']));
        $x = ClassRegistry::init('Plataformasdepagosconfig');
        $plataformas = $x->getList();
        $this->set('plataformas', $plataformas);
    }

    public function impresiones() {
        $conditions = ['Colaimpresione.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Colaimpresione->parseCriteria($this->passedArgs)];
        if (isset($this->request->data['filter']['consorcio']) && $this->request->data['filter']['consorcio'] === "") {
            unset($this->request->data['filter']);
        }
        if (isset($this->request->data['filter']['consorcio'])) {
            $conditions += ['Consorcio.id' => $this->request->data['filter']['consorcio']];
            $this->passedArgs = []; // para evitar
        }

        $this->Paginator->settings = ['conditions' => $conditions + ['Colaimpresionesdetalle.imprimir' => 1],
            'fields' => ['Colaimpresione.id', 'Colaimpresione.liquidation_id', 'Colaimpresione.linkenviado', 'Colaimpresione.saldoenviado', 'Colaimpresione.created', 'Colaimpresionesdetalle.*'],
            'joins' => [['table' => 'colaimpresionesdetalles', 'alias' => 'Colaimpresionesdetalle', 'type' => 'left', 'conditions' => ['Colaimpresione.id=Colaimpresionesdetalle.colaimpresione_id']]],
            'order' => 'Colaimpresione.modified desc'];
        if (!isset($this->request->data['filter']['consorcio'])) {
            $this->Paginator->settings += ['limit' => 10];
            $this->Prg->commonProcess();
        } else {
            $this->Paginator->settings += ['limit' => 100, 'maxLimit' => 100];
        }
        $this->set('colaimpresiones', $this->paginar($this->Paginator));
        $l = $this->Colaimpresione->Client->Consorcio->Liquidation->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'Liquidation.cerrada' => 1, 'recursive' => 0, 'fields' => ['Liquidation.id', 'Liquidation.name2']]);
        $this->set('l', $l);
        $this->set('consorcios', $this->Colaimpresione->Client->Consorcio->find('list', ['conditions' => ['Consorcio.client_id' => $_SESSION['Auth']['User']['client_id']], 'recursive' => 0]));
    }

    /*
     * Hago override porq tengo q validar q al desbloquear, no existan liquidaciones siguientes con cobranzas
     */

    public function invertir($field = null, $id = null) {
        if (!$this->request->is('ajax')) {
            return $this->redirect($this->Auth->logout());
        }
        $model = $this->modelClass;
        if (empty($field) || empty($id) || in_array($field, ['id', 'created', 'modified']) || !$this->$model->hasField($field)) {
            return $this->redirect($this->Auth->logout());
        }

        // valido que el pk que se intenta modificar pertenece al cliente logueado! si es admin, puede hacer todo
        if (!$_SESSION['Auth']['User']['is_admin'] && !$this->$model->canEdit($id)) {
            die("El dato no se puede modificar o es inexistente");
        }

        // verifico q no tenga cobranzas posteriores al cierre de la liquidación q intenta abrir
        // solo cuando invierte el campo bloqueado de true a false. Está bloqueando, cliente o nosotros
        $this->$model->id = $id;
        if ($field === 'bloqueado' && $this->$model->field($field) === true) {
            $liq = $this->Colaimpresione->getLiquidationId($id);
            $lt = $this->Colaimpresione->Liquidation->getLiquidationsTypeId($liq);
            $consorcio = $this->Colaimpresione->Liquidation->getConsorcioId($liq);
            $desde = $this->Colaimpresione->Liquidation->getLiquidationClosedDate($liq);
            if (!empty($this->Colaimpresione->Liquidation->Consorcio->Propietario->Cobranza->getCobranzasFecha($consorcio, $desde, date("Y-m-d H:i:s"), $lt))) {
                die("La Liquidación que intenta desbloquear posee Cobranzas posteriores a su cierre, no se puede desbloquear");
            }
        }


        $this->autoRender = false;
        if ($this->$model && $field && $id) {
            $field = $this->$model->escapeField($field);
            return $this->$model->updateAll([$field => '1 -' . $field], [$this->$model->escapeField() => $id]);
        }
        die("El dato no se puede modificar o es inexistente");
    }

    public function addCola() {
        if (!$this->request->is('ajax')) {
            die;
        }
        die($this->Colaimpresione->addCola($this->request->data['id'], $this->request->data['r'], $this->request->data['m'], $_SESSION['Auth']['User']['client_id']));
    }

    public function panel_addCola() {
        if (!$this->request->is('ajax')) {
            die;
        }
        die($this->Colaimpresione->addCola($this->request->data['id'], $this->request->data['r'], $this->request->data['m'], @$this->request->data['cli']));
    }

    public function finalizar() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $ids = $this->request->data['ids'];
        $cont = 0;
        $error = "";

        foreach ($ids as $v) {
            if (!$this->Colaimpresione->Client->Consorcio->Liquidation->canEdit($v)) {
                $this->Flash->error('El dato es inexistente');
                continue;
            }

            // obtengo el consorcio asociado y los reportes configurados a imprimir
            //$consorcio = $this->Colaimpresione->Client->Consorcio->Liquidation->getConsorcioId($v);

            $resul = $this->Colaimpresione->addCola($v, 'Liquidation');
            if ($resul['e'] == 1) {
                $error = $resul['d'];
            } else {
                $cont++; // cuento si no hubo error
            }
        }
        if (!empty($error)) {
            //$this->Flash->error(utf8_decode($error)); //sino salen los acentos para el culo
        }
        $this->layout = '';
        $this->autoRender = false;
        if ($cont > 0) {
            $this->Flash->success("Se finalizaron $cont Liquidaciones correctamente");
        }
        die(json_encode(['e' => empty($error) ? 0 : 1] + (empty($error) ? [] : ['d' => $error])));
    }

    public function view($id = null) {
        if (!$this->Colaimpresione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $data = $this->Colaimpresione->view($id);
        $this->Reportes->open($data);
    }

    public function panel_view($id = null, $reporte = null) {
        if (!$this->Colaimpresione->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if (!in_array($reporte, ['resumenesdecuentas', 'resumengastos', 'composicionsaldos'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $data = $this->Colaimpresione->view($id, $reporte);
        $this->Reportes->open($data);
    }

    public function panel_delete($id = null) {
        $this->Colaimpresione->id = $id;
        if (!$this->Colaimpresione->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Colaimpresione->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('La Liquidación se encuentra bloqueada, no se puede eliminar'));
        }
        return $this->redirect($this->referer());
    }

    // permito a los administradores borrar de la cola de impresión si no está bloqueado
    public function delete($id = null) {
        if (!$this->Colaimpresione->canEdit($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Colaimpresione->isBloqueado($id)) {
            $this->Flash->error(__('La Liquidación se encuentra bloqueada, no se puede eliminar'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Colaimpresione->saveField('bloqueado', 0);
        return $this->panel_delete($id);
    }

    public function panel_bloquear() {
        $this->Colaimpresione->bloquear($this->request->pass[0]);
        $this->Flash->success(__('La liquidaci&oacute;n fue bloqueada / desbloqueada correctamente'));
        return $this->redirect($this->referer());
    }

    public function enviarlink() {
        $this->autoRender = false;
        if (!$this->Colaimpresione->canEdit($this->request->data['id'])) {
            die(json_encode(['e' => 1, 'd' => __('El dato es inexistente')]));
        }
        $this->panel_enviarlink();
    }

    public function panel_enviarlink() {
        if (!$this->Colaimpresione->exists($this->request->data['id'])) {
            die(json_encode(['e' => 1, 'd' => __('El dato es inexistente')]));
        }
        $this->Colaimpresione->id = $this->request->data['id'];
        $prop = $this->Colaimpresione->Client->Consorcio->Propietario->getPropietariosLinkData($this->Colaimpresione->Client->Consorcio->Liquidation->getConsorcioId($this->Colaimpresione->field('liquidation_id')));

        if (empty($prop)) {
            die(json_encode(['e' => 1, 'd' => __('No hay Propietarios configurados a los cuales enviarles el aviso')]));
        }

        ob_start();
        $consorcio_id = $this->Colaimpresione->Client->Consorcio->Propietario->getPropietarioConsorcio($prop[0]['Propietario']['id']);
        $datoscliente = $this->Colaimpresione->Client->find('first', ['conditions' => ['Client.id' => $this->Colaimpresione->Client->Consorcio->getConsorcioClientId($consorcio_id)]])['Client'];
        $u = "ceonline.com.ar/p/?";

        $cliemail = explode(",", $datoscliente['email']);
        $emailfrom = $cliemail[0];
        $emailsyaenviados = [];
        foreach ($prop as $k => $v) {
            $datospropietario = $v['Propietario'];
            $emails = array_unique(explode(",", strtolower($datospropietario['e'])));
            foreach ($emails as $e) {
                // verifico q el email no esté en la lista negra y q no se haya enviado en esta tanda de envíos
                // sino, un propietario con 10 departamentos le llegan 10 mails en vez de 1
                if (!$this->Colaimpresione->Client->Avisosblacklist->isBlacklisted($e) && !in_array($e, $emailsyaenviados)) {
                    $l = $this->Colaimpresione->Client->Aviso->_encryptURL($e);
                    $view = new View($this, false);
                    $view->autoRender = false;
                    $view->layout = '';
                    $view->set('url', $u);
                    $view->set('client', $datoscliente);
                    $view->set('email', $e);
                    $view->set('link', $l);

                    $text = ("Sr. Propietario:\r\n\r\nActualizamos la información disponible de sus Propiedades. Para visualizarla ingrese en \r\n\r\n *"
                            . "https://$u" . "avisos/view/$l*\r\n\r\n") . h($datoscliente['name']);

                    $html = $view->render('/Avisos/view2');
                    $this->Colaimpresione->Client->Avisosqueue->create(); // el client_id se guarda en tabla avisos ejecutar el cron.php!!
                    $this->Colaimpresione->Client->Avisosqueue->save(['client_id' => $datoscliente['id'],
                        'emailfrom' => !empty($emailfrom) ? $emailfrom : 'noreply@ceonline.com.ar', 'razonsocial' => $datoscliente['name'],
                        'asunto' => 'Acceso a sus Expensas Online', 'altbody' => $text, 'codigohtml' => $html, 'mailto' => $e, 'whatsapp' => $datospropietario['w']], false);

                    $emailsyaenviados[] = $e;
                }
            }
        }
        $fecha = date('Y-m-d H:i:s');
        $this->Colaimpresione->saveField('linkenviado', $fecha);

        ob_clean();
        $this->autoRender = false;

        die(json_encode(['e' => 0, 'd' => date("d/m/Y H:i:s", strtotime($fecha))]));
    }

    public function enviarsaldo() {
        $this->autoRender = false;
        if (!$this->Colaimpresione->canEdit($this->request->data['id'])) {
            die(json_encode(['e' => 1, 'd' => __('El dato es inexistente')]));
        }
        $this->panel_enviarsaldo();
    }

    public function panel_enviarsaldo() {
        $this->autoRender = false;
        if (!$this->Colaimpresione->exists($this->request->data['id'])) {
            die(json_encode(['e' => 1, 'd' => __('El dato es inexistente')]));
        }
        $resul = $this->Colaimpresione->enviarsaldo($this->request->data['id']);
        if ($resul['e'] == 0) {
            die(json_encode(['e' => 0, 'd' => date("d/m/Y H:i:s", strtotime($resul['f']))]));
        } else {
            die(json_encode(['e' => 1, 'd' => __('Ocurri&oacute; un error al reportar los saldos a la Plataforma')]));
        }
    }

    public function panel_verificar() {
        if (!$this->request->is('ajax')) {
            die();
        }
        die(json_encode($this->Colaimpresione->verificar()));
    }

    public function panel_getFile($cli, $id) {
        if (!$this->Colaimpresione->exists($id) || $this->Colaimpresione->find('count', array('conditions' => array('Colaimpresione.client_id' => $cli, 'Colaimpresione.id' => $id))) == 0) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->autoRender = false;
        $this->Colaimpresione->id = $id;
        $archivos = $this->Colaimpresione->field('archivo');
        $lista = preg_split('/#/', $archivos);
        $file = "";
        if (!empty($lista[0])) {
            $x = ClassRegistry::init('Plataformasdepago');
            $plataforma = $x->getConfig($cli);
            $plataformas = $x->get();
            $p = $plataformas[$plataforma['Plataformasdepagosconfig']['plataformasdepago_id']]['modelo'];
            $file = $p::getArchivoInformeDeuda($this->Colaimpresione->Client->getClientCode($cli), $lista[0]);
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=" . h($lista[0]) . ".txt");
            echo $file;
            die;
        }
        echo "El archivo no se encuentra";
    }

    public function borrarMultiple() {
        if (!isset($this->request->data['ids']) || !is_array($this->request->data['ids'])) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $ids = $this->request->data['ids'];
        $cont = 0;
        foreach ($ids as $v) {
            if (!$this->Colaimpresione->canEdit($v)) {
                $cont++;
                continue;
            }
            $this->Colaimpresione->id = $v;
            $this->Colaimpresione->saveField('bloqueado', 0);
            $this->Colaimpresione->Client->Consorcio->Liquidation->id = $this->Colaimpresione->field('liquidation_id');
            $this->Colaimpresione->Client->Consorcio->Liquidation->saveField('bloqueada', 0);
            $resul = $this->Colaimpresione->delete($v);
            if (!empty($resul)) {
                $cont++;
            }
        }
        $this->Flash->success(__('Se borraron exitosamente ' . $cont . ' registros'));
        $this->layout = '';
        $this->autoRender = false;
    }

}
