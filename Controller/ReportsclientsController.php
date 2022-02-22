<?php

App::uses('AppController', 'Controller');

class ReportsclientsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function index() {
        $this->Paginator->settings = array('conditions' => array('Reportsclient.client_id' => $_SESSION['Auth']['User']['client_id'], $this->Reportsclient->parseCriteria($this->passedArgs)),
            'fields' => ['Reportsclient.id', 'Reportsclient.report_id']);
        $this->Prg->commonProcess();
        $reports = $this->Reportsclient->Report->find('all', ['conditions' => ['Report.enabled' => 1], 'order' => 'Report.name', 'fields' => ['Report.id', 'Report.name']]);
        array_unshift($reports, __('Seleccione un reporte...'));
        $reports = Hash::combine($reports, '{n}.Report.id', '{n}.Report.name');
        $this->set(compact('reports'));
        $this->set('reportsclients', $this->paginar($this->Paginator));
    }

}
