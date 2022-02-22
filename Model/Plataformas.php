<?php

/*
 * Métodos para utilizar la Plataforma de Pagos (PLAPSA) en CakePHP 2.x
 */
App::uses('AppModel', 'Model');

class Plataformas extends AppModel {

    private $plataforma = '';

    function __construct() {
        
    }

    public function generarArchivoInformeDeuda($client_code, $comisionPlataformaPago, $consorcio_code, $ltprefijo, $liquidacion, $data, $usa2cuotas) {
        
    }

    public function generaClavePagoElectronico($codcliente, $codconsorcio, $codunidad, $prefijounidad) {
        
    }

    public function obtieneDigitoVerificador($codbarras, $hasta) {
        
    }

    public function generaCodigoBarras($prefijo, $cod_cliente, $cod_consor, $prefijoltype, $cod_unidad, $vto1, $vto2, $banco_total, $banco_total2) {
        
    }

    public function generaCodigoBarrasV2($prefijo, $cod_cliente, $cod_consor, $ltprefijo, $cod_unidad, $vto1, $vto2, $monto1, $monto2 = 0) {
        
    }

}
