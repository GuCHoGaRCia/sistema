<?php

/*
 * Cron utilizado para acreditar cheques propios futuros en la cuenta bancaria
 * tambien acredita las cobranzas automaticas de plapsa en la cuenta bancaria
 */
$webroot = dirname(__FILE__);
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once($webroot . "/../Config/database.php");
ini_set("display_errors", 1);
ini_set('max_execution_time', '10000');
$conf = new DATABASE_CONFIG();
$resul = "\nInicio del cron Movimientos Bancarios - " . date("d/m/Y H:i:s") . "\n";

try {
    $db = new PDO('mysql:host=' . $conf->default['host'] . ';dbname=' . $conf->default['database'] . ';charset=utf8mb4', $conf->default['login'], $conf->default['password']);
} catch (PDOException $e) {
    die();
}

// acredito los Cheques Propios
foreach ($db->query('select sum(cp.importe) as importe,cp.bancoscuenta_id,cp.client_id,b.name from chequespropios cp left join bancoscuentas b on cp.bancoscuenta_id=b.id where cp.anulado=0 and cp.proveedorspago_id!=0 and cp.fecha_vencimiento="' . date("Y-m-d") . '" group by cp.bancoscuenta_id') as $row) {
    if ($db->query('update bancoscuentas set saldo=saldo-' . $row['importe'] . ' where id=' . $row['bancoscuenta_id'])) {
        $resul .= "Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    } else {
        $resul .= "ERROR bancoscuentas set saldo Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    }
}

// contramovimiento Cheques Propios anulados
foreach ($db->query('select cp.*,b.name from chequespropios cp left join bancoscuentas b on cp.bancoscuenta_id=b.id where cp.anulado=1 and cp.proveedorspago_id!=0 and cp.fecha_vencimiento="' . date("Y-m-d") . '"') as $row) {
    $xx = 'insert into bancosdepositosefectivos (caja_id, bancoscuenta_id, user_id, cobranza_id, fecha, concepto, importe, es_transferencia, conciliado, anulado,created,modified) values (';
    $xx .= '0,' . $row['bancoscuenta_id'] . ',' . $row['user_id'] . ',null,"' . date("Y-m-d") . '","' . $row['concepto'] . '",' . $row['importe'] . ',0,0,1,now(),now())';
    if ($db->query($xx)) {
        $rrr = $db->query('update bancoscuentas set saldo=saldo+' . $row['importe'] . ' where id=' . $row['bancoscuenta_id']);
        $resul .= "Contramovimiento Cheque propio de pago proveedor anulado: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    } else {
        $resul .= "ERROR Contramovimiento Cheque propio Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    }
}

// acredito los Cheques Propios de ADMINISTRACION
foreach ($db->query('select sum(cpd.importe) as importe,cpd.bancoscuenta_id,cp.client_id,b.name from chequespropiosadms cp left join chequespropiosadmsdetalles cpd on cpd.chequespropiosadm_id=cp.id left join bancoscuentas b on cpd.bancoscuenta_id=b.id where cpd.proveedorspago_id!=0 and cp.anulado=0 and cp.fecha_vencimiento="' . date("Y-m-d") . '" group by cpd.bancoscuenta_id') as $row) {
    if ($db->query('update bancoscuentas set saldo=saldo-' . $row['importe'] . ' where id=' . $row['bancoscuenta_id'])) {
        $resul .= "Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    } else {
        $resul .= "ERROR bancoscuentas ADM set saldo Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    }
}

// contramovimiento Cheques Propios ADM anulados
foreach ($db->query('select sum(cpd.importe) as importe,cpd.bancoscuenta_id as cuenta,cp.*,b.name from chequespropiosadms cp left join chequespropiosadmsdetalles cpd on cpd.chequespropiosadm_id=cp.id left join bancoscuentas b on cpd.bancoscuenta_id=b.id where cpd.proveedorspago_id!=0 and cp.anulado=1 and cp.fecha_vencimiento="' . date("Y-m-d") . '" group by cpd.bancoscuenta_id') as $row) {
    $xx = 'insert into bancosdepositosefectivos (caja_id, bancoscuenta_id, user_id, cobranza_id, fecha, concepto, importe, es_transferencia, conciliado, anulado,created,modified) values (';
    $xx .= '0,' . $row['cuenta'] . ',' . $row['user_id'] . ',null,"' . date("Y-m-d") . '","' . $row['concepto'] . '",' . $row['importe'] . ',0,0,1,now(),now())';
    if ($db->query($xx)) {
        $rrr = $db->query('update bancoscuentas set saldo=saldo+' . $row['importe'] . ' where id=' . $row['cuenta']);
        $resul .= "Contramovimiento Cheque propio ADM de pago proveedor anulado: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    } else {
        $resul .= "ERROR Contramovimiento Cheque propio ADM Cliente: " . $row['client_id'] . " - Cuenta: " . $row['name'] . " - Importe: " . $row['importe'] . "\n";
    }
}


// la fecha de modificacion del pago electronico tiene q haber sido 3 dias habiles anteriores a la actual
// cargo el deposito 3 dias habiles posteriores a esa fecha. 
// si fue lunes->jueves, martes->viernes, miercoles->lunes, jueves->martes, viernes->miercoles
$hoy = date("Y-m-d");
if (!in_array(date("w", strtotime($hoy)), ['0', '6'])) { // no lo ejecuto ni ni sabado ni domingo porq me duplica cobranzassssss del miercoles anterior! fuck
    $cant = [0, 5, 5, 5, 3, 3, 0];
    $fechaproc = date("Y-m-d", strtotime($hoy . " -" . $cant[date("w", strtotime($hoy))] . " days"));
    //$cant = [3, 3, 3, 5, 5, 5, 4];
    //$fechaproc = date("Y-m-d", strtotime($datos['fecha_proc'] . " -" . $cant[date("w", strtotime($datos['fecha_proc']))] . " days"));
    $c = 0;
    $insertados = [];
    //echo 'select * from pagoselectronicos where date(fecha_proc)="' . $fechaproc . '" and cobranza_id!=0';
    foreach ($db->query('select * from pagoselectronicos where date(fecha_proc)="' . $fechaproc . '" and cobranza_id!=0') as $row) {
        $c++;
        //echo 'select b.id as cuenta,pe.*,co.id as cobranza,co.user_id,co.concepto,co.amount from bancoscuentas b join consorcios c on c.id=b.consorcio_id join propietarios p on c.id=p.consorcio_id join cobranzas co on p.id=co.propietario_id join pagoselectronicos pe on pe.cobranza_id=co.id where date(pe.modified)="' . $fechaproc . '" and pe.id= ' . $row['id'] . ' and pe.cobranza_id!=0';die;
        // tengo el propietario, necesito la cuenta bancaria asociada al consorcio del propietario (busco la x defecto para CA).
        // Si no tiene x defectodevuelve la q tiene menor id habilitada (la primera creada como hasta ahora)
        $bancoscuenta_id = 0;
        $cliente = 0;
        foreach ($db->query('select b.id,c.client_id from bancoscuentas b join consorcios c on b.consorcio_id=c.id join clients cli on cli.id=c.client_id where c.code=' . $row['consorcio_code'] . ' and cli.code=' . $row['client_code'] . ' order by habilitada desc,defectocobranzaautomatica desc,id limit 1') as $fdp) {
            $bancoscuenta_id = $fdp['id'];
            $cliente = $fdp['client_id'];
        }
        if ($bancoscuenta_id == 0 || $cliente == 0) {
            continue;
        }

        // obtengo la comision de la plataforma del cliente actual
        /* Este choclo evitaba q se guarde la comision de PLAPSA en el banco (para los q cobran comision x GP o GG)
         * Al final no se hace este cambio, en chavez y no se quien guardan en el banco la cobranza completa y despues hacen un movimiento descontando la comision de PLAPSA
         */
        /* $comision = -1;
          foreach ($db->query("select comision from plataformasdepagosconfigs where client_id=$cliente and plataformasdepago_id!=0") as $fdp) {
          $comision = $fdp['id'];
          } */

        foreach ($db->query('select b.id as cuenta,pe.*,co.id as cobranza,co.user_id,co.recibimosde,co.amount,c.client_id from bancoscuentas b join consorcios c on c.id=b.consorcio_id join propietarios p on c.id=p.consorcio_id join cobranzas co on p.id=co.propietario_id join pagoselectronicos pe on pe.cobranza_id=co.id where date(pe.fecha_proc)="' . $fechaproc . '" and pe.id= ' . $row['id'] . ' and pe.cobranza_id!=0') as $dd) {
            if (in_array($row['id'], $insertados)) {//no se porq duplica los inserts, lo evito asi mas facil
                continue;
            }
            foreach ($db->query('select id from formasdepagos where client_id=' . $dd['client_id'] . ' and forma="Cobranza Automática"') as $fdp) {
                $formadepago = $fdp['id'];
            }

            // para el caso q el cliente no cobra la comision (la cobra diferida x gasto particular, en el banco no puedo guardar el importe q pagó el propietario, 
            // porq plapsa lo informa restando la comision. Asi q se la resto antes de guardarlo
            //foreach ($db->query('select comision from plataformasdepagosconfigs where client_id=' . $dd['client_id']) as $xx) {
            //    $comision = $xx['comision'];
            //}
            $importe = $dd['amount'] /* - ($comision == 0 ? $dd['comision'] : 0) */;

            $sql = 'insert into bancosdepositosefectivos (caja_id,bancoscuenta_id,user_id,cobranza_id,fecha,concepto,importe,es_transferencia,formasdepago_id,conciliado,anulado,created,modified) values (';
            $sql .= '0,' . $bancoscuenta_id . ',' . $dd['user_id'] . ',' . $dd['cobranza'] . ',"' . $hoy . '","' . $dd['recibimosde'] . '",' . $importe . ',1,' . ($formadepago ?? 0) . ',0,0,now(),now())';
            //echo $sql."<br>";
            $rrr = $db->query($sql);
            $insertados[] = $row['id'];
            //actualizo el saldo de la cuenta bancaria
            //echo 'update bancoscuentas set saldo=saldo+' . $dd['amount'] . ' where id=' . $dd['cuenta']."<br>";
            $rrr = $db->query('update bancoscuentas set saldo=saldo+' . $dd['amount'] . ' where id=' . $bancoscuenta_id);
        }
    }
    $resul .= "Acredito $c cobranzas automaticas 72hs habiles posteriores a la fecha de modificacion del pago electronico\n";
}

//echo $resul;
$resul .= file_get_contents($webroot . '/__logs/cron_movimientosbancarios.txt');
file_put_contents($webroot . '/__logs/cron_movimientosbancarios.txt', $resul);
