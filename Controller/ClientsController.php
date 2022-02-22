<?php

App::uses('AppController', 'Controller');

class ClientsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('panel_saldosResumenCajaBanco');
        array_push($this->Security->unlockedActions, 'panel_add', 'panel_edit'); // permito blackhole x ajax
    }

    public function index() {
        $this->Client->recursive = -1;
        $this->Paginator->settings = array('conditions' => array('Client.id' => $_SESSION['Auth']['User']['client_id']));
        $this->set('clients', $this->paginar($this->Paginator));
        $this->set('cartadeudores', $this->Client->getCartaDeudores($_SESSION['Auth']['User']['client_id']));
        $this->set('recordatoriopago', $this->Client->getRecordatorioPago($_SESSION['Auth']['User']['client_id']));
        $pp = ClassRegistry::init('Plataformasdepago');
        $this->set('plataformas', $pp->getList());
        $this->set('config', $pp->getConfig($_SESSION['Auth']['User']['client_id']));
        $this->set('cuerpoemailavisos', $this->Client->getCuerpoEmailAvisos($_SESSION['Auth']['User']['client_id']));
    }

    public function view($id = null) {
        if (!$this->Client->exists($id) || $id !== $_SESSION['Auth']['User']['client_id']) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id), 'contain' => 'User');
        $this->set('client', $this->Client->find('first', $options));
    }

    public function panel_index() {
        $this->Client->recursive = 0;
        $this->Prg->commonProcess();
        $this->Paginator->settings = array('conditions' => array($this->Client->parseCriteria($this->passedArgs)));
        $pp = ClassRegistry::init('Plataformasdepago');
        $this->set('plataformas', $pp->getList());
        $this->set('clients', $this->paginar($this->Paginator));
    }

    public function panel_view($id = null) {
        if (!$this->Client->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id), 'contain' => 'User');
        $this->set('client', $this->Client->find('first', $options));
    }

    public function panel_procesos() {
        // se utilizó para #220: Reparaciones: Estados configurables
        /* if ($this->Client->Reparacionesestado->find('count') < 10) {// me aseguro de no correr 2 veces el mismo script
          set_time_limit(10000);
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#FF0000' WHERE `reparacionesestados`.`id` = 1;");
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#FF6A00' WHERE `reparacionesestados`.`id` = 2;");
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#2986CC' WHERE `reparacionesestados`.`id` = 3;");
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#999999' WHERE `reparacionesestados`.`id` = 4;");
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#00A813' WHERE `reparacionesestados`.`id` = 5;");
          $this->Client->query("UPDATE `reparacionesestados` SET `color` = '#FF0000' WHERE `reparacionesestados`.`id` = 6;");
          $lista = $this->Client->find('list');
          foreach ($lista as $k => $v) {
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'Pendiente', 'color' => '#FF0000']);
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'En curso', 'color' => '#FF6A00']);
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'Enviada al consejo', 'color' => '#2986CC']);
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'Suspendida', 'color' => '#999999']);
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'Finalizada', 'color' => '#00A813']);
          if ($k == 82) {
          $this->Client->Reparacionesestado->create();
          $this->Client->Reparacionesestado->save(['client_id' => $k, 'nombre' => 'Pendiente Prioritaria', 'color' => '#FF0000']);
          }
          }

          // actualizo reparacionesestado_id en tabla reparaciones
          ini_set('memory_limit','1G');
          $estadosoriginales = $this->Client->Consorcio->Reparacione->Reparacionesestado->find('list', ['conditions' => ['client_id' => 0]]);
          $rep = $this->Client->Consorcio->Reparacione->find('all', ['contain' => ['Consorcio.client_id']]);
          foreach ($rep as $k => $v) {
          $cliente = $v['Consorcio']['client_id'];
          $reparacione_id = $v['Reparacione']['id'];
          $reparacionesestado_id = $v['Reparacione']['reparacionesestado_id'];
          $estadoscliente = $this->Client->Consorcio->Reparacione->Reparacionesestado->find('list', ['conditions' => ['client_id' => $cliente]]);
          foreach ($estadoscliente as $r => $s) {
          $nombreoriginal = $estadosoriginales[$reparacionesestado_id];
          $resul = $this->Client->query("update reparaciones set reparacionesestado_id=(select id from reparacionesestados where client_id=$cliente and nombre='$nombreoriginal') where id=$reparacione_id");
          }
          }

          // actualizo reparacionesestado_id en tabla reparacionesactualizaciones
          $rep2 = $this->Client->Consorcio->Reparacione->Reparacionesactualizacione->find('all', ['contain' => ['Reparacione.Consorcio.client_id']]);
          foreach ($rep2 as $k => $v) {
          $cliente = $v['Reparacione']['Consorcio']['client_id'];
          $reparacione_id = $v['Reparacionesactualizacione']['id'];
          $reparacionesestado_id = $v['Reparacionesactualizacione']['reparacionesestado_id'];
          $estadoscliente = $this->Client->Consorcio->Reparacione->Reparacionesestado->find('list', ['conditions' => ['client_id' => $cliente]]);
          foreach ($estadoscliente as $r => $s) {
          $nombreoriginal = $estadosoriginales[$reparacionesestado_id];
          $resul = $this->Client->query("update reparacionesactualizaciones set reparacionesestado_id=(select id from reparacionesestados where client_id=$cliente and nombre='$nombreoriginal') where id=$reparacione_id");
          }
          }
          } */
    }

    public function panel_control() {
        $this->set('clients', $this->Client->find('list'));
        $this->set('saldoscajabanco', $this->Client->query('SELECT count(*) Consorcios,date(s.fecha) Fecha,second(max(s.created)) Segundosejecucion FROM saldoscajabancos s right join clients c on c.id=s.client_id where s.client_id not in (65,85) and c.enabled=1 group by date(s.fecha) order by date(s.fecha) desc limit 10')); // no cuento prueba y demo
        $this->set('plapsa', $this->Client->query('SELECT count(*) Cantidad,count(DISTINCT client_code) cantcli,date(fecha_proc) Fecha,sum(importe) Total FROM pagoselectronicos where plataforma=1 group by date(fecha_proc) order by date(fecha_proc) desc limit 4'));
        $this->set('roela', $this->Client->query('SELECT count(*) Cantidad,count(DISTINCT client_code) cantcli,date(fecha_proc) Fecha,sum(importe) Total FROM pagoselectronicos where plataforma=3 group by date(fecha_proc) order by date(fecha_proc) desc limit 4'));
        $this->set('usuarios', $this->Client->query("SELECT lastseen Fecha,username Usuario,identificador_cliente Cliente from users u join clients c on c.id=u.client_id where is_admin=0 and username not in ('ecano', 'mlmazzei', 'mmazzei', 'mcorzo', 'mpetrek', 'msebastiani', 'rcasco', 'mcasalderrey', 'akohan', 'wmazzei', 'gcingolani', 'sschuster') order by lastseen desc limit 10"));
        $this->set('blacklist', $this->Client->query('SELECT count(*) Cantidad,name Cliente from avisosblacklists a join clients c on c.id=a.client_id group by client_id order by Cantidad desc limit 10'));
        //$this->set('consultas', $this->Client->query('SELECT count(*) Cantidad,name Cliente from consultas a join clients c on c.id=a.client_id group by client_id order by Cantidad desc limit 10'));
        $this->set('consultas', $this->Client->query('SELECT c.name,c.id as mensaje from consultas a join clients c on c.id=a.client_id where a.es_respuesta=0 and date(a.created)>=CURDATE() group by client_id order by a.id desc limit 30'));
        $this->set('ultimosliquidados', $this->Client->query('SELECT distinct client_id Cliente from colaimpresiones order by id desc limit 10'));
        $this->set('cantidadunidades', $this->Client->query('SELECT count(p.id) as cant from propietarios p join consorcios c on p.consorcio_id=c.id join clients cc on cc.id=c.client_id where c.code<90000 and cc.enabled=1'));
    }

    /*
     * Para todas las administraciones actualizo los saldos cierres. Se utiliza cuando se modifica alguna funcionalidad central y hace falta volver
     * a prorratear la misma (pero sin la necesidad de desbloquear y volver a bloquear)
     */

    public function panel_actualizaSaldosCierres() {
        $this->Client->actualizaSaldosCierres();
        $this->Flash->success(__('El proceso finaliz&oacute; correctamente'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualiza el estado de disponibilidad de todas las liquidaciones
     */

    public function panel_actualizaEstadoDisponibilidad() {
        $this->Client->actualizaEstadoDisponibilidad();
        $this->Flash->success(__('El proceso finaliz&oacute; correctamente'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualiza el numero de recibo de la cobranza manual o automatica para todos los clientes
     */

    public function panel_actualizaNumeroReciboCobranza() {
        $this->Client->actualizaNumeroReciboCobranza();
        $this->Flash->success(__('El proceso finaliz&oacute; correctamente'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Genera las formas de pago de los Propietarios (para el Panel de Propietario y que utilicen Informe de pagos) para cada cliente que no tenga actualmente
     */

    public function panel_generarFormasdePago() {
        $this->Client->generarFormasdePago();
        $this->Flash->success(__('Se generaron las Formas de pago para los Clientes faltantes'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Cifra el nombre del archivo adjunto y lo guarda en 'url' para más seguridad
     */

    public function panel_cifrarURLAdjuntos() {
        $this->Client->cifrarURLAdjuntos();
        $this->Flash->success(__('Se cifraron los nombres de los Adjuntos correctamente'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualiza los Saldos Caja banco para todos los consorcios
     */

    public function panel_saldosResumenCajaBanco() {
        $this->Client->saldosResumenCajaBanco();
        $this->Flash->success(__('Se actualizaron los Saldos de Caja y Banco para todos los Consorcios'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualiza los Saldos Caja banco para todos los consorcios
     */

    public function panel_actualizaSaldoCuentasBancarias() {
        $this->Client->actualizaSaldoCuentasBancarias();
        $this->Flash->success(__('Se actualizaron los Saldos de todas las Cuentas Bancarias'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualiza los Saldos Caja banco para todos los consorcios
     */

    public function panel_actualizaSaldoCajas() {
        $this->Client->actualizaSaldoCajas();
        $this->Flash->success(__('Se actualizaron los Saldos de todas las Cajas'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Actualizo el importe de los Pagos a Proveedor ya realizados, sumando facturas y pago a cuenta y restando notas de credito aplicadas y pagos a cuenta aplicados
     * Este importe es el q se muestra como total en el recibo (es el importe real q se entrega en el momento)
     */

    public function panel_actualizaImportesPagoProveedor() {
        $this->Client->actualizaImportesPagoProveedor();
        $this->Flash->success(__('Se actualizaron los importes de los Pagos a Proveedor de todos los Clientes'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    /*
     * Limpio el HTML de las notas y demas
     */

    public function panel_processCleanHTML() {
        $this->Client->processCleanHTML();
        $this->Flash->success(__('Se limpi&oacute; el HTML de las Notas, Comunicaciones, Gastos Generales, Reparaciones y dem&aacute;s'));
        return $this->redirect(['action' => 'procesos', 'panel' => true]);
    }

    public function panel_add() {
        if ($this->request->is('post')) {
            $resul = $this->Client->guardar($this->request);
            if (empty($resul)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        }
    }

    /**
     * Redirecciona desde "Miembros" a una url de acceso al panel del miembro
     *
     * @param CakeRequest $request
     * @return redirect
     */
    public function panel_link($id = null) {
        if (!$this->Client->exists($id)) {
            die(__('El dato es inexistente'));
        }
        $this->Client->id = $id;
        $email = explode(',', $this->Client->field('email'));
        $this->redirect(array('controller' => 'consultas', 'action' => 'view', 'panel' => false, $this->Client->Consorcio->Propietario->Aviso->_encryptURL($email[0])));
    }

    public function panel_edit($id = null) {
        if (!$this->Client->exists($id)) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['post', 'put'])) {
            $resul = $this->Client->guardar($this->request);
            if (empty($resul)) {
                $this->Flash->success(__('El dato fue guardado'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El dato no pudo ser guardado, intente nuevamente'));
            }
        } else {
            $options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id));
            $this->request->data = $this->Client->find('first', $options);
        }
    }

    public function panel_delete($id = null) {
        $this->Client->id = $id;
        if (!$this->Client->exists()) {
            $this->Flash->error(__('El dato es inexistente'));
            return $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Client->delete()) {
            $this->Flash->success(__('El dato fue eliminado'));
        } else {
            $this->Flash->error(__('No se puede eliminar el cliente CEONLINE'));
        }
        return $this->redirect($this->referer());
    }

}
