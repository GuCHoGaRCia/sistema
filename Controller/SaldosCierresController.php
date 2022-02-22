<?php

App::uses('AppController', 'Controller');

class SaldosCierresController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

    /*
     * Devuelve el saldo del propietario para la cobranza manual. O para la cuenta corriente
     * $this->request->data['p'] -> busca por el id del propietario
     * $this->request->data['c'] -> busca por el codigo de barras del resumen de cuentas (XXXXYYYYZZZZ), el q identifica cliente-consorcio-propietario
     */

    public function getSaldosPropietario() {
        if (!$this->request->is('ajax')) {
            die();
        }
        //$propiet = isset($this->request->data['p']) ? $this->request->data['p'] : (isset($this->request->data['c']) ? $this->SaldosCierre->Propietario->buscaCodigoBarras($this->request->data['c']) : []);
        $propiet = $this->request->data['p'];
        if (!$this->SaldosCierre->Propietario->canEdit($propiet)) {
            die();
        }
        $fdp = $this->SaldosCierre->Liquidation->Consorcio->Client->Formasdepago->get();
        if (empty($fdp)) {
            die("<br><div class='error'>No posee ninguna forma de pago habilitada</div>");
        }
        $this->set('formasdepago', $fdp);
        $this->set('saldos', $this->SaldosCierre->getSaldosPropietario($propiet));
        $this->set('cobranzas', $this->SaldosCierre->Propietario->Cobranza->getCobranzasPropietario($propiet));
        $this->set('ajustes', $this->SaldosCierre->Propietario->Ajuste->getAjustesPropietario($propiet));
        $this->set('liquidations_type_id', $this->SaldosCierre->Liquidation->LiquidationsType->getLiquidationsTypes());
        $this->set('bancoscuentas', $this->SaldosCierre->Liquidation->Consorcio->Client->Banco->Bancoscuenta->getCuentasBancarias($this->SaldosCierre->Propietario->getPropietarioConsorcio($propiet)));
        $this->set('chequesterceros', $this->SaldosCierre->Liquidation->Consorcio->Client->Cheque->getChequesPendientes());
        $this->set('caja_id', $this->SaldosCierre->Liquidation->Consorcio->Client->Caja->find('list', ['conditions' => ['Caja.client_id' => $_SESSION['Auth']['User']['client_id'], 'Caja.user_id' => $_SESSION['Auth']['User']['id']]]));
        $this->set('datospropietario', $this->SaldosCierre->Propietario->find('first', ['conditions' => ['Propietario.id' => $propiet], 'fields' => ['Propietario.name', 'Propietario.unidad', 'Propietario.code', 'Propietario.estado_judicial', 'Consorcio.name'], 'recursive' => 0]));
        $this->set('propietario_id', $propiet);

        $this->layout = '';
        $seccion = 'Cobranzas';
        if (isset($this->request->data['f'])) {
            $seccion = $this->request->data['f'];
        }
        $this->render("/$seccion/agregarcobranza");
    }

    /*
     * Utilizada para obtener el saldo de los propietarios x tipo de liquidacion, para la cobranza periodo por ejemplo
     */

    public function getSaldosTipoLiquidacionPropietarios() {
        if (!$this->request->is('ajax')) {
            die();
        }
        if (!isset($this->request->data['c']) || !$this->SaldosCierre->Propietario->Consorcio->canEdit($this->request->data['c'])) {
            die();
        }
        if (!isset($this->request->data['s'])) {// permito Ajustes o Cobranzas
            $source = 'Cobranzas';
        } else {
            $source = 'Ajustes';
        }
        $this->layout = '';
        $this->set('saldos', $this->SaldosCierre->getSaldosTipoLiquidacionPropietarios($this->request->data['c']));
        $this->set('lt', $this->SaldosCierre->Liquidation->LiquidationsType->getLiquidationsTypes());
        $this->set('cb', $this->SaldosCierre->Propietario->Consorcio->Client->Banco->Bancoscuenta->getCuentasBancarias($this->request->data['c']));
        $this->set('cobranzas', $this->SaldosCierre->Propietario->Cobranza->getCobranzasPeriodo($this->request->data['c']));
        $this->set('ajustes', $this->SaldosCierre->Propietario->Ajuste->getAjustesPeriodo($this->request->data['c']));
        $this->set('cid', $this->request->data['c']);
        $this->set('propietarios', $this->SaldosCierre->Propietario->getPropietarios($this->request->data['c'], ['Propietario.name2']));
        $this->set('formasdepago', $this->SaldosCierre->Propietario->Consorcio->Client->Formasdepago->get(true));
        $this->render("/$source/periodolista");
    }

}
